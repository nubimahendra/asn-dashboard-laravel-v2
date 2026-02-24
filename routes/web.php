<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SyncController;


// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Web Chat Routes (Session Based API) - REMOVED


    // Admin Only Routes
    Route::middleware([\App\Http\Middleware\IsAdmin::class])->group(function () {
        Route::get('/sync-data', [\App\Http\Controllers\PegawaiImportController::class, 'syncPage'])->name('sync.index');
        Route::resource('users', \App\Http\Controllers\UserController::class);

        // Snapshot / Laporan Routes
        Route::get('/snapshot', [App\Http\Controllers\SnapshotController::class, 'index'])->name('snapshot.index');
        Route::post('/snapshot', [App\Http\Controllers\SnapshotController::class, 'store'])->name('snapshot.store');
        Route::get('/snapshot/export/pdf', [App\Http\Controllers\SnapshotController::class, 'downloadPdf'])->name('snapshot.export.pdf');
        Route::get('/snapshot/export/excel', [App\Http\Controllers\SnapshotController::class, 'downloadExcel'])->name('snapshot.export.excel');

        // Chat Admin Routes - REMOVED

        // Pengajuan Cerai Routes
        Route::controller(\App\Http\Controllers\PengajuanCeraiController::class)
            ->prefix('admin/pengajuan-cerai')
            ->name('admin.pengajuan-cerai.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::get('/search-pegawai', 'searchPegawai')->name('search');
                Route::get('/print', 'print')->name('print');
                Route::get('/export-excel', 'exportExcel')->name('export.excel');
                Route::delete('/{id}', 'destroy')->name('destroy');
            });

        // Surat Masuk Routes
        Route::get('/surat-masuk/print', [App\Http\Controllers\SuratMasukController::class, 'print'])->name('surat-masuk.print');
        Route::resource('surat-masuk', App\Http\Controllers\SuratMasukController::class);

        // Chatbot Admin (Refactoring to Vanilla JS)
        // Route::get('/chat', App\Livewire\ChatAdmin::class)->name('chat.index');
        Route::get('/chat', [App\Http\Controllers\ChatAdminController::class, 'index'])->name('chat.index'); // We need to create this controller

        // Pegawai Search & Profile Routes
        Route::prefix('pegawai/master')
            ->name('pegawai.import.')
            ->group(function () {
                Route::get('/', [\App\Http\Controllers\PegawaiImportController::class, 'index'])->name('index');
                Route::get('/search-employee', [\App\Http\Controllers\PegawaiImportController::class, 'searchEmployee'])->name('search-employee');
                Route::get('/profile/{id}', [\App\Http\Controllers\PegawaiImportController::class, 'getEmployeeProfile'])->name('profile');
            });

        // Sync Data Routes (CSV Import)
        Route::prefix('sync-data')
            ->name('pegawai.import.')
            ->group(function () {
                Route::post('/upload', [\App\Http\Controllers\PegawaiImportController::class, 'upload'])->name('upload');
                Route::get('/history', [\App\Http\Controllers\PegawaiImportController::class, 'history'])->name('history');
                Route::get('/status/{filename}', [\App\Http\Controllers\PegawaiImportController::class, 'status'])->name('status');

                // Diff & Sync Routes
                Route::get('/diff-summary/{filename}', [\App\Http\Controllers\PegawaiImportController::class, 'diffSummary'])->name('diff-summary');
                Route::get('/diff-details/{filename}', [\App\Http\Controllers\PegawaiImportController::class, 'diffDetails'])->name('diff-details');
                Route::get('/anomaly-details/{filename}', [\App\Http\Controllers\PegawaiImportController::class, 'anomalyDetails'])->name('anomaly-details');
                Route::post('/sync-confirm', [\App\Http\Controllers\PegawaiImportController::class, 'confirmSync'])->name('confirm-sync');

                // Batch Validation Routes
                Route::get('/batch/{batchId}/errors', [\App\Http\Controllers\PegawaiImportController::class, 'downloadErrors'])->name('batch.errors');
                Route::post('/batch/{batchId}/retry', [\App\Http\Controllers\PegawaiImportController::class, 'retry'])->name('batch.retry');
            });

        // Iuran Korpri Routes
        Route::get('/laporan/iuran-korpri', [App\Http\Controllers\IuranKorpriController::class, 'index'])->name('iuran-korpri.index');
        Route::put('/laporan/iuran-korpri/update', [App\Http\Controllers\IuranKorpriController::class, 'updateBesaran'])->name('iuran-korpri.update');
    });
});
