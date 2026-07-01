<div class="relative inline-block text-left" id="user-dropdown-container">
    <button type="button" id="user-dropdown-button"
        class="flex items-center gap-2 px-4 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-white border border-white/10 transition-all duration-300 font-medium"
        title="Menu User">
        <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
            </path>
        </svg>
        <span class="text-sm font-medium hidden sm:block">User</span>
        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" id="user-dropdown-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <div id="user-dropdown-menu"
        class="hidden absolute right-0 mt-2 w-48 bg-zinc-800 rounded-xl shadow-lg shadow-black/50 py-1 border border-zinc-700 z-50 transform origin-top-right transition-all opacity-0 scale-95">
        
        <div class="px-4 py-2 border-b border-zinc-700 sm:hidden">
            <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name ?? 'User' }}</p>
            <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email ?? '' }}</p>
        </div>

        @if(auth()->user() && (auth()->user()->role === 'admin' || strtolower(auth()->user()->role) === 'admin'))
            <a href="{{ route('masn.users.index') }}"
                class="block px-4 py-2 text-sm text-gray-300 hover:bg-zinc-700 hover:text-indigo-400 transition-colors">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Konfigurasi
                </div>
            </a>
            <a href="{{ route('masn.sync.index') }}"
                class="block px-4 py-2 text-sm text-gray-300 hover:bg-zinc-700 hover:text-indigo-400 transition-colors">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Sync
                </div>
            </a>
            <div class="border-t border-zinc-700 my-1"></div>
        @endif

        <form action="{{ route('logout') }}" method="POST" class="block w-full text-left m-0">
            @csrf
            <button type="submit"
                class="w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-red-900/30 hover:text-red-300 transition-colors rounded-b-xl">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Logout
                </div>
            </button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const button = document.getElementById('user-dropdown-button');
        const menu = document.getElementById('user-dropdown-menu');
        const icon = document.getElementById('user-dropdown-icon');
        const container = document.getElementById('user-dropdown-container');

        if (button && menu) {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                const isHidden = menu.classList.contains('hidden');
                
                if (isHidden) {
                    menu.classList.remove('hidden');
                    // Small delay to allow display:block to apply before animating opacity
                    setTimeout(() => {
                        menu.classList.remove('opacity-0', 'scale-95');
                        menu.classList.add('opacity-100', 'scale-100');
                    }, 10);
                    icon.classList.add('rotate-180');
                } else {
                    menu.classList.remove('opacity-100', 'scale-100');
                    menu.classList.add('opacity-0', 'scale-95');
                    setTimeout(() => {
                        menu.classList.add('hidden');
                    }, 200);
                    icon.classList.remove('rotate-180');
                }
            });

            document.addEventListener('click', function(e) {
                if (container && !container.contains(e.target) && !menu.classList.contains('hidden')) {
                    menu.classList.remove('opacity-100', 'scale-100');
                    menu.classList.add('opacity-0', 'scale-95');
                    setTimeout(() => {
                        menu.classList.add('hidden');
                    }, 200);
                    icon.classList.remove('rotate-180');
                }
            });
        }
    });
</script>
