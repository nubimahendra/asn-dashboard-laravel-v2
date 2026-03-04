@extends('layouts.app')

@section('content')
<div class="container mx-auto px-10 py-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Jabatan Default (Fallback Kelas)</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Digunakan sebagai cadangan fallback ketika mapping spesifik jabatan terhadap Perbup belum ada nilainya.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-red-100 border text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc pl-5">@foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach</ul>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 mb-8 p-6">
        <h3 class="text-lg font-semibold mb-4">Set Kelas Default</h3>
        <form action="{{ route('jabatan-default.store') }}" method="POST" class="flex flex-col sm:flex-row gap-4 items-end">
            @csrf
            <div class="flex-1">
                <label class="block text-sm font-medium mb-1">Jabatan SIASN</label>
                <select id="jabatan_siasn_select" name="jabatan_id" required class="w-full text-sm border rounded-lg p-2 dark:bg-gray-700">
                    <option value="">-- Pilih Jabatan --</option>
                    @foreach($jabatanList as $j)
                        <option value="{{ $j->id }}">{{ $j->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-32">
                <label class="block text-sm font-medium mb-1">Kelas Default</label>
                <input type="number" name="kelas_jabatan" required min="1" max="17" class="w-full text-sm border rounded-lg p-2 dark:bg-gray-700">
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-lg font-medium text-sm">Simpan</button>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
            <h3 class="font-semibold text-gray-700">Daftar Kelas Default</h3>
            <form method="GET" action="{{ route('jabatan-default.index') }}">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..." class="border rounded-lg p-2 text-sm">
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">Nama Jabatan (SIASN)</th>
                        <th class="px-6 py-3 text-center">Kelas Default</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($data as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium">{{ optional($item->jabatan)->nama }}</td>
                            <td class="px-6 py-4 text-center font-bold text-blue-600">{{ $item->kelas_jabatan }}</td>
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('jabatan-default.destroy', $item->jabatan_id) }}" method="POST" onsubmit="return confirm('Hapus default?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-6 py-12 text-center text-gray-500">Belum ada default mapping.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($data->hasPages())
            <div class="px-6 py-4 border-t bg-gray-50">{{ $data->links() }}</div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new TomSelect('#jabatan_siasn_select', {
            create: false,
            sortField: { field: "text", direction: "asc" },
            placeholder: '-- Pilih Jabatan --'
        });
    });
</script>
<style>
    .ts-control {
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        line-height: 1.25rem;
        border-color: #e5e7eb;
        min-height: 42px;
    }
    .dark .ts-control {
        background-color: #374151;
        border-color: #4b5563;
        color: #f3f4f6;
    }
    .dark .ts-dropdown {
        background-color: #374151;
        border-color: #4b5563;
        color: #f3f4f6;
    }
    .dark .ts-dropdown .option {
        color: #f3f4f6;
    }
    .dark .ts-dropdown .option.active, .dark .ts-dropdown .option:hover {
        background-color: #4b5563;
        color: white;
    }
    .dark .ts-control input {
        color: white;
    }
</style>
@endsection
