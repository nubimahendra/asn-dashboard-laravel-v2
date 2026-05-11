@extends('layouts.app')

@section('sidebar-menu')
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
            <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-900/50 mb-2">
                <a href="{{ route('masn.dashboard') }}"
                    class="block px-4 py-2 text-xs text-gray-600 dark:text-gray-400 hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors {{ request()->routeIs('masn.dashboard') ? 'text-blue-600 dark:text-blue-400 font-semibold bg-slate-50 dark:bg-gray-700' : '' }}">
                    Halaman Utama
                </a>
            </div>
            @if(isset($listOpd))
                <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-900/50">
                    <label class="sidebar-text block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 px-1">Filter Unit Kerja</label>
                    <div class="relative" id="opd-dropdown">
                        <button type="button" id="dropdown-trigger"
                            class="w-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-sm px-3 py-2 text-left cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all hover:bg-gray-50 dark:hover:bg-gray-600 flex items-center justify-between group">
                            <span class="block truncate text-xs font-medium {{ isset($filterOpd) && $filterOpd ? 'text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-200' }}">
                                {{ $filterOpd ?? 'Pilih Unit Kerja' }}
                            </span>
                            <svg class="h-3 w-3 text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div id="dropdown-menu"
                            class="hidden w-full bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-lg shadow-inner mt-1 max-h-60 flex flex-col overflow-hidden transition-all origin-top opacity-0">
                            <div class="p-2 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 sticky top-0 z-10">
                                <div class="relative">
                                    <input type="text" id="opd-search" placeholder="Cari OPD..."
                                        autocomplete="off"
                                        class="w-full pl-8 pr-3 py-1.5 border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                        <svg class="h-3.5 w-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="overflow-y-auto flex-1 p-1 scrollbar-thin scrollbar-thumb-gray-200 dark:scrollbar-thumb-gray-600" id="opd-list">
                                <a href="?opd=" class="flex items-center px-4 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/50 hover:text-blue-700 dark:hover:text-blue-300 rounded-md transition-colors"><span class="font-semibold text-blue-500 dark:text-blue-400 w-5 text-center mr-2">•</span> Semua Unit Kerja</a>
                                @foreach($listOpd as $opd)
                                    <a href="?opd={{ urlencode($opd) }}"
                                        class="opd-item flex items-center px-4 py-2 text-xs text-gray-600 dark:text-gray-400 hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors group"
                                        data-name="{{ strtolower($opd) }}">
                                        <span class="w-5 mr-2 flex-shrink-0 text-center {{ isset($filterOpd) && $filterOpd == $opd ? 'text-blue-500 dark:text-blue-400' : 'text-transparent group-hover:text-gray-300 dark:group-hover:text-gray-500' }}"><svg class="w-3.5 h-3.5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></span>
                                        <span class="truncate">{{ $opd }}</span>
                                    </a>
                                @endforeach
                                <div id="no-results" class="hidden px-4 py-4 text-center text-xs text-gray-400 dark:text-gray-500 italic">Tidak ada OPD ditemukan</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</nav>

<nav class="space-y-1 mt-2">
    <div>
        <button type="button" id="menu-pegawai-toggle"
            class="w-full flex items-center justify-between px-2 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition-colors group">
            <div class="flex items-center">
                <svg class="mr-3 h-6 w-6 text-gray-500 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-gray-300 flex-shrink-0 transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span class="sidebar-text truncate">Data Pegawai</span>
            </div>
            <svg id="menu-pegawai-icon"
                class="sidebar-text h-4 w-4 text-gray-400 transform transition-transform duration-200"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div id="menu-pegawai-content" class="hidden mt-2 space-y-2 pl-2 md:pl-0">
            <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-900/50">
                <a href="{{ route('masn.pegawai.import.index') }}"
                    class="block px-4 py-2 text-xs text-gray-600 dark:text-gray-400 hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors {{ request()->routeIs('masn.pegawai.import.*') ? 'text-blue-600 dark:text-blue-400 font-semibold bg-slate-50 dark:bg-gray-700' : '' }}">
                    Master Pegawai
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
                <a href="{{ route('masn.snapshot.index') }}"
                    class="block px-4 py-2 text-xs text-gray-600 dark:text-gray-400 hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors {{ request()->routeIs('masn.snapshot.index') ? 'text-blue-600 dark:text-blue-400 font-semibold bg-slate-50 dark:bg-gray-700' : '' }}">
                    Snapshot Data
                </a>
                <a href="#"
                    class="block px-4 py-2 text-xs text-gray-600 dark:text-gray-400 hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">
                    Profil Pegawai
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
                <a href="{{ route('masn.users.index') }}"
                    class="block px-4 py-2 text-xs text-gray-600 dark:text-gray-400 hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors {{ request()->routeIs('masn.users.*') ? 'text-blue-600 dark:text-blue-400 font-semibold bg-slate-50 dark:bg-gray-700' : '' }}">
                    User
                </a>
                <a href="{{ route('masn.sync.index') }}"
                    class="block px-4 py-2 text-xs text-gray-600 dark:text-gray-400 hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors {{ request()->routeIs('masn.sync.*') ? 'text-blue-600 dark:text-blue-400 font-semibold bg-slate-50 dark:bg-gray-700' : '' }}">
                    Sync Data
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
