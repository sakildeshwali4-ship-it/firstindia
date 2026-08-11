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
use App\Models\Cast;
use App\Models\Language;
use App\Models\EpisodeView;
use App\Models\EpisodeWatchHistory;
use App\Models\SeasonTrailer;

class EpisodeController extends Controller
{
    private $folder = "user";
    
    public function list(Request $request)
    {
        $validation = Validator::make(
            $request->all(),
            [
                'user_id' => 'required|numeric|exists:user,id',
                'season_id' => 'required|numeric', 
            ],
            [
                'user_id.required' => __('api_msg.please_enter_required_fields'),
                'season_id.required' => __('api_msg.please_enter_required_fields'),
            ]
        );
        if ($validation->fails()) { 
            $errors = $validation->errors()->first('user_id'); 
            $errors1 = $validation->errors()->first('season_id');
            $data['status'] = 400;
            if ($errors) {
                $data['message'] = $errors;
            }elseif ($errors1) {
                $data['message'] = $errors1;
            }
            return $data;
        }
        $userId =$request->user_id;
        $seasonId = $request->season_id; 

        $season = Season::where("id", $seasonId)
                    ->where("isActive", 1)
                    ->first();

        if (!$season) {
            return response()->json([
                "status" => 404,
                "message" => "Season not found",
                "data" => []
            ]);
        }
 
        $episodes = Episode::where("season_id", $seasonId)
                    ->orderBy("episode_number", "ASC")
                    ->get();

        if ($episodes->isEmpty()) {
            return response()->json([
                "status" => 204,
                "message" => "No episodes found",
                "data" => []
            ]);
        }

        $episodesData = [];

        foreach ($episodes as $ep) {
 
            $likes = EpisodeReaction::where("episode_id", $ep->id)
                        ->where("is_like", 1)
                        ->count();

            $dislikes = EpisodeReaction::where("episode_id", $ep->id)
                        ->where("is_dislike", 1)
                        ->count();

            $superLikes = EpisodeReaction::where("episode_id", $ep->id)
                        ->where("is_superlike", 1)
                        ->count();
 
            $myReaction = null;
            $watchHistory = null;
            if ($userId) {
                $myReaction = EpisodeReaction::where("user_id", $userId)
                                ->where("episode_id", $ep->id)
                                ->first();
                 
                $watchHistory = EpisodeWatchHistory::where('user_id', $userId)
    ->where('episode_id', $ep->id)
    ->latest('updated_at')
    ->first();

                    
 
            }
 
            $subtitles = [];
            
            
            // Cast data
            $castData = [];
            if (!empty($ep->cast_id)) {
                $castIds = explode(',', $ep->cast_id);
                $castCollection = Cast::whereIn('id', $castIds)->get();
                
                foreach ($castCollection as $cast) {
                    $castData[] = [
                        "id" => $cast->id,
                        "name" => $cast->name,
                        "image" => asset("images/cast/" . $cast->image),
                        // "image" => "https://upload.wikimedia.org/wikipedia/commons/thumb/b/b6/Image_created_with_a_mobile_phone.png/250px-Image_created_with_a_mobile_phone.png",
                        "description" => $cast->personal_info,
                        
                        "type" => $cast->type
                    ];
                }
            }

            // Language data
            $languageData = [];
            if (!empty($ep->language_id)) {
                $languageIds = explode(',', $ep->language_id);

                try {
                    $languages = \App\Models\Language::whereIn('id', $languageIds)->get();
                    foreach ($languages as $lang) {
                        $languageData[] = [
                            "id" => $lang->id,
                            "name" => $lang->name,
                            "image" => asset("images/language/" . $lang->image)
                        ];
                    }
                } catch (\Exception $e) {

                }
            }
            
            
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
 
            $episodesData[] = [
                "id" => $ep->id,
                "season_id" => $seasonId,
                "episode_number" => $ep->episode_number,

                "title" => $ep->name,
                "description" => $ep->description,
                
                "cast" => $castData,
                "language" => $languageData,
                
                "thumbnail_image" => asset("images/episodes/" . $ep->landscape),

                "video_url" => $ep->video_1080,

                "video_320" => $ep->video_1080,
                "video_480" => $ep->video_1080,
                "video_720" => $ep->video_1080,
                "video_1080" => $ep->video_1080,

                "upload_type" => $ep->video_upload_type,

                "duration" => $ep->video_duration,
 
                "watch_progress" => $watchHistory ? $watchHistory->watch_progress : 0,
                "is_watched" => false,
                "is_last_watched" => $watchHistory ? (bool)$watchHistory->is_watched : false,
                
                
                "warning_text"=> $ep->warning_text,
                "warning_text_start_time"=> $ep->warning_text_start_time ?? "00:31",
                "warning_text_end_time"=>$ep->warning_text_end_time ?? "00:31",
                "skip_enable"=> $ep->skip_enable ==1 ? true:false,
                "start_time"=> $ep->skip_start ?? "00:31",
                "end_time"=> $ep->skip_end ?? "00:31",
                "is_end_credit"=> $ep->is_end_credit ==1 ? true:false,
                "end_credit_time"=> $ep->end_credit_time ?? "00:31",
            
                
                "is_buy" =>  0,
                "is_premium" => 0,
 
                "stats" => [
                    "likes" => $likes,
                    "dislikes" => $dislikes,
                    "super_likes" => $superLikes,

                    "is_liked" => $myReaction ? ($myReaction->is_like == 1) : false,
                    "is_disliked" => $myReaction ? ($myReaction->is_dislike == 1) : false,
                    "is_super_liked" => $myReaction ? ($myReaction->is_superlike == 1) : false,
                ],

                "subtitles" => $subtitles
            ];
        }

        return response()->json([
            "status" => 200,
            "message" => "Season episodes retrieved successfully",
            "season" => [
                "season_id" => $season->id,
                "season_name" => $season->name ?? "Season " . $season->season_number,
                "description" => $season->meta_desc ?? "",
                "total_episodes" => count($episodesData),
                "episodes" => $episodesData
            ], 
        ]);
    }
 

public function detail(Request $request)
{
    $episodeId = $request->input('episode_id');
    
    if (!$episodeId) {
        return response()->json([
            "status" => 400,
            "message" => "Episode ID is required"
        ], 400);
    }
    
    // You might want to use the user_id from request instead of auth
    $userId = $request->input('user_id'); // or auth()->id() if using authentication

    $episode = Episode::where("id", $episodeId)->first();

    if (!$episode) {
        return response()->json([
            "status" => 404,
            "message" => "Episode not found"
        ]);
    }
 
    $season = Season::where("id", $episode->season_id)->first();

    $likes = EpisodeReaction::where("episode_id", $episodeId)
                ->where("is_like", 1)->count();

    $dislikes = EpisodeReaction::where("episode_id", $episodeId)
                ->where("is_dislike", 1)->count();

    $superLikes = EpisodeReaction::where("episode_id", $episodeId)
                ->where("is_superlike", 1)->count();

    $myReaction = null;
    if ($userId) {
        $myReaction = EpisodeReaction::where("user_id", $userId)
                        ->where("episode_id", $episodeId)
                        ->first();
    }

    $subtitles = [];

    if (!empty($episode->subtitle_1)) {
        $subtitles[] = [
            "language" => $episode->subtitle_lang_1,
            "language_code" => "en",
            "url" => $episode->subtitle_type == "external"
                        ? $episode->subtitle_1
                        : asset("images/subtitles/" . $episode->subtitle_1),
            "is_default" => true
        ];
    }

    if (!empty($episode->subtitle_2)) {
        $subtitles[] = [
            "language" => $episode->subtitle_lang_2,
            "language_code" => "hi",
            "url" => $episode->subtitle_type == "external"
                        ? $episode->subtitle_2
                        : asset("images/subtitles/" . $episode->subtitle_2),
            "is_default" => false
        ];
    }
 
    $cast = [];
    if (!empty($episode->cast_id)) { 
        $castIds = explode(',', $episode->cast_id);
         
        $castMembers = Cast::whereIn('id', $castIds)
                          ->where('status', 1)
                          ->get();
        
        foreach ($castMembers as $member) {
            $cast[] = [
                "id" => $member->id,
                "name" => $member->name,
                "image" => asset("images/cast/" . $member->image),
                "type" => $member->type,
                "description" => $member->personal_info,
            ];
        }
    }

    $myReaction = null;
    $watchHistory = null;
    if ($userId) {
        $myReaction = EpisodeReaction::where("user_id", $userId)
                        ->where("episode_id", $episode->id)
                        ->first();
         
        $watchHistory = EpisodeWatchHistory::where('user_id', $userId)
            ->where('episode_id', $episode->id)
            ->latest('updated_at')
            ->first();
}
            // Language data
            $languageData = [];
            if (!empty($episode->language_id)) {
                $languageIds = explode(',', $episode->language_id);

                try {
                    $languages = \App\Models\Language::whereIn('id', $languageIds)->get();
                    foreach ($languages as $lang) {
                        $languageData[] = [
                            "id" => $lang->id,
                            "name" => $lang->name,
                            "image" => asset("images/language/" . $lang->image)
                        ];
                    }
                } catch (\Exception $e) {

                }
            }
        if (empty($cast)) {
                $cast = [
                    [
                        "id" => 999,
                        "name" => "Cast information not available",
                        "image" => asset("images/default-cast.png"),
                        "type" => "Unknown"
                    ],
                    [
                        "id" => 998,
                        "name" => "Multiple Cast Members",
                        "image" => asset("images/default-cast-group.png"),
                        "type" => "Ensemble"
                    ]
                ];
            }
            $episodeData = [
                "id" => $episode->id,
                "web_series_id"=>$season->web_series_id, 
                "season_id" => $episode->season_id,
                "season_name" => $season ? $season->name : null,

                "episode_number" => $episode->episode_number,
                "title" => $episode->name,
                "description" => $episode->description,

                "thumbnail_image" => asset("images/episodes/" . $episode->thumbnail),

                "upload_type" => $episode->video_upload_type,
                "cast" => $cast,
                "language" => $languageData,
                "video_url" => $episode->video_1080,
                "video_320" =>  $episode->video_320,
                "video_480" =>  $episode->video_480,
                "video_720" =>  $episode->video_720,
                "video_1080" => $episode->video_1080, 

                "upload_type" => $episode->video_upload_type,


                "duration" => $episode->video_duration,

                "watch_progress" => $watchHistory ? $watchHistory->watch_progress : 0,
                "is_watched" => false,
                "is_last_watched" => $watchHistory ? (bool)$watchHistory->is_watched : false,

                "warning_text"=> $episode->warning_text,
                "warning_text_start_time"=> $episode->warning_text_start_time ?? "00:31",
                "warning_text_end_time"=>$episode->warning_text_end_time ?? "00:31",
                "skip_enable"=> $episode->skip_enable ==1 ? true:false,
                "start_time"=> $episode->skip_start ?? "00:31",
                "end_time"=> $episode->skip_end ?? "00:31",
                "is_end_credit"=> $episode->is_end_credit ==1 ? true:false,
                "end_credit_time"=> $episode->end_credit_time ?? "00:31",

                "stats" => [
                    "likes" => $likes,
                    "dislikes" => $dislikes,
                    "super_likes" => $superLikes,

                    "is_liked" => $myReaction ? ($myReaction->is_like == 1) : false,
                    "is_disliked" => $myReaction ? ($myReaction->is_dislike == 1) : false,
                    "is_super_liked" => $myReaction ? ($myReaction->is_superlike == 1) : false,
                ],

                "subtitles" => $subtitles
            ];

    $previous = Episode::where("season_id", $episode->season_id)
                    ->where("episode_number", "<", $episode->episode_number)
                    ->orderBy("episode_number", "DESC")
                    ->first();
    $next = Episode::where("season_id", $episode->season_id)
                ->where("episode_number", ">", $episode->episode_number)
                ->orderBy("episode_number", "ASC")
                ->first();

    return response()->json([
        "status" => 200,
        "message" => "Episode details retrieved successfully",

        "episode" => $episodeData,
        "previous_episode" => $previous ? [
            "id" => $previous->id,
            "episode_number" => $previous->episode_number,
            "title" => $previous->name,
            "thumbnail_image" => asset("images/episodes/" . $previous->thumbnail),
        ] : null,
        "next_episode" => $next ? [
            "id" => $next->id,
            "episode_number" => $next->episode_number,
            "title" => $next->name,
            "thumbnail_image" => asset("images/episodes/" . $next->thumbnail),
        ] : null,
    ]);
}


public function detailV(Request $request)
{
    $episodeId = $request->input('episode_id');
    
    if (!$episodeId) {
        return response()->json([
            "status" => 400,
            "message" => "Episode ID is required"
        ], 400);
    }
    
    // You might want to use the user_id from request instead of auth
    $userId = $request->input('user_id'); // or auth()->id() if using authentication

    $episode = Episode::where("id", $episodeId)->first();

    if (!$episode) {
        return response()->json([
            "status" => 404,
            "message" => "Episode not found"
        ]);
    }
 
    $season = Season::where("id", $episode->season_id)->first();

    $likes = EpisodeReaction::where("episode_id", $episodeId)
                ->where("is_like", 1)->count();

    $dislikes = EpisodeReaction::where("episode_id", $episodeId)
                ->where("is_dislike", 1)->count();

    $superLikes = EpisodeReaction::where("episode_id", $episodeId)
                ->where("is_superlike", 1)->count();

    $myReaction = null;
    if ($userId) {
        $myReaction = EpisodeReaction::where("user_id", $userId)
                        ->where("episode_id", $episodeId)
                        ->first();
    }

    $subtitles = [];

    if (!empty($episode->subtitle_1)) {
        $subtitles[] = [
            "language" => $episode->subtitle_lang_1,
            "language_code" => "en",
            "url" => $episode->subtitle_type == "external"
                        ? $episode->subtitle_1
                        : asset("images/subtitles/" . $episode->subtitle_1),
            "is_default" => true
        ];
    }

    if (!empty($episode->subtitle_2)) {
        $subtitles[] = [
            "language" => $episode->subtitle_lang_2,
            "language_code" => "hi",
            "url" => $episode->subtitle_type == "external"
                        ? $episode->subtitle_2
                        : asset("images/subtitles/" . $episode->subtitle_2),
            "is_default" => false
        ];
    }
 
    $cast = [];
    if (!empty($episode->cast_id)) { 
        $castIds = explode(',', $episode->cast_id);
         
        $castMembers = Cast::whereIn('id', $castIds)
                          ->where('status', 1)
                          ->get();
        
        foreach ($castMembers as $member) {
            $cast[] = [
                "id" => $member->id,
                "name" => $member->name,
                "image" => asset("images/cast/" . $member->image),
                "type" => $member->type,
                "description" => $member->personal_info,
            ];
        }
    }

    $myReaction = null;
    $watchHistory = null;
    if ($userId) {
        $myReaction = EpisodeReaction::where("user_id", $userId)
                        ->where("episode_id", $episode->id)
                        ->first();
         
        $watchHistory = EpisodeWatchHistory::where('user_id', $userId)
            ->where('episode_id', $episode->id)
            ->latest('updated_at')
            ->first();
}
            // Language data
            $languageData = [];
            if (!empty($episode->language_id)) {
                $languageIds = explode(',', $episode->language_id);

                try {
                    $languages = \App\Models\Language::whereIn('id', $languageIds)->get();
                    foreach ($languages as $lang) {
                        $languageData[] = [
                            "id" => $lang->id,
                            "name" => $lang->name,
                            "image" => asset("images/language/" . $lang->image)
                        ];
                    }
                } catch (\Exception $e) {

                }
            }
        if (empty($cast)) {
                $cast = [
                    [
                        "id" => 999,
                        "name" => "Cast information not available",
                        "image" => asset("images/default-cast.png"),
                        "type" => "Unknown"
                    ],
                    [
                        "id" => 998,
                        "name" => "Multiple Cast Members",
                        "image" => asset("images/default-cast-group.png"),
                        "type" => "Ensemble"
                    ]
                ];
            }
            $episodeData = [
                "id" => $episode->id,
                "web_series_id"=>$season->web_series_id, 
                "season_id" => $episode->season_id,
                "season_name" => $season ? $season->name : null,

                "episode_number" => $episode->episode_number,
                "title" => $episode->name,
                "description" => $episode->description,

                "thumbnail_image" => asset("images/episodes/" . $episode->thumbnail),

                "upload_type" => $episode->video_upload_type,
                "cast" => $cast,
                "language" => $languageData,
                "video_url" =>$this->encryptVideoUrl($episode->video_1080), 
                "video_320" =>  $this->encryptVideoUrl($episode->video_320),
                "video_480" =>  $this->encryptVideoUrl($episode->video_480),
                "video_720" =>  $this->encryptVideoUrl($episode->video_720),
                "video_1080" => $this->encryptVideoUrl($episode->video_1080), 

                "upload_type" => $episode->video_upload_type,


                "duration" => $episode->video_duration,

                "watch_progress" => $watchHistory ? $watchHistory->watch_progress : 0,
                "is_watched" => false,
                "is_last_watched" => $watchHistory ? (bool)$watchHistory->is_watched : false,

                "warning_text"=> $episode->warning_text,
                "warning_text_start_time"=> $episode->warning_text_start_time ?? "00:31",
                "warning_text_end_time"=>$episode->warning_text_end_time ?? "00:31",
                "skip_enable"=> $episode->skip_enable ==1 ? true:false,
                "start_time"=> $episode->skip_start ?? "00:31",
                "end_time"=> $episode->skip_end ?? "00:31",
                "is_end_credit"=> $episode->is_end_credit ==1 ? true:false,
                "end_credit_time"=> $episode->end_credit_time ?? "00:31",

                "stats" => [
                    "likes" => $likes,
                    "dislikes" => $dislikes,
                    "super_likes" => $superLikes,

                    "is_liked" => $myReaction ? ($myReaction->is_like == 1) : false,
                    "is_disliked" => $myReaction ? ($myReaction->is_dislike == 1) : false,
                    "is_super_liked" => $myReaction ? ($myReaction->is_superlike == 1) : false,
                ],

                "subtitles" => $subtitles
            ];

    $previous = Episode::where("season_id", $episode->season_id)
                    ->where("episode_number", "<", $episode->episode_number)
                    ->orderBy("episode_number", "DESC")
                    ->first();
    $next = Episode::where("season_id", $episode->season_id)
                ->where("episode_number", ">", $episode->episode_number)
                ->orderBy("episode_number", "ASC")
                ->first();

    return response()->json([
        "status" => 200,
        "message" => "Episode details retrieved successfully",

        "episode" => $episodeData,
        "previous_episode" => $previous ? [
            "id" => $previous->id,
            "episode_number" => $previous->episode_number,
            "title" => $previous->name,
            "thumbnail_image" => asset("images/episodes/" . $previous->thumbnail),
        ] : null,
        "next_episode" => $next ? [
            "id" => $next->id,
            "episode_number" => $next->episode_number,
            "title" => $next->name,
            "thumbnail_image" => asset("images/episodes/" . $next->thumbnail),
        ] : null,
    ]);
}

 



