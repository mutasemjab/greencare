<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    use Responses;


    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $perPage = $request->get('per_page', 15);

            // Get notifications where user_id is null (for all) OR user_id matches current user
            $notifications = Notification::where(function($query) use ($user) {
                $query->whereNull('user_id')
                      ->orWhere('user_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

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

}