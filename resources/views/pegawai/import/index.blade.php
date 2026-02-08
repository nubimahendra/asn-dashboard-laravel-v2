@extends('layouts.app')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="container mx-auto px-6 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Master Pegawai</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Data pegawai dan import dari file Excel (XLSX)</p>
        </div>

        <!-- Data Display Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Data Pegawai</h2>

            <!-- Search and Filter Section -->
            <div class="mb-6 space-y-4">
                <!-- Search Bar -->
                <div class="flex gap-4">
                    <div class="flex-1">
                        <input type="text" id="pegawai-search-input"
                            placeholder="Cari berdasarkan NIP, Nama, Tempat Lahir, Alamat, No HP, Email..."
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <button id="reset-filter-btn"
                        class="px-6 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors duration-200">
                        Reset Filter
                    </button>
                </div>

                <!-- Filter Dropdowns -->
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jenis Kelamin</label>
                        <select id="filter-jenis-kelamin"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-300 focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua</option>
                            <option value="M">Laki-laki</option>
                            <option value="F">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Agama</label>
                        <select id="filter-agama"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-300 focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jenis Kawin</label>
                        <select id="filter-jenis-kawin"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-300 focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jenis Jabatan</label>
                        <select id="filter-jenis-jabatan"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-300 focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tingkat
                            Pendidikan</label>
                        <select id="filter-tingkat-pendidikan"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-300 focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lokasi Kerja</label>
                        <select id="filter-lokasi-kerja"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-300 focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                NIP</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Nama Lengkap</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Jenis Kelamin</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Tanggal Lahir</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Tempat Lahir</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Alamat</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Nomor HP</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Agama</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Jenis Kawin</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Jenis Jabatan</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Tingkat Pendidikan</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Lokasi Kerja</th>
                        </tr>
                    </thead>
                    <tbody id="pegawai-table-body"
                        class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr>
                            <td colspan="12" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                <svg class="animate-spin inline-block w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Memuat data...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div id="pagination-container" class="mt-4 flex justify-between items-center">
                <div id="pagination-info" class="text-sm text-gray-600 dark:text-gray-400"></div>
                <div id="pagination-links" class="flex gap-2"></div>
            </div>
        </div>

        <!-- Upload Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Upload File Excel (XLSX)</h2>

            <form id="upload-form" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Pilih File Excel (XLSX)
                    </label>
                    <input type="file" id="excel-file" name="file" accept=".xlsx,.xls"
                        class="block w-full text-sm text-gray-900 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Format: File Excel (.xlsx atau .xls). Maksimal 10MB.
                    </p>
                </div>

                <button type="submit" id="upload-btn"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span id="upload-text">Upload & Proses</span>
                    <span id="upload-loading" class="hidden">
                        <svg class="animate-spin inline-block w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Uploading...
                    </span>
                </button>
            </form>

            <!-- Upload Status -->
            <div id="upload-status" class="mt-4 hidden"></div>
        </div>

        <!-- Import History Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Riwayat Import</h2>
                <button id="refresh-btn"
                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                        </path>
                    </svg>
                    Refresh
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Nama File</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Waktu Upload</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Jumlah Baris</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Error</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Progress</th>
                        </tr>
                    </thead>
                    <tbody id="history-table-body"
                        class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                <svg class="animate-spin inline-block w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Memuat data...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        // Pegawai Data Table Elements
        const pegawaiSearchInput = document.getElementById('pegawai-search-input');
        const filterJenisKelamin = document.getElementById('filter-jenis-kelamin');
        const filterAgama = document.getElementById('filter-agama');
        const filterJenisKawin = document.getElementById('filter-jenis-kawin');
        const filterJenisJabatan = document.getElementById('filter-jenis-jabatan');
        const filterTingkatPendidikan = document.getElementById('filter-tingkat-pendidikan');
        const filterLokasiKerja = document.getElementById('filter-lokasi-kerja');
        const resetFilterBtn = document.getElementById('reset-filter-btn');
        const pegawaiTableBody = document.getElementById('pegawai-table-body');
        const paginationInfo = document.getElementById('pagination-info');
        const paginationLinks = document.getElementById('pagination-links');

        // Upload Form Elements
        const uploadForm = document.getElementById('upload-form');
        const uploadBtn = document.getElementById('upload-btn');
        const uploadText = document.getElementById('upload-text');
        const uploadLoading = document.getElementById('upload-loading');
        const uploadStatus = document.getElementById('upload-status');
        const historyTableBody = document.getElementById('history-table-body');
        const refreshBtn = document.getElementById('refresh-btn');

        let currentPage = 1;
        let searchTimeout = null;


        // Load filter options
        async function loadFilterOptions() {
            try {
                const response = await fetch('{{ route('pegawai.import.filter-options') }}');
                const data = await response.json();

                // Populate Agama dropdown
                data.agama.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.nama;
                    filterAgama.appendChild(option);
                });

                // Populate Jenis Kawin dropdown
                data.jenis_kawin.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.nama;
                    filterJenisKawin.appendChild(option);
                });

                // Populate Jenis Jabatan dropdown
                data.jenis_jabatan.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.nama;
                    filterJenisJabatan.appendChild(option);
                });

                // Populate Tingkat Pendidikan dropdown
                data.tingkat_pendidikan.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.nama;
                    filterTingkatPendidikan.appendChild(option);
                });

                // Populate Lokasi Kerja dropdown
                data.lokasi_kerja.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.nama;
                    filterLokasiKerja.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading filter options:', error);
            }
        }

        // Load pegawai data
        async function loadPegawaiData(page = 1) {
            try {
                const params = new URLSearchParams({
                    page: page,
                    search: pegawaiSearchInput.value,
                    jenis_kelamin: filterJenisKelamin.value,
                    agama_id: filterAgama.value,
                    jenis_kawin_id: filterJenisKawin.value,
                    jenis_jabatan_id: filterJenisJabatan.value,
                    tingkat_pendidikan_id: filterTingkatPendidikan.value,
                    lokasi_kerja_id: filterLokasiKerja.value,
                });

                const response = await fetch(`{{ route('pegawai.import.data') }}?${params}`);
                const data = await response.json();

                if (data.data.length === 0) {
                    pegawaiTableBody.innerHTML = `
                                                    <tr>
                                                        <td colspan="12" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                                            Tidak ada data pegawai
                                                        </td>
                                                    </tr>
                                                `;
                    paginationInfo.textContent = '';
                    paginationLinks.innerHTML = '';
                    return;
                }

                // Render table rows
                pegawaiTableBody.innerHTML = data.data.map(pegawai => {
                    const namaLengkap = [
                        pegawai.gelar_depan,
                        pegawai.nama,
                        pegawai.gelar_belakang
                    ].filter(Boolean).join(' ');

                    const jenisKelamin = pegawai.jenis_kelamin === 'M' ? 'Laki-laki' :
                        pegawai.jenis_kelamin === 'F' ? 'Perempuan' : '-';

                    const tanggalLahir = pegawai.tanggal_lahir ?
                        new Date(pegawai.tanggal_lahir).toLocaleDateString('id-ID') : '-';

                    return `
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-300">${pegawai.nip_baru || '-'}</td>
                                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-300">${namaLengkap || '-'}</td>
                                                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">${jenisKelamin}</td>
                                                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">${tanggalLahir}</td>
                                                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">${pegawai.tempat_lahir || '-'}</td>
                                                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">${pegawai.alamat || '-'}</td>
                                                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">${pegawai.no_hp || '-'}</td>
                                                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">${pegawai.agama?.nama || '-'}</td>
                                                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">${pegawai.jenis_kawin?.nama || '-'}</td>
                                                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">${pegawai.jenis_jabatan?.nama || '-'}</td>
                                                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">${pegawai.tingkat_pendidikan?.nama || '-'}</td>
                                                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">${pegawai.lokasi_kerja?.nama || '-'}</td>
                                                    </tr>
                                                `;
                }).join('');

                // Update pagination info
                paginationInfo.textContent = `Menampilkan ${data.from || 0} - ${data.to || 0} dari ${data.total} data`;

                // Render pagination links
                renderPagination(data);

            } catch (error) {
                console.error('Error loading pegawai data:', error);
                pegawaiTableBody.innerHTML = `
                                                <tr>
                                                    <td colspan="12" class="px-6 py-4 text-center text-red-500 dark:text-red-400">
                                                        Gagal memuat data pegawai
                                                    </td>
                                                </tr>
                                            `;
            }
        }

        // Render pagination
        function renderPagination(data) {
            paginationLinks.innerHTML = '';

            if (data.last_page <= 1) return;

            // Previous button
            if (data.current_page > 1) {
                const prevBtn = createPaginationButton('‹', data.current_page - 1);
                paginationLinks.appendChild(prevBtn);
            }

            // Page numbers
            const startPage = Math.max(1, data.current_page - 2);
            const endPage = Math.min(data.last_page, data.current_page + 2);

            if (startPage > 1) {
                paginationLinks.appendChild(createPaginationButton(1, 1));
                if (startPage > 2) {
                    const dots = document.createElement('span');
                    dots.className = 'px-3 py-1 text-gray-500 dark:text-gray-400';
                    dots.textContent = '...';
                    paginationLinks.appendChild(dots);
                }
            }

            for (let i = startPage; i <= endPage; i++) {
                paginationLinks.appendChild(createPaginationButton(i, i, i === data.current_page));
            }

            if (endPage < data.last_page) {
                if (endPage < data.last_page - 1) {
                    const dots = document.createElement('span');
                    dots.className = 'px-3 py-1 text-gray-500 dark:text-gray-400';
                    dots.textContent = '...';
                    paginationLinks.appendChild(dots);
                }
                paginationLinks.appendChild(createPaginationButton(data.last_page, data.last_page));
            }

            // Next button
            if (data.current_page < data.last_page) {
                const nextBtn = createPaginationButton('›', data.current_page + 1);
                paginationLinks.appendChild(nextBtn);
            }
        }

        // Create pagination button
        function createPaginationButton(text, page, isActive = false) {
            const button = document.createElement('button');
            button.textContent = text;
            button.className = isActive
                ? 'px-3 py-1 bg-blue-600 text-white rounded-lg'
                : 'px-3 py-1 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600';

            if (!isActive) {
                button.addEventListener('click', () => {
                    currentPage = page;
                    loadPegawaiData(page);
                });
            }

            return button;
        }

        // Search with debounce
        pegawaiSearchInput.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentPage = 1;
                loadPegawaiData(1);
            }, 500);
        });

        // Filter change handlers
        [filterJenisKelamin, filterAgama, filterJenisKawin, filterJenisJabatan,
            filterTingkatPendidikan, filterLokasiKerja].forEach(filter => {
                filter.addEventListener('change', () => {
                    currentPage = 1;
                    loadPegawaiData(1);
                });
            });

        // Reset filter button
        resetFilterBtn.addEventListener('click', () => {
            pegawaiSearchInput.value = '';
            filterJenisKelamin.value = '';
            filterAgama.value = '';
            filterJenisKawin.value = '';
            filterJenisJabatan.value = '';
            filterTingkatPendidikan.value = '';
            filterLokasiKerja.value = '';
            currentPage = 1;
            loadPegawaiData(1);
        });

        // Handle file upload
        uploadForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(uploadForm);

            // Show loading state
            uploadBtn.disabled = true;
            uploadText.classList.add('hidden');
            uploadLoading.classList.remove('hidden');
            uploadStatus.classList.add('hidden');

            try {
                const response = await fetch('{{ route('pegawai.import.upload') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                // Check if response is ok
                if (!response.ok) {
                    const text = await response.text();
                    console.error('Server response:', text);
                    throw new Error(`Server error: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    showStatus('success', data.message + ` (${data.record_count} baris)`);
                    uploadForm.reset();
                    loadPegawaiData();
                    loadHistory();
                } else {
                    // Display error with details if available
                    let errorMessage = data.message;
                    if (data.errors && data.errors.length > 0) {
                        errorMessage += ':\n' + data.errors.slice(0, 5).join('\n');
                        if (data.errors.length > 5) {
                            errorMessage += `\n... dan ${data.errors.length - 5} error lainnya`;
                        }
                    }
                    if (data.error_detail) {
                        errorMessage += `\n\nDetail: ${data.error_detail.type} di ${data.error_detail.file}:${data.error_detail.line}`;
                    }
                    showStatus('error', errorMessage);
                }
            } catch (error) {
                showStatus('error', 'Terjadi kesalahan saat mengupload file: ' + error.message);
                console.error('Upload error:', error);
            } finally {
                uploadBtn.disabled = false;
                uploadText.classList.remove('hidden');
                uploadLoading.classList.add('hidden');
            }
        });

        // Show status message
        function showStatus(type, message) {
            uploadStatus.classList.remove('hidden');
            uploadStatus.className = 'mt-4 p-4 rounded-lg ' +
                (type === 'success'
                    ? 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 border border-green-400 dark:border-green-700'
                    : 'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 border border-red-400 dark:border-red-700');
            uploadStatus.textContent = message;

            setTimeout(() => {
                uploadStatus.classList.add('hidden');
            }, 5000);
        }

        // Load import history
        async function loadHistory() {
            try {
                const response = await fetch('{{ route('pegawai.import.history') }}');
                const data = await response.json();

                if (data.length === 0) {
                    historyTableBody.innerHTML = `
                                                                                    <tr>
                                                                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                                                                            Belum ada riwayat import
                                                                                        </td>
                                                                                    </tr>
                                                                                `;
                    return;
                }

                historyTableBody.innerHTML = data.map(item => `
                                                                        <tr>
                                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                                                                ${item.filename}
                                                                            </td>
                                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                                                ${item.uploaded_at}
                                                                            </td>
                                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                                                ${item.total_rows}
                                                                            </td>
                                                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                                                ${getErrorBadge(item.total_error_rows, item.import_error_rows, item.processing_error_rows)}
                                                                            </td>
                                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                                ${getStatusBadge(item.status)}
                                                                            </td>
                                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                                ${getProgressBar(item.progress, item.status)}
                                                                            </td>
                                                                        </tr>
                                                                    `).join('');
            } catch (error) {
                console.error('Error loading history:', error);
                historyTableBody.innerHTML = `
                                                                                <tr>
                                                                                    <td colspan="6" class="px-6 py-4 text-center text-red-500 dark:text-red-400">
                                                                                        Gagal memuat riwayat import
                                                                                    </td>
                                                                                </tr>
                                                                            `;
            }
        }

        // Get status badge HTML
        function getStatusBadge(status) {
            const badges = {
                'Menunggu': '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300">Menunggu</span>',
                'Diproses': '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300">Diproses</span>',
                'Selesai': '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300">Selesai</span>',
                'Gagal': '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300">Gagal</span>'
            };
            return badges[status] || status;
        }

        // Get error badge HTML
        function getErrorBadge(totalErrors, importErrors, processingErrors) {
            if (totalErrors === 0) {
                return '<span class="text-gray-500 dark:text-gray-400">-</span>';
            }

            let tooltip = '';
            if (importErrors > 0 && processingErrors > 0) {
                tooltip = `Import: ${importErrors}, Processing: ${processingErrors}`;
            } else if (importErrors > 0) {
                tooltip = `Import error: ${importErrors}`;
            } else {
                tooltip = `Processing error: ${processingErrors}`;
            }

            return `<span class="text-red-600 dark:text-red-400 font-semibold" title="${tooltip}">${totalErrors}</span>`;
        }

        // Get progress bar HTML
        function getProgressBar(progress, status) {
            if (status === 'Selesai') {
                return '<span class="text-sm text-green-600 dark:text-green-400 font-semibold">100%</span>';
            }
            if (status === 'Gagal') {
                return '<span class="text-sm text-red-600 dark:text-red-400">-</span>';
            }
            return `
                                                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                                                        <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" style="width: ${progress}%"></div>
                                                                    </div>
                                                                    <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">${progress}%</span>
                                                                `;
        }

        // Refresh button
        refreshBtn.addEventListener('click', loadHistory);

        // Auto-refresh every 5 seconds
        setInterval(loadHistory, 5000);

        // Initial load
        loadFilterOptions();
        loadPegawaiData();
        loadHistory();
    </script>
@endsection