<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HubController;
use App\Http\Controllers\MariDashboardController;

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/', [HubController::class, 'index'])->name('hub');

    // Admin Only Routes
    Route::middleware([\App\Http\Middleware\IsAdmin::class])->group(function () {
        
        // ==========================================
        // MASN - Manajemen ASN
        // ==========================================
        Route::prefix('masn')->name('masn.')->group(function () {
            Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
            Route::resource('users', \App\Http\Controllers\UserController::class);
            Route::get('/sync-data', [\App\Http\Controllers\PegawaiImportController::class, 'syncPage'])->name('sync.index');
            
            // Snapshot / Laporan Routes
            Route::get('/snapshot', [App\Http\Controllers\SnapshotController::class, 'index'])->name('snapshot.index');
            Route::post('/snapshot', [App\Http\Controllers\SnapshotController::class, 'store'])->name('snapshot.store');
            Route::get('/snapshot/export/pdf', [App\Http\Controllers\SnapshotController::class, 'downloadPdf'])->name('snapshot.export.pdf');
            Route::get('/snapshot/export/excel', [App\Http\Controllers\SnapshotController::class, 'downloadExcel'])->name('snapshot.export.excel');

            // Pegawai Search & Profile Routes
            Route::prefix('pegawai/master')->name('pegawai.import.')->group(function () {
                Route::get('/', [\App\Http\Controllers\PegawaiImportController::class, 'index'])->name('index');
                Route::get('/search-employee', [\App\Http\Controllers\PegawaiImportController::class, 'searchEmployee'])->name('search-employee');
                Route::get('/profile/{id}', [\App\Http\Controllers\PegawaiImportController::class, 'getEmployeeProfile'])->name('profile');
            });

            // Sync Data Routes (CSV Import)
            Route::prefix('sync-data')->name('pegawai.import.')->group(function () {
                Route::post('/upload', [\App\Http\Controllers\PegawaiImportController::class, 'upload'])->name('upload');
                Route::get('/history', [\App\Http\Controllers\PegawaiImportController::class, 'history'])->name('history');
                Route::get('/status/{filename}', [\App\Http\Controllers\PegawaiImportController::class, 'status'])->name('status');
                Route::get('/diff-summary/{filename}', [\App\Http\Controllers\PegawaiImportController::class, 'diffSummary'])->name('diff-summary');
                Route::get('/diff-details/{filename}', [\App\Http\Controllers\PegawaiImportController::class, 'diffDetails'])->name('diff-details');
                Route::get('/anomaly-details/{filename}', [\App\Http\Controllers\PegawaiImportController::class, 'anomalyDetails'])->name('anomaly-details');
                Route::get('/sync-preview/{filename}', [\App\Http\Controllers\PegawaiImportController::class, 'syncPreview'])->name('sync-preview');
                Route::post('/sync-confirm', [\App\Http\Controllers\PegawaiImportController::class, 'confirmSync'])->name('confirm-sync');
                Route::delete('/cancel', [\App\Http\Controllers\PegawaiImportController::class, 'cancelImport'])->name('cancel');
                Route::get('/batch/{batchId}/errors', [\App\Http\Controllers\PegawaiImportController::class, 'downloadErrors'])->name('batch.errors');
                Route::post('/batch/{batchId}/retry', [\App\Http\Controllers\PegawaiImportController::class, 'retry'])->name('batch.retry');
            });
        });

        // ==========================================
        // MARI - Manajemen Iuran Korpri
        // ==========================================
        Route::prefix('mari')->name('mari.')->group(function () {
            Route::get('/', [MariDashboardController::class, 'index'])->name('dashboard');

            // Iuran Korpri Routes
            Route::get('/laporan/iuran-korpri', [App\Http\Controllers\IuranKorpriController::class, 'index'])->name('iuran-korpri.index');
            Route::put('/laporan/iuran-korpri/update', [App\Http\Controllers\IuranKorpriController::class, 'updateBesaran'])->name('iuran-korpri.update');
            
            Route::get('/rincian-iuran', [App\Http\Controllers\RincianIuranController::class, 'index'])->name('rincian-iuran.index');
            
            Route::get('/rekon-iuran', [App\Http\Controllers\RekonIuranController::class, 'index'])->name('rekon-iuran.index');
            Route::put('/rekon-iuran/bulk-override', [App\Http\Controllers\RekonIuranController::class, 'bulkOverride'])->name('rekon-iuran.bulk-override');
            Route::put('/rekon-iuran/single-override', [App\Http\Controllers\RekonIuranController::class, 'singleOverride'])->name('rekon-iuran.single-override');
            Route::delete('/rekon-iuran/override/{id}', [App\Http\Controllers\RekonIuranController::class, 'destroy'])->name('rekon-iuran.destroy');
            Route::post('/rekon-iuran/sync-reset', [App\Http\Controllers\RekonIuranController::class, 'syncReset'])->name('rekon-iuran.sync-reset');
            Route::get('/iuran-korpri/kelas-jabatan', [App\Http\Controllers\KelasJabatanController::class, 'index'])->name('kelas-jabatan.index');
            Route::post('/iuran-korpri/kelas-jabatan/import', [App\Http\Controllers\KelasJabatanController::class, 'import'])->name('kelas-jabatan.import');
            Route::put('/iuran-korpri/kelas-jabatan/tarif', [App\Http\Controllers\KelasJabatanController::class, 'updateTarif'])->name('kelas-jabatan.update-tarif');
            Route::delete('/iuran-korpri/kelas-jabatan/{id}', [App\Http\Controllers\KelasJabatanController::class, 'destroy'])->name('kelas-jabatan.destroy');

            Route::get('/iuran-korpri/kelas-jabatan-perbup', [App\Http\Controllers\KelasJabatanPerbupController::class, 'index'])->name('kelas-jabatan-perbup.index');
            Route::post('/iuran-korpri/kelas-jabatan-perbup/import', [App\Http\Controllers\KelasJabatanPerbupController::class, 'import'])->name('kelas-jabatan-perbup.import');
            Route::delete('/iuran-korpri/kelas-jabatan-perbup/{id}', [App\Http\Controllers\KelasJabatanPerbupController::class, 'destroy'])->name('kelas-jabatan-perbup.destroy');

            Route::get('/iuran-korpri/jabatan-mapping/find-similar', [App\Http\Controllers\JabatanMappingController::class, 'findSimilarPerbup'])->name('jabatan-mapping.find-similar');
            Route::get('/iuran-korpri/jabatan-mapping/generate-bulk', [App\Http\Controllers\JabatanMappingController::class, 'generateBulkSuggestions'])->name('jabatan-mapping.generate-bulk');
            Route::post('/iuran-korpri/jabatan-mapping/bulk-store', [App\Http\Controllers\JabatanMappingController::class, 'storeBulk'])->name('jabatan-mapping.bulk-store');
            Route::get('/iuran-korpri/jabatan-mapping', [App\Http\Controllers\JabatanMappingController::class, 'index'])->name('jabatan-mapping.index');
            Route::post('/iuran-korpri/jabatan-mapping', [App\Http\Controllers\JabatanMappingController::class, 'store'])->name('jabatan-mapping.store');
            Route::delete('/iuran-korpri/jabatan-mapping/{id}', [App\Http\Controllers\JabatanMappingController::class, 'destroy'])->name('jabatan-mapping.destroy');

            Route::get('/iuran-korpri/jabatan-default', [App\Http\Controllers\JabatanDefaultController::class, 'index'])->name('jabatan-default.index');
            Route::post('/iuran-korpri/jabatan-default', [App\Http\Controllers\JabatanDefaultController::class, 'store'])->name('jabatan-default.store');
            Route::delete('/iuran-korpri/jabatan-default/{id}', [App\Http\Controllers\JabatanDefaultController::class, 'destroy'])->name('jabatan-default.destroy');

            Route::get('/iuran-korpri/iuran-kelas-jabatan', [App\Http\Controllers\IuranKelasJabatanController::class, 'index'])->name('iuran-kelas-jabatan.index');
            Route::get('/iuran-korpri/iuran-kelas-jabatan/opd-detail', [App\Http\Controllers\IuranKelasJabatanController::class, 'opdDetail'])->name('iuran-kelas-jabatan.opd-detail');
            Route::post('/iuran-korpri/iuran-kelas-jabatan/generate', [App\Http\Controllers\IuranKelasJabatanController::class, 'generate'])->name('iuran-kelas-jabatan.generate');
        });

        // ==========================================
        // MESRA - Manajemen Surat Menyurat
        // ==========================================
        Route::prefix('mesra')->name('mesra.')->group(function () {
            Route::get('/', function() { return view('mesra.dashboard'); })->name('dashboard');

            // Surat Masuk Routes
            Route::get('/surat-masuk/print', [App\Http\Controllers\SuratMasukController::class, 'print'])->name('surat-masuk.print');
            Route::resource('surat-masuk', App\Http\Controllers\SuratMasukController::class);

            // Pengajuan Cerai Routes
            Route::controller(\App\Http\Controllers\PengajuanCeraiController::class)
                ->prefix('pengajuan-cerai')
                ->name('pengajuan-cerai.')
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::post('/', 'store')->name('store');
                    Route::get('/search-pegawai', 'searchPegawai')->name('search');
                    Route::get('/print', 'print')->name('print');
                    Route::get('/export-excel', 'exportExcel')->name('export.excel');
                    Route::delete('/{id}', 'destroy')->name('destroy');
                });

            // Chatbot Admin
            Route::get('/chat', [App\Http\Controllers\ChatAdminController::class, 'index'])->name('chat.index');
        });
    });
});
