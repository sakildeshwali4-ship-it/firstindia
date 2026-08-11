<?php

namespace App\Http\Controllers\Admin;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;
use App\Http\Controllers\Controller;
use App\Models\General_Setting;
use App\Models\Notification;
use App\Models\Users;
use Illuminate\Http\Request;
use Validator;
use Exception;
use DB;

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

                
                $this->sendFcmNotifyTopic($request->title, $request->message, $notificationImageURL);
				// $this->_sendFcmNotify($request->title, $request->message, $notificationImageURL);
                return response()->json(array('status' => 200, 'success' => __('Label.Data Add Successfully')));
            } else {
                return response()->json(array('status' => 400, 'errors' => __('Label.Data Not Add')));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
	
    function sendFcmNotifyTopic($title = '', $message = '', $image_url = '')
    { 
        $credentials = array();

        $tokenData = $credentials->fetchAuthToken(HttpHandlerFactory::build());
        if (empty($tokenData['access_token'])) {
            return false;
        }

        $accessToken = $tokenData['access_token'];

        $url = 'https://fcm.googleapis.com/v1/projects/firstindia-b67a8/messages:send';

        // 🔥 TOPIC PAYLOAD (1M USERS)
        $payload = [
            'message' => [
                'topic' => 'firstindia_all_users',
                'notification' => [
                    'title' => $title ?: 'Notification',
                    'body'  => $message,
                ],
                'android' => [
                    'priority' => 'HIGH',
                ],
                'apns' => [
                    'headers' => [
                        'apns-priority'  => '10',
                        'apns-push-type' => 'alert',
                        'apns-topic'     => 'com.firstindia.ott'
                    ],
                    'payload' => [
                        'aps' => [
                            'alert' => [
                                'title' => $title ?: 'Notification',
                                'body'  => $message,
                            ],
                            'sound' => 'default',
                            'badge' => 1
                        ]


                    ]
                ]
            ]
        ];

        // Optional image (Android + iOS text safe)
        if (!empty($image_url)) {
            $payload['message']['notification']['image'] = $image_url;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json'
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
        ]);

        $response = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        \Log::info('FCM Topic Response', ['code'=>$code,'resp'=>$response]);
        curl_close($ch);

        return $response;
    }


	function _sendFcmNotify($title = '', $message = '', $image_url = '')
    {
		$credentials = array();
		$token = $credentials->fetchAuthToken(HttpHandlerFactory::build());
		if(!empty($token['access_token'])) {
			$accessToken = $token['access_token'];
		} else {
			return "";
		}
		$result = '';
        $url = 'https://fcm.googleapis.com/v1/projects/firstindia-b67a8/messages:send';                 
        $fields = [
			'message' => [
			"token" => '',
			"notification"=> [
					"title"=>($title != null)?$title:"FirstIndia Notification!", 
					"image"=>($image_url != '')?$image_url:"",
					"body" =>$message
				],
				//"topic" => "ldlffjl"
			],
        ];
        //echo json_encode($fields);exit;
        $headers = [
			'Content-Type:application/json,',
			'Authorization:Bearer '.$accessToken
		];
		Users::select('device_token')->whereNotNull('device_token')->where('device_token', '!=', '')->chunk(100, function ($users) use ($headers, $url, $fields) {
			//foreach($users as $k => $r) {
				$users = $users->toArray();
				$tokenArr = array_column($users, 'device_token');
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				if(!empty($tokenArr)) {
					foreach($tokenArr as $token) {
						$fields['message']['token'] = $token;
						curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
						$result = curl_exec($ch);
					}
				}
				curl_close($ch);
			//}
		});
        return $result; 
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
