@extends('layouts.app')

@section('content')
<div class="container mx-auto px-10 py-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Kelas Jabatan Perbup</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">
                Master data kelas jabatan resmi berdasarkan Peraturan Bupati. Data diimpor dari Excel.
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
    @if(session('import_errors'))
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded relative mb-4">
            <strong class="font-bold">Beberapa baris data gagal diimport:</strong>
            <ul class="list-disc pl-5 mt-2 text-sm">
                @foreach(array_slice(session('import_errors'), 0, 10) as $error)
                    <li>{{ $error }}</li>
                @endforeach
                @if(count(session('import_errors')) > 10)
                    <li class="italic text-gray-600 mt-1">...dan {{ count(session('import_errors')) - 10 }} kesalahan lainnya disembunyikan.</li>
                @endif
            </ul>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 mb-8 p-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Import Data Master Perbup</h3>
        <form action="{{ route('kelas-jabatan-perbup.import') }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
            @csrf
            <div class="flex-1">
                <input type="file" name="file" accept=".xlsx,.csv,.xls" required class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Format yang didukung: Excel (.xlsx, .csv). Urutan/Header wajib: <strong>nama_opd_perbup, nama_jabatan_perbup, kelas_jabatan</strong>.</p>
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md transition-colors text-sm flex items-center shadow-sm">
                Upload & Import
            </button>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden mb-8">
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gray-50 dark:bg-gray-900/50">
            <h3 class="font-semibold text-gray-700 dark:text-gray-200">Data Master Perbup Tersimpan</h3>
            <form method="GET" action="{{ route('kelas-jabatan-perbup.index') }}" class="relative w-full sm:w-auto">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Jabatan / OPD..." class="w-full sm:w-64 pl-4 pr-10 py-2 text-sm border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                <button type="submit" class="absolute right-0 top-0 mt-2 mr-3 text-gray-400 hover:text-gray-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-600">
                    <tr>
                        <th class="px-6 py-3 w-16">No</th>
                        <th class="px-6 py-3">OPD Perbup</th>
                        <th class="px-6 py-3">Jabatan Perbup</th>
                        <th class="px-6 py-3 text-center">Kelas</th>
                        <th class="px-6 py-3 w-24 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($data as $index => $item)
                        <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4">{{ $data->firstItem() + $index }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $item->nama_opd_perbup }}</td>
                            <td class="px-6 py-4">{{ $item->nama_jabatan_perbup }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-1 rounded">{{ $item->kelas_jabatan }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('kelas-jabatan-perbup.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin menghapus data ini?');" class="inline-block">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 p-1.5 rounded-md hover:bg-red-50">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">Belum ada data referensi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($data->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                {{ $data->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
