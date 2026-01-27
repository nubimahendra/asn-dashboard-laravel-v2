<style>
    .typing-cursor::after {
        content: '|';
        animation: blink 1s step-start infinite;
    }

    @keyframes blink {
        50% {
            opacity: 0;
        }
    }
</style>
<div id="asn-chat-widget">
    <!-- Chat Window -->
    <div id="chat-window"
        class="fixed bottom-6 right-6 z-[9999] w-80 sm:w-96 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col h-[500px] hidden">
        <!-- Header -->
        <div class="bg-blue-600 p-4 text-white flex justify-between items-center shrink-0">
            <h3 class="font-semibold">Bantuan ASN</h3>
            <button onclick="toggleChat()" class="hover:bg-blue-700 rounded p-1 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        <!-- Chat Messages -->
        <div id="chat-messages" class="flex-1 p-4 overflow-y-auto bg-gray-50 dark:bg-gray-900 scrollbar-thin">
            <!-- Messages injected here -->
            <div class="text-center text-gray-500 mt-10 text-sm loading-text">Memuat percakapan...</div>
        </div>

        <!-- Input Area -->
        <div class="p-3 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
            <form id="chat-form" onsubmit="event.preventDefault(); sendMessage();" class="flex gap-2 items-end">
                <textarea id="chat-input" rows="1" placeholder="Ketik pesan..."
                    class="flex-1 px-3 py-2 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white resize-none overflow-hidden scrollbar-none"
                    style="min-height: 40px; max-height: 100px;"
                    oninput="this.style.height = 'auto'; this.style.height = (this.scrollHeight) + 'px'"
                    onkeydown="if(event.key === 'Enter' && !event.shiftKey) { event.preventDefault(); sendMessage(); }"></textarea>
                <button type="submit"
                    class="bg-blue-600 text-white rounded-full p-2 hover:bg-blue-700 transition-colors focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Bubble Button -->
    <button id="chat-bubble" onclick="toggleChat()"
        class="fixed bottom-6 right-6 z-[9990] transition-all duration-200 ease-in-out bg-blue-600 hover:bg-blue-700 text-white rounded-full p-4 shadow-lg flex items-center justify-center focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
        </svg>
    </button>
</div>

