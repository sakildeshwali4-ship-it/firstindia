<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ENewspaper;
use Illuminate\Http\Request;
use Validator;
use Exception;
use Illuminate\Support\Facades\Storage;
use App\Jobs\SendENewsNotificationJob; 
use App\Jobs\SendGlobalPushJob; 
use Carbon\Carbon;
use App\Models\Users;
use Illuminate\Support\Facades\Log;
use DB;

class TvReportController extends Controller
{
    public function index()
    {
        $data = ENewspaper::latest()->get();
        return view('admin.enews.index', compact('data'));
    }

    public function enewsData(Request $request)
    {
        $query = ENewspaper::orderBy('id', 'desc');
 
        if ($request->language) {
            $query->where('type', $request->language);
        }

        if ($request->search) {
            $query->where('date', 'LIKE', "%{$request->search}%");
        }

        
        return datatables()->of($query)
            ->addIndexColumn()
            ->addColumn('image', function($row) {
                return asset($row->highlight_image);
            })
            ->addColumn('pdf_file', function($row) {
                return asset($row->pdf_file);
            }) 
            ->addColumn('action', function ($row) {
                        $btn = '<div class="d-flex justify-content-center gap-2">'; 
                        // $btn .= '<a href="'. route('enews.view_reads',$row->id) .'" class="btn btn-primary" title="View Reads">View Reads</a>';
                        $btn .= '<a href="' . route('enews.edit',$row->id) . '" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a>';
                        $btn .= '<a href="' . route('enews.delete',$row->id) . '" title="Delete" onclick="return confirm(\'Are you sure !!! You want to Delete this Channel ?\')"><i class="fa-solid fa-trash-can"></i></a></div>';
                        return $btn;
                    })
            ->rawColumns(['image','action'])
            ->make(true);
    }

    public function create()
    {
        return view('admin.enews.add');
    }

