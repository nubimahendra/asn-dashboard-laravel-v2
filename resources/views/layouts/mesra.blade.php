@extends('layouts.app')

@section('sidebar-menu')
<nav class="space-y-1 mb-2">
    <div>
        <a href="{{ route('mesra.dashboard') }}"
            class="w-full flex items-center px-2 py-2 text-sm font-medium rounded-lg transition-colors group {{ request()->routeIs('mesra.dashboard') ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/50 dark:text-blue-400' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            <svg class="mr-3 h-6 w-6 flex-shrink-0 transition-colors {{ request()->routeIs('mesra.dashboard') ? 'text-blue-700 dark:text-blue-400' : 'text-gray-500 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-gray-300' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="sidebar-text truncate">Dashboard</span>
        </a>
    </div>
</nav>

<nav class="space-y-1">
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
                <a href="{{ route('mesra.surat-masuk.index') }}"
                    class="block px-4 py-2 text-xs text-gray-600 dark:text-gray-400 hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors {{ request()->routeIs('mesra.surat-masuk.*') ? 'text-blue-600 dark:text-blue-400 font-semibold bg-slate-50 dark:bg-gray-700' : '' }}">
                    Inbox
                </a>
            </div>
        </div>
    </div>
</nav>

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
                <a href="{{ route('mesra.pengajuan-cerai.index') }}"
                    class="block px-4 py-2 text-xs text-gray-600 dark:text-gray-400 hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors {{ request()->routeIs('mesra.pengajuan-cerai.*') ? 'text-blue-600 dark:text-blue-400 font-semibold bg-slate-50 dark:bg-gray-700' : '' }}">
                    Pengajuan Cerai
                </a>
            </div>
        </div>
    </div>
</nav>

<nav class="space-y-1 mt-2">
    <div>
        <button type="button" id="menu-chatbot-toggle"
            class="w-full flex items-center justify-between px-2 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition-colors group">
            <div class="flex items-center">
                <svg class="mr-3 h-6 w-6 text-gray-500 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-gray-300 flex-shrink-0 transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                <span class="sidebar-text truncate">Helpdesk</span>
            </div>
            <!-- Badge Replaced by JS -->
            <span id="chat-badge"
                class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full hidden">0</span>
            <svg id="menu-chatbot-icon"
                class="sidebar-text h-4 w-4 text-gray-400 transform transition-transform duration-200 ml-2"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div id="menu-chatbot-content" class="hidden mt-2 space-y-2 pl-2 md:pl-0">
            <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-900/50">
                <a href="{{ route('mesra.chat.index') }}"
                    class="block px-4 py-2 text-xs text-gray-600 dark:text-gray-400 hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors {{ request()->routeIs('mesra.chat.index') ? 'text-blue-600 dark:text-blue-400 font-semibold bg-slate-50 dark:bg-gray-700' : '' }}">
                    Chat
                </a>
            </div>
        </div>
    </div>
</nav>

<hr class="my-4 border-dashed border-gray-200 dark:border-gray-700 mx-2">

<div class="px-2">
    <a href="{{ route('hub') }}" class="w-full flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
        <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        <span class="sidebar-text">Kembali ke Hub</span>
    </a>
</div>
@endsection