<script>
    let chatOpen = false;
    let chatPollingInterval = null;
    const chatWindow = document.getElementById('chat-window');
    const chatBubble = document.getElementById('chat-bubble');
    const messagesContainer = document.getElementById('chat-messages');
    const chatInput = document.getElementById('chat-input');
    const notifSound = new Audio('/sounds/notif.mp3');

    function toggleChat() {
        chatOpen = !chatOpen;
        if (chatOpen) {
            chatWindow.classList.remove('hidden');
            chatBubble.classList.add('hidden');
            loadHistory();
            startPolling();
            setTimeout(scrollToBottom, 100);
        } else {
            chatWindow.classList.add('hidden');
            chatBubble.classList.remove('hidden');
            stopPolling();
        }
    }

    async function loadHistory() {
        try {
            const res = await fetch('/api/chat/history', {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + getCookie('XSRF-TOKEN') // Ensure auth if needed, but normally default axio/fetch handles it if configured. Sanctum needs Referer/CSRF
                }
            });
            // Sanctum simple usage for web actually relies on sessions if we are on same domain. 
            // We just need to make sure we hit the endpoint. If using 'middleware: auth:sanctum', it checks session if Referer is present.

            if (!res.ok) throw new Error('Failed to load');
            const data = await res.json();
            renderMessages(data.messages, data.user);
        } catch (e) {
            console.error(e);
            messagesContainer.innerHTML = '<p class="text-center text-red-500 text-sm mt-4">Gagal memuat percakapan.</p>';
        }
    }

    function renderMessages(messages, user) {
        if (!messages || messages.length === 0) {
            messagesContainer.innerHTML = '<div class="text-center text-gray-500 mt-10 text-sm"><p>Halo! Ada yang bisa kami bantu?</p></div>';
            return;
        }

        let html = '';
        messages.forEach(msg => {
            const isBot = msg.is_from_bot;
            const alignClass = isBot ? 'justify-start' : 'justify-end';
            const bgClass = isBot ? 'bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-bl-none border border-gray-200 dark:border-gray-600' : 'bg-blue-600 text-white rounded-br-none';

            html += `
                <div class="mb-3 flex ${alignClass}">
                    <div id="msg-${msg.id || 'hist-' + Math.random().toString(36).substr(2, 9)}" class="max-w-[80%] w-fit rounded-lg px-4 py-2 text-sm shadow-sm whitespace-pre-wrap ${bgClass}">
                        ${escapeHtml(msg.message)}
                    </div>
                </div>
            `;
        });
        messagesContainer.innerHTML = html;
        scrollToBottom();
    }

    async function sendMessage() {
        const text = chatInput.value.trim();
        if (!text) return;

        // Optimistic UI
        chatInput.value = '';
        chatInput.style.height = 'auto'; // Reset height
        appendMessage(text, false);
        scrollToBottom();

        try {
            // CSRF Token needed for POST
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const res = await fetch('/api/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ message: text })
            });

            if (!res.ok) throw new Error('Send failed');
            const data = await res.json();

            // Replace/Append response
            // Ideally we re-fetch history or just append the bot response.
            // Let's just append bot response if available
            if (data.bot_message) {
                // Modified: Use Typewriter effect for bot response
                const msgId = `msg-${data.bot_message.id}`;
                appendMessage(data.bot_message.message, true, msgId);

                // Trigger typewriter
                await typeWriterEffect(msgId, data.bot_message.message);

                notifSound.play().catch(e => console.log('Audio play failed', e));
                scrollToBottom();
            }
        } catch (e) {
            console.error(e);
            // Show error in chat
        }
    }

    function appendMessage(text, isBot, specificId = null) {
        const alignClass = isBot ? 'justify-start' : 'justify-end';
        const bgClass = isBot ? 'bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-bl-none border border-gray-200 dark:border-gray-600' : 'bg-blue-600 text-white rounded-br-none';
        const msgId = specificId || `msg-${Date.now()}`;

        const div = document.createElement('div');
        div.className = `mb-3 flex ${alignClass}`;
        div.innerHTML = `
            <div id="${msgId}" class="max-w-[80%] w-fit rounded-lg px-4 py-2 text-sm shadow-sm whitespace-pre-wrap ${bgClass}">
                ${escapeHtml(text)}
            </div>
        `;
        messagesContainer.appendChild(div);
    }

    // Typewriter Effect Function
    function typeWriterEffect(elementId, text) {
        return new Promise((resolve) => {
            const element = document.getElementById(elementId);
            if (!element) {
                resolve();
                return;
            }

            element.innerHTML = ''; // Clear content
            element.classList.add('typing-cursor'); // Add cursor

            let i = 0;
            const speed = 30; // ms per char

            function type() {
                if (i < text.length) {
                    element.innerHTML += escapeHtml(text.charAt(i));
                    i++;
                    setTimeout(type, speed);
                    scrollToBottom(); // Auto scroll while typing
                } else {
                    element.classList.remove('typing-cursor'); // Remove cursor
                    resolve();
                }
            }

            type();
        });
    }

    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    function startPolling() {
        if (chatPollingInterval) clearInterval(chatPollingInterval);
        return;
        // Polling logic: fetch history every 5s and diff? 
        // For simplicity, maybe just leave it manually refreshed or simple poll
        // chatPollingInterval = setInterval(loadHistory, 5000); 
        // Careful with overwriting scroll
    }

    function stopPolling() {
        if (chatPollingInterval) clearInterval(chatPollingInterval);
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

    // Helper for Cookie (if needed, but mainly using meta CSRF)
    function getCookie(name) {
        let matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }
</script>