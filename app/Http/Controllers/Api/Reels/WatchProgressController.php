<?php

namespace App\Http\Controllers\Api\Reels;

use App\Http\Controllers\Controller;
use App\Models\Reels\Episode;
use App\Models\Reels\UserEpisodePurchase;
use App\Models\Reels\WatchProgress;
use App\Models\Users;
use App\Services\ReelsWalletService;
use Illuminate\Http\Request; 
use RuntimeException;
use Validator;

class WatchProgressController extends Controller
{
    public function __construct(private ReelsWalletService $walletService)
    {
    }

    public function store(Request $request): array
    {
       
        $validation = Validator::make($request->all(),[
            'user_id' => 'required|exists:user,id',
            'episode_id' => 'required|exists:reel_episodes,id',
            'progress_seconds' => 'required|integer|min:0',
            'guest_id' => 'nullable|string|max:120',
        ]);

        if ($validation->fails()) {
            $data['status'] = 400;
            $data['message'] = __('api_msg.please_enter_required_fields');
            return $data;
        }
 
        $data = $validation->validated();
        $user = Users::query()->findOrFail($data['user_id']);
        $episode = Episode::query()->findOrFail($data['episode_id']);

        if ((bool) $episode->is_locked && (int) $episode->coin_price > 0) {
            $alreadyPurchased = UserEpisodePurchase::query()
                ->where('user_id', $user->id)
                ->where('episode_id', $episode->id)
                ->exists();

            if (! $alreadyPurchased) {
                try {
                    $unlockResult = $this->walletService->unlockEpisode($user, $episode);
                    $user->wallet = $unlockResult['wallet_balance'];
                } catch (RuntimeException $exception) {
                    return [
                        'status' => 400,
                        'message' => $exception->getMessage(),
                        'wallet_balance' => (int) ($user->wallet ?? 0),
                    ];
                }
            }
        }

        $progress = WatchProgress::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'episode_id' => $data['episode_id'],
            ],
            [
                'guest_id' => $data['guest_id'] ?? $request->ip(),
                'progress_seconds' => $data['progress_seconds'],
                'completed' => $data['progress_seconds'] >= 55,
            ],
        );

        return [
            'saved' => true,
            'wallet_balance' => (int) ($user->fresh()->wallet ?? 0),
            'data' => $progress,
        ];
    }
}
