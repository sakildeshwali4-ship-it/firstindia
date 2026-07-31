<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ads;
use App\Models\AdAssignment;
use App\Models\LiveTv;
use App\Models\Video;
use App\Services\AdsSocketNotifier;
use Exception;
use Illuminate\Http\Request;
use Validator;

class AdAssignmentController extends Controller
{
    public function index()
    {
        try {
            return view('admin.ad_assignments.index');
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function data(Request $request)
    {
        try {
            if ($request == true) {
                $input_search = $request['input_search'];
                $data = AdAssignment::with('ad', 'video', 'liveTv')->latest()->get();

                if ($input_search != null && isset($input_search)) {
                    $data = $data->filter(function ($row) use ($input_search) {
                        $search = strtolower($input_search);
                        return str_contains(strtolower($row->ad->title ?? ''), $search)
                            || str_contains(strtolower($this->assignableName($row)), $search)
                            || str_contains(strtolower($row->assignable_type), $search)
                            || str_contains(strtolower($row->ad_position ?? ''), $search);
                    })->values();
                }

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('ad_title', function ($row) {
                        return $row->ad->title ?? '-';
                    })
                    ->addColumn('assignable_label', function ($row) {
                        return ucfirst(str_replace('_', ' ', $row->assignable_type)) . ' - ' . $this->assignableName($row);
                    })
                    ->addColumn('active_badge', function ($row) {
                        if ($row->active == 1) {
                            return '<span class="badge badge-success">Active</span>';
                        }
                        return '<span class="badge badge-danger">Inactive</span>';
                    })
                    ->addColumn('action', function ($row) {
                        $btn = '<div class="d-flex justify-content-center gap-2">';
                        $btn .= '<a href="' . route("editAdAssignment", $row->id) . '" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a> ';
                        $btn .= '<a href="' . route("deleteAdAssignment", $row->id) . '" title="Delete" onclick="return confirm(\'Are you sure !!! You want to Delete this Ad Assignment ?\')"><i class="fa-solid fa-trash-can"></i></a></div>';
                        return $btn;
                    })
                    ->rawColumns(['active_badge', 'action'])
                    ->make(true);
            } else {
                return view('admin.ad_assignments.index');
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function add()
    {
        try {
            return view('admin.ad_assignments.add', $this->formData());
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function save(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), $this->rules());
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            if (!$this->assignableExists($request->assignable_type, $request->assignable_id)) {
                return response()->json(array('status' => 400, 'errors' => 'Selected assign item not found.'));
            }

            $assignment = new AdAssignment();
            $this->fillAssignment($assignment, $request);

            if ($assignment->save()) {
                app(AdsSocketNotifier::class)->notifyTargets([[
                    'type' => $assignment->assignable_type,
                    'id' => (int) $assignment->assignable_id,
                ]], 'admin_updated_assignment');
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
            $assignment = AdAssignment::where('id', $id)->first();
            if (!$assignment) {
                return redirect()->route('adAssignments')->with('error', __('Label.Data Not Found'));
            }

            return view('admin.ad_assignments.edit', array_merge($this->formData(), ['result' => $assignment]));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), array_merge(['id' => 'required|exists:ad_assignments,id'], $this->rules()));
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            if (!$this->assignableExists($request->assignable_type, $request->assignable_id)) {
                return response()->json(array('status' => 400, 'errors' => 'Selected assign item not found.'));
            }

            $assignment = AdAssignment::where('id', $request->id)->first();
            if (isset($assignment->id)) {
                $targets = [[
                    'type' => $assignment->assignable_type,
                    'id' => (int) $assignment->assignable_id,
                ]];

                $this->fillAssignment($assignment, $request);

                if ($assignment->save()) {
                    $targets[] = [
                        'type' => $assignment->assignable_type,
                        'id' => (int) $assignment->assignable_id,
                    ];
                    app(AdsSocketNotifier::class)->notifyTargets($targets, 'admin_updated_assignment');
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
            $assignment = AdAssignment::where('id', $id)->first();
            $targets = [];
            if ($assignment) {
                $targets[] = [
                    'type' => $assignment->assignable_type,
                    'id' => (int) $assignment->assignable_id,
                ];
            }

            if ($assignment && $assignment->delete()) {
                app(AdsSocketNotifier::class)->notifyTargets($targets, 'admin_deleted_assignment');
                return redirect()->route('adAssignments')->with('success', __('Label.Data Delete Successfully'));
            }

            return redirect()->route('adAssignments')->with('error', __('Label.Data Not Deleted'));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function videoAds($id)
    {
        try {
            $video = Video::where('id', $id)->first();
            if (!$video) {
                return redirect()->route('video')->with('error', __('Label.Data Not Found'));
            }

            $ads = Ads::where('active', 1)->orderBy('title')->get();
            $assignments = AdAssignment::where('assignable_type', 'video')
                ->where('assignable_id', $video->id)
                ->orderBy('sort_order')
                ->get();
            $selectedAds = $assignments->pluck('ad_id')->toArray();
            $adPosition = $assignments->first()->ad_position ?? 'mid_roll';

            return view('admin.video.assign_ads', [
                'video' => $video,
                'ads' => $ads,
                'selectedAds' => $selectedAds,
                'adPosition' => $adPosition,
            ]);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function saveVideoAds(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'video_id' => 'required|exists:video,id',
                'ad_ids' => 'nullable|array',
                'ad_ids.*' => 'exists:ads,id',
                'ad_position' => 'required|in:pre_roll,mid_roll,post_roll,banner',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            AdAssignment::where('assignable_type', 'video')
                ->where('assignable_id', $request->video_id)
                ->delete();

            $adIds = $request->ad_ids ?? [];
            foreach ($adIds as $index => $adId) {
                $assignment = new AdAssignment();
                $assignment->ad_id = $adId;
                $assignment->assignable_type = 'video';
                $assignment->assignable_id = $request->video_id;
                $assignment->ad_position = $request->ad_position;
                $assignment->sort_order = $index + 1;
                $assignment->active = 1;
                $assignment->save();
            }

            app(AdsSocketNotifier::class)->notifyTargets([[
                'type' => 'video',
                'id' => (int) $request->video_id,
            ]], 'admin_updated_assignment');

            return response()->json(array('status' => 200, 'success' => __('Label.Data Edit Successfully')));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function liveTvAds($id)
    {
        try {
            $liveTv = LiveTv::where('id', $id)->first();
            if (!$liveTv) {
                return redirect()->route('livetv.index')->with('error', __('Label.Data Not Found'));
            }

            $ads = Ads::where('active', 1)->orderBy('title')->get();
            $assignments = AdAssignment::where('assignable_type', 'live_tv')
                ->where('assignable_id', $liveTv->id)
                ->orderBy('sort_order')
                ->get();
            $selectedAds = $assignments->pluck('ad_id')->toArray();
            $adPosition = $assignments->first()->ad_position ?? 'mid_roll';

            return view('admin.Live_tv_url.assign_ads', [
                'liveTv' => $liveTv,
                'ads' => $ads,
                'selectedAds' => $selectedAds,
                'adPosition' => $adPosition,
            ]);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function saveLiveTvAds(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'live_tv_id' => 'required|exists:live_tvs,id',
                'ad_ids' => 'nullable|array',
                'ad_ids.*' => 'exists:ads,id',
                'ad_position' => 'required|in:pre_roll,mid_roll,post_roll,banner',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            AdAssignment::where('assignable_type', 'live_tv')
                ->where('assignable_id', $request->live_tv_id)
                ->delete();

            $adIds = $request->ad_ids ?? [];
            foreach ($adIds as $index => $adId) {
                $assignment = new AdAssignment();
                $assignment->ad_id = $adId;
                $assignment->assignable_type = 'live_tv';
                $assignment->assignable_id = $request->live_tv_id;
                $assignment->ad_position = $request->ad_position;
                $assignment->sort_order = $index + 1;
                $assignment->active = 1;
                $assignment->save();
            }

            app(AdsSocketNotifier::class)->notifyTargets([[
                'type' => 'live_tv',
                'id' => (int) $request->live_tv_id,
            ]], 'admin_updated_assignment');

            return response()->json(array('status' => 200, 'success' => __('Label.Data Edit Successfully')));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    private function formData()
    {
        return [
            'ads' => Ads::where('active', 1)->orderBy('title')->get(),
            'liveTvs' => LiveTv::orderBy('name')->get(),
            'videos' => Video::orderBy('name')->get(),
        ];
    }

    private function rules()
    {
        return [
            'ad_id' => 'required|exists:ads,id',
            'assignable_type' => 'required|in:live_tv,video',
            'assignable_id' => 'required|integer|min:1',
            'ad_position' => 'required|in:pre_roll,mid_roll,post_roll,banner',
            'sort_order' => 'nullable|integer|min:0',
            'active' => 'required|in:0,1',
        ];
    }

    private function fillAssignment(AdAssignment $assignment, Request $request)
    {
        $assignment->ad_id = $request->ad_id;
        $assignment->assignable_type = $request->assignable_type;
        $assignment->assignable_id = $request->assignable_id;
        $assignment->ad_position = $request->ad_position;
        $assignment->sort_order = $request->sort_order ?: 1;
        $assignment->active = $request->active;
    }

    private function assignableExists($type, $id)
    {
        if ($type == 'live_tv') {
            return LiveTv::where('id', $id)->exists();
        }

        return Video::where('id', $id)->exists();
    }

    private function assignableName($assignment)
    {
        if ($assignment->assignable_type == 'live_tv') {
            return $assignment->liveTv->name ?? '-';
        }

        return $assignment->video->name ?? '-';
    }
}
