<?php

namespace App\Http\Controllers;

use App\Models\BureauNotification;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LivePollController extends Controller
{
    public function poll(Request $request)
    {
        $userId = Auth::id();
        $since = $request->get('since');

        // Unread counts (notifications = system only, exclude message type)
        $unreadNotifications = BureauNotification::where('user_id', $userId)
            ->where('is_read', false)
            ->where('type', '!=', 'message')
            ->count();

        $unreadMessages = Message::where('receiver_id', $userId)
            ->where('is_read', false)
            ->where('receiver_deleted', false)
            ->count();

        // New items since last poll
        $newNotifications = [];
        $newMessages = [];

        if ($since) {
            $newNotifications = BureauNotification::where('user_id', $userId)
                ->where('created_at', '>', $since)
                ->where('type', '!=', 'message')
                ->latest()
                ->limit(5)
                ->get()
                ->map(fn ($n) => [
                    'id' => $n->id,
                    'type' => $n->type,
                    'title' => $n->title,
                    'message' => $n->message,
                    'link' => $n->link,
                    'time' => $n->created_at->diffForHumans(),
                ]);

            $newMessages = Message::where('receiver_id', $userId)
                ->where('created_at', '>', $since)
                ->where('receiver_deleted', false)
                ->with('sender:id,name')
                ->latest()
                ->limit(5)
                ->get()
                ->map(fn ($m) => [
                    'id' => $m->id,
                    'sender' => $m->sender->name,
                    'subject' => $m->subject,
                    'preview' => \Illuminate\Support\Str::limit($m->body, 60),
                    'link' => route('messages.show', $m->parent_id ?? $m->id),
                    'time' => $m->created_at->diffForHumans(),
                ]);
        }

        return response()->json([
            'unread_notifications' => $unreadNotifications,
            'unread_messages' => $unreadMessages,
            'new_notifications' => $newNotifications,
            'new_messages' => $newMessages,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
