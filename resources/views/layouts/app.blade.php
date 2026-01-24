<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

<body class="bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors duration-300">
    <div id="wrapper" class="flex w-full items-stretch gap-0 md:gap-0 lg:gap-0">
        <aside id="main-sidebar"
            class="w-64 bg-white dark:bg-gray-800 shadow-md flex-col h-screen sticky top-0 z-20 flex-shrink-0 border-r border-gray-100 dark:border-gray-700 hidden md:flex transition-all duration-300">
            <div
                class="p-6 border-b border-gray-100 dark:border-gray-700 flex-shrink-0 flex items-center justify-between">
                <a href="{{ route('dashboard') }}"
                    class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2 whitespace-nowrap overflow-hidden transition-all duration-300 hover:text-blue-600 dark:hover:text-blue-400">
                    <svg class="w-6 h-6 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                        </path>
                    </svg>
                    <span class="sidebar-text">DASHBOARD</span>
                </a>
                <button id="sidebar-toggle"
                    class="p-1 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 focus:outline-none ml-auto">
                    <svg id="toggle-icon" class="w-6 h-6 transform transition-transform duration-300" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto py-4 px-3 space-y-4">
                <nav class="space-y-1">
                    <div>
                        <button type="button" id="menu-statistik-toggle"
                            class="w-full flex items-center justify-between px-2 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition-colors group">
                            <div class="flex items-center">
                                <svg class="mr-3 h-6 w-6 text-gray-500 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-gray-300 flex-shrink-0 transition-colors"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <span class="sidebar-text truncate">Statistik</span>
                            </div>
                            <svg id="menu-statistik-icon"
                                class="sidebar-text h-4 w-4 text-gray-400 transform transition-transform duration-200"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="menu-statistik-content" class="hidden mt-2 space-y-2 pl-2 md:pl-0">
                            <!-- Dashboard Link -->
                            <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-900/50 mb-2">
                                <a href="{{ route('dashboard') }}"
                                    class="block px-4 py-2 text-xs text-gray-600 dark:text-gray-400 hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">
                                    Halaman Utama
                                </a>
                            </div>
                            <!-- Filter Unit Kerja -->
                            @if(isset($listOpd))
                                <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-900/50">
                                    <label
                                        class="sidebar-text block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 px-1">Filter
                                        Unit Kerja</label>
                                    <div class="relative" id="opd-dropdown">
                                        <button type="button" id="dropdown-trigger"
                                            class="w-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-sm px-3 py-2 text-left cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all hover:bg-gray-50 dark:hover:bg-gray-600 flex items-center justify-between group">
                                            <span
                                                class="block truncate text-xs font-medium {{ isset($filterOpd) && $filterOpd ? 'text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-200' }}">
                                                {{ $filterOpd ?? 'Pilih Unit Kerja' }}
                                            </span>
                                            <svg class="h-3 w-3 text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                        <div id="dropdown-menu"
                                            class="hidden w-full bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-lg shadow-inner mt-1 max-h-60 flex flex-col overflow-hidden transition-all origin-top opacity-0">
                                            <div
                                                class="p-2 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 sticky top-0 z-10">
                                                <div class="relative">
                                                    <input type="text" id="opd-search" placeholder="Cari OPD..."
                                                        autocomplete="off"
                                                        class="w-full pl-8 pr-3 py-1.5 border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                                    <div
                                                        class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                                        <svg class="h-3.5 w-3.5 text-gray-400" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="overflow-y-auto flex-1 p-1 scrollbar-thin scrollbar-thumb-gray-200 dark:scrollbar-thumb-gray-600"
                                                id="opd-list">
                                                <a href="/"
                                                    class="flex items-center px-4 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/50 hover:text-blue-700 dark:hover:text-blue-300 rounded-md transition-colors"><span
                                                        class="font-semibold text-blue-500 dark:text-blue-400 w-5 text-center mr-2">•</span>
                                                    Semua Unit Kerja</a>
                                                @foreach($listOpd as $opd)
                                                    <a href="?opd={{ urlencode($opd) }}"
                                                        class="opd-item flex items-center px-4 py-2 text-xs text-gray-600 dark:text-gray-400 hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors group"
                                                        data-name="{{ strtolower($opd) }}">
                                                        <span
                                                            class="w-5 mr-2 flex-shrink-0 text-center {{ isset($filterOpd) && $filterOpd == $opd ? 'text-blue-500 dark:text-blue-400' : 'text-transparent group-hover:text-gray-300 dark:group-hover:text-gray-500' }}"><svg
                                                                class="w-3.5 h-3.5 mx-auto" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg></span>
                                                        <span class="truncate">{{ $opd }}</span>
                                                    </a>
                                                @endforeach
                                                <div id="no-results"
                                                    class="hidden px-4 py-4 text-center text-xs text-gray-400 dark:text-gray-500 italic">
                                                    Tidak ada OPD ditemukan</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </nav>

                @if(auth()->user()->role === 'admin')
                    <hr class="my-2 border-dashed border-gray-200 dark:border-gray-700 mx-2">

                    <!-- Chatbot Menu -->
                    <nav class="space-y-1">
                        <div>
                            <button type="button" id="menu-chatbot-toggle"
                                class="w-full flex items-center justify-between px-2 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition-colors group">
                                <div class="flex items-center">
                                    <svg class="mr-3 h-6 w-6 text-gray-500 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-gray-300 flex-shrink-0 transition-colors"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                    </svg>
                                    <span class="sidebar-text truncate">Chatbot</span>
                                </div>
                                <svg id="menu-chatbot-icon"
                                    class="sidebar-text h-4 w-4 text-gray-400 transform transition-transform duration-200"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="menu-chatbot-content" class="hidden mt-2 space-y-2 pl-2 md:pl-0">
                                <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-900/50">
                                    <a href="#"
                                        class="block px-4 py-2 text-xs text-gray-600 dark:text-gray-400 hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">Perangkat</a>
                                    <a href="{{ route('admin.chat.messages.index') }}"
                                        class="block px-4 py-2 text-xs text-gray-600 dark:text-gray-400 hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">Pesan</a>
                                    <a href="#"
                                        class="block px-4 py-2 text-xs text-gray-600 dark:text-gray-400 hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">Grup</a>
                                    <a href="{{ route('admin.chat.contacts.index') }}"
                                        class="block px-4 py-2 text-xs text-gray-600 dark:text-gray-400 hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">Kontak</a>
                                    <a href="{{ route('admin.chat.faqs.index') }}"
                                        class="block px-4 py-2 text-xs text-gray-600 dark:text-gray-400 hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">FAQ</a>
                                    <a href="{{ route('admin.chat.api.index') }}"
                                        class="block px-4 py-2 text-xs text-gray-600 dark:text-gray-400 hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">Api
                                        Fonnte</a>
                                </div>
                            </div>
                        </div>
                    </nav>

                    <!-- Laporan Menu -->
                    <nav class="space-y-1 mt-2">
                        <div>
                            <button type="button" id="menu-laporan-toggle"
                                class="w-full flex items-center justify-between px-2 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition-colors group">
                                <div class="flex items-center">
                                    <svg class="mr-3 h-6 w-6 text-gray-500 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-gray-300 flex-shrink-0 transition-colors"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="sidebar-text truncate">Laporan</span>
                                </div>
                                <svg id="menu-laporan-icon"
                                    class="sidebar-text h-4 w-4 text-gray-400 transform transition-transform duration-200"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="menu-laporan-content" class="hidden mt-2 space-y-2 pl-2 md:pl-0">
                                <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-900/50">
                                    <a href="#"
                                        class="block px-4 py-2 text-xs text-gray-600 dark:text-gray-400 hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">Profil
                                        Pegawai</a>
                                    <a href="#"
                                        class="block px-4 py-2 text-xs text-gray-600 dark:text-gray-400 hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">Iuran
                                        Korpri</a>
                                    <a href="{{ route('admin.pengajuan-cerai.index') }}"
                                        class="block px-4 py-2 text-xs text-gray-600 dark:text-gray-400 hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">Pengajuan
                                        Cerai</a>
                                </div>
                            </div>
                        </div>
                    </nav>

                    <!-- Surat Menu -->
                    <nav class="space-y-1 mt-2">
                        <div>
                            <button type="button" id="menu-surat-toggle"
                                class="w-full flex items-center justify-between px-2 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition-colors group">
                                <div class="flex items-center">
                                    <svg class="mr-3 h-6 w-6 text-gray-500 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-gray-300 flex-shrink-0 transition-colors"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <span class="sidebar-text truncate">Surat</span>
                                </div>
                                <svg id="menu-surat-icon"
                                    class="sidebar-text h-4 w-4 text-gray-400 transform transition-transform duration-200"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="menu-surat-content" class="hidden mt-2 space-y-2 pl-2 md:pl-0">
                                <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-900/50">
                                    <a href="{{ route('surat-masuk.index') }}"
                                        class="block px-4 py-2 text-xs text-gray-600 dark:text-gray-400 hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">Inbox</a>
                                </div>
                            </div>
                        </div>
                    </nav>

                    <!-- Pengaturan Menu -->
                    <nav class="space-y-1 mt-2">
                        <div>
                            <button type="button" id="menu-pengaturan-toggle"
                                class="w-full flex items-center justify-between px-2 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition-colors group">
                                <div class="flex items-center">
                                    <svg class="mr-3 h-6 w-6 text-gray-500 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-gray-300 flex-shrink-0 transition-colors"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="sidebar-text truncate">Pengaturan</span>
                                </div>
                                <svg id="menu-pengaturan-icon"
                                    class="sidebar-text h-4 w-4 text-gray-400 transform transition-transform duration-200"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="menu-pengaturan-content" class="hidden mt-2 space-y-2 pl-2 md:pl-0">
                                <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-900/50">
                                    <a href="{{ route('users.index') }}"
                                        class="block px-4 py-2 text-xs text-gray-600 dark:text-gray-400 hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">User</a>
                                    <a href="{{ route('sync.index') }}"
                                        class="block px-4 py-2 text-xs text-gray-600 dark:text-gray-400 hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">Sync
                                        Data</a>
                                </div>
                            </div>
                        </div>
                    </nav>
                @endif
            </div>
            @if(auth()->user()->role === 'admin')
                <div class="p-4 border-t border-gray-100 dark:border-gray-700">

                </div>
            @endif

            <!-- Logout Button (Sidebar Footer) -->
            <div class="p-4 border-t border-gray-100 dark:border-gray-700 mt-auto">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-2 px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-red-600 dark:hover:text-red-400 rounded-lg transition-colors group">
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

        <main id="main-content"
            class="flex-1 bg-gray-50 dark:bg-gray-900 min-h-screen transition-all duration-300 w-full">
            <div class="px-6 py-4">
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                        role="alert">
                        <strong class="font-bold">Berhasil!</strong>
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif
            </div>
            @yield('content')
        </main>

    </div>
    <script>
        const dropdownTrigger = document.getElementById('dropdown-trigger');
        const dropdownMenu = document.getElementById('dropdown-menu');
        const searchInput = document.getElementById('opd-search');
        const opdList = document.getElementById('opd-list');
        const opdItems = opdList ? opdList.getElementsByClassName('opd-item') : [];
        const noResults = document.getElementById('no-results');
        if (dropdownTrigger && dropdownMenu) {
            dropdownTrigger.addEventListener('click', (e) => {
                e.stopPropagation();
                const isHidden = dropdownMenu.classList.contains('hidden');
                if (isHidden) { dropdownMenu.classList.remove('hidden'); requestAnimationFrame(() => { dropdownMenu.classList.remove('scale-95', 'opacity-0'); dropdownMenu.classList.add('scale-100', 'opacity-100'); }); if (searchInput) setTimeout(() => searchInput.focus(), 100); }
                else { closeDropdown(); }
            });
            document.addEventListener('click', (e) => { if (!dropdownMenu.contains(e.target) && !dropdownTrigger.contains(e.target)) { closeDropdown(); } });
        }
        function closeDropdown() { if (!dropdownMenu) return; dropdownMenu.classList.remove('scale-100', 'opacity-100'); dropdownMenu.classList.add('scale-95', 'opacity-0'); setTimeout(() => { dropdownMenu.classList.add('hidden'); }, 200); }
        if (searchInput) {
            searchInput.addEventListener('click', (e) => e.stopPropagation());
            searchInput.addEventListener('keyup', function (e) {
                const term = e.target.value.toLowerCase();
                let hasResults = false;
                Array.from(opdItems).forEach(item => { const name = item.getAttribute('data-name'); if (name.includes(term)) { item.classList.remove('hidden'); hasResults = true; } else { item.classList.add('hidden'); } });
                if (noResults) { noResults.classList.toggle('hidden', hasResults); }
            });
        }
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
        function debounce(func, wait) { let timeout; return function (...args) { clearTimeout(timeout); timeout = setTimeout(() => func.apply(this, args), wait); }; }
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
        document.addEventListener('click', function (e) {
            const link = e.target.closest('#employee-table-container .pagination a, #employee-table-container nav[role="navigation"] a');
            if (link) { e.preventDefault(); const url = new URL(link.getAttribute('href')); const currentSearch = document.getElementById('employee-search')?.value; if (currentSearch) { url.searchParams.set('search', currentSearch); } fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(response => response.text()).then(html => { document.getElementById('employee-table-container').innerHTML = html; }).catch(error => console.error('Error loading page:', error)); }
        });
        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        if (themeToggleBtn && themeToggleDarkIcon && themeToggleLightIcon) {
            if (document.documentElement.classList.contains('dark')) { themeToggleLightIcon.classList.remove('hidden'); } else { themeToggleDarkIcon.classList.remove('hidden'); }
            themeToggleBtn.addEventListener('click', function () {
                themeToggleDarkIcon.classList.toggle('hidden');
                themeToggleLightIcon.classList.toggle('hidden');
                if (localStorage.getItem('color-theme')) { if (localStorage.getItem('color-theme') === 'light') { document.documentElement.classList.add('dark'); localStorage.setItem('color-theme', 'dark'); } else { document.documentElement.classList.remove('dark'); localStorage.setItem('color-theme', 'light'); } } else { if (document.documentElement.classList.contains('dark')) { document.documentElement.classList.remove('dark'); localStorage.setItem('color-theme', 'light'); } else { document.documentElement.classList.add('dark'); localStorage.setItem('color-theme', 'dark'); } }
                if (typeof updateChartTheme === 'function') { updateChartTheme(); }
            });
        }
        const sidebarToggleBtn = document.getElementById('sidebar-toggle');
        const mainSidebar = document.getElementById('main-sidebar');
        const sidebarTexts = document.querySelectorAll('.sidebar-text');
        const toggleIcon = document.getElementById('toggle-icon');
        if (sidebarToggleBtn && mainSidebar) {
            sidebarToggleBtn.addEventListener('click', () => {
                const isCollapsed = mainSidebar.classList.contains('w-20');
                if (isCollapsed) { mainSidebar.classList.remove('w-20'); mainSidebar.classList.add('w-64'); sidebarTexts.forEach(el => el.classList.remove('hidden')); toggleIcon.classList.remove('rotate-180'); } else { mainSidebar.classList.remove('w-64'); mainSidebar.classList.add('w-20'); sidebarTexts.forEach(el => el.classList.add('hidden')); if (menuStatistikContent && !menuStatistikContent.classList.contains('hidden')) { menuStatistikContent.classList.add('hidden'); menuStatistikIcon.classList.remove('rotate-180'); } if (menuChatbotContent && !menuChatbotContent.classList.contains('hidden')) { menuChatbotContent.classList.add('hidden'); menuChatbotIcon.classList.remove('rotate-180'); } if (menuPengaturanContent && !menuPengaturanContent.classList.contains('hidden')) { menuPengaturanContent.classList.add('hidden'); menuPengaturanIcon.classList.remove('rotate-180'); } toggleIcon.classList.add('rotate-180'); }
            });
        }
    </script>
    <script>
        // Auto-hide alerts after 3 seconds
        setTimeout(function () {
            let alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(function (alert) {
                alert.style.transition = "opacity 0.5s ease";
                alert.style.opacity = "0";
                setTimeout(() => alert.remove(), 500);
            });
        }, 3000);
    </script>
    @yield('scripts')
</body>

</html>