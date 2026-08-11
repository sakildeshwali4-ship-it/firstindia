<?php

namespace App\Http\Controllers\Admin;

use App;
use App\Http\Controllers\Controller;
use App\Models\Ads;
use Exception;
use Illuminate\Http\Request;
use Validator;
use URL;

class AdsController extends Controller
{
    public function index()
    {
        try {
            return view('admin.ads.index');
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function data(Request $request)
    {
        try {
            if ($request == true) {
                $input_search = $request['input_search'];

                $data = Ads::query();
                if ($input_search != null && isset($input_search)) {
                    $data->where(function ($query) use ($input_search) {
                        $query->where('title', 'LIKE', "%{$input_search}%")
                            ->orWhere('type', 'LIKE', "%{$input_search}%")
                            ->orWhere('media_type', 'LIKE', "%{$input_search}%");
                    });
                }

                return DataTables()::of($data->latest()->get())
                    ->addIndexColumn()
                    ->addColumn('active_badge', function ($row) {
                        if ($row->active == 1) {
                            return '<span class="badge badge-success">Active</span>';
                        }
                        return '<span class="badge badge-danger">Inactive</span>';
                    })
                    ->addColumn('action', function ($row) {
                        $btn = '<div class="d-flex justify-content-center gap-2">';
                        $btn .= '<a href="' . route("editAds", $row->id) . '" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a> ';
                        $btn .= '<a href="' . route("deleteAds", $row->id) . '" title="Delete" onclick="return confirm(\'Are you sure !!! You want to Delete this Ad ?\')"><i class="fa-solid fa-trash-can"></i></a></div>';
                        return $btn;
                    })
                    ->rawColumns(['active_badge', 'action'])
                    ->make(true);
            } else {
                return view('admin.ads.index');
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function add()
    {
        try {
            return view('admin.ads.add');
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function save(Request $request)
    { 
        try {
            $validator = Validator::make($request->all(), $this->rules(false));
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $ad = new Ads();
            $this->fillAd($ad, $request);

            if ($ad->save()) {
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
            $ad = Ads::where('id', $id)->first();
            if (!$ad) {
                return redirect()->route('ads')->with('error', __('Label.Data Not Found'));
            }

            return view('admin.ads.edit', ['result' => $ad]);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), array_merge(['id' => 'required|exists:ads,id'], $this->rules(true)));
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $ad = Ads::where('id', $request->id)->first();
            if (isset($ad->id)) {
                $this->fillAd($ad, $request);

                if ($ad->save()) {
                    return response()->json(array('status' => 200, 'success' => __('Label.Data Edit Successfully')));
                } else {
                    return response()->json(array('status' => 400, 'errors' => __('Label.Data Not Updated')));
                }
            }

            return response()->json(array('status' => 400, 'errors' => __('Label.Data Not Found')));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function delete($id)
    {
        try {
            $ad = Ads::where('id', $id)->first();
            if ($ad && $ad->delete()) {
                return redirect()->route('ads')->with('success', __('Label.Data Delete Successfully'));
            }

            return redirect()->route('ads')->with('error', __('Label.Data Not Deleted'));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    private function rules(bool $isUpdate = false)
    {
        return [
            'title' => 'required|string|max:255',
            'type' => 'required|in:normal,l_band',
            'media_type' => 'required|in:image,video',

            'media_url' => [
                'nullable',
                'required_if:media_type,video',
                'url',
            ],

            'click_url' => 'nullable|url',

            'media_image' => [
                $isUpdate ? 'nullable' : 'required_if:media_type,image',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'start_after_seconds' => 'required|integer|min:0',
            'repeat_every_seconds' => 'required|integer|min:0',
            'duration_seconds' => 'required|integer|min:0',
            'skippable_after_seconds' => 'required|integer|min:0',
            'priority' => 'required|integer|min:0',
            'active' => 'required|in:0,1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];
    }

    private function rulesOLD()
    {
        return [
            'title' => 'required',
            'type' => 'required|in:normal,l_band',
            'media_url' => 'nullable',
            'media_type' => 'required|in:image,video',
            'click_url' => 'nullable', 
            'media_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'start_after_seconds' => 'required|integer|min:0',
            'repeat_every_seconds' => 'required|integer|min:0',
            'duration_seconds' => 'required|integer|min:0',
            'skippable_after_seconds' => 'required|integer|min:0',
            'priority' => 'required|integer|min:0',
            'active' => 'required|in:0,1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];
    }

    private function fillAdOLD(Ads $ad, Request $request)
    {
        $ad->title = $request->title;
        $ad->type = $request->type;
        $ad->media_url = $request->media_url ?: null;
        $ad->media_type = $request->media_type;
        $ad->click_url = $request->click_url ?: null;
        $ad->start_after_seconds = $request->start_after_seconds;
        $ad->repeat_every_seconds = $request->repeat_every_seconds;
        $ad->duration_seconds = $request->duration_seconds;
        $ad->skippable_after_seconds = $request->skippable_after_seconds;
        $ad->priority = $request->priority;
        $ad->active = $request->active;
        $ad->start_date = $request->start_date ?: null;
        $ad->end_date = $request->end_date ?: null;
         if ($request->media_type == 'image') {
            if ($request->hasFile('media_image')) {
                $image = $request->file('media_image');
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/ads'), $filename);
                $ad->media_url = 'images/ads/' . $filename;
            }
        } else {
            $ad->media_url = $request->media_url ?: null;
        }
    }

    private function fillAd(Ads $ad, Request $request)
    {
        $ad->title = $request->title;
        $ad->type = $request->type;
        $ad->media_type = $request->media_type;
        $ad->click_url = $request->click_url ?: null;
        $ad->start_after_seconds = $request->start_after_seconds;
        $ad->repeat_every_seconds = $request->repeat_every_seconds;
        $ad->duration_seconds = $request->duration_seconds;
        $ad->skippable_after_seconds = $request->skippable_after_seconds;
        $ad->priority = $request->priority;
        $ad->active = $request->active;
        $ad->start_date = $request->start_date ?: null;
        $ad->end_date = $request->end_date ?: null;

        if ($request->media_type === 'image') {

            // Only replace image when a new image is uploaded.
            if ($request->hasFile('media_image')) {

                // Delete previous local image, if available.
                if (
                    !empty($ad->media_url) &&
                    !filter_var($ad->media_url, FILTER_VALIDATE_URL)
                ) {
                    $oldImagePath = public_path($ad->media_url);

                    if (file_exists($oldImagePath) && is_file($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $image = $request->file('media_image');

                $directory = public_path('images/ads');

                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                $filename = time()
                    . '_'
                    . uniqid()
                    . '.'
                    . $image->getClientOriginalExtension();

                $image->move($directory, $filename);

                $ad->media_url = 'images/ads/' . $filename;
            }

            // When no new image is uploaded, keep existing media_url unchanged.

        } else {

            // For video, save the submitted video URL.
            $ad->media_url = $request->media_url ?: null;
        }
    }
}
