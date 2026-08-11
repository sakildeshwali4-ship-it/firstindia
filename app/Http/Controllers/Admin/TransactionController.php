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
			$total_users = Users::count();
            return view('admin.transaction.index', compact('total_users'));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function data(Request $request)
    {
        try {
            if ($request == true) {

                $all_data = Transction::where('package_id', '>', 0)->get();
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
                            ->orderBy('id', 'desc');
                    } else {
                        $data = Transction::with('package', 'user')
                            ->whereDay('created_at', date('d'))
                            ->whereMonth('created_at', date('m'))
                            ->whereYear('created_at', date('Y'))
                            ->orderBy('id', 'desc');
                    }
                } else if ($type == "month") {

                    if ($input_search != null && isset($input_search)) {
                        $data = Transction::where('payment_id', 'LIKE', "%{$input_search}%")
                            ->with('package', 'user')
                            ->whereMonth('created_at', date('m'))
                            ->whereYear('created_at', date('Y'))
                            ->orderBy('id', 'desc');
                    } else {
                        $data = Transction::with('package', 'user')
                            ->whereMonth('created_at', date('m'))
                            ->whereYear('created_at', date('Y'))
                            ->orderBy('id', 'desc');
                    }
                } else if ($type == "year") {

                    if ($input_search != null && isset($input_search)) {
                        $data = Transction::where('payment_id', 'LIKE', "%{$input_search}%")
                            ->with('package', 'user')
                            ->whereYear('created_at', date('Y'))
                            ->orderBy('id', 'desc');
                    } else {
                        $data = Transction::with('package', 'user')
                            ->whereYear('created_at', date('Y'))
                            ->orderBy('id', 'desc');
                    }
                } else {

                    if ($input_search != null && isset($input_search)) {
                        $data = Transction::where('payment_id', 'LIKE', "%{$input_search}%")->with('package', 'user')->orderBy('id', 'desc');
                    } else {
                        $data = Transction::with('package', 'user')->orderBy('id', 'desc');
                    }
                }
				if(!empty($request['order_type']) && $request['order_type'] != 'all') {
					$data->where('order_type', $request['order_type']); 
				}
				if(isset($request['order_status']) && $request['order_status'] != 'all') {
					$data->where('payment_status_numeric', $request['order_status']); 
				}
				$data->select('payment_status_numeric', 'payment_id', 'amount', 'created_at', 'expiry_date', 'user_id', 'package_id', 'audition_id', 'currency_code', 'order_type');
                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        if ($row->payment_status_numeric == 1) {
                            return "<button type='button' style='background:#15ca20; font-weight:bold; border: none;  color: white; padding: 4px 20px; outline: none;'>Completed</button>";
                        } else if ($row->payment_status_numeric == 2) {
                            return "<button type='button' style='background:#dc3545; font-weight:bold; border: none;  color: white; padding: 4px 20px; outline: none;'>Failed</button>";
                        } else if ($row->payment_status_numeric == 3) {
                            return "<button type='button' style='background:#dc3545; font-weight:bold; border: none;  color: white; padding: 4px 20px; outline: none;'>Cancelled</button>";
                        } else if ($row->payment_status_numeric == 4) {
                            return "<button type='button' style='background:#dc3545; font-weight:bold; border: none;  color: white; padding: 4px 20px; outline: none;'>Refund</button>";
                        } else {
                            return "<button type='button' style='background:#0dceec; font-weight:bold; letter-spacing:0.1px; border: none; color: white; padding: 5px 15px; outline: none;'>Pending</button>";
                        }
                    })
                    ->addColumn('date', function ($row) {
                        $date = date("Y-m-d", strtotime($row->created_at));
                        return $date;
                    })
					->addColumn('order_type', function ($row) {
                        return ucfirst($row->order_type);
                    })
					->with('package_complete_transaction', function() use ($data) {
						$package_complete_transaction = clone $data;
						return $package_complete_transaction->where('payment_status_numeric', 1)->where('order_type', 'package')->sum('amount');
					})->with('audition_complete_transaction', function() use ($data) {
						$audition_complete_transaction = clone $data;
						return $audition_complete_transaction->where('payment_status_numeric', 1)->where('order_type', 'audition')->sum('amount');
					})->with('package_complete_transaction_count', function() use ($data) {
						$package_complete_transaction_count = clone $data;
						return $package_complete_transaction_count->where('payment_status_numeric', 1)->where('order_type', 'package')->count();
					})->with('audition_complete_transaction_count', function() use ($data) {
						$audition_complete_transaction_count = clone $data;
						return $audition_complete_transaction_count->where('payment_status_numeric', 1)->where('order_type', 'audition')->count();
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
