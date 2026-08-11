<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Users;
use Illuminate\Http\Request;
use Validator;
use Exception;
use Illuminate\Support\Facades\Mail;
use App\Helpers\NotificationHelper;
use App\Models\WebSeries;
use App\Models\WebseriesReaction;
use App\Models\EpisodeReaction;
use App\Models\Season;
use App\Models\Episode;

class ReactionController extends Controller
{
    private $folder = "user";
    
    public function episodeReact(Request $request)
    { 
        $validation = Validator::make(
            $request->all(),
            [
                'user_id'    => 'required|numeric|exists:user,id',
                'episode_id' => 'required|numeric|exists:episodes,id',
                'reaction'   => 'required|in:like,dislike,superlike',
            ],
            [
                'user_id.required'    => __('api_msg.please_enter_required_fields'),
                'episode_id.required' => __('api_msg.please_enter_required_fields'),
                'reaction.required'   => __('api_msg.please_enter_required_fields'),
            ]
        );

        if ($validation->fails()) {
            return response()->json([
                "status"  => 400,
                "message" => $validation->errors()->first()
            ]);
        }

        $userId    = $request->user_id;
        $episodeId = $request->episode_id;
        $reaction  = $request->reaction;
 
        $existing = EpisodeReaction::where("user_id", $userId)
            ->where("episode_id", $episodeId)
            ->first();
 
        if (!$existing) {
            $existing = new EpisodeReaction();
            $existing->user_id = $userId;
            $existing->episode_id = $episodeId;
            $existing->is_like = 0;
            $existing->is_dislike = 0;
            $existing->is_superlike = 0;
        }
 
        if ($reaction == "like") {

            if ($existing->is_like == 1) {
                // remove like
                $existing->is_like = 0;
            } else {
                // set like (reset others)
                $existing->is_like = 1;
                $existing->is_dislike = 0;
                $existing->is_superlike = 0;
            }
        }

        elseif ($reaction == "dislike") {

            if ($existing->is_dislike == 1) {
                // remove dislike
                $existing->is_dislike = 0;
            } else {
                // set dislike (reset others)
                $existing->is_dislike = 1;
                $existing->is_like = 0;
                $existing->is_superlike = 0;
            }
        }

        elseif ($reaction == "superlike") {

            if ($existing->is_superlike == 1) {
                // remove superlike
                $existing->is_superlike = 0;
            } else {
                // set superlike (reset others)
                $existing->is_superlike = 1;
                $existing->is_like = 0;
                $existing->is_dislike = 0;
            }
        }
 
        if (
            $existing->is_like == 0 &&
            $existing->is_dislike == 0 &&
            $existing->is_superlike == 0 &&
            $existing->is_wishlist == 0 
        ) {
            $existing->delete();
        } else {
            $existing->save();
        }
 
        $likes = EpisodeReaction::where("episode_id", $episodeId)
            ->where("is_like", 1)
            ->count();

        $dislikes = EpisodeReaction::where("episode_id", $episodeId)
            ->where("is_dislike", 1)
            ->count();

        $superLikes = EpisodeReaction::where("episode_id", $episodeId)
            ->where("is_superlike", 1)
            ->count();
 
        $newReaction = EpisodeReaction::where("user_id", $userId)
            ->where("episode_id", $episodeId)
            ->first();

        return response()->json([
            "status" => 200,
            "message" => "Reaction updated successfully",

            "stats" => [
                "likes" => $likes,
                "dislikes" => $dislikes,
                "super_likes" => $superLikes,

                "is_liked" => $newReaction ? ($newReaction->is_like == 1) : false,
                "is_disliked" => $newReaction ? ($newReaction->is_dislike == 1) : false,
                "is_super_liked" => $newReaction ? ($newReaction->is_superlike == 1) : false,
            ]
        ]);
    }

    public function episodeWishlist(Request $request)
    {
        

        $validation = Validator::make(
            $request->all(),
            [
                'user_id' => 'required|numeric|exists:user,id',
                'episode_id' => 'required|numeric|exists:episodes,id',
            ],
            [
                'user_id.required' => __('api_msg.please_enter_required_fields'),
                'episode_id.required' => __('api_msg.please_enter_required_fields'),
            ]
        );
        if ($validation->fails()) {

            $errors = $validation->errors()->first('user_id');
            $errors1 = $validation->errors()->first('episode_id'); 
            $data['status'] = 400;
            if ($errors) {
                $data['message'] = $errors;
            } elseif ($errors1) {
                $data['message'] = $errors1;
            }
            return $data;
        }

        $episodeId = $request->episode_id;
        $userId = $request->user_id;

        $reaction = EpisodeReaction::where("user_id", $userId)
                        ->where("episode_id", $episodeId)
                        ->first();

        if (!$reaction) {
            $reaction = EpisodeReaction::create([
                "user_id" => $userId,
                "episode_id" => $episodeId,
                "is_wishlist" => 1
            ]);

            return response()->json([
                "status" => 200,
                "message" => "Episode added to wishlist",
                "is_wishlisted" => true
            ]);
        }

        if ($reaction->is_wishlist == 1) {

            $reaction->is_wishlist = 0;
            $reaction->save();

            return response()->json([
                "status" => 200,
                "message" => "Episode removed from wishlist",
                "is_wishlisted" => false
            ]);
        }

        $reaction->is_wishlist = 1;
        $reaction->save();

        return response()->json([
            "status" => 200,
            "message" => "Episode added to wishlist",
            "is_wishlisted" => true
        ]);
    }

