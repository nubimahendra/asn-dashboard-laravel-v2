@extends('layouts.app')

@section('sidebar-menu')
<nav class="space-y-1 mb-2">
    <div>
        <a href="{{ route('siput.dashboard') }}"
            class="w-full flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors group {{ request()->routeIs('siput.dashboard') ? 'bg-indigo-50/80 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400' : 'text-slate-600 dark:text-zinc-300 hover:bg-slate-100/70 dark:hover:bg-zinc-800/50' }}">
            <svg class="mr-3 h-6 w-6 flex-shrink-0 transition-colors {{ request()->routeIs('siput.dashboard') ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 group-hover:text-slate-700 dark:text-zinc-500 dark:group-hover:text-zinc-300' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="sidebar-text truncate">Dashboard SLKS</span>
        </a>
    </div>
</nav>

<nav class="space-y-1">
    <div>
        <button type="button" id="menu-siput-usul-toggle"
            class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium text-slate-600 dark:text-zinc-300 rounded-lg hover:bg-slate-100/70 dark:hover:bg-zinc-800/50 focus:outline-none transition-colors group">
            <div class="flex items-center">
                <svg class="mr-3 h-6 w-6 text-slate-400 group-hover:text-slate-700 dark:text-zinc-500 dark:group-hover:text-zinc-300 flex-shrink-0 transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span class="sidebar-text truncate">Usul SLKS</span>
            </div>
            <svg id="menu-siput-usul-icon"
                class="sidebar-text h-4 w-4 text-slate-400 dark:text-zinc-500 transform transition-transform duration-200 {{ request()->routeIs('siput.usul-slks.*') ? 'rotate-180' : '' }}"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div id="menu-siput-usul-content" class="{{ request()->routeIs('siput.usul-slks.*') ? '' : 'hidden' }} mt-2 space-y-2 pl-2 md:pl-0">
            <div class="p-2.5 rounded-xl bg-slate-50/80 dark:bg-zinc-800/30">
                <a href="{{ route('siput.usul-slks.index') }}"
                    class="block px-4 py-2.5 text-xs text-slate-600 dark:text-zinc-300 hover:bg-indigo-50/60 dark:hover:bg-indigo-500/10 hover:text-indigo-600 hover:translate-x-0.5 rounded-md transition-all duration-200 {{ request()->routeIs('siput.usul-slks.index') || request()->routeIs('siput.usul-slks.edit') ? 'text-indigo-600 dark:text-indigo-400 font-semibold bg-indigo-50/60 dark:bg-indigo-500/10' : '' }}">
                    Input Data Usul
                </a>
                <a href="{{ route('siput.usul-slks.approve') }}"
                    class="block px-4 py-2.5 text-xs text-slate-600 dark:text-zinc-300 hover:bg-indigo-50/60 dark:hover:bg-indigo-500/10 hover:text-indigo-600 hover:translate-x-0.5 rounded-md transition-all duration-200 {{ request()->routeIs('siput.usul-slks.approve') ? 'text-indigo-600 dark:text-indigo-400 font-semibold bg-indigo-50/60 dark:bg-indigo-500/10' : '' }}">
                    Approve
                </a>
            </div>
        </div>
    </div>
</nav>

<nav class="space-y-1 mt-2">
    <div>
        <button type="button" id="menu-siput-laporan-toggle"
            class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium text-slate-600 dark:text-zinc-300 rounded-lg hover:bg-slate-100/70 dark:hover:bg-zinc-800/50 focus:outline-none transition-colors group">
            <div class="flex items-center">
                <svg class="mr-3 h-6 w-6 text-slate-400 group-hover:text-slate-700 dark:text-zinc-500 dark:group-hover:text-zinc-300 flex-shrink-0 transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span class="sidebar-text truncate">Laporan</span>
            </div>
            <svg id="menu-siput-laporan-icon"
                class="sidebar-text h-4 w-4 text-slate-400 dark:text-zinc-500 transform transition-transform duration-200 {{ request()->routeIs('siput.usul-slks.manage') ? 'rotate-180' : '' }}"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div id="menu-siput-laporan-content" class="{{ request()->routeIs('siput.usul-slks.manage') ? '' : 'hidden' }} mt-2 space-y-2 pl-2 md:pl-0">
            <div class="p-2.5 rounded-xl bg-slate-50/80 dark:bg-zinc-800/30">
                <a href="{{ route('siput.usul-slks.manage') }}"
                    class="block px-4 py-2.5 text-xs text-slate-600 dark:text-zinc-300 hover:bg-indigo-50/60 dark:hover:bg-indigo-500/10 hover:text-indigo-600 hover:translate-x-0.5 rounded-md transition-all duration-200 {{ request()->routeIs('siput.usul-slks.manage') ? 'text-indigo-600 dark:text-indigo-400 font-semibold bg-indigo-50/60 dark:bg-indigo-500/10' : '' }}">
                    Cetak Usulan
                </a>
                <a href="#" onclick="alert('Fitur Cetak Riwayat masih dalam tahap pengembangan.'); return false;"
                    class="block px-4 py-2.5 text-xs text-slate-600 dark:text-zinc-300 hover:bg-indigo-50/60 dark:hover:bg-indigo-500/10 hover:text-indigo-600 hover:translate-x-0.5 rounded-md transition-all duration-200">
                    Cetak Riwayat
                </a>
            </div>
        </div>
    </div>
</nav>

<hr class="my-4 border-slate-200/60 dark:border-zinc-800/40 mx-3">

<div class="px-3">
    <a href="{{ route('hub') }}" class="w-full flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-sm shadow-indigo-600/20 hover:shadow-md hover:shadow-indigo-600/30 transition-all duration-200">
        <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        <span class="sidebar-text">Kembali ke Hub</span>
    </a>
</div>

@endsection
