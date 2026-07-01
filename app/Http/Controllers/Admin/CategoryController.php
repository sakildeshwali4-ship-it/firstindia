<?php

namespace App\Http\Controllers\Admin;

use Validator;
use App\Models\Category;
use App\Models\TVShow;
use App\Models\Video;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;

class CategoryController extends Controller
{
    private $folder = "category";

    public function index()
    {
        try {
            return view('admin.category.index');
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
                    $data = Category::where('name', 'LIKE', "%{$input_search}%")->latest()->get();
                } else {
                    $data = Category::latest()->get();
                }

                imageNameToUrl($data, 'image', $this->folder);

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $btn = '<div class="d-flex justify-content-center gap-2">';
                        $btn .= '<a href="' . route("editCategory", $row->id) . '" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a> ';
                        $btn .= '<a href="' . route("deleteCategory", $row->id) . '" title="Delete" onclick="return confirm(\'Are you sure !!! You want to Delete this Category ?\')"><i class="fa-solid fa-trash-can"></i></a></div>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            } else {
                return view('admin.category.index');
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function add()
    {
        try {
            return view('admin.category.add');
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

            $user = new Category();
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
    public function edit($id)
    {
        try {

            $category = Category::where('id', $id)->first();

            imageNameToUrl(array($category), 'image', $this->folder);

            return view('admin.category.edit', ['result' => $category]);
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

            $category = Category::where('id', $request->id)->first();
            if (isset($category->id)) {

                $category->name = $request->name;

                if (isset($request->image)) {
                    $files = $request->image;
                    $category->image = saveImage($files, $this->folder);

                    deleteImageToFolder($this->folder, basename($request->old_image));
                }

                if ($category->save()) {
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
            $category = Category::where('id', $id)->first();
            $Video = Video::whereRaw("find_in_set('" . $category->id . "',video.category_id)")->first();
            $TVShow = TVShow::whereRaw("find_in_set('" . $category->id . "',tv_show.category_id)")->first();

            if ($Video) {
                return back()->with('error', "This Category is used on some other table so you can not remove it.");
            } elseif ($TVShow) {
                return back()->with('error', "This Category is used on some other table so you can not remove it.");
            } else {
                if ($category->delete()) {

                    deleteImageToFolder($this->folder, $category->image);
                    return redirect()->route('category')->with('success', __('Label.Data Delete Successfully'));
                }
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
