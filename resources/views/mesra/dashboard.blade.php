@extends('layouts.mesra')

@section('content')
<div class="container mx-auto px-10 py-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Dashboard MESRA</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">
                Manajemen Surat Menyurat & Helpdesk
            </p>
        </div>
    </div>

    <!-- Placeholder Content -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mb-8">
        <div class="p-12 text-center">
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-blue-50 dark:bg-gray-700 text-blue-500 dark:text-blue-400 mb-6">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Dalam Pengembangan</h2>
            <p class="text-gray-500 dark:text-gray-400 max-w-lg mx-auto">
                Halaman dashboard utama untuk modul MESRA saat ini sedang dalam tahap pengembangan. Silakan gunakan menu layanan di bawah ini atau navigasi sidebar untuk mengakses fitur yang sudah tersedia.
            </p>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden border border-gray-100 dark:border-gray-700">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-lg font-bold text-gray-800 dark:text-white">Layanan MESRA</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-100 dark:divide-gray-700">
            <a href="{{ route('mesra.surat-masuk.index') }}" class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <h3 class="text-base font-bold text-gray-800 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Surat Masuk (Inbox)</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Pengelolaan, pencatatan agenda, dan disposisi surat yang masuk.</p>
            </a>
            
            <a href="{{ route('mesra.pengajuan-cerai.index') }}" class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                <div class="w-12 h-12 bg-rose-100 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h3 class="text-base font-bold text-gray-800 dark:text-white mb-2 group-hover:text-rose-600 dark:group-hover:text-rose-400 transition-colors">Pengajuan Cerai</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Layanan pengajuan, verifikasi, dan monitoring proses permohonan cerai ASN.</p>
            </a>
            
            <a href="{{ route('mesra.chat.index') }}" class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                </div>
                <h3 class="text-base font-bold text-gray-800 dark:text-white mb-2 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">Helpdesk & Chat</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Pusat bantuan interaktif melalui live chat untuk kendala dan pelayanan informasi.</p>
            </a>
        </div>
    </div>
</div>
@endsection
