<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\App_Section;
use App\Models\Avatar;
use App\Models\Banner;
use App\Models\Bookmark;
use App\Models\Cast;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Download;
use App\Models\General_Setting;
use App\Models\Language;
use App\Models\Package;
use App\Models\Social_Link;
use App\Models\Package_Detail;
use App\Models\Payment_Option;
use App\Models\RentTransction;
use App\Models\RentVideo;
use App\Models\Users;
use App\Models\Session;
use App\Models\Transction;
use App\Models\Page;
use App\Models\TVShow;
use App\Models\TVShowVideo;
use App\Models\Type;
use App\Models\Video;
use App\Models\WebSeries;
use App\Models\Video_Watch;
use App\Models\EpisodeWatchHistory;
use App\Models\WebseriesReaction;
use App\Models\ENewspaper;
use App\Models\ENewsRead;
use App\Models\ENewsVisit;
use App\Models\OptionData;
use App\Models\TvView;
use Illuminate\Http\Request;
use App\Models\LiveTv;
use Validator;
use Exception;
use DB;
use Carbon\Carbon;

// Video Type = 1-Video, 2-Show, 3-Language, 4-Category, 5-Upcoming
// Video Upload Type = server_video, external, youtube, vimeo
// Subtitle Type = server_video, external
// Trailer Type = server_video, external, youtube

// Download
// Show  :=> ("video_id" = Session's ID)  AND  ("other_id" = Show's ID)
// Video :=> ("other_id" = "0")

// Vidoe View
// Video :=> video_type = 1, video_id = video id, other_id = 0
// Show  :=> video_type = 2, video_id = episode id, other_id = Show id

class HomeController extends Controller
{

    private $folder_language = "language";
    private $folder_cast = "cast";
    private $folder_category = "category";
    private $folder_video = "video";
    private $folder_show = "show";
    private $folder_app = "app";
    private $folder_avatar = "avatar";
    private $folder_type = "type_image";

