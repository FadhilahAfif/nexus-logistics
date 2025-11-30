<?php

use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan; // Tambahan untuk migrasi

// Halaman Depan (Bebas akses)
Route::get('/', [TrackingController::class, 'index'])->name('home');

// Halaman Hasil & Proses Lacak (Dibatasi 10x per menit via middleware 'throttle')
Route::get('/track', [TrackingController::class, 'track'])
    ->middleware('throttle:10,1') 
    ->name('track');

// --- ROUTE KHUSUS MIGRASI (HANYA SEMENTARA) ---
Route::get('/setup-database', function () {
    // Paksa tampilkan error jika ada
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    try {
        // 1. Jalankan Migrasi (Force karena production)
        Artisan::call('migrate', ["--force" => true]);
        $output = Artisan::output();

        // 2. Jalankan Seeder (Untuk isi data awal user admin)
        Artisan::call('db:seed', ["--force" => true]);
        $output .= "\n" . Artisan::output();
        
        // 3. Clear Cache (Opsional, biar fresh)
        Artisan::call('optimize:clear');
        $output .= "\n" . Artisan::output();

        return "<pre>BERHASIL! Database sudah dibuat dan diisi data awal.\n\nLog:\n$output</pre>";
    } catch (\Throwable $e) {
        return "<pre>GAGAL:\n" . $e->getMessage() . "\n\nTrace:\n" . $e->getTraceAsString() . "</pre>";
    }
});