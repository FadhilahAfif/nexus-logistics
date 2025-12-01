<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function index()
    {
        $metrics = [
            'total' => Shipment::count(),
            'delivered' => Shipment::where('status', 'delivered')->count(),
            'in_transit' => Shipment::where('status', 'in_transit')->count(),
            'pending' => Shipment::where('status', 'pending')->count(),
        ];

        return view('welcome', compact('metrics'));
    }

    public function track(Request $request)
    {
        // 1. Validasi Keamanan Input (Sanitization)
        $request->validate([
            'tracking_number' => 'required|string|max:50|alpha_dash', // Hanya huruf, angka, dash
        ], [
            'tracking_number.required' => 'Nomor resi wajib diisi!',
            'tracking_number.alpha_dash' => 'Format resi tidak valid (hanya huruf & angka).',
        ]);

        $resi = trim($request->tracking_number);

        // 2. Cari Data Paket + History (Diurutkan dari terbaru)
        $shipment = Shipment::with(['updates' => function ($query) {
            $query->orderBy('happened_at', 'desc');
        }])->where('tracking_number', $resi)->first();

        // 3. Jika Tidak Ketemu -> Balikin user dengan pesan error
        if (!$shipment) {
            return back()->with('error', "Nomor Resi '$resi' tidak ditemukan di database kami.");
        }

        // 4. Jika Ketemu -> Tampilkan Halaman Hasil
        return view('tracking-result', compact('shipment'));
    }
}