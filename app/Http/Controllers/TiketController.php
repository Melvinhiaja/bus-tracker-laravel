<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tiket;
use App\Models\Penumpang;
use App\Models\Bus;
use App\Models\Alasan;
use App\Models\Desa;
use App\Models\Perjalanan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Arr;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanPerCardExport;




class TiketController extends Controller
{
    //  Menampilkan daftar tiket
    public function index()
    {
        $today = Carbon::today();

        $tikets = Tiket::with(['penumpangs', 'bus', 'alasan', 'perjalanan'])
            ->whereDate('tanggal_berangkat', '>=', $today)
            ->whereIn('status', ['aktif', 'berjalan']) // 🔥 Tambahkan ini!
            ->orderBy('tanggal_berangkat')
            ->get();

        return view('karyawan.tiket', compact('tikets'));
    }





    //  Menampilkan form pemesanan tiket
    public function create()
    {
        $desas = Desa::all();
        $penumpangs = Penumpang::all();
        $buses = Bus::all();
        $perjalanans = Perjalanan::all();
        $alasans = Alasan::all();

        // Tambahkan pengecekan kapasitas bus di sini
        foreach ($buses as $bus) {
            $tiket_terpakai = Tiket::where('bus_id', $bus->id)
                ->where('status', '!=', 'selesai')
                ->withCount('penumpangs')
                ->get();

            $bus->penumpang_terpakai = $tiket_terpakai->sum('penumpangs_count');
        }

        return view('karyawan.tiket_create', compact('desas', 'penumpangs', 'buses', 'perjalanans', 'alasans'));
    }


    public function store(Request $request)
    {
        Log::info('🔍 Request masuk ke store()', $request->all());

        $validatedData = $request->validate([
            'desa_id' => 'required|array',
            'desa_id.*' => 'exists:desas,id',
            'tanggal_berangkat' => 'required|date',
            'tanggal_pulang' => 'nullable|date|after_or_equal:tanggal_berangkat',
            'penumpang_id' => 'required|array|min:1',
            'penumpang_id.*' => 'exists:penumpangs,id',
            'perjalanan_id' => 'required|exists:perjalanans,id',
            'bus_id' => 'required|exists:buses,id',
            'alasan_id' => 'required|array',
            'alasan_kostum' => 'nullable|array',
        ]);

        return DB::transaction(function () use ($request) {
            $penumpang_terdaftar = Penumpang::whereIn('id', $request->penumpang_id)
                ->whereHas('tiket', function ($query) {
                    $query->whereDate('tanggal_berangkat', '>=', \Carbon\Carbon::today());
                })
                ->exists();

            if ($penumpang_terdaftar) {
                return redirect()->back()->with('error', 'Salah satu penumpang sudah memiliki tiket.');
            }

            $bus_terpilih = Bus::findOrFail($request->bus_id);
            $kapasitas_bus = $bus_terpilih->kapasitas;

            $tiket_terpakai = Tiket::where('bus_id', $bus_terpilih->id)
                ->where('tanggal_berangkat', $request->tanggal_berangkat)
                ->withCount('penumpangs')
                ->get();

            $total_penumpang_saat_ini = $tiket_terpakai->sum('penumpangs_count');
            $jumlah_penumpang_baru = count($request->penumpang_id);
            $sisa_kursi = max(0, $kapasitas_bus - $total_penumpang_saat_ini);

            if ($jumlah_penumpang_baru > $sisa_kursi) {
                return redirect()->back()->with('error', "Tiket tidak cukup! Hanya tersedia $sisa_kursi kursi.");
            }

            $tiket = Tiket::where('bus_id', $request->bus_id)
                ->where('perjalanan_id', $request->perjalanan_id)
                ->where('tanggal_berangkat', $request->tanggal_berangkat)
                ->where(function ($query) use ($request) {
                    if ($request->tanggal_pulang) {
                        $query->where('tanggal_pulang', $request->tanggal_pulang);
                    } else {
                        $query->whereNull('tanggal_pulang');
                    }
                })
                ->first();

            if (!$tiket) {
                $tiket = Tiket::create([
                    'desa_id' => $request->desa_id[0],
                    'bus_id' => $request->bus_id,
                    'perjalanan_id' => $request->perjalanan_id,
                    'tanggal_berangkat' => $request->tanggal_berangkat,
                    'tanggal_pulang' => $request->tanggal_pulang,
                    'jumlah_penumpang' => count($request->penumpang_id),
                    'status' => 'aktif',

                ]);
            } else {
                // Jika tiket sudah ada, update jumlah penumpangnya juga
                $tiket->save();
            }

            // Simpan data per penumpang
            foreach ($request->penumpang_id as $id) {
                if (count($request->penumpang_id) == 1) {
                    // Jika hanya satu penumpang, pakai alasan per individu
                    $alasan_id = $request->alasan_id[$id] ?? null;
                    $alasan_kostum = $request->alasan_kostum[$id] ?? null;
                } else {
                    // Jika lebih dari satu, pakai alasan global (all)
                    $alasan_id = $request->alasan_id['all'] ?? null;
                    $alasan_kostum = $request->alasan_kostum['all'] ?? null;
                }

                Penumpang::where('id', $id)->update([
                    'tiket_id' => $tiket->id,
                    'alasan_id' => $alasan_id,
                    'alasan_kostum' => $alasan_kostum,
                ]);
            }


            return redirect()->route('karyawan.tiket')->with('success', 'Tiket berhasil dipesan!');
        });
    }






