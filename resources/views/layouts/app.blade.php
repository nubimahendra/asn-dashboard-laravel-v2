<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard ASN</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }

        .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
        }

        .scrollbar-thin::-webkit-scrollbar-track {
            background: transparent;
        }

        .scrollbar-thin::-webkit-scrollbar-thumb {
            background-color: #e5e7eb;
            border-radius: 20px;
        }

        .dark .scrollbar-thin::-webkit-scrollbar-thumb {
            background-color: #4b5563;
        }
    </style>
</head>

<body class="bg-slate-50 dark:bg-zinc-950 min-h-screen transition-colors duration-300">

    <!-- Mobile Topbar -->
    <header id="mobile-topbar"
        class="md:hidden fixed top-0 left-0 right-0 z-30 bg-white/95 dark:bg-zinc-900/95 backdrop-blur-sm border-b border-slate-200/60 dark:border-zinc-800/60 shadow-sm flex items-center justify-between px-4 h-14">
        <button id="mobile-menu-btn" aria-label="Buka menu"
            class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-400 focus:outline-none">
            <svg id="mobile-hamburger-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <a href="{{ route('hub') }}"
            class="flex items-center gap-2 text-base font-bold text-gray-800 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            DASHBOARD
        </a>
        <button id="theme-toggle-mobile" aria-label="Toggle tema"
            class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-400 focus:outline-none">
            <svg id="theme-toggle-mobile-dark-icon" class="w-5 h-5 hidden" fill="currentColor" viewBox="0 0 20 20">
                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
            </svg>
            <svg id="theme-toggle-mobile-light-icon" class="w-5 h-5 hidden" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                    clip-rule="evenodd" />
            </svg>
        </button>
    </header>

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebar-overlay"
        class="fixed inset-0 z-40 bg-black/50 hidden md:hidden transition-opacity duration-300 opacity-0"></div>

    <div id="wrapper" class="flex w-full items-stretch gap-0 md:gap-0 lg:gap-0">
        @unless($hideSidebar ?? false)
        <aside id="main-sidebar"
            class="fixed md:sticky inset-y-0 left-0 z-50 md:z-20 w-64 bg-white dark:bg-zinc-900 shadow-xl shadow-slate-200/50 dark:shadow-zinc-950/50 flex-col h-screen flex-shrink-0 border-r border-slate-200/40 dark:border-zinc-800/40 flex transition-transform duration-300 -translate-x-full md:translate-x-0">
            <div
                class="p-5 md:p-7 border-b border-slate-100 dark:border-zinc-800/60 flex-shrink-0 flex items-center justify-between">
                <a href="{{ route('hub') }}"
                    class="text-lg font-semibold text-slate-800 dark:text-zinc-100 tracking-tight flex items-center gap-2 whitespace-nowrap overflow-hidden transition-all duration-300 hover:text-blue-600 dark:hover:text-blue-400">
                    <svg class="w-6 h-6 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                        </path>
                    </svg>
                    <span class="sidebar-text">DASHBOARD</span>
                </a>
                <!-- Close button (mobile) / Collapse button (desktop) -->
                <button id="sidebar-close-mobile" aria-label="Tutup menu"
                    class="md:hidden p-1 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-400 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <button id="sidebar-toggle"
                    class="hidden md:block p-1 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 focus:outline-none ml-auto">
                    <svg id="toggle-icon" class="w-6 h-6 transform transition-transform duration-300" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto py-5 px-4 space-y-5">
                @yield('sidebar-menu')
            </div>
            @if(auth()->user()->role === 'admin')
                <div class="p-4 border-t border-slate-100 dark:border-zinc-800/60">

                </div>
            @endif

            <!-- Theme Toggle Button (Sidebar) -->
            <div class="px-4 py-2 border-t border-slate-100 dark:border-zinc-800/60">
                <button id="theme-toggle-sidebar"
                    class="w-full flex items-center gap-2 px-4 py-2 text-slate-500 dark:text-zinc-400 hover:bg-slate-50/80 dark:hover:bg-zinc-800/60 hover:text-yellow-500 dark:hover:text-yellow-400 rounded-lg transition-colors group">
                    <svg id="theme-toggle-light-icon-sidebar" class="w-5 h-5 hidden flex-shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <svg id="theme-toggle-dark-icon-sidebar" class="w-5 h-5 hidden flex-shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                    <span class="sidebar-text font-medium" id="theme-toggle-text">Tema Gelap</span>
                </button>
            </div>

            <!-- Logout Button (Sidebar Footer) -->
            <div class="px-4 pb-4 border-slate-100 dark:border-zinc-800/60 mt-auto">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-2 px-4 py-2 text-slate-500 dark:text-zinc-400 hover:bg-red-50/60 dark:hover:bg-red-950/20 hover:text-red-600 dark:hover:text-red-400 rounded-lg transition-colors group">
                        <svg class="w-5 h-5 flex-shrink-0 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                        <span class="sidebar-text font-medium">Keluar</span>
                    </button>
                </form>
            </div>
        </aside>
        @endunless

        <main id="main-content"
            class="flex-1 bg-slate-50 dark:bg-zinc-950 min-h-screen transition-all duration-300 w-full min-w-0">
            <!-- Mobile spacer for fixed topbar -->
            <div class="h-14 md:hidden"></div>
            <div class="px-4 py-4 md:px-8 md:py-6">
                <!-- Alerts removed and handled by toast notification -->
            </div>
            @yield('content')
        </main>

    </div>
    <script>
        // Wrap OPD dropdown functionality in IIFE to avoid variable name conflicts
        (function () {
            const dropdownTrigger = document.getElementById('dropdown-trigger');
            const dropdownMenu = document.getElementById('dropdown-menu');
            const searchInput = document.getElementById('opd-search');
            const opdList = document.getElementById('opd-list');
            const opdItems = opdList ? opdList.getElementsByClassName('opd-item') : [];
            const noResults = document.getElementById('no-results');

            function closeDropdown() {
                if (!dropdownMenu) return;
                dropdownMenu.classList.remove('scale-100', 'opacity-100');
                dropdownMenu.classList.add('scale-95', 'opacity-0');
                setTimeout(() => { dropdownMenu.classList.add('hidden'); }, 200);
            }

            if (dropdownTrigger && dropdownMenu) {
                dropdownTrigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const isHidden = dropdownMenu.classList.contains('hidden');
                    if (isHidden) {
                        dropdownMenu.classList.remove('hidden');
                        requestAnimationFrame(() => {
                            dropdownMenu.classList.remove('scale-95', 'opacity-0');
                            dropdownMenu.classList.add('scale-100', 'opacity-100');
                        });
                        if (searchInput) setTimeout(() => searchInput.focus(), 100);
                    } else {
                        closeDropdown();
                    }
                });
                document.addEventListener('click', (e) => {
                    if (!dropdownMenu.contains(e.target) && !dropdownTrigger.contains(e.target)) {
                        closeDropdown();
                    }
                });
            }

            if (searchInput) {
                searchInput.addEventListener('click', (e) => e.stopPropagation());
                searchInput.addEventListener('keyup', function (e) {
                    const term = e.target.value.toLowerCase();
                    let hasResults = false;
                    Array.from(opdItems).forEach(item => {
                        const name = item.getAttribute('data-name');
                        if (name.includes(term)) {
                            item.classList.remove('hidden');
                            hasResults = true;
                        } else {
                            item.classList.add('hidden');
                        }
                    });
                    if (noResults) { noResults.classList.toggle('hidden', hasResults); }
                });
            }
        })();
        const menuStatistikToggle = document.getElementById('menu-statistik-toggle');
        const menuStatistikContent = document.getElementById('menu-statistik-content');
        const menuStatistikIcon = document.getElementById('menu-statistik-icon');
        if (menuStatistikToggle && menuStatistikContent) {
            menuStatistikToggle.addEventListener('click', () => {
                const isHidden = menuStatistikContent.classList.contains('hidden');
                if (isHidden) { menuStatistikContent.classList.remove('hidden'); menuStatistikIcon.classList.add('rotate-180'); } else { menuStatistikContent.classList.add('hidden'); menuStatistikIcon.classList.remove('rotate-180'); }
            });
        }
        const menuChatbotToggle = document.getElementById('menu-chatbot-toggle');
        const menuChatbotContent = document.getElementById('menu-chatbot-content');
        const menuChatbotIcon = document.getElementById('menu-chatbot-icon');
        if (menuChatbotToggle && menuChatbotContent) {
            menuChatbotToggle.addEventListener('click', () => {
                const isHidden = menuChatbotContent.classList.contains('hidden');
                if (isHidden) { menuChatbotContent.classList.remove('hidden'); menuChatbotIcon.classList.add('rotate-180'); } else { menuChatbotContent.classList.add('hidden'); menuChatbotIcon.classList.remove('rotate-180'); }
            });
        }
        const menuPengaturanToggle = document.getElementById('menu-pengaturan-toggle');
        const menuPengaturanContent = document.getElementById('menu-pengaturan-content');
        const menuPengaturanIcon = document.getElementById('menu-pengaturan-icon');
        if (menuPengaturanToggle && menuPengaturanContent) {
            menuPengaturanToggle.addEventListener('click', () => {
                const isHidden = menuPengaturanContent.classList.contains('hidden');
                if (isHidden) { menuPengaturanContent.classList.remove('hidden'); menuPengaturanIcon.classList.add('rotate-180'); } else { menuPengaturanContent.classList.add('hidden'); menuPengaturanIcon.classList.remove('rotate-180'); }
            });
        }
        const menuLaporanToggle = document.getElementById('menu-laporan-toggle');
        const menuLaporanContent = document.getElementById('menu-laporan-content');
        const menuLaporanIcon = document.getElementById('menu-laporan-icon');
        if (menuLaporanToggle && menuLaporanContent) {
            menuLaporanToggle.addEventListener('click', () => {
                const isHidden = menuLaporanContent.classList.contains('hidden');
                if (isHidden) { menuLaporanContent.classList.remove('hidden'); menuLaporanIcon.classList.add('rotate-180'); } else { menuLaporanContent.classList.add('hidden'); menuLaporanIcon.classList.remove('rotate-180'); }
            });
        }
        const menuSuratToggle = document.getElementById('menu-surat-toggle');
        const menuSuratContent = document.getElementById('menu-surat-content');
        const menuSuratIcon = document.getElementById('menu-surat-icon');
        if (menuSuratToggle && menuSuratContent) {
            menuSuratToggle.addEventListener('click', () => {
                const isHidden = menuSuratContent.classList.contains('hidden');
                if (isHidden) { menuSuratContent.classList.remove('hidden'); menuSuratIcon.classList.add('rotate-180'); } else { menuSuratContent.classList.add('hidden'); menuSuratIcon.classList.remove('rotate-180'); }
            });
        }
        const menuPegawaiToggle = document.getElementById('menu-pegawai-toggle');
        const menuPegawaiContent = document.getElementById('menu-pegawai-content');
        const menuPegawaiIcon = document.getElementById('menu-pegawai-icon');
        if (menuPegawaiToggle && menuPegawaiContent) {
            menuPegawaiToggle.addEventListener('click', () => {
                const isHidden = menuPegawaiContent.classList.contains('hidden');
                if (isHidden) { menuPegawaiContent.classList.remove('hidden'); menuPegawaiIcon.classList.add('rotate-180'); } else { menuPegawaiContent.classList.add('hidden'); menuPegawaiIcon.classList.remove('rotate-180'); }
            });
        }
        const menuIuranKorpriToggle = document.getElementById('menu-iurankorpri-toggle');
        const menuIuranKorpriContent = document.getElementById('menu-iurankorpri-content');
        const menuIuranKorpriIcon = document.getElementById('menu-iurankorpri-icon');
        if (menuIuranKorpriToggle && menuIuranKorpriContent) {
            menuIuranKorpriToggle.addEventListener('click', () => {
                const isHidden = menuIuranKorpriContent.classList.contains('hidden');
                if (isHidden) { menuIuranKorpriContent.classList.remove('hidden'); menuIuranKorpriIcon.classList.add('rotate-180'); } else { menuIuranKorpriContent.classList.add('hidden'); menuIuranKorpriIcon.classList.remove('rotate-180'); }
            });
        }
        // SIPUT: Usul SLKS dropdown toggle
        const menuSiputUsulToggle = document.getElementById('menu-siput-usul-toggle');
        const menuSiputUsulContent = document.getElementById('menu-siput-usul-content');
        const menuSiputUsulIcon = document.getElementById('menu-siput-usul-icon');
        if (menuSiputUsulToggle && menuSiputUsulContent) {
            menuSiputUsulToggle.addEventListener('click', () => {
                const isHidden = menuSiputUsulContent.classList.contains('hidden');
                if (isHidden) { menuSiputUsulContent.classList.remove('hidden'); menuSiputUsulIcon.classList.add('rotate-180'); } else { menuSiputUsulContent.classList.add('hidden'); menuSiputUsulIcon.classList.remove('rotate-180'); }
            });
        }
        // SIPUT: Laporan dropdown toggle
        const menuSiputLaporanToggle = document.getElementById('menu-siput-laporan-toggle');
        const menuSiputLaporanContent = document.getElementById('menu-siput-laporan-content');
        const menuSiputLaporanIcon = document.getElementById('menu-siput-laporan-icon');
        if (menuSiputLaporanToggle && menuSiputLaporanContent) {
            menuSiputLaporanToggle.addEventListener('click', () => {
                const isHidden = menuSiputLaporanContent.classList.contains('hidden');
                if (isHidden) { menuSiputLaporanContent.classList.remove('hidden'); menuSiputLaporanIcon.classList.add('rotate-180'); } else { menuSiputLaporanContent.classList.add('hidden'); menuSiputLaporanIcon.classList.remove('rotate-180'); }
            });
        }
        function debounce(func, wait) { let timeout; return function (...args) { clearTimeout(timeout); timeout = setTimeout(() => func.apply(this, args), wait); }; }
        // Wrap in IIFE to avoid variable name conflicts
        (function () {
            const employeeSearchInput = document.getElementById('employee-search');
            if (employeeSearchInput) {
                employeeSearchInput.addEventListener('input', debounce(function (e) {
                    const searchTerm = e.target.value;
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('search', searchTerm);
                    currentUrl.searchParams.set('page', 1);
                    fetch(currentUrl.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(response => response.text()).then(html => { document.getElementById('employee-table-container').innerHTML = html; }).catch(error => console.error('Error searching:', error));
                }, 300));
            }
        })();
        document.addEventListener('click', function (e) {
            const link = e.target.closest('#employee-table-container .pagination a, #employee-table-container nav[role="navigation"] a');
            if (link) { e.preventDefault(); const url = new URL(link.getAttribute('href')); const currentSearch = document.getElementById('employee-search')?.value; if (currentSearch) { url.searchParams.set('search', currentSearch); } fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(response => response.text()).then(html => { document.getElementById('employee-table-container').innerHTML = html; }).catch(error => console.error('Error loading page:', error)); }
        });
        // Sidebar Theme Toggle Logic
        const themeToggleBtnSidebar = document.getElementById('theme-toggle-sidebar');
        const themeToggleDarkIconSidebar = document.getElementById('theme-toggle-dark-icon-sidebar');
        const themeToggleLightIconSidebar = document.getElementById('theme-toggle-light-icon-sidebar');
        const themeToggleText = document.getElementById('theme-toggle-text');

        function updateSidebarThemeIcons() {
            if (document.documentElement.classList.contains('dark')) {
                themeToggleLightIconSidebar?.classList.remove('hidden');
                themeToggleDarkIconSidebar?.classList.add('hidden');
                if(themeToggleText) themeToggleText.textContent = 'Tema Terang';
            } else {
                themeToggleDarkIconSidebar?.classList.remove('hidden');
                themeToggleLightIconSidebar?.classList.add('hidden');
                if(themeToggleText) themeToggleText.textContent = 'Tema Gelap';
            }
        }

        if (themeToggleBtnSidebar && themeToggleDarkIconSidebar && themeToggleLightIconSidebar) {
            updateSidebarThemeIcons();
            themeToggleBtnSidebar.addEventListener('click', function () {
                if (document.documentElement.classList.contains('dark')) { 
                    document.documentElement.classList.remove('dark'); 
                    localStorage.setItem('color-theme', 'light'); 
                } else { 
                    document.documentElement.classList.add('dark'); 
                    localStorage.setItem('color-theme', 'dark'); 
                }
                updateSidebarThemeIcons();
                if (typeof updateChartTheme === 'function') { updateChartTheme(); }
            });
        }
        const sidebarToggleBtn = document.getElementById('sidebar-toggle');
        const mainSidebar = document.getElementById('main-sidebar');
        const sidebarTexts = document.querySelectorAll('.sidebar-text');
        const toggleIcon = document.getElementById('toggle-icon');
        // Desktop collapse toggle
        if (sidebarToggleBtn && mainSidebar) {
            sidebarToggleBtn.addEventListener('click', () => {
                const isCollapsed = mainSidebar.classList.contains('w-20');
                if (isCollapsed) { mainSidebar.classList.remove('w-20'); mainSidebar.classList.add('w-64'); sidebarTexts.forEach(el => el.classList.remove('hidden')); toggleIcon.classList.remove('rotate-180'); } else { mainSidebar.classList.remove('w-64'); mainSidebar.classList.add('w-20'); sidebarTexts.forEach(el => el.classList.add('hidden')); if (menuStatistikContent && !menuStatistikContent.classList.contains('hidden')) { menuStatistikContent.classList.add('hidden'); menuStatistikIcon.classList.remove('rotate-180'); } if (menuChatbotContent && !menuChatbotContent.classList.contains('hidden')) { menuChatbotContent.classList.add('hidden'); menuChatbotIcon.classList.remove('rotate-180'); } if (menuPengaturanContent && !menuPengaturanContent.classList.contains('hidden')) { menuPengaturanContent.classList.add('hidden'); menuPengaturanIcon.classList.remove('rotate-180'); } if (menuIuranKorpriContent && !menuIuranKorpriContent.classList.contains('hidden')) { menuIuranKorpriContent.classList.add('hidden'); menuIuranKorpriIcon.classList.remove('rotate-180'); } toggleIcon.classList.add('rotate-180'); }
            });
        }
        // Mobile sidebar open/close
        (function () {
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const sidebarOverlay = document.getElementById('sidebar-overlay');
            const sidebarCloseMobile = document.getElementById('sidebar-close-mobile');
            function openMobileSidebar() {
                mainSidebar.classList.remove('-translate-x-full');
                sidebarOverlay.classList.remove('hidden', 'opacity-0');
                requestAnimationFrame(() => sidebarOverlay.classList.add('opacity-100'));
                document.body.classList.add('overflow-hidden');
            }
            function closeMobileSidebar() {
                mainSidebar.classList.add('-translate-x-full');
                sidebarOverlay.classList.remove('opacity-100');
                sidebarOverlay.classList.add('opacity-0');
                setTimeout(() => sidebarOverlay.classList.add('hidden'), 300);
                document.body.classList.remove('overflow-hidden');
            }
            if (mobileMenuBtn) mobileMenuBtn.addEventListener('click', openMobileSidebar);
            if (sidebarCloseMobile) sidebarCloseMobile.addEventListener('click', closeMobileSidebar);
            if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeMobileSidebar);
            // Close mobile sidebar on link click inside it
            if (mainSidebar) {
                mainSidebar.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', () => {
                        if (window.innerWidth < 768) closeMobileSidebar();
                    });
                });
            }
        })();
        // Mobile theme toggle
        (function () {
            const mobileToggle = document.getElementById('theme-toggle-mobile');
            const mobileDarkIcon = document.getElementById('theme-toggle-mobile-dark-icon');
            const mobileLightIcon = document.getElementById('theme-toggle-mobile-light-icon');
            if (mobileToggle && mobileDarkIcon && mobileLightIcon) {
                if (document.documentElement.classList.contains('dark')) { mobileLightIcon.classList.remove('hidden'); } else { mobileDarkIcon.classList.remove('hidden'); }
                mobileToggle.addEventListener('click', function () {
                    mobileDarkIcon.classList.toggle('hidden');
                    mobileLightIcon.classList.toggle('hidden');
                    // Sync with sidebar icons too
                    if (localStorage.getItem('color-theme')) {
                        if (localStorage.getItem('color-theme') === 'light') { document.documentElement.classList.add('dark'); localStorage.setItem('color-theme', 'dark'); }
                        else { document.documentElement.classList.remove('dark'); localStorage.setItem('color-theme', 'light'); }
                    } else {
                        if (document.documentElement.classList.contains('dark')) { document.documentElement.classList.remove('dark'); localStorage.setItem('color-theme', 'light'); }
                        else { document.documentElement.classList.add('dark'); localStorage.setItem('color-theme', 'dark'); }
                    }
                    if(typeof updateSidebarThemeIcons === 'function') updateSidebarThemeIcons();
                    if (typeof updateChartTheme === 'function') updateChartTheme();
                });
            }
        })();
    </script>
    </script>
    
    <!-- Toast Notification -->
    @include('components.toast-notification')

    <!-- Helpdesk Widget (Vanilla JS) -->
    @auth
        @if(auth()->check() && strtolower(auth()->user()->role) !== 'admin')
            @include('partials.chat-widget')
        @endif
    @endauth


    @yield('scripts')
</body>

</html>