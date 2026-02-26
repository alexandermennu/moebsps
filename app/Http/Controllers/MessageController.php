<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Inbox - received messages
     */
    public function index(Request $request)
    {
        $folder = $request->get('folder', 'inbox');
        $userId = Auth::id();

        if ($folder === 'sent') {
            $messages = Message::sent($userId)
                ->with('receiver')
                ->latest()
                ->paginate(20);
        } else {
            $messages = Message::inbox($userId)
                ->with('sender')
                ->latest()
                ->paginate(20);
        }

        $unreadCount = Message::where('receiver_id', $userId)
            ->where('is_read', false)
            ->where('receiver_deleted', false)
            ->whereNull('parent_id')
            ->count();

        return view('messages.index', compact('messages', 'folder', 'unreadCount'));
    }

    /**
     * Compose new message
     */
    public function create(Request $request)
    {
        $users = User::where('id', '!=', Auth::id())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $replyTo = null;
        if ($request->has('reply_to')) {
            $replyTo = Message::findOrFail($request->reply_to);
        }

        return view('messages.create', compact('users', 'replyTo'));
    }

    /**
     * Send message
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'parent_id' => 'nullable|exists:messages,id',
        ]);

        $validated['sender_id'] = Auth::id();

        $message = Message::create($validated);

        return redirect()->route('messages.index')
            ->with('success', 'Message sent successfully.');
    }

    /**
     * View message / conversation thread
     */
    public function show(Message $message)
    {
        $userId = Auth::id();

        // Ensure user is sender or receiver
        if ($message->sender_id !== $userId && $message->receiver_id !== $userId) {
            abort(403);
        }

        // Mark as read if user is the receiver
        if ($message->receiver_id === $userId) {
            $message->markAsRead();
        }

        // Mark replies as read too
        $message->replies()
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        $message->load(['sender', 'receiver', 'replies.sender', 'replies.receiver']);

        $users = User::where('id', '!=', Auth::id())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('messages.show', compact('message', 'users'));
    }

    /**
     * Reply to a message thread
     */
    public function reply(Request $request, Message $message)
    {
        $userId = Auth::id();

        if ($message->sender_id !== $userId && $message->receiver_id !== $userId) {
            abort(403);
        }

        $validated = $request->validate([
            'body' => 'required|string',
        ]);

        // Determine receiver (the other person in the thread)
        $receiverId = $message->sender_id === $userId
            ? $message->receiver_id
            : $message->sender_id;

        $reply = Message::create([
            'sender_id' => $userId,
            'receiver_id' => $receiverId,
            'subject' => 'Re: ' . $message->subject,
            'body' => $validated['body'],
            'parent_id' => $message->id,
        ]);

        return redirect()->route('messages.show', $message)
            ->with('success', 'Reply sent.');
    }

    /**
     * Delete (soft) a message
     */
    public function destroy(Message $message)
    {
        $userId = Auth::id();

        if ($message->sender_id === $userId) {
            $message->update(['sender_deleted' => true]);
        }

        if ($message->receiver_id === $userId) {
            $message->update(['receiver_deleted' => true]);
        }

        // If both deleted, permanently remove
        if ($message->fresh()->sender_deleted && $message->fresh()->receiver_deleted) {
            $message->replies()->delete();
            $message->delete();
        }

        return redirect()->route('messages.index')
            ->with('success', 'Message deleted.');
    }

    /**
     * Get unread count (for AJAX if needed)
     */
    public function unreadCount()
    {
        $count = Message::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->where('receiver_deleted', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}
