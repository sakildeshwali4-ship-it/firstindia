<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\Download;
use App\Models\Users;
use App\Models\Video_Watch;
use Illuminate\Http\Request;
use Validator;
use Exception;
use Illuminate\Support\Facades\DB;

// Login Type = 1- Facebook, 2- Google, 3- OTP, 4- Normal, 5- Apple	

class ReportUserController extends Controller
{
    private $folder = "report";

    public function index()
    {
        try {
            return view('admin.report.index');
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

      public function data(Request $request)
{
    try {

        // Normal page load
        if (!$request->ajax()) {
            return view('admin.report.index');
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 1: Pre-aggregate enews_reads (BIG PERFORMANCE GAIN)
        |--------------------------------------------------------------------------
        */


        $readsSub = DB::table('enews_reads')
            ->join('e_newspapers', 'e_newspapers.id', '=', 'enews_reads.enews_id')
            ->select(
                'enews_reads.user_id',

                DB::raw("
                    SUM(
                        CASE 
                            WHEN e_newspapers.type = 'hindi'
                            THEN enews_reads.read_count
                            ELSE 0
                        END
                    ) as hindi_reads
                "),

                DB::raw("
                    SUM(
                        CASE 
                            WHEN e_newspapers.type = 'english'
                            THEN enews_reads.read_count
                            ELSE 0
                        END
                    ) as english_reads
                "),

                DB::raw("SUM(enews_reads.read_count) as total_reads")
            )
            ->groupBy('enews_reads.user_id');

        /*
        |--------------------------------------------------------------------------
        | STEP 2: Base users query (FILTER FIRST)
        |--------------------------------------------------------------------------
        */
        $data = DB::table('user')
            ->leftJoinSub($readsSub, 'reads', function ($join) {
                $join->on('reads.user_id', '=', 'user.id');
            })
            ->select(
                'user.id',
                'user.mobile',
                'user.created_at',
                DB::raw('COALESCE(reads.hindi_reads, 0) as hindi_reads'),
                DB::raw('COALESCE(reads.english_reads, 0) as english_reads'),
                DB::raw('COALESCE(reads.total_reads, 0) as total_reads')
            )
            ->where('user.type', '3');

        /*
        |--------------------------------------------------------------------------
        | STEP 3: Date filters (only when needed)
        |--------------------------------------------------------------------------
        */
      

        /*
        |--------------------------------------------------------------------------
        | STEP 4: DataTables response
        |--------------------------------------------------------------------------
        */
        return DataTables()::of($data)
            ->addIndexColumn()
            ->addColumn('date', function ($row) {
                return date('Y-m-d', strtotime($row->created_at));
            })
            ->addColumn('hindi_reads', fn ($row) => $row->hindi_reads)
            ->addColumn('english_reads', fn ($row) => $row->english_reads)
            ->addColumn('total_reads', fn ($row) => $row->total_reads)
            ->make(true);

    } catch (\Exception $e) {

        return response()->json([
            'status' => 400,
            'errors' => $e->getMessage()
        ]);
    }
}
    public function dataOLd(Request $request)
    {
         
		ini_set('memory_limit', '-1');
        try {
            if ($request) {
				  if ($request->length == 2147483647) {

                    $data = Users::leftJoin('enews_reads', 'enews_reads.user_id', '=', 'user.id')
                        ->leftJoin('e_newspapers', 'e_newspapers.id', '=', 'enews_reads.enews_id')
                        ->select(
                            'user.id',
                            'user.mobile',
                            'user.created_at',

                            DB::raw("
                                COALESCE(
                                    SUM(CASE WHEN e_newspapers.type = 'hindi' 
                                    THEN enews_reads.read_count ELSE 0 END), 0
                                ) as hindi_reads
                            "),

                            DB::raw("
                                COALESCE(
                                    SUM(CASE WHEN e_newspapers.type = 'english' 
                                    THEN enews_reads.read_count ELSE 0 END), 0
                                ) as english_reads
                            "),

                            DB::raw("
                                COALESCE(SUM(enews_reads.read_count), 0) as total_reads
                            ")
                        )->where('user.type', '3')
                        ->groupBy(
                            'user.id',
                            'user.mobile',
                            'user.created_at'
                        );
                    
                    return DataTables()::of($data)
                        ->addIndexColumn()
                        ->addColumn('date', function ($row) {
                            return date('Y-m-d', strtotime($row->created_at));
                        })
                        ->addColumn('total_reads', function ($row) {
                            return $row->total_reads;
                        })
                        ->make(true);

                } else {

                    
                        $input_type = $request['input_type'];

                        $data = Users::leftJoin('enews_reads', 'enews_reads.user_id', '=', 'user.id')
                            ->leftJoin('e_newspapers', 'e_newspapers.id', '=', 'enews_reads.enews_id')
                            ->select(
                                'user.id',
                                'user.mobile',
                                'user.created_at',

                                // Hindi Reads
                                DB::raw("
                                    COALESCE(
                                        SUM(CASE 
                                            WHEN e_newspapers.type = 'hindi' 
                                            THEN enews_reads.read_count 
                                            ELSE 0 
                                        END), 0
                                    ) as hindi_reads
                                "),

                                // English Reads
                                DB::raw("
                                    COALESCE(
                                        SUM(CASE 
                                            WHEN e_newspapers.type = 'english' 
                                            THEN enews_reads.read_count 
                                            ELSE 0 
                                        END), 0
                                    ) as english_reads
                                "),

                                // Total Reads
                                DB::raw("
                                    COALESCE(SUM(enews_reads.read_count), 0) as total_reads
                                ")
                            )->where('user.type', '3');

                        // 🔹 Date filters
                        if ($input_type == "today") {

                            $data->whereDate('user.created_at', now());

                        } elseif ($input_type == "month") {

                            $data->whereMonth('user.created_at', date('m'))
                                ->whereYear('user.created_at', date('Y'));

                        } elseif ($input_type == "year") {

                            $data->whereYear('user.created_at', date('Y'));
                        }

                        // 🔹 IMPORTANT: Strict mode safe
                        $data->groupBy(
                            'user.id',
                            'user.mobile',
                            'user.created_at'
                        );

                        return DataTables()::of($data)
                            ->addIndexColumn()
                            ->addColumn('date', function ($row) {
                                return date('Y-m-d', strtotime($row->created_at));
                            })
                            ->addColumn('hindi_reads', function ($row) {
                                return $row->hindi_reads;
                            })
                            ->addColumn('english_reads', function ($row) {
                                return $row->english_reads;
                            })
                            ->addColumn('total_reads', function ($row) {
                                return $row->total_reads;
                            })
                            ->make(true);
                }
            } else {
                return view('admin.report.index');
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function add()
    {
        try {
            return view('admin.report.add');
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function save(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'password' => 'required|min:4',
                'mobile' => 'required|numeric|unique:user,mobile',
                'email' => 'required|unique:user|email',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $user = new Users();

            $email_array = explode('@', $request->email);
            $user->user_name = user_name($email_array[0]);

            $user->name = $request->name;
            $user->mobile = $request->mobile;
            $user->email = $request->email;
            $user->password = $request->password;
            $user->type = 4;
            $user->api_token = "";
            $user->email_verify_token = "";
            $user->is_email_verify = "";

            $org_name = $request->file('image');
            $user->image = saveImage($org_name, $this->folder);

            if ($user->save()) {
                return response()->json(array('status' => 200, 'success' => __('Label.Data Add Successfully')));
            } else {
                return response()->json(array('status' => 400, 'errors' => __('Label.error_add_user')));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function edit($id)
    {
        try {
            $user = Users::where('id', $id)->first();

            imageNameToUrl(array($user), 'image', $this->folder);

            if ($user) {
                return view('admin.user.edit', ['result' => $user]);
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'mobile' => 'required|unique:user,mobile,' . $request->id,
                'email' => 'required|email|unique:user,email,' . $request->id,
                'image' => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $user = Users::where('id', $request->id)->first();
            if (isset($user->id)) {
                $user->name = $request->name;
                $user->mobile = $request->mobile;
                $user->email = $request->email;

                if (isset($request->image)) {
                    $files = $request->image;
                    $user->image = saveImage($files, $this->folder);

                    deleteImageToFolder($this->folder, basename($request->old_image));
                }

                if ($user->save()) {
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

            $user = Users::where('id', $id)->first();

            if ($user->delete()) {

                Bookmark::where('user_id', $user->id)->delete();
                Download::where('user_id', $user->id)->delete();
                Video_Watch::where('user_id', $user->id)->delete();

                deleteImageToFolder($this->folder, $user->image);
                return redirect()->route('user')->with('success', __('Label.Data Delete Successfully'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function searchUser(Request $request)
    {
        try {
            $name = $request->name;
            $user = Users::orWhere('name', 'like', '%' . $name . '%')->orWhere('mobile', 'like', '%' . $name . '%')->orWhere('email', 'like', '%' . $name . '%')->get();

            $url = url('admin/transaction/add?user_id');
            $text = '<table width="100%" class="table table-striped category-table text-center table-bordered"><tr><th class="table-active">Name</th><th class="table-active">Mobile</th><th class="table-active">Email</th><th class="table-active">Action</th></tr>';
            if ($user->count() > 0) {
                foreach ($user as $row) {

                    $a = '<a href="' . $url . '=' . $row->id . '">Select</a>';
                    $text .= '<tr><td>' . $row->name . '</td><td>' . $row->mobile . '</td><td>' . $row->email . '</td><td>' . $a . '</td></tr>';
                }
            } else {
                $text .= '<tr><td colspan="3">User Not Found</td></tr>';
            }
            $text .= '</table>';

            return response()->json(array('status' => 200, 'success' => 'Search User', 'result' => $text));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }




  public function exportExcel(Request $request)
{
    $fileName = 'user_read_report_' . date('Ymd_His') . '.csv';

    $headers = [
        "Content-Type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=\"$fileName\"",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $callback = function () use ($request) {

        $file = fopen('php://output', 'w');

        // CSV Header
        fputcsv($file, [
            'SN',
            'User',
            'Register Date',
            'Hindi Reads',
            'English Reads',
            'Total No Of Reads'
        ]);

        // Serial number counter
        $sn = 1;

        /*
        |--------------------------------------------------------------------------
        | Pre-aggregate reads
        |--------------------------------------------------------------------------
        */
        $readsSub = DB::table('enews_reads')
            ->join('e_newspapers', 'e_newspapers.id', '=', 'enews_reads.enews_id')
            ->select(
                'enews_reads.user_id',
                DB::raw("SUM(CASE WHEN e_newspapers.type='hindi' THEN enews_reads.read_count ELSE 0 END) as hindi_reads"),
                DB::raw("SUM(CASE WHEN e_newspapers.type='english' THEN enews_reads.read_count ELSE 0 END) as english_reads"),
                DB::raw("SUM(enews_reads.read_count) as total_reads")
            )
            ->groupBy('enews_reads.user_id');

        $query = DB::table('user')
            ->leftJoinSub($readsSub, 'reads', function ($join) {
                $join->on('reads.user_id', '=', 'user.id');
            })
            ->select(
                'user.mobile',
                'user.created_at',
                DB::raw('COALESCE(reads.hindi_reads,0) as hindi_reads'),
                DB::raw('COALESCE(reads.english_reads,0) as english_reads'),
                DB::raw('COALESCE(reads.total_reads,0) as total_reads')
            );

        // Date filter
        if ($request->input_type == 'today') {
            $query->whereDate('user.created_at', now());
        } elseif ($request->input_type == 'month') {
            $query->whereMonth('user.created_at', now()->month)
                  ->whereYear('user.created_at', now()->year);
        } elseif ($request->input_type == 'year') {
            $query->whereYear('user.created_at', now()->year);
        }
 
        
        $query->orderBy('user.id')
            ->chunk(1000, function ($rows) use ($file, &$sn) {

                foreach ($rows as $row) {
                    fputcsv($file, [
                        $sn++,  
                        "\t".$row->mobile,   // 🔥 FIX HERE
                        date('Y-m-d', strtotime($row->created_at)),
                        $row->hindi_reads,
                        $row->english_reads,
                        $row->total_reads
                    ]);
                }
            });

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}









    
}
