<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveTv;
use Illuminate\Http\Request;
use Validator;
use Exception;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon; 
use DB;

class LiveTvController extends Controller
{

    public function index()
    {
        $data = LiveTv::latest()->get();
        return view('admin.Live_tv_url.index', compact('data'));
    }


    public function liveTvData(Request $request)
    {
        $query = LiveTv::orderBy('id', 'desc');


        if ($request->search && isset($request->search['value'])) {
            $searchValue = $request->search['value'];
            $query->where('name', 'LIKE', "%{$searchValue}%");
        }
        return datatables()->of($query)
            ->addIndexColumn()
            ->addColumn('image', function ($row) {
                $url = $row->image; // storage prefix jaruri
                return '<img src="'.$url.'" height="50" />';
            })
            ->addColumn('dialog_image', function ($row) {
                $url = $row->dialog_image;
                return '<img src="'.$url.'" height="50" />';
            })
            ->addColumn('action', function ($row) {
                $editUrl = route('livetv.edit', $row->id);
                $deleteUrl = route('livetv.delete', $row->id);

                return '<div class="d-flex justify-content-center gap-2">
                            <a href="'.$editUrl.'"><i class="fa-regular fa-pen-to-square"></i></a>
                            <a href="'.$deleteUrl.'" onclick="return confirm(\'Delete?\')"><i class="fa-solid fa-trash-can"></i></a>
                        </div>';
            })
            ->rawColumns(['image','dialog_image','action']) // <-- important to render HTML
            ->make(true);
    }


    public function create()
    {
        return view('admin.Live_tv_url.add');
    }

public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name'         => 'required',
                'image'        => 'required|image',
                'dialog_image' => 'required|image',
                'url'          => 'required|url',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'errors' => $validator->errors()->all()
                ]);
            }

            // Ensure the directory exists
            $directory = public_path('live_tv');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true); // recursive creation if parent dirs don't exist
            }

            // Store image with timestamp
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move($directory, $imageName);
                $imagePath = 'live_tv/' . $imageName;
            }

            if ($request->hasFile('dialog_image')) {
                $dialogImage = $request->file('dialog_image');
                $dialogImageName = time() . '_' . $dialogImage->getClientOriginalName();
                $dialogImage->move($directory, $dialogImageName);
                $dialogImagePath = 'live_tv/' . $dialogImageName;
            }

            LiveTv::create([
                'name'         => $request->name,
                'image'        => $imagePath,
                'dialog_image' => $dialogImagePath,
                'url'          => $request->url,
            ]);

            return response()->json([
                'status' => 200,
                'success' => 'Live TV Added Successfully!'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 400,
                'errors' => $e->getMessage()
            ]);
        }
    }
    // public function store(Request $request)
    // {
    //     try {
    //         $validator = Validator::make($request->all(), [
    //             'name'         => 'required',
    //             'image'        => 'required',
    //             'dialog_image' => 'required',
    //             'url'          => 'required',
    //         ]);

    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'status' => 400,
    //                 'errors' => $validator->errors()->all()
    //             ]);
    //         }
    //         $imagePath = $request->file('image')->store('live_tv', 'public');
    //         $dialogImagePath = $request->file('dialog_image')->store('live_tv', 'public');


    //         LiveTv::create([
    //             'name'         => $request->name,
    //             'image'        => $imagePath,
    //             'dialog_image' => $dialogImagePath,
    //             'url'          => $request->url,
    //         ]);

    //         return response()->json([
    //             'status' => 200,
    //             'success' => 'Live TV Added Successfully!'
    //         ]);

    //     } catch (Exception $e) {
    //         return response()->json([
    //             'status' => 400,
    //             'errors' => $e->getMessage()
    //         ]);
    //     }
    // }


    public function edit($id)
    {
        $data = LiveTv::findOrFail($id);
        return view('admin.Live_tv_url.edit', compact('data'));
    }
