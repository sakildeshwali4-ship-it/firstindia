<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RentTransction;
use App\Models\RentVideo;
use App\Models\Users;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Validator;

class RentTransactionController extends Controller
{
    public function index()
    {
        try {
            return view('admin.rent_transaction.index');
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function data(Request $request)
    {
        try {
            if ($request == true) {

                $all_data = RentTransction::get();
                for ($i = 0; $i < count($all_data); $i++) {
                    if ($all_data[$i]['expiry_date'] <= date("Y-m-d")) {
                        $all_data[$i]->status = 0;
                        $all_data[$i]->save();
                    }
                }

                $type = $request['type'];
                $input_search = $request['input_search'];

                if ($type == "today") {

                    if ($input_search != null && isset($input_search)) {
                        $data = RentTransction::where('payment_id', 'LIKE', "%{$input_search}%")->with('user')
                            ->whereDay('created_at', date('d'))
                            ->whereMonth('created_at', date('m'))
                            ->whereYear('created_at', date('Y'))
                            ->orderBy('status', 'desc')->latest()->get();
                    } else {
                        $data = RentTransction::with('user')
                            ->whereDay('created_at', date('d'))
                            ->whereMonth('created_at', date('m'))
                            ->whereYear('created_at', date('Y'))
                            ->orderBy('status', 'desc')->latest()->get();
                    }
                } else if ($type == "month") {

                    if ($input_search != null && isset($input_search)) {
                        $data = RentTransction::where('payment_id', 'LIKE', "%{$input_search}%")->with('user')
                            ->whereMonth('created_at', date('m'))
                            ->whereYear('created_at', date('Y'))
                            ->orderBy('status', 'desc')->latest()->get();
                    } else {
                        $data = RentTransction::with('user')
                            ->whereMonth('created_at', date('m'))
                            ->whereYear('created_at', date('Y'))
                            ->orderBy('status', 'desc')->latest()->get();
                    }
                } else if ($type == "year") {

                    if ($input_search != null && isset($input_search)) {
                        $data = RentTransction::where('payment_id', 'LIKE', "%{$input_search}%")->with('user')
                            ->whereYear('created_at', date('Y'))
                            ->orderBy('status', 'desc')->latest()->get();
                    } else {
                        $data = RentTransction::with('user')
                            ->whereYear('created_at', date('Y'))
                            ->orderBy('status', 'desc')->latest()->get();
                    }
                } else {

                    if ($input_search != null && isset($input_search)) {
                        $data = RentTransction::where('payment_id', 'LIKE', "%{$input_search}%")->with('user')->orderBy('status', 'desc')->latest()->get();
                    } else {
                        $data = RentTransction::with('user')->orderBy('status', 'desc')->latest()->get();
                    }
                }

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        if ($row->status == 1) {
                            return "<button type='button' style='background:#15ca20; font-size:14px; font-weight:bold; border: none;  color: white; padding: 4px 20px; outline: none;'>Active</button>";
                        } else {
                            return "<button type='button' style='background:#0dceec; font-size:14px; font-weight:bold; letter-spacing:0.1px; border: none; color: white; padding: 5px 15px; outline: none;'>Expiry</button>";
                        }
                    })
                    ->addColumn('video_name', function ($row) {
                        return RentTransction::getVideoName($row->video_id, $row->video_type);
                    })
                    ->addColumn('date', function ($row) {
                        $date = date("Y-m-d", strtotime($row->created_at));
                        return $date;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            } else {
                return view('admin.rent_transaction.index');
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function add(Request $request)
    {
        try {
            $user = Users::where('id', $request->user_id)->first();

            $rent_video = RentVideo::where('video_type', 1)->with('video')->latest()->get();
            $rent_show = RentVideo::where('video_type', 2)->with('tvshow')->latest()->get();

            return view('admin.rent_transaction.add', ['user' => $user, 'video' => $rent_video, 'show' => $rent_show]);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function searchUser(Request $request)
    {
        try {
            $name = $request->name;
            $user = Users::orWhere('name', 'like', '%' . $name . '%')->orWhere('mobile', 'like', '%' . $name . '%')->orWhere('email', 'like', '%' . $name . '%')->get();

            $url = url('admin/renttransaction/add?user_id');
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
    public function save(Request $request)
    {
        try {

            if (Auth::guard('admin')->user()->type != 1) {
                return response()->json(array('status' => 400, 'errors' => __('Label.You have no right to add, edit, and delete')));
            } else {

                $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                ]);
                if ($validator->fails()) {
                    $errs = $validator->errors()->all();
                    return response()->json(array('status' => 400, 'errors' => $errs));
                }

                if ($request->rent_video_id != "") {

                    $Rent_Video = RentVideo::where('id', $request->rent_video_id)->where('video_type', 1)->where('status', 1)->first();
                    if (!empty($Rent_Video)) {
                        $Edate = date("Y-m-d", strtotime("$Rent_Video->time $Rent_Video->type"));
                    } else {
                        return APIResponse(400, "Select Right Video.");
                    }

                    $insert = new RentTransction();
                    $insert->user_id = $request->user_id;
                    $insert->unique_id = "";
                    $insert->video_id = $Rent_Video->video_id;
                    $insert->price = $Rent_Video->price;
                    $insert->type_id = $Rent_Video->type_id;
                    $insert->video_type = $Rent_Video->video_type;
                    $insert->status = 1;
                    $insert->expiry_date = $Edate;
                    $insert->description = "Video";
                } else if ($request->rent_show_id != "") {

                    $Rent_Video = RentVideo::where('id', $request->rent_show_id)->where('video_type', 2)->where('status', 1)->first();
                    if (!empty($Rent_Video)) {
                        $Edate = date("Y-m-d", strtotime("$Rent_Video->time $Rent_Video->type"));
                    } else {
                        return APIResponse(400, "Select Right Show.");
                    }

                    $insert = new RentTransction();
                    $insert->user_id = $request->user_id;
                    $insert->unique_id = "";
                    $insert->video_id = $Rent_Video->video_id;
                    $insert->price = $Rent_Video->price;
                    $insert->type_id = $Rent_Video->type_id;
                    $insert->video_type = $Rent_Video->video_type;
                    $insert->status = 1;
                    $insert->expiry_date = $Edate;
                    $insert->description = "Show";
                } else {
                    return response()->json(array('status' => 400, 'errors' => "Select Video Or TVShow."));
                }

                $insert->payment_id = 'admin';
                $insert->currency_code = currency_code();

                if ($insert->save()) {
                    if ($insert->id) {
                        return response()->json(array('status' => 200, 'success' => "Transction Add Successfully"));
                    } else {
                        return response()->json(array('status' => 400, 'errors' => "Transction Not Add"));
                    }
                } else {
                    return response()->json(array('status' => 400, 'errors' => "Transction Not Add"));
                }
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
