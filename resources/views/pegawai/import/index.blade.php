@extends('layouts.app')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="container mx-auto px-6 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Master Pegawai</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Import dan sinkronisasi data pegawai dari file CSV</p>
        </div>

        <!-- Upload Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Upload File CSV</h2>

            <form id="upload-form" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Pilih File CSV (Delimiter: |)
                    </label>
                    <input type="file" id="csv-file" name="file" accept=".csv,.txt"
                        class="block w-full text-sm text-gray-900 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Format: CSV dengan delimiter pipe (|). Maksimal 2MB (dapat ditingkatkan di konfigurasi server).
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
        const uploadForm = document.getElementById('upload-form');
        const uploadBtn = document.getElementById('upload-btn');
        const uploadText = document.getElementById('upload-text');
        const uploadLoading = document.getElementById('upload-loading');
        const uploadStatus = document.getElementById('upload-status');
        const historyTableBody = document.getElementById('history-table-body');
        const refreshBtn = document.getElementById('refresh-btn');

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
                    showStatus('error', data.message);
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
                                    <td colspan="5" class="px-6 py-4 text-center text-red-500 dark:text-red-400">
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