    public function watchProgress(Request $request)
    {
        $validation = Validator::make(
            $request->all(),
            [
                'user_id' => 'required|numeric|exists:user,id',
                'episode_id' => 'required|exists:episodes,id',
                'watch_progress' => 'required|numeric|integer|min:0',
            ],
            [
                'user_id.required' => __('api_msg.please_enter_required_fields'),
                'episode_id.required' => __('api_msg.please_enter_required_fields'),
                'watch_progress.required' => __('api_msg.please_enter_required_fields'),
            ]
        );
        if ($validation->fails()) { 
            $errors = $validation->errors()->first('user_id');
            $errors1 = $validation->errors()->first('episode_id');
            $errors2 = $validation->errors()->first('watch_progress');
            $data['status'] = 400;
            if ($errors) {
                $data['message'] = $errors;
            } elseif ($errors1) {
                $data['message'] = $errors1;
            } elseif ($errors2) {
                $data['message'] = $errors2;
            }
            return $data;
        }
        $userId =$request->user_id; 
	    $episodeId = $request->episode_id;
         

        $episode = Episode::find($episodeId);

	    $alreadyViewed = EpisodeView::where("user_id", $userId)
                                ->where("episode_id", $episodeId)
                                ->exists();

        if (!$alreadyViewed) {

            EpisodeView::create([
                "user_id" => $userId,
                "episode_id" => $episodeId,
                "counted" => 1
            ]);

            $episode->increment("view");
        }
 
        $isWatched = false;

        if ($episode->video_duration > 0) { 
            $isWatched = true;
        }
        
        EpisodeWatchHistory::where('user_id', $userId)->update(['is_watched' => false]);

 
        $progress = EpisodeWatchHistory::updateOrCreate(
            [
                "user_id" => $userId,
                "episode_id" => $request->episode_id
            ],
            [
                "watch_progress" => $request->watch_progress,
                "is_watched" => $isWatched
            ]
        );

        return response()->json([
            "status" => 200,
            "message" => "Watch progress saved successfully",
            "data" => $progress
        ]);
    }

