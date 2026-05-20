@extends('layouts.mari')

@section('content')
    <div class="container mx-auto px-4 sm:px-8 py-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Mapping Eselon Jabatan</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Mengatur mapping antara Jabatan Struktural dengan Eselon untuk perhitungan iuran KORPRI.</p>
            </div>
            <div>
                <form action="{{ route('mari.eselon-mapping.generate') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin melakukan auto-generate mapping eselon? Ini tidak akan menimpa mapping manual yang sudah ada.');">
                    @csrf
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition-colors flex items-center shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                        Auto-Generate Mapping
                    </button>
                </form>
            </div>
        </div>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                </ul>
            </div>
        @endif
        

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Mapped</div>
                <div class="text-3xl font-bold text-gray-800 dark:text-white">{{ $totalMapped }} <span class="text-sm font-normal text-gray-500">/ {{ $totalStrukturalJabatan }}</span></div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Mapping Auto</div>
                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $totalAuto }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Mapping Manual</div>
                <div class="text-3xl font-bold text-orange-600 dark:text-orange-400">{{ $totalManual }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Unmapped Struktural</div>
                <div class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $totalStrukturalJabatan - $totalMapped }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 mb-8 p-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Tambah Mapping Manual</h3>
            <form action="{{ route('mari.eselon-mapping.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                @csrf
                <div class="md:col-span-6">
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Jabatan Struktural (Unmapped)</label>
                    <select id="jabatan_select" name="jabatan_id" required class="w-full text-sm border border-gray-300 rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">-- Pilih Jabatan --</option>
                        @foreach($jabatanList as $j)
                            <option value="{{ $j->id }}">{{ $j->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-4">
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Target Eselon</label>
                    <select name="eselon_key" required class="w-full text-sm border border-gray-300 rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white h-[42px]">
                        <option value="">-- Pilih Eselon --</option>
                        @foreach($eselonList as $e)
                            <option value="{{ $e->eselon_key }}">{{ $e->label }} ({{ $e->eselon_key }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-lg font-medium text-sm h-[42px] transition-colors">Simpan Manual</button>
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex flex-col md:flex-row justify-between items-center gap-4">
                <h3 class="font-semibold text-gray-800 dark:text-white">Daftar Mapping Eselon</h3>
                <form method="GET" action="{{ route('mari.eselon-mapping.index') }}" class="flex flex-wrap gap-2">
                    <select name="tipe" class="border border-gray-300 rounded-lg p-2 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="this.form.submit()">
                        <option value="">Semua Tipe</option>
                        <option value="auto" {{ request('tipe') == 'auto' ? 'selected' : '' }}>Auto</option>
                        <option value="manual" {{ request('tipe') == 'manual' ? 'selected' : '' }}>Manual</option>
                    </select>
                    <select name="eselon_key" class="border border-gray-300 rounded-lg p-2 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="this.form.submit()">
                        <option value="">Semua Eselon</option>
                        @foreach($eselonList as $e)
                            <option value="{{ $e->eselon_key }}" {{ request('eselon_key') == $e->eselon_key ? 'selected' : '' }}>{{ $e->eselon_key }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Jabatan..." class="border border-gray-300 rounded-lg p-2 text-sm w-64 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-2 rounded-lg text-sm transition-colors">Cari</button>
                    @if(request('search') || request('eselon_key') || request('tipe'))
                        <a href="{{ route('mari.eselon-mapping.index') }}" class="bg-red-100 hover:bg-red-200 text-red-600 px-3 py-2 rounded-lg text-sm transition-colors">Reset</a>
                    @endif
                </form>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3">Jabatan Struktural</th>
                            <th class="px-6 py-3 text-center">Eselon</th>
                            <th class="px-6 py-3 text-center">Tipe Mapping</th>
                            <th class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($data as $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 bg-white dark:bg-gray-800">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    {{ optional($item->jabatan)->nama ?? 'Jabatan Dihapus' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full font-bold text-xs">
                                        {{ $item->eselon_key }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($item->is_auto)
                                        <span class="text-emerald-600 font-semibold text-xs flex items-center justify-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                            Auto
                                        </span>
                                    @else
                                        <span class="text-orange-600 font-semibold text-xs flex items-center justify-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            Manual
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        <button type="button" onclick="openEditModal({{ $item->id }}, '{{ addslashes(optional($item->jabatan)->nama) }}', '{{ $item->eselon_key }}')" class="text-blue-600 hover:text-blue-900 dark:hover:text-blue-400" title="Edit Manual">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <form action="{{ route('mari.eselon-mapping.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus mapping ini?');" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 dark:hover:text-red-400" title="Hapus">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">Belum ada mapping eselon. Silakan lakukan Auto-Generate atau tambah manual.</td>
                            </tr>
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

    <!-- Edit Modal -->
    <div id="editModal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center bg-gray-900/50">
        <div class="relative w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Edit Mapping Eselon</h3>
                    <button type="button" onclick="closeEditModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                        <span class="sr-only">Tutup</span>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <p class="text-sm font-medium text-gray-900 dark:text-white mb-2" id="editJabatanName"></p>
                    <form id="editForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Target Eselon</label>
                            <select name="eselon_key" id="editEselonKey" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                                @foreach($eselonList as $e)
                                    <option value="{{ $e->eselon_key }}">{{ $e->label }} ({{ $e->eselon_key }})</option>
                                @endforeach
                            </select>
                        </div>
                        <p class="text-xs text-orange-600 mt-2 italic">*Mengubah mapping ini akan menjadikannya "Manual Override"</p>
                        <div class="mt-6 flex justify-end">
                            <button type="button" onclick="closeEditModal()" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600 mr-2">Batal</button>
                            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('jabatan_select')) {
                new TomSelect('#jabatan_select', {
                    create: false,
                    sortField: { field: "text", direction: "asc" },
                    placeholder: '-- Pilih Jabatan Struktural --'
                });
            }
        });

        function openEditModal(id, jabatanName, eselonKey) {
            document.getElementById('editJabatanName').textContent = jabatanName;
            document.getElementById('editEselonKey').value = eselonKey;
            document.getElementById('editForm').action = `{{ url('mari/eselon-mapping') }}/${id}`;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
    <style>
        .ts-control {
            border-radius: 0.5rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            line-height: 1.25rem;
            border-color: #d1d5db;
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

        .dark .ts-dropdown .option.active,
        .dark .ts-dropdown .option:hover {
            background-color: #4b5563;
            color: white;
        }

        .dark .ts-control input {
            color: white;
        }
    </style>
@endsection
