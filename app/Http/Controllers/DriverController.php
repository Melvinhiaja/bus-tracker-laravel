<?php

namespace App\Http\Controllers;

use App\Models\Penumpang;
use App\Models\Tiket;
use App\Models\TrackingPosition;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    // Menampilkan daftar tiket aktif untuk driver
    public function index()
    {
        $tikets = Tiket::with(['bus', 'perjalanan', 'penumpangs'])
            ->where('status', 'berjalan')
            ->get();

        // Cari tracking aktif yang status "Berjalan"
        $trackingAktif = TrackingPosition::where('status', 'Berjalan')->first();

        return view('driver.driver', compact('tikets', 'trackingAktif'));
    }

    // Menampilkan detail perjalanan
    public function show($id)
    {
        $tiket = Tiket::with(['penumpangs', 'bus', 'perjalanan'])->findOrFail($id);
        return view('driver.show', compact('tiket'));
    }

    // Menampilkan riwayat tiket selesai
    public function riwayat()
    {
        $tikets = Tiket::with(['bus', 'perjalanan', 'penumpangs'])
            ->where('status', 'selesai')
            ->orderByDesc('tanggal_berangkat')
            ->get();

        return view('driver.riwayat_tiket', compact('tikets'));
    }

    // Simpan laporan sementara ke session
    public function simpanLaporan(Request $request, $id)
    {
        $laporanData = json_decode($request->laporan_data, true);
        session()->put('laporan_terakhir', $laporanData);
        return redirect()->route('karyawan.laporan')->with('success', 'Laporan berhasil disimpan!');
    }

    // Selesaikan perjalanan (redirect ke TiketController)
    public function selesaikan($id)
    {
        return redirect()->route('tiket.selesaikan', $id);
    }

    public function lokasiDriver($tiket_id)
    {
        $tiket = Tiket::with('bus')->findOrFail($tiket_id);

        //  Ambil tracking aktif untuk bus ini (jika ada)
        $trackingAktif = TrackingPosition::where('bus_id', $tiket->bus_id)
            ->where('status', 'Berjalan')
            ->first();

        if ($trackingAktif) {
            //  Kalau tracking aktif & user bukan yang login, blokir akses
            if ($trackingAktif->user_id !== auth()->id()) {
                return redirect()->route('driver.index')->with('error', 'Tiket ini sedang berjalan oleh driver lain.');
            }
            //  Kalau user sama → boleh masuk
        } else {
            //  Cek apakah user ini sudah punya tracking aktif di bus lain
            $userTracking = TrackingPosition::where('user_id', auth()->id())
                ->where('status', 'Berjalan')
                ->where('bus_id', '!=', $tiket->bus_id)
                ->exists();

            if ($userTracking) {
                return redirect()->route('driver.index')->with('error', 'Kamu sudah berbagi lokasi di tiket lain!');
            }
        }

        // Lolos semua → boleh masuk
        $namaBus = $tiket->bus->nomor_bus ?? 'Bus Tidak Diketahui';
        return view('driver.lokasidriver', compact('namaBus', 'tiket'));
    }



    public function scanTiket()
    {
        return view('driver.scan_tiket');
    }
}