    //  Menampilkan form edit tiket
    public function edit($id)
    {
        $tiket = Tiket::findOrFail($id);
        $desas = Desa::all();
        $buses = Bus::all();
        $perjalanans = Perjalanan::all();
        $alasans = Alasan::all();

        // Ambil hanya penumpang yang masih terdaftar di tiket ini
        $penumpangs = Penumpang::where('tiket_id', $id)->get();

        return view('karyawan.edittiket', compact('tiket', 'desas', 'penumpangs', 'buses', 'perjalanans', 'alasans'));
    }

    //  Mengupdate tiket
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'desa_id' => 'required|array',
            'desa_id.*' => 'exists:desas,id',
            'alasan_id' => 'required|exists:alasans,id',
            'alasan_kostum' => 'nullable|string',
            'tanggal_berangkat' => 'required|date',
            'tanggal_pulang' => 'nullable|date|after_or_equal:tanggal_berangkat',
            'penumpang_id' => 'required|array|min:1',
            'penumpang_id.*' => 'exists:penumpangs,id',
            'bus_id' => 'required|exists:buses,id',
            'perjalanan_id' => 'required|exists:perjalanans,id',
        ]);

        return DB::transaction(function () use ($request, $id) {
            $tiket = Tiket::findOrFail($id);
            $tiket->update([
                'desa_id' => json_encode($request->desa_id),
                'bus_id' => $request->bus_id,
                'alasan_id' => $request->alasan_id,
                'alasan_kostum' => $request->alasan_kostum,
                'perjalanan_id' => $request->perjalanan_id,
                'tanggal_berangkat' => $request->tanggal_berangkat,
                'tanggal_pulang' => $request->tanggal_pulang,
            ]);

            // Hapus hubungan penumpang lama dan tambahkan yang baru
            Penumpang::where('tiket_id', $tiket->id)->update(['tiket_id' => null]);

            foreach ($request->penumpang_id as $penumpangId) {
                Penumpang::where('id', $penumpangId)->update(['tiket_id' => $tiket->id]);
            }

            return redirect()->route('karyawan.tiket')->with('success', 'Tiket berhasil diperbarui!');
        });
    }



    //   Menghapus tiket
    public function destroy($id)
    {
        $tiket = Tiket::findOrFail($id);

        // Pastikan semua penumpang di tiket ini dihapus relasinya
        Penumpang::where('tiket_id', $tiket->id)->update(['tiket_id' => null]);

        // Hapus tiket
        $tiket->delete();

        return redirect()->route('karyawan.tiket')->with('success', 'Tiket berhasil dihapus.');
    }


    public function hapusPenumpangDariTiket($tiket_id, $penumpang_id)
    {
        try {
            Log::info("hapusPenumpangDariTiket: tiket_id={$tiket_id}, penumpang_id={$penumpang_id}");

            $penumpang = Penumpang::findOrFail($penumpang_id);

            if ($penumpang->tiket_id != $tiket_id) {
                Log::info("Penumpang tidak terdaftar di tiket ini.");
                return response()->json(['message' => 'Penumpang tidak terdaftar di tiket ini.'], 400);
            }

            $penumpang->update(['tiket_id' => null]);

            Log::info("Penumpang {$penumpang_id} berhasil dihapus dari tiket {$tiket_id}.");

            return response()->json(['message' => 'Penumpang berhasil dihapus dari tiket.']);
        } catch (\Exception $e) {
            Log::error("Gagal menghapus penumpang dari tiket: " . $e->getMessage());
            Log::error("Error Details: " . $e->getMessage() . " Trace: " . $e->getTraceAsString());
            return response()->json(['message' => 'Terjadi kesalahan saat menghapus penumpang.'], 500);
        }
    }


     //buat cetak tiket ke barcode 


    public function cetak($id)
    {
        $tiket = Tiket::with(['penumpangs', 'bus', 'perjalanan'])->findOrFail($id);
    
        // Buat URL untuk halaman hasil scan
        $url = url('/hasil-scan/' . $id);
    
        // Generate QR Code dengan URL
        $barcode = QrCode::size(300)->generate($url);
    
        return view('karyawan.cetak_tiket', compact('tiket', 'barcode'))->render();
    }
    
    public function hasilScan($id)
    {
        $tiket = Tiket::with(['penumpangs', 'bus', 'perjalanan'])->findOrFail($id);
    
        // Tambahan: kalau user login, set semua penumpang jadi 'naik'
        if (auth()->check()) {
            $laporanScan = session('laporan_scan', []);
            foreach ($tiket->penumpangs as $penumpang) {
                $laporanScan["{$id}_{$penumpang->id}"] = 'naik';
            }
            session(['laporan_scan' => $laporanScan]);
        }
    
        return view('karyawan.hasilscan', compact('tiket'));
    }
    
    public function cetakPerPenumpang($tiket_id, $penumpang_id)
    {
        $tiket = Tiket::with(['penumpangs', 'bus', 'perjalanan'])->findOrFail($tiket_id);
        $penumpang = $tiket->penumpangs->where('id', $penumpang_id)->first();
    
        if (!$penumpang) {
            abort(404, 'Penumpang tidak ditemukan dalam tiket ini.');
        }
    
        // URL yang akan di-scan QR nya
        $scanUrl = route('hasil.scan.individual', ['tiket_id' => $tiket_id, 'penumpang_id' => $penumpang_id], true);
        $barcode = QrCode::size(200)->generate($scanUrl);
    
        return view('karyawan.cetak_tiket_penumpang', compact('tiket', 'penumpang', 'barcode'));
    }
    
    public function hasilScanIndividual(Request $request, $tiket_id, $penumpang_id)
    {
        $tiket = Tiket::with(['penumpangs', 'bus', 'perjalanan'])->findOrFail($tiket_id);
        $penumpang = $tiket->penumpangs->where('id', $penumpang_id)->first();
    
        if (!$penumpang) {
            abort(404, 'Penumpang tidak ditemukan dalam tiket ini.');
        }
    
        // Tambahan: kalau user login, set penumpang ini jadi 'naik'
        if (auth()->check()) {
            $laporanScan = session('laporan_scan', []);
            $laporanScan["{$tiket_id}_{$penumpang_id}"] = 'naik';
            session(['laporan_scan' => $laporanScan]);
        }
    
        return view('karyawan.hasilscan_individual', compact('tiket', 'penumpang'));
    }
    
    // Tiket Aktif: tanggal berangkat hari ini atau yang akan datang
    public function aktif()
    {
        $today = Carbon::today();
        $tikets = Tiket::with(['penumpangs', 'bus', 'alasan', 'perjalanan'])
            ->whereDate('tanggal_berangkat', '>=', $today)
            ->orderBy('tanggal_berangkat')
            ->get();

        return view('karyawan.tiket', compact('tikets'));
    }


    public function mulai($id)
    {
        $tiket = Tiket::findOrFail($id);
        $tiket->status = 'berjalan';
        $tiket->save();

        return redirect()->route('karyawan.tiket')->with('success', 'Perjalanan dimulai dan tiket dipindahkan ke halaman Driver.');
    }
    //tinjau laporan

    // Untuk menyimpan laporan ke session
    public function simpanLaporan(Request $request)
    {
        $laporanData = json_decode($request->laporan, true);
        $tiket_id = $request->input('tiket_id');
        $laporanScan = session()->get('laporan_scan', []);

        foreach ($laporanData as &$data) {
            foreach ($laporanScan as $key => $status) {
                // Format key adalah tiket_id_penumpang_id
                if (str_starts_with($key, "{$tiket_id}_")) {
                    // Ambil ID penumpang dari key
                    $parts = explode('_', $key);
                    $penumpangId = end($parts);

                    // Cocokkan dengan NIK
                    if (isset($data['nik']) && isset($data['id']) && $data['id'] == $penumpangId) {
                        $data['status'] = $status === 'naik' ? 'Naik' : 'Tidak Naik';
                    }
                }
            }

            // Default jika tidak ditemukan di session
            if (!isset($data['status'])) {
                $data['status'] = '-';
            }
        }

        // Simpan ke session laporan_karyawan
        $laporanSebelumnya = session()->get('laporan_karyawan', []);
        $gabung = array_merge($laporanSebelumnya, $laporanData);
        session()->put('laporan_karyawan', $gabung);

        return redirect()->route('karyawan.pratinjau_laporan')->with('success', 'Laporan berhasil disimpan!');
    }




    // Untuk menampilkan semua laporan
    public function pratinjauLaporan()
    {
        $laporan = session()->get('laporan_karyawan', []);
        return view('karyawan.pratinjau_laporan', compact('laporan'));
    }
    public function hapusLaporan(Request $request)
    {
        $key = $request->key;
        $laporan = session()->get('laporan_karyawan', []);
        $filtered = collect($laporan)->reject(function ($item) use ($key) {
            $groupKey = ($item['bus'] ?? '-') . '|' . ($item['tanggal_berangkat'] ?? '-') . '|' . ($item['asal'] ?? '-') . '|' . ($item['tujuan'] ?? '-');
            return $groupKey === $key;
        })->values()->all();

        session()->put('laporan_karyawan', $filtered);

        return redirect()->route('karyawan.pratinjau_laporan')->with('success', 'Laporan berhasil dihapus.');
    }
    public function resetLaporan()
    {
        session()->forget('laporan_karyawan');
        return redirect()->route('karyawan.pratinjau_laporan')->with('success', 'Semua laporan berhasil direset.');
    }

    public function exportLaporanPDFPerCard(Request $request)
    {
        $data = json_decode(base64_decode($request->data), true);

        if (!$data || !is_array($data)) {
            return redirect()->back()->with('error', 'Data tidak valid.');
        }

        $pdf = Pdf::loadView('karyawan.pdf_laporan_per_card', ['laporan' => $data]);
        return $pdf->stream('laporan_perjalanan.pdf');
    }
    public function exportExcelPerCard(Request $request)
    {
        $laporanData = json_decode(base64_decode($request->data), true);
        return Excel::download(new LaporanPerCardExport($laporanData), 'laporan_perjalanan.xlsx');
    }
    public function exportWordPerCard(Request $request)
    {
        $laporan = json_decode(base64_decode($request->data), true);

        $content = '<h2>Laporan Perjalanan</h2><table border="1" cellspacing="0" cellpadding="5">';
        $content .= '<tr><th>No</th><th>Nama</th><th>Desa</th><th>Jenis Kelamin</th><th>NIK</th><th>TTL</th><th>Alasan</th><th>Alasan Kostum</th><th>Status</th></tr>';

        foreach ($laporan as $i => $row) {
            $content .= '<tr>';
            $content .= '<td>' . ($i + 1) . '</td>';
            $content .= '<td>' . $row['nama'] . '</td>';
            $content .= '<td>' . $row['desa'] . '</td>';
            $content .= '<td>' . $row['jenis_kelamin'] . '</td>';
            $content .= '<td>' . $row['nik'] . '</td>';
            $content .= '<td>' . $row['ttl'] . '</td>';
            $content .= '<td>' . $row['alasan'] . '</td>';
            $content .= '<td>' . $row['alasan_kostum'] . '</td>';
            $content .= '<td>' . $row['status'] . '</td>';
            $content .= '</tr>';
        }

        $content .= '</table>';

        $headers = [
            "Content-type" => "application/vnd.ms-word",
            "Content-Disposition" => "attachment;Filename=laporan_perjalanan.doc"
        ];

        return response($content, 200, $headers);
    }
    public function riwayat()
    {
        $today = \Carbon\Carbon::today();

        $tikets = \App\Models\Tiket::with(['bus', 'alasan', 'perjalanan', 'penumpangs'])
            ->where('status', ['selesai']) // tampilkan hanya tiket yang statusnya "selesai"
            ->orderByDesc('tanggal_berangkat')
            ->get();

        return view('karyawan.riwayat_tiket', compact('tikets'));
    }
    public function selesaikan($id)
    {
        $tiket = Tiket::with('penumpangs')->findOrFail($id);

        $tiket->status = 'selesai';
        $tiket->save();

        // Reset data penumpang
        Penumpang::where('tiket_id', $tiket->id)->update([
            'tiket_id' => null,
            'alasan_id' => null,
            'alasan_kostum' => null,
        ]);

        return redirect()->route('karyawan.tiket')->with('success', 'Perjalanan berhasil diselesaikan dan dipindahkan ke riwayat.');
    }
    public function setStatusPenumpang(Request $request, $id)
    {
        $status = $request->input('status'); // naik / tidak_naik
        $tiket_id = $request->input('tiket_id');

        // Ambil session lama
        $laporan = session()->get('laporan_scan', []);

        // Simpan status dengan key kombinasi tiket_id dan penumpang_id
        $laporan["{$tiket_id}_{$id}"] = $status;

        // Simpan kembali ke session
        session()->put('laporan_scan', $laporan);

        return back()->with('success', 'Status berhasil disimpan.');
    }
}
