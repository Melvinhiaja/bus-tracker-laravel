<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TiketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\LokasiBus;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\TrackingController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Route untuk Admin dan Karyawan menggunakan middleware role-based access
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/create-karyawan', [AdminController::class, 'createKaryawan'])->name('admin.createKaryawan');
    Route::post('/admin/store-karyawan', [AdminController::class, 'storeKaryawan'])->name('admin.storeKaryawan');
    Route::get('/admin/view-karyawan/{id}', [AdminController::class, 'viewKaryawan'])->name('admin.viewKaryawan'); // Tambahan
    Route::get('/admin/edit-karyawan/{id}', [AdminController::class, 'editKaryawan'])->name('admin.editKaryawan');
    Route::post('/admin/update-karyawan/{id}', [AdminController::class, 'updateKaryawan'])->name('admin.updateKaryawan');
    Route::delete('/admin/delete-karyawan/{id}', [AdminController::class, 'deleteKaryawan'])->name('admin.deleteKaryawan');

    //route untuk admin
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/create-admin', [AdminController::class, 'createAdmin'])->name('admin.createAdmin');
    Route::post('/admin/store-admin', [AdminController::class, 'storeAdmin'])->name('admin.storeAdmin');
    Route::get('/admin/view-admin/{id}', [AdminController::class, 'viewAdmin'])->name('admin.viewAdmin');
    Route::get('/admin/edit-admin/{id}', [AdminController::class, 'editAdmin'])->name('admin.editAdmin');
    Route::post('/admin/update-admin/{id}', [AdminController::class, 'updateAdmin'])->name('admin.updateAdmin');
    Route::delete('/admin/delete-admin/{id}', [AdminController::class, 'deleteAdmin'])->name('admin.deleteAdmin');
    Route::get('/admin/bus', [AdminController::class, 'listBus'])->name('admin.listBus');
    Route::get('/admin/bus/create', [AdminController::class, 'createBus'])->name('admin.createBus');
    Route::post('/admin/bus', [AdminController::class, 'storeBus'])->name('admin.storeBus');
    Route::get('/admin/bus/{id}/edit', [AdminController::class, 'editBus'])->name('admin.editBus');
    Route::put('/admin/bus/{id}', [AdminController::class, 'updateBus'])->name('admin.updateBus');
    Route::delete('/admin/bus/{id}', [AdminController::class, 'deleteBus'])->name('admin.deleteBus');
    // Tambah Driver
    Route::get('/admin/driver/create', [AdminController::class, 'createDriver'])->name('admin.createDriver');
    Route::post('/admin/driver/store', [AdminController::class, 'storeDriver'])->name('admin.storeDriver');
    Route::get('/admin/driver/view/{id}', [AdminController::class, 'viewDriver'])->name('admin.viewDriver');
    Route::get('/admin/driver/edit/{id}', [AdminController::class, 'editDriver'])->name('admin.editDriver');
    Route::delete('/admin/driver/delete/{id}', [AdminController::class, 'deleteDriver'])->name('admin.deleteDriver');
    Route::post('/admin/driver/edit/{id}', [AdminController::class, 'updateDriver'])->name('admin.updateDriver');

    //============unutk perjlanan=============//

    Route::get('/admin/perjalanan', [AdminController::class, 'indexPerjalanan'])->name('admin.perjalanan.index');
    Route::post('/admin/perjalanan', [AdminController::class, 'storePerjalanan'])->name('admin.perjalanan.store');
    Route::get('/admin/perjalanan/{perjalanan}/edit', [AdminController::class, 'editPerjalanan'])->name('admin.perjalanan.edit');
    Route::put('/admin/perjalanan/{perjalanan}', [AdminController::class, 'updatePerjalanan'])->name('admin.perjalanan.update');
    Route::delete('/admin/perjalanan/{perjalanan}', [AdminController::class, 'destroyPerjalanan'])->name('admin.perjalanan.destroy');

    //============unutk alasan=============//
    Route::get('/alasan', [AdminController::class, 'indexAlasan'])->name('admin.alasan.index');
    Route::post('/alasan', [AdminController::class, 'storeAlasan'])->name('admin.alasan.store');
    Route::put('/alasan/{id}', [AdminController::class, 'updateAlasan'])->name('admin.alasan.update');
    Route::delete('/alasan/{alasan}', [AdminController::class, 'destroyAlasan'])->name('admin.alasan.destroy');
    //============unutk desa=============//

    Route::get('/admin/desa', [AdminController::class, 'desaIndex'])->name('admin.desa.index');
    Route::post('/admin/desa', [AdminController::class, 'desaStore'])->name('admin.desa.store');
    Route::get('/admin/desa/{id}/edit', [AdminController::class, 'desaEdit'])->name('admin.desa.edit');
    Route::put('/admin/desa/{id}', [AdminController::class, 'desaUpdate'])->name('admin.desa.update');
    Route::delete('/admin/desa/{id}', [AdminController::class, 'desaDestroy'])->name('admin.desa.destroy');
});
Route::middleware(['auth', 'role:karyawan'])->group(function () {
    Route::get('/karyawan', [KaryawanController::class, 'index'])->name('karyawan.dashboard');
    //============unutk penumpang=============//
    Route::get('/karyawan/penumpang', [KaryawanController::class, 'indexPenumpang'])->name('karyawan.penumpang');
    // Route::post('/karyawan/penumpang', [KaryawanController::class, 'storePenumpang'])->name('karyawan.storePenumpang');
    Route::post('/karyawan/store-penumpang', [KaryawanController::class, 'storePenumpang'])->name('karyawan.storePenumpang');
    Route::put('/karyawan/penumpang/{id}', [KaryawanController::class, 'updatePenumpang'])->name('karyawan.updatePenumpang');
    Route::delete('/karyawan/penumpang/{id}', [KaryawanController::class, 'deletePenumpang'])->name('karyawan.deletePenumpang');
    Route::get('/karyawan/kelola-penumpang', [KaryawanController::class, 'kelolaPenumpang'])->name('karyawan.kelolaPenumpang');
    Route::get('/karyawan/penumpang/edit/{id}', [KaryawanController::class, 'editPenumpang'])->name('penumpang.edit');
    Route::delete('/karyawan/penumpang/delete/{id}', [KaryawanController::class, 'deletePenumpang'])->name('penumpang.delete');
    // Route Update
    Route::post('/karyawan/penumpang/update/{id}', [KaryawanController::class, 'updatePenumpang'])->name('penumpang.update');
    //===============tiket===========//
    Route::get('/tiket', [TiketController::class, 'index'])->name('karyawan.tiket');
    Route::get('/tiket/create', [TiketController::class, 'create'])->name('karyawan.tiket.create');
    Route::post('/tiket', [TiketController::class, 'store'])->name('karyawan.tiket.store');
    // Route::put('/karyawan/tiket/{id}', [KaryawanController::class, 'update'])->name('karyawan.tiket.update');
    Route::get('/tiket/{id}/edit', [TiketController::class, 'edit'])->name('karyawan.tiket.edit');
    Route::put('/tiket/{id}', [TiketController::class, 'update'])->name('karyawan.tiket.update');
    Route::get('/karyawan/tiket/riwayat', [TiketController::class, 'riwayat'])->name('karyawan.tiket.riwayat');
    // Route untuk menghapus satu penumpang dari tiket
    Route::delete('/karyawan/tiket/{tiket_id}/hapus-penumpang/{penumpang_id}', [TiketController::class, 'hapusPenumpangDariTiket'])
        ->name('karyawan.tiket.removePenumpang');
    // route untuk menghapus tiket beserta penumpang nyab
    Route::delete('/karyawan/tiket/{id}', [TiketController::class, 'destroy'])->name('karyawan.tiket.destroy');


    //======ktp scan======//
    Route::post('/scan-ktp', [KaryawanController::class, 'scanKtp'])->name('scan.ktp');
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['auth'])->name('dashboard');
    Route::post('/scan-ktp', [KaryawanController::class, 'scanKtp'])->name('scan.ktp');
    Route::get('/tiket/cetak/{tiket_id}/{penumpang_id}', [TiketController::class, 'cetakPerPenumpang'])
        ->name('tiket.cetak_penumpang');
});




