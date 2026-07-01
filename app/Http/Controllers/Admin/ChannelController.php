<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\TVShow;
use App\Models\Video;
use Illuminate\Http\Request;
use Validator;
use Exception;

class ChannelController extends Controller
{

    private $folder = "channel";

    public function index()
    {
        try {
            return view('admin.channel.index');
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function data(Request $request)
    {
        try {
            if ($request == true) {

                $input_search = $request['input_search'];

                if ($input_search != null && isset($input_search)) {
                    $data = Channel::where('name', 'LIKE', "%{$input_search}%")->latest()->get();
                } else {
                    $data = Channel::latest()->get();
                }

                imageNameToUrl($data, 'image', $this->folder);
                imageNameToUrl($data, 'landscape', $this->folder);

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $btn = '<div class="d-flex justify-content-center gap-2">';
                        $btn .= '<a href="' . route("editChannel", $row->id) . '" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a> ';
                        $btn .= '<a href="' . route("deleteChannel", $row->id) . '" title="Delete" onclick="return confirm(\'Are you sure !!! You want to Delete this Channel ?\')"><i class="fa-solid fa-trash-can"></i></a></div>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            } else {
                return view('admin.channel.index');
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function add()
    {
        try {
            return view('admin.channel.add');
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function save(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:2',
                'is_title' => 'required',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'landscape' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $channel = new Channel();
            $channel->name = $request->name;
            $channel->is_title = $request->is_title;
            $channel->status = 1;

            $org_name = $request->file('image');
            $org_name1 = $request->file('landscape');
            if ($org_name != null) {
                $channel->image = saveImage($org_name, $this->folder);
            }
            if ($org_name1 != null) {
                $channel->landscape = saveImage($org_name1, $this->folder);
            }

            if ($channel->save()) {
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
            $channel = Channel::where('id', $id)->first();

            imageNameToUrl(array($channel), 'image', $this->folder);
            imageNameToUrl(array($channel), 'landscape', $this->folder);

            return view('admin.channel.edit', ['result' => $channel]);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:2',
                'is_title' => 'required',
                'image' => 'image|mimes:jpeg,png,jpg|max:2048',
                'landscape' => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($validator->fails()) {

                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $channel = Channel::where('id', $request->id)->first();

            if (isset($channel->id)) {

                $channel->name = $request->name;
                $channel->is_title = $request->is_title;

                if (isset($request->image)) {
                    $files = $request->image;
                    $channel->image = saveImage($files, $this->folder);

                    deleteImageToFolder($this->folder, basename($request->old_image));
                }
                if (isset($request->landscape)) {
                    $files = $request->landscape;
                    $channel->landscape = saveImage($files, $this->folder);

                    deleteImageToFolder($this->folder, basename($request->old_landscape));
                }

                // $org_name = $request->file('image');
                // if ($org_name == null && $channel->image == null) {
                //     $channel->image = "";
                // } else if ($org_name != null && $channel->image == null) {
                //     $channel->image = saveImage($org_name, $this->folder);
                // } else if ($org_name != null) {
                //     $channel->image = saveImage($org_name, $this->folder);
                //     @unlink("images/channel/" . $request->old_image);
                // } else {
                //     $channel->image = $request->old_image;
                // }

                // $org_name1 = $request->file('landscape');
                // if ($org_name1 == null && $channel->landscape == null) {
                //     $channel->landscape = "";
                // } else if ($org_name1 != null && $channel->landscape == null) {
                //     $channel->landscape = saveImage_landscape($org_name1, $this->folder);
                // } else if ($org_name1 != null) {
                //     $channel->landscape = saveImage_landscape($org_name1, $this->folder);
                //     @unlink("images/channel/" . $request->old_landscape);
                // } else {
                //     $channel->landscape = $request->old_landscape;
                // }

                if ($channel->save()) {
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
            $channel = Channel::where('id', $id)->first();
            $Video = Video::where('channel_id', $channel->id)->first();
            $TVShow = TVShow::where('channel_id', $channel->id)->first();

            if ($Video) {
                return back()->with('error', "This Channel is used on some other table so you can not remove it.");
            } elseif ($TVShow) {
                return back()->with('error', "This Channel is used on some other table so you can not remove it.");
            } else {

                deleteImageToFolder($this->folder, $channel->image);
                deleteImageToFolder($this->folder, $channel->landscape);

                $channel->delete();
                return redirect()->route('channel')->with('success', __('Label.Data Delete Successfully'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
