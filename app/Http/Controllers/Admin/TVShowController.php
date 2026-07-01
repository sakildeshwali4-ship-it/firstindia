<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\App_Section;
use App\Models\Cast;
use App\Models\Category;
use App\Models\Channel;
use App\Models\Channel_Section;
use App\Models\Language;
use App\Models\Session;
use App\Models\TVShow;
use App\Models\TVShowVideo;
use App\Models\Type;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Validator;
use Exception;

// Video Type = 1-Video, 2-Show, 3-Language, 4-Category, 5-Upcoming
// Video Upload Type = server_video, external, youtube, vimeo
// Subtitle Type = server_video, external
// Trailer Type = server_video, external, youtube

class TVShowController extends Controller
{

    private $folder = "show";
    private $folder_video = "video";
    private $folder_cast = "cast";

    public function index()
    {
        try {

            $type = Type::where('type', 2)->latest()->get();

            return view('admin.tv_show.index', ['type' => $type]);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function data(Request $request)
    {
        try {
            if ($request == true) {

                $input_search = $request['input_search'];
                $input_type = $request['input_type'];

                if ($input_search != null && isset($input_search)) {

                    if ($input_type == 'all') {
                        $data = TVShow::where('name', 'LIKE', "%{$input_search}%")->where('video_type', '!=', 5)->with('channel')->with('type')->latest()->get();
                    } else {
                        $data = TVShow::where('name', 'LIKE', "%{$input_search}%")->where('type_id', $input_type)->where('video_type', '!=', 5)->with('channel')->with('type')->latest()->get();
                    }
                } else {

                    if ($input_type == 'all') {
                        $data = TVShow::where('video_type', '!=', 5)->with('channel')->with('type')->latest()->get();
                    } else {
                        $data = TVShow::where('type_id', $input_type)->where('video_type', '!=', 5)->with('channel')->with('type')->latest()->get();
                    }
                }

                imageNameToUrl($data, 'thumbnail', $this->folder);

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $btn = '<div class="d-flex justify-content-center gap-2">';
                        $btn .= '<a href="' . route("editTVShow", $row->id) . '" Title="Edit"><i class="fa-regular fa-pen-to-square"></i></a> ';
                        $btn .= '<a href="' . route("deleteTVShow", $row->id) . '" title="Delete" onclick="return confirm(\'Are you sure !!! You want to Delete this TV Show ?\')"><i class="fa-solid fa-trash-can"></i></a></div>';
                        return $btn;
                    })
                    ->addColumn('details', function ($row) {
                        $btn = '<a href="' . route("TVShowDetail", $row->id) . '" class="btn text-white p-1 font-weight-bold" style="background:#17a2b8;">More Details</a> ';
                        return $btn;
                    })
                    ->addColumn('season', function ($row) {
                        $btn = '<a href="' . route("TVShowvideo", $row->id) . '" class="btn text-white p-1 font-weight-bold" style="background:#006a4e;"> Episode List</a> ';
                        return $btn;
                    })
                    ->rawColumns(['action', 'details', 'season'])
                    ->make(true);
            } else {
                return view('admin.tv_show.index');
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function add()
    {
        try {

            $channel = Channel::get();
            $category = Category::get();
            $language = Language::get();
            $cast = Cast::get();
            $type = Type::where('type', '2')->get();

            return view('admin.tv_show.add', ['channel' => $channel, 'category' => $category, 'language' => $language, 'cast' => $cast, 'type' => $type]);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function save(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:2',
                'category_id' => 'required',
                'type_id' => 'required',
                'language_id' => 'required',
                'cast_id' => 'required',
                'description' => 'required',
                'is_premium' => 'required',
                'is_title' => 'required',
                'thumbnail' => 'image|mimes:jpeg,png,jpg|max:2048',
                'landscape' => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $category_id = implode(',', $request->category_id);
            $language_id = implode(',', $request->language_id);
            $cast_id = implode(',', $request->cast_id);

            $TVShow = new TVShow();
            $TVShow->name = $request->name;
            $TVShow->channel_id = isset($request->channel_id) ? $request->channel_id : 0;
            $TVShow->category_id = $category_id;
            $TVShow->language_id = $language_id;
            $TVShow->cast_id = $cast_id;
            $TVShow->video_type = 2;
            $TVShow->description = $request->description;
            $TVShow->is_premium = $request->is_premium;
            $TVShow->is_title = $request->is_title;
            $TVShow->type_id = $request->type_id;
            $TVShow->status = 1;
            $TVShow->view = 0;

            // Release Data
            $TVShow->release_date = "";
            if ($request->release_date) {
                $TVShow->release_date = $request->release_date;
            }

            // Trailer
            $TVShow->trailer_type = isset($request->trailer_type) ? $request->trailer_type : '';
            if ($request->trailer_type == "server_video") {
                $TVShow->trailer_url = isset($request->trailer) ? $request->trailer : '';
            } else {
                $TVShow->trailer_url = isset($request->trailer_url) ? $request->trailer_url : '';
            }

            $TVShow->imdb_rating = isset($request->imdb_rating) ? $request->imdb_rating : 0;
            $TVShow->director_id = "";
            $TVShow->starring_id = "";
            $TVShow->supporting_cast_id = "";
            $TVShow->networks = "";
            $TVShow->maturity_rating = "";
            $TVShow->studios = "";
            $TVShow->content_advisory = "";
            $TVShow->viewing_rights = "";

            $org_name = $request->file('thumbnail');
            $TVShow->thumbnail = "";
            if ($org_name != null && isset($org_name)) {

                $TVShow->thumbnail = saveImage($org_name, $this->folder);
            } elseif ($request->thumbnail_imdb) {

                $url = $request->thumbnail_imdb;
                $S_Name = URLSaveInImage($url, $this->folder);
                $TVShow->thumbnail = $S_Name;
            }

            $org_name1 = $request->file('landscape');
            $TVShow->landscape = "";
            if ($org_name1 != null && isset($org_name1)) {

                $TVShow->landscape = saveImage($org_name1, $this->folder);
            } elseif ($request->landscape_imdb) {

                $url = $request->landscape_imdb;
                $S_Name = URLSaveInImage($url, $this->folder);
                $TVShow->landscape = $S_Name;
            }

            if ($TVShow->save()) {

                // Send Notification
                $imageURL = Get_Image('show', $TVShow->thumbnail);
                $noti_array = array(
                    'id' => $TVShow->id,
                    'name' => $TVShow->name,
                    'image' => $imageURL,
                    'type_id' => $TVShow->type_id,
                    'video_type' => $TVShow->video_type,
                    'upcoming_type' => 0,
                    'description' => string_cut($TVShow->description, 90),
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
    public function edit($id)
    {
        try {
            $TVShow = TVshow::where('id', $id)->first();

            imageNameToUrl(array($TVShow), 'thumbnail', $this->folder);
            imageNameToUrl(array($TVShow), 'landscape', $this->folder);

            $channel = Channel::get();
            $category = Category::get();
            $language = Language::get();
            $cast = Cast::get();
            $type = Type::where('type', '2')->get();

            return view('admin.tv_show.edit', ['result' => $TVShow, 'type' => $type, 'channel' => $channel, 'category' => $category, 'language' => $language, 'cast' => $cast]);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:2',
                'category_id' => 'required',
                'language_id' => 'required',
                'cast_id' => 'required',
                'description' => 'required',
                'is_premium' => 'required',
                'type_id' => 'required',
                'is_title' => 'required',
                'thumbnail' => 'image|mimes:jpeg,png,jpg|max:2048',
                'landscape' => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $category_id = implode(',', $request->category_id);
            $language_id = implode(',', $request->language_id);
            $cast_id = implode(',', $request->cast_id);

            $TVShow = TVShow::where('id', $request->id)->first();
            if (isset($TVShow->id)) {
                $TVShow->name = $request->name;
                $TVShow->channel_id = isset($request->channel_id) ? $request->channel_id : 0;
                $TVShow->category_id = $category_id;
                $TVShow->language_id = $language_id;
                $TVShow->cast_id = $cast_id;
                $TVShow->description = $request->description;
                $TVShow->is_premium = $request->is_premium;
                $TVShow->is_title = $request->is_title;
                $TVShow->type_id = $request->type_id;
                $TVShow->status = 1;
                $TVShow->video_type = 2;

                // Release Data
                $TVShow->release_date = "";
                if ($request->release_date) {
                    $TVShow->release_date = $request->release_date;
                }

                // Trailer
                $TVShow->trailer_type = isset($request->trailer_type) ? $request->trailer_type : '';
                if ($request->trailer_type == "server_video") {

                    if ($request->trailer_type == $request->old_trailer_type) {

                        if ($request->trailer) {
                            $TVShow->trailer_url = $request->trailer;
                            deleteImageToFolder($this->folder_video, basename($request->old_trailer));
                        }
                    } else {
                        if ($request->trailer) {
                            $TVShow->trailer_url = $request->trailer;
                            deleteImageToFolder($this->folder_video, basename($request->old_trailer));
                        } else {
                            $TVShow->trailer_url = "";
                        }
                    }
                } else {

                    deleteImageToFolder($this->folder_video, basename($request->old_trailer));

                    $TVShow->trailer_url = "";
                    if ($request->trailer_url) {
                        $TVShow->trailer_url = $request->trailer_url;
                    }
                }

                $org_name = $request->file('thumbnail');
                $org_name1 = $request->file('landscape');

                if ($org_name != null && isset($org_name)) {

                    $TVShow->thumbnail = saveImage($org_name, $this->folder);
                    deleteImageToFolder($this->folder, basename($request->old_thumbnail));
                } elseif ($request->thumbnail_imdb) {

                    $url = $request->thumbnail_imdb;
                    $S_Name = URLSaveInImage($url, $this->folder);
                    $TVShow->thumbnail = $S_Name;
                    deleteImageToFolder($this->folder, basename($request->old_thumbnail));
                }

                if ($org_name1 != null && isset($org_name1)) {

                    $TVShow->landscape = saveImage($org_name1, $this->folder);
                    deleteImageToFolder($this->folder, basename($request->old_landscape));
                } elseif ($request->landscape_imdb) {

                    $url = $request->landscape_imdb;
                    $S_Name = URLSaveInImage($url, $this->folder);
                    $TVShow->landscape = $S_Name;
                    deleteImageToFolder($this->folder, basename($request->old_landscape));
                }

                if ($TVShow->save()) {
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
            $TVShow = TVShow::where('id', $id)->first();

            $App_Section = App_Section::whereRaw("find_in_set('" . $TVShow->id . "',app_section.video_id)")->where('video_type', 2)->first();
            $Channel_Section = Channel_Section::whereRaw("find_in_set('" . $TVShow->id . "',channel_section.tv_show_id)")->first();

            /*if ($App_Section) {
                return back()->with('error', "This TVShow is used on some other table so you can not remove it.");
            } elseif ($Channel_Section) {
                return back()->with('error', "This TVShow is used on some other table so you can not remove it.");
            } else {*/
                if ($TVShow->delete()) {

                    deleteImageToFolder($this->folder, $TVShow->thumbnail);
                    deleteImageToFolder($this->folder, $TVShow->landscape);

                    deleteImageToFolder($this->folder_video, $TVShow->trailer_url);

                    $TVShowVideo = TVShowVideo::where('show_id', $TVShow->id)->get();
                    foreach ($TVShowVideo as $key => $value) {

                        deleteImageToFolder($this->folder, $value->thumbnail);
                        deleteImageToFolder($this->folder, $value->landscape);
                        deleteImageToFolder($this->folder_video, $value->video_320);
                        deleteImageToFolder($this->folder_video, $value->video_480);
                        deleteImageToFolder($this->folder_video, $value->video_720);
                        deleteImageToFolder($this->folder_video, $value->video_1080);

                        deleteImageToFolder($this->folder_video, $value->subtitle_1);
                        deleteImageToFolder($this->folder_video, $value->subtitle_2);
                        deleteImageToFolder($this->folder_video, $value->subtitle_3);

                        $value->delete();
                    }
                    return redirect()->route('TVShow')->with('success', __('Label.Data Delete Successfully'));
                }
           // }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    // TVShow Details
    public function TVShowDetail($id)
    {
        try {
            $TVShow = TVshow::where('id', $id)->first();
            $x = explode(",", $TVShow->category_id);
            $y = explode(",", $TVShow->language_id);
            $z = explode(",", $TVShow->cast_id);

            $channel = Channel::select('name')->where('id', $TVShow->channel_id)->first();
            $category = Category::select('name')->whereIn('id', $y)->get();
            $language = Language::select('name')->whereIn('id', $x)->get();
            $cast = Cast::select('name', 'type')->whereIn('id', $z)->get();

            return view('admin.tv_show.detail_page', ['tvshow' => $TVShow, 'channel' => $channel, 'category' => $category, 'language' => $language, 'cast' => $cast]);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    // TVShow Video
    public function TVShowvideo(Request $request, $id)
    {
        try {

            $input_search = $request['input_search'];
            $input_session = $request['input_session'];

            if ($input_search != null && isset($input_search)) {

                if ($input_session != 0) {

                    $video_list = TvShowVideo::where('name', 'LIKE', "%{$input_search}%")->where('show_id', $id)
                        ->where('session_id', $input_session)->with('session')->orderBy('sortable', 'asc')->paginate(15);
                } else {

                    $video_list = TvShowVideo::where('name', 'LIKE', "%{$input_search}%")->where('show_id', $id)->with('session')->orderBy('sortable', 'asc')->paginate(15);
                }
            } else {

                if ($input_session != 0) {
                    $video_list = TvShowVideo::where('show_id', $id)->where('session_id', $input_session)->with('session')->orderBy('sortable', 'asc')->paginate(15);
                } else {
                    $video_list = TvShowVideo::where('show_id', $id)->with('session')->orderBy('sortable', 'asc')->paginate(15);
                }
            }

            $session = Session::get();

            imageNameToUrl($video_list, 'thumbnail', $this->folder);
            imageNameToUrl($video_list, 'landscape', $this->folder);
            videoNameToUrl($video_list, 'video_320', $this->folder_video);

            return view('admin.tv_show.video_list', ['tvshowId' => $id, 'result' => $video_list, 'session' => $session]);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function TVShowvideoadd($id)
    {
        try {
            $session = Session::select('*')->get();
            return view('admin.tv_show.add_video', ['tvshowId' => $id, 'session' => $session]);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function TVShowvideosave(Request $request)
    {
        try {
            if ($request->video_upload_type == "server_video") {
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'show_id' => 'required',
                    'session_id' => 'required',
                    'video_upload_type' => 'required',
                    'thumbnail' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                    'landscape' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                    'video_duration' => 'required|after_or_equal:00:00:01',
                    'description' => 'required',
                    'is_premium' => 'required',
                    'is_title' => 'required',
                ]);
            } else {
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'show_id' => 'required',
                    'session_id' => 'required',
                    'video_upload_type' => 'required',
                    'thumbnail' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                    'landscape' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                    'video_duration' => 'required|after_or_equal:00:00:01',
                    'description' => 'required',
                    'is_premium' => 'required',
                    'is_title' => 'required',
                ]);
            }
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $TvShowVideo = new TVShowVideo();
            $TvShowVideo->show_id = $request->show_id;
            $TvShowVideo->session_id = $request->session_id;
            $TvShowVideo->video_type = 2;
            $TvShowVideo->video_upload_type = $request->video_upload_type;
            $TvShowVideo->name = $request->name;

            // Image
            $org_name = $request->file('thumbnail');
            $org_name1 = $request->file('landscape');
            if ($org_name != null) {
                $TvShowVideo->thumbnail = saveImage($org_name, $this->folder);
            }
            if ($org_name1 != null) {
                $TvShowVideo->landscape = saveImage($org_name1, $this->folder);
            }

            if ($request->video_upload_type == "server_video" || $request->video_upload_type == "external") {
                $TvShowVideo->download = $request->download;
            } else {
                $TvShowVideo->download = 0;
            }

            // Videos
            if ($request->video_upload_type == "server_video") {

                $TvShowVideo->video_320 = isset($request->upload_video_320) ? $request->upload_video_320 : '';
                $TvShowVideo->video_480 = isset($request->upload_video_480) ? $request->upload_video_480 : '';
                $TvShowVideo->video_720 = isset($request->upload_video_720) ? $request->upload_video_720 : '';
                $TvShowVideo->video_1080 = isset($request->upload_video_1080) ? $request->upload_video_1080 : '';

                $array = explode('.', $request->upload_video_320);
                $TvShowVideo->video_extension = end($array);
            } else {

                $TvShowVideo->video_320 = isset($request->video_url_320) ? $request->video_url_320 : '';
                $TvShowVideo->video_480 = isset($request->video_url_480) ? $request->video_url_480 : '';
                $TvShowVideo->video_720 = isset($request->video_url_720) ? $request->video_url_720 : '';
                $TvShowVideo->video_1080 = isset($request->video_url_1080) ? $request->video_url_1080 : '';

                $array = explode('.', $request->video_url_320);
                $array1 = explode('?', end($array));
                if (isset($array1) && $array1 != null) {
                    $TvShowVideo->video_extension = isset($array1) ? reset($array1) : "";
                } else {
                    $TvShowVideo->video_extension = "";
                }
            }

            // SubTitle
            $TvShowVideo->subtitle_type = isset($request->subtitle_type) ? $request->subtitle_type : '';
            $TvShowVideo->subtitle_lang_1 = isset($request->subtitle_lang_1) ? $request->subtitle_lang_1 : '';
            $TvShowVideo->subtitle_lang_2 = isset($request->subtitle_lang_2) ? $request->subtitle_lang_2 : '';
            $TvShowVideo->subtitle_lang_3 = isset($request->subtitle_lang_3) ? $request->subtitle_lang_3 : '';
            if ($request->subtitle_type == "server_video") {
                $TvShowVideo->subtitle_1 = isset($request->subtitle1) ? $request->subtitle1 : '';
                $TvShowVideo->subtitle_2 = isset($request->subtitle2) ? $request->subtitle2 : '';
                $TvShowVideo->subtitle_3 = isset($request->subtitle3) ? $request->subtitle3 : '';
            } else {
                $TvShowVideo->subtitle_1 = isset($request->subtitle_url_1) ? $request->subtitle_url_1 : '';
                $TvShowVideo->subtitle_2 = isset($request->subtitle_url_2) ? $request->subtitle_url_2 : '';
                $TvShowVideo->subtitle_3 = isset($request->subtitle_url_3) ? $request->subtitle_url_3 : '';
            }

            $TvShowVideo->video_duration = TimeToMilliseconds($request->video_duration);
            $TvShowVideo->description = $request->description;
            $TvShowVideo->is_premium = $request->is_premium;
            $TvShowVideo->is_title = $request->is_title;
            $TvShowVideo->view = 0;
            $TvShowVideo->status = 1;

            if ($TvShowVideo->save()) {

                $showData = TVShow::where('id', $TvShowVideo->show_id)->first();
                if (isset($showData)) {

                    // Send Notification
                    $imageURL = Get_Image('show', $TvShowVideo->thumbnail);
                    $noti_array = array(
                        'id' => $showData->id,
                        'name' => $showData->name,
                        'image' => $imageURL,
                        'type_id' => $showData->type_id,
                        'video_type' => $showData->video_type,
                        'upcoming_type' => 0,
                        'description' => string_cut($TvShowVideo->description, 90),
                    );
                    sendNotification($noti_array);
                }
                return response()->json(array('status' => 200, 'success' => __('Label.Data Add Successfully')));
            } else {
                return response()->json(array('status' => 400, 'errors' => __('Label.Data Not Add')));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function TVShowvideoedit($show_id, $id)
    {
        try {
            $result = TVShowVideo::where('id', $id)->first();

            imageNameToUrl(array($result), 'thumbnail', $this->folder);
            imageNameToUrl(array($result), 'landscape', $this->folder);

            $session = Session::get();
            if ($result) {
                return view('admin.tv_show.edit_video', ['tvshowId' => $show_id, 'session' => $session, 'result' => $result]);
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function TVShowvideoupdate(Request $request)
    {
        try {
            if ($request->video_upload_type == "server_video") {
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'show_id' => 'required',
                    'video_id' => 'required',
                    'session_id' => 'required',
                    'video_upload_type' => 'required',
                    'thumbnail' => 'image|mimes:jpeg,png,jpg|max:2048',
                    'landscape' => 'image|mimes:jpeg,png,jpg|max:2048',
                    'video_duration' => 'required|after_or_equal:00:00:01',
                    'description' => 'required',
                    'is_premium' => 'required',
                    'is_title' => 'required',
                ]);
            } else {
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'show_id' => 'required',
                    'video_id' => 'required',
                    'session_id' => 'required',
                    'video_upload_type' => 'required',
                    'thumbnail' => 'image|mimes:jpeg,png,jpg|max:2048',
                    'landscape' => 'image|mimes:jpeg,png,jpg|max:2048',
                    'video_duration' => 'required|after_or_equal:00:00:01',
                    'description' => 'required',
                    'is_premium' => 'required',
                    'is_title' => 'required',
                    'video_url_320' => 'required',
                ]);
            }
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $TVShowVideo = TVShowVideo::where('id', $request->video_id)->first();
            if (isset($TVShowVideo->id)) {

                $TVShowVideo->name = $request->name;
                $TVShowVideo->show_id = $request->show_id;
                $TVShowVideo->session_id = $request->session_id;
                $TVShowVideo->is_premium = $request->is_premium;
                $TVShowVideo->is_title = $request->is_title;
                $TVShowVideo->video_duration = TimeToMilliseconds($request->video_duration);
                $TVShowVideo->description = $request->description;
                $TVShowVideo->video_type = 2;
                $TVShowVideo->video_upload_type = $request->video_upload_type;

                if ($request->video_upload_type == "server_video" || $request->video_upload_type == "external") {
                    $TVShowVideo->download = $request->download;
                } else {
                    $TVShowVideo->download = 0;
                }

                // Videos
                if ($request->video_upload_type == "server_video") {

                    if ($request->video_upload_type == $request->old_video_upload_type) {

                        if ($request->upload_video_320) {

                            $array = explode('.', $request->upload_video_320);
                            $TVShowVideo->video_extension = end($array);

                            $TVShowVideo->video_320 = $request->upload_video_320;
                            deleteImageToFolder($this->folder_video, basename($request->old_video_320));
                        }
                        if ($request->upload_video_480) {

                            $TVShowVideo->video_480 = $request->upload_video_480;
                            deleteImageToFolder($this->folder_video, basename($request->old_video_480));
                        }
                        if ($request->upload_video_720) {

                            $TVShowVideo->video_720 = $request->upload_video_720;
                            deleteImageToFolder($this->folder_video, basename($request->old_video_720));
                        }
                        if ($request->upload_video_1080) {

                            $TVShowVideo->video_1080 = $request->upload_video_1080;
                            deleteImageToFolder($this->folder_video, basename($request->old_video_1080));
                        }
                    } else {
                        if ($request->upload_video_320) {

                            $array = explode('.', $request->upload_video_320);
                            $TVShowVideo->video_extension = end($array);

                            $TVShowVideo->video_320 = $request->upload_video_320;
                            deleteImageToFolder($this->folder_video, basename($request->old_video_320));
                        } else {
                            $TVShowVideo->video_320 = "";
                        }
                        if ($request->upload_video_480) {

                            $TVShowVideo->video_480 = $request->upload_video_480;
                            deleteImageToFolder($this->folder_video, basename($request->old_video_480));
                        } else {
                            $TVShowVideo->video_480 = "";
                        }
                        if ($request->upload_video_720) {

                            $TVShowVideo->video_720 = $request->upload_video_720;
                            deleteImageToFolder($this->folder_video, basename($request->old_video_720));
                        } else {
                            $TVShowVideo->video_720 = "";
                        }
                        if ($request->upload_video_1080) {

                            $TVShowVideo->video_1080 = $request->upload_video_1080;
                            deleteImageToFolder($this->folder_video, basename($request->old_video_1080));
                        } else {
                            $TVShowVideo->video_1080 = "";
                        }
                    }
                } else {

                    deleteImageToFolder($this->folder_video, basename($request->old_video_320));
                    deleteImageToFolder($this->folder_video, basename($request->old_video_480));
                    deleteImageToFolder($this->folder_video, basename($request->old_video_720));
                    deleteImageToFolder($this->folder_video, basename($request->old_video_1080));

                    $TVShowVideo->video_480 = "";
                    $TVShowVideo->video_720 = "";
                    $TVShowVideo->video_1080 = "";

                    if ($request->video_url_320) {

                        $array = explode('.', $request->video_url_320);
                        $array1 = explode('?', end($array));
                        if (isset($array1) && $array1 != null) {
                            $TVShowVideo->video_extension = isset($array1) ? reset($array1) : "";
                        } else {
                            $TVShowVideo->video_extension = "";
                        }

                        $TVShowVideo->video_320 = $request->video_url_320;
                    }
                    if ($request->video_url_480) {
                        $TVShowVideo->video_480 = $request->video_url_480;
                    }
                    if ($request->video_url_720) {
                        $TVShowVideo->video_720 = $request->video_url_720;
                    }
                    if ($request->video_url_1080) {
                        $TVShowVideo->video_1080 = $request->video_url_1080;
                    }
                }

                // Subtitle
                $TVShowVideo->subtitle_type = isset($request->subtitle_type) ? $request->subtitle_type : '';
                if ($request->subtitle_type == "server_video") {

                    if ($request->subtitle_type == $request->old_subtitle_type) {
                        if ($request->subtitle1) {
                            $TVShowVideo->subtitle_1 = $request->subtitle1;
                            deleteImageToFolder($this->folder_video, basename($request->old_subtitle_1));
                        }
                        if ($request->subtitle2) {
                            $TVShowVideo->subtitle_2 = $request->subtitle2;
                            deleteImageToFolder($this->folder_video, basename($request->old_subtitle_2));
                        }
                        if ($request->subtitle3) {
                            $TVShowVideo->subtitle_3 = $request->subtitle3;
                            deleteImageToFolder($this->folder_video, basename($request->old_subtitle_3));
                        }
                    } else {
                        if ($request->subtitle1) {
                            $TVShowVideo->subtitle_1 = $request->subtitle1;
                            deleteImageToFolder($this->folder, basename($request->old_subtitle_1));
                        } else {
                            $TVShowVideo->subtitle_1 = "";
                        }
                        if ($request->subtitle2) {
                            $TVShowVideo->subtitle_2 = $request->subtitle2;
                            deleteImageToFolder($this->folder, basename($request->old_subtitle_2));
                        } else {
                            $TVShowVideo->subtitle_2 = "";
                        }
                        if ($request->subtitle3) {
                            $TVShowVideo->subtitle_3 = $request->subtitle3;
                            deleteImageToFolder($this->folder, basename($request->old_subtitle_3));
                        } else {
                            $TVShowVideo->subtitle_3 = "";
                        }
                    }
                } else {

                    deleteImageToFolder($this->folder_video, basename($request->old_subtitle_1));
                    deleteImageToFolder($this->folder_video, basename($request->old_subtitle_2));
                    deleteImageToFolder($this->folder_video, basename($request->old_subtitle_3));

                    $TVShowVideo->subtitle_1 = "";
                    $TVShowVideo->subtitle_2 = "";
                    $TVShowVideo->subtitle_3 = "";

                    if ($request->subtitle_1) {
                        $TVShowVideo->subtitle_1 = $request->subtitle_url_1;
                    }
                    if ($request->subtitle_2) {
                        $TVShowVideo->subtitle_2 = $request->subtitle_url_2;
                    }
                    if ($request->subtitle_3) {
                        $TVShowVideo->subtitle_3 = $request->subtitle_url_3;
                    }
                }

                $TVShowVideo->subtitle_lang_1 = isset($request->subtitle_lang_1) ? $request->subtitle_lang_1 : '';
                $TVShowVideo->subtitle_lang_2 = isset($request->subtitle_lang_2) ? $request->subtitle_lang_2 : '';
                $TVShowVideo->subtitle_lang_3 = isset($request->subtitle_lang_3) ? $request->subtitle_lang_3 : '';

                // Image
                $org_name = $request->file('thumbnail');
                $org_name1 = $request->file('landscape');
                if ($org_name != null) {
                    $TVShowVideo->thumbnail = saveImage($org_name, $this->folder);
                    deleteImageToFolder($this->folder, basename($request->old_thumbnail));
                }
                if ($org_name1 != null) {
                    $TVShowVideo->landscape = saveImage($org_name1, $this->folder);
                    deleteImageToFolder($this->folder, basename($request->old_landscape));
                }

                if ($TVShowVideo->save()) {
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
    public function TVShowvideodelete($id)
    {
        try {
            $TVShowVideo = TVShowVideo::where('id', $id)->first();

            if ($TVShowVideo->delete()) {

                deleteImageToFolder($this->folder, $TVShowVideo->thumbnail);
                deleteImageToFolder($this->folder, $TVShowVideo->landscape);

                deleteImageToFolder($this->folder_video, $TVShowVideo->video_320);
                deleteImageToFolder($this->folder_video, $TVShowVideo->video_480);
                deleteImageToFolder($this->folder_video, $TVShowVideo->video_720);
                deleteImageToFolder($this->folder_video, $TVShowVideo->video_1080);

                deleteImageToFolder($this->folder_video, $TVShowVideo->subtitle_1);
                deleteImageToFolder($this->folder_video, $TVShowVideo->subtitle_2);
                deleteImageToFolder($this->folder_video, $TVShowVideo->subtitle_3);

                return back()->with('success', __('Label.Data Delete Successfully'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    // Sortable List
    public function TVShowvideosortable(Request $request)
    {
        try {

            $ids = $request['ids'];

            if (isset($ids) && $ids != null && $ids != "") {

                $id_array = explode(',', $ids);
                for ($i = 0; $i < count($id_array); $i++) {
                    TVShowVideo::where('id', $id_array[$i])->update(['sortable' => $i + 1]);
                }
            }

            return response()->json(array('status' => 200, 'success' => __('Label.Data Edit Successfully')));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
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
