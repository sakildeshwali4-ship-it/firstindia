<?php

namespace App\Http\Controllers\Admin;

use Validator;
use App\Models\Channel_Banner;
use App\Models\Channel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;

class ChannelBannerlController extends Controller
{
    private $folder = "channel";

    public function index()
    {
        try {
            return view('admin.channel_banner.index');
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
                    $data = Channel_Banner::where('name', 'LIKE', "%{$input_search}%")->orderBy('order_no', 'asc')->latest()->get();
                } else {
                    $data = Channel_Banner::orderBy('order_no', 'asc')->latest()->get();
                }

                imageNameToUrl($data, 'image', $this->folder);

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $btn = '<div class="d-flex justify-content-center gap-2">';
                        $btn .= '<a href="' . route("editChannelBanner", $row->id) . '" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a> ';
                        $btn .= '<a href="' . route("deleteChannelBanner", $row->id) . '" title="Delete" onclick="return confirm(\'Are you sure !!! You want to Delete this Channel Banner ?\')"><i class="fa-solid fa-trash-can"></i></a></div>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            } else {
                return view('admin.channel_banner.index');
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function add()
    {
        try {
            $channel = Channel::latest()->get();
            return view('admin.channel_banner.add', ['channel' => $channel]);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function save(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:2',
                'link' => 'required',
                'order_no' => 'required|numeric|min:1',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $banner = new Channel_Banner();
            $banner->name = $request->name;
            $banner->link = $request->link;
            $banner->order_no = $request->order_no;

            $org_name = $request->file('image');
            $banner->image = saveImage($org_name, $this->folder);

            if ($banner->save()) {
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
            $user = Channel_Banner::where('id', $id)->first();
            $channel = Channel::latest()->get();

            imageNameToUrl(array($user), 'image', $this->folder);

            return view('admin.channel_banner.edit', ['result' => $user, 'channel' => $channel]);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:2',
                'link' => 'required',
                'order_no' => 'required|numeric|min:1',
                'image' => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $banner = Channel_Banner::where('id', $request->id)->first();

            if (isset($banner->id)) {

                $banner->name = $request->name;
                $banner->link = $request->link;
                $banner->order_no = $request->order_no;

                if (isset($request->image)) {
                    $files = $request->image;
                    $banner->image = saveImage($files, $this->folder);

                    deleteImageToFolder($this->folder, basename($request->old_image));
                }

                if ($banner->save()) {
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
            $banner = Channel_Banner::where('id', $id)->first();

            deleteImageToFolder($this->folder, $banner->image);
            return redirect()->route('ChannelBanner')->with('success', __('Label.Data Delete Successfully'));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
