<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Users;
use App\Models\TV_Login;
use App\Models\Transction;
use App\Models\General_Setting;
use App\Models\Audition;
use App\Models\AuditionApplication;
use App\Models\Package;
use App\Models\MobileOtp;
use App\Models\Payment_Option;
use Illuminate\Http\Request;
use Validator;
use Exception;
use Illuminate\Support\Facades\Mail;
use App\Helpers\NotificationHelper;

class UserController extends Controller
{
    private $folder = "user";

    public function registration(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'type' => 'required|numeric',
                    'name' => 'required',
                    'email' => 'required|unique:user|email',
                    'password' => 'required',
                    'mobile' => 'required|numeric',
                ],
                [
                    'type.required' => __('api_msg.please_enter_required_fields'),
                    'email.required' => __('api_msg.please_enter_required_fields'),
                    'mobile.required' => __('api_msg.please_enter_required_fields'),
                ]
            );
            if ($validation->fails()) {

                $errors = $validation->errors()->first('type');
                $errors1 = $validation->errors()->first('email');
                $errors2 = $validation->errors()->first('mobile');
                $data['status'] = 400;
                if ($errors) {
                    $data['message'] = $errors;
                } elseif ($errors1) {
                    $data['message'] = $errors1;
                } elseif ($errors2) {
                    $data['message'] = $errors2;
                } else {
                    $data['message'] = __('api_msg.please_enter_required_fields');
                }
                return $data;
            }

            $type = $request->type;
            $name = $request->name;
            $email = $request->email;
            $password = $request->password;
            $mobile = $request->mobile;
            $email_array = explode('@', $email);

            $data = array(
                'name' => $name,
                'user_name' => user_name($email_array[0]),
                'mobile' => $mobile,
                'email' => $email,
                'password' => $password,
                'image' => "",
                'type' => $type,
                'status' => 1,
                'expiry_date' => "2024-05-31", //temp expiry
                'api_token' => "",
                'email_verify_token' => "",
                'is_email_verify' => "",
            );

            $user_id = Users::insertGetId($data);

            if (isset($user_id)) {

                $user_data = Users::where('id', $user_id)->first();

                imageNameToUrl(array($user_data), 'image', $this->folder);

                // Send Mail (Type = 1- Register Mail, 2 Transaction Mail)
                Send_Mail(1, $user_data->email);

                if ($user_data['expiry_date'] == null) {
                    $user_data['expiry_date'] = "";
                }

                return APIResponse(200, __('api_msg.User_registration_sucessfuly'), array($user_data));
            } else {
                return APIResponse(400, __('api_msg.data_not_save'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function login(Request $request) // 1- Facebook, 2- Google, 3- OTP, 4- Normal, 5- Apple
    {
        try {

            if ($request->type == 1 || $request->type == 2 || $request->type == 5) {

                $validation = Validator::make(
                    $request->all(),
                    [
                        'email' => 'required|email',
                    ],
                    [
                        'email.required' => __('api_msg.please_enter_required_fields'),
                    ]
                );
                if ($validation->fails()) {

                    $errors = $validation->errors()->first('name');
                    $errors1 = $validation->errors()->first('email');
                    $data['status'] = 400;
                    if ($errors) {
                        $data['message'] = $errors;
                    } elseif ($errors1) {
                        $data['message'] = $errors1;
                    }
                    return $data;
                }
            } elseif ($request->type == 3) {

                $validation = Validator::make(
                    $request->all(),
                    [
                        'mobile' => 'required|numeric',
                    ],
                    [
                        'mobile.required' => __('api_msg.please_enter_required_fields'),
                    ]
                );
                if ($validation->fails()) {

                    $errors = $validation->errors()->first('mobile');
                    $data['status'] = 400;
                    if ($errors) {
                        $data['message'] = $errors;
                    }
                    return $data;
                }
            } elseif ($request->type == 4) {

                $validation = Validator::make(
                    $request->all(),
                    [
						"mobile" => "required_without:email",
						"email" => "required_without:mobile",
                        'password' => 'required',
                    ],
                    [
                        'email.required' => __('api_msg.please_enter_required_fields'),
                    ]
                );
                if ($validation->fails()) {

                    $errors = $validation->errors()->first('email');
                    $data['status'] = 400;
                    if ($errors) {
                        $data['message'] = $errors;
                    } else {
                        $data['message'] = __('api_msg.please_enter_required_fields');
                    }
                    return $data;
                }
            } else {
                $validation = Validator::make(
                    $request->all(),
                    [
                        'type' => 'required|numeric',
                    ],
                    [
                        'type.required' => __('api_msg.please_enter_required_fields'),
                    ]
                );
                if ($validation->fails()) {

                    $errors = $validation->errors()->first('type');
                    $data['status'] = 400;
                    if ($errors) {
                        $data['message'] = $errors;
                    }
                    return $data;
                }
            }

            $type = $request->type;
            $name = isset($request->name) ? $request->name : "";
            $email = isset($request->email) ? $request->email : "";
            $password = isset($request->password) ? $request->password : "";
            $mobile = isset($request->mobile) ? $request->mobile : "";
            $device_token = isset($request->device_token) ? $request->device_token : "";
            $device_type = isset($request->device_type) ? $request->device_type : "";

            if ($type == 1 || $type == 2 || $type == 5) {

                $data = Users::where('email', $email)->first();
                if (!empty($data)) {
					$data = $this->_saveDeviceToken ($data, $device_token);
                    // Image
                    imageNameToUrl(array($data), 'image', $this->folder);

                    if ($data->expiry_date == null) {
                        $data->expiry_date = "";
                    }

                    return APIResponse(200, __('api_msg.login_successfully'), array($data));
                } else {

                    $imageName = "";
                    if ($request->image != null) {
                        $org_name = $request->file('image');
                        $imageName = saveImage($org_name, $this->folder);
                    }
                    $email_array = explode('@', $email);

                    $data = array(
                        'name' => $name,
                        'user_name' => user_name($email_array[0]),
                        'mobile' => $mobile,
                        'email' => $email,
                        'password' => $password,
                        'image' => $imageName,
                        'type' => $type,
                        'status' => 1,
                        'expiry_date' => "2024-05-31", //temp expiry
                        'api_token' => "",
                        'email_verify_token' => "",
                        'is_email_verify' => "",
                        'device_token' => $device_token,
                        'device_type' => $device_type,
                    );
                    $user_id = Users::insertGetId($data);

                    if (isset($user_id)) {

                        $user_data = Users::where('id', $user_id)->first();

                        // Image
                        imageNameToUrl(array($user_data), 'image', $this->folder);

                        // Send Mail (Type = 1- Register Mail, 2 Transaction Mail)
                        if ($type == 2) {
                            Send_Mail(1, $user_data->email);
                        }

                        if ($user_data['expiry_date'] == null) {
                            $user_data['expiry_date'] = "";
                        }

                        return APIResponse(200, __('api_msg.login_successfully'), array($user_data));
                    } else {
                        return APIResponse(400, __('api_msg.data_not_save'));
                    }
                }
            } elseif ($type == 3) {

                $data = Users::where('mobile', $mobile)->first();
                if (!empty($data)) {
					$data = $this->_saveDeviceToken ($data, $device_token);
                    imageNameToUrl(array($data), 'image', $this->folder);
                    if ($data->expiry_date == null) {
                        $data->expiry_date = "";
                    }

                    return APIResponse(200, __('api_msg.login_successfully'), array($data));
                } else {

                    $data = array(
                        'name' => $name,
                        'user_name' => user_name($mobile),
                        'mobile' => $mobile,
                        'email' => $email,
                        'password' => $password,
                        'image' => "",
                        'type' => $type,
                        'status' => 1,
                        'expiry_date' => "2024-05-31", //temp expiry
                        'api_token' => "",
                        'email_verify_token' => "",
                        'is_email_verify' => "",
						'device_token' => $device_token,
                        'device_type' => $device_type,
                    );
                    $user_id = Users::insertGetId($data);
                    if (isset($user_id)) {

                        $user_data = Users::where('id', $user_id)->first();

                        imageNameToUrl(array($user_data), 'image', $this->folder);

                        if ($user_data['expiry_date'] == null) {
                            $user_data['expiry_date'] = "";
                        }

                        return APIResponse(200, __('api_msg.login_successfully'), array($user_data));
                    } else {
                        return APIResponse(400, __('api_msg.data_not_save'));
                    }
                }
            } elseif ($type == 4) {
				$data = Users::orWhere(function($query) use ($email,$mobile){
					$query->where('email', $email);
					$query->orWhere('mobile', $mobile);
				})
				->where('password', $password)->first();
                if (!empty($data)) {
					$data = $this->_saveDeviceToken ($data, $device_token);
                    imageNameToUrl(array($data), 'image', $this->folder);
                    if ($data->expiry_date == null) {
                        $data->expiry_date = "";
                    }
                    return APIResponse(200, __('api_msg.login_successfully'), array($data));
                } else {
                    return APIResponse(400, __('api_msg.email_pass_worng'), []);
                }
            } else {
                return APIResponse(400, __('api_msg.change_type'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
	
	private function _saveDeviceToken ($data, $device_token) {
		if(!empty($data) && !empty($device_token)) {
			$data->device_token = $device_token;
			$data->save();
		}
		return $data;
	}

    public function get_profile(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'id' => 'required|numeric',
                ],
                [
                    'id.required' => __('api_msg.please_enter_required_fields'),
                ]
            );
            if ($validation->fails()) {

                $errors = $validation->errors()->first('id');
                $data['status'] = 400;
                if ($errors) {
                    $data['message'] = $errors;
                }
                return $data;
            }

            $id = $request->id;
            $Data = Users::where('id', $id)->first();
            if (!empty($Data)) {

                imageNameToUrl(array($Data), 'image', $this->folder);

                if ($Data->expiry_date == null) {
                    $Data->expiry_date = "";
                }
				
				$Data['is_password'] = 0;
				if($Data['password'] != '') {
					$Data['is_password'] = 1;
				}
                $Data['is_buy'] = IsBuyByUser($id);

                return APIResponse(200, __('api_msg.get_record_successfully'), array($Data));
            } else {
                return APIResponse(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage().$e->getLine()));
        }
    }

    public function image_upload(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'id' => 'required|numeric',
                    'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                ],
                [
                    'id.required' => __('api_msg.please_enter_required_fields'),
                    'image.required' => __('api_msg.please_enter_required_fields'),
                ]
            );
            if ($validation->fails()) {

                $errors = $validation->errors()->first('id');
                $errors1 = $validation->errors()->first('image');
                $data['status'] = 400;
                if ($errors) {
                    $data['message'] = $errors;
                } elseif ($errors1) {
                    $data['message'] = $errors1;
                }
                return $data;
            }

            $id = $request->id;
            $org_name = $request->file('image');

            $data = Users::where('id', $id)->first();
            if (!empty($data)) {

                deleteImageToFolder($this->folder, $data['image']);

                $data->image = saveImage($org_name, $this->folder);
                if ($data->save()) {
                    return APIResponse(200, __('api_msg.update_successfully', []));
                } else {
                    return APIResponse(400, __('api_msg.data_not_save'));
                }
            } else {
                return APIResponse(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function update_profile(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'id' => 'required|numeric',
                ],
                [
                    'id.required' => __('api_msg.please_enter_required_fields'),
                ]
            );
            if ($validation->fails()) {

                $errors = $validation->errors()->first('id');
                $data['status'] = 400;
                if ($errors) {
                    $data['message'] = $errors;
                }
                return $data;
            }

            $id = $request->id;
            $data = array();

            $User_Data = Users::where('id', $id)->first();
            if (!empty($User_Data)) {

                if (isset($request->name) && $request->name != '') {
                    $data['name'] = $request->name;
                }
                if (isset($request->email) && $request->email != '') {
                    $data['email'] = $request->email;
                }
                if (isset($request->mobile) && $request->mobile != '') {
                    $data['mobile'] = $request->mobile;
                }
				if (isset($request->password) && $request->password != '') {
                    $data['password'] = $request->password;
                }
				if (isset($request->device_token) && $request->device_token != '') {
                    $data['device_token'] = $request->device_token;
                }

                $User_Data->update($data);
                if (isset($User_Data)) {

                    imageNameToUrl(array($User_Data), 'image', $this->folder);

                    if ($User_Data['expiry_date'] == null) {
                        $User_Data['expiry_date'] = "";
                    }

                    return APIResponse(200, __('api_msg.update_profile_sucessfuly'), array($User_Data));
                }
            } else {
                return APIResponse(400, __('api_msg.User_id_worng'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    // TV Login
    public function get_tv_login_code(Request $request)
    {
        try {
            $validation = Validator::make(
                $request->all(),
                [
                    'device_token' => 'required',
                ],
                [
                    'device_token.required' => __('api_msg.please_enter_required_fields'),
                ]
            );
            if ($validation->fails()) {

                $errors = $validation->errors()->first('device_token');
                $data['status'] = 400;
                if ($errors) {
                    $data['message'] = $errors;
                }
                return $data;
            }

            $check = TV_Login::where('device_token', $request->device_token)->first();

            if (isset($check)) {

                if ($check->status == 1 && $check->user_id != 0) {

                    $check->device_token = $request->device_token;
                    $check->unique_code = TV_Login_Code();
                    $check->status = 0;
                    $check->user_id = 0;
                    $check->update();
                }
                return APIResponse(200, __('api_msg.get_record_successfully'), array($check));
            } else {

                $insert = new TV_Login();
                $insert->unique_code = TV_Login_Code();
                $insert->device_token = $request->device_token;
                $insert->status = 0;
                $insert->user_id = 0;

                if ($insert->save()) {
                    return APIResponse(200, __('api_msg.get_record_successfully'), array($insert));
                } else {
                    return APIResponse(400, __('api_msg.data_not_save'));
                }
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function tv_login(Request $request)
    {
        try {
            $validation = Validator::make(
                $request->all(),
                [
                    'user_id' => 'required|numeric',
                    'unique_code' => 'required',
                ],
                [
                    'user_id.required' => __('api_msg.please_enter_required_fields'),
                    'unique_code.required' => __('api_msg.please_enter_required_fields'),
                ]
            );
            if ($validation->fails()) {

                $errors = $validation->errors()->first('user_id');
                $errors1 = $validation->errors()->first('unique_code');
                $data['status'] = 400;
                if ($errors) {
                    $data['message'] = $errors;
                } else if ($errors1) {
                    $data['message'] = $errors1;
                }
                return $data;
            }

            $check = TV_Login::where('unique_code', $request->unique_code)->where('status', 0)->where('user_id', 0)->first();

            if (isset($check)) {

                $check->status = 1;
                $check->user_id = $request->user_id;

                if ($check->update()) {

                    $toUser[] = $check->device_token;

                    $data = array(
                        'id' => $check->id,
                        'user_id' => $check->user_id,
                        'device_token' => $check->device_token,
                        'unique_code' => $check->unique_code,
                        'status' => $check->status,
                    );

                    $noty = General_Setting::where('key', 'onesignal_apid')->orWhere('key', 'onesignal_rest_key')->get();
                    $notification = [];
                    foreach ($noty as $row) {
                        $notification[$row->key] = $row->value;
                    }
                    $ONESIGNAL_APP_ID = $notification['onesignal_apid'];
                    $ONESIGNAL_REST_KEY = $notification['onesignal_rest_key'];

                    $title = "Login SuccessFully.";

                    $fields = array(
                        'app_id' => $ONESIGNAL_APP_ID,
                        'include_android_reg_ids' => $toUser,
                        "isAndroid" => true,
                        "channel_for_external_user_ids" => "push",
                        'headings' => array("en" => $title),
                        'contents' => array("en" => $title),
                        'data' => $data,
                    );

                    $fields = json_encode($fields);

                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json; charset=utf-8',
                        'Authorization: Basic ' . $ONESIGNAL_REST_KEY
                    ));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    curl_setopt($ch, CURLOPT_HEADER, FALSE);
                    curl_setopt($ch, CURLOPT_POST, TRUE);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

                    $response = curl_exec($ch);
                    // dd($response);
                    curl_close($ch);

                    $data = Users::where('id', $check->user_id)->first();
                    if (isset($data)) {

                        // Image
                        imageNameToUrl(array($data), 'image', $this->folder);

                        unset($data['password']);

                        if ($data->expiry_date == null) {
                            $data->expiry_date = "";
                        }
                        return APIResponse(200, __('api_msg.get_record_successfully'), array($data));
                    } else {
                        return APIResponse(400, __('api_msg.User_id_worng'));
                    }
                } else {
                }
            } else {

                return APIResponse(400, "Code Is Wrong.",);
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
	
	public function subscriptionCurrentStatus () {
		$general_setting = General_Setting::select('*')->whereIn('key', ['check_is_login', 'check_is_subscribed', 'iso_app_version', 'ios_app_force_download', 'android_app_version', 'android_app_force_download'])->get();
		$setting = [];
		foreach ($general_setting as $row) {
			$setting[$row->key] = $row->value;
		}
		$data['is_login'] = $setting['check_is_login'] == 'true' ? true : false;
		$data['is_subscribed'] = $setting['check_is_subscribed'] == 'true' ? true : false;
        $data['iso_app_version'] = $setting['iso_app_version'];
        $data['ios_app_force_download'] = $setting['ios_app_force_download'] == 'true' ? true : false;
        $data['android_app_version'] = $setting['android_app_version'];
        $data['android_app_force_download'] = $setting['android_app_force_download'] == 'true' ? true : false;
		return APIResponse(200, __('api_msg.get_status_successfully'), $data);
	}
	
	public function getAdditionalBanner () {
		$general_setting = General_Setting::select('*')->whereIn('key', ['additional_banner_status', 'additional_banner_image', 'additional_banner_link'])->get();
		$retData = [];
		foreach ($general_setting as $row) {
			if($row->key == 'additional_banner_status') {
				$retData[$row->key] = ($row->value == 'true') ? true : false;
			} else {
				$retData[$row->key] = $row->value;
			}
		}
		return APIResponse(200, __('api_msg.get_status_successfully'), $retData);
	}
	
	public function tester() {
		$audition = AuditionApplication::with('audition')->with('audition.city:id,city_name')->where('id', 1)->first();
		$user = Users::select('mobile', 'email', 'password', 'type')->where('id', 590)->first();
		$audition->username = $user->type == 3 ? $user->mobile : $user->email;
		$audition->password = $user->password;
		//prd($transaction);
		try {
			Send_Mail('new_application', 'nandk1988@gmail.com', $audition);	
		} catch (Exception $e) {
			echo $e->getMessage();die;
		}
	}

    public function payment_options(Request $request) {
        try {
            /*$validation = Validator::make(
                $request->all(),
                [
                    'customer_id' => 'required|numeric',
                    'customer_phone' => 'required',
                    'amount' => 'required|numeric',
                    'order_type' => 'required'
                ]
            );
            if ($validation->fails()) {
                $error = $validation->errors()->first();
                $data['status'] = 400;
                $data['message'] = $error;
                return $data;
            }*/
            $payment_options = Payment_Option::where('is_live', 1)->pluck('name', 'id');
            return APIResponse(200, __('Get Payment Options List SuccessFully.'), $payment_options);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
	
	public function getOrderDetails(Request $request)
    {
        try {
            $validation = Validator::make(
                $request->all(),
                [
                    'order_id' => 'required'
                ]
            );
            if ($validation->fails()) {
                $error = $validation->errors()->first();
                $data['status'] = 400;
				$data['message'] = $error;
				return $data;
            }

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://api.cashfree.com/pg/orders/".$request['order_id']);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'x-api-version: 2023-08-01',
				'x-client-id: 637113dbfb45f7502e2242a94b311736',
				'x-client-secret: cfsk_ma_prod_daa9af043f36cbb71a2baa9d18d71f7d_699a201c',
				'Content-Type: application/json; charset=utf-8'
			));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_POST, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			$response = curl_exec($ch);
			curl_close($ch);
			return APIResponse(200, __('api_msg.get_record_successfully'), json_decode($response));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
	
	//Payments
	public function create_order(Request $request) {
        try {
            $validation = Validator::make(
                $request->all(),
                [
                    'customer_id' => 'required|numeric',
                    'customer_phone' => 'required',
                    'amount' => 'required|numeric',
                    'order_type' => 'required',
                    'payment_option_id' => 'required'
                ]
            );
            if ($validation->fails()) {
                $error = $validation->errors()->first();
                $data['status'] = 400;
				$data['errors'] = $error;
				return $data;
            }
			
			$Edate = null;
			$user = Users::select('mobile', 'email', 'name')->where('id', $request['customer_id'])->first();
			if($request['order_type'] == 'package') {
				$Pdata = Package::where('id', $request['package_id'])->where('status', '1')->first();
				if (!empty($Pdata)) {
					$Edate = date("Y-m-d", strtotime("$Pdata->time $Pdata->type"));
				} else {
					return APIResponse(400, __('api_msg.please_enter_right_package_id'));
				}
				if($request['package_id'] == 2) {
					$retUrl = 'https://supersingerplusrajasthan.com/';
					$paypalAmount = $request['amount'];
				} else {
					$retUrl = 'https://www.firstindiaplus.com/';
					$paypalAmount = 1.00;
				}
			} else if($request['order_type'] == 'audition') {
				$Adata = AuditionApplication::where('id', $request['audition_id'])->first();
				if (empty($Adata)) {
					return APIResponse(400, __('Application not found!'));
				}
				$retUrl = 'https://supersingerplusrajasthan.com/';
				$paypalAmount = 5.00;
			}
			
			$uniqueOrderId = mb_strimwidth(md5(date('Y-m-d H:i:s')), 0, 16);
			$transaction = new Transction();
            $transaction->user_id = isset($request->customer_id) ? $request->customer_id : 0;
            $transaction->payment_id = $uniqueOrderId;
            $transaction->package_id = $request['package_id'];
            $transaction->audition_id = $request['audition_id'];
            $transaction->live_grand_finale_id = $request['live_grand_finale_id'] ? $request['live_grand_finale_id'] : 0;
            $transaction->order_type = $request['order_type'];
			$transaction->payment_option_id = $request['payment_option_id'];
			if($request['payment_option_id'] == 2) {
				$transaction->amount = $paypalAmount;
			} else if($request['payment_option_id'] == 3) {
				$transaction->amount = $request['amount'] / 100;
			} else {
				$transaction->amount = $request['amount'];
			}
			$transaction->expiry_date = $Edate;
			if($request['payment_option_id'] == 2) {
				$transaction->currency_code = 'USD';
			} else {
				$transaction->currency_code = 'INR';
			}
			$transaction->status = 0;

            if ($transaction->save()) {
				if($request['payment_option_id'] == 2) {
					return $this->_create_paypal_order($request, $transaction, $uniqueOrderId, $retUrl, $user);//Razorpay payment
				} else if($request['payment_option_id'] == 3) {
					return $this->_create_razorpay_order($request, $transaction, $uniqueOrderId, $retUrl, $user);//Razorpay payment
				} else {
					return $this->_create_cashfree_order($request, $transaction, $uniqueOrderId, $retUrl, $user);//Cash free payment
				}
			} else {
				return response()->json(array('status' => 400, 'errors' => 'Error on orders!'));
			}
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
	
	private function _create_cashfree_order ($request, $transaction, $uniqueOrderId, $retUrl, $user) {
		$fields = array(
			'customer_details' => ['customer_id' => $request['customer_id'], 'customer_phone' => $request['customer_phone']],
			'order_tags' => ['auditionid' => $request['audition_id'], 'packageid' => $request['package_id'], 'amount' => $request['amount']],
			'order_meta' => ['return_url' => $retUrl.'#/public/paymentreturn/'.$uniqueOrderId],
			'order_id' => $uniqueOrderId,
			'order_currency' => 'INR',
			'order_amount' => $request['amount'],
		);

		$fields = json_encode($fields);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.cashfree.com/pg/orders");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'x-api-version: 2023-08-01',
			'x-client-id: 637113dbfb45f7502e2242a94b311736',
			'x-client-secret: cfsk_ma_prod_daa9af043f36cbb71a2baa9d18d71f7d_699a201c',
			'Content-Type: application/json; charset=utf-8'
		));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$response = curl_exec($ch);
		$transaction->update(['payment_response' => $response]);
		curl_close($ch);
		$response = json_decode($response);
		if(!empty($response)) {
			return APIResponse(200, __('api_msg.create_record_successfully'), $response);
		} else {
			return response()->json(array('status' => 400, 'errors' => 'Error on orders!'));
		}
	}
	
	private function _create_razorpay_order ($request, $transaction, $uniqueOrderId, $retUrl, $user) {
		$fields = array(
			'amount' => $request['amount'],
			'currency' => 'INR',
			'receipt' => 'Trn-'. $transaction->id,
			'notes' => ['auditionid' => $request['audition_id'], 'packageid' => $request['package_id'], 'customer_id' => $request['customer_id'], 'customer_phone' => $request['customer_phone']]
		);
		$razorpay_live_key = 'rzp_live_kSsZim6DiqXH5y';
		$razorpay_live_secret_key = 'c1ZoJNLtALkVgyspFnl68YpY';
		$fields = json_encode($fields);
		$authAPIkey="Basic ".base64_encode($razorpay_live_key.":".$razorpay_live_secret_key);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.razorpay.com/v1/orders");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Authorization: '.$authAPIkey
		));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$response = curl_exec($ch);
		$response = json_decode($response, true);
		$transaction->update(['payment_response' => $response, 'cf_payment_id' => $response['id']]);
		curl_close($ch);
		if(!empty($response['status']) && $response['status'] == 'created') {
			$return = [];
			$return['key'] = $razorpay_live_key;
			$return['amount'] = $response['amount'];
			$return['currency'] = $response['currency'];
			$return['name'] = '';
			$return['description'] = '';
			$return['image'] = '';
			$return['order_id'] = $response['id'];
			$return['callback_url'] = $retUrl.'#/public/paymentreturn/'.$uniqueOrderId;
			$return['prefill'] = ['name' => $user->name, 'email' => $user->email, 'contact' => $user->mobile];
			$return['notes'] = $response['notes'];
			$return['theme'] = ['color' => '#3399cc'];			
			return APIResponse(200, __('api_msg.create_record_successfully'), $return);
		} else {
			return response()->json(array('status' => 400, 'errors' => 'Error on orders!'));
		}
	}
	
	private function _create_paypal_order ($request, $transaction, $uniqueOrderId, $retUrl, $user) {
		/*$retUrl = $retUrl.'#/public/paymentreturn/'.$uniqueOrderId;
		$itemAmount = ['currency_code' => 'USD', 'value' => 100.00];
		$fields = array(
			'intent' => 'CAPTURE',
			'purchase_units' => [
				[
					'items' => [["name" => 'ok', 'description' => '', "quantity" => 1, 'unit_amount' => $itemAmount]],
					'amount' => array_merge($itemAmount, ['breakdown' => ['item_total' => $itemAmount]])
				]
			],
			'application_context' => ['return_url' => $retUrl, 'cancel_url' => $retUrl],
			'request_id' => $uniqueOrderId,
			'reference_id' => $uniqueOrderId
		);
		$live_key = 'AWWtrIXdvzqxSbrzzXXBsDpCr8zwCVGbZ6LZgvD9GUFSgWXGoKZQ6ron3fQJCq0cLhPn-mk0rzFM8OHx';
		$live_secret_key = 'EIn2NKaF2ToMog4R8vSfp5zBpmTSQvDasD0ie8hupXVXmpGI3sFQxdq-x7-YEPOf_0E8_wOrZ13dihFu';
		$fields = json_encode($fields);
		$authAPIkey="Basic ".base64_encode($live_key.":".$live_secret_key);
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://api-m.sandbox.paypal.com/v2/checkout/orders',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => $fields,
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'Prefer: return=representation',
				'PayPal-Request-Id: '.$uniqueOrderId,
				'Authorization: '.$authAPIkey
			),
		));
		$response = curl_exec($curl);
		$response = json_decode($response, true);
		$transaction->update(['payment_response' => $response, 'cf_payment_id' => $response['id']]);
		curl_close($curl);*/

		//if(!empty($response['status']) && $response['status'] == 'CREATED') {		
			return APIResponse(200, __('api_msg.create_record_successfully'), ['order_id' => $uniqueOrderId, 'amount' => $transaction->amount, 'currency_code' => $transaction->currency_code]);
		//} else {
			//return response()->json(array('status' => 400, 'errors' => 'Error on orders!'));
		//}
	}
	
	
	public function update_order_status(Request $request) {
        try {
            $validation = Validator::make(
                $request->all(),
                [
                    'order_id' => 'required'
                ]
            );
            if ($validation->fails()) {
                $error = $validation->errors()->first();
                $data['status'] = 400;
				$data['errors'] = $error;
				return $data;
            }
			$transaction = Transction::where('payment_id', $request['order_id'])->first();
			if(empty($transaction)) {
				return response()->json(array('status' => 400, 'errors' => 'Order not found!'));
			}
			if($transaction->payment_option_id == 2) {
				$payment_status = $this->_update_paypal_order_status($transaction, $request->response);
			} else if($transaction->payment_option_id == 3) {
				$payment_status = $this->_update_razorpay_order_status($transaction);
			} else {
				$payment_status = $this->_update_cashfree_order_status($transaction);
			}
			$return_payment_response = ['payment_status' => $payment_status, 'user_id' => $transaction->user_id];
			return APIResponse(200, __('Payment status updated successFully'), $return_payment_response);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
	
	private function _update_cashfree_order_status($transaction) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.cashfree.com/pg/orders/".$transaction->payment_id.'/payments');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'x-api-version: 2023-08-01',
			'x-client-id: 637113dbfb45f7502e2242a94b311736',
			'x-client-secret: cfsk_ma_prod_daa9af043f36cbb71a2baa9d18d71f7d_699a201c',
			'Content-Type: application/json; charset=utf-8'
		));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$response = curl_exec($ch);
		$payment_response = json_decode($response, 1);
		//prd($payment_response);
		curl_close($ch);
		$payment_status_numeric = $transaction->payment_status_numeric;
		if(!empty($payment_response[0]['payment_status'])) {
			if(!empty($transaction) && $transaction->payment_status_numeric == 0) {
				if($payment_response[0]['payment_status'] == 'SUCCESS') {
					$payment_status_numeric = 1;
					$user_data = Users::where('id', $transaction->user_id)->first();
					if($transaction->order_type == 'package') {
						if (isset($user_data)) {
							$user_data->expiry_date = $transaction->expiry_date;
							$user_data->save();
							Send_Mail(2, $user_data->email);
						}
					} else if ($transaction->order_type == 'audition') {
						$auditionApplication = AuditionApplication::findOrFail($transaction->audition_id);
						if(!empty($auditionApplication)) {
							$auditionApplication->update(['payment_status' => 'complete']);
							$mailsendemail = !empty($auditionApplication->email) ? $auditionApplication->email : $user_data->email;
							if(!empty($mailsendemail)) {
								$audition = AuditionApplication::with('audition')->with('audition.city:id,city_name')->where('id', $transaction->audition_id)->first();
								$audition->amount = !empty($transaction->amount) ? $transaction->amount : 0;
								//Send_Mail('audition_final_payment', $mailsendemail, $audition);
							}
						} else {
							return response()->json(array('status' => 400, 'errors' => 'Application not found to update!'));
						}
					}
				} else if ($payment_response[0]['payment_status'] == 'FAILED') {
					$payment_status_numeric = 2;
				} else if ($payment_response[0]['payment_status'] == 'USER_DROPPED') {
					$payment_status_numeric = 3; //Cancelled
				} else if ($payment_response[0]['payment_status'] == 'REFUND') {
					$payment_status_numeric = 4; //Refund
				}
				if(!empty($payment_status_numeric)) {
					$updateTransaction = [];
					$updateTransaction['payment_status_numeric'] = $payment_status_numeric;
					$updateTransaction['status'] = $payment_status_numeric;
					$updateTransaction['bank_reference'] = $payment_response[0]['bank_reference'];
					$updateTransaction['cf_payment_id'] = $payment_response[0]['cf_payment_id'];
					$updateTransaction['payment_completion_time'] = !empty($payment_response[0]['payment_completion_time']) ? date('Y-m-d H:i:s', strtotime($payment_response[0]['payment_completion_time'])) : null;
					$updateTransaction['payment_group'] = $payment_response[0]['payment_group'];
					$updateTransaction['payment_status'] = $payment_response[0]['payment_status'];
					$updateTransaction['payment_response'] = $transaction->payment_response.' '.$response;
					$transaction->update($updateTransaction);
				}
			}
		} else {
			$payment_status_numeric = 2;
			$transaction->update(['payment_status_numeric' => $payment_status_numeric, 'status' => 0]);
		}
		return $payment_status_numeric;
	}
	
	private function _update_razorpay_order_status($transaction) {
		$razorpay_live_key = 'rzp_live_kSsZim6DiqXH5y';
		$razorpay_live_secret_key = 'c1ZoJNLtALkVgyspFnl68YpY';
		$authAPIkey="Basic ".base64_encode($razorpay_live_key.":".$razorpay_live_secret_key);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.razorpay.com/v1/orders/".$transaction['cf_payment_id'].'/payments');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Authorization: '.$authAPIkey
		));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$response = curl_exec($ch);
		$payment_response = json_decode($response, 1);
		//prd($payment_response);
		curl_close($ch);
		$payment_status_numeric = $transaction->payment_status_numeric;
		if(!empty($payment_response['items'][0]['status'])) {
			if($transaction->payment_status_numeric == 0) {
				if($payment_response['items'][0]['status'] == 'captured') {
					$payment_status_numeric = 1;
					$user_data = Users::where('id', $transaction->user_id)->first();
					if($transaction->order_type == 'package') {
						if (isset($user_data)) {
							$user_data->expiry_date = $transaction->expiry_date;
							$user_data->save();
							Send_Mail(2, $user_data->email);
						}
					} else if ($transaction->order_type == 'audition') {
						$auditionApplication = AuditionApplication::findOrFail($transaction->audition_id);
						if(!empty($auditionApplication)) {
							$auditionApplication->update(['payment_status' => 'complete']);
							$mailsendemail = !empty($auditionApplication->email) ? $auditionApplication->email : $user_data->email;
							if(!empty($mailsendemail)) {
								$audition = AuditionApplication::with('audition')->with('audition.city:id,city_name')->where('id', $transaction->audition_id)->first();
								$audition->amount = !empty($transaction->amount) ? $transaction->amount : 0;
								//Send_Mail('audition_final_payment', $mailsendemail, $audition);
							}
						} else {
							return response()->json(array('status' => 400, 'errors' => 'Application not found to update!'));
						}
					}
				} else if ($payment_response['items'][0]['status'] == 'failed') {
					$payment_status_numeric = 2;
				} else if ($payment_response['items'][0]['status'] == 'refunded') {
					$payment_status_numeric = 4; //Refund
				}
				if(!empty($payment_status_numeric)) {
					$updateTransaction = [];
					$updateTransaction['payment_status_numeric'] = $payment_status_numeric;
					$updateTransaction['status'] = $payment_status_numeric;
					$updateTransaction['bank_reference'] = $payment_response['items'][0]['vpa'];
					$updateTransaction['payment_completion_time'] = !empty($payment_response['items'][0]['created_at']) ? date('Y-m-d H:i:s', $payment_response['items'][0]['created_at']) : null;
					$updateTransaction['payment_group'] = $payment_response['items'][0]['method'];
					$updateTransaction['payment_status'] = $payment_response['items'][0]['status'];
					$updateTransaction['payment_response'] = $transaction->payment_response.' '.$response;
					$transaction->update($updateTransaction);
				} else {
					return response()->json(array('status' => 400, 'errors' => 'Your payment is in progress!'));
				}
			}
		} else {
			$payment_status_numeric = 3;
			$transaction->update(['payment_status_numeric' => $payment_status_numeric, 'status' => 0]);
		}
		return $payment_status_numeric;
	}
	
	private function _update_paypal_order_status($transaction, $payment_response) {
		$payment_status_numeric = $transaction->payment_status_numeric;
		if(!empty($payment_response['status'])) {
			if($transaction->payment_status_numeric == 0) {
				if($payment_response['status'] == 'COMPLETED') {
					$payment_status_numeric = 1;
					$user_data = Users::where('id', $transaction->user_id)->first();
					if($transaction->order_type == 'package') {
						if (isset($user_data)) {
							$user_data->expiry_date = $transaction->expiry_date;
							$user_data->save();
							Send_Mail(2, $user_data->email);
						}
					} else if ($transaction->order_type == 'audition') {
						$auditionApplication = AuditionApplication::findOrFail($transaction->audition_id);
						if(!empty($auditionApplication)) {
							$auditionApplication->update(['payment_status' => 'complete']);
							$mailsendemail = !empty($auditionApplication->email) ? $auditionApplication->email : $user_data->email;
							if(!empty($mailsendemail)) {
								$audition = AuditionApplication::with('audition')->with('audition.city:id,city_name')->where('id', $transaction->audition_id)->first();
								$audition->amount = !empty($transaction->amount) ? $transaction->amount : 0;
								//Send_Mail('audition_final_payment', $mailsendemail, $audition);
							}
						} else {
							return response()->json(array('status' => 400, 'errors' => 'Application not found to update!'));
						}
					}
				} else if ($payment_response['status'] == 'failed') {
					$payment_status_numeric = 2;
				} else if ($payment_response['status'] == 'refunded') {
					$payment_status_numeric = 4; //Refund
				}
				if(!empty($payment_status_numeric)) {
					$updateTransaction = [];
					$updateTransaction['payment_status_numeric'] = $payment_status_numeric;
					$updateTransaction['status'] = $payment_status_numeric;
					$updateTransaction['bank_reference'] = $payment_response['purchase_units'][0]['payments']['captures'][0]['id'];
					$updateTransaction['cf_payment_id'] = $payment_response['id'];
					$updateTransaction['payment_completion_time'] = !empty($payment_response['create_time']) ? date('Y-m-d H:i:s', strtotime($payment_response['create_time'])) : null;
					$updateTransaction['payment_group'] = $payment_response['payer']['payer_id'];
					$updateTransaction['payment_status'] = $payment_response['status'];
					$updateTransaction['payment_response'] = $transaction->payment_response.' '.json_encode($payment_response);
					$transaction->update($updateTransaction);
				} else {
					return response()->json(array('status' => 400, 'errors' => 'Your payment is in progress!'));
				}
			}
		} else {
			$payment_status_numeric = 3;
			$transaction->update(['payment_status_numeric' => $payment_status_numeric, 'status' => 0]);
		}
		return $payment_status_numeric;
	}
	
	/*public function create_razorpay_order(Request $request) {
        try {
            $validation = Validator::make(
                $request->all(),
                [
                    'customer_id' => 'required|numeric',
                    'customer_phone' => 'required',
                    'amount' => 'required|numeric',
                    'order_type' => 'required',
                    'payment_option_id' => 'required'
                ]
            );
            if ($validation->fails()) {
                $error = $validation->errors()->first();
                $data['status'] = 400;
				$data['errors'] = $error;
				return $data;
            }
			$user = Users::select('mobile', 'email', 'name')->where('id', $request['customer_id'])->first();
			$Edate = null;
			if($request['order_type'] == 'package') {
				$Pdata = Package::where('id', $request['package_id'])->where('status', '1')->first();
				if (!empty($Pdata)) {
					$Edate = date("Y-m-d", strtotime("$Pdata->time $Pdata->type"));
				} else {
					return APIResponse(400, __('api_msg.please_enter_right_package_id'));
				}
				$retUrl = 'https://www.firstindiaplus.com/';
			} else if($request['order_type'] == 'audition') {
				$Adata = AuditionApplication::where('id', $request['audition_id'])->first();
				if (empty($Adata)) {
					return APIResponse(400, __('Application not found!'));
				}
				$retUrl = 'https://supersingerplusrajasthan.com/';
			}
			
			$uniqueOrderId = mb_strimwidth(md5(date('Y-m-d H:i:s')), 0, 16);
			
			$transaction = new Transction();
            $transaction->user_id = isset($request->customer_id) ? $request->customer_id : 0;
            $transaction->payment_id = $uniqueOrderId;
            $transaction->package_id = $request['package_id'];
            $transaction->audition_id = $request['audition_id'];
            $transaction->order_type = $request['order_type'];
            $transaction->payment_option_id = $request['payment_option_id'];
            $transaction->amount = $request['amount'];
			$transaction->expiry_date = $Edate;
			$transaction->currency_code = 'INR';
			$transaction->status = 0;

            if ($transaction->save()) {
				$fields = array(
					'amount' => $request['amount'],
					'currency' => 'INR',
					'receipt' => 'Trn-'. $transaction->id,
					'notes' => ['auditionid' => $request['audition_id'], 'packageid' => $request['package_id'], 'customer_id' => $request['customer_id'], 'customer_phone' => $request['customer_phone']]
				);
				$razorpay_live_key = 'rzp_live_kSsZim6DiqXH5y';
				$razorpay_live_secret_key = 'c1ZoJNLtALkVgyspFnl68YpY';
				$fields = json_encode($fields);
				$authAPIkey="Basic ".base64_encode($razorpay_live_key.":".$razorpay_live_secret_key);
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "https://api.razorpay.com/v1/orders");
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/json',
					'Authorization: '.$authAPIkey
				));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($ch, CURLOPT_HEADER, FALSE);
				curl_setopt($ch, CURLOPT_POST, TRUE);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
				$response = curl_exec($ch);
				$response = json_decode($response, true);
				$transaction->update(['payment_response' => $response, 'cf_payment_id' => $response['id']]);
				curl_close($ch);
                if(!empty($response['status']) && $response['status'] == 'created') {
    				$return = [];
    				$return['key'] = $razorpay_live_key;
    				$return['amount'] = $response['amount'];
    				$return['currency'] = $response['currency'];
    				$return['name'] = '';
    				$return['description'] = '';
    				$return['image'] = '';
    				$return['order_id'] = $response['id'];
    				$return['callback_url'] = $retUrl.'#/public/paymentreturn/'.$uniqueOrderId;
    				$return['prefill'] = ['name' => $user->name, 'email' => $user->email, 'contact' => $user->mobile];
    				$return['notes'] = $response['notes'];
    				$return['theme'] = ['color' => '#3399cc'];			
    				return APIResponse(200, __('api_msg.create_record_successfully'), $return);
                } else {
                    return response()->json(array('status' => 400, 'errors' => 'Error on orders!'));
                }
			} else {
				return response()->json(array('status' => 400, 'errors' => 'Error on orders!'));
			}
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
	
	public function update_razorpay_order_status(Request $request)
    {
        try {
            $validation = Validator::make(
                $request->all(),
                [
                    'order_id' => 'required'
                ]
            );
            if ($validation->fails()) {
                $error = $validation->errors()->first();
                $data['status'] = 400;
				$data['errors'] = $error;
				return $data;
            }
			$transaction = Transction::where('payment_id', $request['order_id'])->first();
			if(empty($transaction)) {
				return response()->json(array('status' => 400, 'errors' => 'Order not found!'));
			}
			$razorpay_live_key = 'rzp_live_kSsZim6DiqXH5y';
			$razorpay_live_secret_key = 'c1ZoJNLtALkVgyspFnl68YpY';
			$authAPIkey="Basic ".base64_encode($razorpay_live_key.":".$razorpay_live_secret_key);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://api.razorpay.com/v1/orders/".$transaction['cf_payment_id'].'/payments');
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Authorization: '.$authAPIkey
			));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_POST, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			$response = curl_exec($ch);
			$payment_response = json_decode($response, 1);
			//prd($payment_response);
			curl_close($ch);
			if(!empty($payment_response['items'][0]['status'])) {
				if($transaction->payment_status_numeric == 0) {
					$return_payment_status = 'pending';
					if($payment_response['items'][0]['status'] == 'captured') {
						$return_payment_status = 'success';
						$payment_status_numeric = 1;
						$user_data = Users::where('id', $transaction->user_id)->first();
						if($transaction->order_type == 'package') {
							if (isset($user_data)) {
								$user_data->expiry_date = $transaction->expiry_date;
								$user_data->save();
								Send_Mail(2, $user_data->email);
							}
						} else if ($transaction->order_type == 'audition') {
							$auditionApplication = AuditionApplication::findOrFail($transaction->audition_id);
							if(!empty($auditionApplication)) {
								$auditionApplication->update(['payment_status' => 'complete']);
								$mailsendemail = !empty($auditionApplication->email) ? $auditionApplication->email : $user_data->email;
								if(!empty($mailsendemail)) {
									$audition = AuditionApplication::with('audition')->with('audition.city:id,city_name')->where('id', $transaction->audition_id)->first();
									$audition->amount = !empty($transaction->amount) ? $transaction->amount : 0;
									//Send_Mail('audition_final_payment', $mailsendemail, $audition);
								}
							} else {
								throw new Exception('Application not found to update!');
							}
						}
					} else if ($payment_response['items'][0]['status'] == 'failed') {
						$return_payment_status = 'failed';
						$payment_status_numeric = 2;
					} else if ($payment_response['items'][0]['status'] == 'refunded') {
						$return_payment_status = 'refund';
						$payment_status_numeric = 4; //Refund
					}
					if(!empty($payment_status_numeric)) {
						$updateTransaction = [];
						$updateTransaction['payment_status_numeric'] = $payment_status_numeric;
                        $updateTransaction['status'] = $payment_status_numeric;
						$updateTransaction['bank_reference'] = $payment_response['items'][0]['vpa'];
						$updateTransaction['payment_completion_time'] = !empty($payment_response['items'][0]['created_at']) ? date('Y-m-d H:i:s', $payment_response['items'][0]['created_at']) : null;
						$updateTransaction['payment_group'] = $payment_response['items'][0]['method'];
						$updateTransaction['payment_status'] = $payment_response['items'][0]['status'];
						$updateTransaction['payment_response'] = $transaction->payment_response.' '.$response;
						$transaction->update($updateTransaction);
					} else {
						return response()->json(array('status' => 400, 'errors' => 'Your payment is in progress!'));
					}
				}
			} else {
				$return_payment_status = 'cancelled';
				$transaction->update(['payment_status_numeric' => 3, 'status' => 0]);
				//throw new Exception('Payment not initiated');
			}
			$return_payment_response = ['payment_status' => $return_payment_status];
			return APIResponse(200, __('Payment status updated successFully'), $return_payment_response);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }*/

    public function get_user_info(Request $request)
    {
        try {

            if (in_array($request->type, [1,2,4,5])) {

                $validation = Validator::make(
                    $request->all(),
                    [
                        'email' => 'required|email',
                    ],
                    [
                        'email.required' => __('api_msg.please_enter_required_fields'),
                    ]
                );
                if ($validation->fails()) {

                    $errors = $validation->errors()->first('name');
                    $errors1 = $validation->errors()->first('email');
                    $data['status'] = 400;
                    if ($errors) {
                        $data['message'] = $errors;
                    } elseif ($errors1) {
                        $data['message'] = $errors1;
                    }
                    return $data;
                }
            } elseif ($request->type == 3) {

                $validation = Validator::make(
                    $request->all(),
                    [
                        'mobile' => 'required|numeric',
                    ],
                    [
                        'mobile.required' => __('api_msg.please_enter_required_fields'),
                    ]
                );
                if ($validation->fails()) {

                    $errors = $validation->errors()->first('mobile');
                    $data['status'] = 400;
                    if ($errors) {
                        $data['message'] = $errors;
                    }
                    return $data;
                }
            } 

            $type = $request->type;
            //$name = isset($request->name) ? $request->name : "";
            $email = isset($request->email) ? $request->email : "";
            //$password = isset($request->password) ? $request->password : "";
            $mobile = isset($request->mobile) ? $request->mobile : "";
            $type_arr = [1,2,4,5];
            if (in_array($type, $type_arr)) {
                $data = Users::where('email', $email)->whereIn('type', $type_arr)->first();
                if (!empty($data)) {
                    return APIResponse(200, __('api_msg.login_successfully'), array($data));
                } else {
                    return APIResponse(400, __('api_msg.data_not_save'));
                }
            } elseif ($type == 3) {
                $data = Users::where(['mobile'=> $mobile,'type'=>3])->first();
                if (!empty($data)) {
                    return APIResponse(200, __('api_msg.login_successfully'), array($data));
                } else {
                    return APIResponse(400, __('api_msg.data_not_save'));
                }
            }  else {
                return APIResponse(400, __('api_msg.change_type'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
	
	public function send_otp(Request $request) {
        try {
            $validation = Validator::make(
                $request->all(),
                [
                    'mobile' => 'required'
                ],
                [
                    'mobile.required' => 'Please enter valid mobile number'
                ]
            );
            if ($validation->fails()) {

                $errors = $validation->errors()->first();
                $data['status'] = 400;
                if ($errors) {
                    $data['message'] = $errors;
                }
                return $data;
            }

            $mobile = $request->mobile;
            //$data = Users::where('mobile', $mobile)->first();
            //if (!empty($data)) {
				$otp = $this->_getOtp();
				$dataUpdated = MobileOtp::updateOrInsert(
					['mobile' => $mobile],
					['otp' => $otp]
				);
				if($dataUpdated) {
					$smsbody = $otp . " is your FirstIndiaPlus OTP. Do not share it with anyone.";
					$url = "http://sms.ishivax.in/api/smsapi?key=c52eccf5e7252571c7f162bb60ddf490&route=1&sender=FINDPS&number=".$mobile."&sms=".$smsbody."&templateid=1407171220626239944";
					$url = str_replace(' ', '%20', $url);
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);        
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, FALSE);
					curl_setopt($ch, CURLOPT_HEADER, FALSE);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
					$output = curl_exec($ch);          
					curl_close($ch);
				}
                return APIResponse(200, __('OTP sent successfully'), json_decode($output));
            //} else {
                //return APIResponse(400, __('api_msg.data_not_found'));
            //}
        } catch (Exception $e) {
			///echo $e->getMessage();die;
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
	
	private function _getOtp() {
		$otp = rand(111111, 999999);
		if($otp == 3011) {
			$this->_getOtp();
		}
		return $otp;
 	}
	
	public function verify_otp(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'mobile' => 'required',
                    'otp' => 'required|numeric'
                ],
                [
                    'mobile.required' => 'Please enter valid mobile number',
                    'otp.required' => 'Please enter valid OTP'
                ]
            );
            if ($validation->fails()) {

                $errors = $validation->errors()->first();
                $data['status'] = 400;
                if ($errors) {
                    $data['message'] = $errors;
                }
                return $data;
            }

            $mobile = $request->mobile;
            $otp = $request->otp;
            $data = MobileOtp::where('mobile', $mobile)->where('otp', $otp)->first();
            if (!empty($data)) {
				return APIResponse(200, __('api_msg.get_record_successfully'), true);
            } else {
                return APIResponse(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function CoupanCode(Request $request)
    {
        
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'user_id' => 'required'
                ],
                [
                    'user_id.required' => 'Please enter user ID'
                ]
            );
            if ($validation->fails()) {

                $errors = $validation->errors()->first();
                $data['status'] = 400;
                if ($errors) {
                    $data['message'] = $errors;
                }
                return $data;
            }

            $user_id = $request->user_id;


            $user = Users::where('id', $user_id)->first();

            if ($user && !empty($user->coupon_code)) {

                return response()->json([
                    'status' => true,
                    'message' => 'Coupon code already exists.',
                    'coupon_code' => $user->coupon_code
                ]);
            }


            do {
                $coupon_code = rand(100000, 999999);
                $exists = Users::where('coupon_code', $coupon_code)->exists();
            } while ($exists);


            $updated = Users::where('id', $user_id)->update(['coupon_code' => $coupon_code]);

            if ($updated) {
                return response()->json([
                    'status' => true,
                    'message' => 'Coupon code created successfully.',
                    'coupon_code' => $coupon_code
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'errors' => 'Coupon code not created!'
                ]);
            }
        } catch (Exception $e) {
            return response()->json(array('status' => true, 'errors' => $e->getMessage()));
        }
    }

    public function customNotifyOld(Request $request)
    {
        $request->validate([
            'fcmToken' => 'required',
            'type'     => 'required|in:Android,ios',
        ]);

        $deviceToken = $request->fcmToken;
        $title = $request->title ?? "jjddddj";
        $body  = $request->message ?? "jjj";

        // Load service account JSON
        $serviceAccount = json_decode(
            file_get_contents(storage_path('app/firebase.json')), true
        );

        $credentials = new \Google\Auth\Credentials\ServiceAccountCredentials(
            "https://www.googleapis.com/auth/firebase.messaging",
            $serviceAccount
        );

        $authToken = $credentials->fetchAuthToken(
            \Google\Auth\HttpHandler\HttpHandlerFactory::build()
        );

        $accessToken = $authToken["access_token"];
        $projectId = $serviceAccount["project_id"];

        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
     

        if ($request->type === "Android") {

            // ANDROID NOTIFICATION
            $payload = [
                "message" => [
                    "token" => $deviceToken,

                    "notification" => [
                        "title" => $title,
                        "body"  => $body
                    ],

                    "data" => [
                        "click_action" => "FLUTTER_NOTIFICATION_CLICK",
                        "title" => $title,
                        "body"  => $body
                    ],

                    "android" => [
                        "priority" => "high",
                        "notification" => [
                            "sound" => "default",
                            "channel_id" => "high_importance_channel"
                        ]
                    ]
                ]
            ];

        } else {
 
            $payload = [
                "message" => [
                    "token" => $deviceToken,

                    "apns" => [
                        "headers" => [
                            "apns-priority" => "10"
                        ],
                        "payload" => [
                            "aps" => [
                                "alert" => [
                                    "title" => $title,
                                    "body"  => $body
                                ],
                                "sound" => "default",
                                "badge" => 1
                            ]
                        ]
                    ]
                ]
            ];
        }

        // Headers
        $headers = [
            "Authorization: Bearer {$accessToken}",
            "Content-Type: application/json"
        ];

        // CURL SEND
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return response()->json([
            "http_code" => $httpCode,
            "response" => json_decode($response, true)
        ]);
    }

    public function customNotify(Request $request)
    {
        $request->validate([
            'fcmToken' => 'required',
            'type'     => 'required|in:Android,ios',
        ]);

        $deviceToken = $request->fcmToken;
        $type = $request->type;
        $title = $request->title ?? "jjddddj";
        $body  = $request->message ?? "jjj";

         

        NotificationHelper::sendNotification(
            $deviceToken,     // OR $user->fcm_token
            $type,               // ios OR android
            "New Update",
            "New E-Newspaper uploaded!",
            ["type" => "news"]
        );
         
    }



}
