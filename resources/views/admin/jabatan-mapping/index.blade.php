@extends('layouts.app')

@section('content')
<div class="container mx-auto px-10 py-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Mapping Jabatan SIASN ke Perbup</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Jembatan relasi antara referensi Jabatan SIASN dengan nomenklatur di Perbup.</p>
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
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 mb-8 p-6">
        <h3 class="text-lg font-semibold mb-4">Tambah / Update Mapping</h3>
        <form action="{{ route('jabatan-mapping.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            @csrf
            <div class="md:col-span-3">
                <label class="block text-sm font-medium mb-1">Jabatan SIASN</label>
                <select id="jabatan_siasn_select" name="jabatan_siasn_id" required class="w-full text-sm border rounded-lg p-2 dark:bg-gray-700">
                    <option value="">-- Pilih Jabatan SIASN --</option>
                    @foreach($jabatanList as $j)
                        <option value="{{ $j->id }}">{{ $j->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-4">
                <label class="block text-sm font-medium mb-1">Target Kelas Perbup</label>
                <select id="kelas_perbup_select" name="kelas_perbup_id" class="w-full text-sm border rounded-lg p-2 dark:bg-gray-700">
                    <option value="">-- (Kosongkan jika hanya ingin nandai INVALID) --</option>
                    @foreach($perbupList as $p)
                        <option value="{{ $p->id }}">{{ $p->nama_opd_perbup }} - {{ $p->nama_jabatan_perbup }} (Kelas {{ $p->kelas_jabatan}})</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-3">
                <label class="block text-sm font-medium mb-1">Status Validasi</label>
                <select name="status_validasi" class="w-full text-sm border rounded-lg p-2 dark:bg-gray-700 h-[42px]">
                    <option value="valid">Valid (Cocok)</option>
                    <option value="invalid">Invalid (Tidak Cocok / Hapus)</option>
                    <option value="unvalidated">Unvalidated</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-lg font-medium text-sm h-[42px]">Simpan</button>
            </div>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
            <h3 class="font-semibold text-gray-700">Daftar Mapping</h3>
            <form method="GET" action="{{ route('jabatan-mapping.index') }}">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..." class="border rounded-lg p-2 text-sm w-64">
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">Jabatan SIASN</th>
                        <th class="px-6 py-3">Jabatan Perbup (Target)</th>
                        <th class="px-6 py-3 text-center">Status</th>
                        <th class="px-6 py-3 text-center">Kelas</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($data as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium">{{ optional($item->jabatanSiasn)->nama }}</td>
                            <td class="px-6 py-4">
                                @if($item->kelasPerbup)
                                    <b>{{ $item->kelasPerbup->nama_opd_perbup }}</b><br>
                                    {{ $item->kelasPerbup->nama_jabatan_perbup }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($item->status_validasi == 'valid')
                                    <span class="text-green-600 font-bold">Valid</span>
                                @elseif($item->status_validasi == 'invalid')
                                    <span class="text-red-600 font-bold">Invalid</span>
                                @else
                                    <span class="text-yellow-600">Unvalidated</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-blue-600">{{ optional($item->kelasPerbup)->kelas_jabatan ?? '-' }}</td>
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('jabatan-mapping.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus mapping?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">Belum ada mapping.</td></tr>
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
            placeholder: '-- Pilih Jabatan SIASN --'
        });
        
        new TomSelect('#kelas_perbup_select', {
            create: false,
            sortField: { field: "text", direction: "asc" },
            placeholder: '-- (Kosongkan jika hanya ingin nandai INVALID) --'
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
