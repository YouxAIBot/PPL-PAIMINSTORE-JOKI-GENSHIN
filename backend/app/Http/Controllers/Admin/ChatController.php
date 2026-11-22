<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        $conversations = Conversation::with(['user', 'messages' => fn ($q) => $q->latest()->limit(1)])
            ->orderByRaw('last_message_at IS NULL, last_message_at DESC')
            ->paginate(20);

        return view('admin.chats.index', compact('conversations'));
    }

    public function show(Conversation $conversation)
    {
        $conversation->load(['user', 'messages.sender']);
        $conversation->update(['admin_unread' => 0]);

        return view('admin.chats.show', compact('conversation'));
    }

    public function reply(Request $request, Conversation $conversation)
    {
        $data = $request->validate(['body' => ['required', 'string', 'max:2000']]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->id(),
            'body' => $data['body'],
            'created_at' => now(),
        ]);

        $conversation->update([
            'last_message_at' => now(),
            'user_unread' => $conversation->user_unread + 1,
        ]);

        return back()->with('success', 'Balasan terkirim.');
    }
}
