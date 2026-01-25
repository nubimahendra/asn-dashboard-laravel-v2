@extends('layouts.app')

@section('content')
    <div class="px-6 py-4">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                    {{ $isHistory ? 'History Snapshot Pegawai' : 'Snapshot Data Pegawai' }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ $isHistory ? 'Menampilkan data snapshot tanggal ' . \Carbon\Carbon::parse($filterMonth)->format('F Y') : 'Preview data pegawai saat ini. Klik Simpan untuk membekukan data bulan ini.' }}
                </p>
            </div>
        </div>

        <!-- Filter Tools -->
        <div
            class="mb-4 bg-white dark:bg-gray-800 rounded-lg px-4 py-2 shadow-sm flex flex-col md:flex-row gap-4 justify-between items-center">
            <!-- Filter Month -->
            <form action="{{ route('snapshot.index') }}" method="GET" class="flex gap-2 items-center w-full md:w-auto">
                <select name="snapshot_month" onchange="this.form.submit()"
                    class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Data Live (Saat Ini) --</option>
                    @foreach($historyMonths as $month)
                        <option value="{{ $month }}" {{ $filterMonth == $month ? 'selected' : '' }}>
                            Snapshot: {{ \Carbon\Carbon::parse($month)->isoFormat('MMMM Y') }}
                        </option>
                    @endforeach
                </select>
            </form>

            <div class="flex gap-1">
                <!-- Export Buttons -->
                <a href="{{ route('snapshot.export.pdf', ['month' => $filterMonth]) }}"
                    class="px-3 py-1 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700 flex items-center gap-1 shadow-sm"
                    style="background-color: #dc2626; color: white;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                        </path>
                    </svg>
                    PDF
                </a>
                <a href="{{ route('snapshot.export.excel', ['month' => $filterMonth]) }}"
                    class="px-3 py-1 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700 flex items-center gap-1 shadow-sm"
                    style="background-color: #16a34a; color: white;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Excel
                </a>

                @if(!$isHistory)
                    <form action="{{ route('snapshot.store') }}" method="POST"
                        onsubmit="return confirm('Apakah Anda yakin ingin menyimpan Snapshot data pegawai? Aksi ini hanya bisa dilakukan sekali per bulan.');">
                        @csrf
                        <button type="submit"
                            class="flex items-center gap-2 px-4 py-1 rounded-lg font-medium transition-colors
                                                                                                                {{ $canSnapshot ? 'bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-500/30' : 'bg-gray-300 dark:bg-gray-700 text-gray-500 cursor-not-allowed' }}"
                            {{ !$canSnapshot ? 'disabled' : '' }}>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                                </path>
                            </svg>
                            {{ $canSnapshot ? 'Simpan Snapshot' : 'Snapshot Bulan Ini Sudah Ada' }}
                        </button>
                    </form>
                @endif
            </div>
        </div>



        <!-- Preview Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div
                class="p-4 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h3 class="font-semibold text-gray-700 dark:text-gray-200">
                        {{ $isHistory ? 'Data Snapshot: ' . \Carbon\Carbon::parse($filterMonth)->format('F Y') : 'Data Pegawai (Live Preview)' }}
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Total: {{ $pegawai->total() }} Pegawai</p>
                </div>

                <!-- Search -->
                <form action="{{ route('snapshot.index') }}" method="GET" class="relative w-full md:w-64">
                    @if($filterMonth)
                        <input type="hidden" name="snapshot_month" value="{{ $filterMonth }}">
                    @endif
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari NIP / Nama..."
                        class="w-full pl-10 pr-4 py-2 text-sm text-gray-700 bg-gray-50 dark:bg-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-gray-50 dark:bg-gray-700 text-xs text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            <th class="px-6 py-4 font-semibold border-b border-gray-100 dark:border-gray-600">No</th>
                            <th class="px-6 py-4 font-semibold border-b border-gray-100 dark:border-gray-600">NIP / Nama
                            </th>
                            <th class="px-6 py-4 font-semibold border-b border-gray-100 dark:border-gray-600">Jabatan</th>
                            <th class="px-6 py-4 font-semibold border-b border-gray-100 dark:border-gray-600">Unit Kerja
                            </th>
                            <th class="px-6 py-4 font-semibold border-b border-gray-100 dark:border-gray-600">Status</th>
                            @if($isHistory)
                                <th class="px-6 py-4 font-semibold border-b border-gray-100 dark:border-gray-600">Tanggal
                                    Snapshot</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($pegawai as $index => $item)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300 w-12">
                                                {{ $pegawai->firstItem() + $index }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->nama_pegawai }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $item->nip_baru ?? '-' }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $item->jabatan ?? '-' }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                                <div class="font-medium">{{ $item->pd ?? '-' }}</div>
                                                <div class="text-xs text-gray-500">{{ $item->sub_pd ?? '' }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                                                                                                                                                                                                                                                        {{ str_contains($item->sts_peg, 'PNS') ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' :
                            (str_contains($item->sts_peg, 'PPPK') ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300') }}">
                                                    {{ $item->sts_peg ?? '-' }}
                                                </span>
                                            </td>
                                            @if($isHistory)
                                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                    {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                                                </td>
                                            @endif
                                        </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isHistory ? 6 : 5 }}"
                                    class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 italic">
                                    Data pegawai kosong.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                {{ $pegawai->links() }}
            </div>
        </div>
    </div>
@endsection