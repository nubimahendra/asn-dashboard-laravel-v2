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

        // --- 1. Timeout Check ---
        $lastActivity = session('chat_last_activity');
        // 10 minutes = 600 seconds
        if ($lastActivity && (time() - $lastActivity > 600)) {
            $this->expireSession($user);
            return response()->json(['status' => 'session_expired'], 401);
        }
        // Update activity
        session(['chat_last_activity' => time()]);

        $pegawai = $user->snapshotPegawai;

        // --- 2. Save User Message ---
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

        // --- 3. Check Chat Mode (Bot vs Human) ---
        $chatMode = session('chat_mode', 'bot'); // default 'bot'

        $botMsg = null;
        if ($chatMode === 'bot') {
            // Get Bot Response
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
        } else {
            // Human mode: Silent. Admin will reply later.
            // Optionally we could send an auto-ack, but requirement implies just "bypass faq".
        }

        return response()->json([
            'user_message' => $userMsg,
            'bot_message' => $botMsg,
            'user_nip' => $user->fresh()->nip,
        ]);
    }

    public function expire(Request $request)
    {
        $this->expireSession($request->user());
        return response()->json(['status' => 'ok']);
    }

    public function switchMode(Request $request)
    {
        $request->validate(['mode' => 'required|in:bot,human']);
        session(['chat_mode' => $request->input('mode')]);

        // Update activity to prevent immediate timeout after switch
        session(['chat_last_activity' => time()]);

        return response()->json(['status' => 'ok', 'mode' => $request->input('mode')]);
    }

    private function expireSession($user)
    {
        session()->forget(['chat_last_activity', 'chat_mode']);
        // Clearing NIP as per requirement to force re-entry
        if ($user) {
            $user->update(['nip' => null]);
        }
    }

    // Admin Methods (Unchanged except fetching session logic if needed? 
    // Conversations view works across all sessions.
    // Reply needs session_id)
    public function conversations()
    {
        // Fetch users who have chat messages
        $users = \App\Models\User::whereHas('chatMessages')
            ->with([
                'snapshotPegawai',
                'chatMessages' => function ($q) {
                    // Ensure we get the latest messages to find the last user message if needed, 
                    // and for the preview text.
                    $q->orderBy('created_at', 'desc');
                }
            ])
            ->withCount([
                'chatMessages as unread_count' => function ($query) {
                    $query->where('is_from_bot', false)->where('is_read', false);
                }
            ])
            ->get()
            ->sortByDesc(function ($user) {
                return $user->chatMessages->first()?->created_at;
            })
            ->values()
            ->map(function ($user) {
                $lastMsg = $user->chatMessages->first(); // Newest message (could be bot)
    
                // Priority:
                // 1. Snapshot Pegawai Name (Real Employee Name)
                // 2. Name from the last "User" message (stored in chat_messages table)
                // 3. User Table Name (Fallback)
    
                $displayName = $user->name; // Default
    
                if ($user->snapshotPegawai) {
                    $displayName = $user->snapshotPegawai->nama_pegawai;
                } else {
                    // Try to find last message from this user to see if they laid claim to a name
                    // (useful if they verified NIP previously but snapshot relation isn't loaded or something)
                    $lastUserMsg = $user->chatMessages->where('is_from_bot', false)->first();
                    if ($lastUserMsg && $lastUserMsg->nama_sender) {
                        $displayName = $lastUserMsg->nama_sender;
                    }
                }

                return [
                    'id' => $user->id,
                    'name' => $displayName,
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
