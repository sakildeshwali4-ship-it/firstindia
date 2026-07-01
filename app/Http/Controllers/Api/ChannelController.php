<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\Channel_Banner;
use App\Models\Channel_Section;
use App\Models\TVShow;
use App\Models\Video;
use Illuminate\Http\Request;
use Exception;

// Video Type = 1-Video, 2-Show, 3-Language, 4-Category, 5-Upcoming
// Video Upload Type = server_video, external, youtube, vimeo
// Subtitle Type = server_video, external
// Trailer Type = server_video, external, youtube

class ChannelController extends Controller
{

    private $folder_video = "video";
    private $folder_show = "show";
    private $folder_channel = "channel";

    public function get_channel()
    {
        try {
            $Data = Channel::latest()->get();
            if (sizeof($Data) > 0) {

                imageNameToUrl($Data, 'image', $this->folder_channel);
                imageNameToUrl($Data, 'landscape', $this->folder_channel);

                return APIResponse(200, __('api_msg.get_record_successfully'), $Data);
            } else {
                return APIResponse(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function channel_section_list(Request $request)
    {
        try {

            $user_id = isset($request->user_id) ? $request->user_id : 0;

            $data = Channel_Section::with('channel')->latest()->get();

            if (count($data) > 0) {

                // Channel Section
                for ($i = 0; $i < count($data); $i++) {

                    $data[$i]['data'] = [];

                    // Channel_Name
                    $data[$i]['channel_name'] = "";
                    if ($data[$i]['channel'] != null) {
                        $data[$i]['channel_name'] = $data[$i]['channel']['name'];
                    }
                    unset($data[$i]['channel']);

                    if ($data[$i]['video_type'] == '1') {

                        $Ids = explode(',', $data[$i]['video_id']);
                        $video_data = Video::whereIn('id', $Ids)->where('video_type', 1)->latest()->get();

                        if (count($video_data) > 0) {

                            $data[$i]['data'] = $video_data;
                            for ($j = 0; $j < count($video_data); $j++) {

                                // Thumbnail && Landscape
                                $data[$i]['data'][$j]['thumbnail'] = Get_Image($this->folder_video, $data[$i]['data'][$j]['thumbnail']);
                                $data[$i]['data'][$j]['landscape'] = Get_Image($this->folder_video, $data[$i]['data'][$j]['landscape']);

                                // Videos (320, 480, 720, 1080)
                                if (isset($data[$i]['data'][$j]['video_320']) && !empty($data[$i]['data'][$j]['video_320'])) {
                                    if ($data[$i]['data'][$j]['video_upload_type'] == "server_video") {
                                        $data[$i]['data'][$j]['video_320'] = Get_Video($this->folder_video, $data[$i]['data'][$j]['video_320']);
                                    }
                                }
                                if (isset($data[$i]['data'][$j]['video_480']) && !empty($data[$i]['data'][$j]['video_480'])) {

                                    if ($data[$i]['data'][$j]['video_upload_type'] == "server_video") {
                                        $data[$i]['data'][$j]['video_480']  = Get_Video($this->folder_video, $data[$i]['data'][$j]['video_480']);
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

                                // Subtitle_1_2_3
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
                                $data[$i]['data'][$j]['is_buy'] = IsBuyByUser($user_id);
                                $data[$i]['data'][$j]['category_name'] = GetCategoryNameByIds($data[$i]['data'][$j]['category_id']);
                                $data[$i]['data'][$j]['session_id'] = "0";
                                $data[$i]['data'][$j]['upcoming_type'] = 0;
                            }
                        } else {
                            $data[$i]['data'] = [];
                        }
                    } elseif ($data[$i]['video_type'] == '2') {

                        $Ids = explode(',', $data[$i]['tv_show_id']);
                        $tvshow_data = TVShow::whereIn('id', $Ids)->where('video_type', 2)->latest()->get();

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
                                $data[$i]['data'][$j]['is_buy'] = IsBuyByUser($user_id);
                                $data[$i]['data'][$j]['category_name'] = GetCategoryNameByIds($data[$i]['data'][$j]['category_id']);
                                $data[$i]['data'][$j]['session_id'] = GetSessionByTVShowId($data[$i]['data'][$j]['id']);
                                $data[$i]['data'][$j]['upcoming_type'] = 0;
                            }
                        } else {
                            $data[$i]['data'] = [];
                        }
                    }
                }

                // Channel Banner (Live Url)
                $live_url = Channel_Banner::orderBy('order_no', 'ASC')->get();
                if (count($live_url) > 0) {

                    for ($i = 0; $i < count($live_url); $i++) {

                        $url_data[$i] = $live_url[$i];
                        $url_data[$i]['is_buy'] = IsBuyByUser($user_id);

                        // Image
                        $url_data[$i]['image'] = Get_Image($this->folder_channel, $live_url[$i]['image']);
                    }
                } else {
                    $url_data = [];
                }

                $final_array['status'] = 200;
                $final_array['message'] = __('api_msg.get_record_successfully');
                $final_array['result'] = $data;
                $final_array['live_url'] = $url_data;
                return $final_array;
            } else {
                return APIResponse(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
