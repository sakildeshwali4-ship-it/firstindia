<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RentVideo;
use App\Models\TVShow;
use App\Models\Type;
use App\Models\Video;
use Illuminate\Http\Request;
use Validator;
use Exception;

class RentVideoController extends Controller
{
    public function index()
    {
        try {
            return view('admin.rent.index');
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function data(Request $request)
    {
        try {
            if ($request == true) {
                $data = RentVideo::latest()->get();

                for ($i = 0; $i < count($data); $i++) {

                    $type = Type::where('id', $data[$i]->type_id)->first();
                    $data[$i]->type_name = "";
                    if (isset($type) && $type != null) {
                        $data[$i]->type_name = $type->name;
                    }

                    if ($data[$i]->video_type == 1) {

                        $video = Video::select('id', 'name')->where('id', $data[$i]->video_id)->latest()->first();

                        $data[$i]->video_name = "";
                        if (isset($video) && $video != null) {
                            $data[$i]->video_name = $video->name;
                        }
                    } else if ($data[$i]->video_type == 2) {

                        $show = TVShow::select('id', 'name')->where('id', $data[$i]->video_id)->latest()->first();
                        $data[$i]->video_name = "";
                        if (isset($show) && $show != null) {
                            $data[$i]->video_name = $show->name;
                        }
                    }
                }

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $btn = '<div class="d-flex justify-content-center gap-2">';
                        $btn .= '<a href="' . route("editRentVideo", $row->id) . '" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a> ';
                        $btn .= '<a href="' . route("deleteRentVideo", $row->id) . '" title="Delete" onclick="return confirm(\'Are you sure !!! You want to Delete this Rent Video ?\')"><i class="fa-solid fa-trash-can"></i></a></div>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            } else {
                return view('admin.rent.index');
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function add(Request $request)
    {
        try {

            if ($request->ajax()) {
                $video = Video::where('type_id', $request->type_id)->where('video_type', '!=', 5)->latest()->get();
                $tvshow = TVShow::where('type_id', $request->type_id)->where('video_type', '!=', 5)->latest()->get();
                return response()->json(array('status' => 200, 'success' => __('Label.Data Edit Successfully'), 'video' => $video, 'tvshow' => $tvshow));
            }

            $type = Type::where('type', 1)->Orwhere('type', 2)->get();
            $video = Video::where('video_type', '!=', 5)->latest()->get();
            $tvshow_video = TVShow::where('video_type', '!=', 5)->latest()->get();
            return view('admin.rent.add', ['type' => $type, 'video' => $video, 'tvshowVideo' => $tvshow_video]);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function save(Request $request)
    {
        try {
            if ($request->video_type == '1') {
                $validator = Validator::make($request->all(), [
                    'type_id' => 'required',
                    'video_type' => 'required',
                    'price' => 'required',
                    'video_id' => 'required',
                    'time' => 'required',
                    'type' => 'required',
                ]);
            } elseif ($request->video_type == '2') {
                $validator = Validator::make($request->all(), [
                    'type_id' => 'required',
                    'video_type' => 'required',
                    'price' => 'required',
                    'show_id' => 'required',
                    'time' => 'required',
                    'type' => 'required',
                ]);
            } else {
                $validator = Validator::make($request->all(), [
                    'type_id' => 'required',
                    'video_type' => 'required',
                    'price' => 'required',
                    'video_id' => 'required',
                    'show_id' => 'required',
                    'time' => 'required',
                    'type' => 'required',
                ]);
            }
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $rent = new RentVideo();
            $rent->type_id = $request->type_id;
            $rent->video_type = $request->video_type;
            $rent->price = $request->price;
            $rent->time = $request->time;
            $rent->type = $request->type;
            $rent->status = 1;

            if ($request->video_type == 1) {
                $RentData = RentVideo::where('video_type', $request->video_type)->where('video_id', $request->video_id)->first();
                if ($RentData != null && $RentData != "") {
                    return response()->json(array('status' => 400, 'errors' => "This Video is Already Exists"));
                }
                $rent->video_id = $request->video_id;
            } else if ($request->video_type == 2) {
                $RentData = RentVideo::where('video_type', $request->video_type)->where('video_id', $request->show_id)->first();
                if ($RentData != null && $RentData != "") {
                    return response()->json(array('status' => 400, 'errors' => "This Show is Already Exists"));
                }
                $rent->video_id = $request->show_id;
            }

            if ($rent->save()) {
                return response()->json(array('status' => 200, 'success' => __('Label.Data Add Successfully')));
            } else {
                return response()->json(array('status' => 400, 'errors' => __('Label.Data Not Add')));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function edit(Request $request, $id)
    {
        try {
            $user = RentVideo::where('id', $id)->first();
            $type = Type::get();
            $video = Video::where('type_id', $user->type_id)->latest()->get();
            $tvshow_video = TVShow::where('type_id', $user->type_id)->latest()->get();

            return view('admin.rent.edit', ['result' => $user, 'type' => $type, 'video' => $video, 'tvshowVideo' => $tvshow_video]);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function update(Request $request)
    {
        try {

            if ($request->video_type == '1') {
                $validator = Validator::make($request->all(), [
                    'type_id' => 'required',
                    'video_type' => 'required',
                    'price' => 'required',
                    'video_id' => 'required',
                    'time' => 'required',
                    'type' => 'required',
                ]);
            } elseif ($request->video_type == '2') {
                $validator = Validator::make($request->all(), [
                    'type_id' => 'required',
                    'video_type' => 'required',
                    'price' => 'required',
                    'show_id' => 'required',
                    'time' => 'required',
                    'type' => 'required',
                ]);
            } else {
                $validator = Validator::make($request->all(), [
                    'type_id' => 'required',
                    'video_type' => 'required',
                    'price' => 'required',
                    'video_id' => 'required',
                    'show_id' => 'required',
                    'time' => 'required',
                    'type' => 'required',
                ]);
            }
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $rent = RentVideo::where('id', $request->id)->first();

            if (isset($rent->id)) {

                $rent->type_id = $request->type_id;
                $rent->video_type = $request->video_type;
                $rent->price = $request->price;
                $rent->time = $request->time;
                $rent->type = $request->type;
                $rent->status = 1;

                if ($request->video_type == 1) {
                    $RentData = RentVideo::where('id', '!=', $rent->id)->where('video_type', $request->video_type)->where('video_id', $request->video_id)->first();
                    if ($RentData) {
                        return response()->json(array('status' => 400, 'errors' => "This Video is Already Exists"));
                    }
                    $rent->video_id = $request->video_id;
                } else if ($request->video_type == 2) {
                    $RentData = RentVideo::where('id', '!=', $rent->id)->where('video_type', $request->video_type)->where('video_id', $request->show_id)->first();
                    if ($RentData != null && $RentData != "") {
                        return response()->json(array('status' => 400, 'errors' => "This Show is Already Exists"));
                    }
                    $rent->video_id = $request->show_id;
                }

                if ($rent->save()) {
                    return response()->json(array('status' => 200, 'success' => __('Label.Data Edit Successfully')));
                } else {
                    return response()->json(array('status' => 400, 'errors' => __('Label.Data Not Updated')));
                }
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function delete($id)
    {
        try {
            $rent = RentVideo::where('id', $id)->first();
            if ($rent->delete()) {
                return redirect()->route('RentVideo')->with('success', __('Label.Data Delete Successfully'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