    public function webseriesReact(Request $request)
    {
        $validation = Validator::make(
            $request->all(),
            [
                'user_id' => 'required|numeric|exists:user,id',
                'web_series_id' => 'required|numeric|exists:web_series,id',
                'reaction' => "required|in:like,dislike,superlike"
            ],
            [
                'user_id.required' => __('api_msg.please_enter_required_fields'),
                'web_series_id.required' => __('api_msg.please_enter_required_fields'),
                'reaction.required' => __('api_msg.please_enter_required_fields'),
            ]
        );

        if ($validation->fails()) {
            return response()->json([
                "status" => 400,
                "message" => $validation->errors()->first()
            ]);
        }

        $webseriesId = $request->web_series_id;
        $reaction    = $request->reaction;
        $userId      = $request->user_id;
 
        $existing = WebseriesReaction::where("user_id", $userId)
            ->where("web_series_id", $webseriesId)
            ->first();

        if (!$existing) {
            $existing = new WebseriesReaction();
            $existing->user_id = $userId;
            $existing->web_series_id = $webseriesId;
            $existing->is_like = 0;
            $existing->is_dislike = 0;
            $existing->is_superlike = 0;
        }
 
        if ($reaction == "like") { 
            if ($existing->is_like == 1) {
                $existing->is_like = 0;
            } else { 
                $existing->is_like = 1;
                $existing->is_dislike = 0;
                $existing->is_superlike = 0;
            }
        }

        elseif ($reaction == "dislike") {

            if ($existing->is_dislike == 1) {
                $existing->is_dislike = 0;
            } else {
                $existing->is_dislike = 1;
                $existing->is_like = 0;
                $existing->is_superlike = 0;
            }
        }

        elseif ($reaction == "superlike") {

            if ($existing->is_superlike == 1) {
                $existing->is_superlike = 0;
            } else {
                $existing->is_superlike = 1;
                $existing->is_like = 0;
                $existing->is_dislike = 0;
            }
        }
 
        if (
            $existing->is_like == 0 &&
            $existing->is_dislike == 0 &&
            $existing->is_superlike == 0 &&
            $existing->is_wishlist == 0
        ) {
            $existing->delete();
        } else {
            $existing->save();
        }
 
        $likes = WebseriesReaction::where("web_series_id", $webseriesId)
            ->where("is_like", 1)
            ->count();

        $dislikes = WebseriesReaction::where("web_series_id", $webseriesId)
            ->where("is_dislike", 1)
            ->count();

        $superLikes = WebseriesReaction::where("web_series_id", $webseriesId)
            ->where("is_superlike", 1)
            ->count();
 
        $newReaction = WebseriesReaction::where("user_id", $userId)
            ->where("web_series_id", $webseriesId)
            ->first();

        return response()->json([
            "status" => 200,
            "message" => "Reaction updated successfully",

            "stats" => [
                "likes" => $likes,
                "dislikes" => $dislikes,
                "super_likes" => $superLikes,

                "is_liked" => $newReaction ? ($newReaction->is_like == 1) : false,
                "is_disliked" => $newReaction ? ($newReaction->is_dislike == 1) : false,
                "is_super_liked" => $newReaction ? ($newReaction->is_superlike == 1) : false,
            ]
        ]);
    }

    public function webseriesWishlist(Request $request)
    { 
  
        $validation = Validator::make(
            $request->all(),
            [
                'user_id' => 'required|numeric|exists:user,id',
                'web_series_id' => 'required|numeric|exists:web_series,id',
            ],
            [
                'user_id.required' => __('api_msg.please_enter_required_fields'),
                'web_series_id.required' => __('api_msg.please_enter_required_fields'),
            ]
        );
        if ($validation->fails()) {

            $errors = $validation->errors()->first('user_id');
            $errors1 = $validation->errors()->first('web_series_id');
            $data['status'] = 400;
            if ($errors) {
                $data['message'] = $errors;
            } elseif ($errors1) {
                $data['message'] = $errors1;
            }
            return $data;
        }

        $webseriesId = $request->web_series_id;
        $userId = $request->user_id;

        $reaction = WebseriesReaction::where("user_id", $userId)
                        ->where("web_series_id", $webseriesId)
                        ->first();

        if (!$reaction) {
            $reaction = WebseriesReaction::create([
                "user_id" => $userId,
                "web_series_id" => $webseriesId,
                "is_wishlist" => 1
            ]);

            return response()->json([
                "status" => 200,
                "message" => "Web Series added to wishlist",
                "is_wishlisted" => true
            ]);
        }

        if ($reaction->is_wishlist == 1) {

            $reaction->is_wishlist = 0;
            $reaction->save();

            return response()->json([
                "status" => 200,
                "message" => "Web Series removed from wishlist",
                "is_wishlisted" => false
            ]);
        }

        $reaction->is_wishlist = 1;
        $reaction->save();

        return response()->json([
            "status" => 200,
            "message" => "Web Series added to wishlist",
            "is_wishlisted" => true
        ]);
    }


}
