@extends('layouts.mari')

@section('content')
<div class="container mx-auto px-10 py-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Dashboard MARI</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">
                Manajemen Iuran Korpri & Kelas Jabatan
            </p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 px-3 py-1 rounded-full">
                Periode: {{ date('F', mktime(0, 0, 0, $bulanSekarang, 10)) }} {{ $tahunSekarang }}
            </span>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Pegawai Aktif</p>
            <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
                {{ number_format($totalPegawaiAktif) }}
            </h2>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-indigo-500">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Pegawai Ber-Golongan</p>
            <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
                {{ number_format($totalPegawaiGolongan) }}
            </h2>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-orange-500">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Unit Kerja (OPD) Terproses</p>
            <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
                {{ number_format($jumlahOpdBulanIni) }}
            </h2>
        </div>
        <div class="bg-gradient-to-r from-emerald-500 to-teal-500 dark:from-emerald-700 dark:to-teal-700 rounded-xl shadow-lg p-6">
            <p class="text-sm text-emerald-100 mb-1">Total Iuran Bulan Ini</p>
            <h2 class="text-2xl lg:text-3xl font-bold text-white truncate">
                Rp {{ number_format($totalIuranBulanIni, 0, ',', '.') }}
            </h2>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden border border-gray-100 dark:border-gray-700">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-lg font-bold text-gray-800 dark:text-white">Layanan MARI</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-100 dark:divide-gray-700">
            <a href="{{ route('mari.iuran-korpri.index') }}" class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-base font-bold text-gray-800 dark:text-white mb-2 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">Laporan Iuran Korpri</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Lihat rekapitulasi iuran per OPD dan update tarif berdasarkan golongan.</p>
            </a>
            
            <a href="{{ route('mari.iuran-kelas-jabatan.index') }}" class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
                <h3 class="text-base font-bold text-gray-800 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Iuran Kelas Jabatan</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Pantau besaran iuran berdasarkan detail kelas jabatan masing-masing pegawai.</p>
            </a>
            
            <a href="{{ route('mari.kelas-jabatan-perbup.index') }}" class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/50 text-purple-600 dark:text-purple-400 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <h3 class="text-base font-bold text-gray-800 dark:text-white mb-2 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">Master Kelas Perbup</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Atur referensi kelas jabatan, mapping jabatan, dan setting kelas jabatan default.</p>
            </a>
        </div>
    </div>
</div>
@endsection
