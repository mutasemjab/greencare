<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Admin\FCMController;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    use Responses;


    public function index(Request $request)
    {
        try {
            $user = Auth::user();

            // Get notifications where user_id is null (for all) OR user_id matches current user
            $notifications = Notification::where(function($query) use ($user) {
                $query->whereNull('user_id')
                    ->orWhere('user_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->get(); // Removed pagination

            return $this->success_response(
                'Notifications retrieved successfully',
                $notifications
            );
        } catch (\Exception $e) {
            return $this->error_response(
                'Failed to retrieve notifications',
                ['error' => $e->getMessage()]
            );
        }
    }

    public function sendNotification(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'title' => 'required|string|max:255',
                'body' => 'required|string|max:1000',
                'screen' => 'nullable|string|max:100',
            ]);

            if ($validator->fails()) {
                return $this->error_response(
                    __('messages.validation_error'),
                    ['errors' => $validator->errors()]
                );
            }

            // Get user
            $user = User::find($request->user_id);

            if (!$user) {
                return $this->error_response(
                    __('messages.user_not_found'),
                    []
                );
            }

            if (!$user->fcm_token) {
                return $this->error_response(
                    __('messages.user_has_no_fcm_token'),
                    []
                );
            }

            // Send notification
            $screen = $request->screen ?? 'notification';
            $result = FCMController::sendToUser(
                $request->user_id,
                $request->title,
                $request->body,
                $screen
            );

            if ($result) {
                return $this->success_response(
                    __('messages.notification_sent_successfully'),
                    [
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'title' => $request->title,
                        'body' => $request->body,
                        'screen' => $screen,
                    ]
                );
            } else {
                return $this->error_response(
                    __('messages.notification_send_failed'),
                    []
                );
            }

        } catch (\Exception $e) {
            return $this->error_response(
                __('messages.error_occurred'),
                ['error' => $e->getMessage()]
            );
        }
    }

    /**
     * Send FCM notification to multiple users
     */
    public function sendBulkNotification(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_ids' => 'required|array|min:1',
                'user_ids.*' => 'required|exists:users,id',
                'title' => 'required|string|max:255',
                'body' => 'required|string|max:1000',
                'screen' => 'nullable|string|max:100',
            ]);

            if ($validator->fails()) {
                return $this->error_response(
                    __('messages.validation_error'),
                    ['errors' => $validator->errors()]
                );
            }

            $screen = $request->screen ?? 'notification';
            $successCount = 0;
            $failCount = 0;
            $results = [];

            foreach ($request->user_ids as $userId) {
                $result = FCMController::sendToUser(
                    $userId,
                    $request->title,
                    $request->body,
                    $screen
                );

                $user = User::find($userId);
                $results[] = [
                    'user_id' => $userId,
                    'user_name' => $user ? $user->name : 'Unknown',
                    'success' => $result,
                ];

                if ($result) {
                    $successCount++;
                } else {
                    $failCount++;
                }
            }

            return $this->success_response(
                __('messages.bulk_notification_sent'),
                [
                    'total' => count($request->user_ids),
                    'success_count' => $successCount,
                    'fail_count' => $failCount,
                    'results' => $results,
                ]
            );

        } catch (\Exception $e) {
            return $this->error_response(
                __('messages.error_occurred'),
                ['error' => $e->getMessage()]
            );
        }
    }


}