    public function TrailerList(Request $request)
    {
        $validation = Validator::make(
            $request->all(),
            [
                'season_id' => 'required|numeric', 
            ],
            [
                'season_id.required' => __('api_msg.please_enter_required_fields'),
            ]
        );
        if ($validation->fails()) { 
            $errors1 = $validation->errors()->first('season_id');
            $data['status'] = 400;
            if($errors1) {
                $data['message'] = $errors1;
            }
            return $data;
        }
        $userId =$request->user_id;
        $seasonId = $request->season_id; 

        $season = SeasonTrailer::where("season_id", $seasonId)
                    ->where('status', 1)
                    ->orderBy("position", "ASC")
                    ->get();

        if (!$season) {
            return response()->json([
                "status" => 404,
                "message" => "Season not found",
                "data" => []
            ]);
        }
        
        foreach ($season as $ep) {
 
            $episodesData[] = [
                "id" => $ep->id,
                "season_id" => $seasonId,
                "trailer_number" => $ep->trailer_number,
                "title" => $ep->title,
                "description" => $ep->meta_desc,
                "thumbnail_image" => asset("images/season_trailers/" . $ep->thumbnail),
                "landscape" => asset("images/season_trailers/" . $ep->landscape),
                "video_url" => $ep->video_url,
                "duration" => 0
            ];
        }

        return response()->json([
            "status" => 200,
            "message" => "Season trailer retrieved successfully",
            "season" => [
                "season_id" => $seasonId, 
                "total_trailer" => count($episodesData),
                "trailer_list" => $episodesData
            ], 
        ]);
    }



