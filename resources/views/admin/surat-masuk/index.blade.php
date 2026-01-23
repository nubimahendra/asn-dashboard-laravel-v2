@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Inbox (Surat Masuk)</h1>

            <div class="flex gap-2">
                <a href="{{ route('surat-masuk.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow transition-colors">
                    + Tambah Surat
                </a>
            </div>
        </div>

        <!-- Filter & Print Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-6">
            <form action="{{ route('surat-masuk.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                <div class="w-full md:w-auto">
                    <label for="month" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bulan</label>
                    <select name="month" id="month"
                        class="w-full md:w-48 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Semua Bulan --</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ request('month', now()->month) == $m ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full md:w-auto">
                    <label for="year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tahun</label>
                    <select name="year" id="year"
                        class="w-full md:w-32 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-blue-500 focus:border-blue-500">
                        @foreach(range(now()->year, now()->year - 5) as $y)
                            <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" style="background-color: #1f2937; color: white;"
                        class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-md transition-colors shadow-sm dark:bg-gray-600 dark:hover:bg-gray-500">
                        Filter
                    </button>

                    <a href="{{ route('surat-masuk.print', request()->all()) }}" target="_blank"
                        style="background-color: #16a34a; color: white;"
                        class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition-colors flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                            </path>
                        </svg>
                        Cetak
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-xs uppercase text-gray-700 dark:text-gray-200">
                        <tr>
                            <th class="px-6 py-3">No</th>
                            <th class="px-6 py-3">No. Agenda</th>
                            <th class="px-6 py-3">No. Surat</th>
                            <th class="px-6 py-3">Tanggal Terima</th>
                            <th class="px-6 py-3">Pengirim</th>
                            <th class="px-6 py-3">Perihal</th>
                            <th class="px-6 py-3">Disposisi</th>
                            <th class="px-6 py-3">Keterangan</th>
                            <th class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($suratMasuks as $index => $surat)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4">{{ $suratMasuks->firstItem() + $index }}</td>
                                <td class="px-6 py-4 font-medium">{{ $surat->nomor_agenda }}</td>
                                <td class="px-6 py-4">{{ $surat->nomor_surat }}</td>
                                <td class="px-6 py-4">
                                    {{ $surat->tanggal_terima ? $surat->tanggal_terima->format('d M Y') : '-' }}
                                </td>
                                <td class="px-6 py-4">{{ $surat->pengirim }}</td>
                                <td class="px-6 py-4">{{ Str::limit($surat->perihal, 30) }}</td>
                                <td class="px-6 py-4">{{ Str::limit($surat->disposisi, 20) }}</td>
                                <td class="px-6 py-4">{{ Str::limit($surat->keterangan, 20) }}</td>
                                <td class="px-6 py-4 flex justify-center gap-2">
                                    <a href="{{ route('surat-masuk.edit', $surat->id) }}"
                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                        title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('surat-masuk.destroy', $surat->id) }}" method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                            title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                <td colspan="9" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    Belum ada data surat masuk.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $suratMasuks->links() }}
            </div>
        </div>
    </div>
@endsection