// 🚌 Route untuk Karyawan (Tracking & Rute)
Route::get('/karyawan/tracking', [TrackingController::class, 'show'])->name('karyawan.tracking');
Route::post('/tracking/kirim', [TrackingController::class, 'kirim'])->name('tracking.kirim');
Route::post('/tracking/update-posisi', [TrackingController::class, 'updatePosisi'])->name('tracking.update_posisi');
Route::get('/tracking/posisi-terkini', [TrackingController::class, 'posisiTerkini'])->name('tracking.posisi_terkini');
Route::post('/tracking/update-status', [TrackingController::class, 'updateStatusOtomatis'])->name('tracking.update_status_otomatis');
Route::delete('/tracking/hapus/{id}', [TrackingController::class, 'hapus'])->name('tracking.hapus');
Route::get('/tracking/data-terakhir', [TrackingController::class, 'dataTerakhir'])->name('tracking.data_terakhir');

//=======driver=========//
// ✅ Route spesifik dulu
Route::get('/driver', [DriverController::class, 'index'])->name('driver.index');
Route::get('/driver/lokasidriver/{tiket_id}', [DriverController::class, 'lokasiDriver'])->name('driver.lokasidriver');
Route::get('/driver/map', function () {
    return view('driver.map');
})->name('driver.map');
Route::post('/driver/laporan/{id}/simpan', [DriverController::class, 'simpanLaporan'])->name('driver.simpan_laporan');
Route::get('/driver/scan-tiket', [DriverController::class, 'scanTiket'])->name('driver.scanTiket');
Route::post('/tiket/{id}/selesaikan', [TiketController::class, 'selesaikan'])->name('tiket.selesaikan');

