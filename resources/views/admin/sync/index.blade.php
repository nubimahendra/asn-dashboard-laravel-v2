@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Sinkronisasi Data Pegawai</h1>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Status Sinkronisasi</h2>
                <div id="sync-status-badge"
                    class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                    Menunggu
                </div>
            </div>

            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                Proses ini akan mengambil data pegawai terbaru dari server sumber (SiDawai).
                Data akan diproses secara bertahap untuk mencegah kegagalan sistem.
            </p>

            <!-- Progress Bar -->
            <div class="mb-6">
                <div class="flex justify-between mb-1">
                    <span id="sync-status-text" class="text-sm font-medium text-blue-700 dark:text-blue-400">Siap untuk
                        sinkronisasi</span>
                    <span id="sync-percent" class="text-sm font-medium text-blue-700 dark:text-blue-400">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4 dark:bg-gray-700 overflow-hidden">
                    <div id="sync-progress-bar"
                        class="bg-blue-600 h-4 rounded-full transition-all duration-300 flex items-center justify-center text-[10px] text-white font-bold"
                        style="width: 0%"></div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-4 mb-6">
                <button type="button" id="btn-start-sync"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 disabled:cursor-not-allowed">
                    Mulai Sinkronisasi
                </button>
                <button type="button" id="btn-cancel-sync"
                    class="hidden px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                    Batal
                </button>
            </div>

            <!-- Logs -->
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Log Proses</h3>
                <div id="sync-logs"
                    class="h-64 overflow-y-auto font-mono text-xs text-gray-600 dark:text-gray-400 space-y-1 p-2">
                    <p class="text-gray-400 italic">Klik tombol "Mulai Sinkronisasi" untuk memulai...</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btnStart = document.getElementById('btn-start-sync');
            const btnCancel = document.getElementById('btn-cancel-sync');
            const progressBar = document.getElementById('sync-progress-bar');
            const statusText = document.getElementById('sync-status-text');
            const percentText = document.getElementById('sync-percent');
            const statusBadge = document.getElementById('sync-status-badge');
            const logs = document.getElementById('sync-logs');

            let isSyncing = false;
            let abortController = null;

            function log(message, type = 'info') {
                const p = document.createElement('p');
                const time = new Date().toLocaleTimeString();
                p.innerHTML = `<span class="text-gray-400">[${time}]</span> ${message}`;

                if (type === 'error') p.classList.add('text-red-500');
                if (type === 'success') p.classList.add('text-green-500');

                logs.appendChild(p);
                logs.scrollTop = logs.scrollHeight;
            }

            function setStatus(status, type = 'neutral') {
                statusBadge.textContent = status;
                statusBadge.className = 'px-3 py-1 rounded-full text-xs font-medium';

                if (type === 'process') {
                    statusBadge.classList.add('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900', 'dark:text-blue-200');
                } else if (type === 'success') {
                    statusBadge.classList.add('bg-green-100', 'text-green-800', 'dark:bg-green-900', 'dark:text-green-200');
                } else if (type === 'error') {
                    statusBadge.classList.add('bg-red-100', 'text-red-800', 'dark:bg-red-900', 'dark:text-red-200');
                } else {
                    statusBadge.classList.add('bg-gray-100', 'text-gray-600', 'dark:bg-gray-700', 'dark:text-gray-400');
                }
            }

            function resetUI() {
                btnStart.disabled = false;
                btnStart.classList.remove('hidden');
                btnCancel.classList.add('hidden');
                isSyncing = false;
            }

            btnCancel.addEventListener('click', function () {
                if (confirm('Apakah Anda yakin ingin membatalkan proses sinkronisasi? Data mungkin tidak lengkap.')) {
                    if (abortController) {
                        abortController.abort();
                    }
                    isSyncing = false;
                    log('Proses dibatalkan oleh pengguna.', 'error');
                    setStatus('Dibatalkan', 'error');
                    statusText.textContent = 'Dibatalkan oleh pengguna';
                    resetUI();
                }
            });

            btnStart.addEventListener('click', async function () {
                if (isSyncing) return;

                isSyncing = true;
                abortController = new AbortController();
                const signal = abortController.signal;

                // UI Reset
                progressBar.style.width = '0%';
                percentText.textContent = '0%';
                logs.innerHTML = '';

                btnStart.disabled = true;
                btnStart.classList.add('hidden');
                btnCancel.classList.remove('hidden');

                setStatus('Memproses...', 'process');
                log('Memulai inisialisasi sinkronisasi...');
                statusText.textContent = 'Menghubungkan ke server...';

                try {
                    // Step 1: Init
                    const initRes = await fetch("{{ route('sync.init') }}", {
                        signal: signal,
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!initRes.ok) throw new Error(`HTTP error! status: ${initRes.status}`);

                    const initData = await initRes.json();

                    if (initData.status !== 'success') {
                        throw new Error(initData.message || 'Gagal inisialisasi');
                    }

                    const total = initData.total;
                    log(`Total data di server sumber: ${total}`);

                    if (total === 0) {
                        progressBar.style.width = '100%';
                        percentText.textContent = '100%';
                        setStatus('Selesai', 'success');
                        log('Tidak ada data baru untuk disinkronkan.', 'success');
                        resetUI();
                        return;
                    }

                    // Step 2: Batch Loop
                    const batchSize = 200;
                    let processed = 0;

                    while (processed < total) {
                        if (signal.aborted) throw new Error('AbortError');

                        const remaining = total - processed;
                        const currentBatch = Math.min(remaining, batchSize);

                        statusText.textContent = `Memproses data ${processed + 1} - ${processed + currentBatch} dari ${total}...`;

                        const formData = new FormData();
                        formData.append('offset', processed);
                        formData.append('limit', batchSize);
                        formData.append('_token', "{{ csrf_token() }}");

                        const batchRes = await fetch("{{ route('sync.batch') }}", {
                            method: 'POST',
                            body: formData,
                            signal: signal,
                            headers: { 'Accept': 'application/json' }
                        });

                        if (!batchRes.ok) throw new Error(`HTTP error! status: ${batchRes.status}`);

                        const batchData = await batchRes.json();

                        if (batchData.status !== 'success') {
                            throw new Error(batchData.message || 'Gagal memproses batch');
                        }

                        processed += batchData.processed;
                        const percent = Math.round((processed / total) * 90);

                        progressBar.style.width = `${percent}%`;
                        percentText.textContent = `${percent}%`;
                        log(`Berhasil memproses batch: ${batchData.processed} data.`);

                        if (batchData.processed === 0) break;

                        // Small delay to allow UI updates and cancellation
                        await new Promise(r => setTimeout(r, 100));
                    }

                    // Step 3: Cleanup
                    if (signal.aborted) throw new Error('AbortError');

                    statusText.textContent = 'Membersihkan data usang...';
                    log('Menghapus data lokal yang tidak valid...');

                    const cleanupRes = await fetch("{{ route('sync.cleanup') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({}),
                        signal: signal
                    });

                    if (!cleanupRes.ok) throw new Error(`HTTP error! status: ${cleanupRes.status}`);

                    const cleanupData = await cleanupRes.json();

                    if (cleanupData.status !== 'success') {
                        throw new Error(cleanupData.message || 'Gagal cleanup');
                    }

                    log(`Pembersihan selesai. Dihapus: ${cleanupData.deleted} data.`);

                    // Finish
                    progressBar.style.width = '100%';
                    percentText.textContent = '100%';
                    setStatus('Sukses', 'success');
                    statusText.textContent = 'Sinkronisasi Selesai!';
                    log('Semua proses selesai dengan sukses.', 'success');

                    btnStart.classList.remove('hidden');
                    btnStart.disabled = false;
                    btnStart.textContent = 'Sinkronisasi Ulang';
                    btnCancel.classList.add('hidden');
                    isSyncing = false;

                } catch (error) {
                    if (error.message === 'AbortError' || error.name === 'AbortError') {
                        log('Proses dibatalkan.', 'error');
                    } else {
                        console.error(error);
                        setStatus('Error', 'error');
                        statusText.textContent = 'Terjadi Kesalahan';
                        log(`ERROR: ${error.message}`, 'error');
                        alert('Terjadi kesalahan: ' + error.message);
                    }
                    resetUI();
                }
            });
        });
    </script>
@endsection