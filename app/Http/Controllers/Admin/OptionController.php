<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OptionData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Exception;

class OptionController extends Controller
{
    
    public function index()
    { 
        try {
    
            $type = OptionData::all();
            return view('admin.option.index', ['type' => $type]);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

 public function optionData(Request $request)
    {
        $query = OptionData::orderBy('id', 'desc');
 
        if ($request->language) {
            $query->where('type', $request->language);
        }

        if ($request->search) {
            $query->where('date', 'LIKE', "%{$request->search}%");
        }

        
        return datatables()->of($query)
            ->addIndexColumn()
            ->addColumn('image', function($row) {
                return asset($row->image);
            }) 
            ->addColumn('action', function ($row) {
                        $btn = '<div class="d-flex justify-content-center gap-2">'; 
                        // $btn .= '<a href="'. route('enews.view_reads',$row->id) .'" class="btn btn-primary" title="View Reads">View Reads</a>';
                        $btn .= '<a href="' . route('option.edit',$row->id) . '" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a>';
                        $btn .= '<a href="' . route('option.delete',$row->id) . '" title="Delete" onclick="return confirm(\'Are you sure !!! You want to Delete this Channel ?\')"><i class="fa-solid fa-trash-can"></i></a></div>';
                        return $btn;
                    })
            ->rawColumns(['image','action'])
            ->make(true);
    }

    public function create()
    {
        return view('admin.option.add');
    }

    public function store(Request $request)
    {
        
       try {
            $validator = Validator::make($request->all(), [
                'type'            => 'required',
                'url'            => 'required',
                'image' => 'nullable|mimetypes:image/png,image/jpeg,image/jpg|max:51200',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'errors' => $validator->errors()->all()
                ]);
            }
 
            $highlightPath = null;

            if ($request->hasFile('image')) {

                $imgFileName = time().'.'.$request->image->extension();

                $imgDestination = public_path('images/option');

                if (!file_exists($imgDestination)) {
                    mkdir($imgDestination, 0777, true);
                }

                $request->file('image')->move($imgDestination, $imgFileName);

                $highlightPath = 'images/option/' . $imgFileName;
            }
            
            OptionData::updateOrCreate(
                ['type'=>$request->type],
                [
                    'image'=> $highlightPath,
                    'url'=>$request->url,
                    'status'=>$request->status
                ]
            );
    
            
            return response()->json([
                'status'  => 200,
                'success' => 'Options Added Successfully!'
            ]);

        } catch (Exception $e) {
            dd($e->getMessage());
            return response()->json([
                'status' => 400,
                'errors' => $e->getMessage()
            ]);
        }
 
    }


    public function edit($id)
    {
        $news = OptionData::findOrFail($id);
        return view('admin.option.edit', compact('news'));
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'type'            => 'required',
                'image' => 'nullable|mimetypes:image/png,image/jpeg,image/jpg',
            ]);

            if ($validator->fails()) { 
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $exists = OptionData::where('type', $request->type)
                    ->where('id', '!=', $id)   // ignore current row
                    ->exists();

            if ($exists) {
                return response()->json([
                    'status' => 400,
                    'errors' => ["OptionData for this TYPE already exists."]
                ]);
            }

            $news = OptionData::findOrFail($id);

    
            if ($request->hasFile('image')) {
    
                if ($news->image && file_exists(public_path($news->image))) {
                    unlink(public_path($news->image));
                }
    
                $imgDestination = public_path('images/option');

                if (!file_exists($imgDestination)) {
                    mkdir($imgDestination, 0777, true);
                }
                
		$newImgName = time().'.'.$request->image->extension();
		$request->file('image')->move($imgDestination, $newImgName);
    
                $news->image = 'images/option/' . $newImgName;
            }
    
            $news->type   = $request->type;
            $news->url   = $request->url;
            $news->status = $request->status ?? $news->status;
 
            if ($news->save()) {
                return response()->json(array('status' => 200, 'success' => 'Option Data Updated Successfully!'));
            } else {
                return response()->json(array('status' => 400, 'errors' => __('Label.Data Not Updated')));
            }
 
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function destroy($id)
    {
        try {
            $news = OptionData::findOrFail($id);
 
            if ($news->image && file_exists(public_path($news->image))) {
                unlink(public_path($news->image));
            }
 
            $news->delete();

            return redirect()
                ->route('option.index')
                ->with('success', __('Label.Data Delete Successfully'));

        } catch (Exception $e) {

            return response()->json([
                'status' => 400,
                'errors' => $e->getMessage()
            ]);
        }

    }
    public function viewReads(){ 
        return view('admin.option.view_reads');

    }

    public function filterReport(Request $request)
    {
        $filter = $request->filter;

        // Date range
        if ($filter === 'today') {
            $start = Carbon::today();
            $end   = Carbon::today()->endOfDay();
        } elseif ($filter === 'yesterday') {
            $start = Carbon::yesterday();
            $end   = Carbon::yesterday()->endOfDay();
        } elseif ($filter === 'week') {
            $start = Carbon::now()->startOfWeek();
            $end   = Carbon::now()->endOfWeek();
        } else { // month
            $start = Carbon::now()->startOfMonth();
            $end   = Carbon::now()->endOfMonth();
        }

        /*
         |-------------------------------------------
         | DATE WISE VIEWS (English / Hindi)
         |-------------------------------------------
         */
        $dateViews = DB::table('e_newspapers as e')
            ->join('enews_reads as r', 'r.enews_id', '=', 'e.id')
            ->select(
                'e.type',
                DB::raw('SUM(r.read_count) as date_views')
            )
            ->whereBetween('e.date', [$start->toDateString(), $end->toDateString()])
            ->groupBy('e.type')
            ->get();

        /*
         |-------------------------------------------
         | TOTAL VIEWS (ALL TIME)
         |-------------------------------------------
         */
        $totalViews = DB::table('e_newspapers as e')
            ->join('enews_reads as r', 'r.enews_id', '=', 'e.id')
            ->select(
                'e.type',
                DB::raw('SUM(r.read_count) as total_views')
            )
            ->groupBy('e.type')
            ->pluck('total_views', 'type');

        // Merge both
        $data = [];
        foreach ($dateViews as $row) {
            $data[] = [
                'type'        => ucfirst($row->type),
                'date_views'  => (int) $row->date_views,
                'total_views' => (int) ($totalViews[$row->type] ?? 0),
            ];
        }

        return response()->json([
            'data' => $data
        ]);
    }
}
