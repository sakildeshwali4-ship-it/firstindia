<?php

namespace App\Http\Controllers\Admin;

use Validator;
use App\Models\Payment_Option;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;

class PaymentOptionController extends Controller
{
    public function index()
    {
        try {
            return view('admin.payment.index');
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
                    $data = Payment_Option::where('name', 'LIKE', "%{$input_search}%")->latest()->get();
                } else {
                    $data = Payment_Option::latest()->get();
                }

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $btn = '<a href="' . route("editPayment", $row->id) . '" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a> ';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            } else {
                return view('admin.payment.index');
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function edit($id)
    {
        try {
            $payment = Payment_Option::where('id', $id)->first();
            return view('admin.payment.edit', ['result' => $payment]);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function update(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'visibility' => 'required',
                'is_live' => 'required',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $payment_option = Payment_Option::where('id', $request->id)->first();

            $data = $request->all();
            $payment_option->live_key_1 = isset($data['live_key_1']) ?  $data['live_key_1'] : '';
            $payment_option->live_key_2 = isset($data['live_key_2']) ?  $data['live_key_2'] : '';
            $payment_option->live_key_3 = isset($data['live_key_3']) ?  $data['live_key_3'] : '';
            $payment_option->test_key_1 = isset($data['test_key_1']) ?  $data['test_key_1'] : '';
            $payment_option->test_key_2 = isset($data['test_key_2']) ?  $data['test_key_2'] : '';
            $payment_option->test_key_3 = isset($data['test_key_3']) ?  $data['test_key_3'] : '';

            if (isset($payment_option->id)) {

                $payment_option->visibility = $request->visibility;
                $payment_option->is_live = $request->is_live;

                if ($payment_option->save()) {
                    return response()->json(array('status' => 200, 'success' => __('Label.Data Edit Successfully')));
                } else {
                    return response()->json(array('status' => 400, 'errors' => __('Label.Data Not Updated')));
                }
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