// ❗ PALING AKHIR! Agar tidak menelan route lain
Route::get('/driver/{id}', [DriverController::class, 'show'])->name('driver.show');

Route::get('/hasil-scan/{tiket_id}/{penumpang_id}', [TiketController::class, 'hasilScanIndividual'])->name('hasil.scan.individual');


Route::post('/penumpang/aksi/{id}', [TiketController::class, 'setStatusPenumpang'])
    ->middleware(['auth', 'role:admin,karyawan,driver'])
    ->name('penumpang.aksi');



//route untuk mencerak tiket ke barcode 
Route::get('/karyawan/tiket/{id}/cetak', [TiketController::class, 'cetak'])->name('karyawan.tiket.cetak');
Route::post('/karyawan/tiket/{id}/mulai', [TiketController::class, 'mulai'])->name('karyawan.tiket.mulai');
Route::post('/karyawan/laporan/simpan', [TiketController::class, 'simpanLaporan'])->name('karyawan.simpan_laporan');
Route::get('/karyawan/laporan', [TiketController::class, 'pratinjauLaporan'])->name('karyawan.pratinjau_laporan');
Route::post('/karyawan/laporan/hapus', [TiketController::class, 'hapusLaporan'])->name('karyawan.hapus_laporan');
Route::post('/karyawan/laporan/reset', [TiketController::class, 'resetLaporan'])->name('karyawan.reset_laporan');
Route::post('/karyawan/laporan/cetak-per-card', [TiketController::class, 'exportLaporanPDFPerCard'])->name('karyawan.cetak_pdf_per_card');
Route::post('/karyawan/laporan/export-excel', [TiketController::class, 'exportExcelPerCard'])->name('karyawan.export_excel_per_card');
Route::post('/karyawan/laporan/export-word', [TiketController::class, 'exportWordPerCard'])->name('karyawan.export_word_per_card');
Route::delete('/karyawan/tiket/{id}', [TiketController::class, 'destroy'])->name('karyawan.tiket.destroy');



// 🌍 Route untuk Update Lokasi Karyawan
Route::post('/update-lokasi', [KaryawanController::class, 'updateLocation']);
// Rute untuk menampilkan lokasi di halaman view
Route::get('/lokasi-bus', [KaryawanController::class, 'showLocations']);
// Rute untuk mendapatkan data lokasi dalam format JSON
Route::get('/lokasi-bus-json', [KaryawanController::class, 'getAllLocations']);


// Route Unauthorized (jika user tidak punya akses)
Route::get('/unauthorized', function () {
    return view('unauthorized'); // Mengarahkan ke halaman blade unauthorized
})->name('unauthorized');

// Halaman Welcome (tanpa autentikasi)
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Dashboard (hanya untuk user yang terverifikasi)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile Routes (hanya untuk user yang login)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// Auth Routes (login, logout, dll)
require __DIR__ . '/auth.php';
// Letakkan di luar semua group middleware auth
Route::get('/hasil-scan/{id}', [TiketController::class, 'hasilScan'])->name('hasil.scan');
