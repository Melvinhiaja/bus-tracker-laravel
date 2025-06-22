<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrackingPosition;
use Illuminate\Support\Facades\Log;



class TrackingController extends Controller
{
    // Menampilkan semua data tracking ke halaman monitoring
    public function show()
    {
        $data = TrackingPosition::with('bus.tikets.perjalanan')
            ->where('status', 'Berjalan')
            ->latest()
            ->get();

        return view('karyawan.tracking', compact('data'));
    }


    // Menerima lokasi awal, tujuan, status dan rute via fetch() dari lokasidriver.blade.php
    public function kirim(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $busId = $data['bus_id']; 

        // Cek apakah ada tiket berjalan untuk bus ini
        $tiketBerjalan = \App\Models\Tiket::where('status', 'berjalan')
            ->where('bus_id', $busId)
            ->first();

        if (!$tiketBerjalan) {
            return response()->json(['success' => false, 'message' => 'Tidak ada tiket berjalan untuk bus ini.']);
        }

        // Cek apakah sudah ada tracking aktif untuk bus ini
        $cekSudahAda = \App\Models\TrackingPosition::where('bus_id', $busId)
            ->where('status', 'Berjalan')
            ->exists();

        if ($cekSudahAda) {
            return response()->json(['success' => false, 'message' => 'Tracking sudah aktif untuk bus ini.']);
        }

        // Kalau belum ada, simpan tracking baru
        \App\Models\TrackingPosition::create([
            'bus_id'        => $busId,
            'user_id'       => auth()->id(),
            'lokasi_awal'   => $data['lokasi_awal'],
            'lokasi_tujuan' => $data['lokasi_tujuan'],
            'status'        => $data['status'],
            'rute'          => $data['rute'],
            'posisi_driver' => [],
            'waktu_tempuh'  => $data['waktu_tempuh'] ?? null,
            'jarak_tempuh'  => $data['jarak_tempuh'] ?? null,
            'waktu'         => now()->format('H:i:s d-m-Y'),
            'rute_asal'     => $data['rute_asal'],
            'rute_tujuan'   => $data['rute_tujuan'],
        ]);

        return response()->json(['success' => true, 'message' => 'Tracking berhasil disimpan.']);
    }


    // Menampilkan list data tracking (tidak wajib dipakai)
    public function index()
    {
        $data = TrackingPosition::latest()->get();
        return view('karyawan.tracking', compact('data'));
    }
    public function updatePosisi(Request $request)
    {
        // \Log::info(' Method updatePosisi() dipanggil!');

        $posisi = $request->input('posisi_driver');
        $busId = $request->input('bus_id');

        // \Log::info('Diterima Bus ID:', [$busId]);
        // \Log::info(' Diterima Posisi:', $posisi);

        $tracking = TrackingPosition::where('bus_id', $busId)
            ->where('status', 'Berjalan')
            ->latest()
            ->first();

        if ($tracking) {
            $tracking->update([
                'posisi_driver' => $posisi
            ]);
            // \Log::info(' Tracking berhasil diupdate untuk bus_id: ' . $busId);
        } else {
            // \Log::warning(' Tracking tidak ditemukan untuk bus_id: ' . $busId);
        }

        return response()->json(['success' => true]);
    }



    // Mengambil posisi terbaru untuk ditampilkan di marker bus
    public function posisiTerkini()
    {
        $all = TrackingPosition::where('status', 'Berjalan')
            ->get(['id', 'bus_id', 'posisi_driver']); // 🔥 Tambahin 'id'
        return response()->json($all);
    }



    // Otomatis update status terakhir menjadi "Sampai"
    public function updateStatusOtomatis(Request $request)
    {
        $last = TrackingPosition::latest()->first();

        if ($last && $last->status !== 'Sampai') {
            // 1. Update status tracking jadi "Sampai"
            $last->update(['status' => 'Sampai']);

            //  2. Sinkron ke tiket, update status tiket jadi "selesai"
            $tiket = \App\Models\Tiket::where('bus_id', $last->bus_id)
                ->where('status', 'berjalan')
                ->latest()
                ->first();

            if ($tiket) {
                $tiket->update(['status' => 'selesai']);

                //  3. Reset data penumpang di tiket ini
                \App\Models\Penumpang::where('tiket_id', $tiket->id)->update([
                    'tiket_id' => null,
                    'alasan_id' => null,
                    'alasan_kostum' => null,
                ]);
            }
        }

        return response()->json(['status' => 'updated']);
    }



    // Hapus data tracking
    public function hapus($id)
    {
        TrackingPosition::findOrFail($id)->delete();
        return back()->with('success', 'Data tracking berhasil dihapus.');
    }
    public function dataTerakhir()
    {
        $last = TrackingPosition::latest()->first();

        if ($last && $last->status === 'Berjalan') {
            return response()->json([
                'lokasi_awal' => $last->lokasi_awal,
                'lokasi_tujuan' => $last->lokasi_tujuan,
                'rute' => $last->rute,
                'status' => $last->status,
                'waktu_tempuh'  => $last->waktu_tempuh,
                'jarak_tempuh'  => $last->jarak_tempuh,
                'nama_bus'      => $last->bus->nomor_bus ?? '-'

            ]);
        }

        return response()->json(null);
    }
}
