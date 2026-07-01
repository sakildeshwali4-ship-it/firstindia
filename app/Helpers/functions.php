<?php

use App\Models\Users;
use App\Models\Admin;
use App\Models\General_Setting;
use App\Models\Smtp;
use App\Models\Category;
use App\Models\Cast;
use App\Models\Package_Detail;
use App\Models\Package;
use App\Models\Type;
use App\Models\RentTransction;
use App\Models\RentVideo;
use App\Models\TVShowVideo;
use App\Models\Transction;
use App\Models\Video_Watch;
use App\Models\Download;
use App\Models\Bookmark;
use App\Models\Payment_Option;
use App\Models\TV_Login;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

function prd ($arr) {
	echo '<pre>'; print_r($arr); die;
}

function adminEmail()
{
    return $emails = Admin::select('user_name', 'email')->where('id', Auth::user()->id)->first();
}
function smtpData()
{
    $setting = Smtp::first();

    if (isset($setting) && $setting != null) {
        return $setting;
    }
    return false;
}
function setting_imdb_key()
{
    $setting = General_Setting::get();
    $data = [];
    foreach ($setting as $value) {
        $data[$value->key] = $value->value;
    }
    return $data['imdb_api_key'];
}
function no_format($num)
{
    if ($num > 1000) {
        $x = round($num);
        $x_number_format = number_format($x);
        $x_array = explode(',', $x_number_format);
        $x_parts = array('K', 'M', 'B', 'T');
        $x_count_parts = count($x_array) - 1;
        $x_display = $x;
        $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
        $x_display .= $x_parts[$x_count_parts - 1];

        return $x_display;
    }
    return $num;
}
function setting_app_logo()
{
    $setting = General_Setting::get();
    $data = [];
    foreach ($setting as $value) {
        $data[$value->key] = $value->value;
    }
    return $data['app_logo'];
}
function saveImage($org_name, $folder)
{
    $img_ext = $org_name->getClientOriginalExtension();
    $filename = rand(10, 100) . time() . '.' . $img_ext;
    $path = $org_name->move(public_path('images/' . $folder), $filename);
    return $filename;
}
function package_detail($Pid)
{
    Package_Detail::where('package_id', $Pid)->delete();

    $Pdata = Package::where('id', $Pid)->first();
    $type = Type::whereIn('id', explode(",", $Pdata->type_id))->get();

    for ($i = 0; $i < count($type); $i++) {
        $type_name[] = $type[$i]->name;
    }

    $type_name_data = implode(',', $type_name);
    if ($type_name_data) {
        $type_name_data_status = 1;
    } else {
        $type_name_data_status = 0;
    }

    $Pdetail = Package_Detail::insert([
        ['package_id' => $Pdata->id, 'package_key' => "All Content", 'package_value' => $Pdata->status],
        ['package_id' => $Pdata->id, 'package_key' => $type_name_data, 'package_value' => $type_name_data_status],
        ['package_id' => $Pdata->id, 'package_key' => "Watch on tv or laptop ", 'package_value' => $Pdata->watch_on_laptop_tv],
        ['package_id' => $Pdata->id, 'package_key' => "Ads free movies and shows (except sports)", 'package_value' => $Pdata->ads_free_movies_shows],
        ['package_id' => $Pdata->id, 'package_key' => "number of devices that can be logged in", 'package_value' => $Pdata->no_of_device],
        ['package_id' => $Pdata->id, 'package_key' => "max video quality", 'package_value' => $Pdata->video_qulity],
    ]);
    return "Success";
}
function currency_code()
{
    $setting = General_Setting::get();
    $data = [];
    foreach ($setting as $value) {
        $data[$value->key] = $value->value;
    }
    return $data['currency_code'];
}
function string_cut($string, $len)
{
    if (strlen($string) > $len) {
        $string = mb_substr(strip_tags($string), 0, $len, 'utf-8') . '...';
        // $string = substr(strip_tags($string),0,$len).'...';
    }
    return $string;
}
function TimeToMilliseconds($str)
{

    $time = explode(":", $str);

    $hour = (int) $time[0] * 60 * 60 * 1000;
    $minute = (int) $time[1] * 60 * 1000;
    $sec = (int) $time[2] * 1000;
    $result = $hour + $minute + $sec;
    return $result;
}
function MillisecondsToTime($str)
{
    $Seconds = (int) $str / 1000;
    $Seconds = round($Seconds);

    $Format = sprintf('%02d:%02d:%02d', ((int) $Seconds / 3600), ((int) $Seconds / 60 % 60), ((int) $Seconds) % 60);
    return $Format;
}
function APIResponse($status, $message, $result = "")
{
    $data['status'] = $status;
    $data['message'] = $message;

    if ($result != "") {
        if (isset($result)) {
            $data['result'] = $result;
        }
    }

    return $data;
}
function GetCategoryNameByIds($ids)
{
    $Ids = explode(',', $ids);
    $data = Category::select('id', 'name')->whereIn('id', $Ids)->get();

    if (count($data) > 0) {

        foreach ($data as $key => $value) {
            $final_data[] = $value['name'];
        }

        $IDs = implode(", ", $final_data);
        return $IDs;
    } else {
        return "";
    }
}
function GetCastNameByIds($ids)
{
    $Ids = explode(',', $ids);
    $data = Cast::select('id', 'name')->whereIn('id', $Ids)->get();

    if (count($data) > 0) {

        foreach ($data as $key => $value) {
            $final_data[] = $value['name'];
        }

        $IDs = implode(", ", $final_data);
        return $IDs;
    } else {
        return "";
    }
}
function IsRentVideo($id, $video_type)
{
    $data = RentVideo::where('video_id', $id)->where('video_type', $video_type)->where('status', 1)->first();
    if (!empty($data)) {
        return 1;
    } else {
        return 0;
    }
}
function GetPriceByRentVideo($id, $video_type)
{
    $data = RentVideo::select('id', 'price')->where('video_id', $id)->where('video_type', $video_type)->where('status', 1)->first();
    if (!empty($data)) {
        return $data['price'];
    } else {
        return 0;
    }
}
function GetSessionByTVShowId($id)
{
    $data = TVShowVideo::select('session_id')->groupBy('session_id')->where('show_id', $id)->get();
    if (count($data) > 0) {

        foreach ($data as $key => $value) {
            $final_data[] = $value['session_id'];
        }
        $return = implode(",", $final_data);
        return $return;
    } else {
        return $return = "";
    }
}
function GetStopTimeByUser($user_id, $video_id, $type_id, $video_type)
{
    $data = Video_Watch::select('id', 'stop_time')->where('user_id', $user_id)->where('video_id', $video_id)->where('video_type', $video_type)->where('status', '1')->first();
    if (!empty($data)) {
        return (int) $data['stop_time'];
    } else {
        return 0;
    }
}
function Is_DownloadByUser($user_id, $video_id, $type_id, $video_type, $other_id = "0")
{
    $data = Download::where('user_id', $user_id)->where('video_id', $video_id)->where('type_id', $type_id)->where('video_type', $video_type)->where('other_id', $other_id)->first();
    if (!empty($data)) {
        return 1;
    } else {
        return 0;
    }
}
function Is_BookmarkByUser($user_id, $video_id, $type_id, $video_type)
{
    $data = Bookmark::where('user_id', $user_id)->where('video_id', $video_id)->where('type_id', $type_id)->where('video_type', $video_type)->where('status', 1)->first();
    if (!empty($data)) {
        return 1;
    } else {
        return 0;
    }
}
function VideoRentBuyByUser($user_id, $video_id, $type_id, $video_type)
{
    $all_data = RentTransction::get();
    for ($i = 0; $i < count($all_data); $i++) {
        if ($all_data[$i]['expiry_date'] < date("Y-m-d")) {
            $all_data[$i]->status = 0;
            $all_data[$i]->save();
        }
    }

    $data = RentTransction::where('user_id', $user_id)->where('video_id', $video_id)->where('type_id', $type_id)->where('video_type', $video_type)->where('status', 1)->first();
    if (!empty($data)) {
        return 1;
    } else {
        return 0;
    }
}
function IsBuyByUser($user_id)
{
    $all_data = Transction::get();
    for ($i = 0; $i < count($all_data); $i++) {
        if ($all_data[$i]['expiry_date'] <= date("Y-m-d")) {
            $all_data[$i]->status = '0';
            $all_data[$i]->save();
        }
    }

    $data = Transction::where('user_id', $user_id)->where('status', '1')->first();
    if (!empty($data)) {
        return 1;
    } else {
        return 0;
    }
}
function GetMiniteToFormate($minute) // $minute = 140
{
    $Time = explode(" ", $minute);
    $init = $Time[0];
    if ($init != "N/A") {

        $format = '%02d:%02d:%02d';

        $hours = floor($init / 60);
        $minutes = ($init % 60);
        $second = 00;

        return sprintf($format, $hours, $minutes, $second);
    } else {
        return "00:00:00";
    }
}
function URLSaveInImage($url, $folder)
{
    $ext = pathinfo($url);
    $image_name = rand(10, 100) . time() . '.' . $ext['extension'];
    $path = public_path() . '/' . 'images/' . $folder . '/';

    file_put_contents($path . $image_name, file_get_contents($url));
    return $image_name;
}
function Paytm($data)
{

    $option = Payment_Option::get();
    foreach ($option as $key => $value) {
        if ($value['name'] == "paytm") {
            $payment_data = $value;
        }
    }

    if (isset($payment_data) && $payment_data['visibility'] == 1) {

        if ($payment_data['is_live'] == 1) {
            $M_id = $payment_data['live_key_1'];
            $M_Key = $payment_data['live_key_2'];
        } else {
            $M_id = $payment_data['test_key_1'];
            $M_Key = $payment_data['test_key_2'];
        }

        /* import checksum generation utility */
        require_once base_path() . '/vendor/paytm/paytmchecksum/PaytmChecksum.php';

        /* initialize an array */
        $paytmParams = array();

        /* add parameters in Array */
        $paytmParams["requestType"] = "Payment";
        $paytmParams["MID"] = $M_id;
        $paytmParams["ORDERID"] = $data['order_id'];
        $paytmParams["callbackUrl"] = $data['CALLBACK_URL'];
        $paytmParams["custId"] = $data['CUST_ID'];
        $paytmParams["txnAmount"] = $data['TXN_AMOUNT'];
        $paytmParams["websiteName"] = $data['WEBSITE'];

        /**
         * Generate checksum by parameters we have
         * Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys
         */
        $paytmChecksum = PaytmChecksum::generateSignature($paytmParams, $M_Key);
        return $paytmChecksum;
    } else {
        return "";
    }
}
function sendNotification($array)
{
    $noty = General_Setting::where('key', 'onesignal_apid')->orWhere('key', 'onesignal_rest_key')->get();
    $notification = [];
    foreach ($noty as $row) {
        $notification[$row->key] = $row->value;
    }

    $ONESIGNAL_APP_ID = $notification['onesignal_apid'];
    $ONESIGNAL_REST_KEY = $notification['onesignal_rest_key'];

    $content = array(
        "en" => $array['description'],
    );

    $fields = array(
        'app_id' => $ONESIGNAL_APP_ID,
        'included_segments' => array('All'),
        'data' => $array,
        'headings' => array("en" => $array['name']),
        'contents' => $content,
        'big_picture' => $array['image'],
    );

    $fields = json_encode($fields);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json; charset=utf-8',
        'Authorization: Basic ' . $ONESIGNAL_REST_KEY,
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    // dd($response);
    curl_close($ch);
}
function tab_icon()
{
    $name = setting_app_logo();
    $folder = "app";

    if ($name != "" && $folder != "") {

        $appName = Config::get('app.image_url');

        if (file_exists(public_path('images/' . $folder . '/' . $name))) {
            $data = $appName . $folder . '/' . $name;
        } else {
            $data = asset('assets/imgs/no_img.png');
        }
    } else {
        $data = asset('/assets/imgs/no_img.png');
    }
    return ($data);
}
function imageNameToUrl($array, $column, $folder)
{
    try {
        foreach ($array as $key => $value) {

            $appName = Config::get('app.image_url');

            if (isset($value[$column]) && $value[$column] != "") {

                if ($folder == "user" || $folder == "cast" || $folder == "avatar") {

                    if (file_exists(public_path('images/' . $folder . '/' . $value[$column]))) {
                        $value[$column] = $appName . $folder . '/' . $value[$column];
                    } else {
                        $value[$column] = asset('assets/imgs/no_user.png');
                    }
                } else {

                    if (file_exists(public_path('images/' . $folder . '/' . $value[$column]))) {
                        $value[$column] = $appName . $folder . '/' . $value[$column];
                    } else {
                        $value[$column] = asset('assets/imgs/no_img.png');
                    }
                }
            } else {

                if ($folder == "user" || $folder == "cast" || $folder == "avatar") {
                    $value[$column] = asset('assets/imgs/no_user.png');
                } else {
                    $value[$column] = asset('assets/imgs/no_img.png');
                }
            }
        }
        return $array;
    } catch (Exception $e) {
        return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
    }
}
function videoNameToUrl($array, $column, $folder)
{
    try {
        foreach ($array as $key => $value) {

            $appName = Config::get('app.image_url');

            if (isset($value[$column]) && $value[$column] != "") {

                if ($folder == "user" || $folder == "cast" || $folder == "avatar") {

                    if (file_exists(public_path('images/' . $folder . '/' . $value[$column]))) {
                        $value[$column] = $appName . $folder . '/' . $value[$column];
                    } else {
                        $value[$column] = "";
                    }
                } else {

                    if (file_exists(public_path('images/' . $folder . '/' . $value[$column]))) {
                        $value[$column] = $appName . $folder . '/' . $value[$column];
                    } else {
                        $value[$column] = "";
                    }
                }
            } else {
                $value[$column] = "";
            }
        }
        return $array;
    } catch (Exception $e) {
        return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
    }
}
function Get_Image($folder = "", $name = "")
{
    $appName = Config::get('app.image_url');
    if ($name != "" && $folder != "") {

        if ($folder == "user" || $folder == "cast" || $folder == "avatar") {
            if (file_exists(public_path('images/' . $folder . '/' . $name))) {
                $name = $appName . $folder . '/' . $name;
            } else {
                $name = asset('assets/imgs/no_user.png');
            }
        } else {
            if (file_exists(public_path('images/' . $folder . '/' . $name))) {
                $name = $appName . $folder . '/' . $name;
            } else {
                $name = asset('assets/imgs/no_img.png');
            }
        }
    } else {
        if ($folder == "user" || $folder == "cast" || $folder == "avatar") {
            $name = asset('assets/imgs/no_user.png');
        } else {
            $name = asset('assets/imgs/no_img.png');
        }
    }
    return ($name);
}
function Get_Video($folder = "", $name = "")
{
    $appName = Config::get('app.image_url');
    if ($name != "" && $folder != "") {

        if ($folder == "user" || $folder == "cast" || $folder == "avatar") {
            if (file_exists(public_path('images/' . $folder . '/' . $name))) {
                $name = $appName . $folder . '/' . $name;
            } else {
                $name = "";
            }
        } else {
            if (file_exists(public_path('images/' . $folder . '/' . $name))) {
                $name = $appName . $folder . '/' . $name;
            } else {
                $name = "";
            }
        }
    } else {
        if ($folder == "user" || $folder == "cast" || $folder == "avatar") {
            $name = "";
        } else {
            $name = "";
        }
    }
    return ($name);
}
function deleteImageToFolder($folder, $name)
{
    try {
        @unlink(public_path('images/') . $folder . "/" . $name);
    } catch (Exception $e) {
        return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
    }
}
function user_name($string)
{
    $rand_number = rand(0, 1000);
    $user_name = '@' . $string . $rand_number;

    $check = Users::where('user_name', $user_name)->first();
    if (isset($check) && $check != null) {
        user_name($string);
    }
    return $user_name;
}
function TV_Login_Code()
{
    $code = rand(1000, 9999);
    $check = TV_Login::where('unique_code', $code)->where('status', 1)->where('user_id', '!=', 0)->first();
    if (isset($check)) {

        TV_Login_Code();
    } else {
        return  (string) $code;
    }
}
function Send_Mail($type, $email) // Type = 1- Register Mail, 2 Transaction Mail
{
    try {

        $smtp = smtpData();
        if (isset($smtp) && $smtp != false && $smtp['status'] == 1) {

            if ($type == 1) {
                $title = App_Name() . " - Register";
                $body = "Welcome to " . App_Name() . " App & Enjoy this app.";
            } else if ($type == 2) {
                $title = App_Name() . " - Transaction";
                $body = "Welcome to " . App_Name() . " App & Enjoy this app. You have Successfully Transaction.";
            } else {
                return true;
            }
            $details = [
                'title' => $title,
                'body' => $body
            ];

            // Send Mail
            try {
                Mail::to($email)->send(new \App\Mail\mail($details));
                return true;
            } catch (\Swift_TransportException $e) {
                return true;
            }
        } else {
            return true;
        }
    } catch (Exception $e) {
        return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
    }
}
function App_Name()
{
    $setting = General_Setting::get();
    $data = [];
    foreach ($setting as $value) {
        $data[$value->key] = $value->value;
    }
    $app_name = $data['app_name'];

    if (isset($app_name) && $app_name != "") {
        return $app_name;
    } else {
        return env('APP_NAME');
    }
}
function Check_Admin_Access()
{
    if (Auth::guard('admin')->user()->type != 1) {
        return 0;
    } else {
        return 1;
    }
}
