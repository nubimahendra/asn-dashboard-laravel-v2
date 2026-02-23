<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\SnapshotPegawai;
use App\Models\Faq;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Log;

class WhatsAppController extends Controller
{
    protected $chatService;

    public function __construct(\App\Services\ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function handleWebhook(Request $request)
    {
        $sender = $request->input('sender'); // Format Fonnte: 62812xxx
        $message = $request->input('message'); // Text message

        // Gunakan Service untuk memproses logic (Cari user, simpan pesan, cari FAQ, simpan balasan)
        // Identifier = $sender
        // Source = 'whatsapp'
        // UserId = null
        $result = $this->chatService->processMessage($sender, $message, 'whatsapp', null);

        $replyMessage = $result['reply_message'];

        // Kirim via WhatsApp (Fonnte logic removed)

        return response()->json(['status' => 'success'], 200);
    }
}
