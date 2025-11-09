<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\AppSetting;
use App\Models\User;
use App\Models\Provider;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Google\Client as GoogleClient;

class FCMController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public static function sendMessage($title, $body, $fcmToken, $userId, $screen = "order")
    {
        if (!$fcmToken) {
            \Log::error("FCM Error: No FCM token provided for user ID $userId");
            return false;
        }

        $credentialsFilePath = base_path('json/green-care-app-a1237-312d2df3ae3e.json');

        try {
            $client = new GoogleClient();
            $client->setAuthConfig($credentialsFilePath);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            $client->useApplicationDefaultCredentials();
            $client->fetchAccessTokenWithAssertion();
            $tokenResponse = $client->getAccessToken();

            $access_token = $tokenResponse['access_token'];
            \Log::info("FCM Access Token for user ID $userId: " . $access_token);

            $headers = [
                "Authorization: Bearer $access_token",
                'Content-Type: application/json'
            ];

            $data = [
                "message" => [
                    "token" => $fcmToken,
                    "notification" => [
                        "title" => $title,
                        "body" => $body
                    ],
                    "data" => [
                        'screen' => $screen,
                        "click_action" => "FLUTTER_NOTIFICATION_CLICK"
                    ],
                    "android" => [
                        "priority" => "high"
                    ]
                ]
            ];

            $payload = json_encode($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/green-care-app-a1237/messages:send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_VERBOSE, true); // Enable verbose output for debugging
            $result = curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);

            if ($result === false || $err) {
                \Log::error("FCM Error for user ID $userId: cURL Error: " . $err);
                return false;
            } else {
                $response = json_decode($result, true);
                \Log::info("FCM Response for user ID $userId: " . json_encode($response));
                if (isset($response['name'])) {
                    return true;
                } else {
                    \Log::error("FCM Error for user ID $userId: " . json_encode($response));
                    if (isset($response['error']['details'][0]['errorCode']) && $response['error']['details'][0]['errorCode'] === 'UNREGISTERED') {
                        \Log::info("FCM token cleanup for user ID $userId");
                        User::where('id', $userId)->update(['fcm_token' => null]);
                    }
                    return false;
                }
            }
        } catch (\Exception $e) {
            \Log::error("FCM Error for user ID $userId: " . $e->getMessage());
            return false;
        }
    }


     public static function sendMessageToAll($title, $body, $screen = "order")
    {
        $users = User::whereNotNull('fcm_token')->get();
        
        if ($users->isEmpty()) {
            \Log::warning("No users with FCM tokens found");
            return false;
        }

        $successCount = 0;
        $failCount = 0;

        foreach ($users as $user) {
            $result = self::sendMessage($title, $body, $user->fcm_token, $user->id, $screen);
            if ($result) {
                $successCount++;
            } else {
                $failCount++;
            }
        }

        \Log::info("FCM Bulk Send - Success: $successCount, Failed: $failCount");
        
        return $successCount > 0; // Return true if at least one notification was sent
    }

    // New method to send to specific user
    public static function sendToUser($userId, $title, $body, $screen = "order")
    {
        $user = User::find($userId);
        
        if (!$user || !$user->fcm_token) {
            \Log::error("User not found or no FCM token for user ID: $userId");
            return false;
        }

        return self::sendMessage($title, $body, $user->fcm_token, $user->id, $screen);
    }


}
