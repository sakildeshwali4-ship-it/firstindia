<?php

namespace App\Http\Controllers\Api\Reels;

use App\Http\Controllers\Controller;
use App\Models\Reels\Episode;
use App\Models\Reels\UserEpisodeLike;
use App\Services\ReelsWalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class FeedController extends Controller
{
    public function __construct(private ReelsWalletService $walletService)
    {
    }

    public function index(Request $request): array
    {
        $validation = Validator::make($request->all(),[
            'user_id' => 'required|exists:user,id',
        ]);

        if ($validation->fails()) {
            $data['status'] = 400;
            $data['message'] = __('api_msg.please_enter_required_fields');
            return $data;
        }
        
        $episodes = Episode::query()
            ->with('series')
            ->where(function ($query) use ($request) {
                $query->where('is_premium', false)
                    ->orWhereHas('purchases', function ($purchaseQuery) use ($request) {
                        $purchaseQuery->where('user_id', $request->user_id);
                    });
            })
            ->inRandomOrder()
            ->take(40)
            ->get();

        $likedEpisodeIds = UserEpisodeLike::query()
            ->where('user_id', $request->user_id)
            ->whereIn('episode_id', $episodes->pluck('id'))
            ->pluck('episode_id')
            ->all();

        $episodes->each(function (Episode $episode) use ($request): void {
            $hasAccess = $this->walletService->hasEpisodeAccess((int) $request->user_id, $episode);

            $episode->setAttribute('is_locked', $hasAccess ? false : ((bool) $episode->is_premium && (int) $episode->coin_price > 0));
            $episode->setAttribute('is_premium', $hasAccess ? false : (bool) $episode->is_premium);
        });

        $episodes->each(function (Episode $episode) use ($likedEpisodeIds): void {
            $episode->setAttribute('liked', in_array($episode->id, $likedEpisodeIds, true));
        });

        return [
            'data' => $episodes,
        ];
    }

    public function like(Request $request, Episode $episode): array
    {
        $validation = Validator::make($request->all(),[
            'user_id' => 'required|exists:user,id',
        ]);

        if ($validation->fails()) {
            $data['status'] = 400;
            $data['message'] = __('api_msg.please_enter_required_fields');
            return $data;
        }
        
        $liked = DB::transaction(function () use ($request, $episode): bool {
            $existingLike = UserEpisodeLike::query()
                ->where('user_id', $request->user_id)
                ->where('episode_id', $episode->id)
                ->lockForUpdate()
                ->first();

            $lockedEpisode = Episode::query()
                ->lockForUpdate()
                ->findOrFail($episode->id);

            if ($existingLike) {
                $existingLike->delete();
                $lockedEpisode->likes = max(0, (int) $lockedEpisode->likes - 1);
                $lockedEpisode->save();

                return false;
            }

            UserEpisodeLike::query()->create([
                'user_id' => $request->user_id,
                'episode_id' => $lockedEpisode->id,
            ]);

            $lockedEpisode->increment('likes');

            return true;
        });

        return [
            'liked' => $liked,
            'likes' => $episode->refresh()->likes,
        ];
    }
}
