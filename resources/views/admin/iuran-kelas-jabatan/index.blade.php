@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-10 py-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Iuran Kelas Jabatan</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">
                    Kelola dan lihat hasil generate Iuran Korpri berbasis Kelas Jabatan berdasarkan periode bulan dan tahun.
                </p>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Berhasil!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Gagal!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Generator Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 mb-8 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">GENERATOR IURAN KORPRI BULANAN (BERBASIS
                KELAS JABATAN)</h3>
            <form action="{{ route('iuran-kelas-jabatan.generate') }}" method="POST"
                class="flex flex-col sm:flex-row items-end gap-4">
                @csrf
                <div class="flex-1 w-full sm:w-auto">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bulan</label>
                    <select name="bulan"
                        class="w-full text-sm border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors py-2 px-3">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ (isset($bulan) ? $bulan : date('n')) == $i ? 'selected' : '' }}>
                                {{ date("F", mktime(0, 0, 0, $i, 1)) }}</option>
                        @endfor
                    </select>
                </div>
                <div class="flex-1 w-full sm:w-auto">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tahun</label>
                    <input type="number" name="tahun" value="{{ isset($tahun) ? $tahun : date('Y') }}" required
                        class="w-full text-sm border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors py-2 px-3">
                </div>
                <button type="submit"
                    onclick="return confirm('Mulai generate iuran Korpri bulan ini? Data yang sudah ada di bulan/tahun sama akan ditimpa (diupdate).')"
                    class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-md transition-colors text-sm flex items-center shadow-sm h-[38px]">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                        </path>
                    </svg>
                    Generate Iuran
                </button>
            </form>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Men-generate iuran Korpri berdasarkan kelas jabatan
                masing-masing pegawai. Pegawai yang tidak diwajibkan (seperti PPPK Paruh Waktu) akan diabaikan.</p>
        </div>

        <!-- Filter & Header Rincian Table -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden mb-8">
            <div
                class="p-4 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gray-50 dark:bg-gray-900/50">
                <h3 class="font-semibold text-gray-700 dark:text-gray-200">
                    Rincian Iuran per Unit Kerja (OPD) - Bulan {{ date("F", mktime(0, 0, 0, $bulan, 1)) }} {{ $tahun }}
                </h3>
                <div class="flex gap-2 w-full sm:w-auto">
                    <!-- Global Stats Badge -->
                    <div
                        class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-1.5 flex items-center space-x-3 text-sm shadow-sm hidden sm:flex">
                        <div class="flex items-center text-gray-600 dark:text-gray-300">
                            <span class="w-2 h-2 rounded-full bg-blue-500 mr-2"></span>
                            <span>Total Pegawai: <strong
                                    class="text-gray-900 dark:text-white">{{ number_format($globalTotals['total_pegawai']) }}</strong></span>
                        </div>
                        <div class="w-px h-4 bg-gray-300 dark:bg-gray-600 hidden sm:block"></div>
                        <div class="flex items-center text-green-600 dark:text-green-400">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                            <span>Total Iuran: <strong class="font-bold text-green-700 dark:text-green-300">Rp
                                    {{ number_format($globalTotals['total_iuran'], 0, ',', '.') }}</strong></span>
                        </div>
                    </div>
                    <!-- End Global Stats Badge -->
                </div>
            </div>

            <!-- Rincian Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead
                        class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-600">
                        <tr>
                            <th scope="col" class="px-6 py-3 w-16 text-center font-semibold">No</th>
                            <th scope="col" class="px-6 py-3 min-w-[250px] font-semibold">Unit Kerja (OPD)</th>
                            <th scope="col" class="px-6 py-3 text-center whitespace-nowrap font-semibold">Total Pegawai</th>
                            <th scope="col"
                                class="px-6 py-3 text-right bg-green-50 dark:bg-green-900/10 text-green-800 dark:text-green-400 font-bold whitespace-nowrap">
                                <div class="flex items-center justify-end">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                    Total Iuran
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($opdBreakdownPaginated as $index => $item)
                            <tr
                                class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                                <td class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    {{ $opdBreakdownPaginated->firstItem() + $index }}</td>
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $item['nama_opd'] }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-1 rounded dark:bg-blue-900 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                        {{ number_format($item['total_pegawai']) }} org
                                    </span>
                                </td>
                                <td
                                    class="px-6 py-4 text-right font-bold text-green-600 dark:text-green-400 bg-green-50/30 dark:bg-green-900/5">
                                    Rp {{ number_format($item['total_iuran'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-3" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                        <p class="text-base font-medium text-gray-900 dark:text-white mb-1">Data iuran belum
                                            di-generate</p>
                                        <p class="text-sm">Gunakan form di atas untuk men-generate data iuran bulan ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-gray-900/80 border-t border-gray-200 dark:border-gray-700 font-bold">
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-right text-gray-700 dark:text-gray-300">TOTAL KESELURUHAN
                            </td>
                            <td class="px-6 py-4 text-center text-gray-900 dark:text-white">
                                {{ number_format($globalTotals['total_pegawai']) }}</td>
                            <td class="px-6 py-4 text-right text-green-700 dark:text-green-400">Rp
                                {{ number_format($globalTotals['total_iuran'], 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if ($opdBreakdownPaginated->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    {{ $opdBreakdownPaginated->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection