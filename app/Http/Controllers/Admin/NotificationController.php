<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\General_Setting;
use App\Models\Notification;
use Illuminate\Http\Request;
use Validator;
use Exception;

class NotificationController extends Controller
{
    private $folder = "notification";

    public function index()
    {
        try {
            return view('admin.notification.index');
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function add()
    {
        try {
            return view('admin.notification.add');
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function save(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'message' => 'required',
                'image' => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($validator->fails()) {

                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $notification = new Notification();
            $notification->title = $request->title;
            $notification->message = $request->message;

            $org_name = $request->file('image');
            $notificationImageURL = '';
            if ($org_name !== null) {
                $notification->image = saveImage($org_name, $this->folder);
                $notificationImageURL = Get_Image('notification', $notification->image);
            } else {
                $notification->image = "";
            }

            if ($notification->save()) {

                $noty = General_Setting::where('key', 'onesignal_apid')->orWhere('key', 'onesignal_rest_key')->get();
                $notification = [];
                foreach ($noty as $row) {
                    $notification[$row->key] = $row->value;
                }

                $ONESIGNAL_APP_ID = $notification['onesignal_apid'];
                $ONESIGNAL_REST_KEY = $notification['onesignal_rest_key'];

                $content = array(
                    "en" => $request->message,
                );

                $fields = array(
                    'app_id' => $ONESIGNAL_APP_ID,
                    'included_segments' => array('All'),
                    'data' => array("foo" => "bar"),
                    'headings' => array("en" => $request->title),
                    'contents' => $content,
                    'big_picture' => $notificationImageURL,
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
                // pre($response);
                curl_close($ch);

                return response()->json(array('status' => 200, 'success' => __('Label.Data Add Successfully')));
            } else {
                return response()->json(array('status' => 400, 'errors' => __('Label.Data Not Add')));
            }
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
                    $data = Notification::where('title', 'LIKE', "%{$input_search}%")->latest()->get();
                } else {
                    $data = Notification::latest()->get();
                }

                imageNameToUrl($data, 'image', $this->folder);

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $btn = '<a href="' . route("deleteNotification", $row->id) . '" onclick="return confirm(\'Are you sure !!! You want to Delete this Notification ?\')" title="Delete"><i class="fa-solid fa-trash-can"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            } else {
                return view('admin.notification.index');
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function setting()
    {
        try {
            $setting = General_Setting::select('*')->get();

            foreach ($setting as $row) {
                $data[$row->key] = $row->value;
            }
            if ($data) {
                return view('admin.notification.setting', ['result' => $data]);
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function settingsave(Request $request)
    {
        try {
            $data = $request->all();
            $data["onesignal_apid"] = isset($data['onesignal_apid']) ? $data['onesignal_apid'] : '';
            $data["onesignal_rest_key"] = isset($data['onesignal_rest_key']) ? $data['onesignal_rest_key'] : '';

            foreach ($data as $key => $value) {
                $setting = General_Setting::where('key', $key)->first();
                if (isset($setting->id)) {
                    $setting->value = $value;
                    $setting->save();
                }
            }
            return response()->json(array('status' => 200, 'success' => __('Label.save_setting')));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function delete($id)
    {
        try {
            $notification = Notification::where('id', $id)->first();
            if ($notification->delete()) {

                deleteImageToFolder($this->folder, $notification->image);
                return redirect()->route('notification')->with('success', __('Label.Data Delete Successfully'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
