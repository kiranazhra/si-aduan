<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KonfigurasiController;
use App\Http\Controllers\Admin\ProfilController;
use App\Http\Controllers\Admin\RekapController;
use App\Http\Controllers\Admin\TiketController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\Unit\DashboardController as UnitDashboardController;
use App\Http\Controllers\Unit\ProfilController as UnitProfilController;
use App\Http\Controllers\Unit\RiwayatController as UnitRiwayatController;
use App\Http\Controllers\Unit\TiketController as UnitTiketController;
use App\Http\Middleware\AdminOnly;
use App\Http\Middleware\UnitOnly;
use Illuminate\Support\Facades\Route;

// ===== Halaman Publik =====
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::view('/panduan', 'guide')->name('guide');

Route::view('/kontak', 'contact')->name('contact');

Route::get('/lapor', [ComplaintController::class, 'create'])->name('complaint.create');
Route::post('/lapor', [ComplaintController::class, 'store'])->name('complaint.store');
Route::get('/lapor/konfirmasi', [ComplaintController::class, 'confirmation'])->name('complaint.confirmation');

Route::get('/cek-status', [StatusController::class, 'index'])->name('status.check');

// ===== Auth =====
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ===== Halaman Admin (Super Admin, Operator, Viewer) =====
Route::middleware(['auth', AdminOnly::class])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Semua Tiket
    Route::get('/tiket', [TiketController::class, 'index'])->name('tickets');
    Route::get('/tiket/{aduan:nomor_tiket}', [TiketController::class, 'show'])->name('tickets.show');
    Route::post('/tiket/{aduan:nomor_tiket}/teruskan', [TiketController::class, 'teruskan'])->name('tickets.forward');
    Route::post('/tiket/{aduan:nomor_tiket}/status', [TiketController::class, 'ubahStatus'])->name('tickets.status');
    Route::post('/tiket/{aduan:nomor_tiket}/solusi', [TiketController::class, 'kirimSolusi'])->name('tickets.solution');

    // Rekap & Laporan
    Route::get('/rekap', [RekapController::class, 'hub'])->name('recap');
    Route::prefix('rekap')->name('recap.')->group(function () {
        Route::get('/data', [RekapController::class, 'data'])->name('data');
        Route::get('/kategori', [RekapController::class, 'kategori'])->name('category');
        Route::get('/grading', [RekapController::class, 'grading'])->name('grading');
        Route::get('/media', [RekapController::class, 'media'])->name('media');
        Route::get('/rating', [RekapController::class, 'rating'])->name('rating');
        Route::get('/lokasi', [RekapController::class, 'lokasi'])->name('room');
        Route::get('/kesimpulan', [RekapController::class, 'kesimpulan'])->name('conclusion');
        Route::get('/berkas', [RekapController::class, 'berkas'])->name('files');
        Route::get('/detail', [RekapController::class, 'detail'])->name('detail');
    });

    // Konfigurasi
    Route::get('/konfigurasi', [KonfigurasiController::class, 'index'])->name('config');
    Route::post('/konfigurasi/unit', [KonfigurasiController::class, 'simpanUnit'])->name('config.unit.store');
    Route::put('/konfigurasi/unit/{unit}', [KonfigurasiController::class, 'ubahUnit'])->name('config.unit.update');
    Route::post('/konfigurasi/staf', [KonfigurasiController::class, 'simpanStaf'])->name('config.staff.store');
    Route::put('/konfigurasi/staf/{user}', [KonfigurasiController::class, 'ubahStaf'])->name('config.staff.update');

    // Profil
    Route::get('/profil', [ProfilController::class, 'index'])->name('profile');
});

// ===== Halaman Unit (Petugas Unit) =====
Route::middleware(['auth', UnitOnly::class])->prefix('unit')->name('unit.')->group(function () {

    Route::get('/dashboard', [UnitDashboardController::class, 'index'])->name('dashboard');

    // Tiket unit (hanya aduan yang didisposisikan ke unit petugas ini)
    Route::get('/tiket', [UnitTiketController::class, 'index'])->name('tickets');
    Route::get('/tiket/{aduan:nomor_tiket}', [UnitTiketController::class, 'show'])->name('tickets.show');
    Route::post('/tiket/{aduan:nomor_tiket}/status', [UnitTiketController::class, 'ubahStatus'])->name('tickets.status');

    // Riwayat & profil
    Route::get('/riwayat', [UnitRiwayatController::class, 'index'])->name('history');
    Route::get('/profil', [UnitProfilController::class, 'index'])->name('profile');
});
