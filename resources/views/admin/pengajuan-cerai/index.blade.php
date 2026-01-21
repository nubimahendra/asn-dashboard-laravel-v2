@extends('layouts.app')

@section('content')
    <div class="px-6 py-4">
        <div class="mb-4 flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Pengajuan Cerai</h2>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Berhasil!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 mb-6">
            <!-- Form Section -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-200">Form Pengajuan</h3>
                <form action="{{ route('admin.pengajuan-cerai.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- NIP Autocomplete -->
                        <div class="relative">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">NIP</label>
                            <input type="text" id="nip" name="nip" required
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                autocomplete="off" placeholder="Ketik NIP atau Nama...">
                            <div id="nip-suggestions"
                                class="absolute z-10 w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg hidden mt-1 max-h-60 overflow-y-auto">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama</label>
                            <input type="text" id="nama" name="nama" readonly
                                class="w-full rounded-md border-gray-300 bg-gray-100 dark:bg-gray-600 dark:text-gray-200 shadow-sm cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jabatan</label>
                            <input type="text" id="jabatan" name="jabatan" readonly
                                class="w-full rounded-md border-gray-300 bg-gray-100 dark:bg-gray-600 dark:text-gray-200 shadow-sm cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Unit
                                Kerja</label>
                            <input type="text" id="unit_kerja" name="unit_kerja" readonly
                                class="w-full rounded-md border-gray-300 bg-gray-100 dark:bg-gray-600 dark:text-gray-200 shadow-sm cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">OPD</label>
                            <input type="text" id="opd" name="opd" readonly
                                class="w-full rounded-md border-gray-300 bg-gray-100 dark:bg-gray-600 dark:text-gray-200 shadow-sm cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal
                                Surat</label>
                            <input type="date" name="tanggal_surat" required
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jenis
                                Pengajuan</label>
                            <select name="jenis_pengajuan" required
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- Pilih Jenis --</option>
                                <option value="Penggugat">Penggugat</option>
                                <option value="Tergugat">Tergugat</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Keterangan</label>
                        <textarea name="keterangan" rows="3"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>

                    <div class="mt-6 flex gap-2">
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Simpan</button>
                        <button type="reset" id="btn-reset"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">Batal/Clear</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Riwayat Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg run-animation p-6">
        <h3 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-200">Riwayat Pengajuan</h3>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700 text-xs text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        <th class="px-6 py-4 font-semibold border-b border-gray-100 dark:border-gray-600">No</th>
                        <th class="px-6 py-4 font-semibold border-b border-gray-100 dark:border-gray-600">NIP</th>
                        <th class="px-6 py-4 font-semibold border-b border-gray-100 dark:border-gray-600">Nama</th>
                        <th class="px-6 py-4 font-semibold border-b border-gray-100 dark:border-gray-600">Tanggal Surat</th>
                        <th class="px-6 py-4 font-semibold border-b border-gray-100 dark:border-gray-600">Jenis</th>
                        <th class="px-6 py-4 font-semibold border-b border-gray-100 dark:border-gray-600">Unit Kerja</th>
                         <th class="px-6 py-4 font-semibold border-b border-gray-100 dark:border-gray-600 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($data as $index => $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $data->firstItem() + $index }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $item->nip }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $item->nama }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $item->tanggal_surat->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->jenis_pengajuan === 'Penggugat' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' }}">
                                {{ $item->jenis_pengajuan }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $item->unit_kerja }}</td>
                        <td class="px-6 py-4 text-center text-sm">
                            <form action="{{ route('admin.pengajuan-cerai.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-red-600 hover:bg-red-100 dark:text-red-400 dark:hover:bg-red-900/50 rounded-lg transition-colors" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 italic">Belum ada data pengajuan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
            {{ $data->links() }}
        </div>

            <!-- Cetak Section -->
            <hr class="my-6 border-gray-200 dark:border-gray-700">
            <h4 class="text-md font-semibold mb-3 text-gray-700 dark:text-gray-200">Cetak Laporan</h4>
            <div class="flex flex-col md:flex-row gap-4 items-end">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dari Tanggal</label>
                    <input type="date" id="start_date"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sampai Tanggal</label>
                    <input type="date" id="end_date"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <button onclick="printReport()"
                        class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-600 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                            </path>
                        </svg>
                        Cetak / PDF
                    </button>
                </div>
                <div>
                    <button onclick="exportExcel()"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Export Excel
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Autocomplete Logic
        const nipInput = document.getElementById('nip');
        const suggestionsBox = document.getElementById('nip-suggestions');
        let timeoutId;

        nipInput.addEventListener('input', function () {
            clearTimeout(timeoutId);
            const term = this.value;
            if (term.length < 3) {
                suggestionsBox.classList.add('hidden');
                return;
            }

            timeoutId = setTimeout(() => {
                fetch(`{{ route('admin.pengajuan-cerai.search') }}?term=${term}`)
                    .then(response => response.json())
                    .then(data => {
                        suggestionsBox.innerHTML = '';
                        if (data.length > 0) {
                            data.forEach(item => {
                                const div = document.createElement('div');
                                div.className = 'px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer text-sm text-gray-700 dark:text-gray-200';
                                div.innerHTML = `<strong>${item.nip}</strong> - ${item.nama}`;
                                div.addEventListener('click', () => {
                                    fillForm(item);
                                    suggestionsBox.classList.add('hidden');
                                });
                                suggestionsBox.appendChild(div);
                            });
                            suggestionsBox.classList.remove('hidden');
                        } else {
                            suggestionsBox.classList.add('hidden');
                        }
                    });
            }, 300);
        });

        // Hide suggestions when clicking outside
        document.addEventListener('click', function (e) {
            if (e.target !== nipInput && e.target !== suggestionsBox) {
                suggestionsBox.classList.add('hidden');
            }
        });

        function fillForm(data) {
            document.getElementById('nip').value = data.nip;
            document.getElementById('nama').value = data.nama;
            document.getElementById('jabatan').value = data.jabatan;
            document.getElementById('unit_kerja').value = data.unit_kerja;
            document.getElementById('opd').value = data.opd;
        }

        // Reset Form Logic
        document.getElementById('btn-reset').addEventListener('click', function () {
            document.getElementById('nama').value = '';
            document.getElementById('jabatan').value = '';
            document.getElementById('unit_kerja').value = '';
            document.getElementById('opd').value = '';
        });

        function getParams() {
            const start = document.getElementById('start_date').value;
            const end = document.getElementById('end_date').value;
            return `?start_date=${start}&end_date=${end}`;
        }

        function printReport() {
            window.open(`{{ route('admin.pengajuan-cerai.print') }}${getParams()}`, '_blank');
        }

        function exportExcel() {
            window.location.href = `{{ route('admin.pengajuan-cerai.export.excel') }}${getParams()}`;
        }
    </script>
@endsection