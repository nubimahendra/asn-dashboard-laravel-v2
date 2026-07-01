@extends('layouts.app')

@section('sidebar-menu')
<nav class="space-y-1 mb-2">
    <div>
        <a href="{{ route('mari.dashboard') }}"
            class="w-full flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors group {{ request()->routeIs('mari.dashboard') ? 'bg-indigo-50/80 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400' : 'text-slate-600 dark:text-zinc-300 hover:bg-slate-100/70 dark:hover:bg-zinc-800/50' }}">
            <svg class="mr-3 h-6 w-6 flex-shrink-0 transition-colors {{ request()->routeIs('mari.dashboard') ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 group-hover:text-slate-700 dark:text-zinc-500 dark:group-hover:text-zinc-300' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="sidebar-text truncate">Dashboard</span>
        </a>
    </div>
</nav>

<nav class="space-y-1">
    <div>
        <button type="button" id="menu-iurankorpri-toggle"
            class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium text-slate-600 dark:text-zinc-300 rounded-lg hover:bg-slate-100/70 dark:hover:bg-zinc-800/50 focus:outline-none transition-colors group">
            <div class="flex items-center">
                <svg class="mr-3 h-6 w-6 text-slate-400 group-hover:text-slate-700 dark:text-zinc-500 dark:group-hover:text-zinc-300 flex-shrink-0 transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <span class="sidebar-text truncate">Iuran Korpri</span>
            </div>
            <svg id="menu-iurankorpri-icon"
                class="sidebar-text h-4 w-4 text-slate-400 dark:text-zinc-500 transform transition-transform duration-200 {{ request()->routeIs('mari.iuran-korpri.*', 'mari.rincian-iuran.*', 'mari.rekon-iuran.*') ? 'rotate-180' : '' }}"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div id="menu-iurankorpri-content" class="{{ request()->routeIs('mari.iuran-korpri.*', 'mari.rincian-iuran.*', 'mari.rekon-iuran.*') ? '' : 'hidden' }} mt-2 space-y-2 pl-2 md:pl-0">
            <div class="p-2.5 rounded-xl bg-slate-50/80 dark:bg-zinc-800/30">
                <a href="{{ route('mari.iuran-korpri.index') }}"
                    class="block px-4 py-2.5 text-xs text-slate-600 dark:text-zinc-300 hover:bg-indigo-50/60 dark:hover:bg-indigo-500/10 hover:text-indigo-600 hover:translate-x-0.5 rounded-md transition-all duration-200 {{ request()->routeIs('mari.iuran-korpri.index') ? 'text-indigo-600 dark:text-indigo-400 font-semibold bg-indigo-50/60 dark:bg-indigo-500/10' : '' }}">
                    Laporan Iuran
                </a>
                <a href="{{ route('mari.rincian-iuran.index') }}"
                    class="block px-4 py-2.5 text-xs text-slate-600 dark:text-zinc-300 hover:bg-indigo-50/60 dark:hover:bg-indigo-500/10 hover:text-indigo-600 hover:translate-x-0.5 rounded-md transition-all duration-200 {{ request()->routeIs('mari.rincian-iuran.index') ? 'text-indigo-600 dark:text-indigo-400 font-semibold bg-indigo-50/60 dark:bg-indigo-500/10' : '' }}">
                    Rincian Iuran
                </a>
                <a href="{{ route('mari.rekon-iuran.index') }}"
                    class="block px-4 py-2.5 text-xs text-slate-600 dark:text-zinc-300 hover:bg-indigo-50/60 dark:hover:bg-indigo-500/10 hover:text-indigo-600 hover:translate-x-0.5 rounded-md transition-all duration-200 {{ request()->routeIs('mari.rekon-iuran.index') ? 'text-indigo-600 dark:text-indigo-400 font-semibold bg-indigo-50/60 dark:bg-indigo-500/10' : '' }}">
                    Rekon Iuran Manual
                </a>
                {{-- <a href="{{ route('mari.iuran-kelas-jabatan.index') }}"
                    class="block px-4 py-2.5 text-xs text-slate-600 dark:text-zinc-300 hover:bg-indigo-50/60 dark:hover:bg-indigo-500/10 hover:text-indigo-600 hover:translate-x-0.5 rounded-md transition-all duration-200 {{ request()->routeIs('mari.iuran-kelas-jabatan.*') ? 'text-indigo-600 dark:text-indigo-400 font-semibold bg-indigo-50/60 dark:bg-indigo-500/10' : '' }}">
                    Iuran Kelas Jabatan
                </a> --}}
            </div>
        </div>
    </div>
</nav>

