<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Channel;
use App\Models\Channel_Section;
use App\Models\TVShow;
use App\Models\Type;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Exception;

class ChannelSectionController extends Controller
{
    public function index()
    {
        try {
            $params['type'] = Type::where('type', 1)->Orwhere('type', 2)->get();
            $params['category'] = Category::get();
            $params['channel'] = Channel::get();
            return view('admin.channel_section.index', $params);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function save(Request $request)
    {
        try {
            if (Auth::guard('admin')->user()->type != 1) {
                return response()->json(array('status' => 400, 'errors' => __('Label.You have no right to add, edit, and delete')));
            } else {
                $validator = Validator::make($request->all(), [
                    'title' => 'required|min:2',
                    'channel_id' => 'required',
                    'video_type' => 'required',
                    'type_id' => 'required',
                    'video_id' => 'required',
                ]);
                if ($validator->fails()) {
                    $errs = $validator->errors()->all();
                    return response()->json(array('status' => 400, 'errors' => $errs));
                }

                $data = new Channel_Section();
                $data->type_id = $request->type_id;
                $data->video_type = $request->video_type;
                $data->channel_id = $request->channel_id;
                $data->title = $request->title;
                $data->screen_layout = $request->screen_layout;

                if ($request->video_type == '1') {
                    $VideoId = implode(',', $request->video_id);
                    $data->video_id = $VideoId;
                    $data->tv_show_id = "";
                    $data->language_id = "";
                    $data->category_ids = "";
                } elseif ($request->video_type == '2') {
                    $TVShowId = implode(',', $request->video_id);
                    $data->video_id = "";
                    $data->tv_show_id = $TVShowId;
                    $data->language_id = "";
                    $data->category_ids = "";
                } elseif ($request->video_type == '3') {
                    $LangId = implode(',', $request->language_id);
                    $data->video_id = "";
                    $data->tv_show_id = "";
                    $data->language_id = $LangId;
                    $data->category_ids = "";
                } else {
                    $CategoryIds = implode(',', $request->category_ids);
                    $data->video_id = "";
                    $data->tv_show_id = "";
                    $data->language_id = "";
                    $data->category_ids = $CategoryIds;
                }

                if ($data->save()) {
                    return response()->json(array('status' => 200, 'success' => __('Label.Data Add Successfully')));
                } else {
                    return response()->json(array('status' => 400, 'errors' => __('Label.Data Not Add')));
                }
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function update(Request $request)
    {
        try {
            $Channel_section = Channel_Section::where('id', $request->id)->first();

            if ($Channel_section->video_type == 1) {
                $video = Video::where('type_id', $Channel_section->type_id)->where('channel_id', $Channel_section->channel_id)->get();
            } else if ($Channel_section->video_type == 2) {
                $video = TVShow::where('type_id', $Channel_section->type_id)->where('channel_id', $Channel_section->channel_id)->get();
            }
            return response()->json(array('status' => 200, 'success' => 'Data Get Successfully', 'result' => $Channel_section, 'video' => $video));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function delete($id)
    {
        try {
            if (Auth::guard('admin')->user()->type != 1) {
                return response()->json(array('status' => 400, 'errors' => __('Label.You have no right to add, edit, and delete')));
            } else {
                $video = Channel_Section::where('id', $id)->first();
                if ($video) {
                    $video->delete();
                    return response()->json(array('status' => 200, 'success' => 'Data Deleted Successfully'));
                } else {
                    return response()->json(array('status' => 200, 'success' => 'Data Deleted Successfully'));
                }
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function get_lang_or_cat(Request $request)
    {
        if ($request->Video_Type == 1) {
            if ($request->Type_Id != "" && $request->Type_Id != null && $request->Channel_Id != "" && $request->Channel_Id != null) {
                $data = Video::where('type_id', $request->Type_Id)->where('channel_id', $request->Channel_Id)->get();
                return response()->json(array('status' => 200, 'success' => 'Data Get Successfully', 'result' => $data));
            } else {
                return response()->json(array('status' => 400, 'errors' => "Data Not Found"));
            }
        } else if ($request->Video_Type == 2) {
            if ($request->Type_Id != "" && $request->Type_Id != null && $request->Channel_Id != "" && $request->Channel_Id != null) {
                $data = TVShow::where('type_id', $request->Type_Id)->where('channel_id', $request->Channel_Id)->get();
                return response()->json(array('status' => 200, 'success' => 'Data Get Successfully', 'result' => $data));
            } else {
                return response()->json(array('status' => 400, 'errors' => "Data Not Found"));
            }
        } else {
            return response()->json(array('status' => 400, 'errors' => "Data Not Found"));
        }
    }

    public function GetSectionData()
    {
        $data = Channel_Section::with('type')->with('category')->with('channel')->orderBy('id', 'desc')->get();
        for ($i = 0; $i < count($data); $i++) {

            if ($data[$i]['video_type'] == 1) {

                $video = Video::select('id', 'name')->whereIn('id', explode(",", $data[$i]['video_id']))->where('type_id', $data[$i]['type_id'])->get();
                $videos = array();
                for ($j = 0; $j < count($video); $j++) {
                    $videos[] = $video[$j]['name'];
                }
                if (isset($videos)) {
                    $html = [];
                    for ($k = 0; $k < count($videos); $k++) {
                        $html[$k] = "<p class='btn btn-outline-dark btn-sm mr-2'>" . $videos[$k] . "</p>";
                    }
                    $html = implode(" ", $html);
                    $data[$i]['video_list'] = $html;
                } else {
                    $data[$i]['video_list'] = "";
                }
            } else if ($data[$i]['video_type'] == 2) {

                $video = TVShow::select('id', 'name')->whereIn('id', explode(",", $data[$i]['tv_show_id']))->where('type_id', $data[$i]['type_id'])->get();
                $videos = array();
                for ($j = 0; $j < count($video); $j++) {
                    $videos[] = $video[$j]['name'];
                }
                if (isset($videos)) {
                    $html = [];
                    for ($k = 0; $k < count($videos); $k++) {
                        $html[$k] = "<p class='btn btn-outline-dark btn-sm mr-2'>" . $videos[$k] . "</p>";
                    }
                    $html = implode(" ", $html);
                    $data[$i]['video_list'] = $html;
                } else {
                    $data[$i]['video_list'] = "";
                }
            }
        }
        return response()->json(array('status' => 200, 'success' => 'Data Get Successfully', 'result' => $data));
    }

    public function update1(Request $request)
    {
        try {
            if (Auth::guard('admin')->user()->type != 1) {
                return response()->json(array('status' => 400, 'errors' => __('Label.You have no right to add, edit, and delete')));
            } else {
                $validator = Validator::make($request->all(), [
                    'title' => 'required|min:2',
                    'channel_id' => 'required',
                    'video_type' => 'required',
                    'type_id' => 'required',
                    'screen_layout' => 'required',
                    'video_id' => 'required',
                    'edit_id' => 'required',
                ]);

                if ($validator->fails()) {
                    $errs = $validator->errors()->all();
                    return response()->json(array('status' => 400, 'errors' => $errs));
                }

                $data = Channel_Section::where('id', $request->edit_id)->first();
                if (isset($data->id)) {

                    $data->type_id = $request->type_id;
                    $data->channel_id = $request->channel_id;
                    $data->video_type = $request->video_type;
                    $data->title = $request->title;
                    $data->screen_layout = $request->screen_layout;

                    $VideoId = implode(',', $request->video_id);
                    if ($request->video_type == '1') {
                        $data->video_id = $VideoId;
                        $data->tv_show_id = "";
                        $data->language_id = "";
                        $data->category_ids = "";
                    } elseif ($request->video_type == '2') {
                        $data->video_id = "";
                        $data->tv_show_id = $VideoId;
                        $data->language_id = "";
                        $data->category_ids = "";
                    } elseif ($request->video_type == '3') {
                        $data->video_id = "";
                        $data->tv_show_id = "";
                        $data->language_id = $VideoId;
                        $data->category_ids = "";
                    } else if ($request->video_type == '4') {
                        $data->video_id = "";
                        $data->tv_show_id = "";
                        $data->language_id = "";
                        $data->category_ids = $VideoId;
                    }

                    if ($data->save()) {
                        return response()->json(array('status' => 200, 'success' => __('Label.Data Edit Successfully')));
                    } else {
                        return response()->json(array('status' => 400, 'errors' => __('Label.Data Not Updated')));
                    }
                } else {
                    return response()->json(array('status' => 400, 'errors' => __('Label.Data Not Updated')));
                }
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