    public function store(Request $request)
    {
       try {
            $validator = Validator::make($request->all(), [
                'type'            => 'required|in:hindi,english',
                'date'            => 'required|date',
                'pdf_file'        => 'required|mimes:pdf|max:51200',
                'highlight_image' => 'nullable|mimetypes:image/png,image/jpeg,image/jpg|max:51200',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'errors' => $validator->errors()->all()
                ]);
            }

            $exists = ENewspaper::where('type', $request->type)->where('date', $request->date)->exists();

            if ($exists) {
                return response()->json([
                    'status' => 400,
                    'errors' => ["This newspaper already exists for this date & type."]
                ]);
            }

            $pdfFileName = time() . '_' . str_replace(' ', '_', $request->file('pdf_file')->getClientOriginalName());
            $pdfDestination = public_path('images/enews/pdf');

            if (!file_exists($pdfDestination)) {
                mkdir($pdfDestination, 0777, true);
            }

            $request->file('pdf_file')->move($pdfDestination, $pdfFileName);

            $pdfPath = 'images/enews/pdf/' . $pdfFileName;

            $highlightPath = null;

            if ($request->hasFile('highlight_image')) {

                $imgFileName = time() . '_' . str_replace(' ', '_', $request->file('highlight_image')->getClientOriginalName());
                $imgDestination = public_path('images/enews/images');

                if (!file_exists($imgDestination)) {
                    mkdir($imgDestination, 0777, true);
                }

                $request->file('highlight_image')->move($imgDestination, $imgFileName);

                $highlightPath = 'images/enews/images/' . $imgFileName;
            }
 

            $news = ENewspaper::create([
                'type'            => $request->type,
                'date'            => $request->date,
                'pdf_file'        => $pdfPath,
                'highlight_image' => $highlightPath,
                'status'          => $request->status ?? 1,
            ]);
	   
       if($request->type =="english" || $request->type =="English"){

        Log::info("ENews Constroller start");
		$formattedDate = Carbon::parse($request->date)->format('d F Y');

            $title = "Today’s Newspaper is Live";
            $body  = "Hindi & English E-Newspapers are now available on First India Plus";
            // SendGlobalPushJob::dispatch(
            //         $title ,
            //         $body
            //     );
            SendENewsNotificationJob::dispatch(
                $title,
                $body,
                [
                    'type' => 'enews',
                    'date' => $formattedDate
                ]
            )->onQueue('enews');

       }

       //Log::info("ENews Constroller end");      
	 

            //$testUserIds = [83624, 3552, 3]; // yaha apne 3-4 users ki ID dalo

		

		//$allTokens = Users::whereIn('id', $testUserIds)->whereNotNull('device_token')
    //->pluck('device_token')
    //->toArray();

//$chunkSize = 300; // 300 per job – best balance

//foreach (array_chunk($allTokens, $chunkSize) as $chunk) {
  //  SendENewsNotificationJob::dispatch(
    //    $title,
      //  $body,
        //['type' => 'enews'],
        //$chunk
    //)->onQueue('enews');
//}



            
            return response()->json([
                'status'  => 200,
                'success' => 'E-Newspaper Added Successfully!'
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
        $news = ENewspaper::findOrFail($id);
        return view('admin.enews.edit', compact('news'));
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'type'            => 'required|in:hindi,english',
                'date'            => 'required|date',
                'pdf_file'        => 'nullable|mimes:pdf',
                'highlight_image' => 'nullable|mimetypes:image/png,image/jpeg,image/jpg',
            ]);

            if ($validator->fails()) { 
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $exists = ENewspaper::where('type', $request->type)
                    ->where('date', $request->date)
                    ->where('id', '!=', $id)   // ignore current row
                    ->exists();

            if ($exists) {
                return response()->json([
                    'status' => 400,
                    'errors' => ["E-Newspaper for this TYPE & DATE already exists."]
                ]);
            }

            $news = ENewspaper::findOrFail($id);

            if ($request->hasFile('pdf_file')) {
    
                if ($news->pdf_file && file_exists(public_path($news->pdf_file))) {
                    unlink(public_path($news->pdf_file));
                } 

                $pdfDestination = public_path('images/enews/pdf');

                if (!file_exists($pdfDestination)) {
                    mkdir($pdfDestination, 0777, true);
                }
    
                $newPdfName = time() . '_' . str_replace(' ', '_', $request->file('pdf_file')->getClientOriginalName());
                $request->file('pdf_file')->move($pdfDestination, $newPdfName);
    
                $news->pdf_file = 'images/enews/pdf/' . $newPdfName;
            }

    
            if ($request->hasFile('highlight_image')) {
    
                if ($news->highlight_image && file_exists(public_path($news->highlight_image))) {
                    unlink(public_path($news->highlight_image));
                }
    
                $imgDestination = public_path('images/enews/images');

                if (!file_exists($imgDestination)) {
                    mkdir($imgDestination, 0777, true);
                }
                
                $newImgName = time() . '_' . str_replace(' ', '_', $request->file('highlight_image')->getClientOriginalName());
                $request->file('highlight_image')->move($imgDestination, $newImgName);
    
                $news->highlight_image = 'images/enews/images/' . $newImgName;
            }
    
            $news->type   = $request->type;
            $news->date   = $request->date;
            $news->status = $request->status ?? $news->status;
 
            if ($news->save()) {
                return response()->json(array('status' => 200, 'success' => 'E-Newspaper Updated Successfully!'));
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
            $news = ENewspaper::findOrFail($id);
 
            if ($news->pdf_file && file_exists(public_path($news->pdf_file))) {
                unlink(public_path($news->pdf_file));
            }
 
            if ($news->highlight_image && file_exists(public_path($news->highlight_image))) {
                unlink(public_path($news->highlight_image));
            }
 
            $news->delete();

            return redirect()
                ->route('enews.index')
                ->with('success', __('Label.Data Delete Successfully'));

        } catch (Exception $e) {

            return response()->json([
                'status' => 400,
                'errors' => $e->getMessage()
            ]);
        }

    }
    public function viewReads(){ 
        return view('admin.enews.view_tv_reads');

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

        $typeMap = [
	        'english' => 'Sach Bedhadak',
	        'hindi'   => 'First India',
	    ];

        // Merge both
         $data = [];

	    foreach ($dateViews as $row) {

            $originalTotal = (int) ($totalViews[$row->type] ?? 0);
            $originalDate  = (int) $row->date_views;

            $data[] = [
                'type'        => $typeMap[$row->type] ?? ucfirst($row->type),
                'date_views'  => (int) round($originalDate * 0.9), // 10% less
                'total_views' => (int) round($originalTotal * 0.7), // 30% less
            ];
        }

        return response()->json([
	        'status' => true,
	        'filter' => $filter ?? 'month',
	        'data'   => $data
	    ]);
    }
}
