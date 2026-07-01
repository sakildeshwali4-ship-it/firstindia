<?php

namespace App\Http\Controllers\Admin;

use Validator;
use App\Models\Cast;
use App\Models\TVShow;
use App\Models\Video;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;

class CastController extends Controller
{
    private $folder = "cast";

    public function index()
    {
        try {
            return view('admin.cast.index');
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

                    if ($input_type == "all") {

                        $data = Cast::where(function ($query) use ($input_search) {
                            $query->where('name', 'LIKE', "%{$input_search}%");
                        })
                            ->latest()->get();
                    } else {

                        $data = Cast::where(function ($query) use ($input_search) {
                            $query->where('name', 'LIKE', "%{$input_search}%");
                        })
                            ->where('type', $input_type)
                            ->latest()->get();
                    }
                } else {

                    if ($input_type == "all") {

                        $data = Cast::latest()->get();
                    } else {

                        $data = Cast::where('type', $input_type)->latest()->get();
                    }
                }

                imageNameToUrl($data, 'image', $this->folder);

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $btn = '<div class="d-flex justify-content-center gap-2">';
                        $btn .= '<a href="' . route("editCast", $row->id) . '" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a> ';
                        $btn .= '<a href="' . route("deleteCast", $row->id) . '" title="Delete" onclick="return confirm(\'Are you sure !!! You want to Delete this Cast ?\')"><i class="fa-solid fa-trash-can"></i></a></div>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            } else {
                return view('admin.cast.index');
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function add()
    {
        try {
            return view('admin.cast.add');
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function save(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:2',
                'type' => 'required',
                'personal_info' => 'required',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $cast = new Cast();
            $cast->name = $request->name;
            $cast->type = $request->type;
            $cast->personal_info = $request->personal_info;

            $org_name = $request->file('image');
            $cast->image = saveImage($org_name, $this->folder);

            if ($cast->save()) {
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
            $user = Cast::where('id', $id)->first();

            imageNameToUrl(array($user), 'image', $this->folder);

            return view('admin.cast.edit', ['result' => $user]);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:2',
                'type' => 'required',
                'personal_info' => 'required',
                'image' => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $cast = Cast::where('id', $request->id)->first();

            if (isset($cast->id)) {

                $cast->name = $request->name;
                $cast->type = $request->type;
                $cast->personal_info = $request->personal_info;

                if (isset($request->image)) {
                    $files = $request->image;
                    $cast->image = saveImage($files, $this->folder);

                    deleteImageToFolder($this->folder, basename($request->old_image));
                }

                if ($cast->save()) {
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
            $cast = Cast::where('id', $id)->first();
            $Video = Video::whereRaw("find_in_set('" . $cast->id . "',video.cast_id)")->first();
            $TVShow = TVShow::whereRaw("find_in_set('" . $cast->id . "',tv_show.cast_id)")->first();

            if ($Video) {
                return back()->with('error', "This Cast is used on some other table so you can not remove it.");
            } elseif ($TVShow) {
                return back()->with('error', "This Cast is used on some other table so you can not remove it.");
            } else {
                if ($cast->delete()) {

                    deleteImageToFolder($this->folder, $cast->image);
                    return redirect()->route('cast')->with('success', __('Label.Data Delete Successfully'));
                }
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
