<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Services\AdminNotificationService;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    protected $notificationService;

    public function __construct(AdminNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of notifications
     */
    public function index(Request $request)
    {
        $query = AdminNotification::orderBy('created_at', 'desc');

        // Filter by type
        if ($request->filled('type') && $request->type !== 'all') {
            $query->ofType($request->type);
        }

        // Filter by read status
        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->unread();
            } elseif ($request->status === 'read') {
                $query->read();
            }
        }

        $notifications = $query->paginate(20);
        $unreadCount = AdminNotification::unread()->count();
        $types = ['all', 'order', 'appointment', 'appointment_provider'];

        return view('admin.notifications.index', compact('notifications', 'unreadCount', 'types'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        try {
            $notification = AdminNotification::findOrFail($id);
            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => __('messages.notification_marked_as_read')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_occurred')
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        try {
            $this->notificationService->markAllAsRead();

            return response()->json([
                'success' => true,
                'message' => __('messages.all_notifications_marked_as_read')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_occurred')
            ], 500);
        }
    }

    /**
     * Get unread count
     */
    public function getUnreadCount()
    {
        $count = $this->notificationService->getUnreadCount();
        return response()->json(['count' => $count]);
    }

    /**
     * Get latest notifications
     */
    public function getLatest()
    {
        $notifications = AdminNotification::unread()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'url' => $notification->url,
                    'icon' => $notification->icon,
                    'badge_color' => $notification->badge_color,
                    'created_at_human' => $notification->created_at_human
                ];
            });

        return response()->json(['notifications' => $notifications]);
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        try {
            $notification = AdminNotification::findOrFail($id);
            $notification->delete();

            return back()->with('success', __('messages.notification_deleted_successfully'));
        } catch (\Exception $e) {
            return back()->with('error', __('messages.error_occurred'));
        }
    }

    /**
     * Delete all read notifications
     */
    public function deleteAllRead()
    {
        try {
            AdminNotification::read()->delete();

            return back()->with('success', __('messages.all_read_notifications_deleted'));
        } catch (\Exception $e) {
            return back()->with('error', __('messages.error_occurred'));
        }
    }
}