<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Avatar;
use Illuminate\Http\Request;
use Validator;
use Exception;

class AvatarController extends Controller
{
    private $folder = "avatar";

    public function index()
    {
        try {
            return view('admin.avatar.index');
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
                    $data = Avatar::where('name', 'LIKE', "%{$input_search}%")->latest()->get();
                } else {
                    $data = Avatar::latest()->get();
                }

                imageNameToUrl($data, 'image', $this->folder);

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $btn = '<div class="d-flex justify-content-center gap-2">';
                        $btn .= '<a href="' . route("editAvatar", $row->id) . '" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a> ';
                        $btn .= '<a href="' . route("deleteAvatar", $row->id) . '" title="Delete" onclick="return confirm(\'Are you sure !!! You want to Delete this Avatar ?\')"><i class="fa-solid fa-trash-can"></i></a></div>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            } else {
                return view('admin.avatar.index');
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function add()
    {
        try {
            return view('admin.avatar.add');
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function save(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:2',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $user = new Avatar();
            $user->name = $request->name;
            $org_name = $request->file('image');
            $user->image = saveImage($org_name, $this->folder);

            if ($user->save()) {
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
            $Avatar = Avatar::where('id', $id)->first();

            imageNameToUrl(array($Avatar), 'image', $this->folder);

            return view('admin.avatar.edit', ['result' => $Avatar]);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:2',
                'image' => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($validator->fails()) {

                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $avatar = Avatar::where('id', $request->id)->first();
            if (isset($avatar->id)) {

                $avatar->name = $request->name;

                if (isset($request->image)) {
                    $files = $request->image;
                    $avatar->image = saveImage($files, $this->folder);

                    deleteImageToFolder($this->folder, basename($request->old_image));
                }

                if ($avatar->save()) {
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

            $Avatar = Avatar::where('id', $id)->first();
            if (isset($Avatar)) {

                deleteImageToFolder($this->folder, $Avatar->image);
                $Avatar->delete();
            }
            return redirect()->route('Avatar')->with('success', __('Label.Data Delete Successfully'));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
