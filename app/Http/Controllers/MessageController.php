<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);

        $messages = Message::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->with(['sender', 'receiver'])
            ->latest()
            ->get();

        return response()->json($messages);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);

        $validatedData = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content'     => 'required|string',
        ]);

        $message = Message::create([
            'sender_id'   => $user->id,
            'receiver_id' => $validatedData['receiver_id'],
            'content'     => $validatedData['content'],
        ]);

        return response()->json([
            'message' => 'Message sent successfully',
            'data'    => $message->load(['sender', 'receiver']),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Message $message)
    {
        $this->authorize('view', $message);
        return response()->json($message->load(['sender', 'receiver']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Message $message)
    {
        $this->authorize('delete', $message);
        $message->delete();
        return response()->json(['message' => 'Message deleted successfully'], 204);
    }
}
