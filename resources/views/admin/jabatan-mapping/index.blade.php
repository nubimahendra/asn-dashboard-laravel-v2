@extends('layouts.mari')

@section('content')
    <div class="container mx-auto px-10 py-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Mapping Jabatan SIASN ke Perbup</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Jembatan relasi antara referensi Jabatan SIASN dengan
                    nomenklatur di Perbup.</p>
            </div>
        </div>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 mb-8 p-6">
            <h3 class="text-lg font-semibold mb-4">Tambah / Update Mapping</h3>
            <form action="{{ route('mari.jabatan-mapping.store') }}" method="POST"
                class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                @csrf
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium mb-1">Jabatan SIASN</label>
                    <select id="jabatan_siasn_select" name="jabatan_siasn_id" required
                        class="w-full text-sm border rounded-lg p-2 dark:bg-gray-700">
                        <option value="">-- Pilih Jabatan SIASN --</option>
                        @foreach($jabatanList as $j)
                            <option value="{{ $j->id }}">{{ $j->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-4 relative">
                    <label class="block text-sm font-medium mb-1">Target Kelas Perbup</label>
                    <select id="kelas_perbup_select" name="kelas_perbup_id"
                        class="w-full text-sm border rounded-lg p-2 dark:bg-gray-700">
                        <option value="">-- (Kosongkan jika hanya ingin nandai INVALID) --</option>
                        @foreach($perbupList as $p)
                            <option value="{{ $p->id }}">{{ $p->nama_opd_perbup }} - {{ $p->nama_jabatan_perbup }} (Kelas
                                {{ $p->kelas_jabatan}})</option>
                        @endforeach
                    </select>
                    <div id="similarity-help-text" class="absolute -bottom-5 left-0 w-full mt-1"></div>
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
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-lg font-medium text-sm h-[42px]">Simpan</button>
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 mb-8 p-6">
            <h3 class="text-lg font-semibold mb-4">Mapping Masal Otomatis (Berdasarkan Kemiripan Nama)</h3>
            <p class="text-sm text-gray-500 mb-4">Fitur ini akan mencari semua Jabatan SIASN yang <b>belum di-map</b>, lalu mencarikan padanannya di Target Kelas Perbup yang paling mirip.</p>
            
            <button type="button" id="btn-generate-bulk" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition-colors mb-4 flex items-center">
                <span id="btn-text">Generate Saran Mapping Otomatis</span>
                <svg id="btn-spinner" class="animate-spin ml-2 h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>

            <form action="{{ route('mari.jabatan-mapping.bulk-store') }}" method="POST" id="form-bulk-mapping" class="hidden mt-4">
                @csrf
                <div class="overflow-x-auto max-h-96 overflow-y-auto mb-4 border rounded-lg">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs uppercase bg-gray-50 sticky top-0 shadow-sm z-10">
                            <tr>
                                <th class="px-4 py-3 w-10">
                                    <input type="checkbox" id="check-all-bulk" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </th>
                                <th class="px-4 py-3">Jabatan SIASN</th>
                                <th class="px-4 py-3">Saran Target Perbup</th>
                                <th class="px-4 py-3 w-32 text-center">Kemiripan</th>
                                <th class="px-4 py-3 w-32 text-center">Status Nanti</th>
                            </tr>
                        </thead>
                        <tbody id="bulk-suggestions-tbody" class="divide-y divide-gray-100">
                            <!-- Rows injected via JS -->
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium text-sm transition-colors">
                        Proses Simpan Masal
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
                <h3 class="font-semibold text-gray-700">Daftar Mapping</h3>
                <form method="GET" action="{{ route('mari.jabatan-mapping.index') }}">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..."
                        class="border rounded-lg p-2 text-sm w-64">
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
                                <td class="px-6 py-4 text-center font-bold text-blue-600">
                                    {{ optional($item->kelasPerbup)->kelas_jabatan ?? '-' }}</td>
                                <td class="px-6 py-4 text-center">
                                    <form action="{{ route('mari.jabatan-mapping.destroy', $item->id) }}" method="POST"
                                        onsubmit="return confirm('Hapus mapping?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">Belum ada mapping.</td>
                            </tr>
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
            const perbupSelect = new TomSelect('#kelas_perbup_select', {
                create: false,
                sortField: { field: "text", direction: "asc" },
                placeholder: '-- (Kosongkan jika hanya ingin nandai INVALID) --'
            });

            const siasnSelect = new TomSelect('#jabatan_siasn_select', {
                create: false,
                sortField: { field: "text", direction: "asc" },
                placeholder: '-- Pilih Jabatan SIASN --',
                onChange: function(value) {
                    if (!value) return;
                    
                    const helpText = document.getElementById('similarity-help-text');
                    if (helpText) helpText.innerHTML = '<span class="text-blue-500 text-xs">Mencari kemiripan...</span>';

                    fetch(`{{ route('mari.jabatan-mapping.find-similar') }}?jabatan_siasn_id=${value}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.perbup_id) {
                                perbupSelect.setValue(data.perbup_id);
                                if (helpText) {
                                    helpText.innerHTML = `<span class="text-green-600 text-xs font-semibold">Ditemukan target dengan kemiripan ${data.similarity}%</span>`;
                                }
                            } else {
                                perbupSelect.setValue('');
                                if (helpText) {
                                    helpText.innerHTML = `<span class="text-gray-500 text-xs">Tidak ditemukan kemiripan yang cukup.</span>`;
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error finding similar perbup:', error);
                            if (helpText) helpText.innerHTML = '';
                        });
                }
            });

            // Bulk Mapping Logic
            const btnGenerate = document.getElementById('btn-generate-bulk');
            const formBulk = document.getElementById('form-bulk-mapping');
            const tbodyBulk = document.getElementById('bulk-suggestions-tbody');
            const btnText = document.getElementById('btn-text');
            const btnSpinner = document.getElementById('btn-spinner');
            const checkAll = document.getElementById('check-all-bulk');

            if (checkAll) {
                checkAll.addEventListener('change', function() {
                    const checkboxes = tbodyBulk.querySelectorAll('.check-bulk-item');
                    checkboxes.forEach(cb => {
                        if(!cb.disabled) cb.checked = this.checked;
                    });
                });
            }

            if (btnGenerate) {
                btnGenerate.addEventListener('click', function() {
                    btnGenerate.disabled = true;
                    btnText.innerText = 'Memproses...';
                    btnSpinner.classList.remove('hidden');
                    tbodyBulk.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Menganalisis kemiripan dari seluruh data, mohon tunggu...</td></tr>';
                    formBulk.classList.remove('hidden');

                    fetch(`{{ route('mari.jabatan-mapping.generate-bulk') }}`)
                        .then(res => res.json())
                        .then(data => {
                            btnGenerate.disabled = false;
                            btnText.innerText = 'Generate Ulang Saran';
                            btnSpinner.classList.add('hidden');

                            if (data.success && data.data && data.data.length > 0) {
                                let html = '';
                                data.data.forEach((item, index) => {
                                    const statusLabel = item.similarity >= 60 
                                        ? '<span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold">Valid</span>' 
                                        : '<span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs font-bold">Unvalidated</span>';
                                    
                                    html += `
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-2">
                                                <input type="checkbox" name="mappings[${index}][process]" value="1" checked class="check-bulk-item rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                <input type="hidden" name="mappings[${index}][siasn_id]" value="${item.siasn_id}">
                                                <input type="hidden" name="mappings[${index}][perbup_id]" value="${item.perbup_id}">
                                                <input type="hidden" name="mappings[${index}][similarity]" value="${item.similarity}">
                                            </td>
                                            <td class="px-4 py-2 font-medium text-xs">${item.siasn_nama}</td>
                                            <td class="px-4 py-2 text-xs">${item.perbup_nama}</td>
                                            <td class="px-4 py-2 text-center text-xs">
                                                <div class="w-full bg-gray-200 rounded-full h-1.5 mb-1 dark:bg-gray-700">
                                                    <div class="${item.similarity >= 60 ? 'bg-green-600' : 'bg-yellow-400'} h-1.5 rounded-full" style="width: ${item.similarity}%"></div>
                                                </div>
                                                ${item.similarity}%
                                            </td>
                                            <td class="px-4 py-2 text-center">${statusLabel}</td>
                                        </tr>
                                    `;
                                });
                                tbodyBulk.innerHTML = html;
                            } else {
                                tbodyBulk.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Tidak ada saran mapping yang ditemukan atau semua jabatan SIASN sudah di-mapping.</td></tr>';
                            }
                        })
                        .catch(err => {
                            btnGenerate.disabled = false;
                            btnText.innerText = 'Coba Lagi';
                            btnSpinner.classList.add('hidden');
                            tbodyBulk.innerHTML = `<tr><td colspan="5" class="px-4 py-8 text-center text-red-500">Terjadi kesalahan: ${err.message}</td></tr>`;
                        });
                });
            }
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