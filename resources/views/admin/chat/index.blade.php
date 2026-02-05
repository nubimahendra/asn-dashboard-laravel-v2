@extends('layouts.app')

@section('content')
    <div
        class="flex h-[calc(100vh-100px)] overflow-hidden bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700">

        <!-- Sidebar / User List -->
        <div class="w-1/3 border-r border-gray-200 dark:border-gray-700 flex flex-col">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Percakapan</h2>
                <div class="relative">
                    <input type="text" id="admin-chat-search" placeholder="Cari Pegawai..."
                        class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500 text-gray-800 dark:text-white">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>

            <div id="user-list" class="flex-1 overflow-y-auto scrollbar-thin">
                <!-- User items injected here -->
                <div class="p-4 text-center text-gray-500 text-sm">Memuat...</div>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="w-2/3 flex flex-col bg-gray-50 dark:bg-gray-900">
            <!-- Header -->
            <div id="chat-header"
                class="p-4 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center hidden">
                <div>
                    <h3 id="chat-user-name" class="font-bold text-gray-800 dark:text-gray-200">User Name</h3>
                    <p id="chat-user-nip" class="text-xs text-gray-500">NIP: -</p>
                </div>
            </div>

            <!-- Messages -->
            <div id="admin-chat-messages" class="flex-1 p-4 overflow-y-auto scrollbar-thin">
                <div class="flex h-full items-center justify-center text-gray-400">
                    <p>Pilih percakapan dari daftar di sebelah kiri.</p>
                </div>
            </div>

            <!-- Input -->
            <div id="chat-input-area"
                class="p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 hidden">
                <form id="admin-chat-form" onsubmit="event.preventDefault(); sendReply();" class="flex gap-2 items-end">
                    <textarea id="admin-reply-input" rows="1" placeholder="Ketik balasan..."
                        class="flex-1 px-4 py-2 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white resize-none overflow-hidden"
                        style="min-height: 40px; max-height: 120px;"
                        oninput="this.style.height = 'auto'; this.style.height = (this.scrollHeight) + 'px'"
                        onkeydown="if(event.key === 'Enter' && !event.shiftKey) { event.preventDefault(); sendReply(); }"></textarea>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-xl font-medium transition-colors h-10">
                        Kirim
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        let selectedUserId = null;
        let users = [];

        // Polling for user list
        setInterval(loadUsers, 5000);
        loadUsers();

        async function loadUsers() {
            try {
                const res = await fetch('/api/chat/conversations');
                if (!res.ok) return;
                const data = await res.json();
                renderUsers(data);
            } catch (e) { console.error(e); }
        }

        function renderUsers(data) {
            const container = document.getElementById('user-list');

            let html = '';
            if (data.length === 0) {
                html = '<div class="p-8 text-center text-gray-500 text-sm">Belum ada percakapan.</div>';
            } else {
                data.forEach(user => {
                    const isActive = selectedUserId == user.id;
                    const bgClass = isActive ? 'bg-blue-100 dark:bg-gray-700' : 'hover:bg-blue-50 dark:hover:bg-gray-700';
                    const unreadBadge = user.unread_count > 0 ? `<span class="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">${user.unread_count}</span>` : '';

                    // Pass user.name (which is nama_sender from controller) to selectUser
                    html += `
                                                            <div onclick="selectUser(${user.id}, ${JSON.stringify(user.name).replace(/"/g, '&quot;')}, ${user.nip ? JSON.stringify(user.nip).replace(/"/g, '&quot;') : 'null'})" 
                                                                 class="p-4 border-b border-gray-100 dark:border-gray-700 cursor-pointer transition-colors ${bgClass}">
                                                                <div class="flex justify-between items-start mb-1">
                                                                    <span class="font-medium text-gray-900 dark:text-gray-100">${escapeHtml(user.name)}</span>
                                                                    ${unreadBadge}
                                                                </div>
                                                                <div class="text-xs text-gray-500 dark:text-gray-400 truncate flex justify-between">
                                                                    <span class="truncate max-w-[70%]">${escapeHtml(user.last_message)}</span>
                                                                    <span>${user.last_time}</span>
                                                                </div>
                                                            </div>
                                                        `;
                });
            }

            if (container.innerHTML !== html) {
                container.innerHTML = html;
            }
        }

        async function selectUser(userId, name, nip) {
            selectedUserId = userId;
            document.getElementById('chat-header').classList.remove('hidden');
            document.getElementById('chat-input-area').classList.remove('hidden');

            // Name should be the one passed from renderUsers (which comes from API mapping to nama_sender)
            document.getElementById('chat-user-name').innerText = name;
            document.getElementById('chat-user-nip').innerText = nip ? 'NIP: ' + nip : (name === 'Assistant' ? 'System' : 'NIP: -');

            loadUsers(); // Refresh to update active state UI
            loadMessages(userId);
        }

        async function loadMessages(userId) {
            const container = document.getElementById('admin-chat-messages');
            // Don't clear if already showing this user? Maybe better UX. But simpler to clear.
            container.innerHTML = '<div class="flex h-full items-center justify-center text-gray-500">Memuat...</div>';

            try {
                const res = await fetch(`/api/chat/admin/history/${userId}`);
                if (!res.ok) throw new Error('Failed');
                const messages = await res.json();

                if (messages.length === 0) {
                    container.innerHTML = '<div class="flex h-full items-center justify-center text-gray-400">Belum ada pesan.</div>';
                    return;
                }

                let html = '';
                messages.forEach(msg => {
                    const isMyMessage = msg.is_from_bot; // In Admin view, "bot" (admin reply) is "Me"
                    const alignClass = isMyMessage ? 'justify-end' : 'justify-start';
                    const bgClass = isMyMessage ? 'bg-blue-600 text-white rounded-br-none' : 'bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-bl-none border border-gray-200 dark:border-gray-600';

                    // Format Timestamp
                    const date = new Date(msg.created_at);
                    const timeStr = date.getHours().toString().padStart(2, '0') + ':' + date.getMinutes().toString().padStart(2, '0');

                    html += `
                                        <div class="mb-2 flex ${alignClass}">
                                            <div class="max-w-[85%] w-fit px-2 py-1 rounded-lg shadow-sm text-sm whitespace-pre-wrap leading-tight ${bgClass}">
                                                 ${escapeHtml(msg.message)}
                                                 <div class="text-[10px] text-right -mt-1 opacity-70 flex justify-end items-center gap-1 leading-none pb-0">
                                                    <span>${timeStr}</span>
                                                 </div>
                                            </div>
                                        </div>
                                    `;
                });
                container.innerHTML = html;
                container.scrollTop = container.scrollHeight;
            } catch (e) {
                console.error(e);
                container.innerHTML = '<div class="text-red-500 text-center mt-10">Gagal memuat pesan.</div>';
            }
        }

        async function sendReply() {
            if (!selectedUserId) return;
            const input = document.getElementById('admin-reply-input');
            const text = input.value.trim();
            if (!text) return;

            input.value = '';
            input.style.height = 'auto';

            const container = document.getElementById('admin-chat-messages');

            // Format current time
            const now = new Date();
            const timeStr = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');

            const div = document.createElement('div');
            div.className = 'mb-2 flex justify-end';
            div.innerHTML = `
                    <div class="max-w-[85%] w-fit px-2 py-1 rounded-lg shadow-sm text-sm whitespace-pre-wrap leading-tight bg-blue-600 text-white rounded-br-none">
                        ${escapeHtml(text)}
                        <div class="text-[10px] text-right -mt-1 opacity-70 flex justify-end items-center gap-1 leading-none pb-0">
                            <span>${timeStr}</span>
                        </div>
                    </div>`;
            container.appendChild(div);
            container.scrollTop = container.scrollHeight;

            try {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                // Use fetch to send
                const res = await fetch('/api/chat/admin/reply', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({ user_id: selectedUserId, message: text })
                });

                if (!res.ok) throw new Error('Failed');
                loadUsers(); // Update left sidebar
            } catch (e) {
                console.error(e);
                div.querySelector('.bg-blue-600').classList.add('bg-red-500'); // Indicate error
                alert('Gagal mengirim pesan');
            }
        }

        function escapeHtml(text) {
            if (!text) return text;
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    </script>
@endsection