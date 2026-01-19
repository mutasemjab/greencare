<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:notification-table', ['only' => ['index', 'show']]);
        $this->middleware('permission:notification-add', ['only' => ['create', 'store']]);
        $this->middleware('permission:notification-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:notification-delete', ['only' => ['destroy']]);
    }

    public function create()
    {

        return view('admin.notifications.create');

    }

    public function send(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'body' => 'required',
            'user_id' => 'nullable|exists:users,id', // Optional: for specific user
            'send_to' => 'required|in:all,specific' // all or specific
        ]);

        $title = $request->title;
        $body = $request->body;
        $response = false;

        // Send to all users or specific user
        if ($request->send_to === 'all') {
            $response = FCMController::sendMessageToAll($title, $body);
        } elseif ($request->send_to === 'specific' && $request->user_id) {
            $response = FCMController::sendToUser($request->user_id, $title, $body);
        }

        // Save the notification
        if ($response) {
            $noti = new Notification([
                'title' => $title,
                'body' => $body,
                'user_id' => $request->send_to === 'specific' ? $request->user_id : null,
            ]);
            $noti->save();

            return redirect()->back()->with('message', 'Notification sent successfully');
        } else {
            return redirect()->back()->with('error', 'Notification was not sent');
        }
    }
}
