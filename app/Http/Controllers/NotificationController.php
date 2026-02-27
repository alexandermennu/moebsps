<?php

namespace App\Http\Controllers;

use App\Models\BureauNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->bureauNotifications()
            ->where('type', '!=', 'message')
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(BureauNotification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->markAsRead();

        if ($notification->link) {
            return redirect($notification->link);
        }

        return redirect()->route('notifications.index');
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()
            ->bureauNotifications()
            ->where('type', '!=', 'message')
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return redirect()->route('notifications.index')
            ->with('success', 'All notifications marked as read.');
    }
}
