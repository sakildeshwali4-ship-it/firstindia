<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Transction;
use App\Models\Users;
use Illuminate\Http\Request;
use Validator;
use Exception;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        try {
            return view('admin.transaction.index');
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function data(Request $request)
    {
        try {
            if ($request == true) {

                $all_data = Transction::get();
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
                        $data = Transction::where('payment_id', 'LIKE', "%{$input_search}%")
                            ->with('package', 'user')
                            ->whereDay('created_at', date('d'))
                            ->whereMonth('created_at', date('m'))
                            ->whereYear('created_at', date('Y'))
                            ->orderBy('status', 'desc')->latest()->get();
                    } else {
                        $data = Transction::with('package', 'user')
                            ->whereDay('created_at', date('d'))
                            ->whereMonth('created_at', date('m'))
                            ->whereYear('created_at', date('Y'))
                            ->orderBy('status', 'desc')->latest()->get();
                    }
                } else if ($type == "month") {

                    if ($input_search != null && isset($input_search)) {
                        $data = Transction::where('payment_id', 'LIKE', "%{$input_search}%")
                            ->with('package', 'user')
                            ->whereMonth('created_at', date('m'))
                            ->whereYear('created_at', date('Y'))
                            ->orderBy('status', 'desc')->latest()->get();
                    } else {
                        $data = Transction::with('package', 'user')
                            ->whereMonth('created_at', date('m'))
                            ->whereYear('created_at', date('Y'))
                            ->orderBy('status', 'desc')->latest()->get();
                    }
                } else if ($type == "year") {

                    if ($input_search != null && isset($input_search)) {
                        $data = Transction::where('payment_id', 'LIKE', "%{$input_search}%")
                            ->with('package', 'user')
                            ->whereYear('created_at', date('Y'))
                            ->orderBy('status', 'desc')->latest()->get();
                    } else {
                        $data = Transction::with('package', 'user')
                            ->whereYear('created_at', date('Y'))
                            ->orderBy('status', 'desc')->latest()->get();
                    }
                } else {

                    if ($input_search != null && isset($input_search)) {
                        $data = Transction::where('payment_id', 'LIKE', "%{$input_search}%")->with('package', 'user')->orderBy('status', 'desc')->latest()->get();
                    } else {
                        $data = Transction::with('package', 'user')->orderBy('status', 'desc')->latest()->get();
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
                    ->addColumn('date', function ($row) {
                        $date = date("Y-m-d", strtotime($row->created_at));
                        return $date;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            } else {
                return view('admin.transaction.index');
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function add(Request $request)
    {
        try {
            $user = Users::where('id', $request->user_id)->first();
            $package = Package::get();
            return view('admin.transaction.add', ['user' => $user, 'package' => $package]);
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
                    'package_id' => 'required',
                ]);
                if ($validator->fails()) {
                    $errs = $validator->errors()->all();
                    return response()->json(array('status' => 400, 'errors' => $errs));
                }

                $package = Package::where('id', $request->package_id)->first();
                $expiry_date = date('Y-m-d', strtotime('+' . $package->time . ' ' . strtolower($package->type)));

                $user = Users::where('id', $request->user_id)->first();
                if (isset($user->id)) {
                    $user->expiry_date = $expiry_date;
                    $user->save();
                }

                $Transction = new Transction();
                $Transction->user_id = $request->user_id;
                $Transction->unique_id = "";
                $Transction->package_id = $request->package_id;
                $Transction->description = $package->name;
                $Transction->amount = $package->price;
                $Transction->payment_id = 'admin';
                $Transction->currency_code = currency_code();
                $Transction->expiry_date = $expiry_date;
                $Transction->status = 1;

                if ($Transction->save()) {
                    if ($Transction->id) {
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
