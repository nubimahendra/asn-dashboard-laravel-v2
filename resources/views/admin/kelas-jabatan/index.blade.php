@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-10 py-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Kelas Jabatan</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">
                    Kelola data referensi mapping jabatan dan OPD dengan atribut kelas jabatan.
                </p>
            </div>
        </div>

        @if(session('import_errors'))
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Beberapa baris data gagal diimport:</strong>
                <ul class="list-disc pl-5 mt-2 text-sm">
                    @foreach(array_slice(session('import_errors'), 0, 10) as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                    @if(count(session('import_errors')) > 10)
                        <li class="italic text-gray-600 mt-1">...dan {{ count(session('import_errors')) - 10 }} kesalahan lainnya
                            disembunyikan.</li>
                    @endif
                </ul>
            </div>
        @endif

        <!-- Import Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 mb-8 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Import Data Referensi</h3>
            <form action="{{ route('kelas-jabatan.import') }}" method="POST" enctype="multipart/form-data"
                class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                @csrf
                <div class="flex-1">
                    <input type="file" name="file" accept=".xlsx,.csv,.xls" required
                        class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-700 dark:file:text-gray-300">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Format yang didukung: Excel (.xlsx, .csv).
                        Urutan/Header wajib: <strong>nama_opd, nama_jabatan, kelas_jabatan</strong>.</p>
                </div>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md transition-colors text-sm flex items-center shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    Upload & Import
                </button>
            </form>
        </div>

        <!-- Data Table -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden mb-8">
            <div
                class="p-4 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gray-50 dark:bg-gray-900/50">
                <h3 class="font-semibold text-gray-700 dark:text-gray-200">Data Kelas Jabatan Tersimpan</h3>

                <form method="GET" action="{{ route('kelas-jabatan.index') }}" class="relative w-full sm:w-auto">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Jabatan / OPD..."
                        class="w-full sm:w-64 pl-4 pr-10 py-2 text-sm border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors">
                    <button type="submit"
                        class="absolute right-0 top-0 mt-2 mr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead
                        class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-600">
                        <tr>
                            <th scope="col" class="px-6 py-3 w-16">No</th>
                            <th scope="col" class="px-6 py-3 min-w-[250px]">OPD</th>
                            <th scope="col" class="px-6 py-3 min-w-[250px]">Jabatan</th>
                            <th scope="col" class="px-6 py-3 text-center">Kelas</th>
                            <th scope="col" class="px-6 py-3 w-24 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($data as $index => $item)
                            <tr
                                class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                                <td class="px-6 py-4">{{ $data->firstItem() + $index }}</td>
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    {{ $item->unor->nama_opd ?? $item->unor->nama ?? '-' }}
                                </td>
                                <td class="px-6 py-4">{{ $item->jabatan->nama ?? '-' }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-1 rounded dark:bg-blue-900 dark:text-blue-300 shadow-sm border border-blue-200 dark:border-blue-800">
                                        {{ $item->kelas_jabatan }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <form action="{{ route('kelas-jabatan.destroy', $item->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus mapping jabatan ini?');"
                                        class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 p-1.5 rounded-md hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
                                            title="Hapus Data">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-3" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                            </path>
                                        </svg>
                                        <p>Belum ada data referensi kelas jabatan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($data instanceof \Illuminate\Pagination\LengthAwarePaginator && $data->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    {{ $data->links() }}
                </div>
            @endif
        </div>

        <!-- Tarif Iuran Card -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 mb-8 overflow-hidden">
            <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                <h3 class="font-semibold text-gray-700 dark:text-gray-200">Tarif Iuran per Kelas Jabatan Tahun
                    {{ date('Y') }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Sesuaikan nominal iuran yang berlaku untuk
                    masing-masing kelas jabatan.</p>
            </div>

            <form action="{{ route('kelas-jabatan.update-tarif') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="p-6">
                    <!-- Responsive Grid layout for tariffs -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @forelse($tarifIuran ?? [] as $tarif)
                            <div
                                class="flex items-center space-x-3 bg-gray-50 flex-row dark:bg-gray-900/30 p-3 rounded-lg border border-gray-100 dark:border-gray-700">
                                <div
                                    class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300 rounded-lg flex items-center justify-center font-bold text-lg shadow-sm border border-blue-200 dark:border-blue-800">
                                    {{ $tarif->kelas_jabatan }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Nominal
                                        (Rp)</label>
                                    <input type="number" name="tarif[{{ $tarif->id }}]" value="{{ $tarif->nominal }}" min="0"
                                        required
                                        class="w-full text-sm font-bold border border-gray-200 dark:border-gray-600 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors py-1.5 px-3">
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-4 text-center text-gray-500 dark:text-gray-400 text-sm">
                                Data tarif iuran belum tersedia.
                            </div>
                        @endforelse
                    </div>
                </div>
                <div
                    class="px-6 py-4 bg-gray-50 dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md transition-colors text-sm flex items-center shadow-sm"
                        onclick="return confirm('Simpan perubahan tarif iuran kelas jabatan?')">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Tarif Iuran
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection