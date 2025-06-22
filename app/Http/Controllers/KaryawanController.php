<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\Bus;
use App\Models\Desa;
use App\Models\Penumpang;
use App\Models\Tiket;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use RealRashid\SweetAlert\Facades\Alert;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\DB;
use App\Models\LokasiBus;
use Illuminate\Http\Request;




class KaryawanController extends Controller
{
    public function scanKtp(Request $request)
    {
        try {
            // Validasi file KTP
            $request->validate([
                'foto_ktp' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            // Simpan gambar ke storage
            $file = $request->file('foto_ktp');
            $imagePath = $file->store('ktp_images', 'public');

            // Pastikan file tersimpan
            if (!Storage::disk('public')->exists($imagePath)) {
                return response()->json(['error' => 'Gagal menyimpan gambar.'], 500);
            }

            // Jalankan OCR
            $ocr = new TesseractOCR(storage_path("app/public/{$imagePath}"));
            $ocr->psm(6)->oem(3)->lang('ind + ocrb');
            $text = $ocr->run();

            // Parsing data KTP
            $parsedData = $this->extractKtpData($text);

            return response()->json([
                'image_path' => asset("storage/{$imagePath}"),
                'ocr_text' => $text,
                'parsed_data' => $parsedData
            ]);
        } catch (\Exception $e) {
            Log::error("Error saat scan KTP: " . $e->getMessage());
            return response()->json(['error' => 'Gagal memproses scan KTP.'], 500);
        }
    }

    private function extractKtpData($text)
    {
        $lines = explode("\n", strtolower($text));
        $data = [];

        foreach ($lines as $line) {
            $line = trim(preg_replace('/[^a-zA-Z0-9\/:\s-]/', '', $line));

            if (preg_match('/nik[:\s]+(\d{16})/i', $line, $matches)) {
                $data['nik'] = $matches[1];
            } elseif (preg_match('/nama[:\s]+(.+)/i', $line, $matches)) {
                $data['nama'] = ucwords($matches[1]);
            } elseif (preg_match('/tempat\/tgl lahir[:\s]+(.+)/i', $line, $matches)) {
                $data['tempat_tgl_lahir'] = ucwords($matches[1]);
            } elseif (preg_match('/jenis kelamin[:\s]+(laki-laki|perempuan)/i', $line, $matches)) {
                $data['jenis_kelamin'] = ucfirst($matches[1]);
            } elseif (preg_match('/alamat[:\s]+(.+)/i', $line, $matches)) {
                $data['alamat'] = ucwords($matches[1]);
            } elseif (preg_match('/rt\/rw[:\s]+(.+)/i', $line, $matches)) {
                $data['rt_rw'] = $matches[1];
            } elseif (preg_match('/kecamatan[:\s]+(.+)/i', $line, $matches)) {
                $data['kecamatan'] = ucwords($matches[1]);
            } elseif (preg_match('/agama[:\s]+(.+)/i', $line, $matches)) {
                $data['agama'] = ucwords($matches[1]);
            } elseif (preg_match('/status perkawinan[:\s]+(.+)/i', $line, $matches)) {
                $data['status_perkawinan'] = ucwords($matches[1]);
            }
        }

        return $data;
    }

    public function storePenumpang(Request $request)
    {
        try {

            Log::info('Request diterima:', $request->all());

            // Validasi input
            $request->validate([
                'nik' => 'required|numeric|digits:16',
                'nama' => 'required|string|max:255',
                'tempat_tgl_lahir' => 'required|string|max:255',
                'jenis_kelamin' => 'required|string|max:20',
                'alamat' => 'required|string|max:255',
                'rt_rw' => 'nullable|string|max:10',
                'kelurahan_desa' => 'required|string|max:255',
                'kecamatan' => 'required|string|max:255',
                'agama' => 'required|string|max:50',
                'status_perkawinan' => 'required|string|max:50',
                'pekerjaan' => 'required|string|max:255',
                'kewarganegaraan' => 'required|string|max:50',
                'berlaku_hingga' => 'required|string|max:20',
                'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            // Simpan file KTP jika ada
            if ($request->hasFile('foto_ktp')) {
                $filePath = $request->file('foto_ktp')->store('ktp_images', 'public');
            } else {
                $filePath = null;
            }

            // Simpan data ke database
            $penumpang = new Penumpang();
            $penumpang->nik = $request->nik;
            $penumpang->nama = $request->nama;
            $penumpang->tempat_tgl_lahir = $request->tempat_tgl_lahir;
            $penumpang->jenis_kelamin = $request->jenis_kelamin;
            $penumpang->alamat = $request->alamat;
            $penumpang->rt_rw = $request->rt_rw;
            $penumpang->kelurahan_desa = $request->kelurahan_desa;
            $penumpang->kecamatan = $request->kecamatan;
            $penumpang->agama = $request->agama;
            $penumpang->status_perkawinan = $request->status_perkawinan;
            $penumpang->pekerjaan = $request->pekerjaan;
            $penumpang->kewarganegaraan = $request->kewarganegaraan;
            $penumpang->berlaku_hingga = $request->berlaku_hingga;
            $penumpang->foto_ktp = $filePath;
            $penumpang->save();

            return response()->json(['message' => 'Data berhasil disimpan!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }


    public function indexPenumpang()
    {
        $penumpangs = Penumpang::all();
        return view('karyawan.penumpang', compact('penumpangs'));
    }

    public function updatePenumpang(Request $request, $id)
    {
        $penumpang = Penumpang::find($id);

        if (!$penumpang) {
            return redirect()->back()->with('error', 'Data penumpang tidak ditemukan');
        }

        $request->validate([
            'nik' => 'required|numeric|digits:16',
            'nama' => 'required|string|max:255',
            'tempat_tgl_lahir' => 'required|string|max:255',
            'jenis_kelamin' => 'required|string|max:20',
            'alamat' => 'required|string|max:255',
            'rt_rw' => 'nullable|string|max:10',
            'kelurahan_desa' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'agama' => 'required|string|max:50',
            'status_perkawinan' => 'required|string|max:50',
            'pekerjaan' => 'required|string|max:255',
            'kewarganegaraan' => 'required|string|max:50',
            'berlaku_hingga' => 'required|string|max:20',
            'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Simpan data yang diupdate
        $penumpang->update([
            'nik' => $request->nik,
            'nama' => $request->nama,
            'tempat_tgl_lahir' => $request->tempat_tgl_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'rt_rw' => $request->rt_rw,
            'kelurahan_desa' => $request->kelurahan_desa,
            'kecamatan' => $request->kecamatan,
            'agama' => $request->agama,
            'status_perkawinan' => $request->status_perkawinan,
            'pekerjaan' => $request->pekerjaan,
            'kewarganegaraan' => $request->kewarganegaraan,
            'berlaku_hingga' => $request->berlaku_hingga,
        ]);

        // Cek jika ada upload foto baru
        if ($request->hasFile('foto_ktp')) {
            $fotoPath = $request->file('foto_ktp')->store('penumpang', 'public');
            $penumpang->update(['foto_ktp' => $fotoPath]);
        }

        return redirect()->route('karyawan.kelolaPenumpang')->with('success', 'Data penumpang berhasil diperbarui');
    }






    //---------------------///
    public function index()
    {

        // Ambil semua tiket dari database dengan relasi penumpang dan bus
        $tikets = Tiket::with(['penumpangs', 'bus'])->get();
        $penumpangs = Penumpang::all(); // Tambahkan ini
        $buses = Bus::all(); // Ambil semua data bus


        // Kirim data ke view dashboard
        return view('karyawan.dashboard', compact('tikets', 'penumpangs', 'buses'));

        // return view('karyawan.dashboard');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'required|string|max:255',
            'alasan_kostum' => 'nullable|string|max:255',
            'tanggal_berangkat' => 'required|date',
            'tanggal_pulang' => 'nullable|date|after_or_equal:tanggal_berangkat',
        ]);

        $tiket = Tiket::findOrFail($id);
        $tiket->update([
            'alasan' => $request->alasan,
            'alasan_kostum' => $request->alasan_kostum,
            'tanggal_berangkat' => $request->tanggal_berangkat,
            'tanggal_pulang' => $request->tanggal_pulang,
        ]);

        return redirect()->back()->with('success', 'Tiket berhasil diperbarui!');
    }

    //=====================penumpang===============//






    public function kelolaPenumpang()
    {
        // Ambil daftar desa yang terdaftar
        $desaTerdaftar = Desa::pluck('nama')->toArray();

        // Ambil penumpang yang desanya terdaftar dan tidak terdaftar
        $penumpangTerdaftar = Penumpang::whereIn('kelurahan_desa', $desaTerdaftar)->get();
        $penumpangTidakTerdaftar = Penumpang::whereNotIn('kelurahan_desa', $desaTerdaftar)->get();

        return view('karyawan.kelola_penumpang', compact('penumpangTerdaftar', 'penumpangTidakTerdaftar'));
    }

    public function editPenumpang($id)
    {
        $penumpang = Penumpang::findOrFail($id);
        return view('karyawan.edit_penumpang', compact('penumpang'));
    }


    public function deletePenumpang($id)
    {
        $penumpang = Penumpang::findOrFail($id);
        if ($penumpang->foto) {
            Storage::disk('public')->delete($penumpang->foto);
        }
        $penumpang->delete();
        return redirect()->back()->with('success', 'Penumpang berhasil dihapus.');
    }






    // Menampilkan halaman lokasi beserta data lokasi
    public function showLocations()
    {
        $locations = LokasiBus::all();
        return view('driver.lokasi_bus', compact('locations'));
    }

    // Mendapatkan semua lokasi dalam format JSON
    public function getAllLocations()
    {
        $locations = LokasiBus::all();
        return response()->json($locations);
    }
}
