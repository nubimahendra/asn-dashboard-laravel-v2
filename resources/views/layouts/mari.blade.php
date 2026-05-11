@extends('layouts.app')

@section('sidebar-menu')
<nav class="space-y-1 mb-2">
    <div>
        <a href="{{ route('mari.dashboard') }}"
            class="w-full flex items-center px-2 py-2 text-sm font-medium rounded-lg transition-colors group {{ request()->routeIs('mari.dashboard') ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/50 dark:text-blue-400' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            <svg class="mr-3 h-6 w-6 flex-shrink-0 transition-colors {{ request()->routeIs('mari.dashboard') ? 'text-blue-700 dark:text-blue-400' : 'text-gray-500 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-gray-300' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="sidebar-text truncate">Dashboard</span>
        </a>
    </div>
</nav>

<nav class="space-y-1">
    <div>
        <button type="button" id="menu-iurankorpri-toggle"
            class="w-full flex items-center justify-between px-2 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition-colors group">
            <div class="flex items-center">
                <svg class="mr-3 h-6 w-6 text-gray-500 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-gray-300 flex-shrink-0 transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <span class="sidebar-text truncate">Iuran Korpri</span>
            </div>
            <svg id="menu-iurankorpri-icon"
                class="sidebar-text h-4 w-4 text-gray-400 transform transition-transform duration-200"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div id="menu-iurankorpri-content" class="hidden mt-2 space-y-2 pl-2 md:pl-0">
            <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-900/50">
                <a href="{{ route('mari.iuran-korpri.index') }}"
                    class="block px-4 py-2 text-xs text-gray-600 dark:text-gray-400 hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors {{ request()->routeIs('mari.iuran-korpri.index') ? 'text-blue-600 dark:text-blue-400 font-semibold bg-slate-50 dark:bg-gray-700' : '' }}">
                    Laporan Iuran (Golongan)
                </a>
                <a href="{{ route('mari.iuran-kelas-jabatan.index') }}"
                    class="block px-4 py-2 text-xs text-gray-600 dark:text-gray-400 hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors {{ request()->routeIs('mari.iuran-kelas-jabatan.*') ? 'text-blue-600 dark:text-blue-400 font-semibold bg-slate-50 dark:bg-gray-700' : '' }}">
                    Iuran Kelas Jabatan
                </a>
            </div>
        </div>
    </div>
</nav>

@if(auth()->user()->role === 'admin' || strtolower(auth()->user()->role) === 'admin')
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
                <span class="sidebar-text truncate">Pengaturan Tarif</span>
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
                <div class="mt-2 mb-1 px-4 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">
                    Data Master Tarif</div>
                <a href="{{ route('mari.kelas-jabatan-perbup.index') }}"
                    class="block px-4 py-2 text-xs text-gray-600 dark:text-gray-400 hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors {{ request()->routeIs('mari.kelas-jabatan-perbup.*') ? 'text-blue-600 dark:text-blue-400 font-semibold bg-slate-50 dark:bg-gray-700' : '' }}">
                    Master Kelas Perbup
                </a>
                <a href="{{ route('mari.jabatan-mapping.index') }}"
                    class="block px-4 py-2 text-xs text-gray-600 dark:text-gray-400 hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors {{ request()->routeIs('mari.jabatan-mapping.*') ? 'text-blue-600 dark:text-blue-400 font-semibold bg-slate-50 dark:bg-gray-700' : '' }}">
                    Mapping Jabatan
                </a>
                <a href="{{ route('mari.jabatan-default.index') }}"
                    class="block px-4 py-2 text-xs text-gray-600 dark:text-gray-400 hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors {{ request()->routeIs('mari.jabatan-default.*') ? 'text-blue-600 dark:text-blue-400 font-semibold bg-slate-50 dark:bg-gray-700' : '' }}">
                    Kelas Jabatan Default
                </a>
            </div>
        </div>
    </div>
</nav>
@endif

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
