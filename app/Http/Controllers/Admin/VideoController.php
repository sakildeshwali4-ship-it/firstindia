<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\App_Section;
use App\Models\Cast;
use App\Models\Category;
use App\Models\Channel;
use App\Models\Channel_Section;
use App\Models\Language;
use App\Models\Type;
use App\Models\Video;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Validator;
use Exception;

// Video Type = 1-Video, 2-Show, 3-Language, 4-Category, 5-Upcoming
// Video Upload Type = server_video, external, youtube, vimeo
// Subtitle Type = server_video, external
// Trailer Type = server_video, external, youtube

class VideoController extends Controller
{
    private $folder_video = "video";
    private $folder_cast = "cast";

    public function index(Request $request)
    {
        try {

            $input_search = $request['input_search'];
            $input_type = $request['input_type'];

            if ($input_search != null && isset($input_search)) {

                if ($input_type != 0) {

                    $video_list = Video::where('name', 'LIKE', "%{$input_search}%")
                        ->where('video_type', '!=', 5)
                        ->where('type_id', $input_type)
                        ->with('type')
                        ->orderBy('id', 'desc')->paginate(15);
                } else {

                    $video_list = Video::where('name', 'LIKE', "%{$input_search}%")
                        ->where('video_type', '!=', 5)
                        ->with('type')
                        ->orderBy('id', 'desc')->paginate(15);
                }
            } else {

                if ($input_type != 0) {
                    $video_list = Video::where('video_type', '!=', 5)->where('type_id', $input_type)->with('type')->orderBy('id', 'desc')->paginate(15);
                } else {
                    $video_list = Video::where('video_type', '!=', 5)->with('type')->orderBy('id', 'desc')->paginate(15);
                }
            }

            imageNameToUrl($video_list, 'thumbnail', $this->folder_video);
            imageNameToUrl($video_list, 'landscape', $this->folder_video);
            videoNameToUrl($video_list, 'video_320', $this->folder_video);

            $type = Type::where('type', 1)->latest()->get();
			$pageNo = 1;
			if(!empty($request->get('page'))){
			   $pageNo = $request->get('page');
			}
            return view('admin.video.index', ['result' => $video_list, 'type' => $type, 'pageNo' => $pageNo]);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function add()
    {
        try {

            $params['channel'] = Channel::get();
            $params['category'] = Category::get();
            $params['language'] = Language::get();
            $params['type'] = Type::where('type', 1)->get();
            $params['cast'] = Cast::get();

            return view('admin.video.add', $params);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function save(Request $request)
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
                    'video_url_1080' => 'required',
                    'thumbnail' => 'image|mimes:jpeg,png,jpg|max:2048',
                    'landscape' => 'image|mimes:jpeg,png,jpg|max:2048',
                ]);
            } else {
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
                    'video_url_1080' => 'required',
                    'thumbnail' => 'image|mimes:jpeg,png,jpg|max:2048',
                    'landscape' => 'image|mimes:jpeg,png,jpg|max:2048',
                ]);
            }
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $category_id = implode(',', $request->category_id);
            $language_id = implode(',', $request->language_id);
            $cast_id = 0;
            if(!empty($request->cast_id)){
                $cast_id = implode(',', $request->cast_id);
            }

            $video = new Video();
            $video->channel_id = isset($request->channel_id) ? $request->channel_id : 0;
            $video->category_id = $category_id;
            $video->language_id = $language_id;
            $video->cast_id = $cast_id;
            $video->type_id = $request->type_id;
            $video->video_type = 1;
            $video->name = $request->name;
            $video->video_upload_type = $request->video_upload_type;
            $video->is_premium = $request->is_premium;
            $video->description = $request->description;
            $video->video_duration = TimeToMilliseconds($request->video_duration);
            $video->is_title = $request->is_title;
            $video->view = 0;
            $video->status = 1;
            $video->skip_enable = $request->skip_enable;
            $video->skip_start  = $request->skip_enable ? $request->skip_start : null;
            $video->skip_end    = $request->skip_enable ? $request->skip_end : null;

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
                $noti_array = array(
                    'id' => $video->id,
                    'name' => $video->name,
                    'image' => $imageURL,
                    'type_id' => $video->type_id,
                    'video_type' => $video->video_type,
                    'upcoming_type' => 0,
                    'description' => string_cut($video->description, 90),
                );
                sendNotification($noti_array);

                return response()->json(array('status' => 200, 'success' => __('Label.Data Add Successfully')));
            } else {
                return response()->json(array('status' => 400, 'errors' => __('Label.Data Not Add')));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function detail($id)
    {
        try {
            $params['result'] = Video::where('id', $id)->first();

            imageNameToUrl(array($params['result']), 'thumbnail', $this->folder_video);
            imageNameToUrl(array($params['result']), 'landscape', $this->folder_video);

            $x = explode(",", $params['result']->category_id);
            $y = explode(",", $params['result']->language_id);
            $z = explode(",", $params['result']->cast_id);

            $params['channel'] = Channel::select('name')->where('id', $params['result']->channel_id)->first();
            $params['category'] = Category::select('name')->whereIn('id', $x)->get();
            $params['language'] = Language::select('name')->whereIn('id', $y)->get();
            $params['cast'] = Cast::select('name', 'type')->whereIn('id', $z)->get();

            return view('admin.video.detail_page', $params);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function edit($id)
    {
        try {
			$idPage = explode('_', $id);
			$id = $idPage[0];
			$pageNo = ['page' => 1];
			if(!empty($idPage[1])) {
				$pageNo['page'] = $idPage[1];
			}
            $params['result'] = Video::where('id', $id)->first();

            imageNameToUrl(array($params['result']), 'thumbnail', $this->folder_video);
            imageNameToUrl(array($params['result']), 'landscape', $this->folder_video);

            $params['channel'] = Channel::get();
            $params['category'] = Category::get();
            $params['language'] = Language::get();
            $params['type'] = Type::where('type', 1)->get();
            $params['cast'] = Cast::get();
            $params['page'] = $pageNo;

            return view('admin.video.edit', $params);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

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
                    'video_url_1080' => 'required',
                    'thumbnail' => 'image|mimes:jpeg,png,jpg|max:2048',
                    'landscape' => 'image|mimes:jpeg,png,jpg|max:2048',
                ]);
            }
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $video = Video::where('id', $request->id)->first();

            if (isset($video->id)) {

                $category_id = implode(',', $request->category_id);
                $language_id = implode(',', $request->language_id);
                $cast_id = 0;
                if(!empty($request->cast_id) ){
                    $cast_id = implode(',', $request->cast_id);    
                }
                

                $video->channel_id = isset($request->channel_id) ? $request->channel_id : 0;
                $video->category_id = $category_id;
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
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function delete($id)
    {
        try {
            $video = Video::where('id', $id)->first();
            $App_Section = App_Section::whereRaw("find_in_set('" . $video->id . "',app_section.video_id)")->first();
            $Channel_Section = Channel_Section::whereRaw("find_in_set('" . $video->id . "',channel_section.video_id)")->first();
            //dd($App_Section);
            /*if (!is_null($App_Section)) {
                return back()->with('error', "This Video is used on some other table so you can not remove it.");
            } elseif (!is_null($Channel_Section)) {
                return back()->with('error', "This Video is used on some other table so you can not remove it.");
            } else {*/
                if ($video->delete()) {

                    deleteImageToFolder($this->folder_video, $video->thumbnail);
                    deleteImageToFolder($this->folder_video, $video->landscape);

                    deleteImageToFolder($this->folder_video, $video->video_320);
                    deleteImageToFolder($this->folder_video, $video->video_480);
                    deleteImageToFolder($this->folder_video, $video->video_720);
                    deleteImageToFolder($this->folder_video, $video->video_1080);

                    deleteImageToFolder($this->folder_video, $video->trailer_url);

                    deleteImageToFolder($this->folder_video, $video->subtitle_1);
                    deleteImageToFolder($this->folder_video, $video->subtitle_2);
                    deleteImageToFolder($this->folder_video, $video->subtitle_3);

                    return redirect()->route('video')->with('success', __('Label.Data Delete Successfully'));
                }
           // }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function saveChunk()
    {

        @set_time_limit(5 * 60);

        $targetDir = public_path('/images/video');

        //$targetDir = 'uploads';

        $cleanupTargetDir = true; // Remove old files

        $maxFileAge = 5 * 3600; // Temp file age in seconds

        // Create target dir
        if (!file_exists($targetDir)) {
            @mkdir($targetDir);
        }

        // Get a file name
        if (isset($_REQUEST["name"])) {
            $fileName = $_REQUEST["name"];
        } elseif (!empty($_FILES)) {
            $fileName = $_FILES["file"]["name"];
        } else {
            $fileName = uniqid("file_");
        }
        $category_image = $fileName;
        $filePath = $targetDir . DIRECTORY_SEPARATOR . $category_image;
        // Chunking might be enabled

        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
        // Remove old temp files

        if ($cleanupTargetDir) {
            if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
            }

            while (($file = readdir($dir)) !== false) {
                $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;
                // If temp file is current file proceed to the next
                if ($tmpfilePath == "{$filePath}.part") {
                    continue;
                }

                // Remove temp file if it is older than the max age and is not the current file
                if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
                    @unlink($tmpfilePath);
                }
            }
            closedir($dir);
        }

        // Open temp file

        if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
            die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
        }

        if (!empty($_FILES)) {
            if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
            }

            // Read binary input stream and append it to temp file
            if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
            }
        } else {
            if (!$in = @fopen("php://input", "rb")) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
            }
        }

        while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
        }

        @fclose($out);
        @fclose($in);
        // Check if file has been uploaded
        if (!$chunks || $chunk == $chunks - 1) {
            // Strip the temp .part suffix off
            rename("{$filePath}.part", $filePath);
        }
        // Return Success JSON-RPC response
        die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
    }

    // IMDb
    public function SerachName($txtVal)
    {
        try {
            $imdbTitle = $txtVal;
            $imdb_api_key = setting_imdb_key();
            if (strlen($imdbTitle) >= 3 && $imdb_api_key != "" && isset($imdb_api_key) && $imdb_api_key != null) {
                $url = 'https://imdb-api.com/API/SearchMovie/' . $imdb_api_key . '/' . $imdbTitle;
                $response = Http::get($url);
                $Status = $response->getStatusCode();
                $Data = $response->json();

                if ($Status == 200) {
                    return response()->json(array('status' => 200, 'success' => __('Label.Data Edit Successfully'), 'data' => $Data));
                }
            } else {
                return response()->json(array('status' => 400, 'success' => "Enter Imdb Key"));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function GetData($imdbID)
    {
        try {
            $imdb_api_key = setting_imdb_key();

            if ($imdb_api_key != "" && isset($imdb_api_key) && $imdb_api_key != null) {

                $url = 'https://imdb-api.com/API/Title/' . $imdb_api_key . '/' . $imdbID;
                $response = Http::get($url);
                $Status = $response->getStatusCode();
                $movies = $response->json();

                // Category
                $C_Id = [];
                $C_Insert_Data = [];
                if (isset($movies['genres']) && $movies['genres'] != "N/A" && $movies['genres'] != null) {

                    $Cat_name = explode(", ", $movies['genres']);
                    for ($i = 0; $i < count($Cat_name); $i++) {

                        $Category = Category::where(DB::raw('lower(name)'), strtolower($Cat_name[$i]))->first();
                        if (!empty($Category)) {

                            $C_Id[] = $Category['id'];
                        } else {

                            $insert = new Category();
                            $insert->name = $Cat_name[$i];
                            $insert->image = "";
                            $insert->save();

                            $C_Id[] = $insert->id;
                            $C_Insert_Data[] = $insert;
                        }
                    }
                }

                // Language
                $L_Id = [];
                $L_Insert_Data = [];
                if (isset($movies['languages']) && $movies['languages'] != "N/A" && $movies['languages'] != null) {

                    $Lang_name = explode(", ", $movies['languages']);
                    for ($i = 0; $i < count($Lang_name); $i++) {

                        $Language = Language::where(DB::raw('lower(name)'), strtolower($Lang_name[$i]))->first();
                        if (!empty($Language)) {

                            $L_Id[] = $Language['id'];
                        } else {

                            $insert = new Language();
                            $insert->name = $Lang_name[$i];
                            $insert->image = "";
                            $insert->save();

                            $L_Id[] = $insert->id;
                            $L_Insert_Data[] = $insert;
                        }
                    }
                }

                // Cast
                $Cast_Id = [];
                $Cast_Insert_Data = [];
                if (isset($movies['directors']) && $movies['directors'] != "N/A" && $movies['directors'] != null) {

                    $Director_name = explode(", ", $movies['directors']);
                    for ($i = 0; $i < count($Director_name); $i++) {

                        $Director = Cast::where(DB::raw('lower(name)'), strtolower($Director_name[$i]))->first();
                        if (!empty($Director)) {

                            $Cast_Id[] = $Director['id'];
                        } else {

                            $url = 'https://imdb-api.com/en/API/SearchName/' . $imdb_api_key . '/' . $Director_name[$i];
                            $response = Http::get($url);
                            $Status = $response->getStatusCode();
                            $Data = (array) $response->json();

                            $insert = new Cast();
                            $insert->name = $Director_name[$i];
                            $insert->type = "Director";
                            if (isset($Data['results']) && count($Data['results']) > 0 && $Data['results'] != null) {

                                if ($Data['results'][0]['image'] != "" && $Data['results'][0]['image'] != null) {

                                    $CI_Name = URLSaveInImage($Data['results'][0]['image'], $this->folder_cast);
                                    $insert->image = $CI_Name;
                                } else {
                                    $insert->image = "";
                                }
                                $insert->personal_info = $Data['results'][0]['description'];
                            } else {

                                $insert->image = "";
                                $insert->personal_info = "";
                            }
                            $insert->save();

                            $Cast_Id[] = $insert->id;
                            $Cast_Insert_Data[] = $insert;
                        }
                    }
                }
                if (isset($movies['writers']) && $movies['writers'] != "N/A" && $movies['writers'] != null) {

                    $Director_name = explode(", ", $movies['writers']);
                    for ($i = 0; $i < count($Director_name); $i++) {

                        $Director = Cast::where(DB::raw('lower(name)'), strtolower($Director_name[$i]))->first();
                        if (!empty($Director)) {

                            $Cast_Id[] = $Director['id'];
                        } else {

                            $url = 'https://imdb-api.com/en/API/SearchName/' . $imdb_api_key . '/' . $Director_name[$i];
                            $response = Http::get($url);
                            $Status = $response->getStatusCode();
                            $Data = (array) $response->json();

                            $insert = new Cast();
                            $insert->name = $Director_name[$i];
                            $insert->type = "Writer";
                            if (isset($Data['results']) && count($Data['results']) > 0 && $Data['results'] != null) {

                                if ($Data['results'][0]['image'] != "" && $Data['results'][0]['image'] != null) {

                                    $CI_Name = URLSaveInImage($Data['results'][0]['image'], $this->folder_cast);
                                    $insert->image = $CI_Name;
                                } else {
                                    $insert->image = "";
                                }
                                $insert->personal_info = $Data['results'][0]['description'];
                            } else {

                                $insert->image = "";
                                $insert->personal_info = "";
                            }
                            $insert->save();

                            $Cast_Id[] = $insert->id;
                            $Cast_Insert_Data[] = $insert;
                        }
                    }
                }
                if (isset($movies['stars']) && $movies['stars'] != "N/A" && $movies['stars'] != null) {

                    $Director_name = explode(", ", $movies['stars']);
                    for ($i = 0; $i < count($Director_name); $i++) {

                        $Director = Cast::where(DB::raw('lower(name)'), strtolower($Director_name[$i]))->first();
                        if (!empty($Director)) {

                            $Cast_Id[] = $Director['id'];
                        } else {

                            $url = 'https://imdb-api.com/en/API/SearchName/' . $imdb_api_key . '/' . $Director_name[$i];
                            $response = Http::get($url);
                            $Status = $response->getStatusCode();
                            $Data = (array) $response->json();

                            $insert = new Cast();
                            $insert->name = $Director_name[$i];
                            $insert->type = "Actor";
                            if (isset($Data['results']) && count($Data['results']) > 0 && $Data['results'] != null) {

                                if ($Data['results'][0]['image'] != "" && $Data['results'][0]['image'] != null) {

                                    $CI_Name = URLSaveInImage($Data['results'][0]['image'], $this->folder_cast);
                                    $insert->image = $CI_Name;
                                } else {
                                    $insert->image = "";
                                }
                                $insert->personal_info = $Data['results'][0]['description'];
                            } else {

                                $insert->image = "";
                                $insert->personal_info = "";
                            }
                            $insert->save();

                            $Cast_Id[] = $insert->id;
                            $Cast_Insert_Data[] = $insert;
                        }
                    }
                }

                // Poster
                if (isset($movies['image']) && $movies['image'] != null) {
                    $Poster_img = $movies['image'];
                } else {
                    $Poster_img = "";
                }

                // Title
                if (isset($movies['title']) && $movies['title'] != null) {
                    $title = $movies['title'];
                } else {
                    $title = "";
                }

                // Description
                if (isset($movies['plot'])) {
                    $Description = $movies['plot'];
                } else {
                    $Description = "";
                }

                // Year
                if (isset($movies['year']) && $movies['year'] != null) {
                    $Year = $movies['year'];
                } else {
                    $Year = "";
                }

                // imdbRating
                if (isset($movies['imDbRating']) && $movies['imDbRating'] != null) {
                    $imdbRating = $movies['imDbRating'];
                } else {
                    $imdbRating = "";
                }

                return response()->json(array('status' => 200, 'C_Id' => $C_Id, 'L_Id' => $L_Id, 'C_Insert_Data' => $C_Insert_Data, 'L_Insert_Data' => $L_Insert_Data, 'Poster_img' => $Poster_img, 'title' => $title, 'Description' => $Description, 'Cast_Id' => $Cast_Id, 'Cast_Insert_Data' => $Cast_Insert_Data, 'Year' => $Year, 'imdbRating' => $imdbRating));
            } else {
                return response()->json(array('status' => 400, 'success' => "Enter Imdb Key"));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
