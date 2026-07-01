<?php

namespace App\Http\Controllers\Admin;

use Validator;
use App\Models\Page;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;

class PageController extends Controller
{
    private $folder_app = "app";

    public function index()
    {
        try {
            return view('admin.page.index');
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
                    $data = Page::where('title', 'LIKE', "%{$input_search}%")->latest()->get();
                } else {
                    $data = Page::get();
                }

                imageNameToUrl($data, 'icon', $this->folder_app);

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {

                        $btn = '<div class="d-flex justify-content-center gap-2">';
                        $btn .= '<a href="' . route('editPage', [$row->id]) . '" title="Edit">';
                        $btn .= '<i class="fa-regular fa-pen-to-square"></i>';
                        $btn .= '</a>';
                        $btn .= '<a href="' . url('pages/' . $row->id) . '" target="_blank" title="View">';
                        $btn .= '<i class="fa-solid fa-eye"></i>';
                        $btn .= '</a>';
                        $btn .= '</div>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            } else {
                return view('admin.page.index');
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function edit($id)
    {
        try {
            $page = Page::where('id', $id)->first();

            imageNameToUrl(array($page), 'icon', $this->folder_app);


            return view('admin.page.edit', ['result' => $page]);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'required',
                'page_subtitle' => 'required',
            ]);

            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            } else {

                $page = Page::where('id', $request->id)->first();

                if (isset($page->id)) {

                    $page->title = $request->title;
                    $page->description = $request->description;
                    $page->page_subtitle = $request->page_subtitle;
                    if ($request->image != null && isset($request->image)) {
                        $org_name = $request->file('image');
                        $page->icon = saveImage($org_name, $this->folder_app);
                        deleteImageToFolder($this->folder_app, basename($request->old_image));
                    }
                    $page->status = 1;

                    if ($page->save()) {
                        return response()->json(array('status' => 200, 'success' => __('Label.Data Edit Successfully')));
                    } else {
                        return response()->json(array('status' => 400, 'errors' => __('Label.Data Not Updated')));
                    }
                }
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
