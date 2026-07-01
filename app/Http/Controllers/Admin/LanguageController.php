<?php

namespace App\Http\Controllers\Admin;

use Validator;
use App\Models\TVShow;
use App\Models\Video;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;

class LanguageController extends Controller
{

    private $folder = "language";

    public function index()
    {
        try {
            return view('admin.language.index');
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
                    $data = Language::where('name', 'LIKE', "%{$input_search}%")->latest()->get();
                } else {
                    $data = Language::latest()->get();
                }

                imageNameToUrl($data, 'image', $this->folder);

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $btn = '<div class="d-flex justify-content-center gap-2">';
                        $btn .= '<a href="' . route("editLanguage", $row->id) . '" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a> ';
                        $btn .= '<a href="' . route("deleteLanguage", $row->id) . '" title="Delete" onclick="return confirm(\'Are you sure !!! You want to Delete this Language ?\')"><i class="fa-solid fa-trash-can"></i></a></div>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            } else {
                return view('admin.language.index');
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function add()
    {
        try {
            return view('admin.language.add');
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

            $language = new Language();
            $language->name = $request->name;

            $org_name = $request->file('image');
            $language->image = saveImage($org_name, $this->folder);

            if ($language->save()) {
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

            $language = language::where('id', $id)->first();

            imageNameToUrl(array($language), 'image', $this->folder);

            return view('admin.language.edit', ['result' => $language]);
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

            $language = Language::where('id', $request->id)->first();
            if (isset($language->id)) {

                $language->name = $request->name;

                if (isset($request->image)) {
                    $files = $request->image;
                    $language->image = saveImage($files, $this->folder);

                    deleteImageToFolder($this->folder, basename($request->old_image));
                }

                if ($language->save()) {
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
            $language = Language::where('id', $id)->first();
            $Video = Video::whereRaw("find_in_set('" . $language->id . "',video.language_id)")->first();
            $TVShow = TVShow::whereRaw("find_in_set('" . $language->id . "',tv_show.language_id)")->first();

            if ($Video) {
                return back()->with('error', "This Language is used on some other table so you can not remove it.");
            } elseif ($TVShow) {
                return back()->with('error', "This Language is used on some other table so you can not remove it.");
            } else {
                if ($language->delete()) {

                    deleteImageToFolder($this->folder, $language->image);
                    return redirect()->route('language')->with('success', __('Label.Data Delete Successfully'));
                }
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
