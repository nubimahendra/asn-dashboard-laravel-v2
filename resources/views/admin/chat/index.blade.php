@extends('layouts.app')

@section('content')
    <div
        class="flex h-[calc(100vh-theme('spacing.24'))] overflow-hidden bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700">
        <!-- Left Sidebar: Contact List -->
        <div class="w-full md:w-1/3 lg:w-1/4 border-r border-gray-100 dark:border-gray-700 flex flex-col">
            <!-- Header -->
            <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200">Pesan Masuk</h2>
            </div>

            <!-- List -->
            <div class="flex-1 overflow-y-auto custom-scrollbar">
                @forelse($chats as $chat)
                    <button onclick="loadChat('{{ $chat->sender_number }}')"
                        class="w-full text-left p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors border-b border-gray-50 dark:border-gray-800 focus:outline-none focus:bg-blue-50 dark:focus:bg-blue-900/20 group relative"
                        id="contact-{{ $chat->sender_number }}">
                        <div class="flex justify-between items-start mb-1">
                            <span class="font-semibold text-gray-800 dark:text-gray-200 truncate pr-2">
                                {{ $chat->pegawai ? $chat->pegawai->nama_pegawai : $chat->sender_number }}
                            </span>
                            <span class="text-xs text-gray-400 whitespace-nowrap">
                                {{ $chat->latest_message ? $chat->latest_message->created_at->format('H:i') : '' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-end">
                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate w-3/4">
                                {{ $chat->latest_message ? Str::limit($chat->latest_message->message, 30) : 'Tidak ada pesan' }}
                            </p>
                            @if($chat->latest_message && !$chat->latest_message->is_read && $chat->latest_message->direction == 'in')
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    Baru
                                </span>
                            @endif
                        </div>
                    </button>
                @empty
                    <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                        Belum ada pesan masuk.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right Column: Chat Area -->
        <div class="hidden md:flex flex-1 flex-col bg-gray-50 dark:bg-gray-900" id="chat-window">
            @if(isset($messages))
                @include('admin.chat.partials.conversation')
            @else
                <!-- Placeholder State -->
                <div class="flex-1 flex flex-col items-center justify-center text-gray-400 dark:text-gray-600">
                    <svg class="w-16 h-16 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                        </path>
                    </svg>
                    <p class="text-lg font-medium">Pilih kontak untuk melihat percakapan</p>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            function loadChat(phoneNumber) {
                const chatWindow = document.getElementById('chat-window');

                // Visual feedback
                chatWindow.innerHTML = '<div class="flex-1 flex items-center justify-center"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div></div>';

                // Fetch Conversation Partial
                const url = "{{ route('admin.chat.messages.show', ':phone') }}".replace(':phone', phoneNumber);
                fetch(url)
                    .then(response => response.text())
                    .then(html => {
                        chatWindow.innerHTML = html;
                        scrollToBottom();
                    })
                    .catch(error => {
                        console.error('Error loading chat:', error);
                        chatWindow.innerHTML = '<div class="p-4 text-center text-red-500">Gagal memuat percakapan.</div>';
                    });
            }

            function scrollToBottom() {
                const history = document.getElementById('chat-history');
                if (history) {
                    history.scrollTop = history.scrollHeight;
                }
            }

            // Delegate submit event for dynamic form
            document.addEventListener('submit', function (e) {
                if (e.target && e.target.id === 'reply-form') {
                    e.preventDefault();
                    const form = e.target;
                    const btn = form.querySelector('button[type="submit"]');
                    const originalBtnContent = btn.innerHTML;
                    const input = form.querySelector('textarea');
                    const message = input.value;
                    const phoneNumber = form.querySelector('input[name="phone_number"]').value;

                    if (!message.trim()) return;

                    // Simple optimistic UI update or wait for server
                    btn.disabled = true;
                    btn.innerHTML = '<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

                    fetch(`{{ route('admin.chat.messages.reply') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json' // Expect JSON
                        },
                        body: JSON.stringify({
                            phone_number: phoneNumber,
                            message: message
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.html) {
                                const history = document.getElementById('chat-history');
                                // Create a temp div to parse HTML string
                                const temp = document.createElement('div');
                                temp.innerHTML = data.html;
                                history.appendChild(temp.firstElementChild);
                                scrollToBottom();
                                form.reset();
                            }
                        })
                        .catch(error => {
                            console.error('Error sending reply:', error);
                            alert('Gagal mengirim pesan.');
                        })
                        .finally(() => {
                            btn.disabled = false;
                            btn.innerHTML = originalBtnContent;
                            input.focus();
                        });
                }
            });
        </script>
    @endpush
@endsection