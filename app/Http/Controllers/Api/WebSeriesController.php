<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Users;
use App\Models\TV_Login;
use App\Models\Transction;
use App\Models\General_Setting;
use App\Models\Audition;
use App\Models\AuditionApplication;
use App\Models\Package;
use App\Models\MobileOtp;
use App\Models\Payment_Option;
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
use App\Models\EpisodeWatchHistory;

class WebSeriesController extends Controller
{
    private $folder = "user";
    
    public function webserieslist(Request $request)
    {
        $validation = Validator::make(
            $request->all(),
            [
                'user_id' => 'required|numeric',
            ],
            [
                'user_id.required' => __('api_msg.please_enter_required_fields'),
            ]
        );
        if ($validation->fails()) {
            return response()->json([
                "status"  => 400,
                "message" => $validation->errors()->first()
            ]);
        }

        $userId =$request->user_id; 
 
        $webseries = WebSeries::where('isActive', 1)
                        ->orderBy("id", "DESC")
                        ->paginate(10);

        $data = [];

        foreach ($webseries as $ws) {
 
            $likes = WebseriesReaction::where("web_series_id", $ws->id)
                        ->where("is_like", 1)
                        ->count();

            $dislikes = WebseriesReaction::where("web_series_id", $ws->id)
                        ->where("is_dislike", 1)
                        ->count();

            $superLikes = WebseriesReaction::where("web_series_id", $ws->id)
                        ->where("is_superlike", 1)
                        ->count();
 
            $myReaction = null;
            if ($userId) {
                $myReaction = WebseriesReaction::where("user_id", $userId)
                                ->where("web_series_id", $ws->id)
                                ->first();
            }

            $data[] = [
                "id" => $ws->id,
                "title" => $ws->title,
                "description" => $ws->description,
                "banner_image" =>  null,
                "thumbnail_image" => !empty($ws->thumbnail) ? asset("images/web_series/" . $ws->thumbnail) : null,
                "landscape" => !empty($ws->landscape) ? asset("images/web_series/" . $ws->landscape) : null, 

                "category" => $ws->category_name ?? "N/A",
                "release_date" => $ws->release_date 
                        ? date('Y-m-d', strtotime($ws->release_date))
                        : null,
                "release_year" => $ws->release_date 
                        ? date('Y', strtotime($ws->release_date))
                        : null,
                "rating" => $ws->imdb_rating,

                "total_seasons" => $ws->seasons()->count(),
                "total_episodes" => $ws->episodes()->count(), 
                "total_views" => Episode::where("web_series_id", $ws->id)->sum("view"),
                "is_buy" => $ws->is_premium == 1 ? 1 : 0,
                "is_premium" => $ws->is_premium == 1 ? 1 : 0,
                "likes" => $ws->is_like == 1 ? true : false,
                "dislikes" => $ws->is_dislike == 1 ? true : false,
                "super_likes" => $ws->is_superlike == 1 ? true : false,
                "wishlist" => $ws->wishlist == 1 ? true : false,
                "stats" => [
                    "likes" => $likes,
                    "dislikes" => $dislikes,
                    "super_likes" => $superLikes,

                    "is_liked" => $myReaction ? ($myReaction->is_like == 1) : false,
                    "is_disliked" => $myReaction ? ($myReaction->is_dislike == 1) : false,
                    "is_super_liked" => $myReaction ? ($myReaction->is_superlike == 1) : false,
                    "is_in_my_list" => $myReaction ? ($myReaction->is_wishlist == 1) : false,
                ]
            ];
        }

        return response()->json([
            "status" => 200,
            "message" => "WebSeries list retrieved successfully",
            "data" => $data,

            "meta" => [
                "current_page" => $webseries->currentPage(),
                "total_pages" => $webseries->lastPage(),
                "total_items" => $webseries->total(),

                "has_next" => $webseries->hasMorePages(),
                "has_previous" => $webseries->currentPage() > 1,
            ]
        ]);
    }


