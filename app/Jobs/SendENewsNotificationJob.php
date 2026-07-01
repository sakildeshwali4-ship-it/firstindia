<?php

namespace App\Jobs;

use App\Models\Users;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendENewsNotificationJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public $title;
    public $body;
    public $data;

    public function __construct($title, $body, $data = [])
    {
        $this->title = $title;
        $this->body  = $body;
        $this->data  = $data;
    }

    public function handle()
    { 
        \Log::info("ENews Job Started");

        $androidTokens = Users::where('device_type', 'Android')
            ->whereNotNull('device_token')
            ->pluck('device_token')
            ->toArray();
 
        $iosTokens = Users::where('device_type', 'ios')
            ->whereNotNull('device_token')
            ->pluck('device_token')
            ->toArray();

        \Log::info("Android Tokens Found: " . count($androidTokens));
        \Log::info("iOS Tokens Found: " . count($iosTokens));

        foreach ($androidTokens as $token) {
            \Log::info("Sending Android Notification to: " . $token);
            $this->sendAndroidFCM($token);
        }

        foreach ($iosTokens as $token) {
             \Log::info("Sending iOS Notification to: " . $token);
            $this->sendIOSAPNs($token);
        }
        \Log::info("ENews Job Finished");
    }


    private function sendAndroidFCM($fcmToken)
    {
        $serviceAccount = json_decode(
            file_get_contents(storage_path('app/firebase.json')), true
        );

        // Firebase config
        $projectId    = $serviceAccount['project_id'];
        $clientEmail  = $serviceAccount['client_email'];
        $privateKey   = $serviceAccount['private_key'];

        // Create JWT for OAuth
        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT'
        ];

        $now = time();

        $claims = [
            "iss" => $clientEmail,
            "scope" => "https://www.googleapis.com/auth/firebase.messaging",
            "aud" => "https://oauth2.googleapis.com/token",
            "iat" => $now,
            "exp" => $now + 3600
        ];

        $jwtHeader = rtrim(strtr(base64_encode(json_encode($header)), '+/', '-_'), '=');
        $jwtClaims = rtrim(strtr(base64_encode(json_encode($claims)), '+/', '-_'), '=');

        openssl_sign($jwtHeader . "." . $jwtClaims, $signature, $privateKey, "sha256");
        $jwtSignature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

        $jwt = $jwtHeader . "." . $jwtClaims . "." . $jwtSignature;

        // Exchange JWT → Access Token
        $tokenResponse = json_decode(file_get_contents("https://oauth2.googleapis.com/token", false, stream_context_create([
            "http" => [
                "method"  => "POST",
                "header"  => "Content-type: application/x-www-form-urlencoded\r\n",
                "content" => http_build_query([
                    "grant_type" => "urn:ietf:params:oauth:grant-type:jwt-bearer",
                    "assertion"  => $jwt
                ])
            ]
        ])), true);

        $accessToken = $tokenResponse["access_token"];

        // FCM URL
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        // Payload (same as before)
        $payload = [
            "message" => [
                "token" => $fcmToken,

                "notification" => [
                    "title" => $this->title,
                    "body"  => $this->body
                ],

                "data" => array_merge($this->data, [
                    "title" => $this->title,
                    "body"  => $this->body,
                    "click_action" => "FLUTTER_NOTIFICATION_CLICK"
                ]),

                "android" => [
                    "priority" => "high",
                    "notification" => [
                        "sound" => "default",
                        "channel_id" => "high_importance_channel"
                    ]
                ]
            ]
        ];

        $headers = [
            "Authorization: Bearer {$accessToken}",
            "Content-Type: application/json"
        ];

        // CURL CALL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        \Log::info("Android FCM v1 Response: " . $response);
    }


    private function sendIOSAPNs($apnsToken)
    {
        $bundleId     = "com.firstindia.ott";
        $authKeyPath  = storage_path('app/AuthKey_83XH27ZCNL.p8');
        $authKeyId    = "83XH27ZCNL";
        $teamId       = "5YAJXYB4Z8";

        $privateKey = file_get_contents($authKeyPath);

        // Header + Claims
        $header = ['alg' => 'ES256', 'kid' => $authKeyId];
        $claims = ['iss' => $teamId, 'iat' => time()];

        $jwtHeader  = $this->base64url(json_encode($header));
        $jwtClaims  = $this->base64url(json_encode($claims));
        $signInput  = $jwtHeader . "." . $jwtClaims;

        openssl_sign($signInput, $signature, $privateKey, 'sha256');
        $jwt = $signInput . "." . $this->base64url($signature);

        // APNs Payload
        $payload = [
            "aps" => [
                "alert" => [
                    "title" => $this->title,
                    "body"  => $this->body,
                ],
                "sound" => "default",
                "badge" => 1,
                "mutable-content" => 1
            ],
            "data" => $this->data
        ];

        $ch = curl_init("https://api.push.apple.com/3/device/{$apnsToken}");
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "authorization: bearer {$jwt}",
            "apns-topic: {$bundleId}"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_exec($ch);
        curl_close($ch);
    }

    private function base64url($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
