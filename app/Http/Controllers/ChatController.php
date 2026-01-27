<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatMessage;
use App\Services\ChatService;
use App\Models\SnapshotPegawai;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function history(Request $request)
    {
        $user = $request->user();
        // Filter by current session ID to reset chat on login
        $messages = ChatMessage::where('user_id', $user->id)
            ->where('session_id', $request->session()->getId())
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'messages' => $messages,
            'user' => [
                'nip' => $user->nip,
                'name' => $user->name,
            ]
        ]);
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $user = $request->user();
        $messageText = $request->input('message');
        $sessionId = $request->session()->getId();

        $pegawai = $user->snapshotPegawai;

        // 1. Simpan Pesan User
        $userMsg = ChatMessage::create([
            'user_id' => $user->id,
            'session_id' => $sessionId,
            'nip_sender' => $user->nip,
            'nama_sender' => $pegawai ? $pegawai->nama_pegawai : $user->name,
            'message' => $messageText,
            'is_from_bot' => false,
            'is_read' => false,
            'source' => 'web'
        ]);

        // 2. Bot Response
        $botResponse = $this->chatService->getBotResponse($messageText, $user);

        // Refresh User Message to get updated name if NIP was verified
        $userMsg->refresh();

        $botMsg = ChatMessage::create([
            'user_id' => $user->id,
            'session_id' => $sessionId,
            'nip_sender' => 'bot',
            'nama_sender' => 'Assistant',
            'message' => $botResponse,
            'is_from_bot' => true,
            'is_read' => true,
            'source' => 'web'
        ]);

        return response()->json([
            'user_message' => $userMsg,
            'bot_message' => $botMsg,
            'user_nip' => $user->fresh()->nip,
        ]);
    }

    // Admin Methods (Unchanged except fetching session logic if needed? 
    // Conversations view works across all sessions.
    // Reply needs session_id)
    public function conversations()
    {
        // ... (Keep existing implementation, maybe show latest message from ANY session)
        // Actually, if we reset, previous chats are "History". Admin might want to see latest active.
        // Current logic `latest()` works fine.
        $users = \App\Models\User::whereHas('chatMessages')
            ->withCount([
                'chatMessages as unread_count' => function ($query) {
                    $query->where('is_from_bot', false)->where('is_read', false);
                }
            ])
            ->with([
                'chatMessages' => function ($q) {
                    $q->latest()->limit(1);
                }
            ])
            ->get()
            ->sortByDesc(function ($user) {
                return $user->chatMessages->first()?->created_at;
            })
            ->values()
            ->map(function ($user) {
                $lastMsg = $user->chatMessages->first();
                return [
                    'id' => $user->id,
                    'name' => $lastMsg?->nama_sender ?? $user->name,
                    'nip' => $user->nip,
                    'unread_count' => $user->unread_count,
                    'last_message' => $lastMsg ? $lastMsg->message : '',
                    'last_time' => $lastMsg ? $lastMsg->created_at->format('H:i') : '',
                ];
            });

        return response()->json($users);
    }

    // Admin sees ALL history or just session? Usually all.
    // If admin replies, we need target session.
    public function userHistory($userId)
    {
        // Admin sees all history for context
        $messages = ChatMessage::where('user_id', $userId)
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark as read
        ChatMessage::where('user_id', $userId)
            ->where('is_from_bot', false)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json($messages);
    }

    public function reply(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $admin = $request->user();

        // Find latest session_id from user's last message
        $lastMsg = ChatMessage::where('user_id', $request->user_id)->latest()->first();
        $sessionId = $lastMsg ? $lastMsg->session_id : null;

        $msg = ChatMessage::create([
            'user_id' => $request->user_id,
            'session_id' => $sessionId, // Use user's last session
            'nip_sender' => $admin->nip,
            'nama_sender' => $admin->name,
            'message' => $request->message,
            'is_from_bot' => true,
            'is_read' => true,
            'source' => 'web'
        ]);

        return response()->json($msg);
    }
}
