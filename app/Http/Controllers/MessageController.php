<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * List all conversations for the authenticated user.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);

        $conversations = Conversation::where('user_one_id', $user->id)
            ->orWhere('user_two_id', $user->id)
            ->with(['userOne', 'userTwo', 'messages' => function($q) {
                $q->latest()->limit(1);
            }])
            ->orderByDesc('last_message_at')
            ->get();

        return response()->json($conversations);
    }

    /**
     * Start a new conversation or get an existing one.
     */
    public function start(Request $request)
    {
        $user = Auth::user();
        $otherUserId = $request->input('user_id');
        
        if (!$otherUserId) return response()->json(['message' => 'User ID is required'], 400);

        // Order the IDs to guarantee uniqueness in the pair
        $ids = [$user->id, (int)$otherUserId];
        sort($ids);

        $conversation = Conversation::firstOrCreate([
            'user_one_id' => $ids[0],
            'user_two_id' => $ids[1],
        ]);

        return response()->json($conversation->load(['userOne', 'userTwo']));
    }

    /**
     * Get message history for a conversation.
     */
    public function show(Conversation $conversation)
    {
        $this->authorizeConversation($conversation);
        
        $messages = $conversation->messages()
            ->with('sender')
            ->oldest()
            ->get();

        return response()->json($messages);
    }

    /**
     * Send a new message.
     */
    public function store(Request $request, Conversation $conversation)
    {
        $this->authorizeConversation($conversation);

        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $user = Auth::user();
        $receiver_id = $conversation->user_one_id === $user->id 
            ? $conversation->user_two_id 
            : $conversation->user_one_id;

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $user->id,
            'receiver_id'     => $receiver_id,
            'content'         => $validated['content'],
        ]);

        $conversation->update(['last_message_at' => now()]);

        // Broadcast to others in the conversation
        broadcast(new MessageSent($message->load('sender')))->toOthers();

        return response()->json($message);
    }

    /**
     * Ensure the user belongs to the conversation.
     */
    protected function authorizeConversation(Conversation $conversation)
    {
        $user = Auth::user();
        if ($conversation->user_one_id !== $user->id && $conversation->user_two_id !== $user->id) {
            abort(403, 'Unauthorized access to conversation');
        }
    }
}