    public function listV(Request $request)
    {
        $validation = Validator::make(
            $request->all(),
            [
                'user_id' => 'required|numeric|exists:user,id',
                'season_id' => 'required|numeric', 
            ],
            [
                'user_id.required' => __('api_msg.please_enter_required_fields'),
                'season_id.required' => __('api_msg.please_enter_required_fields'),
            ]
        );
        if ($validation->fails()) { 
            $errors = $validation->errors()->first('user_id'); 
            $errors1 = $validation->errors()->first('season_id');
            $data['status'] = 400;
            if ($errors) {
                $data['message'] = $errors;
            }elseif ($errors1) {
                $data['message'] = $errors1;
            }
            return $data;
        }
        $userId =$request->user_id;
        $seasonId = $request->season_id; 

        $season = Season::where("id", $seasonId)
                    ->where("isActive", 1)
                    ->first();

        if (!$season) {
            return response()->json([
                "status" => 404,
                "message" => "Season not found",
                "data" => []
            ]);
        }
 
        $episodes = Episode::where("season_id", $seasonId)
                    ->orderBy("episode_number", "ASC")
                    ->get();

        if ($episodes->isEmpty()) {
            return response()->json([
                "status" => 204,
                "message" => "No episodes found",
                "data" => []
            ]);
        }

        $episodesData = [];

        foreach ($episodes as $ep) {
 
            $likes = EpisodeReaction::where("episode_id", $ep->id)
                        ->where("is_like", 1)
                        ->count();

            $dislikes = EpisodeReaction::where("episode_id", $ep->id)
                        ->where("is_dislike", 1)
                        ->count();

            $superLikes = EpisodeReaction::where("episode_id", $ep->id)
                        ->where("is_superlike", 1)
                        ->count();
 
            $myReaction = null;
            $watchHistory = null;
            if ($userId) {
                $myReaction = EpisodeReaction::where("user_id", $userId)
                                ->where("episode_id", $ep->id)
                                ->first();
                 $watchHistory = EpisodeWatchHistory::where('user_id', $userId)
                    ->where('episode_id', $ep->id)
                    ->first();
 
            }
 
            $subtitles = [];
            
            
            // Cast data
            $castData = [];
            if (!empty($ep->cast_id)) {
                $castIds = explode(',', $ep->cast_id);
                $castCollection = Cast::whereIn('id', $castIds)->get();
                
                foreach ($castCollection as $cast) {
                    $castData[] = [
                        "id" => $cast->id,
                        "name" => $cast->name,
                        "image" => asset("images/cast/" . $cast->image),
                        // "image" => "https://upload.wikimedia.org/wikipedia/commons/thumb/b/b6/Image_created_with_a_mobile_phone.png/250px-Image_created_with_a_mobile_phone.png",
                        
                        "type" => $cast->type
                    ];
                }
            }

            // Language data
            $languageData = [];
            if (!empty($ep->language_id)) {
                $languageIds = explode(',', $ep->language_id);

                try {
                    $languages = \App\Models\Language::whereIn('id', $languageIds)->get();
                    foreach ($languages as $lang) {
                        $languageData[] = [
                            "id" => $lang->id,
                            "name" => $lang->name,
                            "image" => asset("images/language/" . $lang->image)
                        ];
                    }
                } catch (\Exception $e) {

                }
            }
            
            
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
 
            $episodesData[] = [
                "id" => $ep->id,
                "season_id" => $seasonId,
                "episode_number" => $ep->episode_number,

                "title" => $ep->name,
                "description" => $ep->description,
                
                "cast" => $castData,
                "language" => $languageData,
                
                "thumbnail_image" => asset("images/episodes/" . $ep->landscape),

                "video_url" => $this->encryptVideoUrl($ep->video_1080), 
                "video_320" => $this->encryptVideoUrl($ep->video_320),
                "video_480" => $this->encryptVideoUrl($ep->video_480),
                "video_720" => $this->encryptVideoUrl($ep->video_720),
                "video_1080" => $this->encryptVideoUrl($ep->video_1080),

                "upload_type" => $ep->video_upload_type,

                "duration" => $ep->video_duration,
 
                "watch_progress" => $watchHistory ? $watchHistory->watch_progress : 0,

                "is_watched" => $watchHistory ? (bool)$watchHistory->is_watched : false,
                
                "warning_text"=> $ep->warning_text,
                "warning_text_start_time"=> $ep->warning_text_start_time ?? "00:31",
                "warning_text_end_time"=>$ep->warning_text_end_time ?? "00:31",
                "skip_enable"=> $ep->skip_enable ==1 ? true:false,
                "start_time"=> $ep->skip_start ?? "00:31",
                "end_time"=> $ep->skip_end ?? "00:31",
                
                
                
                "is_watched" => false,
                
                "is_buy" =>  0,
                "is_premium" => 0,
 
                "stats" => [
                    "likes" => $likes,
                    "dislikes" => $dislikes,
                    "super_likes" => $superLikes,

                    "is_liked" => $myReaction ? ($myReaction->is_like == 1) : false,
                    "is_disliked" => $myReaction ? ($myReaction->is_dislike == 1) : false,
                    "is_super_liked" => $myReaction ? ($myReaction->is_superlike == 1) : false,
                ],

                "subtitles" => $subtitles
            ];
        }

        return response()->json([
            "status" => 200,
            "message" => "Season episodes retrieved successfully",
            "season" => [
                "season_id" => $season->id,
                "season_name" => $season->name ?? "Season " . $season->season_number,
                "description" => $season->meta_desc ?? "",
                "total_episodes" => count($episodesData),
                "episodes" => $episodesData
            ], 
        ]);
    }

  private function encryptVideoUrl($url)
    {
        if (!$url) return null;
    
        $key = "YeSakilKaCodeHeiMaheshSir"; // same key React me bhi
    
        $iv = substr(hash('sha256', $key), 0, 16);
    
        $encrypted = openssl_encrypt($url, 'AES-256-CBC', $key, 0, $iv);
    
        return base64_encode($encrypted);
    }









}
