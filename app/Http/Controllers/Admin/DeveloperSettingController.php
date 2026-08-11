<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\General_Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
use Exception;

class DeveloperSettingController extends Controller
{
	private $folder = "app";
	
    public function index()
    {
		if(Auth::user()->id != 3) {
			return redirect()->route('setting')->with('error', __('Label.You have no right to add, edit, and delete'));
		}
        try {
            $setting = General_Setting::select('*')->whereIn('key', ['check_is_login', 'check_is_subscribed', 'additional_banner_status', 'additional_banner_image', 'additional_banner_link', 'iso_app_version', 'ios_app_force_download', 'android_app_version', 'android_app_force_download','popup_banner_status', 'popup_banner_image', 'popup_banner_link', 'show_whats_up_button','popup_banner_live_status','home_banner_url','home_banner_image','enews_banner_image'])->get();
            $admin = Admin::select('*')->where('id', Auth::user()->id)->first();

            foreach ($setting as $row) {
                $data[$row->key] = $row->value;
            }

            if ($data && $admin) {
                return view('admin.developer_setting.index', ['result' => $data, 'admin' => $admin]);
            } else {
                abort(404);
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
	
	public function subscription(Request $request)
    {
		if(Auth::user()->id != 3) {
			return response()->json(array('status' => 400, 'errors' => 'Label.You have no right to add, edit, and delete'));
		}
        try {
            if (Auth::guard('admin')->user()->type != 1) {
                return response()->json(array('status' => 400, 'errors' => __('Label.You have no right to add, edit, and delete')));
            } else {
                $data = $request->all();
                $data["check_is_login"] = isset($data['check_is_login']) ? $data['check_is_login'] : false;
                $data["check_is_subscribed"] = isset($data['check_is_subscribed']) ? $data['check_is_subscribed'] : false;
                $data["additional_banner_status"] = isset($data['additional_banner_status']) ? $data['additional_banner_status'] : false;
                $data["additional_banner_link"] = isset($data['additional_banner_link']) ? $data['additional_banner_link'] : '';
				if (isset($data['additional_banner_image']) && $data['additional_banner_image'] != null) {
                    $additional_banner_image = $request->file('additional_banner_image');
                    $additional_banner_image = saveImage($additional_banner_image, $this->folder);
					$data['additional_banner_image'] = Get_Image('app', $additional_banner_image);
                } else {
                    if ($request->old_additional_banner_image) {
                        $data['additional_banner_image'] = $request->old_additional_banner_image;
                    } else {
                        $data['additional_banner_image'] = "";
                    }
                }
				
				$data["popup_banner_status"] = isset($data['popup_banner_status']) ? $data['popup_banner_status'] : false;
				$data["popup_banner_live_status"] = isset($data['popup_banner_live_status']) ? $data['popup_banner_live_status'] : false;
                $data["popup_banner_link"] = isset($data['popup_banner_link']) ? $data['popup_banner_link'] : '';
				if (isset($data['popup_banner_image']) && $data['popup_banner_image'] != null) {
                    $popup_banner_image = $request->file('popup_banner_image');
                    $popup_banner_image = saveImage($popup_banner_image, $this->folder);
					$data['popup_banner_image'] = Get_Image('app', $popup_banner_image);
                } else {
                    if ($request->old_popup_banner_image) {
                        $data['popup_banner_image'] = $request->old_popup_banner_image;
                    } else {
                        $data['popup_banner_image'] = "";
                    }
                }
				
				$data["show_whats_up_button"] = isset($data['show_whats_up_button']) ? $data['show_whats_up_button'] : false;
				
				$data["home_banner_url"] = isset($data['home_banner_url']) ? $data['home_banner_url'] : '';
				if (isset($data['home_banner_image']) && $data['home_banner_image'] != null) {
                    $home_banner_image = $request->file('home_banner_image');
                    $home_banner_image = saveImage($home_banner_image, $this->folder);
					$data['home_banner_image'] = Get_Image('app', $home_banner_image);
                } else {
                    if ($request->home_banner_image) {
                        $data['home_banner_image'] = $request->home_banner_image;
                    } else {
                        $data['home_banner_image'] = "";
                    }
                }

                if (isset($data['enews_banner_image']) && $data['enews_banner_image'] != null) {
                    $enews_banner_image = $request->file('enews_banner_image');
                    $enews_banner_image = saveImage($enews_banner_image, $this->folder);
                    $data['enews_banner_image'] = Get_Image('app', $enews_banner_image);
                } else {
                    if ($request->old_enews_banner_image) {
                        $data['enews_banner_image'] = $request->old_enews_banner_image;
                    } else {
                        $data['enews_banner_image'] = "";
                    }
                }

                foreach ($data as $key => $value) {
                    $setting = General_Setting::where('key', $key)->first();
                    if (isset($setting->id)) {
                        $setting->value = $value;
                        $setting->save();
                    }
                }
                return response()->json(array('status' => 200, 'success' => __('Label.save_setting')));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