public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'url'  => 'required',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
                'dialog_image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'errors' => $validator->errors()->all()
                ]);
            }

            $data = LiveTv::findOrFail($id);

            // Ensure the live_tv directory exists
            $directory = public_path('live_tv');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Image upload logic
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($data->image && file_exists(public_path($data->image))) {
                    unlink(public_path($data->image));
                }
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move($directory, $imageName);
                $imagePath = 'live_tv/' . $imageName;
            } else {
                $imagePath = $data->image; // keep old
            }

            if ($request->hasFile('dialog_image')) {
                // Delete old dialog image if exists
                if ($data->dialog_image && file_exists(public_path($data->dialog_image))) {
                    unlink(public_path($data->dialog_image));
                }
                $dialogImage = $request->file('dialog_image');
                $dialogImageName = time() . '_' . $dialogImage->getClientOriginalName();
                $dialogImage->move($directory, $dialogImageName);
                $dialogImagePath = 'live_tv/' . $dialogImageName;
            } else {
                $dialogImagePath = $data->dialog_image; // keep old
            }

            // Update record
            $data->update([
                'name' => $request->name,
                'url'  => $request->url,
                'image' => $imagePath,
                'dialog_image' => $dialogImagePath,
            ]);

            return response()->json([
                'status' => 200,
                'success' => 'Updated Successfully!'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 400,
                'errors' => $e->getMessage()
            ]);
        }
    }

    // public function update(Request $request, $id)
    // {
    //     try {
    //         $validator = Validator::make($request->all(), [
    //             'name' => 'required',
    //             'url'  => 'required',
    //             'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
    //             'dialog_image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp',
    //         ]);

    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'status' => 400,
    //                 'errors' => $validator->errors()->all()
    //             ]);
    //         }

    //         $data = LiveTv::findOrFail($id);

    //         // Image upload logic
    //         if ($request->hasFile('image')) {
    //             if ($data->image && Storage::disk('public')->exists($data->image)) {
    //                 Storage::disk('public')->delete($data->image);
    //             }
    //             $imagePath = $request->file('image')->store('live_tv', 'public');
    //         } else {
    //             $imagePath = $data->image; // keep old
    //         }

    //         if ($request->hasFile('dialog_image')) {
    //             if ($data->dialog_image && Storage::disk('public')->exists($data->dialog_image)) {
    //                 Storage::disk('public')->delete($data->dialog_image);
    //             }
    //             $dialogImagePath = $request->file('dialog_image')->store('live_tv', 'public');
    //         } else {
    //             $dialogImagePath = $data->dialog_image; // keep old
    //         }

    //         // Update record
    //         $data->update([
    //             'name' => $request->name,
    //             'url'  => $request->url,
    //             'image' => $imagePath,
    //             'dialog_image' => $dialogImagePath,
    //         ]);

    //         return response()->json([
    //             'status' => 200,
    //             'success' => 'Updated Successfully!'
    //         ]);

    //     } catch (Exception $e) {
    //         return response()->json([
    //             'status' => 400,
    //             'errors' => $e->getMessage()
    //         ]);
    //     }
    // }


    public function destroy($id)
    {
        try {
            $data = LiveTv::findOrFail($id);
            if ($data->image && Storage::disk('public')->exists($data->image)) {
                Storage::disk('public')->delete($data->image);
            }
            if ($data->dialog_image && Storage::disk('public')->exists($data->dialog_image)) {
                Storage::disk('public')->delete($data->dialog_image);
            }
            $data->delete();

            return redirect()
                ->route('livetv.index')
                ->with('success', 'Deleted Successfully');

        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'errors' => $e->getMessage()
            ]);
        }
    }


    public function reports(){ 
        return view('admin.Live_tv_url.reports'); 
    }

    public function filterReport(Request $request)
    {
        $filter = $request->filter;

        if($filter == 'custom'){
            $start = Carbon::parse($request->start)->startOfDay();
            $end = Carbon::parse($request->end)->endOfDay();
        }
        elseif ($filter === 'today') {
            $start = Carbon::today();
            $end   = Carbon::today()->endOfDay();
        }
        elseif ($filter === 'yesterday') {
            $start = Carbon::yesterday();
            $end   = Carbon::yesterday()->endOfDay();
        }
        elseif ($filter === 'week') {
            $start = Carbon::now()->startOfWeek();
            $end   = Carbon::now()->endOfWeek();
        }
        else {
            $start = Carbon::now()->startOfMonth();
            $end   = Carbon::now()->endOfMonth();
        }
 
       $dateViews = DB::table('live_tvs as e')
            ->leftJoin('tv_views as ev', function ($join) use ($start, $end) {
                $join->on('e.id', '=', 'ev.tv_id')
                     ->whereBetween('ev.last_view_at', [$start, $end]);
            })
            ->select(
                'e.id',
                'e.name',
                DB::raw('COALESCE(SUM(ev.view_count),0) as date_views')
            )
            ->groupBy('e.id', 'e.name')
            ->orderBy('e.id')
            ->get();

 
        $totalViews = DB::table('tv_views')
            ->select('tv_id', DB::raw('SUM(view_count) as total_views'))
            ->groupBy('tv_id')
            ->pluck('total_views', 'tv_id');


        $data = [];

        foreach ($dateViews as $row) {
            $data[] = [
                'tv_id' => $row->id,
                'tv_name' => $row->name,
                'date_views' => (int) $row->date_views,
                'total_views' => (int) ($totalViews[$row->id] ?? 0),
            ];
        } 

        return response()->json([
            'data' => $data
        ]);
    }























}