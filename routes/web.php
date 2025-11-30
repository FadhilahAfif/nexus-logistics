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

// --- DEBUG ROUTE (HANYA SEMENTARA) ---
Route::get('/debug-dashboard', function () {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $results = [];

    try {
        // 1. Test Recent Shipments Query
        $results['RecentShipments'] = \App\Models\Shipment::query()->latest()->limit(5)->get();
        echo "✅ RecentShipments: OK <br>";
    } catch (\Throwable $e) {
        echo "❌ RecentShipments Error: " . $e->getMessage() . "<br>";
    }

    try {
        // 2. Test Donut Chart Query
        $results['Donut'] = \App\Models\Shipment::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
        echo "✅ Donut Chart: OK <br>";
    } catch (\Throwable $e) {
        echo "❌ Donut Chart Error: " . $e->getMessage() . "<br>";
    }

    try {
        // 3. Test Trend Chart Query (The likely culprit)
        $results['Trend'] = \App\Models\Shipment::query()
            ->selectRaw("TO_CHAR(created_at, 'YYYY-MM-DD') as date, COUNT(*) as total")
            ->where('created_at', '>=', \Illuminate\Support\Carbon::today()->subDays(6))
            ->groupBy('date')
            ->pluck('total', 'date');
        echo "✅ Trend Chart: OK <br>";
    } catch (\Throwable $e) {
        echo "❌ Trend Chart Error: " . $e->getMessage() . "<br>";
    }
});