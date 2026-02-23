@extends('layouts.app')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="container mx-auto px-6 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Sync Data Pegawai</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Sinkronisasi data dengan upload file CSV (Delimiter |)</p>
        </div>

        <!-- Upload Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Upload File CSV (Delimiter |)</h2>

            <form id="upload-form" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Pilih File CSV
                    </label>
                    <input type="file" id="excel-file" name="file" accept=".csv"
                        class="block w-full text-sm text-gray-900 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Format: File CSV (.csv) dengan pemisah kolom (delimiter) berupa karakter pipa <code>|</code>.
                        Maksimal 50MB.
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

            <!-- Batch Summary Section -->
            <div id="batch-summary-section" class="mt-6 hidden border-t border-gray-200 dark:border-gray-700 pt-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Batch Summary</h3>
                <div
                    class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700 mb-4 sm:max-w-md">
                    <div class="grid grid-cols-2 pb-2 mb-2 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">Total Rows</span>
                        <span class="font-bold text-gray-800 dark:text-white text-right" id="batch-total">0</span>
                    </div>
                    <div class="grid grid-cols-2 pb-2 mb-2 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">Valid Rows</span>
                        <span class="font-bold text-green-600 dark:text-green-400 text-right" id="batch-valid">0</span>
                    </div>
                    <div class="grid grid-cols-2 pb-2 mb-2 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">Invalid Rows</span>
                        <span class="font-bold text-red-600 dark:text-red-400 text-right" id="batch-invalid">0</span>
                    </div>
                    <div class="grid grid-cols-2 pt-2">
                        <span class="text-gray-600 dark:text-gray-400 font-bold">Status</span>
                        <span class="text-right" id="batch-status-badge"></span>
                    </div>
                </div>

                <div id="batch-error-actions" class="flex flex-col sm:flex-row gap-3 mb-6 hidden">
                    <a id="download-errors-btn" href="#" target="_blank"
                        class="px-6 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-medium rounded-lg text-center transition-colors">
                        Download Error CSV
                    </a>
                    <button id="retry-batch-btn"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors flex items-center justify-center">
                        Retry Validation
                    </button>
                </div>
            </div>

            <!-- Diff Summary Section -->
            <div id="diff-section" class="mt-6 hidden border-t border-gray-200 dark:border-gray-700 pt-6">
                <!-- Copied from import index -->
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Hasil Analisis Perubahan</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
                        <div class="text-sm text-blue-600 dark:text-blue-400 font-medium">Data Baru</div>
                        <div class="text-2xl font-bold text-blue-700 dark:text-blue-300" id="count-new">0</div>
                        <div class="text-xs text-blue-500 dark:text-blue-400 mt-1">Pegawai baru akan ditambahkan</div>
                    </div>

                    <div
                        class="bg-yellow-50 dark:bg-yellow-900/30 p-4 rounded-lg border border-yellow-200 dark:border-yellow-800">
                        <div class="text-sm text-yellow-600 dark:text-yellow-400 font-medium">Data Berubah</div>
                        <div class="text-2xl font-bold text-yellow-700 dark:text-yellow-300" id="count-changed">0</div>
                        <div class="text-xs text-yellow-500 dark:text-yellow-400 mt-1">Pegawai dengan perubahan data</div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/30 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                        <div class="text-sm text-gray-600 dark:text-gray-400 font-medium">Tidak Berubah</div>
                        <div class="text-2xl font-bold text-gray-700 dark:text-gray-300" id="count-unchanged">0</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Data identik dengan database</div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button id="confirm-sync-btn"
                        class="flex-1 px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors flex items-center justify-center">
                        Konfirmasi & Sinkronisasi
                    </button>
                    <button id="show-details-btn"
                        class="px-6 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium rounded-lg transition-colors">
                        Lihat Detail Perubahan
                    </button>
                </div>

                <div id="diff-details-container" class="mt-6 hidden">
                    <h4 class="text-md font-semibold text-gray-700 dark:text-gray-200 mb-3">Detail Perubahan</h4>
                    <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg max-h-96">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800 sticky top-0">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                        Nama / NIP</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                        Status</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                        Perubahan</th>
                                </tr>
                            </thead>
                            <tbody id="diff-details-body"
                                class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Anomaly Details Modal -->
        <div id="anomaly-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeAnomalyModal()"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-gray-200 dark:border-gray-700">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                    Detail Anomali Data (<span id="anomaly-modal-filename" class="text-sm font-normal text-gray-500"></span>)
                                </h3>
                                <div class="mt-4">
                                    <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead class="bg-gray-50 dark:bg-gray-900">
                                                <tr>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">PNS ID</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama / NIP</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan Anomali</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rekomendasi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="anomaly-modal-body" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                                <!-- Content loaded via JS -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="anomaly-pagination" class="mt-4 flex justify-end"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200 dark:border-gray-600">
                        <button type="button" onclick="closeAnomalyModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Import History Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Riwayat Sinkronisasi (CSV)</h2>
                <button id="refresh-btn"
                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors duration-200">
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
                                Anomali</th>
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
                    </tbody>
                </table>
            </div>
            <div id="history-pagination" class="mt-4 flex justify-end"></div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Upload Form Elements
        const uploadForm = document.getElementById('upload-form');
        const uploadBtn = document.getElementById('upload-btn');
        const uploadText = document.getElementById('upload-text');
        const uploadLoading = document.getElementById('upload-loading');
        const uploadStatus = document.getElementById('upload-status');
        const historyTableBody = document.getElementById('history-table-body');
        const refreshBtn = document.getElementById('refresh-btn');
        const historyPagination = document.getElementById('history-pagination');

        // Diff Elements
        const diffSection = document.getElementById('diff-section');
        const countNew = document.getElementById('count-new');
        const countChanged = document.getElementById('count-changed');
        const countUnchanged = document.getElementById('count-unchanged');
        const confirmSyncBtn = document.getElementById('confirm-sync-btn');
        const showDetailsBtn = document.getElementById('show-details-btn');
        const diffDetailsContainer = document.getElementById('diff-details-container');
        const diffDetailsBody = document.getElementById('diff-details-body');
        const loadMoreDetailsBtn = document.getElementById('load-more-details-btn');

        let currentUploadFilename = null;
        let detailsPage = 1;
        let currentHistoryPage = 1;

        // Handle file upload
        uploadForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(uploadForm);

            // Show loading state
            uploadBtn.disabled = true;
            uploadText.classList.add('hidden');
            uploadLoading.classList.remove('hidden');
            uploadStatus.classList.add('hidden');
            diffSection.classList.add('hidden'); // Hide diff section on new upload

            try {
                const response = await fetch('{{ route('pegawai.import.upload') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                let data;
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.indexOf("application/json") !== -1) {
                    data = await response.json();
                } else {
                    const text = await response.text();
                    console.error('Server response:', text);
                    throw new Error(`Server error: ${response.status}`);
                }

                if (response.ok && (data.success === true || data.success === undefined)) {
                    showStatus('success', data.message || 'File berhasil diupload');
                    uploadForm.reset();
                    loadHistory();

                    // Show Diff Summary if available
                    if (data.diff_summary) {
                        currentUploadFilename = data.filename;
                        showDiffSummary(data.diff_summary);
                    }

                    if (data.batch_summary && data.batch_id) {
                        currentBatchId = data.batch_id;
                        showBatchSummary(data.batch_summary);
                    }
                } else {
                    // Display error with details if available
                    let errorMessage = data.message || 'Terjadi kesalahan validasi atau server.';

                    if (data.errors) {
                        let errorList = [];
                        if (Array.isArray(data.errors)) {
                            errorList = data.errors;
                        } else if (typeof data.errors === 'object') {
                            for (let key in data.errors) {
                                errorList = errorList.concat(data.errors[key]);
                            }
                        }

                        if (errorList.length > 0) {
                            errorMessage += ':\n' + errorList.slice(0, 5).join('\n');
                            if (errorList.length > 5) {
                                errorMessage += `\n... dan ${errorList.length - 5} error lainnya`;
                            }
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

        // Show Diff Summary
        function showDiffSummary(summary) {
            diffSection.classList.remove('hidden');
            countNew.textContent = summary.new;
            countChanged.textContent = summary.changed;
            countUnchanged.textContent = summary.unchanged;

            // Reset details view
            diffDetailsContainer.classList.add('hidden');
            diffDetailsBody.innerHTML = '';
            showDetailsBtn.textContent = 'Lihat Detail Perubahan';

            // Enable/Disable confirm button based on changes
            if (summary.new === 0 && summary.changed === 0) {
                confirmSyncBtn.disabled = true;
                confirmSyncBtn.classList.add('opacity-50', 'cursor-not-allowed');
                confirmSyncBtn.title = "Tidak ada perubahan data";
            } else {
                confirmSyncBtn.disabled = false;
                confirmSyncBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                confirmSyncBtn.title = "";
            }
        }

        let currentBatchId = null;
        const batchSummarySection = document.getElementById('batch-summary-section');
        const batchTotal = document.getElementById('batch-total');
        const batchValid = document.getElementById('batch-valid');
        const batchInvalid = document.getElementById('batch-invalid');
        const batchStatusBadge = document.getElementById('batch-status-badge');
        const batchErrorActions = document.getElementById('batch-error-actions');
        const downloadErrorsBtn = document.getElementById('download-errors-btn');
        const retryBatchBtn = document.getElementById('retry-batch-btn');

        function showBatchSummary(summary) {
            batchSummarySection.classList.remove('hidden');
            batchTotal.textContent = summary.total.toLocaleString();
            batchValid.textContent = summary.valid.toLocaleString();
            batchInvalid.textContent = summary.invalid.toLocaleString();

            if (summary.status === 'failed') {
                batchStatusBadge.innerHTML = '<span class="px-3 py-1 text-xs font-bold rounded-full bg-red-100 text-red-800 uppercase tracking-wider">FAILED</span>';

                // Show actions
                batchErrorActions.classList.remove('hidden');
                downloadErrorsBtn.href = `{{ url('admin/sync-data/batch') }}/${currentBatchId}/errors`;

                // Disable Confirm Sync Button and override UI
                confirmSyncBtn.disabled = true;
                confirmSyncBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                confirmSyncBtn.classList.add('bg-red-600', 'opacity-50', 'cursor-not-allowed');
                confirmSyncBtn.textContent = 'Tidak dapat dilanjutkan';
                confirmSyncBtn.title = 'Perbaiki error terlebih dahulu sebelum melanjutkan';
            } else {
                batchStatusBadge.innerHTML = '<span class="px-3 py-1 text-xs font-bold rounded-full bg-green-100 text-green-800 uppercase tracking-wider">READY</span>';

                // Hide error actions
                batchErrorActions.classList.add('hidden');

                // Keep Confirm Sync button state based on diff summary alone, 
                // Restore its UI just in case it was red earlier
                confirmSyncBtn.classList.remove('bg-red-600');
                confirmSyncBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                confirmSyncBtn.textContent = 'Konfirmasi & Sinkronisasi';
            }
        }

        // Retry button handling
        if (retryBatchBtn) {
            retryBatchBtn.addEventListener('click', async () => {
                if (!currentBatchId) return;

                const originalText = retryBatchBtn.innerHTML;
                retryBatchBtn.disabled = true;
                retryBatchBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Retrying...';

                try {
                    const response = await fetch(`{{ url('admin/sync-data/batch') }}/${currentBatchId}/retry`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        showStatus('success', data.message || 'Retry berhasil.');
                        if (data.batch_summary) {
                            showBatchSummary(data.batch_summary);
                        }
                    } else {
                        showStatus('error', data.message || 'Gagal melakukan retry validation.');
                    }
                } catch (error) {
                    showStatus('error', 'Terjadi kesalahan saat retry: ' + error.message);
                } finally {
                    retryBatchBtn.disabled = false;
                    retryBatchBtn.innerHTML = originalText;
                }
            });
        }

        // Toggle Details
        showDetailsBtn.addEventListener('click', () => {
            if (diffDetailsContainer.classList.contains('hidden')) {
                diffDetailsContainer.classList.remove('hidden');
                showDetailsBtn.textContent = 'Sembunyikan Detail';
                loadDiffDetails(1, true);
            } else {
                diffDetailsContainer.classList.add('hidden');
                showDetailsBtn.textContent = 'Lihat Detail Perubahan';
            }
        });

        // Load Diff Details
        async function loadDiffDetails(page = 1, reset = false) {
            if (!currentUploadFilename) return;

            try {
                if (reset) {
                    diffDetailsBody.innerHTML = '<tr><td colspan="3" class="px-4 py-3 text-center text-gray-500">Memuat detail...</td></tr>';
                    detailsPage = 1;
                }

                // Call properly constructed URL
                const response = await fetch(`{{ url('admin/sync-data/diff-details') }}/${currentUploadFilename}?page=${page}&type=all`);
                const data = await response.json();

                if (reset) diffDetailsBody.innerHTML = '';

                if (data.data.length === 0) {
                    if (reset) diffDetailsBody.innerHTML = '<tr><td colspan="3" class="px-4 py-3 text-center text-gray-500">Tidak ada detail perubahan yang ditampilkan.</td></tr>';
                    loadMoreDetailsBtn.classList.add('hidden');
                    return;
                }

                data.data.forEach(item => {
                    let changesHtml = '';
                    if (item.status === 'new') {
                        changesHtml = '<span class="text-blue-600">Pegawai Baru</span>';
                    } else if (item.status === 'changed' && item.changes) {
                        changesHtml = '<ul class="list-disc list-inside text-xs">';
                        for (const [field, change] of Object.entries(item.changes)) {
                            changesHtml += `<li><span class="font-semibold">${change.label || field}:</span> 
                                    <span class="text-red-500 line-through">${change.old || '(kosong)'}</span> 
                                    <span class="text-gray-400">→</span> 
                                    <span class="text-green-600 font-medium">${change.new || '(kosong)'}</span></li>`;
                        }
                        changesHtml += '</ul>';
                    } else {
                        changesHtml = '<span class="text-gray-500">Tidak ada perubahan detail</span>';
                    }

                    const statusBadge = item.status === 'new'
                        ? '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Baru</span>'
                        : '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Berubah</span>';

                    const row = `
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-3">
                                     <div class="font-medium text-gray-900 dark:text-gray-100">${item.nama || '-'}</div>
                                     <div class="text-xs text-gray-500">${item.nip_baru || '-'}</div>
                                </td>
                                <td class="px-4 py-3">${statusBadge}</td>
                                <td class="px-4 py-3">${changesHtml}</td>
                            </tr>
                        `;
                    diffDetailsBody.insertAdjacentHTML('beforeend', row);
                });

                // Handle pagination
                if (data.next_page_url) {
                    loadMoreDetailsBtn.classList.remove('hidden');
                    loadMoreDetailsBtn.onclick = () => loadDiffDetails(page + 1, false);
                } else {
                    loadMoreDetailsBtn.classList.add('hidden');
                }

            } catch (error) {
                console.error('Error loading details:', error);
                if (reset) diffDetailsBody.innerHTML = '<tr><td colspan="3" class="px-4 py-3 text-center text-red-500">Gagal memuat detail.</td></tr>';
            }
        }

        // Confirm Sync
        confirmSyncBtn.addEventListener('click', async () => {
            if (!confirm('Apakah Anda yakin ingin menyinkronkan data ini ke database utama?')) return;

            confirmSyncBtn.disabled = true;
            confirmSyncBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memproses...';

            try {
                const response = await fetch('{{ route('pegawai.import.confirm-sync') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ filename: currentUploadFilename })
                });

                const data = await response.json();

                if (data.success) {
                    alert('Sinkronisasi dimulai! Data sedang berjalan di background.');
                    diffSection.classList.add('hidden');
                    loadHistory();

                    currentUploadFilename = null;
                } else {
                    alert('Gagal memulai sinkronisasi: ' + data.message);
                    confirmSyncBtn.disabled = false;
                    confirmSyncBtn.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Konfirmasi & Sinkronisasi';
                }
            } catch (error) {
                console.error('Sync error:', error);
                alert('Terjadi kesalahan saat sinkronisasi.');
                confirmSyncBtn.disabled = false;
                confirmSyncBtn.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Konfirmasi & Sinkronisasi';
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
        async function loadHistory(page = 1) {
            try {
                currentHistoryPage = page;
                const response = await fetch(`{{ route('pegawai.import.history') }}?page=${page}`);
                const data = await response.json();

                const imports = data.data;

                if (imports.length === 0) {
                    historyTableBody.innerHTML = `
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                            Belum ada riwayat import
                                        </td>
                                    </tr>
                                `;
                    historyPagination.innerHTML = '';
                    return;
                }

                historyTableBody.innerHTML = imports.map(item => `
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
                                        ${getAnomaliBadge(item.filename, item.anomaly_rows || 0)}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        ${getStatusBadge(item.status)}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        ${getProgressBar(item.progress, item.status)}
                                    </td>
                                </tr>
                            `).join('');

                renderHistoryPagination(data);

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

        function getAnomaliBadge(filename, totalErrors) {
            if (totalErrors === 0) {
                return '<span class="text-gray-500 dark:text-gray-400">-</span>';
            }

            return `<button onclick="openAnomalyModal('${filename}')" class="text-yellow-600 dark:text-yellow-400 font-semibold hover:underline" title="Klik untuk melihat detail anomali">${totalErrors} Anomali</button>`;
        }
        
        // Modal functions
        const anomalyModal = document.getElementById('anomaly-modal');
        const anomalyModalFilename = document.getElementById('anomaly-modal-filename');
        const anomalyModalBody = document.getElementById('anomaly-modal-body');
        const anomalyPagination = document.getElementById('anomaly-pagination');
        let currentAnomalyFilename = null;

        function openAnomalyModal(filename) {
            currentAnomalyFilename = filename;
            anomalyModalFilename.textContent = filename;
            anomalyModal.classList.remove('hidden');
            loadAnomalyDetails(1);
        }

        function closeAnomalyModal() {
            anomalyModal.classList.add('hidden');
            currentAnomalyFilename = null;
        }

        async function loadAnomalyDetails(page = 1) {
            if (!currentAnomalyFilename) return;
            
            anomalyModalBody.innerHTML = '<tr><td colspan="4" class="px-4 py-3 text-center text-gray-500">Memuat data...</td></tr>';
            anomalyPagination.innerHTML = '';
            
            try {
                const response = await fetch(`{{ url('admin/sync-data/anomaly-details') }}/${currentAnomalyFilename}?page=${page}`);
                const data = await response.json();
                
                if (data.data.length === 0) {
                    anomalyModalBody.innerHTML = '<tr><td colspan="4" class="px-4 py-3 text-center text-gray-500">Tidak ada anomali.</td></tr>';
                    return;
                }
                
                anomalyModalBody.innerHTML = data.data.map(item => `
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">${item.pns_id || '-'}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900 dark:text-gray-100">${item.nama || '-'}</div>
                            <div class="text-xs text-gray-500">${item.nip_baru || '-'}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-red-600 dark:text-red-400 font-semibold">${item.catatan_anomali ? item.catatan_anomali.replace('Referensi tidak valid/ditemukan: ', '<span class="px-2 py-1 text-xs rounded bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">') + '</span>' : '-'}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">Silakan melengkapi data master yang tercatat diatas sebelum melakukan sinkronisasi selanjutnya.</td>
                    </tr>
                `).join('');
                
                renderAnomalyPagination(data);
            } catch (error) {
                console.error('Error loading anomalies:', error);
                anomalyModalBody.innerHTML = '<tr><td colspan="4" class="px-4 py-3 text-center text-red-500">Gagal memuat detail anomali.</td></tr>';
            }
        }
        
        function renderAnomalyPagination(meta) {
            if (meta.last_page <= 1) {
                anomalyPagination.innerHTML = '';
                return;
            }

            let html = '<nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">';
            
            html += `<button ${meta.current_page === 1 ? 'disabled' : `onclick="loadAnomalyDetails(${meta.current_page - 1})"`} class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium ${meta.current_page === 1 ? 'text-gray-300 dark:text-gray-500 cursor-not-allowed' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-600'}">&lt;</button>`;
            
            html += `<button class="relative inline-flex items-center px-4 py-2 border text-sm font-medium z-10 bg-blue-50 dark:bg-blue-900 border-blue-500 text-blue-600 dark:text-blue-300">${meta.current_page}</button>`;
            
            html += `<button ${meta.current_page === meta.last_page ? 'disabled' : `onclick="loadAnomalyDetails(${meta.current_page + 1})"`} class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium ${meta.current_page === meta.last_page ? 'text-gray-300 dark:text-gray-500 cursor-not-allowed' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-600'}">&gt;</button>`;
            
            html += '</nav>';
            anomalyPagination.innerHTML = html;
        }

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

        function renderHistoryPagination(meta) {
            if (meta.last_page <= 1) {
                historyPagination.innerHTML = '';
                return;
            }

            let html = '<nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">';

            html += `
                    <button ${meta.current_page === 1 ? 'disabled' : `onclick="loadHistory(${meta.current_page - 1})"`} 
                        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium ${meta.current_page === 1 ? 'text-gray-300 dark:text-gray-500 cursor-not-allowed' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-600'}">
                        <span class="sr-only">Previous</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                `;

            for (let i = 1; i <= meta.last_page; i++) {
                if (i === 1 || i === meta.last_page || (i >= meta.current_page - 1 && i <= meta.current_page + 1)) {
                    const activeClass = i === meta.current_page
                        ? 'z-10 bg-blue-50 dark:bg-blue-900 border-blue-500 text-blue-600 dark:text-blue-300'
                        : 'bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-600';

                    html += `
                            <button onclick="loadHistory(${i})" 
                                class="relative inline-flex items-center px-4 py-2 border text-sm font-medium ${activeClass}">
                                ${i}
                            </button>
                        `;
                } else if (i === meta.current_page - 2 || i === meta.current_page + 2) {
                    html += `<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300">...</span>`;
                }
            }

            html += `
                    <button ${meta.current_page === meta.last_page ? 'disabled' : `onclick="loadHistory(${meta.current_page + 1})"`} 
                        class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium ${meta.current_page === meta.last_page ? 'text-gray-300 dark:text-gray-500 cursor-not-allowed' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-600'}">
                        <span class="sr-only">Next</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                `;

            html += '</nav>';
            historyPagination.innerHTML = html;
        }

        refreshBtn.addEventListener('click', () => loadHistory(currentHistoryPage));
        setInterval(() => loadHistory(currentHistoryPage), 5000);
        loadHistory(1);
    </script>
@endsection