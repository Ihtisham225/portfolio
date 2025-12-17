<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $query = Message::query();
        
        // Search
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('subject', 'like', '%' . $request->search . '%')
                  ->orWhere('message', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $messages = $query->latest()->paginate(20);

        return view('admin.messages.index', compact('messages'));
    }

    public function show(Message $message)
    {
        // Mark as read when viewing
        if ($message->is_unread) {
            $message->markAsRead();
        }

        return view('admin.messages.show', compact('message'));
    }

    public function reply(Request $request, Message $message)
    {
        $request->validate([
            'response' => 'required|string',
        ]);

        $message->markAsReplied($request->response);

        // Send email reply (implement email logic here)
        // Mail::to($message->email)->send(new MessageReply($message, $request->response));

        return redirect()->route('admin.messages.show', $message)
            ->with('success', 'Reply sent successfully.');
    }

    public function markAsRead(Message $message)
    {
        $message->markAsRead();

        return redirect()->back()->with('success', 'Message marked as read.');
    }

    public function markAsArchived(Message $message)
    {
        $message->markAsArchived();

        return redirect()->back()->with('success', 'Message archived.');
    }

    public function destroy(Message $message)
    {
        $message->delete();

        return redirect()->route('admin.messages.index')
            ->with('success', 'Message deleted successfully.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,read,archive',
            'ids' => 'required|array',
            'ids.*' => 'exists:messages,id',
        ]);

        $action = $request->action;
        $ids = $request->ids;

        switch ($action) {
            case 'delete':
                Message::whereIn('id', $ids)->delete();
                $message = 'Messages deleted successfully.';
                break;
            case 'read':
                Message::whereIn('id', $ids)->update(['status' => 'read']);
                $message = 'Messages marked as read.';
                break;
            case 'archive':
                Message::whereIn('id', $ids)->update(['status' => 'archived']);
                $message = 'Messages archived successfully.';
                break;
        }

        return redirect()->back()->with('success', $message);
    }
}