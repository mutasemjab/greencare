<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use App\Models\LabNotification;
use App\Services\LabNotificationService;
use Illuminate\Http\Request;

class LabNotificationController extends Controller
{
    protected $service;

    public function __construct(LabNotificationService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $labId = auth('lab')->id();

        $query = LabNotification::forLab($labId)->orderBy('created_at', 'desc');

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->unread();
            } elseif ($request->status === 'read') {
                $query->read();
            }
        }

        $notifications = $query->paginate(20);
        $unreadCount   = $this->service->getUnreadCount($labId);

        return view('lab.notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markAsRead($id)
    {
        $notification = LabNotification::where('lab_id', auth('lab')->id())->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        $this->service->markAllAsRead(auth('lab')->id());

        return response()->json(['success' => true]);
    }

    public function getUnreadCount()
    {
        $count = $this->service->getUnreadCount(auth('lab')->id());

        return response()->json(['count' => $count]);
    }

    public function getLatest()
    {
        $notifications = LabNotification::forLab(auth('lab')->id())
            ->unread()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(fn($n) => [
                'id'               => $n->id,
                'type'             => $n->type,
                'title'            => $n->title,
                'message'          => $n->message,
                'url'              => $n->url,
                'icon'             => $n->icon,
                'badge_color'      => $n->badge_color,
                'created_at_human' => $n->created_at_human,
            ]);

        return response()->json(['notifications' => $notifications]);
    }

    public function destroy($id)
    {
        LabNotification::where('lab_id', auth('lab')->id())->findOrFail($id)->delete();

        return back()->with('success', 'تم حذف الإشعار');
    }

    public function deleteAllRead()
    {
        LabNotification::forLab(auth('lab')->id())->read()->delete();

        return back()->with('success', 'تم حذف جميع الإشعارات المقروءة');
    }
}
