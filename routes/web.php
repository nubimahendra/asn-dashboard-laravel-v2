<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\ChatAdminController;
use App\Http\Controllers\WhatsAppController;

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Admin Only Routes
    Route::middleware([\App\Http\Middleware\IsAdmin::class])->group(function () {
        Route::post('/sync-pegawai', [SyncController::class, 'sync'])->name('sync.pegawai');
        Route::resource('users', \App\Http\Controllers\UserController::class);

        Route::prefix('admin/chat')->name('admin.chat.')->group(function () {
            Route::get('/', [ChatAdminController::class, 'index'])->name('index');
            Route::get('/{phone}', [ChatAdminController::class, 'show'])->name('show');
            Route::post('/reply', [ChatAdminController::class, 'reply'])->name('reply');
        });

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
    });
});


Route::post('/webhook/whatsapp', [WhatsAppController::class, 'handleWebhook'])->name('webhook.whatsapp');