    // ================ General (get) API's ================
    public function general_setting()
    {
        try {

            $data = General_Setting::get();
            if (count($data) > 0) {

                foreach ($data as $key => $value) {

                    if ($value['key'] == "app_logo") {
                        if (!empty($value['value'])) {
                            $value['value'] = Get_Image($this->folder_app, $value['value']);
                        }
                    }

                    if ($value['key'] == "currency") {
                        if (!empty($value['value'])) {
                            $value['value'] = strtoupper($value['value']);
                        }
                    }
                }

                return APIResponse(200, __('api_msg.get_record_successfully'), $data);
            } else {
                return APIResponse(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function get_pages()
    {
        try {
            $data = Page::get();

            $return['status'] = 200;
            $return['message'] = __('api_msg.get_record_successfully');
            $return['result'] = [];

            for ($i = 0; $i < count($data); $i++) {

                $return['result'][$i]['page_name'] = $data[$i]['title'];
                $return['result'][$i]['url'] = env('APP_URL') . 'pages/' . $data[$i]['id'];
                $return['result'][$i]['icon'] = Get_Image($this->folder_app, $data[$i]['icon']);
                $return['result'][$i]['page_subtitle'] = $data[$i]['page_subtitle'];
            }
            return $return;
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function get_social_link()
    {
        try {
            $Data = Social_Link::latest()->get();
            if (sizeof($Data) > 0) {

                imageNameToUrl($Data, 'image', $this->folder_app);

                return APIResponse(200, __('api_msg.get_record_successfully'), $Data);
            } else {
                return APIResponse(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function get_payment_option()
    {
        try {

            $data['status'] = 200;
            $data['message'] = __('api_msg.get_record_successfully');

            $Option_data = Payment_Option::where(['is_live'=>'1','visibility'=>'1'])->get();
            foreach ($Option_data as $key => $value) {

                $data['result'][$value['name']] = $value;
            }

            return $data;
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function get_avatar()
    {
        try {
            $Data = Avatar::latest()->get();
            if (sizeof($Data) > 0) {

                imageNameToUrl($Data, 'image', $this->folder_avatar);

                return APIResponse(200, __('api_msg.get_record_successfully'), $Data);
            } else {
                return APIResponse(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function get_type()
    {
        try {

            $data = Type::where('id', '!=', 32)->orderBy('position')->get();

            if ($data->count() > 0) {

                // $staticType = [
                //     "id" => 11,
                //     "name" => "WebSeries",
                //     "type" => 1,
                //     "type_image" => url('assets/imgs/no_img.png'),
                //     "position" => 0,
                //     "created_at" => now(),
                //     "updated_at" => now(),
                //     "is_webSeries" => true,
                // ];

                imageNameToUrl($data, 'type_image', $this->folder_type);

                foreach ($data as $row) {
                    $row->type = (int) $row->type;
                     $row->is_webSeries = $row->is_webseries;
                }

                // $data->prepend($staticType);

                return APIResponse(200, __('api_msg.get_record_successfully'), $data);

            } else {
                return APIResponse(400, __('api_msg.data_not_found'));
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'errors' => $e->getMessage()
            ]);
        }
    }
    public function get_language()
    {
        try {
            $Data = Language::latest()->get();
            if (sizeof($Data) > 0) {

                imageNameToUrl($Data, 'image', $this->folder_language);

                return APIResponse(200, __('api_msg.get_record_successfully'), $Data);
            } else {
                return APIResponse(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function get_category()
    {
        try {
           $Data = Category::where('id', '!=', 24)->latest()->get();
            if (sizeof($Data) > 0) {

                imageNameToUrl($Data, 'image', $this->folder_category);

                return APIResponse(200, __('api_msg.get_record_successfully'), $Data);
            } else {
                return APIResponse(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function apply_coupon(Request $request)
    {
        try {

            // apply_coupon_type == 1 (Package)
            // apply_coupon_type == 2 (Rent Video)
            if ($request->apply_coupon_type == 1) {
                $validation = Validator::make(
                    $request->all(),
                    [
                        'user_id' => 'required|numeric',
                        'package_id' => 'required|numeric',
                        'unique_id' => 'required',
                    ],
                    [
                        'user_id.required' => __('api_msg.please_enter_required_fields'),
                        'package_id.required' => __('api_msg.please_enter_required_fields'),
                        'unique_id.required' => __('api_msg.please_enter_required_fields'),
                    ]
                );
                if ($validation->fails()) {

                    $errors = $validation->errors()->first('user_id');
                    $errors1 = $validation->errors()->first('package_id');
                    $errors2 = $validation->errors()->first('unique_id');
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
            } elseif ($request->apply_coupon_type == 2) {
                $validation = Validator::make(
                    $request->all(),
                    [
                        'user_id' => 'required|numeric',
                        'video_id' => 'required|numeric',
                        'price' => 'required|numeric',
                        'type_id' => 'required|numeric',
                        'video_type' => 'required|numeric',
                        'unique_id' => 'required',
                    ],
                    [
                        'user_id.required' => __('api_msg.please_enter_required_fields'),
                        'video_id.required' => __('api_msg.please_enter_required_fields'),
                        'price.required' => __('api_msg.please_enter_required_fields'),
                        'type_id.required' => __('api_msg.please_enter_required_fields'),
                        'video_type.required' => __('api_msg.please_enter_required_fields'),
                        'unique_id.required' => __('api_msg.please_enter_required_fields'),
                    ]
                );

                if ($validation->fails()) {

                    $errors = $validation->errors()->first('user_id');
                    $errors2 = $validation->errors()->first('video_id');
                    $errors1 = $validation->errors()->first('price');
                    $errors3 = $validation->errors()->first('type_id');
                    $errors4 = $validation->errors()->first('video_type');
                    $errors5 = $validation->errors()->first('unique_id');
                    $data['status'] = 400;
                    if ($errors) {
                        $data['message'] = $errors;
                    } elseif ($errors1) {
                        $data['message'] = $errors1;
                    } elseif ($errors2) {
                        $data['message'] = $errors2;
                    } elseif ($errors3) {
                        $data['message'] = $errors3;
                    } elseif ($errors4) {
                        $data['message'] = $errors4;
                    } elseif ($errors5) {
                        $data['message'] = $errors5;
                    }
                    return $data;
                }
            } else {
                $validation = Validator::make(
                    $request->all(),
                    [
                        'apply_coupon_type' => 'required|numeric',
                    ],
                    [
                        'apply_coupon_type.required' => __('api_msg.please_enter_required_fields'),
                    ]
                );
                if ($validation->fails()) {

                    $errors = $validation->errors()->first('apply_coupon_type');
                    $data['status'] = 400;
                    if ($errors) {
                        $data['message'] = $errors;
                    }
                    return $data;
                }
            }

            $user_id = $request->user_id;
            $unique_id = $request->unique_id;
            $date = date("Y-m-d");
            $array = array();

            $coupon_check = Coupon::where('unique_id', $unique_id)->first();
            if (isset($coupon_check)) {

                if ($coupon_check['start_date'] > $date) {
                    return APIResponse(400, __('api_msg.coupon_not_start'));
                }
                if ($coupon_check['end_date'] < $date) {
                    return APIResponse(400, __('api_msg.coupon_expriy'));
                }
                if ($coupon_check['is_use'] == 1) {
                    $use_check = Transction::where('unique_id', $coupon_check['unique_id'])->first();
                    $rent_use_check = RentTransction::where('unique_id', $coupon_check['unique_id'])->first();
                    if (isset($use_check) || isset($rent_use_check)) {
                        return APIResponse(400, __('api_msg.coupon_already_use'));
                    }
                }

                if ($request->apply_coupon_type == 1) {

                    $Pdata = Package::where('id', $request->package_id)->where('status', '1')->first();
                    if (empty($Pdata)) {
                        return APIResponse(400, __('api_msg.please_enter_right_package_id'));
                    }

                    $discount_amount = 0;
                    if ($coupon_check['amount_type'] == 1) {

                        $discount_amount = $Pdata['price'] - $coupon_check['price'];
                    } elseif ($coupon_check['amount_type'] == 2) {

                        $minus_amount = ($coupon_check['price'] / 100) * $Pdata['price'];
                        $discount_amount = $Pdata['price'] - $minus_amount;

                        if ($discount_amount > $Pdata['price']) {
                            $discount_amount = 0;
                        }
                    }
                    $discount_amount = max($discount_amount, 0);

                    $array = array(
                        'id' => $coupon_check['id'],
                        'unique_id' => $unique_id,
                        'total_amount' => $Pdata['price'],
                        'discount_amount' => $discount_amount,
                    );
                } elseif ($request->apply_coupon_type == 2) {

                    $Rent_Video = RentVideo::where('video_id', $request->video_id)->where('price', $request->price)->where('type_id', $request->type_id)->where('video_type', $request->video_type)->where('status', '1')->first();
                    if (empty($Rent_Video)) {
                        return APIResponse(400, __('api_msg.please_enter_right_rent_video'));
                    }

                    $discount_amount = 0;
                    if ($coupon_check['amount_type'] == 1) {

                        $discount_amount = $Rent_Video['price'] - $coupon_check['price'];
                    } elseif ($coupon_check['amount_type'] == 2) {

                        $minus_amount = ($coupon_check['price'] / 100) * $Rent_Video['price'];
                        $discount_amount = $Rent_Video['price'] - $minus_amount;

                        if ($discount_amount > $Rent_Video['price']) {
                            $discount_amount = 0;
                        }
                    }
                    $discount_amount = max($discount_amount, 0);

                    $array = array(
                        'id' => $coupon_check['id'],
                        'unique_id' => $unique_id,
                        'total_amount' => $Rent_Video['price'],
                        'discount_amount' => $discount_amount,
                    );
                }
            } else {
                return APIResponse(400, __('api_msg.coupon_id_worng'));
            }

            return APIResponse(200, __('api_msg.add_successfully'), $array);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    // ================ Home API's ================
    public function get_banner(Request $request)
    {
       
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'type_id' => 'required|numeric',
                    'is_home_page' => 'required|numeric',
                ],
                [
                    'type_id.required' => __('api_msg.please_enter_required_fields'),
                    'is_home_page.required' => __('api_msg.please_enter_required_fields'),
                ]
            );
            if ($validation->fails()) {

                $errors = $validation->errors()->first('type_id');
                $errors1 = $validation->errors()->first('is_home_page');
                $data['status'] = 400;
                if ($errors) {
                    $data['message'] = $errors;
                } elseif ($errors1) {
                    $data['message'] = $errors1;
                }
                return $data;
            }

            $type_id = $request->type_id;
            $is_home_page = $request->is_home_page;

            if ($is_home_page == "1") {
 
                $Data = Banner::where('is_home_screen', '1')->with([
                    'webseries.seasons'
                ])->with('video')->with('tvshow')->latest()->get();
                //dd($Data);
                if (count($Data) > 0) {

                    $Final_Data = [];
                    for ($i = 0; $i < count($Data); $i++) {
                        if ($Data[$i]['video_type'] == 0) { 
                            if ($Data[$i]['webseries'] != null) {

                                $Final_Data[$i]['id'] = 1;
                                $Final_Data[$i]['name'] = $Data[$i]['webseries']['title'];
                                $Final_Data[$i]['category_id'] = $Data[$i]['webseries']['category_id'];
                                $Final_Data[$i]['description'] = $Data[$i]['webseries']['description'];
                                $Final_Data[$i]['type_id'] =  (int) $Data[$i]['type_id'];
                                $Final_Data[$i]['video_type'] = (int) $Data[$i]['webseries']['video_type'];
                                // Thumbnail && Landscape
                                $Final_Data[$i]['thumbnail'] = asset("images/web_series/" . $Data[$i]['webseries']['thumbnail']);
                                $Final_Data[$i]['landscape'] = asset("images/web_series/" . $Data[$i]['webseries']['thumbnail']);
                                

                                // Video (320, 480, 720, 1080)
                                $Final_Data[$i]['video_upload_type'] = $Data[$i]['webseries']['video_upload_type'];
                                if (isset($Data[$i]['webseries']['video_320']) && !empty($Data[$i]['webseries']['video_320'])) {
                                    if ($Data[$i]['webseries']['video_upload_type'] == "server_video") {
                                        $Final_Data[$i]['video_320'] = Get_Video($this->folder_video, $Data[$i]['video']['video_320']);
                                    }
                                }
                                if (isset($Data[$i]['webseries']['video_480']) && !empty($Data[$i]['webseries']['video_480'])) {
                                    if ($Data[$i]['webseries']['video_upload_type'] == "server_video") {
                                        $Final_Data[$i]['video_480'] = Get_Video($this->folder_video, $Data[$i]['webseries']['video_480']);
                                    }
                                }
                                if (isset($Data[$i]['webseries']['video_720']) && !empty($Data[$i]['webseries']['video_720'])) {
                                    if ($Data[$i]['webseries']['video_upload_type'] == "server_video") {
                                        $Final_Data[$i]['video_720'] = Get_Video($this->folder_video, $Data[$i]['webseries']['video_720']);
                                    }
                                }
                                if (isset($Data[$i]['video']['video_1080']) && !empty($Data[$i]['webseries']['video_1080'])) {
                                    if ($Data[$i]['video']['video_upload_type'] == "server_video") {
                                        $Final_Data[$i]['video_1080'] = Get_Video($this->folder_video, $Data[$i]['webseries']['video_1080']);
                                    }
                                }

                                // Trailer
                                $Final_Data[$i]['trailer_type'] = $Data[$i]['webseries']['trailer_type'];
                                if (isset($Data[$i]['webseries']['trailer_url']) && !empty($Data[$i]['video']['trailer_url'])) {
                                    if ($Data[$i]['webseries']['trailer_type'] == "server_video") {
                                        $Data[$i]['webseries']['trailer_url'] = Get_Video($this->folder_video, $Data[$i]['webseries']['trailer_url']);
                                    }
                                }

                                // SubTitle_1_1_3
                                $Final_Data[$i]['subtitle_type'] = $Data[$i]['webseries']['subtitle_type'];
                                if (isset($Data[$i]['webseries']['subtitle_1']) && !empty($Data[$i]['webseries']['subtitle_1'])) {
                                    if ($Data[$i]['webseries']['subtitle_type'] == "server_video") {
                                        $Data[$i]['webseries']['subtitle_1'] = Get_Video($this->folder_video, $Data[$i]['webseries']['subtitle_1']);
                                    }
                                }
                                if (isset($Data[$i]['webseries']['subtitle_2']) && !empty($Data[$i]['webseries']['subtitle_2'])) {
                                    if ($Data[$i]['webseries']['subtitle_type'] == "server_video") {
                                        $Data[$i]['webseries']['subtitle_2'] = Get_Video($this->folder_video, $Data[$i]['webseries']['subtitle_2']);
                                    }
                                }
                                if (isset($Data[$i]['webseries']['subtitle_3']) && !empty($Data[$i]['webseries']['subtitle_3'])) {
                                    if ($Data[$i]['webseries']['subtitle_type'] == "server_video") {
                                        $Data[$i]['webseries']['subtitle_3'] = Get_Video($this->folder_video, $Data[$i]['webseries']['subtitle_3']);
                                    }
                                }

                                $Final_Data[$i]['stop_time'] = 0;
                                $Final_Data[$i]['is_downloaded'] = 0;
                                $Final_Data[$i]['is_bookmark'] = 0;
                                $Final_Data[$i]['rent_buy'] = 0;
                                $Final_Data[$i]['is_rent'] = IsRentVideo($Data[$i]['webseries']['id'], $Data[$i]['webseries']['video_type']);
                                $Final_Data[$i]['rent_price'] = GetPriceByRentVideo($Data[$i]['webseries']['id'], $Data[$i]['webseries']['video_type']);
                                $Final_Data[$i]['is_buy'] = 0;
                                $Final_Data[$i]['is_webSeries'] = 1;
                                $Final_Data[$i]['category_name'] = GetCategoryNameByIds($Data[$i]['webseries']['category_id']);
                                $Final_Data[$i]['session_id'] = "0";
                                $Final_Data[$i]['upcoming_type'] = 0;
								$seasonTrailer = null;

                                $webseries = $Data[$i]['webseries'];

                                if ($webseries && $webseries->seasons && $webseries->seasons->count() > 0) {
 
                                    $lastSeason = $webseries->seasons
                                        ->sortByDesc('id')
                                        ->first();

                                    if ($lastSeason && !empty($lastSeason->video)) {
                                       $seasonTrailer = $lastSeason->video;
                                    }
                                }

                                // $Final_Data[$i]['video_full_data'] = $webseries->toArray();
                                $Final_Data[$i]['video_full_data'] = [
                                    "id" => $webseries->id,
                                    "channel_id" => "",
                                    "category_id" => "",
                                    "language_id" => "",
                                    "cast_id" => "",
                                    "type_id" => (int) $Data[$i]['type_id'],
                                    "video_type" => 0,
                                    "name" => $webseries->title,
                                    "thumbnail" => asset("images/web_series/" . $webseries->thumbnail),
                                    "landscape" => asset("images/web_series/" . $webseries->thumbnail),
                                    "description" => $webseries->description,
                                    "is_premium" => 0,
                                    "is_title" =>  "",
                                    "download" => 0,
                                    "video_upload_type" => "",
                                    "video_320" =>  "",
                                    "video_480" =>  "",
                                    "video_720" =>  "",
                                    "video_1080" =>  "",
                                    "video_extension" =>  "",
                                    "video_duration" => 0,
                                    "trailer_type" => $webseries->trailer_type ?? "",
                                    "trailer_url" => $webseries->trailer_url ?? "",
                                    "subtitle_type" => $webseries->subtitle_type ?? "",
                                    "subtitle_lang_1" => $webseries->subtitle_lang_1 ?? "",
                                    "subtitle_1" => $webseries->subtitle_1 ?? "",
                                    "subtitle_lang_2" => $webseries->subtitle_lang_2 ?? "",
                                    "subtitle_2" => $webseries->subtitle_2 ?? "",
                                    "subtitle_lang_3" => $webseries->subtitle_lang_3 ?? "",
                                    "subtitle_3" => $webseries->subtitle_3 ?? "",
                                    "release_date" => $webseries->release_date ?? "",
                                    "release_year" => $webseries->release_year ?? "",
                                    "imdb_rating" => $webseries->imdb_rating ?? "",
                                    "view" => 0,
                                    "status" => 1,
                                    "created_at" => $webseries->created_at ?? "",
                                    "updated_at" => $webseries->updated_at ?? "",
                                    "director_id" => $webseries->director_id ?? "",
                                    "supporting_cast_id" => $webseries->supporting_cast_id ?? "",
                                    "starring_id" => $webseries->starring_id ?? "",
                                    "networks" => $webseries->networks ?? "",
                                    "maturity_rating" => $webseries->maturity_rating ?? "",
                                    "age_restriction" => $webseries->age_restriction ?? "",
                                    "max_video_quality" => $webseries->max_video_quality ?? "",
                                    "release_tag" => $webseries->release_tag ?? "",
                                    "video_size" => 0,
                                    "is_exclusive_movie" => 0,
                                    "exclusive_movie_data" => $webseries->exclusive_movie_data,
                                    "related_videos" => $webseries->related_videos,
                                    "skip_enable" => $webseries->skip_enable,
                                    "skip_start" => $webseries->skip_start,
                                    "skip_end" => $webseries->skip_end,
                                ];

                                
                                unset($Final_Data[$i]['video_full_data']['seasons']);

                                $Final_Data[$i]['video_full_data']['trailer_url'] = $seasonTrailer;

                            }
                        }
                        elseif ($Data[$i]['video_type'] == 1) {
                            if ($Data[$i]['video'] != null) {

                                $Final_Data[$i]['id'] = $Data[$i]['video']['id'];
                                $Final_Data[$i]['name'] = $Data[$i]['video']['name'];
                                $Final_Data[$i]['category_id'] = $Data[$i]['video']['category_id'];
                                $Final_Data[$i]['description'] = $Data[$i]['video']['description'];
                                $Final_Data[$i]['type_id'] = (int) $Data[$i]['video']['type_id'];
                                $Final_Data[$i]['video_type'] = (int) $Data[$i]['video']['video_type'];
                                // Thumbnail && Landscape
                                $Final_Data[$i]['thumbnail'] = Get_Image($this->folder_video, $Data[$i]['video']['thumbnail']);
                                $Final_Data[$i]['landscape'] = Get_Image($this->folder_video, $Data[$i]['video']['landscape']);

                                // Video (320, 480, 720, 1080)
                                $Final_Data[$i]['video_upload_type'] = $Data[$i]['video']['video_upload_type'];
                                if (isset($Data[$i]['video']['video_320']) && !empty($Data[$i]['video']['video_320'])) {
                                    if ($Data[$i]['video']['video_upload_type'] == "server_video") {
                                        $Final_Data[$i]['video_320'] = Get_Video($this->folder_video, $Data[$i]['video']['video_320']);
                                    }
                                }
                                if (isset($Data[$i]['video']['video_480']) && !empty($Data[$i]['video']['video_480'])) {
                                    if ($Data[$i]['video']['video_upload_type'] == "server_video") {
                                        $Final_Data[$i]['video_480'] = Get_Video($this->folder_video, $Data[$i]['video']['video_480']);
                                    }
                                }
                                if (isset($Data[$i]['video']['video_720']) && !empty($Data[$i]['video']['video_720'])) {
                                    if ($Data[$i]['video']['video_upload_type'] == "server_video") {
                                        $Final_Data[$i]['video_720'] = Get_Video($this->folder_video, $Data[$i]['video']['video_720']);
                                    }
                                }
                                if (isset($Data[$i]['video']['video_1080']) && !empty($Data[$i]['video']['video_1080'])) {
                                    if ($Data[$i]['video']['video_upload_type'] == "server_video") {
                                        $Final_Data[$i]['video_1080'] = Get_Video($this->folder_video, $Data[$i]['video']['video_1080']);
                                    }
                                }

                                // Trailer
                                $Final_Data[$i]['trailer_type'] = $Data[$i]['video']['trailer_type'];
                                if (isset($Data[$i]['video']['trailer_url']) && !empty($Data[$i]['video']['trailer_url'])) {
                                    if ($Data[$i]['video']['trailer_type'] == "server_video") {
                                        $Data[$i]['video']['trailer_url'] = Get_Video($this->folder_video, $Data[$i]['video']['trailer_url']);
                                    }
                                }

                                // SubTitle_1_1_3
                                $Final_Data[$i]['subtitle_type'] = $Data[$i]['video']['subtitle_type'];
                                if (isset($Data[$i]['video']['subtitle_1']) && !empty($Data[$i]['video']['subtitle_1'])) {
                                    if ($Data[$i]['video']['subtitle_type'] == "server_video") {
                                        $Data[$i]['video']['subtitle_1'] = Get_Video($this->folder_video, $Data[$i]['video']['subtitle_1']);
                                    }
                                }
                                if (isset($Data[$i]['video']['subtitle_2']) && !empty($Data[$i]['video']['subtitle_2'])) {
                                    if ($Data[$i]['video']['subtitle_type'] == "server_video") {
                                        $Data[$i]['video']['subtitle_2'] = Get_Video($this->folder_video, $Data[$i]['video']['subtitle_2']);
                                    }
                                }
                                if (isset($Data[$i]['video']['subtitle_3']) && !empty($Data[$i]['video']['subtitle_3'])) {
                                    if ($Data[$i]['video']['subtitle_type'] == "server_video") {
                                        $Data[$i]['video']['subtitle_3'] = Get_Video($this->folder_video, $Data[$i]['video']['subtitle_3']);
                                    }
                                }

                                $Final_Data[$i]['stop_time'] = 0;
                                $Final_Data[$i]['is_downloaded'] = 0;
                                $Final_Data[$i]['is_bookmark'] = 0;
                                $Final_Data[$i]['rent_buy'] = 0;
                                $Final_Data[$i]['is_rent'] = IsRentVideo($Data[$i]['video']['id'], $Data[$i]['video']['video_type']);
                                $Final_Data[$i]['rent_price'] = GetPriceByRentVideo($Data[$i]['video']['id'], $Data[$i]['video']['video_type']);
                                $Final_Data[$i]['is_buy'] = 0;
                                $Final_Data[$i]['is_webSeries'] = 0;
                                $Final_Data[$i]['category_name'] = GetCategoryNameByIds($Data[$i]['video']['category_id']);
                                $Final_Data[$i]['session_id'] = "0";
                                $Final_Data[$i]['upcoming_type'] = 0;
                                $videoFullData = $Data[$i]['video'];
                                $videoFullData['video_size'] = isset($videoFullData['video_size']) 
                                    ? (int) $videoFullData['video_size'] 
                                    : 0;
                                
                                $videoFullData['is_exclusive_movie'] = isset($videoFullData['is_exclusive_movie']) 
                                    ? (int) $videoFullData['is_exclusive_movie'] 
                                    : 0;
                                $videoFullData['skip_enable'] = isset($videoFullData['skip_enable']) 
                                    ? (int) $videoFullData['skip_enable'] 
                                    : 0;
                                $Final_Data[$i]['video_full_data'] = $videoFullData; 
                            }
                        } else if ($Data[$i]['video_type'] == 2) {
                            if ($Data[$i]['tvshow'] != null) {

                                $Final_Data[$i]['id'] = $Data[$i]['tvshow']['id'];
                                $Final_Data[$i]['name'] = $Data[$i]['tvshow']['name'];
                                $Final_Data[$i]['category_id'] = $Data[$i]['tvshow']['category_id'];
                                $Final_Data[$i]['description'] = $Data[$i]['tvshow']['description'];
                                $Final_Data[$i]['video_type'] = (int) $Data[$i]['tvshow']['video_type'];
                                $Final_Data[$i]['type_id'] = (int) $Data[$i]['tvshow']['type_id'];
                                // Thumbnail && Landscape
                                $Final_Data[$i]['thumbnail'] = Get_Image($this->folder_show, $Data[$i]['tvshow']['thumbnail']);
                                $Final_Data[$i]['landscape'] = Get_Image($this->folder_show, $Data[$i]['tvshow']['landscape']);
                                // Trailer
                                $Final_Data[$i]['trailer_type'] = $Data[$i]['tvshow']['trailer_type'];
                                if (isset($Final_Data[$i]['trailer_url']) && !empty($Final_Data[$i]['trailer_url'])) {
                                    if ($Final_Data[$i]['trailer_type'] == "server_video") {
                                        $Final_Data[$i]['trailer_url'] = Get_Video($this->folder_video, $Final_Data[$i]['trailer_url']);
                                    }
                                }

                                $Final_Data[$i]['stop_time'] = 0;
                                $Final_Data[$i]['is_downloaded'] = 0;
                                $Final_Data[$i]['is_bookmark'] = 0;
                                $Final_Data[$i]['rent_buy'] = 0;
                                $Final_Data[$i]['is_rent'] = IsRentVideo($Data[$i]['tvshow']['id'], $Data[$i]['tvshow']['video_type']);
                                $Final_Data[$i]['rent_price'] = GetPriceByRentVideo($Data[$i]['tvshow']['id'], $Data[$i]['tvshow']['video_type']);
                                $Final_Data[$i]['is_buy'] = 0;
                                $Final_Data[$i]['is_webSeries'] = 0;
                                $Final_Data[$i]['category_name'] = GetCategoryNameByIds($Data[$i]['tvshow']['category_id']);
                                $Final_Data[$i]['session_id'] = GetSessionByTVShowId($Data[$i]['tvshow']['id']);
                                $Final_Data[$i]['upcoming_type'] = 0;
                            }
                        } else if ($Data[$i]['video_type'] == 5) {

                            if ($Data[$i]['upcoming_type'] == 1) {

                                if ($Data[$i]['video'] != null) {

                                    $Final_Data[$i]['id'] = $Data[$i]['video']['id'];
                                    $Final_Data[$i]['name'] = $Data[$i]['video']['name'];
                                    $Final_Data[$i]['category_id'] = $Data[$i]['video']['category_id'];
                                    $Final_Data[$i]['description'] = $Data[$i]['video']['description'];
                                    $Final_Data[$i]['type_id'] = (int) $Data[$i]['video']['type_id'];
                                    $Final_Data[$i]['video_type'] = (int) $Data[$i]['video']['video_type'];
                                    // Thumbnail && Landscape
                                    $Final_Data[$i]['thumbnail'] = Get_Image($this->folder_video, $Data[$i]['video']['thumbnail']);
                                    $Final_Data[$i]['landscape'] = Get_Image($this->folder_video, $Data[$i]['video']['landscape']);

                                    // Video (320, 480, 720, 1080)
                                    $Final_Data[$i]['video_upload_type'] = $Data[$i]['video']['video_upload_type'];
                                    if (isset($Data[$i]['video']['video_320']) && !empty($Data[$i]['video']['video_320'])) {
                                        if ($Data[$i]['video']['video_upload_type'] == "server_video") {
                                            $Final_Data[$i]['video_320'] = Get_Video($this->folder_video, $Data[$i]['video']['video_320']);
                                        }
                                    }
                                    if (isset($Data[$i]['video']['video_480']) && !empty($Data[$i]['video']['video_480'])) {
                                        if ($Data[$i]['video']['video_upload_type'] == "server_video") {
                                            $Final_Data[$i]['video_480'] = Get_Video($this->folder_video, $Data[$i]['video']['video_480']);
                                        }
                                    }
                                    if (isset($Data[$i]['video']['video_720']) && !empty($Data[$i]['video']['video_720'])) {
                                        if ($Data[$i]['video']['video_upload_type'] == "server_video") {
                                            $Final_Data[$i]['video_720'] = Get_Video($this->folder_video, $Data[$i]['video']['video_720']);
                                        }
                                    }
                                    if (isset($Data[$i]['video']['video_1080']) && !empty($Data[$i]['video']['video_1080'])) {
                                        if ($Data[$i]['video']['video_upload_type'] == "server_video") {
                                            $Final_Data[$i]['video_1080'] = Get_Video($this->folder_video, $Data[$i]['video']['video_1080']);
                                        }
                                    }

                                    // Trailer
                                    $Final_Data[$i]['trailer_type'] = $Data[$i]['video']['trailer_type'];
                                    if (isset($Data[$i]['video']['trailer_url']) && !empty($Data[$i]['video']['trailer_url'])) {
                                        if ($Data[$i]['video']['trailer_type'] == "server_video") {
                                            $Data[$i]['video']['trailer_url'] = Get_Video($this->folder_video, $Data[$i]['video']['trailer_url']);
                                        }
                                    }

                                    // SubTitle_1_1_3
                                    $Final_Data[$i]['subtitle_type'] = $Data[$i]['video']['subtitle_type'];
                                    if (isset($Data[$i]['video']['subtitle_1']) && !empty($Data[$i]['video']['subtitle_1'])) {
                                        if ($Data[$i]['video']['subtitle_type'] == "server_video") {
                                            $Data[$i]['video']['subtitle_1'] = Get_Video($this->folder_video, $Data[$i]['video']['subtitle_1']);
                                        }
                                    }
                                    if (isset($Data[$i]['video']['subtitle_2']) && !empty($Data[$i]['video']['subtitle_2'])) {
                                        if ($Data[$i]['video']['subtitle_type'] == "server_video") {
                                            $Data[$i]['video']['subtitle_2'] = Get_Video($this->folder_video, $Data[$i]['video']['subtitle_2']);
                                        }
                                    }
                                    if (isset($Data[$i]['video']['subtitle_3']) && !empty($Data[$i]['video']['subtitle_3'])) {
                                        if ($Data[$i]['video']['subtitle_type'] == "server_video") {
                                            $Data[$i]['video']['subtitle_3'] = Get_Video($this->folder_video, $Data[$i]['video']['subtitle_3']);
                                        }
                                    }

                                    $Final_Data[$i]['stop_time'] = 0;
                                    $Final_Data[$i]['is_downloaded'] = 0;
                                    $Final_Data[$i]['is_bookmark'] = 0;
                                    $Final_Data[$i]['rent_buy'] = 0;
                                    $Final_Data[$i]['is_rent'] = IsRentVideo($Data[$i]['video']['id'], $Data[$i]['video']['video_type']);
                                    $Final_Data[$i]['rent_price'] = GetPriceByRentVideo($Data[$i]['video']['id'], $Data[$i]['video']['video_type']);
                                    $Final_Data[$i]['is_buy'] = 0;
                                    $Final_Data[$i]['is_webSeries'] = 0;
                                    $Final_Data[$i]['category_name'] = GetCategoryNameByIds($Data[$i]['video']['category_id']);
                                    $Final_Data[$i]['session_id'] = "0";
                                    $Final_Data[$i]['upcoming_type'] = 1;
                                }
                            } else if ($Data[$i]['upcoming_type'] == 2) {

                                if ($Data[$i]['tvshow'] != null) {

                                    $Final_Data[$i]['id'] = $Data[$i]['tvshow']['id'];
                                    $Final_Data[$i]['name'] = $Data[$i]['tvshow']['name'];
                                    $Final_Data[$i]['category_id'] = $Data[$i]['tvshow']['category_id'];
                                    $Final_Data[$i]['description'] = $Data[$i]['tvshow']['description'];
                                    $Final_Data[$i]['video_type'] = (int) $Data[$i]['tvshow']['video_type'];
                                    $Final_Data[$i]['type_id'] = (int) $Data[$i]['tvshow']['type_id'];
                                    // Thumbnail && Landscape
                                    $Final_Data[$i]['thumbnail'] = Get_Image($this->folder_show, $Data[$i]['tvshow']['thumbnail']);
                                    $Final_Data[$i]['landscape'] = Get_Image($this->folder_show, $Data[$i]['tvshow']['landscape']);
                                    // Trailer
                                    $Final_Data[$i]['trailer_type'] = $Data[$i]['tvshow']['trailer_type'];
                                    if (isset($Final_Data[$i]['trailer_url']) && !empty($Final_Data[$i]['trailer_url'])) {
                                        if ($Final_Data[$i]['trailer_type'] == "server_video") {
                                            $Final_Data[$i]['trailer_url'] = Get_Video($this->folder_video, $Final_Data[$i]['trailer_url']);
                                        }
                                    }

                                    $Final_Data[$i]['stop_time'] = 0;
                                    $Final_Data[$i]['is_downloaded'] = 0;
                                    $Final_Data[$i]['is_bookmark'] = 0;
                                    $Final_Data[$i]['rent_buy'] = 0;
                                    $Final_Data[$i]['is_rent'] = IsRentVideo($Data[$i]['tvshow']['id'], $Data[$i]['tvshow']['video_type']);
                                    $Final_Data[$i]['rent_price'] = GetPriceByRentVideo($Data[$i]['tvshow']['id'], $Data[$i]['tvshow']['video_type']);
                                    $Final_Data[$i]['is_buy'] = 0;
                                    $Final_Data[$i]['is_webSeries'] = 0;
                                    $Final_Data[$i]['category_name'] = GetCategoryNameByIds($Data[$i]['tvshow']['category_id']);
                                    $Final_Data[$i]['session_id'] = GetSessionByTVShowId($Data[$i]['tvshow']['id']);
                                    $Final_Data[$i]['upcoming_type'] = 2;
                                }
                            }
                        }
                    }

                    $reset = array_values($Final_Data);
                    return APIResponse(200, __('api_msg.get_record_successfully'), $reset);
                } else {
                    return APIResponse(200, __('api_msg.get_record_successfully'), []);
                }
            } elseif ($is_home_page == "2") {
                
                $Data = Banner::where('is_home_screen', '2')->where('type_id', $type_id)->with('webseries')->with('video')->with('tvshow')->latest()->get();
                if (count($Data) > 0) {

                    $Final_Data = [];
                    for ($i = 0; $i < count($Data); $i++) {

                        if ($Data[$i]['video_type'] == 0) {
                            
                            if ($Data[$i]['webseries'] != null) {

                                $Final_Data[$i]['id'] = 1;
                                $Final_Data[$i]['name'] = $Data[$i]['webseries']['title'];
                                $Final_Data[$i]['category_id'] = $Data[$i]['webseries']['category_id'];
                                $Final_Data[$i]['description'] = $Data[$i]['webseries']['description'];
                                $Final_Data[$i]['type_id'] = (int) $Data[$i]['type_id'];
                                $Final_Data[$i]['video_type'] = (int) $Data[$i]['webseries']['video_type'];
                                // Thumbnail && Landscape
                                $Final_Data[$i]['thumbnail'] = asset("images/web_series/" . $Data[$i]['webseries']['thumbnail']);
                                $Final_Data[$i]['landscape'] = asset("images/web_series/" . $Data[$i]['webseries']['thumbnail']);
                                

                                // Video (320, 480, 720, 1080)
                                $Final_Data[$i]['video_upload_type'] = $Data[$i]['webseries']['video_upload_type'];
                                if (isset($Data[$i]['webseries']['video_320']) && !empty($Data[$i]['webseries']['video_320'])) {
                                    if ($Data[$i]['webseries']['video_upload_type'] == "server_video") {
                                        $Final_Data[$i]['video_320'] = Get_Video($this->folder_video, $Data[$i]['video']['video_320']);
                                    }
                                }
                                if (isset($Data[$i]['webseries']['video_480']) && !empty($Data[$i]['webseries']['video_480'])) {
                                    if ($Data[$i]['webseries']['video_upload_type'] == "server_video") {
                                        $Final_Data[$i]['video_480'] = Get_Video($this->folder_video, $Data[$i]['webseries']['video_480']);
                                    }
                                }
                                if (isset($Data[$i]['webseries']['video_720']) && !empty($Data[$i]['webseries']['video_720'])) {
                                    if ($Data[$i]['webseries']['video_upload_type'] == "server_video") {
                                        $Final_Data[$i]['video_720'] = Get_Video($this->folder_video, $Data[$i]['webseries']['video_720']);
                                    }
                                }
                                if (isset($Data[$i]['video']['video_1080']) && !empty($Data[$i]['webseries']['video_1080'])) {
                                    if ($Data[$i]['video']['video_upload_type'] == "server_video") {
                                        $Final_Data[$i]['video_1080'] = Get_Video($this->folder_video, $Data[$i]['webseries']['video_1080']);
                                    }
                                }

                                // Trailer
                                $Final_Data[$i]['trailer_type'] = $Data[$i]['webseries']['trailer_type'];
                                if (isset($Data[$i]['webseries']['trailer_url']) && !empty($Data[$i]['video']['trailer_url'])) {
                                    if ($Data[$i]['webseries']['trailer_type'] == "server_video") {
                                        $Data[$i]['webseries']['trailer_url'] = Get_Video($this->folder_video, $Data[$i]['webseries']['trailer_url']);
                                    }
                                }

                                // SubTitle_1_1_3
                                $Final_Data[$i]['subtitle_type'] = $Data[$i]['webseries']['subtitle_type'];
                                if (isset($Data[$i]['webseries']['subtitle_1']) && !empty($Data[$i]['webseries']['subtitle_1'])) {
                                    if ($Data[$i]['webseries']['subtitle_type'] == "server_video") {
                                        $Data[$i]['webseries']['subtitle_1'] = Get_Video($this->folder_video, $Data[$i]['webseries']['subtitle_1']);
                                    }
                                }
                                if (isset($Data[$i]['webseries']['subtitle_2']) && !empty($Data[$i]['webseries']['subtitle_2'])) {
                                    if ($Data[$i]['webseries']['subtitle_type'] == "server_video") {
                                        $Data[$i]['webseries']['subtitle_2'] = Get_Video($this->folder_video, $Data[$i]['webseries']['subtitle_2']);
                                    }
                                }
                                if (isset($Data[$i]['webseries']['subtitle_3']) && !empty($Data[$i]['webseries']['subtitle_3'])) {
                                    if ($Data[$i]['webseries']['subtitle_type'] == "server_video") {
                                        $Data[$i]['webseries']['subtitle_3'] = Get_Video($this->folder_video, $Data[$i]['webseries']['subtitle_3']);
                                    }
                                }

                                $Final_Data[$i]['stop_time'] = 0;
                                $Final_Data[$i]['is_downloaded'] = 0;
                                $Final_Data[$i]['is_bookmark'] = 0;
                                $Final_Data[$i]['rent_buy'] = 0;
                                $Final_Data[$i]['is_rent'] = IsRentVideo($Data[$i]['webseries']['id'], $Data[$i]['webseries']['video_type']);
                                $Final_Data[$i]['rent_price'] = GetPriceByRentVideo($Data[$i]['webseries']['id'], $Data[$i]['webseries']['video_type']);
                                $Final_Data[$i]['is_buy'] = 0;
                                $Final_Data[$i]['is_webSeries'] = 1;
                                $Final_Data[$i]['category_name'] = GetCategoryNameByIds($Data[$i]['webseries']['category_id']);
                                $Final_Data[$i]['session_id'] = "0";
                                $Final_Data[$i]['upcoming_type'] = 0;
								$seasonTrailer = null;

                                $webseries = $Data[$i]['webseries'];

                                if ($webseries && $webseries->seasons && $webseries->seasons->count() > 0) {
 
                                    $lastSeason = $webseries->seasons
                                        ->sortByDesc('id')
                                        ->first();

                                    if ($lastSeason && !empty($lastSeason->video)) {
                                       $seasonTrailer = $lastSeason->video;
                                    }
                                }

                                // $Final_Data[$i]['video_full_data'] = $webseries->toArray();
                                $Final_Data[$i]['video_full_data'] = [
                                    "id" => $webseries->id,
                                    "channel_id" => "",
                                    "category_id" => "",
                                    "language_id" => "",
                                    "cast_id" => "",
                                    "type_id" => (int) $Data[$i]['type_id'],
                                    "video_type" => 0,
                                    "name" => $webseries->title,
                                    "thumbnail" => asset("images/web_series/" . $webseries->thumbnail),
                                    "landscape" => asset("images/web_series/" . $webseries->thumbnail),
                                    "description" => $webseries->description,
                                    "is_premium" => 0,
                                    "is_title" =>  "",
                                    "download" => 0,
                                    "video_upload_type" => "",
                                    "video_320" =>  "",
                                    "video_480" =>  "",
                                    "video_720" =>  "",
                                    "video_1080" =>  "",
                                    "video_extension" =>  "",
                                    "video_duration" => 0,
                                    "trailer_type" => $webseries->trailer_type ?? "",
                                    "trailer_url" => $webseries->trailer_url ?? "",
                                    "subtitle_type" => $webseries->subtitle_type ?? "",
                                    "subtitle_lang_1" => $webseries->subtitle_lang_1 ?? "",
                                    "subtitle_1" => $webseries->subtitle_1 ?? "",
                                    "subtitle_lang_2" => $webseries->subtitle_lang_2 ?? "",
                                    "subtitle_2" => $webseries->subtitle_2 ?? "",
                                    "subtitle_lang_3" => $webseries->subtitle_lang_3 ?? "",
                                    "subtitle_3" => $webseries->subtitle_3 ?? "",
                                    "release_date" => $webseries->release_date ?? "",
                                    "release_year" => $webseries->release_year ?? "",
                                    "imdb_rating" => $webseries->imdb_rating ?? "",
                                    "view" => 0,
                                    "status" => 1,
                                    "created_at" => $webseries->created_at ?? "",
                                    "updated_at" => $webseries->updated_at ?? "",
                                    "director_id" => $webseries->director_id ?? "",
                                    "supporting_cast_id" => $webseries->supporting_cast_id ?? "",
                                    "starring_id" => $webseries->starring_id ?? "",
                                    "networks" => $webseries->networks ?? "",
                                    "maturity_rating" => $webseries->maturity_rating ?? "",
                                    "age_restriction" => $webseries->age_restriction ?? "",
                                    "max_video_quality" => $webseries->max_video_quality ?? "",
                                    "release_tag" => $webseries->release_tag ?? "",
                                    "video_size" => 0,
                                    "is_exclusive_movie" => 0,
                                    "exclusive_movie_data" => $webseries->exclusive_movie_data,
                                    "related_videos" => $webseries->related_videos,
                                    "skip_enable" => $webseries->skip_enable,
                                    "skip_start" => $webseries->skip_start,
                                    "skip_end" => $webseries->skip_end,
                                ];

                                
                                unset($Final_Data[$i]['video_full_data']['seasons']);

                                $Final_Data[$i]['video_full_data']['trailer_url'] = $seasonTrailer;
                            }
                        }
                        else if ($Data[$i]['video_type'] == 1) {
                            if ($Data[$i]['video'] != null) {
                                
                                $Final_Data[$i]['id'] = $Data[$i]['video']['id'];
                                $Final_Data[$i]['name'] = $Data[$i]['video']['name'];
                                $Final_Data[$i]['category_id'] = $Data[$i]['video']['category_id'];
                                $Final_Data[$i]['description'] = $Data[$i]['video']['description'];
                                $Final_Data[$i]['type_id'] = (int) $Data[$i]['video']['type_id'];
                                $Final_Data[$i]['video_type'] = $Data[$i]['video']['video_type'];
                                // Thumbnail && Landscape
                                $Final_Data[$i]['thumbnail'] = Get_Image($this->folder_video, $Data[$i]['video']['thumbnail']);
                                $Final_Data[$i]['landscape'] = Get_Image($this->folder_video, $Data[$i]['video']['landscape']);

                                // Video (320, 480, 720, 1080)
                                $Final_Data[$i]['video_upload_type'] = $Data[$i]['video']['video_upload_type'];
                                if (isset($Data[$i]['video']['video_320']) && !empty($Data[$i]['video']['video_320'])) {
                                    if ($Data[$i]['video']['video_upload_type'] == "server_video") {
                                        $Final_Data[$i]['video_320'] = Get_Video($this->folder_video, $Data[$i]['video']['video_320']);
                                    }
                                }
                                if (isset($Data[$i]['video']['video_480']) && !empty($Data[$i]['video']['video_480'])) {
                                    if ($Data[$i]['video']['video_upload_type'] == "server_video") {
                                        $Final_Data[$i]['video_480'] = Get_Video($this->folder_video, $Data[$i]['video']['video_480']);
                                    }
                                }
                                if (isset($Data[$i]['video']['video_720']) && !empty($Data[$i]['video']['video_720'])) {
                                    if ($Data[$i]['video']['video_upload_type'] == "server_video") {
                                        $Final_Data[$i]['video_720'] = Get_Video($this->folder_video, $Data[$i]['video']['video_720']);
                                    }
                                }
                                if (isset($Data[$i]['video']['video_1080']) && !empty($Data[$i]['video']['video_1080'])) {
                                    if ($Data[$i]['video']['video_upload_type'] == "server_video") {
                                        $Final_Data[$i]['video_1080'] = Get_Video($this->folder_video, $Data[$i]['video']['video_1080']);
                                    }
                                }

                                // Trailer
                                $Final_Data[$i]['trailer_type'] = $Data[$i]['video']['trailer_type'];
                                if (isset($Data[$i]['video']['trailer_url']) && !empty($Data[$i]['video']['trailer_url'])) {
                                    if ($Data[$i]['video']['trailer_type'] == "server_video") {
                                        $Data[$i]['video']['trailer_url'] = Get_Video($this->folder_video, $Data[$i]['video']['trailer_url']);
                                    }
                                }

                                // SubTitle_1_1_3
                                $Final_Data[$i]['subtitle_type'] = $Data[$i]['video']['subtitle_type'];
                                if (isset($Data[$i]['video']['subtitle_1']) && !empty($Data[$i]['video']['subtitle_1'])) {
                                    if ($Data[$i]['video']['subtitle_type'] == "server_video") {
                                        $Data[$i]['video']['subtitle_1'] = Get_Video($this->folder_video, $Data[$i]['video']['subtitle_1']);
                                    }
                                }
                                if (isset($Data[$i]['video']['subtitle_2']) && !empty($Data[$i]['video']['subtitle_2'])) {
                                    if ($Data[$i]['video']['subtitle_type'] == "server_video") {
                                        $Data[$i]['video']['subtitle_2'] = Get_Video($this->folder_video, $Data[$i]['video']['subtitle_2']);
                                    }
                                }
                                if (isset($Data[$i]['video']['subtitle_3']) && !empty($Data[$i]['video']['subtitle_3'])) {
                                    if ($Data[$i]['video']['subtitle_type'] == "server_video") {
                                        $Data[$i]['video']['subtitle_3'] = Get_Video($this->folder_video, $Data[$i]['video']['subtitle_3']);
                                    }
                                }

                                $Final_Data[$i]['stop_time'] = 0;
                                $Final_Data[$i]['is_downloaded'] = 0;
                                $Final_Data[$i]['is_bookmark'] = 0;
                                $Final_Data[$i]['rent_buy'] = 0;
                                $Final_Data[$i]['is_rent'] = IsRentVideo($Data[$i]['video']['id'], $Data[$i]['video']['video_type']);
                                $Final_Data[$i]['rent_price'] = GetPriceByRentVideo($Data[$i]['video']['id'], $Data[$i]['video']['video_type']);
                                $Final_Data[$i]['is_buy'] = 0;
                                $Final_Data[$i]['category_name'] = GetCategoryNameByIds($Data[$i]['video']['category_id']);
                                $Final_Data[$i]['session_id'] = "0";
                                $Final_Data[$i]['upcoming_type'] = 0; 
                                $videoFullData = $Data[$i]['video'];
                                $videoFullData['video_size'] = isset($videoFullData['video_size']) 
                                    ? (int) $videoFullData['video_size'] 
                                    : 0;
                                
                                $videoFullData['is_exclusive_movie'] = isset($videoFullData['is_exclusive_movie']) 
                                    ? (int) $videoFullData['is_exclusive_movie'] 
                                    : 0;
                                $videoFullData['skip_enable'] = isset($videoFullData['skip_enable']) 
                                    ? (int) $videoFullData['skip_enable'] 
                                    : 0;
                                $Final_Data[$i]['video_full_data'] = $videoFullData; 
                            }
                        } else if ($Data[$i]['video_type'] == 2) {
                            if ($Data[$i]['tvshow'] != null) {

                                $Final_Data[$i]['id'] = $Data[$i]['tvshow']['id'];
                                $Final_Data[$i]['name'] = $Data[$i]['tvshow']['name'];
                                $Final_Data[$i]['category_id'] = $Data[$i]['tvshow']['category_id'];
                                $Final_Data[$i]['description'] = $Data[$i]['tvshow']['description'];
                                $Final_Data[$i]['video_type'] = (int) $Data[$i]['tvshow']['video_type'];
                                $Final_Data[$i]['type_id'] = (int) $Data[$i]['tvshow']['type_id'];
                                // Thumbnail && Landscape
                                $Final_Data[$i]['thumbnail'] = Get_Image($this->folder_show, $Data[$i]['tvshow']['thumbnail']);
                                $Final_Data[$i]['landscape'] = Get_Image($this->folder_show, $Data[$i]['tvshow']['landscape']);
                                // Trailer
                                $Final_Data[$i]['trailer_type'] = $Data[$i]['tvshow']['trailer_type'];
                                if (isset($Final_Data[$i]['trailer_url']) && !empty($Final_Data[$i]['trailer_url'])) {
                                    if ($Final_Data[$i]['trailer_type'] == "server_video") {
                                        $Final_Data[$i]['trailer_url'] = Get_Video($this->folder_video, $Final_Data[$i]['trailer_url']);
                                    }
                                }

                                $Final_Data[$i]['stop_time'] = 0;
                                $Final_Data[$i]['is_downloaded'] = 0;
                                $Final_Data[$i]['is_bookmark'] = 0;
                                $Final_Data[$i]['rent_buy'] = 0;
                                $Final_Data[$i]['is_rent'] = IsRentVideo($Data[$i]['tvshow']['id'], $Data[$i]['tvshow']['video_type']);
                                $Final_Data[$i]['rent_price'] = GetPriceByRentVideo($Data[$i]['tvshow']['id'], $Data[$i]['tvshow']['video_type']);
                                $Final_Data[$i]['is_buy'] = 0;
                                $Final_Data[$i]['category_name'] = GetCategoryNameByIds($Data[$i]['tvshow']['category_id']);
                                $Final_Data[$i]['session_id'] = GetSessionByTVShowId($Data[$i]['tvshow']['id']);
                                $Final_Data[$i]['upcoming_type'] = 0;
                            }
                        } else if ($Data[$i]['video_type'] == 5) {

                            if ($Data[$i]['upcoming_type'] == 1) {

                                if ($Data[$i]['video'] != null) {

                                    $Final_Data[$i]['id'] = $Data[$i]['video']['id'];
                                    $Final_Data[$i]['name'] = $Data[$i]['video']['name'];
                                    $Final_Data[$i]['category_id'] = $Data[$i]['video']['category_id'];
                                    $Final_Data[$i]['description'] = $Data[$i]['video']['description'];
                                    $Final_Data[$i]['type_id'] = (int) $Data[$i]['video']['type_id'];
                                    $Final_Data[$i]['video_type'] = (int) $Data[$i]['video']['video_type'];
                                    // Thumbnail && Landscape
                                    $Final_Data[$i]['thumbnail'] = Get_Image($this->folder_video, $Data[$i]['video']['thumbnail']);
                                    $Final_Data[$i]['landscape'] = Get_Image($this->folder_video, $Data[$i]['video']['landscape']);

                                    // Video (320, 480, 720, 1080)
                                    $Final_Data[$i]['video_upload_type'] = $Data[$i]['video']['video_upload_type'];
                                    if (isset($Data[$i]['video']['video_320']) && !empty($Data[$i]['video']['video_320'])) {
                                        if ($Data[$i]['video']['video_upload_type'] == "server_video") {
                                            $Final_Data[$i]['video_320'] = Get_Video($this->folder_video, $Data[$i]['video']['video_320']);
                                        }
                                    }
                                    if (isset($Data[$i]['video']['video_480']) && !empty($Data[$i]['video']['video_480'])) {
                                        if ($Data[$i]['video']['video_upload_type'] == "server_video") {
                                            $Final_Data[$i]['video_480'] = Get_Video($this->folder_video, $Data[$i]['video']['video_480']);
                                        }
                                    }
                                    if (isset($Data[$i]['video']['video_720']) && !empty($Data[$i]['video']['video_720'])) {
                                        if ($Data[$i]['video']['video_upload_type'] == "server_video") {
                                            $Final_Data[$i]['video_720'] = Get_Video($this->folder_video, $Data[$i]['video']['video_720']);
                                        }
                                    }
                                    if (isset($Data[$i]['video']['video_1080']) && !empty($Data[$i]['video']['video_1080'])) {
                                        if ($Data[$i]['video']['video_upload_type'] == "server_video") {
                                            $Final_Data[$i]['video_1080'] = Get_Video($this->folder_video, $Data[$i]['video']['video_1080']);
                                        }
                                    }

                                    // Trailer
                                    $Final_Data[$i]['trailer_type'] = $Data[$i]['video']['trailer_type'];
                                    if (isset($Data[$i]['video']['trailer_url']) && !empty($Data[$i]['video']['trailer_url'])) {
                                        if ($Data[$i]['video']['trailer_type'] == "server_video") {
                                            $Data[$i]['video']['trailer_url'] = Get_Video($this->folder_video, $Data[$i]['video']['trailer_url']);
                                        }
                                    }

                                    // SubTitle_1_1_3
                                    $Final_Data[$i]['subtitle_type'] = $Data[$i]['video']['subtitle_type'];
                                    if (isset($Data[$i]['video']['subtitle_1']) && !empty($Data[$i]['video']['subtitle_1'])) {
                                        if ($Data[$i]['video']['subtitle_type'] == "server_video") {
                                            $Data[$i]['video']['subtitle_1'] = Get_Video($this->folder_video, $Data[$i]['video']['subtitle_1']);
                                        }
                                    }
                                    if (isset($Data[$i]['video']['subtitle_2']) && !empty($Data[$i]['video']['subtitle_2'])) {
                                        if ($Data[$i]['video']['subtitle_type'] == "server_video") {
                                            $Data[$i]['video']['subtitle_2'] = Get_Video($this->folder_video, $Data[$i]['video']['subtitle_2']);
                                        }
                                    }
                                    if (isset($Data[$i]['video']['subtitle_3']) && !empty($Data[$i]['video']['subtitle_3'])) {
                                        if ($Data[$i]['video']['subtitle_type'] == "server_video") {
                                            $Data[$i]['video']['subtitle_3'] = Get_Video($this->folder_video, $Data[$i]['video']['subtitle_3']);
                                        }
                                    }

                                    $Final_Data[$i]['stop_time'] = 0;
                                    $Final_Data[$i]['is_downloaded'] = 0;
                                    $Final_Data[$i]['is_bookmark'] = 0;
                                    $Final_Data[$i]['rent_buy'] = 0;
                                    $Final_Data[$i]['is_rent'] = IsRentVideo($Data[$i]['video']['id'], $Data[$i]['video']['video_type']);
                                    $Final_Data[$i]['rent_price'] = GetPriceByRentVideo($Data[$i]['video']['id'], $Data[$i]['video']['video_type']);
                                    $Final_Data[$i]['is_buy'] = 0;
                                    $Final_Data[$i]['category_name'] = GetCategoryNameByIds($Data[$i]['video']['category_id']);
                                    $Final_Data[$i]['session_id'] = "0";
                                    $Final_Data[$i]['upcoming_type'] = 1;
                                }
                            } else if ($Data[$i]['upcoming_type'] == 2) {

                                if ($Data[$i]['tvshow'] != null) {

                                    $Final_Data[$i]['id'] = $Data[$i]['tvshow']['id'];
                                    $Final_Data[$i]['name'] = $Data[$i]['tvshow']['name'];
                                    $Final_Data[$i]['category_id'] = $Data[$i]['tvshow']['category_id'];
                                    $Final_Data[$i]['description'] = $Data[$i]['tvshow']['description'];
                                    $Final_Data[$i]['video_type'] = (int) $Data[$i]['tvshow']['video_type'];
                                    $Final_Data[$i]['type_id'] = (int) $Data[$i]['tvshow']['type_id'];
                                    // Thumbnail && Landscape
                                    $Final_Data[$i]['thumbnail'] = Get_Image($this->folder_show, $Data[$i]['tvshow']['thumbnail']);
                                    $Final_Data[$i]['landscape'] = Get_Image($this->folder_show, $Data[$i]['tvshow']['landscape']);
                                    // Trailer
                                    $Final_Data[$i]['trailer_type'] = $Data[$i]['tvshow']['trailer_type'];
                                    if (isset($Final_Data[$i]['trailer_url']) && !empty($Final_Data[$i]['trailer_url'])) {
                                        if ($Final_Data[$i]['trailer_type'] == "server_video") {
                                            $Final_Data[$i]['trailer_url'] = Get_Video($this->folder_video, $Final_Data[$i]['trailer_url']);
                                        }
                                    }

                                    $Final_Data[$i]['stop_time'] = 0;
                                    $Final_Data[$i]['is_downloaded'] = 0;
                                    $Final_Data[$i]['is_bookmark'] = 0;
                                    $Final_Data[$i]['rent_buy'] = 0;
                                    $Final_Data[$i]['is_rent'] = IsRentVideo($Data[$i]['tvshow']['id'], $Data[$i]['tvshow']['video_type']);
                                    $Final_Data[$i]['rent_price'] = GetPriceByRentVideo($Data[$i]['tvshow']['id'], $Data[$i]['tvshow']['video_type']);
                                    $Final_Data[$i]['is_buy'] = 0;
                                    $Final_Data[$i]['category_name'] = GetCategoryNameByIds($Data[$i]['tvshow']['category_id']);
                                    $Final_Data[$i]['session_id'] = GetSessionByTVShowId($Data[$i]['tvshow']['id']);
                                    $Final_Data[$i]['upcoming_type'] = 2;
                                }
                            }
                        }
                    }

                    $reset = array_values($Final_Data);
                    return APIResponse(200, __('api_msg.get_record_successfully'), $reset);
                } else {
                    return APIResponse(200, __('api_msg.get_record_successfully'), []);
                }
            } else {
                return APIResponse(200, __('api_msg.get_record_successfully'), []);
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function section_list(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'type_id' => 'required|numeric',
                    'is_home_page' => 'required|numeric',
                ],
                [
                    'type_id.required' => __('api_msg.please_enter_required_fields'),
                    'is_home_page.required' => __('api_msg.please_enter_required_fields'),
                ]
            );
            if ($validation->fails()) {

                $errors = $validation->errors()->first('type_id');
                $errors1 = $validation->errors()->first('is_home_page');
                $data['status'] = 400;
                if ($errors) {
                    $data['message'] = $errors;
                } elseif ($errors1) {
                    $data['message'] = $errors1;
                }
                return $data;
            }

            $type_id = $request->type_id;
            $is_home_page = $request->is_home_page;
            $user_id = isset($request->user_id) ? $request->user_id : 0;
			$IsBuyByUserData = Transction::select('id')->where('user_id', $user_id)->where('status', '1')->first();
			if (!empty($IsBuyByUserData)) {
				$isBuyByUser = 1;
			} else {
				$isBuyByUser = 1; //temp expiry
			}
            if ($is_home_page == "1") {

                $data = App_Section::where('is_home_screen', '1')->orderBy('position')->get();
                if (count($data) > 0) {
                   
                    for ($i = 0; $i < count($data); $i++) {

                        $data[$i]['data'] = [];
                        $Ids = explode(',', $data[$i]['video_id']);
						$videoIds = $data[$i]['video_id'];
                        if ($data[$i]['video_type'] == 0) { 
                            $video_data = WebSeries::select(
                                            'id',
                                            'title as name',
                                            'slug',
                                            'description',
                                            'thumbnail',
                                            'video'
                                        )
                                        ->whereIn('id', $Ids)
                                        ->orderByRaw("FIELD(id, $videoIds)")->get();

                            //$video_data = Video::whereIn('id', $Ids)->orderByRaw("FIELD(id, $videoIds)")->get();

                            if (count($video_data) > 0) {

                                $data[$i]['data'] = $video_data;
                                for ($j = 0; $j < count($video_data); $j++) {
                                       
                                    // Thumbnail && Landscape
                                    $data[$i]['data'][$j]['thumbnail'] = Get_Image($this->folder_video, $data[$i]['data'][$j]['thumbnail']);
                                    $data[$i]['data'][$j]['landscape'] = Get_Image($this->folder_video, $data[$i]['data'][$j]['thumbnail']);

                                    // Video (320, 480, 720, 1080)
                                    if (isset($data[$i]['data'][$j]['video_320']) && !empty($data[$i]['data'][$j]['video_320'])) {
                                        if ($data[$i]['data'][$j]['video_upload_type'] == "server_video") {
                                            $data[$i]['data'][$j]['video_320'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['video_320']);
                                        }
                                    }
                                    if (isset($data[$i]['data'][$j]['video_480']) && !empty($data[$i]['data'][$j]['video_480'])) {
                                        if ($data[$i]['data'][$j]['video_upload_type'] == "server_video") {
                                            $data[$i]['data'][$j]['video_480'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['video_480']);
                                        }
                                    }
                                    if (isset($data[$i]['data'][$j]['video_720']) && !empty($data[$i]['data'][$j]['video_720'])) {
                                        if ($data[$i]['data'][$j]['video_upload_type'] == "server_video") {
                                            $data[$i]['data'][$j]['video_720'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['video_720']);
                                        }
                                    }
                                    if (isset($data[$i]['data'][$j]['video_1080']) && !empty($data[$i]['data'][$j]['video_1080'])) {
                                        if ($data[$i]['data'][$j]['video_upload_type'] == "server_video") {
                                            $data[$i]['data'][$j]['video_1080'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['video_1080']);
                                        }
                                    }

                                    // Trailer
                                    if (isset($data[$i]['data'][$j]['trailer_url']) && !empty($data[$i]['data'][$j]['trailer_url'])) {
                                        if ($data[$i]['data'][$j]['trailer_type'] == "server_video") {
                                            $data[$i]['data'][$j]['trailer_url'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['trailer_url']);
                                        }
                                    }

                                    // SubTitle_1_2_3
                                    if (isset($data[$i]['data'][$j]['subtitle_1']) && !empty($data[$i]['data'][$j]['subtitle_1'])) {
                                        if ($data[$i]['data'][$j]['subtitle_type'] == "server_file") {
                                            $data[$i]['data'][$j]['subtitle_1'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['subtitle_1']);
                                        }
                                    }
                                    if (isset($data[$i]['data'][$j]['subtitle_2']) && !empty($data[$i]['data'][$j]['subtitle_2'])) {
                                        if ($data[$i]['data'][$j]['subtitle_type'] == "server_file") {
                                            $data[$i]['data'][$j]['subtitle_2'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['subtitle_2']);
                                        }
                                    }
                                    if (isset($data[$i]['data'][$j]['subtitle_3']) && !empty($data[$i]['data'][$j]['subtitle_3'])) {
                                        if ($data[$i]['data'][$j]['subtitle_type'] == "server_file") {
                                            $data[$i]['data'][$j]['subtitle_3'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['subtitle_3']);
                                        }
                                    }

                                    $data[$i]['data'][$j]['stop_time'] = GetStopTimeByUser($user_id, $data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['type_id'], $data[$i]['data'][$j]['video_type']);
                                    $data[$i]['data'][$j]['is_downloaded'] = Is_DownloadByUser($user_id, $data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['type_id'], $data[$i]['data'][$j]['video_type']);
                                    $data[$i]['data'][$j]['is_bookmark'] = Is_BookmarkByUser($user_id, $data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['type_id'], $data[$i]['data'][$j]['video_type']);
                                    $data[$i]['data'][$j]['rent_buy'] = VideoRentBuyByUser($user_id, $data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['type_id'], $data[$i]['data'][$j]['video_type']);
                                    $data[$i]['data'][$j]['is_rent'] = IsRentVideo($data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['video_type']);
                                    $data[$i]['data'][$j]['rent_price'] = GetPriceByRentVideo($data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['video_type']);
                                    $data[$i]['data'][$j]['is_buy'] = $isBuyByUser;
                                    $data[$i]['data'][$j]['category_name'] = GetCategoryNameByIds($data[$i]['data'][$j]['category_id']);
                                    $data[$i]['data'][$j]['session_id'] = "0";
                                    $data[$i]['data'][$j]['upcoming_type'] = 0;
                                    
                                }
                            } else {
                                $data[$i]['data'] = [];
                            }
                        } elseif ($data[$i]['video_type'] == 1) {

                            $video_data = Video::whereIn('id', $Ids)->orderByRaw("FIELD(id, $videoIds)")->get();

                            if (count($video_data) > 0) {

                                $data[$i]['data'] = $video_data;
                                for ($j = 0; $j < count($video_data); $j++) {

                                    // Thumbnail && Landscape
                                    $data[$i]['data'][$j]['thumbnail'] = Get_Image($this->folder_video, $data[$i]['data'][$j]['thumbnail']);
                                    $data[$i]['data'][$j]['landscape'] = Get_Image($this->folder_video, $data[$i]['data'][$j]['landscape']);

                                    // Video (320, 480, 720, 1080)
                                    if (isset($data[$i]['data'][$j]['video_320']) && !empty($data[$i]['data'][$j]['video_320'])) {
                                        if ($data[$i]['data'][$j]['video_upload_type'] == "server_video") {
                                            $data[$i]['data'][$j]['video_320'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['video_320']);
                                        }
                                    }
                                    if (isset($data[$i]['data'][$j]['video_480']) && !empty($data[$i]['data'][$j]['video_480'])) {
                                        if ($data[$i]['data'][$j]['video_upload_type'] == "server_video") {
                                            $data[$i]['data'][$j]['video_480'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['video_480']);
                                        }
                                    }
                                    if (isset($data[$i]['data'][$j]['video_720']) && !empty($data[$i]['data'][$j]['video_720'])) {
                                        if ($data[$i]['data'][$j]['video_upload_type'] == "server_video") {
                                            $data[$i]['data'][$j]['video_720'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['video_720']);
                                        }
                                    }
                                    if (isset($data[$i]['data'][$j]['video_1080']) && !empty($data[$i]['data'][$j]['video_1080'])) {
                                        if ($data[$i]['data'][$j]['video_upload_type'] == "server_video") {
                                            $data[$i]['data'][$j]['video_1080'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['video_1080']);
                                        }
                                    }

                                    // Trailer
                                    if (isset($data[$i]['data'][$j]['trailer_url']) && !empty($data[$i]['data'][$j]['trailer_url'])) {
                                        if ($data[$i]['data'][$j]['trailer_type'] == "server_video") {
                                            $data[$i]['data'][$j]['trailer_url'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['trailer_url']);
                                        }
                                    }

                                    // SubTitle_1_2_3
                                    if (isset($data[$i]['data'][$j]['subtitle_1']) && !empty($data[$i]['data'][$j]['subtitle_1'])) {
                                        if ($data[$i]['data'][$j]['subtitle_type'] == "server_file") {
                                            $data[$i]['data'][$j]['subtitle_1'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['subtitle_1']);
                                        }
                                    }
                                    if (isset($data[$i]['data'][$j]['subtitle_2']) && !empty($data[$i]['data'][$j]['subtitle_2'])) {
                                        if ($data[$i]['data'][$j]['subtitle_type'] == "server_file") {
                                            $data[$i]['data'][$j]['subtitle_2'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['subtitle_2']);
                                        }
                                    }
                                    if (isset($data[$i]['data'][$j]['subtitle_3']) && !empty($data[$i]['data'][$j]['subtitle_3'])) {
                                        if ($data[$i]['data'][$j]['subtitle_type'] == "server_file") {
                                            $data[$i]['data'][$j]['subtitle_3'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['subtitle_3']);
                                        }
                                    }

                                    $data[$i]['data'][$j]['stop_time'] = GetStopTimeByUser($user_id, $data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['type_id'], $data[$i]['data'][$j]['video_type']);
                                    $data[$i]['data'][$j]['is_downloaded'] = Is_DownloadByUser($user_id, $data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['type_id'], $data[$i]['data'][$j]['video_type']);
                                    $data[$i]['data'][$j]['is_bookmark'] = Is_BookmarkByUser($user_id, $data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['type_id'], $data[$i]['data'][$j]['video_type']);
                                    $data[$i]['data'][$j]['rent_buy'] = VideoRentBuyByUser($user_id, $data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['type_id'], $data[$i]['data'][$j]['video_type']);
                                    $data[$i]['data'][$j]['is_rent'] = IsRentVideo($data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['video_type']);
                                    $data[$i]['data'][$j]['rent_price'] = GetPriceByRentVideo($data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['video_type']);
                                    $data[$i]['data'][$j]['is_buy'] = $isBuyByUser;
                                    $data[$i]['data'][$j]['category_name'] = GetCategoryNameByIds($data[$i]['data'][$j]['category_id']);
                                    $data[$i]['data'][$j]['session_id'] = "0";
                                    $data[$i]['data'][$j]['upcoming_type'] = 0;
                                }
                            } else {
                                $data[$i]['data'] = [];
                            }
                        } elseif ($data[$i]['video_type'] == 2) {

                            $tvshow_data = TVShow::whereIn('id', $Ids)->orderByRaw("FIELD(id, $videoIds)")->get();

                            if (count($tvshow_data) > 0) {
                                $data[$i]['data'] = $tvshow_data;
                                for ($j = 0; $j < count($tvshow_data); $j++) {

                                    // Thumbnail && Landscape
                                    $data[$i]['data'][$j]['thumbnail'] = Get_Image($this->folder_show, $data[$i]['data'][$j]['thumbnail']);
                                    $data[$i]['data'][$j]['landscape'] = Get_Image($this->folder_show, $data[$i]['data'][$j]['landscape']);
                                    // Trailer
                                    if (isset($data[$i]['data'][$j]['trailer_url']) && !empty($data[$i]['data'][$j]['trailer_url'])) {
                                        if ($data[$i]['data'][$j]['trailer_type'] == "server_video") {
                                            $data[$i]['data'][$j]['trailer_url'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['trailer_url']);
                                        }
                                    }

                                    $data[$i]['data'][$j]['stop_time'] = 0;
                                    $data[$i]['data'][$j]['is_downloaded'] = 0;
                                    $data[$i]['data'][$j]['is_bookmark'] = Is_BookmarkByUser($user_id, $data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['type_id'], $data[$i]['data'][$j]['video_type']);
                                    $data[$i]['data'][$j]['rent_buy'] = VideoRentBuyByUser($user_id, $data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['type_id'], $data[$i]['data'][$j]['video_type']);
                                    $data[$i]['data'][$j]['is_rent'] = IsRentVideo($data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['video_type']);
                                    $data[$i]['data'][$j]['rent_price'] = GetPriceByRentVideo($data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['video_type']);
                                    $data[$i]['data'][$j]['is_buy'] = $isBuyByUser;
                                    $data[$i]['data'][$j]['category_name'] = GetCategoryNameByIds($data[$i]['data'][$j]['category_id']);
                                    $data[$i]['data'][$j]['session_id'] = GetSessionByTVShowId($data[$i]['data'][$j]['id']);
                                    $data[$i]['data'][$j]['upcoming_type'] = 0;
                                }
                            } else {
                                $data[$i]['data'] = [];
                            }
                        } elseif ($data[$i]['video_type'] == 3) {

                            $lang_data = Language::whereIn('id', $Ids)->orderByRaw("FIELD(id, $videoIds)")->get();

                            if (count($lang_data) > 0) {

                                imageNameToUrl($lang_data, 'image', $this->folder_language);
                                $data[$i]['data'] = $lang_data;
                            } else {
                                $data[$i]['data'] = [];
                            }
                        } elseif ($data[$i]['video_type'] == 4) {

                            $category_data = Category::whereIn('id', $Ids)->orderByRaw("FIELD(id, $videoIds)")->get();

                            if (count($category_data) > 0) {

                                imageNameToUrl($category_data, 'image', $this->folder_category);
                                $data[$i]['data'] = $category_data;
                            } else {
                                $data[$i]['data'] = [];
                            }
                        } elseif ($data[$i]['video_type'] == 5) {

                            if ($data[$i]['upcoming_type'] == 1) {

                                $video_data = Video::whereIn('id', $Ids)->orderByRaw("FIELD(id, $videoIds)")->get();

                                if (count($video_data) > 0) {

                                    $data[$i]['data'] = $video_data;
                                    for ($j = 0; $j < count($video_data); $j++) {

                                        // Thumbnail && Landscape
                                        $data[$i]['data'][$j]['thumbnail'] = Get_Image($this->folder_video, $data[$i]['data'][$j]['thumbnail']);
                                        $data[$i]['data'][$j]['landscape'] = Get_Image($this->folder_video, $data[$i]['data'][$j]['landscape']);

                                        // Video (320, 480, 720, 1080)
                                        if (isset($data[$i]['data'][$j]['video_320']) && !empty($data[$i]['data'][$j]['video_320'])) {
                                            if ($data[$i]['data'][$j]['video_upload_type'] == "server_video") {
                                                $data[$i]['data'][$j]['video_320'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['video_320']);
                                            }
                                        }
                                        if (isset($data[$i]['data'][$j]['video_480']) && !empty($data[$i]['data'][$j]['video_480'])) {
                                            if ($data[$i]['data'][$j]['video_upload_type'] == "server_video") {
                                                $data[$i]['data'][$j]['video_480'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['video_480']);
                                            }
                                        }
                                        if (isset($data[$i]['data'][$j]['video_720']) && !empty($data[$i]['data'][$j]['video_720'])) {
                                            if ($data[$i]['data'][$j]['video_upload_type'] == "server_video") {
                                                $data[$i]['data'][$j]['video_720'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['video_720']);
                                            }
                                        }
                                        if (isset($data[$i]['data'][$j]['video_1080']) && !empty($data[$i]['data'][$j]['video_1080'])) {
                                            if ($data[$i]['data'][$j]['video_upload_type'] == "server_video") {
                                                $data[$i]['data'][$j]['video_1080'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['video_1080']);
                                            }
                                        }

                                        // Trailer
                                        if (isset($data[$i]['data'][$j]['trailer_url']) && !empty($data[$i]['data'][$j]['trailer_url'])) {
                                            if ($data[$i]['data'][$j]['trailer_type'] == "server_video") {
                                                $data[$i]['data'][$j]['trailer_url'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['trailer_url']);
                                            }
                                        }

                                        // SubTitle_1_2_3
                                        if (isset($data[$i]['data'][$j]['subtitle_1']) && !empty($data[$i]['data'][$j]['subtitle_1'])) {
                                            if ($data[$i]['data'][$j]['subtitle_type'] == "server_file") {
                                                $data[$i]['data'][$j]['subtitle_1'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['subtitle_1']);
                                            }
                                        }
                                        if (isset($data[$i]['data'][$j]['subtitle_2']) && !empty($data[$i]['data'][$j]['subtitle_2'])) {
                                            if ($data[$i]['data'][$j]['subtitle_type'] == "server_file") {
                                                $data[$i]['data'][$j]['subtitle_2'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['subtitle_2']);
                                            }
                                        }
                                        if (isset($data[$i]['data'][$j]['subtitle_3']) && !empty($data[$i]['data'][$j]['subtitle_3'])) {
                                            if ($data[$i]['data'][$j]['subtitle_type'] == "server_file") {
                                                $data[$i]['data'][$j]['subtitle_3'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['subtitle_3']);
                                            }
                                        }

                                        $data[$i]['data'][$j]['stop_time'] = GetStopTimeByUser($user_id, $data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['type_id'], $data[$i]['data'][$j]['video_type']);
                                        $data[$i]['data'][$j]['is_downloaded'] = Is_DownloadByUser($user_id, $data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['type_id'], $data[$i]['data'][$j]['video_type']);
                                        $data[$i]['data'][$j]['is_bookmark'] = Is_BookmarkByUser($user_id, $data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['type_id'], $data[$i]['data'][$j]['video_type']);
                                        $data[$i]['data'][$j]['rent_buy'] = VideoRentBuyByUser($user_id, $data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['type_id'], $data[$i]['data'][$j]['video_type']);
                                        $data[$i]['data'][$j]['is_rent'] = IsRentVideo($data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['video_type']);
                                        $data[$i]['data'][$j]['rent_price'] = GetPriceByRentVideo($data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['video_type']);
                                        $data[$i]['data'][$j]['is_buy'] = $isBuyByUser;
                                        $data[$i]['data'][$j]['category_name'] = GetCategoryNameByIds($data[$i]['data'][$j]['category_id']);
                                        $data[$i]['data'][$j]['session_id'] = "0";
                                        $data[$i]['data'][$j]['upcoming_type'] = 1;
                                    }
                                } else {
                                    $data[$i]['data'] = [];
                                }
                            } else if ($data[$i]['upcoming_type'] == 2) {

                                $tvshow_data = TVShow::whereIn('id', $Ids)->orderByRaw("FIELD(id, $videoIds)")->get();

                                if (count($tvshow_data) > 0) {
                                    $data[$i]['data'] = $tvshow_data;
                                    for ($j = 0; $j < count($tvshow_data); $j++) {

                                        // Thumbnail && Landscape
                                        $data[$i]['data'][$j]['thumbnail'] = Get_Image($this->folder_show, $data[$i]['data'][$j]['thumbnail']);
                                        $data[$i]['data'][$j]['landscape'] = Get_Image($this->folder_show, $data[$i]['data'][$j]['landscape']);
                                        // Trailer
                                        if (isset($data[$i]['data'][$j]['trailer_url']) && !empty($data[$i]['data'][$j]['trailer_url'])) {
                                            if ($data[$i]['data'][$j]['trailer_type'] == "server_video") {
                                                $data[$i]['data'][$j]['trailer_url'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['trailer_url']);
                                            }
                                        }

                                        $data[$i]['data'][$j]['stop_time'] = 0;
                                        $data[$i]['data'][$j]['is_downloaded'] = 0;
                                        $data[$i]['data'][$j]['is_bookmark'] = Is_BookmarkByUser($user_id, $data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['type_id'], $data[$i]['data'][$j]['video_type']);
                                        $data[$i]['data'][$j]['rent_buy'] = VideoRentBuyByUser($user_id, $data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['type_id'], $data[$i]['data'][$j]['video_type']);
                                        $data[$i]['data'][$j]['is_rent'] = IsRentVideo($data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['video_type']);
                                        $data[$i]['data'][$j]['rent_price'] = GetPriceByRentVideo($data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['video_type']);
                                        $data[$i]['data'][$j]['is_buy'] = $isBuyByUser;
                                        $data[$i]['data'][$j]['category_name'] = GetCategoryNameByIds($data[$i]['data'][$j]['category_id']);
                                        $data[$i]['data'][$j]['session_id'] = GetSessionByTVShowId($data[$i]['data'][$j]['id']);
                                        $data[$i]['data'][$j]['upcoming_type'] = 2;
                                    }
                                } else {
                                    $data[$i]['data'] = [];
                                }
                            }
                        }
                    }
                       
                    // Continue Watching
                    $continue = Video_Watch::where('user_id', $user_id)->where('status', '1')->latest()->get();
                    $continue_watching = array();
                    for ($i = 0; $i < count($continue); $i++) {

                        if ($continue[$i]['video_type'] == 1) {

                            $V_continue_video_data = Video::where('id', $continue[$i]['video_id'])->first();
                            if (!empty($V_continue_video_data)) {

                                // Thumbnail && Landscape
                                $V_continue_video_data['thumbnail'] = Get_Image($this->folder_video, $V_continue_video_data['thumbnail']);
                                $V_continue_video_data['landscape'] = Get_Image($this->folder_video, $V_continue_video_data['landscape']);

                                // Video (320, 480, 720, 1080)
                                if (isset($V_continue_video_data['video_320']) && !empty($V_continue_video_data['video_320'])) {
                                    if ($V_continue_video_data['video_upload_type'] == "server_video") {
                                        $V_continue_video_data['video_320'] = Get_Video($this->folder_video, $V_continue_video_data['video_320']);
                                    }
                                }
                                if (isset($V_continue_video_data['video_480']) && !empty($V_continue_video_data['video_480'])) {
                                    if ($V_continue_video_data['video_upload_type'] == "server_video") {
                                        $V_continue_video_data['video_480'] = Get_Video($this->folder_video, $V_continue_video_data['video_480']);
                                    }
                                }
                                if (isset($V_continue_video_data['video_720']) && !empty($V_continue_video_data['video_720'])) {
                                    if ($V_continue_video_data['video_upload_type'] == "server_video") {
                                        $V_continue_video_data['video_720'] = Get_Video($this->folder_video, $V_continue_video_data['video_720']);
                                    }
                                }
                                if (isset($V_continue_video_data['video_1080']) && !empty($V_continue_video_data['video_1080'])) {
                                    if ($V_continue_video_data['video_upload_type'] == "server_video") {
                                        $V_continue_video_data['video_1080'] = Get_Video($this->folder_video, $V_continue_video_data['video_1080']);
                                    }
                                }

                                // Trailer
                                if (isset($V_continue_video_data['trailer_url']) && !empty($V_continue_video_data['trailer_url'])) {
                                    if ($V_continue_video_data['trailer_type'] == "server_video") {
                                        $V_continue_video_data['trailer_url'] = Get_Video($this->folder_video, $V_continue_video_data['trailer_url']);
                                    }
                                }

                                // SubTitle_1_1_3
                                if (isset($V_continue_video_data['subtitle_1']) && !empty($V_continue_video_data['subtitle_1'])) {
                                    if ($V_continue_video_data['subtitle_type'] == "server_video") {
                                        $V_continue_video_data['subtitle_1'] = Get_Video($this->folder_video, $V_continue_video_data['subtitle_1']);
                                    }
                                }
                                if (isset($V_continue_video_data['subtitle_2']) && !empty($V_continue_video_data['subtitle_2'])) {
                                    if ($V_continue_video_data['subtitle_type'] == "server_video") {
                                        $V_continue_video_data['subtitle_2'] = Get_Video($this->folder_video, $V_continue_video_data['subtitle_2']);
                                    }
                                }
                                if (isset($V_continue_video_data['subtitle_3']) && !empty($V_continue_video_data['subtitle_3'])) {
                                    if ($V_continue_video_data['subtitle_type'] == "server_video") {
                                        $V_continue_video_data['subtitle_3'] = Get_Video($this->folder_video, $V_continue_video_data['subtitle_3']);
                                    }
                                }

                                $V_continue_video_data['stop_time'] = GetStopTimeByUser($user_id, $V_continue_video_data['id'], $V_continue_video_data['type_id'], $V_continue_video_data['video_type']);
                                $V_continue_video_data['is_downloaded'] = Is_DownloadByUser($user_id, $V_continue_video_data['id'], $V_continue_video_data['type_id'], $V_continue_video_data['video_type']);
                                $V_continue_video_data['is_bookmark'] = Is_BookmarkByUser($user_id, $V_continue_video_data['id'], $V_continue_video_data['type_id'], $V_continue_video_data['video_type']);
                                $V_continue_video_data['rent_buy'] = VideoRentBuyByUser($user_id, $V_continue_video_data['id'], $V_continue_video_data['type_id'], $V_continue_video_data['video_type']);
                                $V_continue_video_data['is_rent'] = IsRentVideo($V_continue_video_data['id'], $V_continue_video_data['video_type']);
                                $V_continue_video_data['rent_price'] = GetPriceByRentVideo($V_continue_video_data['id'], $V_continue_video_data['video_type']);
                                $V_continue_video_data['is_buy'] = $isBuyByUser;
                                $V_continue_video_data['category_name'] = GetCategoryNameByIds($V_continue_video_data['category_id']);
                                $V_continue_video_data['session_id'] = 0;
                                $V_continue_video_data['show_id'] = 0;
                                $V_continue_video_data['upcoming_type'] = 0;

                                $continue_watching[$i] = $V_continue_video_data;
                            }
                        } elseif ($continue[$i]['video_type'] == 2) {

                            $V_continue_episode_data = TVShowVideo::where('id', $continue[$i]['video_id'])->with('show')->first();
                            if (!empty($V_continue_episode_data)) {

                                // Thumbnail && Landscape
                                $V_continue_episode_data['thumbnail'] = Get_Image($this->folder_show, $V_continue_episode_data['thumbnail']);
                                $V_continue_episode_data['landscape'] = Get_Image($this->folder_show, $V_continue_episode_data['landscape']);

                                // Video (320, 480, 720, 1080)
                                if (isset($V_continue_episode_data['video_320']) && !empty($V_continue_episode_data['video_320'])) {
                                    if ($V_continue_episode_data['video_upload_type'] == "server_video") {
                                        $V_continue_episode_data['video_320'] = Get_Video($this->folder_video, $V_continue_episode_data['video_320']);
                                    }
                                }
                                if (isset($V_continue_episode_data['video_480']) && !empty($V_continue_episode_data['video_480'])) {
                                    if ($V_continue_episode_data['video_upload_type'] == "server_video") {
                                        $V_continue_episode_data['video_480'] = Get_Video($this->folder_video, $V_continue_episode_data['video_480']);
                                    }
                                }
                                if (isset($V_continue_episode_data['video_720']) && !empty($V_continue_episode_data['video_720'])) {
                                    if ($V_continue_episode_data['video_upload_type'] == "server_video") {
                                        $V_continue_episode_data['video_720'] = Get_Video($this->folder_video, $V_continue_episode_data['video_720']);
                                    }
                                }
                                if (isset($V_continue_episode_data['video_1080']) && !empty($V_continue_episode_data['video_1080'])) {
                                    if ($V_continue_episode_data['video_upload_type'] == "server_video") {
                                        $V_continue_episode_data['video_1080'] = Get_Video($this->folder_video, $V_continue_episode_data['video_1080']);
                                    }
                                }

                                // SubTitle_1_1_3
                                if (isset($V_continue_episode_data['subtitle_1']) && !empty($V_continue_episode_data['subtitle_1'])) {
                                    if ($V_continue_episode_data['subtitle_type'] == "server_video") {
                                        $V_continue_episode_data['subtitle_1'] = Get_Video($this->folder_video, $V_continue_episode_data['subtitle_1']);
                                    }
                                }
                                if (isset($V_continue_episode_data['subtitle_2']) && !empty($V_continue_episode_data['subtitle_2'])) {
                                    if ($V_continue_episode_data['subtitle_type'] == "server_video") {
                                        $V_continue_episode_data['subtitle_2'] = Get_Video($this->folder_video, $V_continue_episode_data['subtitle_2']);
                                    }
                                }
                                if (isset($V_continue_episode_data['subtitle_3']) && !empty($V_continue_episode_data['subtitle_3'])) {
                                    if ($V_continue_episode_data['subtitle_type'] == "server_video") {
                                        $V_continue_episode_data['subtitle_3'] = Get_Video($this->folder_video, $V_continue_episode_data['subtitle_3']);
                                    }
                                }

                                $V_continue_episode_data['stop_time'] = (int) $continue[$i]['stop_time'];
                                $V_continue_episode_data['is_buy'] = $isBuyByUser;
                                $V_continue_video_data['upcoming_type'] = 0;

                                $V_continue_episode_data['is_downloaded'] = 0;
                                $V_continue_episode_data['is_bookmark'] = 0;
                                $V_continue_episode_data['rent_buy'] = 0;
                                $V_continue_episode_data['is_rent'] = 0;
                                $V_continue_episode_data['rent_price'] = 0;
                                $V_continue_episode_data['language_id'] = "";
                                $V_continue_episode_data['channel_id'] = "";
                                $V_continue_episode_data['category_id'] = "";
                                $V_continue_episode_data['category_name'] = "";
                                $V_continue_episode_data['name'] = "";
                                $V_continue_episode_data['type_id'] = 0;
                                $V_continue_episode_data['video_type'] = 0;
                                if ($V_continue_episode_data['show'] != null) {
                                    $V_continue_episode_data['is_downloaded'] = Is_DownloadByUser($user_id, $V_continue_episode_data['session_id'], $V_continue_episode_data['show']['type_id'], $V_continue_episode_data['video_type'], $V_continue_episode_data['show_id']);
                                    $V_continue_episode_data['is_bookmark'] = Is_BookmarkByUser($user_id, $V_continue_episode_data['show_id'], $V_continue_episode_data['show']['type_id'], $V_continue_episode_data['video_type']);
                                    $V_continue_episode_data['rent_buy'] = VideoRentBuyByUser($user_id, $V_continue_episode_data['show_id'], $V_continue_episode_data['show']['type_id'], $V_continue_episode_data['video_type']);
                                    $V_continue_episode_data['is_rent'] = IsRentVideo($V_continue_episode_data['show_id'], $V_continue_episode_data['video_type']);
                                    $V_continue_episode_data['rent_price'] = GetPriceByRentVideo($V_continue_episode_data['show_id'], $V_continue_episode_data['video_type']);
                                    $V_continue_episode_data['language_id'] = (string) $V_continue_episode_data['show']['language_id'];
                                    $V_continue_episode_data['channel_id'] = $V_continue_episode_data['show']['channel_id'];
                                    $V_continue_episode_data['category_id'] = (string) $V_continue_episode_data['show']['category_id'];
                                    $V_continue_episode_data['category_name'] = GetCategoryNameByIds($V_continue_episode_data['show']['category_id']);
                                    $V_continue_episode_data['name'] = $V_continue_episode_data['show']['name'];
                                    $V_continue_episode_data['type_id'] = (int) $V_continue_episode_data['show']['type_id'];
                                    $V_continue_episode_data['video_type'] = (int) $V_continue_episode_data['show']['video_type'];
                                }

                                unset($V_continue_episode_data['show']);
                                $continue_watching[$i] = $V_continue_episode_data;
                            }
                        }
                    }

                    $reset = array_values($continue_watching);

                    $return['status'] = 200;
                    $return['message'] = __('api_msg.get_record_successfully');
                    $return['result'] = $data;
                    $return['continue_watching'] = $reset;
                    return $return;
                } else {
                    return APIResponse(400, __('api_msg.data_not_found'));
                }
            } elseif ($is_home_page == "2") {
    
                $data = App_Section::where('is_home_screen', '2')->where('type_id', $type_id)->orderBy('id', 'desc')->get();
               
                if (count($data) > 0) {
                    
                    for ($i = 0; $i < count($data); $i++) {
 
                        $data[$i]['data'] = [];
                        $Ids = explode(',', $data[$i]['video_id']);
                        
						$videoIds = $data[$i]['video_id'];
                      
                        if ($data[$i]['video_type'] == 0) { 
                            $video_data = WebSeries::select(
                                            'id',
                                            'title as name',
                                            'slug',
                                            'description',
                                            'thumbnail',
                                            'video'
                                        )
                                        ->whereIn('id', $Ids)
                                        ->orderByRaw("FIELD(id, $videoIds)")->get();

                            //$video_data = Video::whereIn('id', $Ids)->orderByRaw("FIELD(id, $videoIds)")->get();
                               
                            if (count($video_data) > 0) {

                                $data[$i]['data'] = $video_data;
                                for ($j = 0; $j < count($video_data); $j++) {
                                       
                                    // Thumbnail && Landscape
                                    $data[$i]['data'][$j]['thumbnail'] = Get_Image($this->folder_video, $data[$i]['data'][$j]['thumbnail']);
                                    $data[$i]['data'][$j]['landscape'] = Get_Image($this->folder_video, $data[$i]['data'][$j]['thumbnail']);

                                    // Video (320, 480, 720, 1080)
                                    if (isset($data[$i]['data'][$j]['video_320']) && !empty($data[$i]['data'][$j]['video_320'])) {
                                        if ($data[$i]['data'][$j]['video_upload_type'] == "server_video") {
                                            $data[$i]['data'][$j]['video_320'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['video_320']);
                                        }
                                    }
                                    if (isset($data[$i]['data'][$j]['video_480']) && !empty($data[$i]['data'][$j]['video_480'])) {
                                        if ($data[$i]['data'][$j]['video_upload_type'] == "server_video") {
                                            $data[$i]['data'][$j]['video_480'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['video_480']);
                                        }
                                    }
                                    if (isset($data[$i]['data'][$j]['video_720']) && !empty($data[$i]['data'][$j]['video_720'])) {
                                        if ($data[$i]['data'][$j]['video_upload_type'] == "server_video") {
                                            $data[$i]['data'][$j]['video_720'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['video_720']);
                                        }
                                    }
                                    if (isset($data[$i]['data'][$j]['video_1080']) && !empty($data[$i]['data'][$j]['video_1080'])) {
                                        if ($data[$i]['data'][$j]['video_upload_type'] == "server_video") {
                                            $data[$i]['data'][$j]['video_1080'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['video_1080']);
                                        }
                                    }

                                    // Trailer
                                    if (isset($data[$i]['data'][$j]['trailer_url']) && !empty($data[$i]['data'][$j]['trailer_url'])) {
                                        if ($data[$i]['data'][$j]['trailer_type'] == "server_video") {
                                            $data[$i]['data'][$j]['trailer_url'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['trailer_url']);
                                        }
                                    }

                                    // SubTitle_1_2_3
                                    if (isset($data[$i]['data'][$j]['subtitle_1']) && !empty($data[$i]['data'][$j]['subtitle_1'])) {
                                        if ($data[$i]['data'][$j]['subtitle_type'] == "server_file") {
                                            $data[$i]['data'][$j]['subtitle_1'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['subtitle_1']);
                                        }
                                    }
                                    if (isset($data[$i]['data'][$j]['subtitle_2']) && !empty($data[$i]['data'][$j]['subtitle_2'])) {
                                        if ($data[$i]['data'][$j]['subtitle_type'] == "server_file") {
                                            $data[$i]['data'][$j]['subtitle_2'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['subtitle_2']);
                                        }
                                    }
                                    if (isset($data[$i]['data'][$j]['subtitle_3']) && !empty($data[$i]['data'][$j]['subtitle_3'])) {
                                        if ($data[$i]['data'][$j]['subtitle_type'] == "server_file") {
                                            $data[$i]['data'][$j]['subtitle_3'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['subtitle_3']);
                                        }
                                    }

                                    $data[$i]['data'][$j]['stop_time'] = GetStopTimeByUser($user_id, $data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['type_id'], $data[$i]['data'][$j]['video_type']);
                                    $data[$i]['data'][$j]['is_downloaded'] = Is_DownloadByUser($user_id, $data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['type_id'], $data[$i]['data'][$j]['video_type']);
                                    $data[$i]['data'][$j]['is_bookmark'] = Is_BookmarkByUser($user_id, $data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['type_id'], $data[$i]['data'][$j]['video_type']);
                                    $data[$i]['data'][$j]['rent_buy'] = VideoRentBuyByUser($user_id, $data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['type_id'], $data[$i]['data'][$j]['video_type']);
                                    $data[$i]['data'][$j]['is_rent'] = IsRentVideo($data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['video_type']);
                                    $data[$i]['data'][$j]['rent_price'] = GetPriceByRentVideo($data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['video_type']);
                                    $data[$i]['data'][$j]['is_buy'] = $isBuyByUser;
                                    $data[$i]['data'][$j]['category_name'] = GetCategoryNameByIds($data[$i]['data'][$j]['category_id']);
                                    $data[$i]['data'][$j]['session_id'] = "0";
                                    $data[$i]['data'][$j]['upcoming_type'] = 0;
                                    
                                }
                            } else {
                                $data[$i]['data'] = [];
                            }
                        } elseif ($data[$i]['video_type'] == 1) {

                            $video_data = Video::whereIn('id', $Ids)->orderByRaw("FIELD(id, $videoIds)")->get();

                            if (count($video_data) > 0) {

                                $data[$i]['data'] = $video_data;
                                for ($j = 0; $j < count($video_data); $j++) {

                                    // Thumbnail && Landscape
                                    $data[$i]['data'][$j]['thumbnail'] = Get_Image($this->folder_video, $data[$i]['data'][$j]['thumbnail']);
                                    $data[$i]['data'][$j]['landscape'] = Get_Image($this->folder_video, $data[$i]['data'][$j]['landscape']);

                                    // Video (320, 480, 720, 1080)
                                    if (isset($data[$i]['data'][$j]['video_320']) && !empty($data[$i]['data'][$j]['video_320'])) {
                                        if ($data[$i]['data'][$j]['video_upload_type'] == "server_video") {
                                            $data[$i]['data'][$j]['video_320'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['video_320']);
                                        }
                                    }
                                    if (isset($data[$i]['data'][$j]['video_480']) && !empty($data[$i]['data'][$j]['video_480'])) {
                                        if ($data[$i]['data'][$j]['video_upload_type'] == "server_video") {
                                            $data[$i]['data'][$j]['video_480'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['video_480']);
                                        }
                                    }
                                    if (isset($data[$i]['data'][$j]['video_720']) && !empty($data[$i]['data'][$j]['video_720'])) {
                                        if ($data[$i]['data'][$j]['video_upload_type'] == "server_video") {
                                            $data[$i]['data'][$j]['video_720'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['video_720']);
                                        }
                                    }
                                    if (isset($data[$i]['data'][$j]['video_1080']) && !empty($data[$i]['data'][$j]['video_1080'])) {
                                        if ($data[$i]['data'][$j]['video_upload_type'] == "server_video") {
                                            $data[$i]['data'][$j]['video_1080'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['video_1080']);
                                        }
                                    }

                                    // Trailer
                                    if (isset($data[$i]['data'][$j]['trailer_url']) && !empty($data[$i]['data'][$j]['trailer_url'])) {
                                        if ($data[$i]['data'][$j]['trailer_type'] == "server_video") {
                                            $data[$i]['data'][$j]['trailer_url'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['trailer_url']);
                                        }
                                    }

                                    // SubTitle_1_2_3
                                    if (isset($data[$i]['data'][$j]['subtitle_1']) && !empty($data[$i]['data'][$j]['subtitle_1'])) {
                                        if ($data[$i]['data'][$j]['subtitle_type'] == "server_video") {
                                            $data[$i]['data'][$j]['subtitle_1'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['subtitle_1']);
                                        }
                                    }
                                    if (isset($data[$i]['data'][$j]['subtitle_2']) && !empty($data[$i]['data'][$j]['subtitle_2'])) {
                                        if ($data[$i]['data'][$j]['subtitle_type'] == "server_video") {
                                            $data[$i]['data'][$j]['subtitle_2'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['subtitle_2']);
                                        }
                                    }
                                    if (isset($data[$i]['data'][$j]['subtitle_3']) && !empty($data[$i]['data'][$j]['subtitle_3'])) {
                                        if ($data[$i]['data'][$j]['subtitle_type'] == "server_video") {
                                            $data[$i]['data'][$j]['subtitle_3'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['subtitle_3']);
                                        }
                                    }

                                    $data[$i]['data'][$j]['stop_time'] = GetStopTimeByUser($user_id, $data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['type_id'], $data[$i]['data'][$j]['video_type']);
                                    $data[$i]['data'][$j]['is_downloaded'] = Is_DownloadByUser($user_id, $data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['type_id'], $data[$i]['data'][$j]['video_type']);
                                    $data[$i]['data'][$j]['is_bookmark'] = Is_BookmarkByUser($user_id, $data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['type_id'], $data[$i]['data'][$j]['video_type']);
                                    $data[$i]['data'][$j]['rent_buy'] = VideoRentBuyByUser($user_id, $data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['type_id'], $data[$i]['data'][$j]['video_type']);
                                    $data[$i]['data'][$j]['is_rent'] = IsRentVideo($data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['video_type']);
                                    $data[$i]['data'][$j]['rent_price'] = GetPriceByRentVideo($data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['video_type']);
                                    $data[$i]['data'][$j]['is_buy'] = $isBuyByUser;
                                    $data[$i]['data'][$j]['category_name'] = GetCategoryNameByIds($data[$i]['data'][$j]['category_id']);
                                    $data[$i]['data'][$j]['session_id'] = "0";
                                    $data[$i]['data'][$j]['upcoming_type'] = 0;
                                }
                            } else {
                                $data[$i]['data'] = [];
                            }
                        } elseif ($data[$i]['video_type'] == 2) {

                            $tvshow_data = TVShow::whereIn('id', $Ids)->orderByRaw("FIELD(id, $videoIds)")->get();

                            if (count($tvshow_data) > 0) {
                                $data[$i]['data'] = $tvshow_data;
                                for ($j = 0; $j < count($tvshow_data); $j++) {

                                    // Thumbnail && Landscape
                                    $data[$i]['data'][$j]['thumbnail'] = Get_Image($this->folder_show, $data[$i]['data'][$j]['thumbnail']);
                                    $data[$i]['data'][$j]['landscape'] = Get_Image($this->folder_show, $data[$i]['data'][$j]['landscape']);

                                    $data[$i]['data'][$j]['stop_time'] = 0;
                                    $data[$i]['data'][$j]['is_downloaded'] = 0;
                                    $data[$i]['data'][$j]['is_bookmark'] = Is_BookmarkByUser($user_id, $data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['type_id'], $data[$i]['data'][$j]['video_type']);
                                    $data[$i]['data'][$j]['rent_buy'] = VideoRentBuyByUser($user_id, $data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['type_id'], $data[$i]['data'][$j]['video_type']);
                                    $data[$i]['data'][$j]['is_rent'] = IsRentVideo($data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['video_type']);
                                    $data[$i]['data'][$j]['rent_price'] = GetPriceByRentVideo($data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['video_type']);
                                    $data[$i]['data'][$j]['is_buy'] = $isBuyByUser;
                                    $data[$i]['data'][$j]['category_name'] = GetCategoryNameByIds($data[$i]['data'][$j]['category_id']);
                                    $data[$i]['data'][$j]['session_id'] = GetSessionByTVShowId($data[$i]['data'][$j]['id']);
                                    $data[$i]['data'][$j]['upcoming_type'] = 0;
                                }
                            } else {
                                $data[$i]['data'] = [];
                            }
                        } elseif ($data[$i]['video_type'] == 3) {

                            $lang_data = Language::whereIn('id', $Ids)->orderByRaw("FIELD(id, $videoIds)")->get();

                            if (count($lang_data) > 0) {

                                imageNameToUrl($lang_data, 'image', $this->folder_language);
                                $data[$i]['data'] = $lang_data;
                            } else {
                                $data[$i]['data'] = [];
                            }
                        } elseif ($data[$i]['video_type'] == 4) {

                            $category_data = Category::whereIn('id', $Ids)->orderByRaw("FIELD(id, $videoIds)")->get();

                            if (count($category_data) > 0) {

                                imageNameToUrl($category_data, 'image', $this->folder_category);
                                $data[$i]['data'] = $category_data;
                            } else {
                                $data[$i]['data'] = [];
                            }
                        } elseif ($data[$i]['video_type'] == 5) {

                            if ($data[$i]['upcoming_type'] == 1) {

                                $video_data = Video::whereIn('id', $Ids)->orderByRaw("FIELD(id, $videoIds)")->get();

                                if (count($video_data) > 0) {

                                    $data[$i]['data'] = $video_data;
                                    for ($j = 0; $j < count($video_data); $j++) {

                                        // Thumbnail && Landscape
                                        $data[$i]['data'][$j]['thumbnail'] = Get_Image($this->folder_video, $data[$i]['data'][$j]['thumbnail']);
                                        $data[$i]['data'][$j]['landscape'] = Get_Image($this->folder_video, $data[$i]['data'][$j]['landscape']);

                                        // Video (320, 480, 720, 1080)
                                        if (isset($data[$i]['data'][$j]['video_320']) && !empty($data[$i]['data'][$j]['video_320'])) {
                                            if ($data[$i]['data'][$j]['video_upload_type'] == "server_video") {
                                                $data[$i]['data'][$j]['video_320'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['video_320']);
                                            }
                                        }
                                        if (isset($data[$i]['data'][$j]['video_480']) && !empty($data[$i]['data'][$j]['video_480'])) {
                                            if ($data[$i]['data'][$j]['video_upload_type'] == "server_video") {
                                                $data[$i]['data'][$j]['video_480'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['video_480']);
                                            }
                                        }
                                        if (isset($data[$i]['data'][$j]['video_720']) && !empty($data[$i]['data'][$j]['video_720'])) {
                                            if ($data[$i]['data'][$j]['video_upload_type'] == "server_video") {
                                                $data[$i]['data'][$j]['video_720'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['video_720']);
                                            }
                                        }
                                        if (isset($data[$i]['data'][$j]['video_1080']) && !empty($data[$i]['data'][$j]['video_1080'])) {
                                            if ($data[$i]['data'][$j]['video_upload_type'] == "server_video") {
                                                $data[$i]['data'][$j]['video_1080'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['video_1080']);
                                            }
                                        }

                                        // Trailer
                                        if (isset($data[$i]['data'][$j]['trailer_url']) && !empty($data[$i]['data'][$j]['trailer_url'])) {
                                            if ($data[$i]['data'][$j]['trailer_type'] == "server_video") {
                                                $data[$i]['data'][$j]['trailer_url'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['trailer_url']);
                                            }
                                        }

                                        // SubTitle_1_2_3
                                        if (isset($data[$i]['data'][$j]['subtitle_1']) && !empty($data[$i]['data'][$j]['subtitle_1'])) {
                                            if ($data[$i]['data'][$j]['subtitle_type'] == "server_video") {
                                                $data[$i]['data'][$j]['subtitle_1'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['subtitle_1']);
                                            }
                                        }
                                        if (isset($data[$i]['data'][$j]['subtitle_2']) && !empty($data[$i]['data'][$j]['subtitle_2'])) {
                                            if ($data[$i]['data'][$j]['subtitle_type'] == "server_video") {
                                                $data[$i]['data'][$j]['subtitle_2'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['subtitle_2']);
                                            }
                                        }
                                        if (isset($data[$i]['data'][$j]['subtitle_3']) && !empty($data[$i]['data'][$j]['subtitle_3'])) {
                                            if ($data[$i]['data'][$j]['subtitle_type'] == "server_video") {
                                                $data[$i]['data'][$j]['subtitle_3'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['subtitle_3']);
                                            }
                                        }

                                        $data[$i]['data'][$j]['stop_time'] = GetStopTimeByUser($user_id, $data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['type_id'], $data[$i]['data'][$j]['video_type']);
                                        $data[$i]['data'][$j]['is_downloaded'] = Is_DownloadByUser($user_id, $data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['type_id'], $data[$i]['data'][$j]['video_type']);
                                        $data[$i]['data'][$j]['is_bookmark'] = Is_BookmarkByUser($user_id, $data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['type_id'], $data[$i]['data'][$j]['video_type']);
                                        $data[$i]['data'][$j]['rent_buy'] = VideoRentBuyByUser($user_id, $data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['type_id'], $data[$i]['data'][$j]['video_type']);
                                        $data[$i]['data'][$j]['is_rent'] = IsRentVideo($data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['video_type']);
                                        $data[$i]['data'][$j]['rent_price'] = GetPriceByRentVideo($data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['video_type']);
                                        $data[$i]['data'][$j]['is_buy'] = $isBuyByUser;
                                        $data[$i]['data'][$j]['category_name'] = GetCategoryNameByIds($data[$i]['data'][$j]['category_id']);
                                        $data[$i]['data'][$j]['session_id'] = "0";
                                        $data[$i]['data'][$j]['upcoming_type'] = 1;
                                    }
                                } else {
                                    $data[$i]['data'] = [];
                                }
                            } else if ($data[$i]['upcoming_type'] == 2) {

                                $tvshow_data = TVShow::whereIn('id', $Ids)->orderByRaw("FIELD(id, $videoIds)")->get();

                                if (count($tvshow_data) > 0) {
                                    $data[$i]['data'] = $tvshow_data;
                                    for ($j = 0; $j < count($tvshow_data); $j++) {

                                        // Thumbnail && Landscape
                                        $data[$i]['data'][$j]['thumbnail'] = Get_Image($this->folder_show, $data[$i]['data'][$j]['thumbnail']);
                                        $data[$i]['data'][$j]['landscape'] = Get_Image($this->folder_show, $data[$i]['data'][$j]['landscape']);

                                        $data[$i]['data'][$j]['stop_time'] = 0;
                                        $data[$i]['data'][$j]['is_downloaded'] = 0;
                                        $data[$i]['data'][$j]['is_bookmark'] = Is_BookmarkByUser($user_id, $data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['type_id'], $data[$i]['data'][$j]['video_type']);
                                        $data[$i]['data'][$j]['rent_buy'] = VideoRentBuyByUser($user_id, $data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['type_id'], $data[$i]['data'][$j]['video_type']);
                                        $data[$i]['data'][$j]['is_rent'] = IsRentVideo($data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['video_type']);
                                        $data[$i]['data'][$j]['rent_price'] = GetPriceByRentVideo($data[$i]['data'][$j]['id'], $data[$i]['data'][$j]['video_type']);
                                        $data[$i]['data'][$j]['is_buy'] = $isBuyByUser;
                                        $data[$i]['data'][$j]['category_name'] = GetCategoryNameByIds($data[$i]['data'][$j]['category_id']);
                                        $data[$i]['data'][$j]['session_id'] = GetSessionByTVShowId($data[$i]['data'][$j]['id']);
                                        $data[$i]['data'][$j]['upcoming_type'] = 2;
                                    }
                                } else {
                                    $data[$i]['data'] = [];
                                }
                            }
                        }
                    }

                    $return['status'] = 200;
                    $return['message'] = __('api_msg.get_record_successfully');
                    $return['result'] = $data;
                    $return['continue_watching'] = [];
                    return $return;
                } else {
                    return APIResponse(400, __('api_msg.data_not_found'));
                }
            } else {
                return APIResponse(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
	
    public function section_detail(Request $request)
    {
        try {
            $validation = Validator::make(
                $request->all(),
                [
                    'type_id' => 'required|numeric',
                    'video_type' => 'required|numeric',
                    'video_id' => 'required|numeric',
                ],
                [
                    'type_id.required' => __('api_msg.please_enter_required_fields'),
                    'video_type.required' => __('api_msg.please_enter_required_fields'),
                    'video_id.required' => __('api_msg.please_enter_required_fields'),
                ]
            );
            if ($validation->fails()) {

                $errors = $validation->errors()->first('type_id');
                $errors1 = $validation->errors()->first('video_type');
                $errors2 = $validation->errors()->first('video_id');
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
			$user_id = isset($request->user_id) ? $request->user_id : 0;
			$IsBuyByUserData = Transction::select('id')->where('user_id', $user_id)->where('status', '1')->first();
			if (!empty($IsBuyByUserData)) {
				$isBuyByUser = 1;
			} else {
				$isBuyByUser = 1; //temp expiry
			}
            $type_id = $request->type_id;
            $video_type = $request->video_type;
            $video_id = $request->video_id;
            $upcoming_type = isset($request->upcoming_type) ? $request->upcoming_type : 0;

            if ($video_type == 1) {

                $data['status'] = 200;
                $data['message'] = __('api_msg.get_record_successfully');

                $data['result'] = Video::where('id', $video_id)->where('video_type', $video_type)->first();

              
                if (!empty($data['result'])) {

                    // Thumbnail && Landscape
                    $data['result']['thumbnail'] = Get_Image($this->folder_video, $data['result']['thumbnail']);
                    $data['result']['landscape'] = Get_Image($this->folder_video, $data['result']['landscape']);

                    // Video (320, 480, 720, 1080)
                    if (isset($data['result']['video_320']) && !empty($data['result']['video_320'])) {
                        if ($data['result']['video_upload_type'] == "server_video") {
                            $data['result']['video_320'] = Get_Video($this->folder_video, $data['result']['video_320']);
                        }
                    }
                    if (isset($data['result']['video_480']) && !empty($data['result']['video_480'])) {
                        if ($data['result']['video_upload_type'] == "server_video") {
                            $data['result']['video_480'] = Get_Video($this->folder_video, $data['result']['video_480']);
                        }
                    }
                    if (isset($data['result']['video_720']) && !empty($data['result']['video_720'])) {
                        if ($data['result']['video_upload_type'] == "server_video") {
                            $data['result']['video_720'] = Get_Video($this->folder_video, $data['result']['video_720']);
                        }
                    }
                    if (isset($data['result']['video_1080']) && !empty($data['result']['video_1080'])) {
                        if ($data['result']['video_upload_type'] == "server_video") {
                            $data['result']['video_1080'] = Get_Video($this->folder_video, $data['result']['video_1080']);
                        }
                    }

                    // Trailer
                    if (isset($data['result']['trailer_url']) && !empty($data['result']['trailer_url'])) {
                        if ($data['result']['trailer_type'] == "server_video") {
                            $data['result']['trailer_url'] = Get_Video($this->folder_video, $data['result']['trailer_url']);
                        }
                    }

                    // SubTitle_1_2_3
                    if (isset($data['result']['subtitle_1']) && !empty($data['result']['subtitle_1'])) {
                        if ($data['result']['subtitle_type'] == "server_video") {
                            $data['result']['subtitle_1'] = Get_Video($this->folder_video, $data['result']['subtitle_1']);
                        }
                    }
                    if (isset($data['result']['subtitle_2']) && !empty($data['result']['subtitle_2'])) {
                        if ($data['result']['subtitle_type'] == "server_video") {
                            $data['result']['subtitle_2'] = Get_Video($this->folder_video, $data['result']['subtitle_2']);
                        }
                    }
                    if (isset($data['result']['subtitle_3']) && !empty($data['result']['subtitle_3'])) {
                        if ($data['result']['subtitle_type'] == "server_video") {
                            $data['result']['subtitle_3'] = Get_Video($this->folder_video, $data['result']['subtitle_3']);
                        }
                    }

                    $data['result']['stop_time'] = GetStopTimeByUser($user_id, $data['result']['id'], $data['result']['type_id'], $data['result']['video_type']);
                    $data['result']['is_downloaded'] = Is_DownloadByUser($user_id, $data['result']['id'], $data['result']['type_id'], $data['result']['video_type']);
                    $data['result']['is_bookmark'] = Is_BookmarkByUser($user_id, $data['result']['id'], $data['result']['type_id'], $data['result']['video_type']);
                    $data['result']['rent_buy'] = VideoRentBuyByUser($user_id, $data['result']['id'], $data['result']['type_id'], $data['result']['video_type']);
                    $data['result']['is_rent'] = IsRentVideo($data['result']['id'], $data['result']['video_type']);
                    $data['result']['rent_price'] = GetPriceByRentVideo($data['result']['id'], $data['result']['video_type']);
                    $data['result']['is_buy'] = $isBuyByUser;
                    $data['result']['category_name'] = GetCategoryNameByIds($data['result']['category_id']);
                    $data['result']['session_id'] = "0";
                    $data['result']['upcoming_type'] = 0;

                    $data['cast'] = array();
                    $data['session'] = array();
                    $data['get_related_video'] = array();
                    $data['language'] = array();
                    $data['more_details'] = array();

                    // Cast
                    $Cast_Ids = explode(',', $data['result']['cast_id']);
                    $data['cast'] = Cast::whereIn('id', $Cast_Ids)->get();
                    imageNameToUrl($data['cast'], 'image', $this->folder_cast);

                    // Language
                    $Language_Ids = explode(',', $data['result']['language_id']);
                    $data['language'] = Language::whereIn('id', $Language_Ids)->get();
                    imageNameToUrl($data['language'], 'image', $this->folder_language);
					//Get Related Video
					$relatedData = [];
					if(!empty($data['result']['related_videos'])) {
						$related_videos = Video::whereIn('id', explode(',', $data['result']['related_videos']))->get()->toArray();
						if(!empty($related_videos)) {
							foreach ($related_videos as $key => $value) {
								// Thumbnail && Landscape
								$value['thumbnail'] = Get_Image($this->folder_video, $value['thumbnail']);
								$value['landscape'] = Get_Image($this->folder_video, $value['landscape']);

								// Video (320, 480, 720, 1080)
								if (isset($value['video_320']) && !empty($value['video_320'])) {
									if ($value['video_upload_type'] == "server_video") {
										$value['video_320'] = Get_Video($this->folder_video, $value['video_320']);
									}
								}
								if (isset($value['video_480']) && !empty($value['video_480'])) {
									if ($value['video_upload_type'] == "server_video") {
										$value['video_480'] = Get_Video($this->folder_video, $value['video_480']);
									}
								}
								if (isset($value['video_720']) && !empty($value['video_720'])) {
									if ($value['video_upload_type'] == "server_video") {
										$value['video_720'] = Get_Video($this->folder_video, $value['video_720']);
									}
								}
								if (isset($value['video_1080']) && !empty($value['video_1080'])) {
									if ($value['video_upload_type'] == "server_video") {
										$value['video_1080'] = Get_Video($this->folder_video, $value['video_1080']);
									}
								}

								// Trailer
								if (isset($value['trailer_url']) && !empty($value['trailer_url'])) {
									if ($value['trailer_type'] == "server_video") {
										$value['trailer_url'] = Get_Video($this->folder_video, $value['trailer_url']);
									}
								}

								// SubTitle_1_2_3
								if (isset($value['subtitle_1']) && !empty($value['subtitle_1'])) {
									if ($value['subtitle_type'] == "server_video") {
										$value['subtitle_1'] = Get_Video($this->folder_video, $value['subtitle_1']);
									}
								}
								if (isset($value['subtitle_2']) && !empty($value['subtitle_2'])) {
									if ($value['subtitle_type'] == "server_video") {
										$value['subtitle_2'] = Get_Video($this->folder_video, $value['subtitle_2']);
									}
								}
								if (isset($value['subtitle_3']) && !empty($value['subtitle_3'])) {
									if ($value['subtitle_type'] == "server_video") {
										$value['subtitle_3'] = Get_Video($this->folder_video, $value['subtitle_3']);
									}
								}

								$value['stop_time'] = GetStopTimeByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
								$value['is_downloaded'] = Is_DownloadByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
								$value['is_bookmark'] = Is_BookmarkByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
								$value['rent_buy'] = VideoRentBuyByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
								$value['is_rent'] = IsRentVideo($value['id'], $value['video_type']);
								$value['rent_price'] = GetPriceByRentVideo($value['id'], $value['video_type']);
								$value['is_buy'] = $isBuyByUser;
								$value['category_name'] = GetCategoryNameByIds($value['category_id']);
								$value['session_id'] = "0";
								$value['upcoming_type'] = 0;
								$relatedData[] = $value;
							}
						}
					}

                    
                    /*$Category_Ids = explode(',', $data['result']['category_id']);
                    $All_Video = Video::where('id', '!=', $data['result']['id'])->where('video_type', $video_type)->latest()->get();

                    $RelatedData = [];
                    foreach ($All_Video as $key => $value) {

                        $C_Ids = explode(',', $value['category_id']);
                        foreach ($C_Ids as $key1 => $value1) {

                            if (in_array($value1, $Category_Ids)) {

                                // Thumbnail && Landscape
                                $value['thumbnail'] = Get_Image($this->folder_video, $value['thumbnail']);
                                $value['landscape'] = Get_Image($this->folder_video, $value['landscape']);

                                // Video (320, 480, 720, 1080)
                                if (isset($value['video_320']) && !empty($value['video_320'])) {
                                    if ($value['video_upload_type'] == "server_video") {
                                        $value['video_320'] = Get_Video($this->folder_video, $value['video_320']);
                                    }
                                }
                                if (isset($value['video_480']) && !empty($value['video_480'])) {
                                    if ($value['video_upload_type'] == "server_video") {
                                        $value['video_480'] = Get_Video($this->folder_video, $value['video_480']);
                                    }
                                }
                                if (isset($value['video_720']) && !empty($value['video_720'])) {
                                    if ($value['video_upload_type'] == "server_video") {
                                        $value['video_720'] = Get_Video($this->folder_video, $value['video_720']);
                                    }
                                }
                                if (isset($value['video_1080']) && !empty($value['video_1080'])) {
                                    if ($value['video_upload_type'] == "server_video") {
                                        $value['video_1080'] = Get_Video($this->folder_video, $value['video_1080']);
                                    }
                                }

                                // Trailer
                                if (isset($value['trailer_url']) && !empty($value['trailer_url'])) {
                                    if ($value['trailer_type'] == "server_video") {
                                        $value['trailer_url'] = Get_Video($this->folder_video, $value['trailer_url']);
                                    }
                                }

                                // SubTitle_1_2_3
                                if (isset($value['subtitle_1']) && !empty($value['subtitle_1'])) {
                                    if ($value['subtitle_type'] == "server_video") {
                                        $value['subtitle_1'] = Get_Video($this->folder_video, $value['subtitle_1']);
                                    }
                                }
                                if (isset($value['subtitle_2']) && !empty($value['subtitle_2'])) {
                                    if ($value['subtitle_type'] == "server_video") {
                                        $value['subtitle_2'] = Get_Video($this->folder_video, $value['subtitle_2']);
                                    }
                                }
                                if (isset($value['subtitle_3']) && !empty($value['subtitle_3'])) {
                                    if ($value['subtitle_type'] == "server_video") {
                                        $value['subtitle_3'] = Get_Video($this->folder_video, $value['subtitle_3']);
                                    }
                                }

                                $value['stop_time'] = GetStopTimeByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                                $value['is_downloaded'] = Is_DownloadByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                                $value['is_bookmark'] = Is_BookmarkByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                                $value['rent_buy'] = VideoRentBuyByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                                $value['is_rent'] = IsRentVideo($value['id'], $value['video_type']);
                                $value['rent_price'] = GetPriceByRentVideo($value['id'], $value['video_type']);
                                $value['is_buy'] = $isBuyByUser;
                                $value['category_name'] = GetCategoryNameByIds($value['category_id']);
                                $value['session_id'] = "0";
                                $value['upcoming_type'] = 0;

                                $RelatedData[] = $value;
                                break;
                            } else {
                                $RelatedData = [];
                            }
                        }
                    }*/
                    $data['get_related_video'] = $relatedData;

                    // More Details
                    $More_Details[0]['title'] = "Starring";
                    $More_Details[1]['title'] = "Genres";
                    $More_Details[2]['title'] = "Director";
                    $More_Details[3]['title'] = "Supporting Actors";
                    $More_Details[4]['title'] = "Maturity Rating";
                    $More_Details[5]['title'] = "Networks";

                    $More_Details[0]['description'] = GetCastNameByIds($data['result']['cast_id']);
                    $More_Details[1]['description'] = GetCategoryNameByIds($data['result']['category_id']);
                    $More_Details[2]['description'] = "";
                    $More_Details[3]['description'] = "";
                    $More_Details[4]['description'] = $data['result']['maturity_rating'];
                    $More_Details[5]['description'] = $data['result']['networks'];

                    $data['more_details'] = $More_Details;
					$data['result']['is_exclusive_movie'] = !empty($data['result']['is_exclusive_movie']) ? true : false;
					if($data['result']['is_exclusive_movie']) {
						$exclusive_movie_data = json_decode($data['result']['exclusive_movie_data'], 1);
						$exclusive_movie_data_arr = [];
						$exclusive_movie_data_arr[0]['url'] = isset($exclusive_movie_data['trailer_url']) ? $exclusive_movie_data['trailer_url'] : '';
						if(!empty($exclusive_movie_data['trailer_image'])) {
							$exclusive_movie_data_arr[0]['image'] = asset($exclusive_movie_data['trailer_image']);
						} else {
							$exclusive_movie_data_arr[0]['image'] = '';
						}
						
						$exclusive_movie_data_arr[1]['url'] = isset($exclusive_movie_data['teaser_url']) ? $exclusive_movie_data['teaser_url'] : '';
						if(!empty($exclusive_movie_data['teaser_image'])) {
							$exclusive_movie_data_arr[1]['image'] = asset($exclusive_movie_data['teaser_image']);
						} else {
							$exclusive_movie_data_arr[1]['image'] = '';
						}
						
						$exclusive_movie_data_arr[2]['url'] = isset($exclusive_movie_data['promo_url']) ? $exclusive_movie_data['promo_url'] : '';
						if(!empty($exclusive_movie_data['promo_image'])) {
							$exclusive_movie_data_arr[2]['image'] = asset($exclusive_movie_data['promo_image']);
						} else {
							$exclusive_movie_data_arr[2]['image'] = '';
						}
						$data['result']['exclusive_movie_data'] = $exclusive_movie_data_arr;
					} else {
						$data['result']['exclusive_movie_data'] = [];
					}
                    $ads = DB::table('ads as a')
                        ->join('ad_assignments as aa', 'aa.ad_id', '=', 'a.id')
                        ->where('aa.assignable_type', 'video')
                        ->where('aa.assignable_id', $data['result']['id'])
                        ->where('aa.active', 1)
                        ->where('a.active', 1)
                        ->select(
                            'a.id',
                            'a.type',
                            'a.title',
                            'a.media_url',
                            'a.media_type',
                            'a.click_url',
                            'a.start_after_seconds',
                            'a.repeat_every_seconds',
                            'a.duration_seconds',
                            'a.skippable_after_seconds',
                            'a.priority',
                            'a.active',
                            'aa.ad_position'
                        )
                        ->orderBy('a.priority')
                        ->get()
                        ->map(function ($ad) {
                            $mediaUrl = $ad->media_url; 
                            if ($ad->media_type == 'image' && !empty($mediaUrl)) {
                                $mediaUrl = asset($mediaUrl);
                            }
                            
                            return [
                                'id' => $ad->id,
                                'type' => $ad->type,
                                'title' => $ad->title,
                                'media_url' =>  $mediaUrl,
                                'media_type' => $ad->media_type,
                                'click_url' => $ad->click_url,
                                'start_after_seconds' => (int)$ad->start_after_seconds,
                                'repeat_every_seconds' => (int)$ad->repeat_every_seconds,
                                'duration_seconds' => (int)$ad->duration_seconds,
                                'skippable_after_seconds' => (int)$ad->skippable_after_seconds,
                                'position' => $ad->ad_position,
                                'priority' => (int)$ad->priority,
                                'active' => (bool)$ad->active,
                            ];
                        })
                        ->values();

                    $data['result']['ads'] = $ads;
                } else {
                    $data['result'] = [];
                }

                return $data;
            } elseif ($video_type == 2) {

                $data['status'] = 200;
                $data['message'] = __('api_msg.get_record_successfully');

                $data['result'] = TVShow::where('id', $video_id)->where('video_type', $video_type)->first();

                if (!empty($data['result'])) {

                    // Thumbnail && Landscape
                    $data['result']['thumbnail'] = Get_Image($this->folder_show, $data['result']['thumbnail']);
                    $data['result']['landscape'] = Get_Image($this->folder_show, $data['result']['landscape']);
                    // Trailer
                    if (isset($data['result']['trailer_url']) && !empty($data['result']['trailer_url'])) {
                        if ($data['result']['trailer_type'] == "server_video") {
                            $data['result']['trailer_url'] = Get_Video($this->folder_video, $data['result']['trailer_url']);
                        }
                    }

                    $data['result']['stop_time'] = 0;
                    $data['result']['is_downloaded'] = 0;
                    $data['result']['is_bookmark'] = Is_BookmarkByUser($user_id, $data['result']['id'], $data['result']['type_id'], $data['result']['video_type']);
                    $data['result']['rent_buy'] = VideoRentBuyByUser($user_id, $data['result']['id'], $data['result']['type_id'], $data['result']['video_type']);
                    $data['result']['is_rent'] = IsRentVideo($data['result']['id'], $data['result']['video_type']);
                    $data['result']['rent_price'] = GetPriceByRentVideo($data['result']['id'], $data['result']['video_type']);
                    $data['result']['is_buy'] = $isBuyByUser;
                    $data['result']['category_name'] = GetCategoryNameByIds($data['result']['category_id']);
                    $data['result']['session_id'] = GetSessionByTVShowId($data['result']['id']);
                    $data['result']['upcoming_type'] = 0;

                    $data['cast'] = array();
                    $data['session'] = array();
                    $data['get_related_video'] = array();
                    $data['language'] = array();
                    $data['more_details'] = array();

                    // Cast
                    $Cast_Ids = explode(',', $data['result']['cast_id']);
                    $data['cast'] = Cast::whereIn('id', $Cast_Ids)->get();
                    imageNameToUrl($data['cast'], 'image', $this->folder_cast);

                    // Session
                    $Session_Ids = explode(',', $data['result']['session_id']);
                    $data['session'] = Session::whereIn('id', $Session_Ids)->get();
                    for ($i = 0; $i < count($data['session']); $i++) {

                        $data['session'][$i]['is_downloaded'] = Is_DownloadByUser($user_id, $data['session'][$i]['id'], $data['result']['type_id'], $data['result']['video_type'], $data['result']['id']);
                        $data['session'][$i]['rent_buy'] = 0;
                        $data['session'][$i]['is_rent'] = 0;
                        $data['session'][$i]['rent_price'] = 0;
                        $data['session'][$i]['is_buy'] = $isBuyByUser;
                    }

                    // Language
                    $Language_Ids = explode(',', $data['result']['language_id']);
                    $data['language'] = Language::whereIn('id', $Language_Ids)->get();
                    imageNameToUrl($data['language'], 'image', $this->folder_language);

                    // Get Related Video
                    $Category_Ids = explode(',', $data['result']['category_id']);
                    $All_Video = TVShow::where('id', '!=', $data['result']['id'])->where('video_type', $video_type)->latest()->get();

                    $RelatedData = [];
                    foreach ($All_Video as $key => $value) {

                        $C_Ids = explode(',', $value['category_id']);
                        foreach ($C_Ids as $key1 => $value1) {

                            if (in_array($value1, $Category_Ids)) {

                                // Thumbnail && Landscape
                                $value['thumbnail'] = Get_Image($this->folder_show, $value['thumbnail']);
                                $value['landscape'] = Get_Image($this->folder_show, $value['landscape']);

                                $value['stop_time'] = 0;
                                $value['is_downloaded'] = 0;
                                $value['is_bookmark'] = Is_BookmarkByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                                $value['rent_buy'] = VideoRentBuyByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                                $value['is_rent'] = IsRentVideo($value['id'], $value['video_type']);
                                $value['rent_price'] = GetPriceByRentVideo($value['id'], $value['video_type']);
                                $value['is_buy'] = $isBuyByUser;
                                $value['category_name'] = GetCategoryNameByIds($value['category_id']);
                                $value['session_id'] = GetSessionByTVShowId($value['id']);
                                $value['upcoming_type'] = 0;

                                $RelatedData[] = $value;
                                break;
                            } else {
                                $RelatedData = [];
                            }
                        }
                    }
                    $data['get_related_video'] = $RelatedData;

                    // More Details
                    $More_Details[0]['title'] = "Starring";
                    $More_Details[1]['title'] = "Genres";
                    $More_Details[2]['title'] = "Director";
                    $More_Details[3]['title'] = "Supporting Actors";
                    $More_Details[4]['title'] = "Maturity Rating";
                    $More_Details[5]['title'] = "Networks";

                    $More_Details[0]['description'] = GetCastNameByIds($data['result']['cast_id']);
                    $More_Details[1]['description'] = GetCategoryNameByIds($data['result']['category_id']);
                    $More_Details[2]['description'] = "";
                    $More_Details[3]['description'] = "";
                    $More_Details[4]['description'] = $data['result']['maturity_rating'];
                    $More_Details[5]['description'] = $data['result']['networks'];

                    $data['more_details'] = $More_Details;
                } else {
                    $data['result'] = [];
                }
                return $data;
            } elseif ($video_type == 5) {

                $data['status'] = 200;
                $data['message'] = __('api_msg.get_record_successfully');

                if ($upcoming_type == 1) {

                    $data['result'] = Video::where('id', $video_id)->where('video_type', $video_type)->first();
                    if (!empty($data['result'])) {

                        // Thumbnail && Landscape
                        $data['result']['thumbnail'] = Get_Image($this->folder_video, $data['result']['thumbnail']);
                        $data['result']['landscape'] = Get_Image($this->folder_video, $data['result']['landscape']);

                        // Video (320, 480, 720, 1080)
                        if (isset($data['result']['video_320']) && !empty($data['result']['video_320'])) {
                            if ($data['result']['video_upload_type'] == "server_video") {
                                $data['result']['video_320'] = Get_Video($this->folder_video, $data['result']['video_320']);
                            }
                        }
                        if (isset($data['result']['video_480']) && !empty($data['result']['video_480'])) {
                            if ($data['result']['video_upload_type'] == "server_video") {
                                $data['result']['video_480'] = Get_Video($this->folder_video, $data['result']['video_480']);
                            }
                        }
                        if (isset($data['result']['video_720']) && !empty($data['result']['video_720'])) {
                            if ($data['result']['video_upload_type'] == "server_video") {
                                $data['result']['video_720'] = Get_Video($this->folder_video, $data['result']['video_720']);
                            }
                        }
                        if (isset($data['result']['video_1080']) && !empty($data['result']['video_1080'])) {
                            if ($data['result']['video_upload_type'] == "server_video") {
                                $data['result']['video_1080'] = Get_Video($this->folder_video, $data['result']['video_1080']);
                            }
                        }

                        // Trailer
                        if (isset($data['result']['trailer_url']) && !empty($data['result']['trailer_url'])) {
                            if ($data['result']['trailer_type'] == "server_video") {
                                $data['result']['trailer_url'] = Get_Video($this->folder_video, $data['result']['trailer_url']);
                            }
                        }

                        // SubTitle_1_2_3
                        if (isset($data['result']['subtitle_1']) && !empty($data['result']['subtitle_1'])) {
                            if ($data['result']['subtitle_type'] == "server_video") {
                                $data['result']['subtitle_1'] = Get_Video($this->folder_video, $data['result']['subtitle_1']);
                            }
                        }
                        if (isset($data['result']['subtitle_2']) && !empty($data['result']['subtitle_2'])) {
                            if ($data['result']['subtitle_type'] == "server_video") {
                                $data['result']['subtitle_2'] = Get_Video($this->folder_video, $data['result']['subtitle_2']);
                            }
                        }
                        if (isset($data['result']['subtitle_3']) && !empty($data['result']['subtitle_3'])) {
                            if ($data['result']['subtitle_type'] == "server_video") {
                                $data['result']['subtitle_3'] = Get_Video($this->folder_video, $data['result']['subtitle_3']);
                            }
                        }

                        $data['result']['stop_time'] = GetStopTimeByUser($user_id, $data['result']['id'], $data['result']['type_id'], $data['result']['video_type']);
                        $data['result']['is_downloaded'] = Is_DownloadByUser($user_id, $data['result']['id'], $data['result']['type_id'], $data['result']['video_type']);
                        $data['result']['is_bookmark'] = Is_BookmarkByUser($user_id, $data['result']['id'], $data['result']['type_id'], $data['result']['video_type']);
                        $data['result']['rent_buy'] = VideoRentBuyByUser($user_id, $data['result']['id'], $data['result']['type_id'], $data['result']['video_type']);
                        $data['result']['is_rent'] = IsRentVideo($data['result']['id'], $data['result']['video_type']);
                        $data['result']['rent_price'] = GetPriceByRentVideo($data['result']['id'], $data['result']['video_type']);
                        $data['result']['is_buy'] = $isBuyByUser;
                        $data['result']['category_name'] = GetCategoryNameByIds($data['result']['category_id']);
                        $data['result']['session_id'] = "0";
                        $data['result']['upcoming_type'] = 1;

                        $data['cast'] = array();
                        $data['session'] = array();
                        $data['get_related_video'] = array();
                        $data['language'] = array();
                        $data['more_details'] = array();

                        // Cast
                        $Cast_Ids = explode(',', $data['result']['cast_id']);
                        $data['cast'] = Cast::whereIn('id', $Cast_Ids)->get();
                        imageNameToUrl($data['cast'], 'image', $this->folder_cast);

                        // Language
                        $Language_Ids = explode(',', $data['result']['language_id']);
                        $data['language'] = Language::whereIn('id', $Language_Ids)->get();
                        imageNameToUrl($data['language'], 'image', $this->folder_language);

                        //Get Related Video
                        $Category_Ids = explode(',', $data['result']['category_id']);
                        $All_Video = Video::where('id', '!=', $data['result']['id'])->where('video_type', $video_type)->latest()->get();

                        $RelatedData = [];
                        foreach ($All_Video as $key => $value) {

                            $C_Ids = explode(',', $value['category_id']);
                            foreach ($C_Ids as $key1 => $value1) {

                                if (in_array($value1, $Category_Ids)) {

                                    // Thumbnail && Landscape
                                    $value['thumbnail'] = Get_Image($this->folder_video, $value['thumbnail']);
                                    $value['landscape'] = Get_Image($this->folder_video, $value['landscape']);

                                    // Video (320, 480, 720, 1080)
                                    if (isset($value['video_320']) && !empty($value['video_320'])) {
                                        if ($value['video_upload_type'] == "server_video") {
                                            $value['video_320'] = Get_Video($this->folder_video, $value['video_320']);
                                        }
                                    }
                                    if (isset($value['video_480']) && !empty($value['video_480'])) {
                                        if ($value['video_upload_type'] == "server_video") {
                                            $value['video_480'] = Get_Video($this->folder_video, $value['video_480']);
                                        }
                                    }
                                    if (isset($value['video_720']) && !empty($value['video_720'])) {
                                        if ($value['video_upload_type'] == "server_video") {
                                            $value['video_720'] = Get_Video($this->folder_video, $value['video_720']);
                                        }
                                    }
                                    if (isset($value['video_1080']) && !empty($value['video_1080'])) {
                                        if ($value['video_upload_type'] == "server_video") {
                                            $value['video_1080'] = Get_Video($this->folder_video, $value['video_1080']);
                                        }
                                    }

                                    // Trailer
                                    if (isset($value['trailer_url']) && !empty($value['trailer_url'])) {
                                        if ($value['trailer_type'] == "server_video") {
                                            $value['trailer_url'] = Get_Video($this->folder_video, $value['trailer_url']);
                                        }
                                    }

                                    // SubTitle_1_2_3
                                    if (isset($value['subtitle_1']) && !empty($value['subtitle_1'])) {
                                        if ($value['subtitle_type'] == "server_video") {
                                            $value['subtitle_1'] = Get_Video($this->folder_video, $value['subtitle_1']);
                                        }
                                    }
                                    if (isset($value['subtitle_2']) && !empty($value['subtitle_2'])) {
                                        if ($value['subtitle_type'] == "server_video") {
                                            $value['subtitle_2'] = Get_Video($this->folder_video, $value['subtitle_2']);
                                        }
                                    }
                                    if (isset($value['subtitle_3']) && !empty($value['subtitle_3'])) {
                                        if ($value['subtitle_type'] == "server_video") {
                                            $value['subtitle_3'] = Get_Video($this->folder_video, $value['subtitle_3']);
                                        }
                                    }

                                    $value['stop_time'] = GetStopTimeByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                                    $value['is_downloaded'] = Is_DownloadByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                                    $value['is_bookmark'] = Is_BookmarkByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                                    $value['rent_buy'] = VideoRentBuyByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                                    $value['is_rent'] = IsRentVideo($value['id'], $value['video_type']);
                                    $value['rent_price'] = GetPriceByRentVideo($value['id'], $value['video_type']);
                                    $value['is_buy'] = $isBuyByUser;
                                    $value['category_name'] = GetCategoryNameByIds($value['category_id']);
                                    $value['session_id'] = "0";
                                    $value['upcoming_type'] = 1;

                                    $RelatedData[] = $value;
                                    break;
                                } else {
                                    $RelatedData = [];
                                }
                            }
                        }
                        $data['get_related_video'] = $RelatedData;

                        // More Details
                        $More_Details[0]['title'] = "Starring";
                        $More_Details[1]['title'] = "Genres";
                        $More_Details[2]['title'] = "Director";
                        $More_Details[3]['title'] = "Supporting Actors";
                        $More_Details[4]['title'] = "Maturity Rating";
                        $More_Details[5]['title'] = "Networks";

                        $More_Details[0]['description'] = GetCastNameByIds($data['result']['cast_id']);
                        $More_Details[1]['description'] = GetCategoryNameByIds($data['result']['category_id']);
                        $More_Details[2]['description'] = "";
                        $More_Details[3]['description'] = "";
                        $More_Details[4]['description'] = $data['result']['maturity_rating'];
                        $More_Details[5]['description'] = $data['result']['networks'];

                        $data['more_details'] = $More_Details;
                    } else {
                        $data['result'] = [];
                    }
                } else if ($upcoming_type == 2) {

                    $data['result'] = TVShow::where('id', $video_id)->where('video_type', $video_type)->first();
                    if (!empty($data['result'])) {

                        // Thumbnail && Landscape
                        $data['result']['thumbnail'] = Get_Image($this->folder_show, $data['result']['thumbnail']);
                        $data['result']['landscape'] = Get_Image($this->folder_show, $data['result']['landscape']);
                        // Trailer
                        if (isset($data['result']['trailer_url']) && !empty($data['result']['trailer_url'])) {
                            if ($data['result']['trailer_type'] == "server_video") {
                                $data['result']['trailer_url'] = Get_Video($this->folder_video, $data['result']['trailer_url']);
                            }
                        }

                        $data['result']['stop_time'] = 0;
                        $data['result']['is_downloaded'] = 0;
                        $data['result']['is_bookmark'] = Is_BookmarkByUser($user_id, $data['result']['id'], $data['result']['type_id'], $data['result']['video_type']);
                        $data['result']['rent_buy'] = VideoRentBuyByUser($user_id, $data['result']['id'], $data['result']['type_id'], $data['result']['video_type']);
                        $data['result']['is_rent'] = IsRentVideo($data['result']['id'], $data['result']['video_type']);
                        $data['result']['rent_price'] = GetPriceByRentVideo($data['result']['id'], $data['result']['video_type']);
                        $data['result']['is_buy'] = $isBuyByUser;
                        $data['result']['category_name'] = GetCategoryNameByIds($data['result']['category_id']);
                        $data['result']['session_id'] = GetSessionByTVShowId($data['result']['id']);
                        $data['result']['upcoming_type'] = 2;

                        $data['cast'] = array();
                        $data['session'] = array();
                        $data['get_related_video'] = array();
                        $data['language'] = array();
                        $data['more_details'] = array();

                        // Cast
                        $Cast_Ids = explode(',', $data['result']['cast_id']);
                        $data['cast'] = Cast::whereIn('id', $Cast_Ids)->get();
                        imageNameToUrl($data['cast'], 'image', $this->folder_cast);

                        // Session
                        $Session_Ids = explode(',', $data['result']['session_id']);
                        $data['session'] = Session::whereIn('id', $Session_Ids)->get();
                        for ($i = 0; $i < count($data['session']); $i++) {

                            $data['session'][$i]['is_downloaded'] = Is_DownloadByUser($user_id, $data['session'][$i]['id'], $data['result']['type_id'], $data['result']['video_type'], $data['result']['id']);
                            $data['session'][$i]['rent_buy'] = 0;
                            $data['session'][$i]['is_rent'] = 0;
                            $data['session'][$i]['rent_price'] = 0;
                            $data['session'][$i]['is_buy'] = $isBuyByUser;
                        }

                        // Language
                        $Language_Ids = explode(',', $data['result']['language_id']);
                        $data['language'] = Language::whereIn('id', $Language_Ids)->get();
                        imageNameToUrl($data['language'], 'image', $this->folder_language);

                        // Get Related Video
                        $Category_Ids = explode(',', $data['result']['category_id']);
                        $All_Video = TVShow::where('id', '!=', $data['result']['id'])->where('video_type', $video_type)->latest()->get();

                        $RelatedData = [];
                        foreach ($All_Video as $key => $value) {

                            $C_Ids = explode(',', $value['category_id']);
                            foreach ($C_Ids as $key1 => $value1) {

                                if (in_array($value1, $Category_Ids)) {

                                    // Thumbnail && Landscape
                                    $value['thumbnail'] = Get_Image($this->folder_show, $value['thumbnail']);
                                    $value['landscape'] = Get_Image($this->folder_show, $value['landscape']);

                                    $value['stop_time'] = 0;
                                    $value['is_downloaded'] = 0;
                                    $value['is_bookmark'] = Is_BookmarkByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                                    $value['rent_buy'] = VideoRentBuyByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                                    $value['is_rent'] = IsRentVideo($value['id'], $value['video_type']);
                                    $value['rent_price'] = GetPriceByRentVideo($value['id'], $value['video_type']);
                                    $value['is_buy'] = $isBuyByUser;
                                    $value['category_name'] = GetCategoryNameByIds($value['category_id']);
                                    $value['session_id'] = GetSessionByTVShowId($value['id']);
                                    $value['upcoming_type'] = 2;

                                    $RelatedData[] = $value;
                                    break;
                                } else {
                                    $RelatedData = [];
                                }
                            }
                        }
                        $data['get_related_video'] = $RelatedData;

                        // More Details
                        $More_Details[0]['title'] = "Starring";
                        $More_Details[1]['title'] = "Genres";
                        $More_Details[2]['title'] = "Director";
                        $More_Details[3]['title'] = "Supporting Actors";
                        $More_Details[4]['title'] = "Maturity Rating";
                        $More_Details[5]['title'] = "Networks";

                        $More_Details[0]['description'] = GetCastNameByIds($data['result']['cast_id']);
                        $More_Details[1]['description'] = GetCategoryNameByIds($data['result']['category_id']);
                        $More_Details[2]['description'] = "";
                        $More_Details[3]['description'] = "";
                        $More_Details[4]['description'] = $data['result']['maturity_rating'];
                        $More_Details[5]['description'] = $data['result']['networks'];

                        $data['more_details'] = $More_Details;
                    } else {
                        $data['result'] = [];
                    }
                } else {
                    $data['result'] = [];
                }

                return $data;
            } else {
                return APIResponse(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function cast_detail(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'cast_id' => 'required|numeric',
                ],
                [
                    'cast_id.required' => __('api_msg.please_enter_required_fields'),
                ]
            );
            if ($validation->fails()) {

                $errors = $validation->errors()->first('cast_id');
                $data['status'] = 400;
                if ($errors) {
                    $data['message'] = $errors;
                }
                return $data;
            }

            $cast_id = $request->cast_id;
            $Data = Cast::where('id', $cast_id)->first();
            if (!empty($Data)) {

                imageNameToUrl(array($Data), 'image', $this->folder_cast);

                return APIResponse(200, __('api_msg.get_record_successfully'), array($Data));
            } else {
                return APIResponse(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function get_video_by_session_id(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'show_id' => 'required|numeric',
                    'session_id' => 'required|numeric',
                ],
                [
                    'show_id.required' => __('api_msg.please_enter_required_fields'),
                    'session_id.required' => __('api_msg.please_enter_required_fields'),
                ]
            );
            if ($validation->fails()) {

                $errors = $validation->errors()->first('show_id');
                $errors1 = $validation->errors()->first('session_id');
                $data['status'] = 400;
                if ($errors) {
                    $data['message'] = $errors;
                } elseif ($errors1) {
                    $data['message'] = $errors1;
                }
                return $data;
            }

            $session_id = $request->session_id;
            $show_id = $request->show_id;
            $user_id = isset($request->user_id) ? $request->user_id : 0;
            $data['status'] = 200;
            $data['message'] = __('api_msg.get_record_successfully');

            $Show_Data = TVShow::where('id', $show_id)->first();
            if (!empty($Show_Data)) {

                $TVShow_Video = TVShowVideo::where('session_id', $session_id)->where('show_id', $show_id)->orderBy('sortable', 'asc')->get();
                if (count($TVShow_Video) > 0) {

                    for ($i = 0; $i < count($TVShow_Video); $i++) {

                        // Thumbnail && Landscape
                        $TVShow_Video[$i]['thumbnail'] = Get_Image($this->folder_show, $TVShow_Video[$i]['thumbnail']);
                        $TVShow_Video[$i]['landscape'] = Get_Image($this->folder_show, $TVShow_Video[$i]['landscape']);

                        // Video (320, 480, 720, 1080)
                        if (isset($TVShow_Video[$i]['video_320']) && !empty($TVShow_Video[$i]['video_320'])) {
                            if ($TVShow_Video[$i]['video_upload_type'] == "server_video") {
                                $TVShow_Video[$i]['video_320']  = Get_Video($this->folder_video, $TVShow_Video[$i]['video_320']);
                            }
                        }
                        if (isset($TVShow_Video[$i]['video_480']) && !empty($TVShow_Video[$i]['video_480'])) {
                            if ($TVShow_Video[$i]['video_upload_type'] == "server_video") {
                                $TVShow_Video[$i]['video_480'] = Get_Video($this->folder_video, $TVShow_Video[$i]['video_480']);
                            }
                        }
                        if (isset($TVShow_Video[$i]['video_720']) && !empty($TVShow_Video[$i]['video_720'])) {
                            if ($TVShow_Video[$i]['video_upload_type'] == "server_video") {
                                $TVShow_Video[$i]['video_720'] = Get_Video($this->folder_video, $TVShow_Video[$i]['video_720']);
                            }
                        }
                        if (isset($TVShow_Video[$i]['video_1080']) && !empty($TVShow_Video[$i]['video_1080'])) {
                            if ($TVShow_Video[$i]['video_upload_type'] == "server_video") {
                                $TVShow_Video[$i]['video_1080'] = Get_Video($this->folder_video, $TVShow_Video[$i]['video_1080']);
                            }
                        }

                        // SubTitle_1_2_3
                        if (isset($TVShow_Video[$i]['subtitle_1']) && !empty($TVShow_Video[$i]['subtitle_1'])) {
                            if ($TVShow_Video[$i]['subtitle_type'] == "server_video") {
                                $TVShow_Video[$i]['subtitle_1'] = Get_Video($this->folder_video, $TVShow_Video[$i]['subtitle_1']);
                            }
                        }
                        if (isset($TVShow_Video[$i]['subtitle_2']) && !empty($TVShow_Video[$i]['subtitle_2'])) {
                            if ($TVShow_Video[$i]['subtitle_type'] == "server_video") {
                                $TVShow_Video[$i]['subtitle_2'] = Get_Video($this->folder_video, $TVShow_Video[$i]['subtitle_2']);
                            }
                        }
                        if (isset($TVShow_Video[$i]['subtitle_3']) && !empty($TVShow_Video[$i]['subtitle_3'])) {
                            if ($TVShow_Video[$i]['subtitle_type'] == "server_video") {
                                $TVShow_Video[$i]['subtitle_3'] = Get_Video($this->folder_video, $TVShow_Video[$i]['subtitle_3']);
                            }
                        }

                        $TVShow_Video[$i]['stop_time'] = GetStopTimeByUser($user_id, $TVShow_Video[$i]['id'], $TVShow_Video[$i]['type_id'], $TVShow_Video[$i]['video_type']);
                        $TVShow_Video[$i]['is_downloaded'] = Is_DownloadByUser($user_id, $TVShow_Video[$i]['id'], $TVShow_Video[$i]['type_id'], $TVShow_Video[$i]['video_type'], $Show_Data['id']);
                        $TVShow_Video[$i]['is_bookmark'] = 0;
                        $TVShow_Video[$i]['rent_buy'] = 0;
                        $TVShow_Video[$i]['is_rent'] = 0;
                        $TVShow_Video[$i]['rent_price'] = 0;
                        $TVShow_Video[$i]['is_buy'] = IsBuyByUser($user_id);
                        $TVShow_Video[$i]['category_name'] = "";
                        $TVShow_Video[$i]['upcoming_type'] = 0;
                        if ($TVShow_Video[$i]['video_type'] == 5) {
                            $TVShow_Video[$i]['upcoming_type'] = 2;
                        }

                        $data['result'] = $TVShow_Video;
                    }
                } else {
                    $data['result'] = [];
                }
            } else {
                $data['result'] = [];
            }
            return $data;
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function get_bookmark_video(Request $request)
    {
        try {

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

                $errors = $validation->errors()->first('user_id');
                $data['status'] = 400;
                if ($errors) {
                    $data['message'] = $errors;
                }
                return $data;
            }

            $user_id = $request->user_id;
            $data = array();
            // $All_Video = Bookmark::where('user_id', $user_id)->where('status', 1)->latest()->get();
            $bookmarkQuery = Bookmark::query()
            ->where('bookmark.user_id', $user_id)
            ->where('bookmark.status', 1)
            ->selectRaw("
                bookmark.user_id,
                bookmark.video_type,
                bookmark.video_id,
                NULL as webseries_id, 
                bookmark.created_at
            ");

            $reactionQuery = WebseriesReaction::query()
            ->where('webseries_reactions.user_id', $user_id)
            ->where('webseries_reactions.is_wishlist', true)
            ->leftJoin('web_series', 'web_series.id', '=', 'webseries_reactions.web_series_id')
            ->selectRaw("
                webseries_reactions.user_id,
                3 as video_type,
                web_series.id as video_id,
                webseries_reactions.web_series_id, 
                webseries_reactions.created_at
            ");
            
              $All_Video = $bookmarkQuery
            ->unionAll($reactionQuery)
            ->orderBy('created_at', 'DESC')
            ->get(); 
            foreach ($All_Video as $value) {
                    if ($value->video_type == 1) {

                        $video = Video::find($value->video_id);
                        if (!$video) continue;

                        $data[] = [
                            'id'           => $video->id,
                            'name'        => $video->name,
                            'video_type'   => 1,
                            'thumbnail'    => Get_Image($this->folder_video, $video->thumbnail),
                            'landscape'    => Get_Image($this->folder_video, $video->landscape), 
                            'is_bookmark'  => 1,
                            'stop_time'    => GetStopTimeByUser(
                                                $user_id,
                                                $video->id,
                                                $video->type_id,
                                                1
                                            ),
                            'is_webseries'    => 0,
                        ];
                    }
                    elseif ($value->video_type == 2) {

                        $tv = TVShow::find($value->video_id);
                        if (!$tv) continue;

                        $data[] = [
                            'id'           => $tv->id,
                            'name'        => $tv->name,
                            'video_type'   => 2,
                            'thumbnail'    => Get_Image($this->folder_show, $tv->thumbnail),
                            'landscape'    => Get_Image($this->folder_show, $tv->landscape), 
                            'is_bookmark'  => 1,
                            'stop_time'    => 0,
                            'is_webseries'    => 0,
                        ];
                    }
                    elseif ($value->video_type == 3) {

                        $ws = WebSeries::find($value->webseries_id);
                        if (!$ws) continue;

                        $data[] = [
                            'id'           => $ws->id,
                            'name'        => $ws->title,
                            'video_type'   => 3,
                            'thumbnail'    => asset("images/web_series/" . $ws->thumbnail),
                            'landscape'    => asset("images/web_series/" . $ws->thumbnail), 
                            'is_bookmark'  => 0,
                            'stop_time'    => 0,
                            'is_webseries'    => 1,
                        ];
                    }
                }
            // foreach ($All_Video as $key => $value) {

            //     if ($value['video_type'] == 1) {

            //         $Video = Video::where('id', $value['video_id'])->first();
            //         if (!empty($Video)) {

            //             // Thumbnail && Landscape
            //             $Video['thumbnail'] = Get_Image($this->folder_video, $Video['thumbnail']);
            //             $Video['landscape'] = Get_Image($this->folder_video, $Video['landscape']);

            //             // Video (320, 480, 720, 1080)
            //             if (isset($Video['video_320']) && !empty($Video['video_320'])) {
            //                 if ($Video['video_upload_type'] == "server_video") {
            //                     $Video['video_320'] = Get_Video($this->folder_video, $Video['video_320']);
            //                 }
            //             }
            //             if (isset($Video['video_480']) && !empty($Video['video_480'])) {
            //                 if ($Video['video_upload_type'] == "server_video") {
            //                     $Video['video_480'] = Get_Video($this->folder_video, $Video['video_480']);
            //                 }
            //             }
            //             if (isset($Video['video_720']) && !empty($Video['video_720'])) {
            //                 if ($Video['video_upload_type'] == "server_video") {
            //                     $Video['video_720'] = Get_Video($this->folder_video, $Video['video_720']);
            //                 }
            //             }
            //             if (isset($Video['video_1080']) && !empty($Video['video_1080'])) {
            //                 if ($Video['video_upload_type'] == "server_video") {
            //                     $Video['video_1080'] = Get_Video($this->folder_video, $Video['video_1080']);
            //                 }
            //             }

            //             // Trailer
            //             if (isset($Video['trailer_url']) && !empty($Video['trailer_url'])) {
            //                 if ($Video['trailer_type'] == "server_video") {
            //                     $Video['trailer_url'] = Get_Video($this->folder_video, $Video['trailer_url']);
            //                 }
            //             }

            //             // SubTitle_1_2_3
            //             if (isset($Video['subtitle_1']) && !empty($Video['subtitle_1'])) {
            //                 if ($Video['subtitle_type'] == "server_video") {
            //                     $Video['subtitle_1'] = Get_Video($this->folder_video, $Video['subtitle_1']);
            //                 }
            //             }
            //             if (isset($Video['subtitle_2']) && !empty($Video['subtitle_2'])) {
            //                 if ($Video['subtitle_type'] == "server_video") {
            //                     $Video['subtitle_2'] = Get_Video($this->folder_video, $Video['subtitle_2']);
            //                 }
            //             }
            //             if (isset($Video['subtitle_3']) && !empty($Video['subtitle_3'])) {
            //                 if ($Video['subtitle_type'] == "server_video") {
            //                     $Video['subtitle_3'] = Get_Video($this->folder_video, $Video['subtitle_3']);
            //                 }
            //             }

            //             $Video['stop_time'] = GetStopTimeByUser($user_id, $Video['id'], $Video['type_id'], $Video['video_type']);
            //             $Video['is_downloaded'] = Is_DownloadByUser($user_id, $Video['id'], $Video['type_id'], $Video['video_type']);
            //             $Video['is_bookmark'] = Is_BookmarkByUser($user_id, $Video['id'], $Video['type_id'], $Video['video_type']);
            //             $Video['rent_buy'] = VideoRentBuyByUser($user_id, $Video['id'], $Video['type_id'], $Video['video_type']);
            //             $Video['is_rent'] = IsRentVideo($Video['id'], $Video['video_type']);
            //             $Video['rent_price'] = GetPriceByRentVideo($Video['id'], $Video['video_type']);
            //             $Video['is_buy'] = IsBuyByUser($user_id);
            //             $Video['category_name'] = GetCategoryNameByIds($Video['category_id']);
            //             $Video['session_id'] = "0";
            //             $Video['upcoming_type'] = 0;

            //             $data[] = $Video;
            //         }
            //     } elseif ($value['video_type'] == 2) {

            //         $Video = TVShow::where('id', $value['video_id'])->first();
            //         if (!empty($Video)) {

            //             // Thumbnail && Landscape
            //             $Video['thumbnail'] = Get_Image($this->folder_show, $Video['thumbnail']);
            //             $Video['landscape'] = Get_Image($this->folder_show, $Video['landscape']);
            //             // Trailer
            //             if (isset($Video['trailer_url']) && !empty($Video['trailer_url'])) {
            //                 if ($Video['trailer_type'] == "server_video") {
            //                     $Video['trailer_url'] = Get_Video($this->folder_video,  $Video['trailer_url']);
            //                 }
            //             }

            //             $Video['stop_time'] = 0;
            //             $Video['is_downloaded'] = 0;
            //             $Video['is_bookmark'] = Is_BookmarkByUser($user_id, $Video['id'], $Video['type_id'], $Video['video_type']);
            //             $Video['rent_buy'] = VideoRentBuyByUser($user_id, $Video['id'], $Video['type_id'], $Video['video_type']);
            //             $Video['is_rent'] = IsRentVideo($Video['id'], $Video['video_type']);
            //             $Video['rent_price'] = GetPriceByRentVideo($Video['id'], $Video['video_type']);
            //             $Video['is_buy'] = IsBuyByUser($user_id);
            //             $Video['category_name'] = GetCategoryNameByIds($Video['category_id']);
            //             $Video['session_id'] = GetSessionByTVShowId($Video['id']);
            //             $Video['upcoming_type'] = 0;

            //             $data[] = $Video;
            //         }
            //     }
            // }

            return APIResponse(200, __('api_msg.get_record_successfully'), $data);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function search_video(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                ],
                [
                    'name.required' => __('api_msg.please_enter_required_fields'),
                ]
            );
            if ($validation->fails()) {

                $errors = $validation->errors()->first('name');
                $data['status'] = 400;
                if ($errors) {
                    $data['message'] = $errors;
                }
                return $data;
            }

            $name = $request->name;
            $user_id = isset($request->user_id) ? $request->user_id : 0;

            $data['status'] = 200;
            $data['message'] = __('api_msg.get_record_successfully');
            $data['result'] = array();
            $data['video'] = array();
            $data['tvshow'] = array();

            $All_Video = Video::where('name', 'LIKE', "%{$name}%")->latest()->get();
            foreach ($All_Video as $key => $value) {

                // Thumbnail && Landscape
                $value['thumbnail'] = Get_Image($this->folder_video, $value['thumbnail']);
                $value['landscape'] = Get_Image($this->folder_video, $value['landscape']);

                // Video (320, 480, 720, 1080)
                if (isset($value['video_320']) && !empty($value['video_320'])) {
                    if ($value['video_upload_type'] == "server_video") {
                        $value['video_320'] = Get_Video($this->folder_video, $value['video_320']);
                    }
                }
                if (isset($value['video_480']) && !empty($value['video_480'])) {
                    if ($value['video_upload_type'] == "server_video") {
                        $value['video_480'] = Get_Video($this->folder_video, $value['video_480']);
                    }
                }
                if (isset($value['video_720']) && !empty($value['video_720'])) {
                    if ($value['video_upload_type'] == "server_video") {
                        $value['video_720'] = Get_Video($this->folder_video, $value['video_720']);
                    }
                }
                if (isset($value['video_1080']) && !empty($value['video_1080'])) {
                    if ($value['video_upload_type'] == "server_video") {
                        $value['video_1080'] = Get_Video($this->folder_video, $value['video_1080']);
                    }
                }

                // Trailer
                if (isset($value['trailer_url']) && !empty($value['trailer_url'])) {
                    if ($value['trailer_type'] == "server_file") {
                        $value['trailer_url'] = Get_Video($this->folder_video, $value['trailer_url']);
                    }
                }

                // SubTitle_1_2_3
                if (isset($value['subtitle_1']) && !empty($value['subtitle_1'])) {
                    if ($value['subtitle_type'] == "server_video") {
                        $value['subtitle_1'] = Get_Video($this->folder_video, $value['subtitle_1']);
                    }
                }
                if (isset($value['subtitle_2']) && !empty($value['subtitle_2'])) {
                    if ($value['subtitle_type'] == "server_video") {
                        $value['subtitle_2'] = Get_Video($this->folder_video, $value['subtitle_2']);
                    }
                }
                if (isset($value['subtitle_3']) && !empty($value['subtitle_3'])) {
                    if ($value['subtitle_type'] == "server_video") {
                        $value['subtitle_3'] = Get_Video($this->folder_video, $value['subtitle_3']);
                    }
                }

                $value['stop_time'] = GetStopTimeByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                $value['is_downloaded'] = Is_DownloadByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                $value['is_bookmark'] = Is_BookmarkByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                $value['rent_buy'] = VideoRentBuyByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                $value['is_rent'] = IsRentVideo($value['id'], $value['video_type']);
                $value['rent_price'] = GetPriceByRentVideo($value['id'], $value['video_type']);
                $value['is_buy'] = IsBuyByUser($user_id);
                $value['category_name'] = GetCategoryNameByIds($value['category_id']);
                $value['session_id'] = "0";
                $value['upcoming_type'] = 0;
                if ($value['video_type'] == 5) {
                    $value['upcoming_type'] = 1;
                }

                $data['video'][] = $value;
            }

            $All_TVShow = TVShow::where('name', 'LIKE', "%{$name}%")->latest()->get();
            foreach ($All_TVShow as $key => $value) {

                // Thumbnail && Landscape
                $value['thumbnail'] = Get_Image($this->folder_show, $value['thumbnail']);
                $value['landscape'] = Get_Image($this->folder_show, $value['landscape']);
                // Trailer
                if (isset($value['trailer_url']) && !empty($value['trailer_url'])) {
                    if ($value['trailer_type'] == "server_video") {
                        $value['trailer_url'] = Get_Video($this->folder_video, $value['trailer_url']);
                    }
                }

                $value['stop_time'] = 0;
                $value['is_downloaded'] = 0;
                $value['is_bookmark'] = Is_BookmarkByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                $value['rent_buy'] = VideoRentBuyByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                $value['is_rent'] = IsRentVideo($value['id'], $value['video_type']);
                $value['rent_price'] = GetPriceByRentVideo($value['id'], $value['video_type']);
                $value['is_buy'] = IsBuyByUser($user_id);
                $value['category_name'] = GetCategoryNameByIds($value['category_id']);
                $value['session_id'] = GetSessionByTVShowId($value['id']);
                $value['session_id'] = "0";
                $value['upcoming_type'] = 0;
                if ($value['video_type'] == 5) {
                    $value['upcoming_type'] = 2;
                }

                $data['tvshow'][] = $value;
            }

            return $data;
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function video_by_category(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'category_id' => 'required|numeric',
                ],
                [
                    'category_id.required' => __('api_msg.please_enter_required_fields'),
                ]
            );
            if ($validation->fails()) {

                $errors = $validation->errors()->first('category_id');
                $data['status'] = 400;
                if ($errors) {
                    $data['message'] = $errors;
                }
                return $data;
            }

            $category_id = $request->category_id;
            $user_id = isset($request->user_id) ? $request->user_id : 0;
            $type_id = isset($request->type_id) ? $request->type_id : 0;
            $upcoming_type = isset($request->upcoming_type) ? $request->upcoming_type : 0;

            $check_type = Type::where('id', $type_id)->first();

            $type = 1;
            if (isset($check_type)) {
                $type = $check_type->type;
            }

            $data = array();
            if ($type == 1) {

                $All_Video = Video::where('video_type', $type)->latest()->get();
                foreach ($All_Video as $key => $value) {
                    $C_Ids = explode(',', $value['category_id']);
                    if (in_array($category_id, $C_Ids)) {

                        // Thumbnail && Landscape
                        $value['thumbnail'] = Get_Image($this->folder_video, $value['thumbnail']);
                        $value['landscape'] = Get_Image($this->folder_video, $value['landscape']);

                        // Video (320, 480, 720, 1080)
                        if (isset($value['video_320']) && !empty($value['video_320'])) {
                            if ($value['video_upload_type'] == "server_video") {
                                $value['video_320'] = Get_Video($this->folder_video, $value['video_320']);
                            }
                        }
                        if (isset($value['video_480']) && !empty($value['video_480'])) {
                            if ($value['video_upload_type'] == "server_video") {
                                $value['video_480'] = Get_Video($this->folder_video, $value['video_480']);
                            }
                        }
                        if (isset($value['video_720']) && !empty($value['video_720'])) {
                            if ($value['video_upload_type'] == "server_video") {
                                $value['video_720'] = Get_Video($this->folder_video, $value['video_720']);
                            }
                        }
                        if (isset($value['video_1080']) && !empty($value['video_1080'])) {
                            if ($value['video_upload_type'] == "server_video") {
                                $value['video_1080'] = Get_Video($this->folder_video, $value['video_1080']);
                            }
                        }

                        // Trailer
                        if (isset($value['trailer_url']) && !empty($value['trailer_url'])) {
                            if ($value['trailer_type'] == "server_video") {
                                $value['trailer_url'] = Get_Video($this->folder_video, $value['trailer_url']);
                            }
                        }

                        // SubTitle_1_2_3
                        if (isset($value['subtitle_1']) && !empty($value['subtitle_1'])) {
                            if ($value['subtitle_type'] == "server_video") {
                                $value['subtitle_1'] = Get_Video($this->folder_video, $value['subtitle_1']);
                            }
                        }
                        if (isset($value['subtitle_2']) && !empty($value['subtitle_2'])) {
                            if ($value['subtitle_type'] == "server_video") {
                                $value['subtitle_2'] = Get_Video($this->folder_video, $value['subtitle_2']);
                            }
                        }
                        if (isset($value['subtitle_3']) && !empty($value['subtitle_3'])) {
                            if ($value['subtitle_type'] == "server_video") {
                                $value['subtitle_3'] = Get_Video($this->folder_video, $value['subtitle_3']);
                            }
                        }

                        $value['stop_time'] = GetStopTimeByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                        $value['is_downloaded'] = Is_DownloadByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                        $value['is_bookmark'] = Is_BookmarkByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                        $value['rent_buy'] = VideoRentBuyByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                        $value['is_rent'] = IsRentVideo($value['id'], $value['video_type']);
                        $value['rent_price'] = GetPriceByRentVideo($value['id'], $value['video_type']);
                        $value['is_buy'] = IsBuyByUser($user_id);
                        $value['category_name'] = GetCategoryNameByIds($value['category_id']);
                        $value['session_id'] = "0";
                        $value['upcoming_type'] = 0;

                        $data[] = $value;
                    }
                }
            }
            if ($type == 2) {

                $TVShow = TVShow::where('video_type', $type)->latest()->get();
                foreach ($TVShow as $key => $Video) {
                    $C_Ids = explode(',', $Video['category_id']);
                    if (in_array($category_id, $C_Ids)) {

                        // Thumbnail && Landscape
                        $Video['thumbnail'] = Get_Image($this->folder_show, $Video['thumbnail']);
                        $Video['landscape'] = Get_Image($this->folder_show, $Video['landscape']);
                        // Trailer
                        if (isset($Video['trailer_url']) && !empty($Video['trailer_url'])) {
                            if ($Video['trailer_type'] == "server_video") {
                                $Video['trailer_url'] = Get_Video($this->folder_video, $Video['trailer_url']);
                            }
                        }

                        $Video['stop_time'] = 0;
                        $Video['is_downloaded'] = 0;
                        $Video['is_bookmark'] = Is_BookmarkByUser($user_id, $Video['id'], $Video['type_id'], $Video['video_type']);
                        $Video['rent_buy'] = VideoRentBuyByUser($user_id, $Video['id'], $Video['type_id'], $Video['video_type']);
                        $Video['is_rent'] = IsRentVideo($Video['id'], $Video['video_type']);
                        $Video['rent_price'] = GetPriceByRentVideo($Video['id'], $Video['video_type']);
                        $Video['is_buy'] = IsBuyByUser($user_id);
                        $Video['category_name'] = GetCategoryNameByIds($Video['category_id']);
                        $Video['session_id'] = GetSessionByTVShowId($Video['id']);
                        $Video['upcoming_type'] = 0;
                        $data[] = $Video;
                    }
                }
            }
            if ($type == 5) {

                if ($upcoming_type == 1) {

                    $All_Video = Video::where('video_type', $type)->latest()->get();
                    foreach ($All_Video as $key => $value) {
                        $C_Ids = explode(',', $value['category_id']);
                        if (in_array($category_id, $C_Ids)) {

                            // Thumbnail && Landscape
                            $value['thumbnail'] = Get_Image($this->folder_video, $value['thumbnail']);
                            $value['landscape'] = Get_Image($this->folder_video, $value['landscape']);

                            // Video (320, 480, 720, 1080)
                            if (isset($value['video_320']) && !empty($value['video_320'])) {
                                if ($value['video_upload_type'] == "server_video") {
                                    $value['video_320'] = Get_Video($this->folder_video, $value['video_320']);
                                }
                            }
                            if (isset($value['video_480']) && !empty($value['video_480'])) {
                                if ($value['video_upload_type'] == "server_video") {
                                    $value['video_480'] = Get_Video($this->folder_video, $value['video_480']);
                                }
                            }
                            if (isset($value['video_720']) && !empty($value['video_720'])) {
                                if ($value['video_upload_type'] == "server_video") {
                                    $value['video_720'] = Get_Video($this->folder_video, $value['video_720']);
                                }
                            }
                            if (isset($value['video_1080']) && !empty($value['video_1080'])) {
                                if ($value['video_upload_type'] == "server_video") {
                                    $value['video_1080'] = Get_Video($this->folder_video, $value['video_1080']);
                                }
                            }

                            // Trailer
                            if (isset($value['trailer_url']) && !empty($value['trailer_url'])) {
                                if ($value['trailer_type'] == "server_video") {
                                    $value['trailer_url'] = Get_Video($this->folder_video, $value['trailer_url']);
                                }
                            }

                            // SubTitle_1_2_3
                            if (isset($value['subtitle_1']) && !empty($value['subtitle_1'])) {
                                if ($value['subtitle_type'] == "server_video") {
                                    $value['subtitle_1'] = Get_Video($this->folder_video, $value['subtitle_1']);
                                }
                            }
                            if (isset($value['subtitle_2']) && !empty($value['subtitle_2'])) {
                                if ($value['subtitle_type'] == "server_video") {
                                    $value['subtitle_2'] = Get_Video($this->folder_video, $value['subtitle_2']);
                                }
                            }
                            if (isset($value['subtitle_3']) && !empty($value['subtitle_3'])) {
                                if ($value['subtitle_type'] == "server_video") {
                                    $value['subtitle_3'] = Get_Video($this->folder_video, $value['subtitle_3']);
                                }
                            }

                            $value['stop_time'] = GetStopTimeByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                            $value['is_downloaded'] = Is_DownloadByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                            $value['is_bookmark'] = Is_BookmarkByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                            $value['rent_buy'] = VideoRentBuyByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                            $value['is_rent'] = IsRentVideo($value['id'], $value['video_type']);
                            $value['rent_price'] = GetPriceByRentVideo($value['id'], $value['video_type']);
                            $value['is_buy'] = IsBuyByUser($user_id);
                            $value['category_name'] = GetCategoryNameByIds($value['category_id']);
                            $value['session_id'] = "0";
                            $value['upcoming_type'] = 1;

                            $data[] = $value;
                        }
                    }
                } else if ($upcoming_type == 2) {

                    $TVShow = TVShow::where('video_type', $type)->latest()->get();
                    foreach ($TVShow as $key => $Video) {
                        $C_Ids = explode(',', $Video['category_id']);
                        if (in_array($category_id, $C_Ids)) {

                            // Thumbnail && Landscape
                            $Video['thumbnail'] = Get_Image($this->folder_show, $Video['thumbnail']);
                            $Video['landscape'] = Get_Image($this->folder_show, $Video['landscape']);
                            // Trailer
                            if (isset($Video['trailer_url']) && !empty($Video['trailer_url'])) {
                                if ($Video['trailer_type'] == "server_video") {
                                    $Video['trailer_url'] = Get_Video($this->folder_video, $Video['trailer_url']);
                                }
                            }

                            $Video['stop_time'] = 0;
                            $Video['is_downloaded'] = 0;
                            $Video['is_bookmark'] = Is_BookmarkByUser($user_id, $Video['id'], $Video['type_id'], $Video['video_type']);
                            $Video['rent_buy'] = VideoRentBuyByUser($user_id, $Video['id'], $Video['type_id'], $Video['video_type']);
                            $Video['is_rent'] = IsRentVideo($Video['id'], $Video['video_type']);
                            $Video['rent_price'] = GetPriceByRentVideo($Video['id'], $Video['video_type']);
                            $Video['is_buy'] = IsBuyByUser($user_id);
                            $Video['category_name'] = GetCategoryNameByIds($Video['category_id']);
                            $Video['session_id'] = GetSessionByTVShowId($Video['id']);
                            $Video['upcoming_type'] = 2;
                            $data[] = $Video;
                        }
                    }
                }
            }

            return APIResponse(200, __('api_msg.get_record_successfully'), $data);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function video_by_language(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'language_id' => 'required|numeric',
                ],
                [
                    'language_id.required' => __('api_msg.please_enter_required_fields'),
                ]
            );
            if ($validation->fails()) {

                $errors = $validation->errors()->first('language_id');
                $data['status'] = 400;
                if ($errors) {
                    $data['message'] = $errors;
                }
                return $data;
            }

            $language_id = $request->language_id;
            $user_id = isset($request->user_id) ? $request->user_id : 0;
            $type_id = isset($request->type_id) ? $request->type_id : 0;
            $upcoming_type = isset($request->upcoming_type) ? $request->upcoming_type : 0;

            $check_type = Type::where('id', $type_id)->first();

            $type = 1;
            if (isset($check_type)) {
                $type = $check_type->type;
            }

            $data = array();
            if ($type  == 1) {

                $All_Video = Video::latest()->get();
                foreach ($All_Video as $key => $value) {

                    $C_Ids = explode(',', $value['language_id']);
                    if (in_array($language_id, $C_Ids)) {

                        // Thumbnail && Landscape
                        $value['thumbnail'] = Get_Image($this->folder_video, $value['thumbnail']);
                        $value['landscape'] = Get_Image($this->folder_video, $value['landscape']);

                        // Video (320, 480, 720, 1080)
                        if (isset($value['video_320']) && !empty($value['video_320'])) {
                            if ($value['video_upload_type'] == "server_video") {
                                $value['video_320'] = Get_Video($this->folder_video, $value['video_320']);
                            }
                        }
                        if (isset($value['video_480']) && !empty($value['video_480'])) {
                            if ($value['video_upload_type'] == "server_video") {
                                $value['video_480'] = Get_Video($this->folder_video, $value['video_480']);
                            }
                        }
                        if (isset($value['video_720']) && !empty($value['video_720'])) {
                            if ($value['video_upload_type'] == "server_video") {
                                $value['video_720'] = Get_Video($this->folder_video, $value['video_720']);
                            }
                        }
                        if (isset($value['video_1080']) && !empty($value['video_1080'])) {
                            if ($value['video_upload_type'] == "server_video") {
                                $value['video_1080'] = Get_Video($this->folder_video, $value['video_1080']);
                            }
                        }

                        // Trailer
                        if (isset($value['trailer_url']) && !empty($value['trailer_url'])) {
                            if ($value['trailer_type'] == "server_video") {
                                $value['trailer_url'] = Get_Video($this->folder_video, $value['trailer_url']);
                            }
                        }

                        // SubTitle_1_2_3
                        if (isset($value['subtitle_1']) && !empty($value['subtitle_1'])) {
                            if ($value['subtitle_type'] == "server_video") {
                                $value['subtitle_1'] = Get_Video($this->folder_video, $value['subtitle_1']);
                            }
                        }
                        if (isset($value['subtitle_2']) && !empty($value['subtitle_2'])) {
                            if ($value['subtitle_type'] == "server_video") {
                                $value['subtitle_2'] = Get_Video($this->folder_video, $value['subtitle_2']);
                            }
                        }
                        if (isset($value['subtitle_3']) && !empty($value['subtitle_3'])) {
                            if ($value['subtitle_type'] == "server_video") {
                                $value['subtitle_3'] = Get_Video($this->folder_video, $value['subtitle_3']);
                            }
                        }

                        $value['stop_time'] = GetStopTimeByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                        $value['is_downloaded'] = Is_DownloadByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                        $value['is_bookmark'] = Is_BookmarkByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                        $value['rent_buy'] = VideoRentBuyByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                        $value['is_rent'] = IsRentVideo($value['id'], $value['video_type']);
                        $value['rent_price'] = GetPriceByRentVideo($value['id'], $value['video_type']);
                        $value['is_buy'] = IsBuyByUser($user_id);
                        $value['category_name'] = GetCategoryNameByIds($value['category_id']);
                        $value['session_id'] = "0";
                        $value['upcoming_type'] = 0;

                        $data[] = $value;
                    }
                }
            }
            if ($type  == 2) {

                $TVShow = TVShow::latest()->get();
                foreach ($TVShow as $key => $value) {
                    $C_Ids = explode(',', $value['language_id']);
                    if (in_array($language_id, $C_Ids)) {

                        // Thumbnail && Landscape
                        $value['thumbnail'] = Get_Image($this->folder_show, $value['thumbnail']);
                        $value['landscape'] = Get_Image($this->folder_show, $value['landscape']);
                        // Trailer
                        if (isset($value['trailer_url']) && !empty($value['trailer_url'])) {
                            if ($value['trailer_type'] == "server_video") {
                                $value['trailer_url'] = Get_Video($this->folder_video, $value['trailer_url']);
                            }
                        }

                        $value['stop_time'] = 0;
                        $value['is_downloaded'] = 0;
                        $value['is_bookmark'] = Is_BookmarkByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                        $value['rent_buy'] = VideoRentBuyByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                        $value['is_rent'] = IsRentVideo($value['id'], $value['video_type']);
                        $value['rent_price'] = GetPriceByRentVideo($value['id'], $value['video_type']);
                        $value['is_buy'] = IsBuyByUser($user_id);
                        $value['category_name'] = GetCategoryNameByIds($value['category_id']);
                        $value['session_id'] = GetSessionByTVShowId($value['id']);
                        $value['upcoming_type'] = 0;

                        $data[] = $value;
                    }
                }
            }
            if ($type == 5) {

                if ($upcoming_type == 1) {

                    $All_Video = Video::latest()->get();
                    foreach ($All_Video as $key => $value) {

                        $C_Ids = explode(',', $value['language_id']);
                        if (in_array($language_id, $C_Ids)) {

                            // Thumbnail && Landscape
                            $value['thumbnail'] = Get_Image($this->folder_video, $value['thumbnail']);
                            $value['landscape'] = Get_Image($this->folder_video, $value['landscape']);

                            // Video (320, 480, 720, 1080)
                            if (isset($value['video_320']) && !empty($value['video_320'])) {
                                if ($value['video_upload_type'] == "server_video") {
                                    $value['video_320'] = Get_Video($this->folder_video, $value['video_320']);
                                }
                            }
                            if (isset($value['video_480']) && !empty($value['video_480'])) {
                                if ($value['video_upload_type'] == "server_video") {
                                    $value['video_480'] = Get_Video($this->folder_video, $value['video_480']);
                                }
                            }
                            if (isset($value['video_720']) && !empty($value['video_720'])) {
                                if ($value['video_upload_type'] == "server_video") {
                                    $value['video_720'] = Get_Video($this->folder_video, $value['video_720']);
                                }
                            }
                            if (isset($value['video_1080']) && !empty($value['video_1080'])) {
                                if ($value['video_upload_type'] == "server_video") {
                                    $value['video_1080'] = Get_Video($this->folder_video, $value['video_1080']);
                                }
                            }

                            // Trailer
                            if (isset($value['trailer_url']) && !empty($value['trailer_url'])) {
                                if ($value['trailer_type'] == "server_video") {
                                    $value['trailer_url'] = Get_Video($this->folder_video, $value['trailer_url']);
                                }
                            }

                            // SubTitle_1_2_3
                            if (isset($value['subtitle_1']) && !empty($value['subtitle_1'])) {
                                if ($value['subtitle_type'] == "server_video") {
                                    $value['subtitle_1'] = Get_Video($this->folder_video, $value['subtitle_1']);
                                }
                            }
                            if (isset($value['subtitle_2']) && !empty($value['subtitle_2'])) {
                                if ($value['subtitle_type'] == "server_video") {
                                    $value['subtitle_2'] = Get_Video($this->folder_video, $value['subtitle_2']);
                                }
                            }
                            if (isset($value['subtitle_3']) && !empty($value['subtitle_3'])) {
                                if ($value['subtitle_type'] == "server_video") {
                                    $value['subtitle_3'] = Get_Video($this->folder_video, $value['subtitle_3']);
                                }
                            }

                            $value['stop_time'] = GetStopTimeByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                            $value['is_downloaded'] = Is_DownloadByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                            $value['is_bookmark'] = Is_BookmarkByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                            $value['rent_buy'] = VideoRentBuyByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                            $value['is_rent'] = IsRentVideo($value['id'], $value['video_type']);
                            $value['rent_price'] = GetPriceByRentVideo($value['id'], $value['video_type']);
                            $value['is_buy'] = IsBuyByUser($user_id);
                            $value['category_name'] = GetCategoryNameByIds($value['category_id']);
                            $value['session_id'] = "0";
                            $value['upcoming_type'] = 1;

                            $data[] = $value;
                        }
                    }
                } else if ($upcoming_type == 2) {

                    $TVShow = TVShow::latest()->get();
                    foreach ($TVShow as $key => $value) {
                        $C_Ids = explode(',', $value['language_id']);
                        if (in_array($language_id, $C_Ids)) {

                            // Thumbnail && Landscape
                            $value['thumbnail'] = Get_Image($this->folder_show, $value['thumbnail']);
                            $value['landscape'] = Get_Image($this->folder_show, $value['landscape']);
                            // Trailer
                            if (isset($value['trailer_url']) && !empty($value['trailer_url'])) {
                                if ($value['trailer_type'] == "server_video") {
                                    $value['trailer_url'] = Get_Video($this->folder_video, $value['trailer_url']);
                                }
                            }

                            $value['stop_time'] = 0;
                            $value['is_downloaded'] = 0;
                            $value['is_bookmark'] = Is_BookmarkByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                            $value['rent_buy'] = VideoRentBuyByUser($user_id, $value['id'], $value['type_id'], $value['video_type']);
                            $value['is_rent'] = IsRentVideo($value['id'], $value['video_type']);
                            $value['rent_price'] = GetPriceByRentVideo($value['id'], $value['video_type']);
                            $value['is_buy'] = IsBuyByUser($user_id);
                            $value['category_name'] = GetCategoryNameByIds($value['category_id']);
                            $value['session_id'] = GetSessionByTVShowId($value['id']);
                            $value['upcoming_type'] = 2;
                            $data[] = $value;
                        }
                    }
                }
            }

            return APIResponse(200, __('api_msg.get_record_successfully'), $data);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function video_view(Request $request)
    {
        try {
            $validation = Validator::make(
                $request->all(),
                [
                    'video_type' => 'required|numeric',
                    'video_id' => 'required|numeric',
                    'user_id' => 'required|numeric',
                ],
                [
                    'video_type.required' => __('api_msg.please_enter_required_fields'),
                    'video_id.required' => __('api_msg.please_enter_required_fields'),
                    'user_id.required' => __('api_msg.please_enter_required_fields'),
                ]
            );
            if ($validation->fails()) {

                $errors = $validation->errors()->first('video_type');
                $errors1 = $validation->errors()->first('video_id');
                $errors2 = $validation->errors()->first('user_id');
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

            $video_type = $request->video_type;
            $video_id = $request->video_id;
            $other_id = isset($request->other_id) ? $request->other_id : 0;
            $user_id = isset($request->user_id) ? $request->user_id : 0;

            if ($video_type == 1) {

                Video::where('id', $video_id)->increment('view', 1);
            } else if ($video_type == 2) {

                TVShow::where('id', $other_id)->increment('view', 1);
                TVShowVideo::where('show_id', $other_id)->where('id', $video_id)->increment('view', 1);
            }

            return APIResponse(200, "Video View Successfully.", []);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    // ================ Rent API's ================
    public function rent_video_list()
    {
        try {

            $data['status'] = 200;
            $data['message'] = __('api_msg.get_record_successfully');
            $data['result'] = array();
            $data['video'] = array();
            $data['tvshow'] = array();

            $Rent_Data = RentVideo::where('status', 1)->get();

            foreach ($Rent_Data as $key => $value) {

                if ($value['video_type'] == 1) {

                    $Video = Video::where('id', $value['video_id'])->first();
                    if (!empty($Video)) {

                        // Thumbnail && Landscape
                        $Video['thumbnail'] = Get_Image($this->folder_video, $Video['thumbnail']);
                        $Video['landscape'] = Get_Image($this->folder_video, $Video['landscape']);

                        // Video (320, 480, 720, 1080)
                        if (isset($Video['video_320']) && !empty($Video['video_320'])) {
                            if ($Video['video_upload_type'] == "server_video") {
                                $Video['video_320'] = Get_Video($this->folder_video, $Video['video_320']);
                            }
                        }
                        if (isset($Video['video_480']) && !empty($Video['video_480'])) {
                            if ($Video['video_upload_type'] == "server_video") {
                                $Video['video_480'] = Get_Video($this->folder_video, $Video['video_480']);
                            }
                        }
                        if (isset($Video['video_720']) && !empty($Video['video_720'])) {
                            if ($Video['video_upload_type'] == "server_video") {
                                $Video['video_720'] = Get_Video($this->folder_video, $Video['video_720']);
                            }
                        }
                        if (isset($Video['video_1080']) && !empty($Video['video_1080'])) {
                            if ($Video['video_upload_type'] == "server_video") {
                                $Video['video_1080'] = Get_Video($this->folder_video, $Video['video_1080']);
                            }
                        }

                        // Trailer
                        if (isset($Video['trailer_url']) && !empty($Video['trailer_url'])) {
                            if ($Video['trailer_type'] == "server_video") {
                                $Video['trailer_url'] = Get_Video($this->folder_video, $Video['trailer_url']);
                            }
                        }

                        // SubTitle_1_2_3
                        if (isset($Video['subtitle_1']) && !empty($Video['subtitle_1'])) {
                            if ($Video['subtitle_type'] == "server_video") {
                                $Video['subtitle_1'] = Get_Video($this->folder_video, $Video['subtitle_1']);
                            }
                        }
                        if (isset($Video['subtitle_2']) && !empty($Video['subtitle_2'])) {
                            if ($Video['subtitle_type'] == "server_video") {
                                $Video['subtitle_2'] = Get_Video($this->folder_video, $Video['subtitle_2']);
                            }
                        }
                        if (isset($Video['subtitle_3']) && !empty($Video['subtitle_3'])) {
                            if ($Video['subtitle_type'] == "server_video") {
                                $Video['subtitle_3'] = Get_Video($this->folder_video, $Video['subtitle_3']);
                            }
                        }

                        $Video['stop_time'] = 0;
                        $Video['is_downloaded'] = 0;
                        $Video['is_bookmark'] = 0;
                        $Video['rent_buy'] = 0;
                        $Video['is_rent'] = IsRentVideo($Video['id'], $Video['video_type']);
                        $Video['rent_price'] = GetPriceByRentVideo($Video['id'], $Video['video_type']);
                        $Video['is_buy'] = 0;
                        $Video['category_name'] = GetCategoryNameByIds($Video['category_id']);
                        $Video['session_id'] = "0";
                        $Video['upcoming_type'] = 0;

                        $Video['rent_time'] = $value['time'];
                        $Video['rent_type'] = $value['type'];

                        $data['video'][] = $Video;
                    }
                } elseif ($value['video_type'] == 2) {

                    $Video = TVShow::where('id', $value['video_id'])->first();
                    if (!empty($Video)) {

                        // Thumbnail && Landscape
                        $Video['thumbnail'] = Get_Image($this->folder_show, $Video['thumbnail']);
                        $Video['landscape'] = Get_Image($this->folder_show, $Video['landscape']);
                        // Trailer
                        if (isset($Video['trailer_url']) && !empty($Video['trailer_url'])) {
                            if ($Video['trailer_type'] == "server_video") {
                                $Video['trailer_url'] = Get_Video($this->folder_video, $Video['trailer_url']);
                            }
                        }

                        $Video['stop_time'] = 0;
                        $Video['is_downloaded'] = 0;
                        $Video['is_bookmark'] = 0;
                        $Video['rent_buy'] = 0;
                        $Video['is_rent'] = IsRentVideo($Video['id'], $Video['video_type']);
                        $Video['rent_price'] = GetPriceByRentVideo($Video['id'], $Video['video_type']);
                        $Video['is_buy'] = 0;
                        $Video['category_name'] = GetCategoryNameByIds($Video['category_id']);
                        $Video['session_id'] = GetSessionByTVShowId($Video['id']);
                        $Video['upcoming_type'] = 0;

                        $Video['rent_time'] = $value['time'];
                        $Video['rent_type'] = $value['type'];

                        $data['tvshow'][] = $Video;
                    }
                }
            }

            return $data;
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function user_rent_video_list(Request $request)
    {
        try {

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

                $errors = $validation->errors()->first('user_id');
                $data['status'] = 400;
                if ($errors) {
                    $data['message'] = $errors;
                }
                return $data;
            }

            // Delete Expriy data
            $all_data = RentTransction::get();
            for ($i = 0; $i < count($all_data); $i++) {
                if ($all_data[$i]['expiry_date'] < date("Y-m-d")) {
                    $all_data[$i]->status = 0;
                    $all_data[$i]->save();
                }
            }

            $user_id = $request->user_id;

            $data['status'] = 200;
            $data['message'] = __('api_msg.get_record_successfully');
            $data['result'] = array();
            $data['video'] = array();
            $data['tvshow'] = array();

            $Rent_Data = RentTransction::where('user_id', $user_id)->where('status', 1)->get();

            foreach ($Rent_Data as $key => $value) {

                if ($value['video_type'] == 1) {

                    $Video = Video::where('id', $value['video_id'])->first();
                    if (!empty($Video)) {

                        // Thumbnail && Landscape
                        $Video['thumbnail'] = Get_Image($this->folder_video, $Video['thumbnail']);
                        $Video['landscape'] = Get_Image($this->folder_video, $Video['landscape']);

                        // Video (320, 480, 720, 1080)
                        if (isset($Video['video_320']) && !empty($Video['video_320'])) {
                            if ($Video['video_upload_type'] == "server_video") {
                                $Video['video_320'] = Get_Video($this->folder_video, $Video['video_320']);
                            }
                        }
                        if (isset($Video['video_480']) && !empty($Video['video_480'])) {
                            if ($Video['video_upload_type'] == "server_video") {
                                $Video['video_480'] = Get_Video($this->folder_video, $Video['video_480']);
                            }
                        }
                        if (isset($Video['video_720']) && !empty($Video['video_720'])) {
                            if ($Video['video_upload_type'] == "server_video") {
                                $Video['video_720'] = Get_Video($this->folder_video, $Video['video_720']);
                            }
                        }
                        if (isset($Video['video_1080']) && !empty($Video['video_1080'])) {
                            if ($Video['video_upload_type'] == "server_video") {
                                $Video['video_1080'] = Get_Video($this->folder_video, $Video['video_1080']);
                            }
                        }

                        // Trailer
                        if (isset($Video['trailer_url']) && !empty($Video['trailer_url'])) {
                            if ($Video['trailer_type'] == "server_video") {
                                $Video['trailer_url'] = Get_Video($this->folder_video, $Video['trailer_url']);
                            }
                        }

                        // SubTitle_1_2_3
                        if (isset($Video['subtitle_1']) && !empty($Video['subtitle_1'])) {
                            if ($Video['subtitle_type'] == "server_video") {
                                $Video['subtitle_1'] = Get_Video($this->folder_video, $Video['subtitle_1']);
                            }
                        }
                        if (isset($Video['subtitle_2']) && !empty($Video['subtitle_2'])) {
                            if ($Video['subtitle_type'] == "server_video") {
                                $Video['subtitle_2'] = Get_Video($this->folder_video, $Video['subtitle_2']);
                            }
                        }
                        if (isset($Video['subtitle_3']) && !empty($Video['subtitle_3'])) {
                            if ($Video['subtitle_type'] == "server_video") {
                                $Video['subtitle_3'] = Get_Video($this->folder_video, $Video['subtitle_3']);
                            }
                        }

                        $Video['stop_time'] = GetStopTimeByUser($user_id, $Video['id'], $Video['type_id'], $Video['video_type']);
                        $Video['is_downloaded'] = Is_DownloadByUser($user_id, $Video['id'], $Video['type_id'], $Video['video_type']);
                        $Video['is_bookmark'] = Is_BookmarkByUser($user_id, $Video['id'], $Video['type_id'], $Video['video_type']);
                        $Video['rent_buy'] = VideoRentBuyByUser($user_id, $Video['id'], $Video['type_id'], $Video['video_type']);
                        $Video['is_rent'] = IsRentVideo($Video['id'], $Video['video_type']);
                        $Video['rent_price'] = GetPriceByRentVideo($Video['id'], $Video['video_type']);
                        $Video['is_buy'] = IsBuyByUser($user_id);
                        $Video['category_name'] = GetCategoryNameByIds($Video['category_id']);
                        $Video['session_id'] = "0";
                        $Video['upcoming_type'] = 0;

                        $data['video'][] = $Video;
                    }
                } elseif ($value['video_type'] == 2) {

                    $Video = TVShow::where('id', $value['video_id'])->first();
                    if (!empty($Video)) {

                        // Thumbnail && Landscape
                        $Video['thumbnail'] = Get_Image($this->folder_show, $Video['thumbnail']);
                        $Video['landscape'] = Get_Image($this->folder_show, $Video['landscape']);
                        // Trailer
                        if (isset($Video['trailer_url']) && !empty($Video['trailer_url'])) {
                            if ($Video['trailer_type'] == "server_video") {
                                $Video['trailer_url'] = Get_Video($this->folder_video, $Video['trailer_url']);
                            }
                        }

                        $Video['stop_time'] = 0;
                        $Video['is_downloaded'] = 0;
                        $Video['is_bookmark'] = Is_BookmarkByUser($user_id, $Video['id'], $Video['type_id'], $Video['video_type']);
                        $Video['rent_buy'] = VideoRentBuyByUser($user_id, $Video['id'], $Video['type_id'], $Video['video_type']);
                        $Video['is_rent'] = IsRentVideo($Video['id'], $Video['video_type']);
                        $Video['rent_price'] = GetPriceByRentVideo($Video['id'], $Video['video_type']);
                        $Video['is_buy'] = IsBuyByUser($user_id);
                        $Video['category_name'] = GetCategoryNameByIds($Video['category_id']);
                        $Video['session_id'] = GetSessionByTVShowId($Video['id']);
                        $Video['upcoming_type'] = 0;

                        $data['tvshow'][] = $Video;
                    }
                }
            }
            return $data;
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function add_rent_transaction(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'user_id' => 'required|numeric',
                    'video_id' => 'required|numeric',
                    'price' => 'required|numeric',
                    'type_id' => 'required|numeric',
                    'video_type' => 'required|numeric',
                ],
                [
                    'user_id.required' => __('api_msg.please_enter_required_fields'),
                    'video_id.required' => __('api_msg.please_enter_required_fields'),
                    'price.required' => __('api_msg.please_enter_required_fields'),
                    'type_id.required' => __('api_msg.please_enter_required_fields'),
                    'video_type.required' => __('api_msg.please_enter_required_fields'),
                ]
            );
            if ($validation->fails()) {

                $errors = $validation->errors()->first('user_id');
                $errors2 = $validation->errors()->first('video_id');
                $errors1 = $validation->errors()->first('price');
                $errors3 = $validation->errors()->first('type_id');
                $errors4 = $validation->errors()->first('video_type');
                $data['status'] = 400;
                if ($errors) {
                    $data['message'] = $errors;
                } elseif ($errors1) {
                    $data['message'] = $errors1;
                } elseif ($errors2) {
                    $data['message'] = $errors2;
                } elseif ($errors3) {
                    $data['message'] = $errors3;
                } elseif ($errors4) {
                    $data['message'] = $errors4;
                }
                return $data;
            }

            $user_id = $request->user_id;
            $video_id = $request->video_id;
            $price = $request->price;
            $type_id = $request->type_id;
            $video_type = $request->video_type;
            $unique_id = isset($request->unique_id) ? $request->unique_id : "";
            $description = isset($request->description) ? $request->description : "";
            $payment_id = isset($request->payment_id) ? $request->payment_id : "";
            $currency_code = isset($request->currency_code) ? $request->currency_code : currency_code();

            $Rent_Video = RentVideo::where('video_id', $video_id)->where('type_id', $type_id)->where('video_type', $video_type)->where('status', '1')->first();
            if (!empty($Rent_Video)) {
                $Edate = date("Y-m-d", strtotime("$Rent_Video->time $Rent_Video->type"));
            } else {
                return APIResponse(400, __('api_msg.please_enter_right_rent_video'));
            }

            $insert = new RentTransction();
            $insert->user_id = $user_id;
            $insert->unique_id = $unique_id;
            $insert->video_id = $video_id;
            $insert->price = $price;
            $insert->type_id = $type_id;
            $insert->video_type = $video_type;
            $insert->status = 1;
            $insert->expiry_date = $Edate;
            $insert->description = $description;
            $insert->payment_id = $payment_id;
            $insert->currency_code = $currency_code;

            if ($insert->save()) {

                $user_data = Users::where('id', $user_id)->first();
                if ($video_type ==  1) {
                    $video_data = Video::where('id', $video_id)->first();
                } elseif ($video_type == 2) {
                    $video_data = TVShow::where('id', $video_id)->first();
                }

                if (isset($user_data) && isset($video_data)) {

                    // Send Mail (Type = 1- Register Mail, 2 Transaction Mail)
                    Send_Mail(2, $user_data->email);
                }

                return APIResponse(200, __('api_msg.add_successfully'), []);
            } else {
                return APIResponse(400, __('api_msg.data_not_save'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    // ================ Package && Trnasaction API's ================
    public function get_package(Request $request)
    {
        try {

            $all_data = Transction::get();
            for ($i = 0; $i < count($all_data); $i++) {
                if ($all_data[$i]['expiry_date'] <= date("Y-m-d")) {
                    $all_data[$i]->status = 0;
                    $all_data[$i]->save();
                }
            }

            $user_id = isset($request->user_id) ? $request->user_id : 0;

            $data['status'] = 200;
            $data['message'] = __('api_msg.get_record_successfully');
            $data['result'] = [];

            $Package_Data = Package::select('id', 'name', 'price', 'time', 'type', 'type_id', 'android_product_package', 'ios_product_package')->get();

            foreach ($Package_Data as $key => $value) {
                $Data = Package_Detail::where('package_id', $value['id'])->get();
                $value['data'] = $Data;

                $Transction_Data = Transction::where('user_id', $user_id)->where('package_id', $value['id'])->where('status', '1')->first();
                if (!empty($Transction_Data)) {
                    $value['is_buy'] = 1;
                } else {
                    $value['is_buy'] = 0;
                }
                $data['result'][] = $value;
            }
            return $data;
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function add_transaction(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'user_id' => 'required|numeric',
                    'package_id' => 'required|numeric',
                    'amount' => 'required|numeric',
                ],
                [
                    'user_id.required' => __('api_msg.please_enter_required_fields'),
                    'package_id.required' => __('api_msg.please_enter_required_fields'),
                    'amount.required' => __('api_msg.please_enter_required_fields'),
                ]
            );
            if ($validation->fails()) {

                $errors = $validation->errors()->first('user_id');
                $errors1 = $validation->errors()->first('package_id');
                $errors2 = $validation->errors()->first('amount');
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

            $user_id = $request->user_id;
            $package_id = $request->package_id;
            $amount = $request->amount;
            $description = isset($request->description) ? $request->description : "";
            $payment_id = isset($request->payment_id) ? $request->payment_id : "";
            $currency_code = isset($request->currency_code) ? $request->currency_code : currency_code();
            $unique_id = isset($request->unique_id) ? $request->unique_id : "";

            $Pdata = Package::where('id', $package_id)->where('status', '1')->first();
            if (!empty($Pdata)) {
                $Edate = date("Y-m-d", strtotime("$Pdata->time $Pdata->type"));
            } else {
                return APIResponse(400, __('api_msg.please_enter_right_package_id'));
            }

            $insert = new Transction();
            $insert->user_id = $user_id;
            $insert->unique_id = $unique_id;
            $insert->package_id = $package_id;
            $insert->description = $description;
            $insert->amount = $amount;
            $insert->payment_id = $payment_id;
            $insert->currency_code = $currency_code;
            $insert->expiry_date = $Edate;
            $insert->status = 1;
            $insert->bank_reference = isset($request->bank_reference) ? $request->bank_reference : "";
            $insert->cf_payment_id = isset($request->cf_payment_id) ? $request->cf_payment_id : "";
            $insert->payment_completion_time = !empty($request->payment_completion_time) ? date('Y-m-d H:i:s', strtotime($request->payment_completion_time)) : null;
            $insert->payment_group = isset($request->payment_group) ? $request->payment_group : "";
            $insert->payment_status = isset($request->payment_status) ? $request->payment_status : "";
            $insert->payment_response = isset($request->payment_response) ? $request->payment_response : "";

            if ($insert->save()) {

                $user_data = Users::where('id', $user_id)->first();

                if (isset($user_data)) {

                    // Expiry Date
                    $user_data->expiry_date = $Edate;
                    $user_data->save();

                    // Send Mail (Type = 1- Register Mail, 2 Transaction Mail)
                    Send_Mail(2, $user_data->email);
                }

                return APIResponse(200, __('api_msg.add_successfully'), []);
            } else {
                return APIResponse(400, __('api_msg.data_not_save'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
	
    public function subscription_list(Request $request)
    {
        try {

            $all_data = Transction::get();
            for ($i = 0; $i < count($all_data); $i++) {
                if ($all_data[$i]['expiry_date'] <= date("Y-m-d")) {
                    $all_data[$i]->status = '0';
                    $all_data[$i]->save();
                }
            }

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

                $errors = $validation->errors()->first('user_id');
                $data['status'] = 400;
                if ($errors) {
                    $data['message'] = $errors;
                }
                return $data;
            }

            $user_id = $request->user_id;
            $result = Transction::where('user_id', $user_id)->with('package')->latest()->get();

            foreach ($result as $key => $value) {
                if ($value['package'] != null) {
                    $value['package_name'] = $value['package']['name'];
                    $value['package_price'] = $value['package']['price'];
                } else {
                    $value['package_name'] = "";
                    $value['package_price'] = 0;
                }

                $value['data'] = $value['created_at']->format('Y-m-d');

                unset($value['package']);
            }

            return APIResponse(200, __('api_msg.get_record_successfully'), $result);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    // ================ Add/Remove API's ================
    public function add_continue_watching(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'user_id' => 'required|numeric',
                    'video_type' => 'required|numeric',
                    'video_id' => 'required|numeric',
                    'stop_time' => 'required|numeric',
                ],
                [
                    'user_id.required' => __('api_msg.please_enter_required_fields'),
                    'video_type.required' => __('api_msg.please_enter_required_fields'),
                    'video_id.required' => __('api_msg.please_enter_required_fields'),
                    'stop_time.required' => __('api_msg.please_enter_required_fields'),
                ]
            );
            if ($validation->fails()) {

                $errors = $validation->errors()->first('user_id');
                $errors1 = $validation->errors()->first('video_type');
                $errors2 = $validation->errors()->first('video_id');
                $errors3 = $validation->errors()->first('stop_time');
                $data['status'] = 400;
                if ($errors) {
                    $data['message'] = $errors;
                } elseif ($errors1) {
                    $data['message'] = $errors1;
                } elseif ($errors2) {
                    $data['message'] = $errors2;
                } elseif ($errors3) {
                    $data['message'] = $errors3;
                }
                return $data;
            }

            $user_id = $request->user_id;
            $video_id = $request->video_id;
            $video_type = $request->video_type;
            $stop_time = $request->stop_time;
            $type_id = isset($request->type_id) ? $request->type_id : 0;

            $data = Video_Watch::where('user_id', $user_id)->where('video_id', $video_id)->where('video_type', $video_type)->first();
            if (!empty($data)) {

                Video_Watch::where('id', $data['id'])->update(['stop_time' => $stop_time, 'status' => '1', 'type_id' => $type_id]);

                $Data = Video_Watch::where('id', $data['id'])->first();
                $Data['status'] = (int) $Data['status'];
                return APIResponse(200, __('api_msg.add_successfully'));
            } else {

                $insert = new Video_Watch();
                $insert->user_id = $user_id;
                $insert->video_id = $video_id;
                $insert->type_id = $type_id;
                $insert->video_type = $video_type;
                $insert->stop_time = $stop_time;
                $insert->status = '1';
                if ($insert->save()) {

                    $Data = Video_Watch::where('id', $insert['id'])->first();
                    $Data['status'] = (int) $Data['status'];
                    return APIResponse(200, __('api_msg.add_successfully'));
                } else {
                    return APIResponse(400, __('api_msg.data_not_save'));
                }
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function remove_continue_watching(Request $request)
    {
        try {
            $validation = Validator::make(
                $request->all(),
                [
                    'user_id' => 'required|numeric',
                    'video_id' => 'required|numeric',
                    'video_type' => 'required|numeric',
                ],
                [
                    'user_id.required' => __('api_msg.please_enter_required_fields'),
                    'video_id.required' => __('api_msg.please_enter_required_fields'),
                    'video_type.required' => __('api_msg.please_enter_required_fields'),
                ]
            );
            if ($validation->fails()) {

                $errors = $validation->errors()->first('user_id');
                $errors1 = $validation->errors()->first('video_type');
                $errors2 = $validation->errors()->first('video_id');
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

            $user_id = $request->user_id;
            $video_id = $request->video_id;
            $video_type = $request->video_type;

            $remove = Video_Watch::where('user_id', $user_id)->where('video_type', $video_type)->where('video_id', $video_id)->first();
            if (!empty($remove)) {
                $remove->status = '0';
                $remove->update();
            }
            return APIResponse(200, __('api_msg.delete_success'), []);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function add_remove_bookmark(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'user_id' => 'required|numeric',
                    'video_id' => 'required|numeric',
                    'video_type' => 'required|numeric',
                    'type_id' => 'required|numeric',
                ],
                [
                    'user_id.required' => __('api_msg.please_enter_required_fields'),
                    'video_id.required' => __('api_msg.please_enter_required_fields'),
                    'video_type.required' => __('api_msg.please_enter_required_fields'),
                    'type_id.required' => __('api_msg.please_enter_required_fields'),
                ]
            );
            if ($validation->fails()) {

                $errors = $validation->errors()->first('user_id');
                $errors1 = $validation->errors()->first('video_type');
                $errors2 = $validation->errors()->first('video_id');
                $errors3 = $validation->errors()->first('type_id');
                $data['status'] = 400;
                if ($errors) {
                    $data['message'] = $errors;
                } elseif ($errors1) {
                    $data['message'] = $errors1;
                } elseif ($errors2) {
                    $data['message'] = $errors2;
                } elseif ($errors3) {
                    $data['message'] = $errors3;
                }
                return $data;
            }

            $user_id = $request->user_id;
            $video_id = $request->video_id;
            $video_type = $request->video_type;
            $type_id = $request->type_id;

            $data = Bookmark::where('user_id', $user_id)->where('video_id', $video_id)->where('type_id', $type_id)->where('video_type', $video_type)->first();

            if (!empty($data)) {

                if ($data['status'] == 1) {

                    $data->status = 0;
                    $data->update();
                    return APIResponse(200, __('api_msg.delete_success'), []);
                } else {

                    $data->status = 1;
                    $data->update();
                    return APIResponse(200, __('api_msg.add_successfully'), []);
                }
            } else {

                $insert = new Bookmark();
                $insert->user_id = $user_id;
                $insert->video_id = $video_id;
                $insert->type_id = $type_id;
                $insert->video_type = $video_type;
                $insert->status = '1';

                if ($insert->save()) {
                    return APIResponse(200, __('api_msg.add_successfully'), []);
                } else {
                    return APIResponse(400, __('api_msg.data_not_save'));
                }
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function add_remove_download(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'user_id' => 'required|numeric',
                    'video_id' => 'required|numeric',
                    'video_type' => 'required|numeric',
                    'type_id' => 'required|numeric',
                ],
                [
                    'user_id.required' => __('api_msg.please_enter_required_fields'),
                    'video_id.required' => __('api_msg.please_enter_required_fields'),
                    'video_type.required' => __('api_msg.please_enter_required_fields'),
                    'type_id.required' => __('api_msg.please_enter_required_fields'),
                ]
            );
            if ($validation->fails()) {

                $errors = $validation->errors()->first('user_id');
                $errors1 = $validation->errors()->first('video_type');
                $errors2 = $validation->errors()->first('video_id');
                $errors3 = $validation->errors()->first('type_id');
                $data['status'] = 400;
                if ($errors) {
                    $data['message'] = $errors;
                } elseif ($errors1) {
                    $data['message'] = $errors1;
                } elseif ($errors2) {
                    $data['message'] = $errors2;
                } elseif ($errors3) {
                    $data['message'] = $errors3;
                }
                return $data;
            }

            $user_id = $request->user_id;
            $video_id = $request->video_id;
            $video_type = $request->video_type;
            $type_id = $request->type_id;
            $other_id = isset($request->other_id) ? $request->other_id : 0;

            $data = Download::where('user_id', $user_id)->where('video_id', $video_id)->where('type_id', $type_id)->where('video_type', $video_type)->where('other_id', $other_id)->first();

            if (!empty($data)) {

                $data->delete();
                return APIResponse(200, __('api_msg.delete_success'), []);
            } else {

                $insert = new Download();
                $insert->user_id = $user_id;
                $insert->video_id = $video_id;
                $insert->type_id = $type_id;
                $insert->video_type = $video_type;
                $insert->other_id = $other_id;

                if ($insert->save()) {
                    return APIResponse(200, __('api_msg.add_successfully'), []);
                } else {
                    return APIResponse(400, __('api_msg.data_not_save'));
                }
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    // =============== Not Working =============== 
    public function get_payment_token(Request $request)
    {
        try {
            $validation = Validator::make(
                $request->all(),
                [
                    'MID' => 'required',
                    'order_id' => 'required',
                    'CHANNEL_ID' => 'required',
                    'CUST_ID' => 'required',
                    'TXN_AMOUNT' => 'required',
                    'WEBSITE' => 'required',
                    'CALLBACK_URL' => 'required',
                    'INDUSTRY_TYPE_ID' => 'required',
                ]
            );
            if ($validation->fails()) {

                $data['status'] = 400;
                $data['message'] = __('api_msg.please_enter_required_fields');
                return $data;
            }

            $data['MID'] = $request->MID;
            $data['order_id'] = $request->order_id;
            $data['CHANNEL_ID'] = $request->CHANNEL_ID;
            $data['CUST_ID'] = $request->CUST_ID;
            $data['TXN_AMOUNT'] = $request->TXN_AMOUNT;
            $data['WEBSITE'] = $request->WEBSITE;
            $data['CALLBACK_URL'] = $request->CALLBACK_URL;
            $data['INDUSTRY_TYPE_ID'] = $request->INDUSTRY_TYPE_ID;

            $ChackSum = Paytm($data);
            $array['paytmChecksum'] = $ChackSum;
            $array['verifySignature'] = true;

            $final_data['status'] = 200;
            $final_data['message'] = "Success";
            $final_data['result'] = $array;
            return $final_data;
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
	
	public function popupBannerDetails () {
		$general_setting = General_Setting::select('*')->whereIn('key', ['popup_banner_status', 'popup_banner_live_status', 'popup_banner_image', 'popup_banner_link'])->get();
		$retData = [];
		foreach ($general_setting as $row) {
			if($row->key == 'popup_banner_status' || $row->key == 'popup_banner_live_status') {
				$retData[$row->key] = ($row->value == 'true') ? true : false;
			} else {
				$retData[$row->key] = $row->value;
			}
		}
		return APIResponse(200, __('api_msg.get_status_successfully'), $retData);
	}
	
	public function whatsupButtonStatus () {
		$general_setting = General_Setting::select('*')->whereIn('key', ['show_whats_up_button'])->get();
		$retData = [];
		foreach ($general_setting as $row) {
			if($row->key == 'show_whats_up_button') {
				$retData[$row->key] = ($row->value == 'true') ? true : false;
			} else {
				$retData[$row->key] = $row->value;
			}
		}
		return APIResponse(200, __('api_msg.get_status_successfully'), $retData);
	}
	
	public function homeBannerDetails () {
		$retData = General_Setting::whereIn('key', ['home_banner_url', 'home_banner_image'])->pluck('value','key');
		return APIResponse(200, __('api_msg.get_status_successfully'), $retData);
	}

    public function section_list_new(Request $request) {
        try {
            $validation = Validator::make(
                $request->all(),
                [
                    'type_id' => 'required|numeric',
                    'is_home_page' => 'required|numeric',
                ],
                [
                    'type_id.required' => __('api_msg.please_enter_required_fields'),
                    'is_home_page.required' => __('api_msg.please_enter_required_fields'),
                ]
            );
            if ($validation->fails()) {
                $data['status'] = 400;
                $data['message'] = $validation->errors()->first();
                return $data;
            }
            $type_id = $request->type_id;
            $is_home_page = $request->is_home_page;
            $videoFieldsArray = ['video.id', 'video.video_type', 'video.type_id', 'video.video_duration', 'video.thumbnail', 'video.landscape', 'video.category_id', 'video.video_1080'];
            $user_id = isset($request->user_id) ? $request->user_id : 0;
            $isBuyUserData = Transction::select('id')->where('user_id', $user_id)->where('status', '1')->first();
            if (!empty($isBuyUserData)) {
                $isBuyByUser = 1;
            } else {
                $isBuyByUser = 1; //temp expiry
            }
            $sectionQuery = App_Section::where('is_home_screen', $is_home_page)
                ->orderByDesc('is_webseries') // is_webseries = 1 sabse pehle
                ->orderBy('position'); // uske baad position

            if(!empty($type_id)) {
                $sectionQuery->where('type_id', $type_id);
            }

            if(!empty($request->app_section_id)) {
                $sectionQuery->where('id', $request->app_section_id);
            }

            $sections = $sectionQuery->get();
            
            $data = [];
            if(!empty($sections)) {
                $i = 0;
                foreach($sections as $sec) {
                   
                    $data[$i] = ['id' => $sec->id, 'title' => $sec->title, 'screen_layout' => $sec->screen_layout];
                    $vIds = explode(',', $sec['video_id']);
                    if(empty($request->app_section_id)) {
                        $vIds = array_slice($vIds, 0, 5, true);
                    }

                    $videoIdsString = implode(',', $vIds);
                    
                    if($sec->is_webseries == '1'){  
                        
                        $videoQuery = WebSeries::select(
                                    'id',
                                    'title as name',
                                    'slug',
                                    'description',
                                    'thumbnail',
                                    'video',
                                    DB::raw("{$sec->type_id} as type_id"), 
                                    DB::raw("24 as category_id"),
                                    DB::raw("0 as video_type"),
                                    DB::raw("1 as is_webseries")
                                )
                                ->whereIn('id', $vIds)
                                ->orderByRaw("FIELD(id, $videoIdsString)");
                          
                    }else{
                        $videoQuery = Video::select($videoFieldsArray)->whereIn('id', $vIds)->orderByRaw("FIELD(id, $videoIdsString)");
                    }
                    $videos = $videoQuery->get();
                  
                    if (!empty($videos)) {
                        $j = 0;
                        $data[$i]['data'] = $this->get_video_data_comman($videos, $user_id, $isBuyByUser, false);
                    }
                    $i++;
                }
            }
            $continue_watching = [];
            if($request->type_id == 0 and $request->is_home_page == 1 && !empty($user_id)) {
                $videoFieldsArray[] = 'stop_time';
                // $videoContinue = Video_Watch::select(
                //         'video.id as content_id',
                //         'video.title as name',
                //         'video.thumbnail',
                //         'video_watch.stop_time',
                //         'video_watch.created_at'
                //     )
                //     ->join('video', 'video.id', '=', 'video_watch.video_id')
                //     ->where('video_watch.user_id', $user_id)
                //     ->orderBy('video_watch.created_at', 'desc')
                //     ->get()
                //     ->map(function ($item) {
                //         $item->content_type = 'video';
                //         $item->is_webseries = false;
                //         return $item;
                //     });
                $videoContinue = Video_Watch::select($videoFieldsArray)->leftjoin('video', 'video.id', 'video_watch.video_id')->where('user_id', $user_id)->orderBy('video_watch.created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($item) { 
                    $item->web_series_id = 0;
                    $item->is_webseries = false;
                    return $item;
                }); 

           
                $webseriesContinue = EpisodeWatchHistory::select(
                        'episodes.id',
                        'episodes.thumbnail',
                        'episodes.landscape',
                        'episodes.video_duration',
                        'episodes.web_series_id',
                        'web_series.title as name', 
                        'episodes.video_1080',
                        'episode_watch_histories.watch_progress as stop_time',
                        'episode_watch_histories.created_at'
                    )
                    ->join('episodes', 'episodes.id', '=', 'episode_watch_histories.episode_id')
                    ->join('web_series', 'web_series.id', '=', 'episodes.web_series_id')
                    ->where('episode_watch_histories.user_id', $user_id)
                    ->where('episode_watch_histories.is_watched', 1)
                    ->orderBy('episode_watch_histories.id', 'desc')
                    ->limit(5)
                    ->get()
                    ->map(function ($item) {
                        $item->category_id = 24;
                        $item->video_type = 0;
                        $item->is_webseries = true;
                        return $item;
                    });  
     
            $webseriesContinue = $webseriesContinue->take(1);
 
                $videoWatch = collect()
                    ->merge($videoContinue)
                    ->merge($webseriesContinue)
                    ->sortByDesc('created_at')
                    ->take(5)
                    ->values(); 

                $continue_watching = $this->get_video_data_comman($videoWatch, $user_id, $isBuyByUser, true);
               
            }
            if(!empty($data)) { 
                //$reset = array_values($continue_watching);
                $return['status'] = 200;
                $return['message'] = __('api_msg.get_record_successfully');
                $return['result'] = $data;
                $return['continue_watching'] = $continue_watching;
                return $return;
            } else {
                return APIResponse(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage().$e->getLine()));
        }
    }


    private function get_video_data_comman ($videos, $user_id, $isBuyByUser, $isContinue) {
       
        $j = 0;
        $vData = [];
        $cates = Category::pluck('name', 'id');
        foreach($videos as $v) {
            
            // Thumbnail && Landscape
            //$data[$i]['data'][$j] = $v; 
            $vData[$j]['stop_time'] = (int) ($v['stop_time'] ?? 0);
            $vData[$j]['upcoming_type'] = 0;
           
            $vData[$j]['id'] = $v['id'];
             
            $vData[$j]['video_type'] = $v['video_type'];
            $vData[$j]['type_id'] = $v['type_id'];
            $vData[$j]['video_duration'] = (int) ($v['video_duration'] ?? 0);
            if($v['is_webseries'] == '1'){
                if($isContinue){
                    $vData[$j]['thumbnail'] =  !empty($v['thumbnail'] ) ? asset("images/episodes/" .$v['thumbnail'] ) : null;
                    $vData[$j]['landscape'] = !empty($v['landscape']) ? asset("images/episodes/" .$v['landscape']) : null;
                }else{ 
                    $vData[$j]['thumbnail'] =  !empty($v['thumbnail'] ) ? asset("images/web_series/" .$v['thumbnail'] ) : null;
                    $vData[$j]['landscape'] = !empty($v['thumbnail']) ? asset("images/web_series/" .$v['thumbnail']) : null;
                }
            }else{ 
                $vData[$j]['thumbnail'] = Get_Image($this->folder_video, $v['thumbnail']);
                $vData[$j]['landscape'] = Get_Image($this->folder_video, $v['landscape']);

            }
            if (isset($v['video_1080']) && !empty($v['video_1080'])) {
                if ($v['video_upload_type'] == "server_video") {
                    $vData[$j]['video_1080'] = Get_Video($this->folder_video, $v['video_1080']);
                }
            }
           
             
            //$vData[$j]['is_downloaded'] = Is_DownloadByUser($user_id, $v['id'], $v['type_id'], $v['video_type']);
            //$vData[$j]['is_bookmark'] = Is_BookmarkByUser($user_id, $v['id'], $v['type_id'], $v['video_type']);
            //$vData[$j]['rent_buy'] = VideoRentBuyByUser($user_id, $v['id'], $v['type_id'], $v['video_type']);
            //$vData[$j]['is_rent'] = IsRentVideo($v['id'], $v['video_type']);
            //$vData[$j]['rent_price'] = GetPriceByRentVideo($v['id'], $v['video_type']);
            $vData[$j]['is_buy'] = $isBuyByUser;
            $vData[$j]['is_premium'] = 0;
            
            $vData[$j]['category_name'] = 
    (!empty($v['category_id']) && isset($cates[$v['category_id']])) 
    ? $cates[$v['category_id']] 
    : null;
          
            $vData[$j]['session_id'] = 0;
            $vData[$j]['upcoming_type'] = 0;
            $vData[$j]['is_webseries'] = @$v['is_webseries'] ?? 0;
            $vData[$j]['web_series_id'] = @$v['web_series_id'] ?? 0;
              
            $j++;
        } 
          
        return $vData;
    }

    public function newsList(Request $request)
    {
         try {

            $validation = Validator::make($request->all(),[
                'type' => 'required|in:hindi,english',
                'date' => 'required|date'
                ]
            );

            if ($validation->fails()) {
                $data['status'] = 400;
                $data['message'] = __('api_msg.please_enter_required_fields');
                return $data;
            } 

            $query = ENewspaper::where('type', $request->type)->where('date', $request->date)->where('status', 1); 
    
            
            $data = $query->orderBy('date', 'DESC')->get();

            $response = $data->map(function($row) {
                return [
                    'id' => $row->id,
                    'type' => $row->type,
                    'date' => $row->date,
                    'pdf_url' => $row->pdf_file ? asset($row->pdf_file) : null,
                    'highlight_image' => $row->highlight_image ? asset($row->highlight_image) : null,
                    'status' => $row->status,
                ];
            });
 
            if ($response->count() == 0) {
                return response()->json([
                    'status' => 404,
                    'message' => 'No newspaper found for the given filters.'
                ]);
            }

            return response()->json([
                'status' => 200,
                'data'   => $response
            ]);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function eNewsMarkRead(Request $request)
    {
 
        $validation = Validator::make($request->all(),[
            'user_id' => 'required|exists:user,id',
            'enews_id' => 'required|exists:e_newspapers,id',
        ]);

        if ($validation->fails()) {
                $data['status'] = 400;
                $data['message'] = __('api_msg.please_enter_required_fields');
                return $data;
            }
  
        $isRead = ENewsRead::where('user_id', $request->user_id)->where('enews_id', $request->enews_id)->first();
     
        if (!$isRead) {
            ENewsRead::create([
                'user_id' => $request->user_id,
                'enews_id' => $request->enews_id,
            ]);
        }

        return APIResponse(200, __('api_msg.add_successfully'), []);
    }

    public function markVisit(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'user_id' => 'required|exists:user,id',
        ]);

        if ($validation->fails()) {
            $data['status'] = 400;
            $data['message'] = __('api_msg.please_enter_required_fields');
            return $data;
        }

        $today = date('Y-m-d');

        $exists = ENewsVisit::where('user_id', $request->user_id)
                            ->where('date', $today)
                            ->first();

        if (!$exists) {
            ENewsVisit::create([
                'user_id' => $request->user_id,
                'date'    => $today
            ]);
        }

        return APIResponse(200, 'Visit recorded successfully', []);
    }
    
    
    public function optionData(Request $request)
    {
         try {
  

            $query = OptionData::where('status', 1); 
    
            
            $data = $query->orderBy('id', 'DESC')->get();

            $response = $data->map(function($row) {
                return [
                    'id' => $row->id,
                    'type' => $row->type,
                    'url' => $row->url, 
                    'image' => $row->image ? asset($row->image) : null,
                    'status' => $row->status,
                ];
            });
 
            if ($response->count() == 0) {
                return response()->json([
                    'status' => 404,
                    'message' => 'option not found.'
                ]);
            }

            return response()->json([
                'message'=> "data get successfully.",
                'title'=> "title is dynamic.",
                'status' => 200,
                'data'   => $response
            ]);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

public function enewsBannerDetails(){
 try {
            $response= General_Setting::where('key', 'enews_banner_image')->select('value')->first();

            if ($response->count() == 0) {
                return response()->json([
                    'status' => 404,
                    'message' => 'option not found.'
                ]);
            }

            return response()->json([
                'message'=> "data get successfully.",
                'status' => 1,
                'isNavigate'   => 0,
		'webseriesId' => 123,
                'result'   => $response->value,
            ]);
        } catch (Exception $e) {
            return response()->json(array('status' => 0, 'errors' => $e->getMessage()));
        }
}


    public function liveTvUrls()
    {
        try {
            $data = [
                'first_india_news' => 'https://xlbor37ydvaj-hls-live.wmncdn.net/firstindianewstv1/live.stream/index.m3u8',
                'bharat_24' => 'https://cdn.ottlive.co.in/bharat24/index.m3u8'
            ];

            return response()->json([
                'status' => 200,
                'message' => "success",
                'data' => $data
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 400,
                'errors' => $e->getMessage()
            ]);
        }
    }
    public function tvViews(Request $request)
    {
        try {
            $validation = Validator::make($request->all(),[
                'user_id' => 'required',
                'tv_id'   => 'required',
                'type'    => 'required',
            ]);

            if ($validation->fails()) {
                $data['status'] = 400;
                $data['message'] = __('api_msg.please_enter_required_fields');
                return $data;
            }

            $today = Carbon::today();

            $view = TvView::where('user_id', $request->user_id)
                ->where('tv_id', $request->tv_id)
                ->where('type', $request->type)
                ->first();

            if (!$view) { 
                TvView::create([
                    'user_id'      => $request->user_id,
                    'tv_id'        => $request->tv_id,
                    'type'         => $request->type,
                    'view_count'   => 1,
                    'last_view_at' => now(),
                ]);
            } else { 
                if (!Carbon::parse($view->last_view_at)->isToday()) {
                    $view->increment('view_count');
                    $view->update([
                        'last_view_at' => now(),
                    ]);
                }
            }

            return response()->json([
                'status' => 200,
                'message' => 'Success'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 400,
                'errors' => $e->getMessage()
            ]);
        }
    }

    public function getLiveTvUrlsOLd()
    {
        
        $data = LiveTv::get();
        $data = $data->map(function($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'image' => $item->image ?  $item->image : null,
                'dialog_image' => $item->dialog_image ? $item->dialog_image : null,
                'url' => $item->url,
            ];
        });

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $data->isNotEmpty() ? $data : null
        ]);
        
    }
    public function getLiveTvUrls()
    {
        $channels = LiveTv::select(
            'id',
            'name',
            'image',
            'dialog_image',
            'url'
        )->get();

        $ads = DB::table('ads as a')
            ->join('ad_assignments as aa', 'aa.ad_id', '=', 'a.id')
            ->where('a.active', 1)
            ->where('aa.active', 1)
            ->where('aa.assignable_type', 'live_tv')
            ->select(
                'a.id',
                'a.type',
                'a.title',
                'a.media_url',
                'a.media_type',
                'a.click_url',
                'a.start_after_seconds',
                'a.repeat_every_seconds',
                'a.duration_seconds',
                'a.skippable_after_seconds',
                'a.priority',
                'a.active',
                'aa.assignable_id as channel_id',
                'aa.ad_position'
            )
            ->get()
            ->groupBy('id')
            ->map(function ($items) {

                $first = $items->first();
                $mediaUrl = $first->media_url; 
                if ($first->media_type == 'image' && !empty($mediaUrl)) {
                    $mediaUrl = asset($mediaUrl);
                }
                
                return [
                    'id' => $first->id,
                    'channel_ids' => $items->pluck('channel_id')
                        ->unique()
                        ->values(),

                    'type' => $first->type,
                    'title' => $first->title,
                    'media_url' => $mediaUrl,
                    'media_type' => $first->media_type,
                    'click_url' => $first->click_url,

                    'start_after_seconds' => $first->start_after_seconds,
                    'repeat_every_seconds' => $first->repeat_every_seconds,
                    'duration_seconds' => $first->duration_seconds,
                    'skippable_after_seconds' => $first->skippable_after_seconds,

                    'position' => $first->ad_position,
                    'priority' => $first->priority,
                    'active' => (bool) $first->active,
                ];
            })
            ->values();

        $enews_papers = [
            'heading' => 'E-News Papers Touch & Read',
            'papers' => [
                [
                    'name' => 'First India English',
                    'image_url' => 'https://cdn.example.com/enews/english-cover.jpg',
                    'type' => 'english',
                    'display_order' => 1,
                ],
                [
                    'name' => 'First India Hindi',
                    'image_url' => 'https://cdn.example.com/enews/hindi-cover.jpg',
                    'type' => 'hindi',
                    'display_order' => 2,
                ],
            ],
        ];

        return response()->json([
            'status' => 200,
            'message' => 'success',
            'data' => $channels,
            'enews_papers' => $enews_papers,
            'ads' => $ads
        ]);
    }  

}
