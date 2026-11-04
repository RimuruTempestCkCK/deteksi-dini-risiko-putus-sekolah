<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\NilaiPresensiController;
use App\Http\Controllers\MiningController;
use App\Http\Controllers\KomparasiController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\IntervensiController;
use App\Http\Controllers\LaporanExportController;

// Autentikasi Admin
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Modul Data Master Siswa
    Route::post('/siswa/import-dapodik', [SiswaController::class, 'importDapodik'])->name('siswa.import');
    Route::match(['delete', 'post'], '/siswa/destroy-all', [SiswaController::class, 'destroyAll'])->name('siswa.destroy-all');
    Route::resource('siswa', SiswaController::class);

    // Modul Nilai Rapor & Presensi
    Route::get('/nilai', [NilaiPresensiController::class, 'index'])->name('nilai.index');
    Route::post('/nilai/import-leger', [NilaiPresensiController::class, 'importLeger'])->name('nilai.import');

    // Modul KDD Mining Pipeline
    Route::get('/mining', [MiningController::class, 'index'])->name('mining.index');
    Route::post('/mining/train', [MiningController::class, 'train'])->name('mining.train');

    // Modul Komparasi Random Forest vs Naive Bayes
    Route::get('/komparasi', [KomparasiController::class, 'index'])->name('komparasi.index');
    Route::post('/komparasi/set-active', [KomparasiController::class, 'setActive'])->name('komparasi.set-active');

    // Modul Prediksi & Simulasi
    Route::get('/prediksi', [PredictionController::class, 'index'])->name('prediksi.index');
    Route::post('/prediksi/batch', [PredictionController::class, 'runBatch'])->name('prediksi.batch');
    Route::get('/prediksi/simulasi', [PredictionController::class, 'formSimulasi'])->name('prediksi.simulasi');
    Route::post('/prediksi/simulasi', [PredictionController::class, 'processSimulasi'])->name('prediksi.simulasi.process');

    // Modul Tindakan Intervensi
    Route::get('/intervensi', [IntervensiController::class, 'index'])->name('intervensi.index');
    Route::post('/intervensi', [IntervensiController::class, 'store'])->name('intervensi.store');
    Route::patch('/intervensi/{id}/status', [IntervensiController::class, 'updateStatus'])->name('intervensi.status');

    // Modul Laporan & Export
    Route::get('/laporan', [LaporanExportController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/download-pdf', [LaporanExportController::class, 'downloadPdf'])->name('laporan.pdf');
});
