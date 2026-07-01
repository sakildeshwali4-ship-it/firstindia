<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\TVShow;
use App\Models\Type;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Exception;

class BannerController extends Controller
{
    public function index()
    {
        try {

            $type = Type::get();
            $video = array();
            if ($type != null && count($type) > 0) {

                if ($type[0]->type == 1) {
                    $video = Video::where('type_id', $type[0]->id)->get();
                } else if ($type[0]->type == 2) {
                    $video = TVShow::where('type_id', $type[0]->id)->get();
                }
            }
            return view('admin.banner.index', ['type' => $type, 'video' => $video]);
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
                    'type_id' => 'required',
                    'is_home_screen' => 'required',
                    'video_type' => 'required',
                    'video_id' => 'required',
                ]);
                if ($validator->fails()) {
                    $errs = $validator->errors()->all();
                    return response()->json(array('status' => 400, 'errors' => $errs));
                }

                $banner = new Banner();
                $banner->is_home_screen = $request->is_home_screen;
                $banner->type_id = $request->type_id;
                $banner->video_type = $request->video_type;
                $banner->upcoming_type = isset($request->upcoming_type) ? $request->upcoming_type : 0;
                $banner->video_id = $request->video_id;
                $banner->status = 1;

                if ($banner->save()) {
                    return response()->json(array('status' => 200, 'success' => __('Label.Data Add Successfully')));
                } else {
                    return response()->json(array('status' => 400, 'errors' => __('Label.Data Not Add')));
                }
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function TypeByVideo(Request $request)
    {
        try {
            if ($request->type == 1) {
                $data = Video::where('type_id', $request->type_id)->get();
            } else if ($request->type == 2) {
                $data = TVShow::where('type_id', $request->type_id)->get();
            } else if ($request->type == 5) {

                $data = array();
                if ($request->upcoming_type == 1) {
                    $data = Video::where('type_id', $request->type_id)->get();
                } else if ($request->upcoming_type == 2) {
                    $data = TVShow::where('type_id', $request->type_id)->get();
                }
            }
            return response()->json(array('status' => 200, 'success' => __('Label.Data Add Successfully'), 'result' => $data));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function BannerList(Request $request)
    {
        try {

            if ($request->is_home_screen == 1) {

                $data = Banner::where('is_home_screen', $request->is_home_screen)->with('type')->orderBy('id', 'desc')->get();
                for ($i = 0; $i < count($data); $i++) {
                    if ($data[$i]->video_type == 1) {

                        $video = Video::select('id', 'name')->where('id', $data[$i]->video_id)->first();
                        $data[$i]['video'] = $video;
                    } else if ($data[$i]->video_type == 2) {

                        $show = TVShow::select('id', 'name')->where('id', $data[$i]->video_id)->first();
                        $data[$i]['video'] = $show;
                    } else if ($data[$i]->video_type == 5) {

                        if ($data[$i]->upcoming_type == 1) {

                            $video = Video::select('id', 'name')->where('id', $data[$i]->video_id)->first();
                            $data[$i]['video'] = $video;
                        } else if ($data[$i]->upcoming_type == 2) {

                            $show = TVShow::select('id', 'name')->where('id', $data[$i]->video_id)->first();
                            $data[$i]['video'] = $show;
                        }
                    }
                }
            } else {

                $data = Banner::where('type_id', $request->type_id)->where('is_home_screen', $request->is_home_screen)->orderBy('id', 'desc')->with('type')->get();
                for ($i = 0; $i < count($data); $i++) {

                    if ($data[$i]->video_type == 1) {

                        $video = Video::select('id', 'name')->where('id', $data[$i]->video_id)->first();
                        $data[$i]['video'] = $video;
                    } else if ($data[$i]->video_type == 2) {

                        $show = TVShow::select('id', 'name')->where('id', $data[$i]->video_id)->first();
                        $data[$i]['video'] = $show;
                    } else if ($data[$i]->video_type == 5) {

                        if ($data[$i]->upcoming_type == 1) {

                            $video = Video::select('id', 'name')->where('id', $data[$i]->video_id)->first();
                            $data[$i]['video'] = $video;
                        } else if ($data[$i]->upcoming_type == 2) {

                            $show = TVShow::select('id', 'name')->where('id', $data[$i]->video_id)->first();
                            $data[$i]['video'] = $show;
                        }
                    }
                }
            }

            return response()->json(array('status' => 200, 'success' => __('Label.Data Add Successfully'), 'result' => $data));
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

                $banner = Banner::where('id', $id)->first();
                if ($banner->image) {
                    if ($banner->delete()) {
                        @unlink("images/banner/" . $banner->image);
                        return response()->json(array('status' => 200, 'success' => __('Label.Data Delete Successfully')));
                    }
                } else {
                    if ($banner->delete()) {
                        return response()->json(array('status' => 200, 'success' => __('Label.Data Delete Successfully')));
                    }
                }
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