@if((auth()->user()->role === 'admin' || strtolower(auth()->user()->role) === 'admin') && !auth()->user()->hasPdScope())
<nav class="space-y-1 mt-2">
    <div>
        <button type="button" id="menu-pengaturan-toggle"
            class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium text-slate-600 dark:text-zinc-300 rounded-lg hover:bg-slate-100/70 dark:hover:bg-zinc-800/50 focus:outline-none transition-colors group">
            <div class="flex items-center">
                <svg class="mr-3 h-6 w-6 text-slate-400 group-hover:text-slate-700 dark:text-zinc-500 dark:group-hover:text-zinc-300 flex-shrink-0 transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="sidebar-text truncate">Pengaturan</span>
            </div>
            <svg id="menu-pengaturan-icon"
                class="sidebar-text h-4 w-4 text-slate-400 dark:text-zinc-500 transform transition-transform duration-200 {{ request()->routeIs('mari.pengaturan-tarif.*', 'mari.pengaturan.*', 'mari.kelas-jabatan-perbup.*', 'mari.jabatan-mapping.*', 'mari.jabatan-default.*', 'mari.eselon-mapping.*') ? 'rotate-180' : '' }}"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div id="menu-pengaturan-content" class="{{ request()->routeIs('mari.pengaturan-tarif.*', 'mari.pengaturan.*', 'mari.kelas-jabatan-perbup.*', 'mari.jabatan-mapping.*', 'mari.jabatan-default.*', 'mari.eselon-mapping.*') ? '' : 'hidden' }} mt-2 space-y-2 pl-2 md:pl-0">
            <div class="p-2.5 rounded-xl bg-slate-50/80 dark:bg-zinc-800/30">
                <div class="mt-2 mb-1 px-4 text-[11px] font-medium text-slate-400 dark:text-zinc-500 uppercase tracking-wide">
                    Data Master Tarif</div>
                <a href="{{ route('mari.pengaturan-tarif.iuran-golongan') }}"
                    class="block px-4 py-2.5 text-xs text-slate-600 dark:text-zinc-300 hover:bg-indigo-50/60 dark:hover:bg-indigo-500/10 hover:text-indigo-600 hover:translate-x-0.5 rounded-md transition-all duration-200 {{ request()->routeIs('mari.pengaturan-tarif.iuran-golongan') ? 'text-indigo-600 dark:text-indigo-400 font-semibold bg-indigo-50/60 dark:bg-indigo-500/10' : '' }}">
                    Iuran Golongan
                </a>
                <a href="{{ route('mari.pengaturan.invoice') }}"
                    class="block px-4 py-2.5 text-xs text-slate-600 dark:text-zinc-300 hover:bg-indigo-50/60 dark:hover:bg-indigo-500/10 hover:text-indigo-600 hover:translate-x-0.5 rounded-md transition-all duration-200 {{ request()->routeIs('mari.pengaturan.invoice') ? 'text-indigo-600 dark:text-indigo-400 font-semibold bg-indigo-50/60 dark:bg-indigo-500/10' : '' }}">
                    Pengaturan Invoice
                </a>
                <a href="{{ route('mari.kelas-jabatan-perbup.index') }}"
                    class="block px-4 py-2.5 text-xs text-slate-600 dark:text-zinc-300 hover:bg-indigo-50/60 dark:hover:bg-indigo-500/10 hover:text-indigo-600 hover:translate-x-0.5 rounded-md transition-all duration-200 {{ request()->routeIs('mari.kelas-jabatan-perbup.*') ? 'text-indigo-600 dark:text-indigo-400 font-semibold bg-indigo-50/60 dark:bg-indigo-500/10' : '' }}">
                    Master Kelas Perbup
                </a>
                <a href="{{ route('mari.jabatan-mapping.index') }}"
                    class="block px-4 py-2.5 text-xs text-slate-600 dark:text-zinc-300 hover:bg-indigo-50/60 dark:hover:bg-indigo-500/10 hover:text-indigo-600 hover:translate-x-0.5 rounded-md transition-all duration-200 {{ request()->routeIs('mari.jabatan-mapping.*') ? 'text-indigo-600 dark:text-indigo-400 font-semibold bg-indigo-50/60 dark:bg-indigo-500/10' : '' }}">
                    Mapping Jabatan
                </a>
                <a href="{{ route('mari.jabatan-default.index') }}"
                    class="block px-4 py-2.5 text-xs text-slate-600 dark:text-zinc-300 hover:bg-indigo-50/60 dark:hover:bg-indigo-500/10 hover:text-indigo-600 hover:translate-x-0.5 rounded-md transition-all duration-200 {{ request()->routeIs('mari.jabatan-default.*') ? 'text-indigo-600 dark:text-indigo-400 font-semibold bg-indigo-50/60 dark:bg-indigo-500/10' : '' }}">
                    Kelas Jabatan Default
                </a>
                <a href="{{ route('mari.eselon-mapping.index') }}"
                    class="block px-4 py-2.5 text-xs text-slate-600 dark:text-zinc-300 hover:bg-indigo-50/60 dark:hover:bg-indigo-500/10 hover:text-indigo-600 hover:translate-x-0.5 rounded-md transition-all duration-200 {{ request()->routeIs('mari.eselon-mapping.*') ? 'text-indigo-600 dark:text-indigo-400 font-semibold bg-indigo-50/60 dark:bg-indigo-500/10' : '' }}">
                    Mapping Eselon
                </a>
            </div>
        </div>
    </div>
</nav>
@endif

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
