<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cast;
use App\Models\Episode;
use App\Models\Type;
use App\Models\Language; 
use App\Models\WebSeries; 
use App\Models\Season; 
use Validator;
use Exception;

class EpisodeController extends Controller
{
    private $folder_exlusive_movie = "episodes_movie";
    private $folder_video = "episodes";
    private $folder_image = "episodes";
    private $folder_cast = "cast";


    public function index(Request $request)
    {
        
        try {

            $input_search = $request['input_search'];
            $input_type = $request['input_type'];

            if ($input_search != null && isset($input_search)) {

                if ($input_type != 0) { 
                    
                    $video_list = Episode::where('name', 'LIKE', "%{$input_search}%")
                        ->where('video_type', '!=', 5)
                        ->where('type_id', $input_type)
                        ->with('type')
                        ->orderBy('id', 'desc')->paginate(15);
                } else {
                    $video_list = Episode::where('name', 'LIKE', "%{$input_search}%")
                        ->where('video_type', '!=', 5)
                        ->with('type')
                        ->orderBy('id', 'desc')->paginate(15);
                }
            } else { 
                if ($input_type != 0) {
                    $video_list = Episode::where('video_type', '!=', 5)->where('type_id', $input_type)->with('type')->orderBy('id', 'desc')->paginate(15);
                } else {
                    $video_list = Episode::where('video_type', '!=', 5)->with('type')->orderBy('id', 'desc')->paginate(15);
                }
            }
            imageNameToUrl($video_list, 'thumbnail', $this->folder_video);
            imageNameToUrl($video_list, 'landscape', $this->folder_video);
            videoNameToUrl($video_list, 'video_320', $this->folder_video);

            $type = Type::where('type', 1)->latest()->get();
			
			$queryArray = [];
			if(!empty($_GET)){
			   $queryArray = $_GET;
			}
            
            return view('admin.episode.index', ['result' => $video_list, 'type' => $type, 'queryArray' => $queryArray]);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function add()
    {
        try {

            $params['channel'] = array();
            
            $params['category'] =  array();
            $params['language'] = Language::get();
            $params['type'] = Type::where('type', 1)->get();
            
            $params['cast'] = Cast::get();
            $params['webseries'] = WebSeries::where('isActive', 1)->pluck('title', 'id');

            return view('admin.episode.add', $params);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         
        try {
            if ($request->video_upload_type == "server_video") {
                $validator = Validator::make($request->all(), [
                    'name' => 'required|min:2',
                    'webseries_id' => 'required',
                    'season_id' => 'required',
                    'episode_number' => 'required',
                    'category_id' => 'required',
                    'language_id' => 'required',
                    //'cast_id' => 'required',
                    'type_id' => 'required',
                    'video_upload_type' => 'required',
                    'description' => 'required',
                    //'video_duration' => 'required|after_or_equal:00:00:01',
                    'is_premium' => 'required',
                    'is_title' => 'required',
                    'video_url_1080' => 'required',
                    'thumbnail' => 'image|mimes:jpeg,png,jpg|max:2048', 
                ]);
            } else {
                $validator = Validator::make($request->all(), [
                    'webseries_id' => 'required',
                    'season_id' => 'required',
                    'episode_number' => 'required',
                    'name' => 'required|min:2',
                    // 'category_id' => 'required',
                    'language_id' => 'required',
                    //'cast_id' => 'required',
                    // 'type_id' => 'required',
                    'video_upload_type' => 'required',
                    'description' => 'required', 
                    'is_premium' => 'required',
                    'is_title' => 'required',
                    'video_url_1080' => 'required',
                    'thumbnail' => 'image|mimes:jpeg,png,jpg|max:2048',
                    'landscape' => 'image|mimes:jpeg,png,jpg|max:2048',
                ]);
            }

            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            // $category_id = implode(',', $request->category_id);
            $category_id = 0;
            $language_id = implode(',', $request->language_id);
            $cast_id = 0;
            if(!empty($request->cast_id)){
                $cast_id = implode(',', $request->cast_id);
            }

			$input = $request->all();
            $video = new Episode();
            $video->is_exclusive_movie = isset($request->is_exclusive_movie) ? $request->is_exclusive_movie : 0;
			if($video->is_exclusive_movie == 1) {
				$exclusive_movie_data = [];
				$exclusive_movie_data['trailer_url'] = !empty($input['exclusive_movie_data']['trailer_url']) ? $input['exclusive_movie_data']['trailer_url'] : '';
				$exclusive_movie_data['trailer_image'] = '';
				if (!empty($input['exclusive_movie_data']['trailer_image'])) {
					$exclusive_movie_data['trailer_image'] = saveImage($input['exclusive_movie_data']['trailer_image'], $this->folder_exlusive_movie, true);
				}
				$exclusive_movie_data['teaser_url'] = !empty($input['exclusive_movie_data']['teaser_url']) ? $input['exclusive_movie_data']['teaser_url'] : '';
				$exclusive_movie_data['teaser_image'] = '';
				if (!empty($input['exclusive_movie_data']['teaser_image'])) {
					$exclusive_movie_data['teaser_image'] = saveImage($input['exclusive_movie_data']['teaser_image'], $this->folder_exlusive_movie, true);
				}
				$exclusive_movie_data['promo_url'] = !empty($input['exclusive_movie_data']['promo_url']) ? $input['exclusive_movie_data']['promo_url'] : '';
				$exclusive_movie_data['promo_image'] = '';
				if (!empty($input['exclusive_movie_data']['promo_image'])) {
					$exclusive_movie_data['promo_image'] = saveImage($input['exclusive_movie_data']['promo_image'], $this->folder_exlusive_movie, true);
				}
				$video->exclusive_movie_data = json_encode($exclusive_movie_data);
			} 
            
            $video->web_series_id = $request->webseries_id;
            $video->season_id = $request->season_id;
            $video->episode_number = $request->episode_number;
            $video->warning_text = $request->warning_text;
            $video->warning_text_start_time = $request->warning_text_start;
            $video->warning_text_end_time = $request->warning_text_end;
            $video->category_id = $category_id ?? 1;
            $video->language_id = $language_id;
            $video->cast_id = $cast_id;
            $video->type_id = $request->type_id ?? 1;
            $video->video_type = 1;
            $video->name = $request->name;
            $video->video_upload_type = $request->video_upload_type;
            $video->is_premium = $request->is_premium;
            $video->description = $request->description;
            $video->video_duration = TimeToMilliseconds($request->video_duration);
            $video->is_title = $request->is_title;
            $video->view = 0;
            $video->status = '1';
            $video->skip_enable = $request->skip_enable;
            $video->skip_start  = $request->skip_enable ? $request->skip_start : null;
            $video->skip_end    = $request->skip_enable ? $request->skip_end : null;


            $video->is_end_credit = $request->is_end_credit ?? 0;
            $video->end_credit_time  = $request->end_credit_time ? $request->end_credit_time : 0;
            // $video->intro_end    = $request->intro_end ? $request->intro_end : null;

            $video->is_like = $request->is_like; 
            $video->is_dislike = $request->is_dislike; 
            $video->is_superlike = $request->is_superlike;
            $video->wishlist = $request->wishlist;
			
			if(!empty($input['related_videos'])) {
				$video->related_videos = implode(",", $input['related_videos']);
			}

            // Release Data
            $video->release_date = "";
            if ($request->release_date) {
                $video->release_date = $request->release_date;
            }
            // Is Download
            if ($request->video_upload_type == "server_video" || $request->video_upload_type == "external") {
                $video->download = $request->download;
            } else {
                $video->download = 0;
            }

            // Video (320, 480, 720, 1080)
            if ($request->video_upload_type == "server_video") {

                $video->video_320 = isset($request->upload_video_320) ? $request->upload_video_320 : '';
                $video->video_480 = isset($request->upload_video_480) ? $request->upload_video_480 : '';
                $video->video_720 = isset($request->upload_video_720) ? $request->upload_video_720 : '';
                $video->video_1080 = isset($request->upload_video_1080) ? $request->upload_video_1080 : '';

                $array = explode('.', $request->video_url_1080);
                $video->video_extension = end($array);
            } else {

                $video->video_320 = isset($request->video_url_320) ? $request->video_url_320 : '';
                $video->video_480 = isset($request->video_url_480) ? $request->video_url_480 : '';
                $video->video_720 = isset($request->video_url_720) ? $request->video_url_720 : '';
                $video->video_1080 = isset($request->video_url_1080) ? $request->video_url_1080 : '';

                $array = explode('.', $request->video_url_320);
                $array1 = explode('?', end($array));
                if (isset($array1) && $array1 != null) {
                    $video->video_extension = isset($array1) ? reset($array1) : "";
                } else {
                    $video->video_extension = "";
                }
            }

            // Subtitle_1_2_3
            $video->subtitle_type = isset($request->subtitle_type) ? $request->subtitle_type : '';
            $video->subtitle_lang_1 = isset($request->subtitle_lang_1) ? $request->subtitle_lang_1 : '';
            $video->subtitle_lang_2 = isset($request->subtitle_lang_2) ? $request->subtitle_lang_2 : '';
            $video->subtitle_lang_3 = isset($request->subtitle_lang_3) ? $request->subtitle_lang_3 : '';
            if ($request->subtitle_type == "server_video") {
                $video->subtitle_1 = isset($request->subtitle1) ? $request->subtitle1 : '';
                $video->subtitle_2 = isset($request->subtitle2) ? $request->subtitle2 : '';
                $video->subtitle_3 = isset($request->subtitle3) ? $request->subtitle3 : '';
            } else {
                $video->subtitle_1 = isset($request->subtitle_url_1) ? $request->subtitle_url_1 : '';
                $video->subtitle_2 = isset($request->subtitle_url_2) ? $request->subtitle_url_2 : '';
                $video->subtitle_3 = isset($request->subtitle_url_3) ? $request->subtitle_url_3 : '';
            }

            // Trailer
            $video->trailer_type = isset($request->trailer_type) ? $request->trailer_type : $request->video_upload_type;
            if ($request->trailer_type == "server_video") {
                $video->trailer_url = isset($request->trailer) ? $request->trailer : $request->upload_video_1080;
            } else {
                $video->trailer_url = isset($request->trailer_url) ? $request->trailer_url : $request->video_url_1080;
            }

            $video->release_year = isset($request->release_year) ? $request->release_year : '';
            $video->imdb_rating = isset($request->imdb_rating) ? $request->imdb_rating : 0;

            $video->director_id = "";
            $video->starring_id = "";
            $video->supporting_cast_id = "";
            $video->networks = "";
            $video->maturity_rating = "";
            $video->age_restriction = "";
            $video->max_video_quality = "";
            $video->release_tag = "";

            $org_name = $request->file('thumbnail');
            $org_name1 = $request->file('landscape');
            $video->thumbnail = "";
            $video->landscape = "";

            if ($org_name != null && isset($org_name)) {

                $video->thumbnail = saveImage($org_name, $this->folder_video);
            } elseif ($request->thumbnail_imdb) {

                $url = $request->thumbnail_imdb;
                $S_Name = URLSaveInImage($url, $this->folder_video);
                $video->thumbnail = $S_Name;
            }
            if ($org_name1 != null && isset($org_name1)) {

                $video->landscape = saveImage($org_name1, $this->folder_video);
            } elseif ($request->landscape_imdb) {

                $url = $request->landscape_imdb;
                $S_Name = URLSaveInImage($url, $this->folder_video);
                $video->landscape = $S_Name;
            }

            if ($video->save()) {

                // Send Notification
                $imageURL = Get_Image('video', $video->thumbnail);
                // $noti_array = array(
                //     'id' => $video->id,
                //     'name' => $video->name,
                //     'image' => $imageURL,
                //     'type_id' => $video->type_id,
                //     'video_type' => $video->video_type,
                //     'upcoming_type' => 0,
                //     'description' => string_cut($video->description, 90),
                // );
                //sendNotification($noti_array);

                return response()->json(array('status' => 200, 'success' => __('Label.Data Add Successfully')));
            } else {
                return response()->json(array('status' => 400, 'errors' => __('Label.Data Not Add')));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {

            $idPage = explode('_', $id);
            $id = $idPage[0];

            $queryArray = request()->query();

            // ✅ Episode Record
            $params['result'] = Episode::where('id', $id)->firstOrFail();

            // ✅ Image Convert
            imageNameToUrl([$params['result']], 'thumbnail', $this->folder_video);
            imageNameToUrl([$params['result']], 'landscape', $this->folder_video);

            // ✅ Webseries List
            $params['webseries'] = WebSeries::where('isActive', 1)
                                    ->pluck('title', 'id');

            // ✅ Load Seasons based on episode selected webseries
            $params['seasons'] = Season::where('web_series_id', $params['result']->web_series_id)
                                ->where('isActive', 1)
                                ->get();

            // ✅ Other Dropdowns
            $params['category'] = Array();
            $params['language'] = Language::get();
            $params['type']     = Type::where('type', 1)->get();
            $params['cast']     = Cast::get();
            $params['selected_categories'] = explode(',', $params['result']->category_id);
            $params['selected_languages']  = explode(',', $params['result']->language_id);
            $params['selected_cast']       = explode(',', $params['result']->cast_id);
            $params['queryArray'] = $queryArray;

            // ✅ Exclusive Movie Data Decode
            $params['result']->exclusive_movie_data =
                json_decode($params['result']->exclusive_movie_data, true);

            // ✅ Related Videos Options
            if (!empty($params['result']->related_videos)) {

                $params['related_video_options'] =
                    Episode::whereIn(
                        'id',
                        explode(',', $params['result']->related_videos)
                    )->pluck('name', 'id')->toArray();

            } else {
                $params['related_video_options'] = [];
            }

            return view('admin.episode.edit', $params);

        } catch (Exception $e) {

            return response()->json([
                'status' => 400,
                'errors' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    { 
        try {
            if ($request->video_upload_type == "server_video") {
                $validator = Validator::make($request->all(), [
                    'name' => 'required|min:2',
                    'category_id' => 'required',
                    'language_id' => 'required',
                    //'cast_id' => 'required',
                    'type_id' => 'required',
                    'video_upload_type' => 'required',
                    'description' => 'required',
                    //'video_duration' => 'required|after_or_equal:00:00:01',
                    'is_premium' => 'required',
                    'is_title' => 'required',
                    'thumbnail' => 'image|mimes:jpeg,png,jpg|max:2048',
                    'landscape' => 'image|mimes:jpeg,png,jpg|max:2048',
                ]);
            } else {
                $validator = Validator::make($request->all(), [
                    'webseries_id' => 'required',
                    'season_id' => 'required',
                    'episode_number' => 'required',
                    'name' => 'required|min:2',
                    // 'category_id' => 'required',
                    'language_id' => 'required',
                    //'cast_id' => 'required',
                    // 'type_id' => 'required',
                    'video_upload_type' => 'required',
                    'description' => 'required', 
                    'is_premium' => 'required',
                    'is_title' => 'required',
                    'video_url_1080' => 'required', 
                ]);
            }
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $video = Episode::where('id', $request->id)->first();
			$input = $request->all();
            if (isset($video->id)) {
				$video->is_exclusive_movie = isset($request->is_exclusive_movie) ? $request->is_exclusive_movie : 0;
				if($video->is_exclusive_movie == 1) {
					$exclusive_movie_data = json_decode($video->exclusive_movie_data, 1);
					$exclusive_movie_data['trailer_url'] = $input['exclusive_movie_data']['trailer_url'];
					if (!empty($input['exclusive_movie_data']['trailer_image'])) {
						$exclusive_movie_data['trailer_image'] = saveImage($input['exclusive_movie_data']['trailer_image'], $this->folder_exlusive_movie, true);
					} else {
						$exclusive_movie_data['trailer_image'] = $input['trailer_image_old'];
					}
					$exclusive_movie_data['teaser_url'] = $input['exclusive_movie_data']['teaser_url'];
					if (!empty($input['exclusive_movie_data']['teaser_image'])) {
						$exclusive_movie_data['teaser_image'] = saveImage($input['exclusive_movie_data']['teaser_image'], $this->folder_exlusive_movie, true);
					} else {
						$exclusive_movie_data['teaser_image'] = $input['teaser_image_old'];
					}
					$exclusive_movie_data['promo_url'] = $input['exclusive_movie_data']['promo_url'];
					if (!empty($input['exclusive_movie_data']['promo_image'])) {
						$exclusive_movie_data['promo_image'] = saveImage($input['exclusive_movie_data']['promo_image'], $this->folder_exlusive_movie, true);
					} else {
						$exclusive_movie_data['promo_image'] = $input['promo_image_old'];
					}
					$video->exclusive_movie_data = json_encode($exclusive_movie_data);
				}
				
                $category_id = array();
                $language_id = implode(',', $request->language_id);
                $cast_id = 0;
                if(!empty($request->cast_id) ){
                    $cast_id = implode(',', $request->cast_id);    
                }
                

                $video->channel_id = isset($request->channel_id) ? $request->channel_id : 0;
                $video->category_id = $category_id ?? 0;
                $video->language_id = $language_id;
                $video->cast_id = $cast_id;
                $video->type_id = $request->type_id;
                $video->video_type = 1;
                $video->name = $request->name;
                $video->video_upload_type = $request->video_upload_type;
                $video->description = $request->description;
                $video->video_duration = TimeToMilliseconds($request->video_duration);
                $video->is_premium = $request->is_premium;
                $video->is_title = $request->is_title;
                $video->skip_enable  = $request->skip_enable;
                $video->skip_start   = $request->skip_enable ? $request->skip_start : null;
                $video->skip_end     = $request->skip_enable ? $request->skip_end : null;

                $video->is_end_credit = $request->is_end_credit ?? 0;
                $video->end_credit_time  = $request->end_credit_time ? $request->end_credit_time : 0;

                // $video->skip_intro = $request->skip_intro;
                // $video->intro_start  = $request->intro_start ? $request->intro_start : null;
                // $video->intro_end    = $request->intro_end ? $request->intro_end : null;

                
                $video->web_series_id = $request->webseries_id;
                $video->season_id = $request->season_id;
                $video->episode_number = $request->episode_number;
                $video->warning_text = $request->warning_text;
                $video->warning_text_start_time = $request->warning_text_start;
                $video->warning_text_end_time = $request->warning_text_end;
                $video->is_like = $request->is_like; 
                $video->is_dislike = $request->is_dislike; 
                $video->is_superlike = $request->is_superlike;
                $video->wishlist = $request->wishlist;
                $video->status = '1';
				
				// if(!empty($input['related_videos'])) {
				// 	$video->related_videos = implode(",", $input['related_videos']);
				// } else {
				// 	$video->related_videos = null;
				// }

                // Release Data
                $video->release_date = "";
                if ($request->release_date) {
                    $video->release_date = $request->release_date;
                }

                if ($request->video_upload_type == "server_video" || $request->video_upload_type == "external") {
                    $video->download = $request->download;
                } else {
                    $video->download = 0;
                }

                // Videos (320, 420, 720, 1080)
                if ($request->video_upload_type == "server_video") {

                    if ($request->video_upload_type == $request->old_video_upload_type) {

                        if ($request->upload_video_320) {

                            $array = explode('.', $request->upload_video_320);
                            $video->video_extension = end($array);

                            $video->video_320 = $request->upload_video_320;
                            deleteImageToFolder($this->folder_video, basename($request->old_video_320));
                        }
                        if ($request->upload_video_480) {

                            $video->video_480 = $request->upload_video_480;
                            deleteImageToFolder($this->folder_video, basename($request->old_video_480));
                        }
                        if ($request->upload_video_720) {

                            $video->video_720 = $request->upload_video_720;
                            deleteImageToFolder($this->folder_video, basename($request->old_video_720));
                        }
                        if ($request->upload_video_1080) {

                            $video->video_1080 = $request->upload_video_1080;
                            deleteImageToFolder($this->folder_video, basename($request->old_video_1080));
                        }
                    } else {
                        if ($request->upload_video_320) {

                            $array = explode('.', $request->upload_video_320);
                            $video->video_extension = end($array);

                            $video->video_320 = $request->upload_video_320;
                            deleteImageToFolder($this->folder_video, basename($request->old_video_320));
                        } else {
                            $video->video_320 = "";
                        }
                        if ($request->upload_video_480) {

                            $video->video_480 = $request->upload_video_480;
                            deleteImageToFolder($this->folder_video, basename($request->old_video_480));
                        } else {
                            $video->video_480 = "";
                        }
                        if ($request->upload_video_720) {

                            $video->video_720 = $request->upload_video_720;
                            deleteImageToFolder($this->folder_video, basename($request->old_video_720));
                        } else {
                            $video->video_720 = "";
                        }
                        if ($request->upload_video_1080) {

                            $video->video_1080 = $request->upload_video_1080;
                            deleteImageToFolder($this->folder_video, basename($request->old_video_1080));
                        } else {
                            $video->video_1080 = "";
                        }
                    }
                } else {

                    deleteImageToFolder($this->folder_video, basename($request->old_video_320));
                    deleteImageToFolder($this->folder_video, basename($request->old_video_480));
                    deleteImageToFolder($this->folder_video, basename($request->old_video_720));
                    deleteImageToFolder($this->folder_video, basename($request->old_video_1080));

                    $video->video_480 = "";
                    $video->video_720 = "";
                    $video->video_1080 = "";

                    if ($request->video_url_320) {

                        $array = explode('.', $request->video_url_320);
                        $array1 = explode('?', end($array));
                        if (isset($array1) && $array1 != null) {
                            $video->video_extension = isset($array1) ? reset($array1) : "";
                        } else {
                            $video->video_extension = "";
                        }

                        $video->video_320 = $request->video_url_320;
                    }
                    if ($request->video_url_480) {
                        $video->video_480 = $request->video_url_480;
                    }
                    if ($request->video_url_720) {
                        $video->video_720 = $request->video_url_720;
                    }
                    if ($request->video_url_1080) {
                        $video->video_1080 = $request->video_url_1080;
                    }
                }

                // Subtitle
                $video->subtitle_type = isset($request->subtitle_type) ? $request->subtitle_type : '';
                $video->subtitle_lang_1 = isset($request->subtitle_lang_1) ? $request->subtitle_lang_1 : '';
                $video->subtitle_lang_2 = isset($request->subtitle_lang_2) ? $request->subtitle_lang_2 : '';
                $video->subtitle_lang_3 = isset($request->subtitle_lang_3) ? $request->subtitle_lang_3 : '';
                if ($request->subtitle_type == "server_video") {

                    if ($request->subtitle_type == $request->old_subtitle_type) {
                        if ($request->subtitle1) {
                            $video->subtitle_1 = $request->subtitle1;
                            deleteImageToFolder($this->folder_video, basename($request->old_subtitle_1));
                        }
                        if ($request->subtitle2) {
                            $video->subtitle_2 = $request->subtitle2;
                            deleteImageToFolder($this->folder_video, basename($request->old_subtitle_2));
                        }
                        if ($request->subtitle3) {
                            $video->subtitle_3 = $request->subtitle3;
                            deleteImageToFolder($this->folder_video, basename($request->old_subtitle_3));
                        }
                    } else {
                        if ($request->subtitle1) {
                            $video->subtitle_1 = $request->subtitle1;
                            deleteImageToFolder($this->folder_video, basename($request->old_subtitle_1));
                        } else {
                            $video->subtitle_1 = "";
                        }
                        if ($request->subtitle2) {
                            $video->subtitle_2 = $request->subtitle2;
                            deleteImageToFolder($this->folder_video, basename($request->old_subtitle_2));
                        } else {
                            $video->subtitle_2 = "";
                        }
                        if ($request->subtitle3) {
                            $video->subtitle_3 = $request->subtitle3;
                            deleteImageToFolder($this->folder_video, basename($request->old_subtitle_3));
                        } else {
                            $video->subtitle_3 = "";
                        }
                    }
                } else {

                    deleteImageToFolder($this->folder_video, basename($request->old_subtitle_1));
                    deleteImageToFolder($this->folder_video, basename($request->old_subtitle_2));
                    deleteImageToFolder($this->folder_video, basename($request->old_subtitle_3));

                    $video->subtitle_1 = "";
                    $video->subtitle_2 = "";
                    $video->subtitle_3 = "";

                    if ($request->subtitle_1) {
                        $video->subtitle_1 = $request->subtitle_url_1;
                    }
                    if ($request->subtitle_2) {
                        $video->subtitle_2 = $request->subtitle_url_2;
                    }
                    if ($request->subtitle_3) {
                        $video->subtitle_3 = $request->subtitle_url_3;
                    }
                }

                // Trailer
                $video->trailer_type = isset($request->trailer_type) ? $request->trailer_type : $request->video_upload_type;
                if ($request->trailer_type == "server_video") {

                    if ($request->trailer_type == $request->old_trailer_type) {

                        if ($request->upload_video_1080) {
                            $video->trailer_url = $request->upload_video_1080;
                            deleteImageToFolder($this->folder_video, basename($request->old_trailer));
                        }
                    } else {
                        if ($request->video_url_1080) {
                            $video->trailer_url = $request->video_url_1080;//upload_video_1080
                            deleteImageToFolder($this->folder_video, basename($request->old_trailer));
                        } else {
                            $video->trailer_url = "";
                        }
                    }
                } else {
                    deleteImageToFolder($this->folder_video, basename($request->old_trailer));
                    $video->trailer_url = "";
                    if ($request->video_url_1080) {
                        $video->trailer_url = $request->video_url_1080;
                    }
                }

                $org_name = $request->file('thumbnail');
                $org_name1 = $request->file('landscape');

                if ($org_name != null && isset($org_name)) {

                    $video->thumbnail = saveImage($org_name, $this->folder_video);
                    deleteImageToFolder($this->folder_video, basename($request->old_thumbnail));
                } elseif ($request->thumbnail_imdb) {

                    $url = $request->thumbnail_imdb;
                    $S_Name = URLSaveInImage($url, $this->folder_video);
                    $video->thumbnail = $S_Name;
                    deleteImageToFolder($this->folder_video, basename($request->old_thumbnail));
                }
                if ($org_name1 != null && isset($org_name1)) {

                    $video->landscape = saveImage($org_name1, $this->folder_video);
                    deleteImageToFolder($this->folder_video, basename($request->old_landscape));
                } elseif ($request->landscape_imdb) {

                    $url = $request->landscape_imdb;
                    $S_Name = URLSaveInImage($url, $this->folder_video);
                    $video->landscape = $S_Name;
                    deleteImageToFolder($this->folder_video, basename($request->old_landscape));
                }

                $video->release_year = isset($request->release_year) ? $request->release_year : '';
                $video->imdb_rating = isset($request->imdb_rating) ? $request->imdb_rating : 0;

                if ($video->save()) {
                    return response()->json(array('status' => 200, 'success' => __('Label.Data Edit Successfully')));
                } else {
                    return response()->json(array('status' => 400, 'errors' => __('Label.Data Not Updated')));
                }
            } else {
                return response()->json(array('status' => 400, 'errors' => __('Label.Data Not Updated')));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage().$e->getLine()));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     public function delete($id)
    {
        try {
            $episode = Episode::where('id', $id)->first(); 
             
                if ($episode->delete()) {

                    deleteImageToFolder($this->folder_video, $episode->thumbnail);
                    deleteImageToFolder($this->folder_video, $episode->landscape);
                    return redirect()->route('episodes.video')->with('success', __('Label.Data Delete Successfully'));
                }
           // }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }


    public function getSeasons(Request $request)
    {
        $seasons = Season::where('web_series_id', $request->webseries_id)->where('isActive', 1)->get();
        return response()->json($seasons);
    }

     public function detail($id)
    {
        try {
            $params['result'] = Episode::with(['webseries', 'season'])
                    ->where('id', $id)
                    ->first();
 

            imageNameToUrl(array($params['result']), 'thumbnail', $this->folder_video);
            imageNameToUrl(array($params['result']), 'landscape', $this->folder_video);

            $x = explode(",", $params['result']->category_id);
            $y = explode(",", $params['result']->language_id);
            $z = explode(",", $params['result']->cast_id);

            $params['channel'] = array();
            $params['category'] = array();
            $params['language'] = Language::select('name')->whereIn('id', $y)->get();
            $params['cast'] = Cast::select('name', 'type')->whereIn('id', $z)->get();

            return view('admin.episode.detail_page', $params);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

}
	