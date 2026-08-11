<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WebSeries; 
use Validator;
use Exception; 
use Illuminate\Support\Str;
use Auth; 
use DB;
use Carbon\Carbon; 

class WebSeriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $folder = "web_series";

    public function index()
    {
    
        try {
            return view('admin.web_series.index');
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

   public function data(Request $request){
        
        try {
            
            // ✅ AJAX Request Check
            if ($request->ajax()) {

                $input_search = $request->input_search;

                if (!empty($input_search)) {
                    $data = WebSeries::where('title', 'LIKE', "%{$input_search}%")
                        ->latest()
                        ->get();
                } else {
                    $data = WebSeries::latest()->get();
                }
                imageNameToUrl($data, 'thumbnail', $this->folder); 

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->editColumn('isActive', function ($row) {
                        if($row->isActive =='1'){
                            $status = '<span class="btn btn-success bg-success px-2">Active</span>';
                        }else{
                            $status = '<span class="btn btn-danger bg-danger px-2">InActive</span>';
                        }
                        return $status;
                    })
                    ->editColumn('release_date', function ($row) {
                        return $row->release_date
                            ? \Carbon\Carbon::parse($row->release_date)->format('d-m-Y')
                            : '-';
                    })
                    ->addColumn('action', function ($row) {

                        $btn = '<div class="d-flex justify-content-center gap-2">';
                        if(Auth::user()->id == 3){
                            $btn .= '<a class="btn btn-primary" href="' . route("web-series.reports", $row->id) . '" title="report">
                                   Reports
                                </a>';
                        }

                        $btn .= '<a href="' . route("web-series.edit", $row->id) . '" title="Edit">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </a>';

                        $btn .= '<a href="' . route("web-series.destroy", $row->id) . '" title="Delete"
                                    onclick="return confirm(\'Are you sure?\')">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>';

                        $btn .= '</div>';

                        return $btn;
                    })

                    ->rawColumns(['isActive','release_date','action'])
                    ->make(true);
            }

            // ✅ Normal Page Load
            return view('admin.web_series.index');

        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'errors' => $e->getMessage()
            ]);
        }
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            return view('admin.web_series.add');
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    { 
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|min:2',
                'isActive' => 'required',
                'release_date' => 'required|date',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'description' => 'required', 
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $webseries = new WebSeries();
            $webseries->title = $request->title; 
            $webseries->slug = Str::slug($request->title); 
            $webseries->isActive = $request->isActive;
            $webseries->status = '1';
            $webseries->description = $request->description;
            $webseries->release_date = $request->release_date ?? date('Y-m-d');
            $webseries->is_like = $request->is_like; 
            $webseries->is_dislike = $request->is_dislike; 
            $webseries->is_superlike = $request->is_superlike;
            $webseries->wishlist = $request->wishlist;
            $webseries->isHeader =$request->isHeader;

            $org_name = $request->file('image'); 
            if ($org_name != null) {
                $webseries->thumbnail = saveImage($org_name, $this->folder);
            } 

            $org_name1 = $request->file('landscape'); 
            if ($org_name1 != null) {
                $webseries->landscape = saveImage($org_name1, $this->folder);
            } 


            if ($webseries->save()) {
                return response()->json(array('status' => 200, 'success' => __('Label.Data Add Successfully')));
            } else {
                return response()->json(array('status' => 400, 'errors' => __('Label.Data Not Add')));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $webseries = WebSeries::where('id', $id)->first();

            imageNameToUrl(array($webseries), 'thumbnail', $this->folder); 
            imageNameToUrl(array($webseries), 'landscape', $this->folder); 

            return view('admin.web_series.edit', ['result' => $webseries]);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    { 
        try {
            
            $validator = Validator::make($request->all(), [
                'title' => 'required|min:2',
                'isActive' => 'required',
                'release_date' => 'required|date',
                'description' => 'required', 
            ]);
            if ($validator->fails()) {

                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $webseries = WebSeries::where('id', $request->id)->first();
            if (isset($webseries->id)) { 
                $webseries->title = $request->title;  
                $webseries->slug = Str::slug($request->title); 
                $webseries->isActive = $request->isActive;
                $webseries->status = '1';
                $webseries->description = $request->description;
                $webseries->release_date = $request->release_date ?? date('Y-m-d');
                $webseries->is_like = $request->is_like; 
                $webseries->is_dislike = $request->is_dislike; 
                $webseries->is_superlike = $request->is_superlike;
                $webseries->wishlist = $request->wishlist;
                $webseries->isHeader =$request->isHeader;

                if (isset($request->image)) {
                    $files = $request->image;
                    $webseries->thumbnail = saveImage($files, $this->folder);

                    deleteImageToFolder($this->folder, basename($request->old_image));
                }

                if (isset($request->landscape)) {
                    $files = $request->landscape;
                    $webseries->landscape = saveImage($files, $this->folder);

                    deleteImageToFolder($this->folder, basename($request->old_landscape));
                }
 
                if ($webseries->save()) {
                    return response()->json(array('status' => 200, 'success' => __('Label.Data Edit Successfully')));
                } else {
                    return response()->json(array('status' => 400, 'errors' => __('Label.Data Not Updated')));
                }
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     
    public function destroy($id)
    { 
        try {
            $webSeries = WebSeries::where('id', $id)->first(); 

                deleteImageToFolder($this->folder, $webSeries->thumbnail); 
                $webSeries->delete();
                return redirect()->route('web-series.index')->with('success', __('Label.Data Delete Successfully'));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function reports(){ 
        return view('admin.web_series.reports'); 
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
 
        $dateViews = DB::table('episodes as e')
            ->leftJoin('episode_views as ev', function ($join) use ($start, $end) {
                $join->on('e.id', '=', 'ev.episode_id')
                     ->whereBetween('ev.created_at', [$start, $end]);
            })
            ->select(
                'e.id',
                'e.episode_number',
                'e.name',
                DB::raw('COALESCE(SUM(ev.counted),0) as date_views')
            )
            ->groupBy('e.id','e.episode_number','e.name')
            ->orderBy('e.episode_number')
            ->get();

        // Total lifetime views
        $totalViews = DB::table('episodes')
            ->select('id','view')
            ->pluck('view','id');

        $data = [];

        foreach ($dateViews as $row) {
            $data[] = [
                'episode_id' => $row->id,
                'episode_number' => $row->episode_number,
                'episode_name' => $row->name,
                'date_views' => (int)$row->date_views,
                'total_views' => (int)($totalViews[$row->id] ?? 0),
            ];
        } 
        return response()->json([
            'data' => $data
        ]);
    }
}
