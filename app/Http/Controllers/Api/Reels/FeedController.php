<?php

namespace App\Http\Controllers\Api\Reels;

use App\Http\Controllers\Controller;
use App\Models\Reels\Episode;
use Illuminate\Http\Request;
use Validator;

class FeedController extends Controller
{
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
        
        return [
            'data' => Episode::query()
                ->with('series')
                ->where('is_locked', false)
                ->inRandomOrder()
                ->take(40)
                ->get(),
        ];
    }

    public function like(Episode $episode): array
    {
        $validation = Validator::make($request->all(),[
            'user_id' => 'required|exists:user,id',
        ]);

        if ($validation->fails()) {
            $data['status'] = 400;
            $data['message'] = __('api_msg.please_enter_required_fields');
            return $data;
        }
        
        $episode->increment('likes');

        return [
            'liked' => true,
            'likes' => $episode->refresh()->likes,
        ];
    }
}
