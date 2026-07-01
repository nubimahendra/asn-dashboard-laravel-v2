@extends('layouts.siput')

@section('content')
<div class="container mx-auto px-10 py-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Dashboard SIPUT</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">
                Sistem Input & Pengusulan Tanda Kehormatan Satyalancana Karya Satya
            </p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-gray-500">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Draft Usulan</p>
            <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
                {{ number_format($totalDraft) }}
            </h2>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Usulan Diajukan</p>
            <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
                {{ number_format($totalDiajukan) }}
            </h2>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-emerald-500">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Usulan Disetujui</p>
            <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
                {{ number_format($totalDisetujui) }}
            </h2>
        </div>
        <div class="bg-gradient-to-r from-emerald-500 to-teal-500 dark:from-emerald-700 dark:to-teal-700 rounded-xl shadow-lg p-6">
            <p class="text-sm text-emerald-100 mb-1">Total Pegawai Aktif</p>
            <h2 class="text-2xl lg:text-3xl font-bold text-white truncate">
                {{ number_format($totalPns + $totalPppk + $totalPppkPw) }}
            </h2>
        </div>
    </div>

    <!-- Breakdown Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Pegawai Negeri Sipil (PNS)</p>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                        {{ number_format($totalPns) }}
                    </h2>
                </div>
                <div class="p-3 bg-blue-100 text-blue-600 rounded-lg dark:bg-blue-900/50 dark:text-blue-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Pegawai PPPK</p>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                        {{ number_format($totalPppk) }}
                    </h2>
                </div>
                <div class="p-3 bg-purple-100 text-purple-600 rounded-lg dark:bg-purple-900/50 dark:text-purple-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">PPPK Paruh Waktu</p>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                        {{ number_format($totalPppkPw) }}
                    </h2>
                </div>
                <div class="p-3 bg-orange-100 text-orange-600 rounded-lg dark:bg-orange-900/50 dark:text-orange-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden border border-gray-100 dark:border-gray-700">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-lg font-bold text-gray-800 dark:text-white">Layanan SIPUT</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-100 dark:divide-gray-700">
            <a href="{{ route('siput.usul-slks.index') }}" class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h3 class="text-base font-bold text-gray-800 dark:text-white mb-2 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">Input Data Usulan</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Buat usulan SLKS baru berdasarkan rekomendasi masa kerja otomatis.</p>
            </a>
            
            <a href="{{ route('siput.usul-slks.approve') }}" class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-base font-bold text-gray-800 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Approve Usulan</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Verifikasi dan kelola persetujuan usulan SLKS beserta nomor Keppres.</p>
            </a>
        </div>
    </div>
</div>
@endsection
