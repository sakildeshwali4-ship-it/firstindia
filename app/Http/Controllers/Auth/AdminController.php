<?php

namespace App\Http\Controllers\Auth;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Exception;

class AdminController extends Controller
{

    use AuthenticatesUsers;
    protected $redirectTo = 'admin/login';

    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }
    public function getLogin()
    {
        try {
            return view('auth.login');
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function postLogin(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:4',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            } else {
                if ($token = Auth()->guard('admin')->attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
                    $user = auth()->guard('admin')->user();
                    return response()->json(array('status' => 200, 'success' => __('Label.success_login')));
                } else {
                    return response()->json(array('status' => 400, 'errors' => __('Label.error_login')));
                }
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function logout()
    {
        try {
            Auth()->guard('admin')->logout();
            return redirect()->route('adminLogin')->with('success', "Logout Successfully.");
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    
    public function cashfree_payment()
    {
        try {
            return view('admin.payment.cashfree_payment');
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
