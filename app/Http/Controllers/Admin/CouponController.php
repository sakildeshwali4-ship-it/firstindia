<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Validator;
use Exception;

class CouponController extends Controller
{
    public function index()
    {
        try {
            return view('admin.coupon.index');
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function data(Request $request)
    {
        try {
            if ($request == true) {

                $input_search = $request['input_search'];

                if ($input_search != null && isset($input_search)) {
                    $data = Coupon::where(function ($query) use ($input_search) {
                        $query->where('name', 'LIKE', "%{$input_search}%")->orWhere('unique_id', 'LIKE', "%{$input_search}%");
                    })->latest()->get();
                } else {
                    $data = Coupon::latest()->get();
                }

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $btn = '<div class="d-flex justify-content-center gap-2">';
                        $btn .= '<a href="' . route("editCoupon", $row->id) . '" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a> ';
                        $btn .= '<a href="' . route("deleteCoupon", $row->id) . '" title="Delete" onclick="return confirm(\'Are you sure !!! You want to Delete this Coupon ?\')"><i class="fa-solid fa-trash-can"></i></a></div>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            } else {
                return view('admin.coupon.index');
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function add()
    {
        try {
            return view('admin.coupon.add');
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function save(Request $request)
    {
        try {
            if ($request['amount_type'] == 1) {
                $validator = Validator::make($request->all(), [
                    'name' => 'required|min:2',
                    'start_date' => 'required',
                    'end_date' => 'required',
                    'price' => 'required',
                ]);
            } elseif ($request['amount_type'] == 2) {
                $validator = Validator::make($request->all(), [
                    'name' => 'required|min:2',
                    'start_date' => 'required',
                    'end_date' => 'required',
                    'price' => 'required|numeric|max:100',
                ]);
            }
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $insert = new Coupon();
            $insert->unique_id = Str::random(6);
            $insert->name = $request->name;
            $insert->start_date = $request->start_date;
            $insert->end_date = $request->end_date;
            $insert->amount_type = $request->amount_type;
            $insert->price = $request->price;
            $insert->is_use = $request->is_use;
            $insert->status = 1;

            if ($insert->save()) {
                return response()->json(array('status' => 200, 'success' => __('Label.Data Add Successfully')));
            } else {
                return response()->json(array('status' => 400, 'errors' => __('Label.Data Not Add')));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function randomAdd()
    {
        try {
            return view('admin.coupon.random_add');
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function randomSave(Request $request)
    {
        try {
            if ($request['amount_type'] == 1) {
                $validator = Validator::make($request->all(), [
                    'total_number' => 'required|numeric|min:1',
                    'start_date' => 'required',
                    'end_date' => 'required',
                    'price' => 'required',
                ]);
            } elseif ($request['amount_type'] == 2) {
                $validator = Validator::make($request->all(), [
                    'total_number' => 'required|numeric|min:1',
                    'start_date' => 'required',
                    'end_date' => 'required',
                    'price' => 'required|numeric|max:100',
                ]);
            }
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            for ($i = 0; $i < $request->total_number; $i++) {

                $insert = new Coupon();
                $insert->unique_id = Str::random(6);
                $insert->name = Str::random(8);
                $insert->start_date = $request->start_date;
                $insert->end_date = $request->end_date;
                $insert->amount_type = $request->amount_type;
                $insert->price = $request->price;
                $insert->is_use = $request->is_use;
                $insert->status = 1;
                $insert->save();
            }
            return response()->json(array('status' => 200, 'success' => __('Label.Data Add Successfully')));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function edit(Request $request, $id)
    {
        try {
            $data = Coupon::where('id', $id)->first();
            return view('admin.coupon.edit', ['result' => $data]);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function update(Request $request)
    {
        try {
            if ($request['amount_type'] == 1) {
                $validator = Validator::make($request->all(), [
                    'name' => 'required|min:2',
                    'start_date' => 'required',
                    'end_date' => 'required',
                    'price' => 'required',
                ]);
            } elseif ($request['amount_type'] == 2) {
                $validator = Validator::make($request->all(), [
                    'name' => 'required|min:2',
                    'start_date' => 'required',
                    'end_date' => 'required',
                    'price' => 'required|numeric|max:100',
                ]);
            }
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $update = Coupon::where('id', $request->id)->first();
            if (isset($update->id)) {

                $update->name = $request->name;
                $update->start_date = $request->start_date;
                $update->end_date = $request->end_date;
                $update->amount_type = $request->amount_type;
                $update->price = $request->price;
                $update->is_use = $request->is_use;

                if ($update->save()) {
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
            $data = Coupon::where('id', $id)->first();
            if ($data->delete()) {
                return redirect()->route('coupon')->with('success', __('Label.Data Delete Successfully'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