    public function detail(Request $request)
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
            }elseif ($errors1) {
                $data['message'] = $errors1;
            }
            return $data;
        }
        $userId =$request->user_id;
        $webseriesId = $request->web_series_id;

 
        $ws = WebSeries::where("id", $webseriesId)
                ->where("isActive", 1)
                ->first();

        if (!$ws) {
            return response()->json([
                "status" => 404,
                "message" => "WebSeries not found",
                "data" => null
            ], 404);
        }
 
        // $likes = WebseriesReaction::where("web_series_id", $ws->id)
        //             ->where("is_like", 1)
        //             ->count();

        // $dislikes = WebseriesReaction::where("web_series_id", $ws->id)
        //             ->where("is_dislike", 1)
        //             ->count();

        // $superLikes = WebseriesReaction::where("web_series_id", $ws->id)
        //             ->where("is_superlike", 1)
        //             ->count();

        $likes = EpisodeReaction::join('episodes', 'episodes.id', '=', 'episode_reactions.episode_id')
            ->where('episodes.web_series_id', $ws->id)
            ->where('episode_reactions.is_like', 1)
            ->count();

        $dislikes = EpisodeReaction::join('episodes', 'episodes.id', '=', 'episode_reactions.episode_id')
            ->where('episodes.web_series_id', $ws->id)
            ->where('episode_reactions.is_dislike', 1)
            ->count();

        $superLikes = EpisodeReaction::join('episodes', 'episodes.id', '=', 'episode_reactions.episode_id')
            ->where('episodes.web_series_id', $ws->id)
            ->where('episode_reactions.is_superlike', 1)
            ->count();
 
        $myReaction = null;
        if ($userId) {
            $myReaction = WebseriesReaction::where("user_id", $userId)
                            ->where("web_series_id", $ws->id)
                            ->first();
        }
 
        $seasonsData = [];

        $seasons = Season::where("web_series_id", $ws->id)
                    ->where("isActive", 1)
                    ->orderBy("season_number", "DESC")
                    ->orderBy("id", "DESC")
                    ->get();

        foreach ($seasons as $season) {

            $episodes = Episode::where("season_id", $season->id)
                        ->orderBy("episode_number", "ASC")
                        ->get();

            $episodesList = [];

            foreach ($episodes as $ep) {

                $watchProgress = 0;
                $isWatched = false;

                if ($userId) { 
                    $history = EpisodeWatchHistory::where("user_id", $userId)
                        ->where("episode_id", $ep->id)
                        ->first();

                    if ($history) {
                        $watchProgress = $history->watch_progress;
                        $isWatched = $history->is_watched;
                    }
                }
                
                $episodesList[] = [
                    "id" => $ep->id,
                    "episode_number" => $ep->episode_number,
                    "title" => $ep->name,
                    "duration" => $ep->video_duration, 
                    "banner_image" => null,
                    "thumbnail_image" => !empty($ep->thumbnail) ? asset("images/episodes/" . $ep->thumbnail) : null,
                    "landscape" => !empty($ep->landscape) ? asset("images/episodes/" . $ep->landscape) : null, 
                    "watch_progress" => $watchProgress,
                    "is_watched" => $isWatched,
                    "description" => $ep->description

                ];
            }

            $seasonsData[] = [
                "id" => $season->id,
                "series_id" => $ws->id,
                "season_number" => $season->season_number ?? $season->id,
                "title" => $season->title,
                "description" => $season->description, 
                "banner_image" => null,
                "thumbnail_image" => !empty($season->thumbnail) ? asset("images/season/" . $season->thumbnail) : null,
                "landscape" => !empty($season->landscape) ? asset("images/season/" . $season->landscape) : null,
                "video_url" =>  $season->video, 

                "total_episodes" => count($episodesList),

                "episodes" => $episodesList
            ];
        }
 
        return response()->json([
            "status" => 200,
            "message" => "WebSeries details retrieved successfully",

            "data" => [
                "id" => $ws->id,
                "title" => $ws->title,
                "description" => $ws->description,
                "banner_image" => !empty($ws->landscape) ? asset("images/web_series/" . $ws->landscape) : null,
                "thumbnail_image" => !empty($ws->thumbnail) ? asset("images/web_series/" . $ws->thumbnail) : null,

                "category" => $ws->category_name ?? "N/A",

                "release_year" => $ws->release_date
                        ? date("Y", strtotime($ws->release_date))
                        : null,

                "rating" => $ws->imdb_rating,

                "total_seasons" => count($seasonsData),
                "total_episodes" => Episode::where("web_series_id", $ws->id)->count(),
                "total_views" => Episode::where("web_series_id", $ws->id)->sum("view"),

                "is_buy" => $ws->is_premium == 1 ? 1 : 0,
                "is_premium" => $ws->is_premium == 1 ? 1 : 0,
 
                "seasons" => $seasonsData,
 
                "stats" => [
                    "likes" => $likes,
                    "dislikes" => $dislikes,
                    "super_likes" => $superLikes,

                    "is_liked" => $myReaction ? ($myReaction->is_like == 1) : false,
                    "is_disliked" => $myReaction ? ($myReaction->is_dislike == 1) : false,
                    "is_super_liked" => $myReaction ? ($myReaction->is_superlike == 1) : false,
                    "is_in_my_list" => $myReaction ? ($myReaction->is_wishlist == 1) : false,

                    "liked_at" => $myReaction && $myReaction->is_like
                                    ? $myReaction->updated_at
                                    : null,

                    "disliked_at" => $myReaction && $myReaction->is_dislike
                                    ? $myReaction->updated_at
                                    : null,

                    "super_liked_at" => $myReaction && $myReaction->is_superlike
                                    ? $myReaction->updated_at
                                    : null,

                    "added_to_list_at" => $myReaction && $myReaction->is_wishlist
                                    ? $myReaction->updated_at
                                    : null,
                ]
            ]
        ]);
    }

    public function seasons(Request $request)
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
            }elseif ($errors1) {
                $data['message'] = $errors1;
            }
            return $data;
        }
        $userId =$request->user_id;
        $webseriesId = $request->web_series_id;


        // ✅ Only Seasons (No Webseries Detail)
        $seasons = Season::where("web_series_id", $webseriesId)
                    ->where("isActive", 1)
                    ->orderBy("season_number", "DESC")
                    ->orderBy("id", "DESC")
                    ->get();

        if ($seasons->isEmpty()) {
            return response()->json([
                "status" => 404,
                "message" => "No seasons found",
                "data" => []
            ]);
        }

        $seasonWiseData = [];

        foreach ($seasons as $season) {
 
            $episodes = Episode::where("season_id", $season->id)
                        ->orderBy("episode_number", "ASC")
                        ->get();

            $episodesArray = [];

            
            foreach ($episodes as $ep) {
                $watchProgress = 0;
                $isWatched = false;

                if ($userId) { 
                    $history = EpisodeWatchHistory::where("user_id", $userId)
                        ->where("episode_id", $ep->id)
                        ->first();

                    if ($history) {
                        $watchProgress = $history->watch_progress;
                        $isWatched = $history->is_watched;
                    }
                }


                $subtitles = [];

                if (!empty($ep->subtitle_1)) {
                    $subtitles[] = [
                        "language" => $ep->subtitle_lang_1,
                        "language_code" => "en",
                        "url" => $ep->subtitle_type == "external"
                                ? $ep->subtitle_1
                                : asset("images/subtitles/" . $ep->subtitle_1),
                        "is_default" => true
                    ];
                }

                if (!empty($ep->subtitle_2)) {
                    $subtitles[] = [
                        "language" => $ep->subtitle_lang_2,
                        "language_code" => "hi",
                        "url" => $ep->subtitle_type == "external"
                                ? $ep->subtitle_2
                                : asset("images/subtitles/" . $ep->subtitle_2),
                        "is_default" => false
                    ];
                }

                // ✅ Episode Response
                $episodesArray[] = [
                    "id" => $ep->id,
                    "episode_number" => $ep->episode_number,
                    "title" => $ep->name,
                    "description" => $ep->description,

                    "thumbnail_image" => asset("images/episodes/" . $ep->thumbnail),

                    "video_url" => $ep->video_1080,
                    "upload_type" => $ep->video_upload_type,

                    "duration" => $ep->video_duration,
                    "watch_progress" => $watchProgress,
                    "is_watched" => $isWatched,


                    "subtitles" => $subtitles
                ];
            }

            // ✅ Season Wise Object
            $seasonWiseData[] = [
                "season_id" => $season->id,
                "season_name" => $season->name ?? "Season " . $season->season_number,
                "total_episodes" => count($episodesArray),
                "episodes" => $episodesArray
            ];
        }

        return response()->json([
            "status" => 200,
            "message" => "Season wise episodes retrieved successfully",
            "data" => $seasonWiseData
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->query("query");

        if (!$query) {
            return response()->json([
                "status" => 400,
                "message" => "Search query is required",
                "data" => []
            ]);
        }
 
        $webseries = WebSeries::where("title", "LIKE", "%$query%")
                        ->where("isActive", 1)
                        ->get();

        // ✅ Search Seasons
        $seasons = Season::where("title", "LIKE", "%$query%")
                    ->where("isActive", 1)
                    ->get();

        // ✅ Search Episodes
        $episodes = Episode::where("name", "LIKE", "%$query%")
                    ->orWhere("description", "LIKE", "%$query%")
                    ->get();

        return response()->json([
            "status" => 200,
            "message" => "Search results retrieved successfully",

            "data" => [
                "webseries" => $webseries,
                "seasons" => $seasons,
                "episodes" => $episodes
            ]
        ]);
    }


}
