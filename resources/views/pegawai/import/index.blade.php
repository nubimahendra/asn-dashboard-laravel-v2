@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Master Pegawai</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Cari profil pegawai dan import data dari file Excel (XLSX)</p>
    </div>

    <!-- Employee Search Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Cari Profil Pegawai</h2>

        <!-- Search Bar -->
        <div class="mb-4">
            <div class="relative">
                <input type="text" id="employee-search-input" placeholder="Cari berdasarkan NIP atau Nama..."
                    class="w-full px-4 py-3 pl-12 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <svg class="absolute left-4 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>

        <!-- Search Results -->
        <div id="search-results"
            class="hidden mt-4 bg-gray-50 dark:bg-gray-700 rounded-lg p-4 max-h-96 overflow-y-auto">
            <div id="search-results-list"></div>
        </div>

        <!-- No Results Message -->
        <div id="no-results" class="hidden mt-4 text-center text-gray-500 dark:text-gray-400 py-8">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-lg font-medium">Tidak ada hasil ditemukan</p>
            <p class="text-sm mt-1">Coba gunakan kata kunci lain</p>
        </div>
    </div>

    <!-- Employee Profile Section -->
    <div id="profile-section" class="hidden bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Profil Pegawai</h2>
            <button id="clear-profile-btn"
                class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors">
                Tutup
            </button>
        </div>

        <!-- Profile Card -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-600 rounded-lg p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Basic Info -->
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Informasi Dasar</h3>
                    <div class="space-y-2">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Nama Lengkap</p>
                            <p id="profile-nama" class="font-semibold text-gray-900 dark:text-gray-100"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">NIP Baru</p>
                            <p id="profile-nip-baru" class="font-medium text-gray-700 dark:text-gray-300"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">NIP Lama</p>
                            <p id="profile-nip-lama" class="font-medium text-gray-700 dark:text-gray-300"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">NIK</p>
                            <p id="profile-nik" class="font-medium text-gray-700 dark:text-gray-300"></p>
                        </div>
                    </div>
                </div>

                <!-- Personal Info -->
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Data Pribadi</h3>
                    <div class="space-y-2">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Jenis Kelamin</p>
                            <p id="profile-jenis-kelamin" class="font-medium text-gray-700 dark:text-gray-300"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Tempat, Tanggal Lahir</p>
                            <p id="profile-lahir" class="font-medium text-gray-700 dark:text-gray-300"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Agama</p>
                            <p id="profile-agama" class="font-medium text-gray-700 dark:text-gray-300"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Status Perkawinan</p>
                            <p id="profile-jenis-kawin" class="font-medium text-gray-700 dark:text-gray-300"></p>
                        </div>
                    </div>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Kontak</h3>
                    <div class="space-y-2">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Nomor HP</p>
                            <p id="profile-no-hp" class="font-medium text-gray-700 dark:text-gray-300"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Email</p>
                            <p id="profile-email" class="font-medium text-gray-700 dark:text-gray-300"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Alamat</p>
                            <p id="profile-alamat" class="font-medium text-gray-700 dark:text-gray-300"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Employment Info -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Informasi Kepegawaian</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Jenis Pegawai</p>
                    <p id="profile-jenis-pegawai" class="font-medium text-gray-700 dark:text-gray-300"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Status CPNS/PNS</p>
                    <p id="profile-status-cpns-pns" class="font-medium text-gray-700 dark:text-gray-300"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Kedudukan Hukum</p>
                    <p id="profile-kedudukan-hukum" class="font-medium text-gray-700 dark:text-gray-300"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Golongan</p>
                    <p id="profile-golongan" class="font-medium text-gray-700 dark:text-gray-300"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Jabatan</p>
                    <p id="profile-jabatan" class="font-medium text-gray-700 dark:text-gray-300"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Jenis Jabatan</p>
                    <p id="profile-jenis-jabatan" class="font-medium text-gray-700 dark:text-gray-300"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Unit Organisasi</p>
                    <p id="profile-unor" class="font-medium text-gray-700 dark:text-gray-300"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Tingkat Pendidikan</p>
                    <p id="profile-tingkat-pendidikan" class="font-medium text-gray-700 dark:text-gray-300"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Pendidikan</p>
                    <p id="profile-pendidikan" class="font-medium text-gray-700 dark:text-gray-300"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Lokasi Kerja</p>
                    <p id="profile-lokasi-kerja" class="font-medium text-gray-700 dark:text-gray-300"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Instansi Kerja</p>
                    <p id="profile-instansi-kerja" class="font-medium text-gray-700 dark:text-gray-300"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">TMT CPNS</p>
                    <p id="profile-tmt-cpns" class="font-medium text-gray-700 dark:text-gray-300"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">TMT PNS</p>
                    <p id="profile-tmt-pns" class="font-medium text-gray-700 dark:text-gray-300"></p>
                </div>
            </div>
        </div>

        <!-- History Tabs -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Riwayat</h3>

            <!-- Tab Navigation -->
            <div class="flex flex-wrap gap-2 mb-4">
                <button class="tab-btn px-4 py-2 rounded-lg font-medium bg-blue-600 text-white" data-tab="golongan">
                    Riwayat Golongan
                </button>
                <button
                    class="tab-btn px-4 py-2 rounded-lg font-medium bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300"
                    data-tab="jabatan">
                    Riwayat Jabatan
                </button>
                <button
                    class="tab-btn px-4 py-2 rounded-lg font-medium bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300"
                    data-tab="pendidikan">
                    Riwayat Pendidikan
                </button>
                <button
                    class="tab-btn px-4 py-2 rounded-lg font-medium bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300"
                    data-tab="status">
                    Riwayat Status
                </button>
            </div>

            <!-- Tab Content -->
            <!-- Riwayat Golongan -->
            <div id="tab-golongan" class="tab-content">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    TMT</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Golongan</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    MK Tahun</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    MK Bulan</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Keterangan</th>
                            </tr>
                        </thead>
                        <tbody id="riwayat-golongan-body"
                            class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Riwayat Jabatan -->
            <div id="tab-jabatan" class="tab-content hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    TMT</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Jabatan</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Jenis Jabatan</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Unit Organisasi</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Keterangan</th>
                            </tr>
                        </thead>
                        <tbody id="riwayat-jabatan-body"
                            class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Riwayat Pendidikan -->
            <div id="tab-pendidikan" class="tab-content hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Tahun Lulus</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Tingkat</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Pendidikan</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Institusi</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Keterangan</th>
                            </tr>
                        </thead>
                        <tbody id="riwayat-pendidikan-body"
                            class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Riwayat Status -->
            <div id="tab-status" class="tab-content hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    TMT</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Status</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Keterangan</th>
                            </tr>
                        </thead>
                        <tbody id="riwayat-status-body"
                            class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        </tbody>
                    </table>
                </div>
            </div>
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
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
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
    // Employee Search Elements
    const employeeSearchInput = document.getElementById('employee-search-input');
    const searchResults = document.getElementById('search-results');
    const searchResultsList = document.getElementById('search-results-list');
    const noResults = document.getElementById('no-results');
    const profileSection = document.getElementById('profile-section');
    const clearProfileBtn = document.getElementById('clear-profile-btn');

    // Upload Form Elements
    const uploadForm = document.getElementById('upload-form');
    const uploadBtn = document.getElementById('upload-btn');
    const uploadText = document.getElementById('upload-text');
    const uploadLoading = document.getElementById('upload-loading');
    const uploadStatus = document.getElementById('upload-status');
    const historyTableBody = document.getElementById('history-table-body');
    const refreshBtn = document.getElementById('refresh-btn');

    let searchTimeout = null;

    // Search for employees
    employeeSearchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        const query = employeeSearchInput.value.trim();

        if (query.length < 2) {
            searchResults.classList.add('hidden');
            noResults.classList.add('hidden');
            return;
        }

        searchTimeout = setTimeout(async () => {
            try {
                const response = await fetch(`{{ route('pegawai.import.search-employee') }}?query=${encodeURIComponent(query)}`);
                const employees = await response.json();

                if (employees.length === 0) {
                    searchResults.classList.add('hidden');
                    noResults.classList.remove('hidden');
                    return;
                }

                noResults.classList.add('hidden');
                searchResults.classList.remove('hidden');
                searchResultsList.innerHTML = employees.map(emp => `
                        <div class="search-result-item p-3 mb-2 bg-white dark:bg-gray-800 rounded-lg cursor-pointer hover:bg-blue-50 dark:hover:bg-gray-600 transition-colors"
                            data-employee-id="${emp.id}">
                            <div class="font-medium text-gray-900 dark:text-gray-100">${emp.nama_lengkap}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">NIP: ${emp.nip_baru || '-'}</div>
                        </div>
                    `).join('');

                // Add click handlers to search results
                document.querySelectorAll('.search-result-item').forEach(item => {
                    item.addEventListener('click', () => {
                        const employeeId = item.getAttribute('data-employee-id');
                        loadEmployeeProfile(employeeId);
                        searchResults.classList.add('hidden');
                        employeeSearchInput.value = '';
                    });
                });

            } catch (error) {
                console.error('Search error:', error);
            }
        }, 500);
    });

    // Load employee profile
    async function loadEmployeeProfile(employeeId) {
        try {
            const response = await fetch(`{{ route('pegawai.import.index') }}/profile/${employeeId}`);
            const data = await response.json();

            if (data.error) {
                alert('Gagal memuat profil pegawai');
                return;
            }

            displayProfile(data.profile);
            displayHistory(data.riwayat);
            profileSection.classList.remove('hidden');

            // Scroll to profile section
            profileSection.scrollIntoView({ behavior: 'smooth', block: 'start' });

        } catch (error) {
            console.error('Error loading profile:', error);
            alert('Terjadi kesalahan saat memuat profil pegawai');
        }
    }

    // Display employee profile
    function displayProfile(profile) {
        document.getElementById('profile-nama').textContent = profile.nama_lengkap || '-';
        document.getElementById('profile-nip-baru').textContent = profile.nip_baru || '-';
        document.getElementById('profile-nip-lama').textContent = profile.nip_lama || '-';
        document.getElementById('profile-nik').textContent = profile.nik || '-';

        const jenisKelamin = profile.jenis_kelamin === 'M' ? 'Laki-laki' :
            profile.jenis_kelamin === 'F' ? 'Perempuan' : '-';
        document.getElementById('profile-jenis-kelamin').textContent = jenisKelamin;

        const lahir = [profile.tempat_lahir, profile.tanggal_lahir].filter(Boolean).join(', ');
        document.getElementById('profile-lahir').textContent = lahir || '-';

        document.getElementById('profile-agama').textContent = profile.agama || '-';
        document.getElementById('profile-jenis-kawin').textContent = profile.jenis_kawin || '-';
        document.getElementById('profile-no-hp').textContent = profile.no_hp || '-';
        document.getElementById('profile-email').textContent = profile.email || '-';
        document.getElementById('profile-alamat').textContent = profile.alamat || '-';

        // Kepegawaian info
        document.getElementById('profile-jenis-pegawai').textContent = profile.jenis_pegawai || '-';
        document.getElementById('profile-status-cpns-pns').textContent = profile.status_cpns_pns || '-';
        document.getElementById('profile-kedudukan-hukum').textContent = profile.kedudukan_hukum || '-';
        document.getElementById('profile-golongan').textContent = profile.golongan || '-';
        document.getElementById('profile-jabatan').textContent = profile.jabatan || '-';
        document.getElementById('profile-jenis-jabatan').textContent = profile.jenis_jabatan || '-';
        document.getElementById('profile-unor').textContent = profile.unor || '-';
        document.getElementById('profile-tingkat-pendidikan').textContent = profile.tingkat_pendidikan || '-';
        document.getElementById('profile-pendidikan').textContent = profile.pendidikan || '-';
        document.getElementById('profile-lokasi-kerja').textContent = profile.lokasi_kerja || '-';
        document.getElementById('profile-instansi-kerja').textContent = profile.instansi_kerja || '-';
        document.getElementById('profile-tmt-cpns').textContent = profile.tmt_cpns || '-';
        document.getElementById('profile-tmt-pns').textContent = profile.tmt_pns || '-';
    }

    // Display employee history
    function displayHistory(riwayat) {
        // Riwayat Golongan
        const golonganBody = document.getElementById('riwayat-golongan-body');
        if (riwayat.golongan.length === 0) {
            golonganBody.innerHTML = '<tr><td colspan="5" class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">Tidak ada riwayat golongan</td></tr>';
        } else {
            golonganBody.innerHTML = riwayat.golongan.map(item => `
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-300">${item.tmt || '-'}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-300">${item.golongan || '-'}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">${item.mk_tahun || '-'}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">${item.mk_bulan || '-'}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">${item.keterangan || '-'}</td>
                    </tr>
                `).join('');
        }

        // Riwayat Jabatan
        const jabatanBody = document.getElementById('riwayat-jabatan-body');
        if (riwayat.jabatan.length === 0) {
            jabatanBody.innerHTML = '<tr><td colspan="5" class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">Tidak ada riwayat jabatan</td></tr>';
        } else {
            jabatanBody.innerHTML = riwayat.jabatan.map(item => `
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-300">${item.tmt || '-'}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-300">${item.jabatan || '-'}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">${item.jenis_jabatan || '-'}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">${item.unor || '-'}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">${item.keterangan || '-'}</td>
                    </tr>
                `).join('');
        }

        // Riwayat Pendidikan
        const pendidikanBody = document.getElementById('riwayat-pendidikan-body');
        if (riwayat.pendidikan.length === 0) {
            pendidikanBody.innerHTML = '<tr><td colspan="5" class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">Tidak ada riwayat pendidikan</td></tr>';
        } else {
            pendidikanBody.innerHTML = riwayat.pendidikan.map(item => `
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-300">${item.tahun_lulus || '-'}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-300">${item.tingkat_pendidikan || '-'}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">${item.pendidikan || '-'}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">${item.institusi || '-'}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">${item.keterangan || '-'}</td>
                    </tr>
                `).join('');
        }

        // Riwayat Status
        const statusBody = document.getElementById('riwayat-status-body');
        if (riwayat.status.length === 0) {
            statusBody.innerHTML = '<tr><td colspan="3" class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">Tidak ada riwayat status</td></tr>';
        } else {
            statusBody.innerHTML = riwayat.status.map(item => `
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-300">${item.tmt || '-'}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-300">${item.status || '-'}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">${item.keterangan || '-'}</td>
                    </tr>
                `).join('');
        }
    }

    // Tab switching functionality
    document.querySelectorAll('.tab-btn').forEach(button => {
        button.addEventListener('click', () => {
            const tabName = button.getAttribute('data-tab');

            // Update button styles
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('bg-blue-600', 'text-white');
                btn.classList.add('bg-gray-200', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
            });
            button.classList.add('bg-blue-600', 'text-white');
            button.classList.remove('bg-gray-200', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');

            // Show/hide tab content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            document.getElementById(`tab-${tabName}`).classList.remove('hidden');
        });
    });

    // Clear profile button
    clearProfileBtn.addEventListener('click', () => {
        profileSection.classList.add('hidden');
        employeeSearchInput.value = '';
        searchResults.classList.add('hidden');
        noResults.classList.add('hidden');
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
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
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
    loadHistory();
</script>
@endsection