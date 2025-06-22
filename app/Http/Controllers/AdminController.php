<?php

namespace App\Http\Controllers;

use App\Models\Alasan;
use App\Models\Bus;
use App\Models\Desa;
use App\Models\Perjalanan;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{

    // Menampilkan daftar perjalanan
    public function indexPerjalanan()
    {
        $perjalanans = Perjalanan::all();
        return view('admin.create_perjalanan', compact('perjalanans'));
    }

    // Menyimpan perjalanan baru
    public function storePerjalanan(Request $request)
    {
        $request->validate([
            'asal' => 'required|string|max:255',
            'tujuan' => 'required|string|max:255',
            'jenis' => 'required|in:sekali jalan,pulang pergi',
            'lokasi_awal' => 'nullable|string',
            'lokasi_tujuan' => 'nullable|string'
        ]);

        Perjalanan::create([
            'asal' => $request->asal,
            'tujuan' => $request->tujuan,
            'jenis' => $request->jenis,
            'lokasi_awal' => $request->lokasi_awal,
            'lokasi_tujuan' => $request->lokasi_tujuan,
        ]);

        return redirect()->route('admin.perjalanan.index')->with('success', 'Perjalanan berhasil ditambahkan!');
    }

    // Mengedit perjalanan (via modal)
    public function editPerjalanan(Perjalanan $perjalanan)
    {
        return response()->json($perjalanan); // Untuk AJAX modal edit
    }

    // Update perjalanan
    public function updatePerjalanan(Request $request, Perjalanan $perjalanan)
    {
        $request->validate([
            'asal' => 'required|string|max:255',
            'tujuan' => 'required|string|max:255',
            'jenis' => 'required|in:sekali jalan,pulang pergi',
            'lokasi_awal' => 'nullable|string',
            'lokasi_tujuan' => 'nullable|string'
        ]);

        $perjalanan->update([
            'asal' => $request->asal,
            'tujuan' => $request->tujuan,
            'jenis' => $request->jenis,
            'lokasi_awal' => $request->lokasi_awal,
            'lokasi_tujuan' => $request->lokasi_tujuan,
        ]);

        return redirect()->route('admin.perjalanan.index')->with('success', 'Perjalanan berhasil diperbarui!');
    }

    // Hapus perjalanan
    public function destroyPerjalanan(Perjalanan $perjalanan)
    {
        $perjalanan->delete();
        return redirect()->route('admin.perjalanan.index')->with('success', 'Perjalanan berhasil dihapus!');
    }



    // Menampilkan daftar desa

    public function desaIndex()
    {
        $desas = Desa::all();
        return view('admin.create_desa', compact('desas'));
    }

    public function desaStore(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|unique:desas,nama',
        ]);

        Desa::create([
            'nama' => $request->nama,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return redirect()->route('admin.desa.index')->with('success', 'Desa berhasil ditambahkan.');
    }

    public function desaEdit($id)
    {
        return response()->json(Desa::find($id));
    }

    public function desaUpdate(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|unique:desas,nama,' . $id,
        ]);

        $desa = Desa::findOrFail($id);
        $desa->update(['nama' => $request->nama]);

        return redirect()->route('admin.desa.index')->with('success', 'Desa berhasil diperbarui.');
    }

    public function desaDestroy($id)
    {
        Desa::findOrFail($id)->delete();
        return redirect()->route('admin.desa.index')->with('success', 'Desa berhasil dihapus.');
    }



    // Menampilkan daftar alasan
    public function indexAlasan()
    {
        $alasans = Alasan::all();
        return view('admin.create_alasan', compact('alasans'));
    }

    // Menyimpan alasan baru
    public function storeAlasan(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255'
        ]);

        Alasan::create([
            'nama' => $request->nama
        ]);

        return redirect()->route('admin.alasan.index')->with('success', 'Alasan berhasil ditambahkan.');
    }

    // Mengupdate alasan
    public function updateAlasan(Request $request, $id)
    {
        $alasan = Alasan::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255'
        ]);

        $alasan->update([
            'nama' => $request->nama
        ]);

        return redirect()->route('admin.alasan.index')->with('success', 'Alasan berhasil diperbarui!');
    }


    // Menghapus alasan
    public function destroyAlasan(Alasan $alasan)
    {
        $alasan->delete();
        return redirect()->route('admin.alasan.index')->with('success', 'Alasan berhasil dihapus.');
    }



    // Tampilkan daftar bus
    public function listBus()
    {
        $buses = Bus::all();
        return view('admin.list_bus', compact('buses'));
    }

    // Form tambah bus
    public function createBus()
    {
        return view('admin.create_bus');
    }

    // Simpan bus baru
    public function storeBus(Request $request)
    {
        $request->validate([
            'nomor_bus' => 'required|string|unique:buses',
            'gambar' => 'nullable|image|max:2048',
            'kapasitas' => 'required|integer|min:1',
            'jumlah' => 'required|integer|min:1',
        ]);

        $gambarPath = $request->file('gambar') ? $request->file('gambar')->store('gambar_bus', 'public') : null;

        Bus::create([
            'nomor_bus' => $request->nomor_bus,
            'gambar' => $gambarPath,
            'kapasitas' => $request->kapasitas,
            'jumlah' => $request->jumlah,
        ]);

        return redirect()->route('admin.listBus')->with('success', 'Bus berhasil ditambahkan.');
    }

    // Form edit bus
    public function editBus($id)
    {
        $bus = Bus::findOrFail($id);
        return view('admin.edit_bus', compact('bus'));
    }

    // Update bus
    public function updateBus(Request $request, $id)
    {
        $bus = Bus::findOrFail($id);

        $request->validate([
            'nomor_bus' => 'required|string|unique:buses,nomor_bus,' . $id,
            'gambar' => 'nullable|image|max:2048',
            'kapasitas' => 'required|integer|min:1',
            'jumlah' => 'required|integer|min:1',
        ]);

        if ($request->hasFile('gambar')) {
            if ($bus->gambar) {
                Storage::disk('public')->delete($bus->gambar);
            }
            $bus->gambar = $request->file('gambar')->store('gambar_bus', 'public');
        }

        $bus->update([
            'nomor_bus' => $request->nomor_bus,
            'kapasitas' => $request->kapasitas,
            'jumlah' => $request->jumlah,
            'gambar' => $bus->gambar,
        ]);

        return redirect()->route('admin.listBus')->with('success', 'Bus berhasil diperbarui.');
    }

    // Hapus bus
    public function deleteBus($id)
    {
        $bus = Bus::findOrFail($id);
        if ($bus->gambar) {
            Storage::disk('public')->delete($bus->gambar);
        }
        $bus->delete();
        return redirect()->route('admin.listBus')->with('success', 'Bus berhasil dihapus.');
    }

    // Middleware untuk membatasi akses hanya untuk admin
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'admin') {
                return redirect('/')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
            }
            return $next($request);
        });
    }

    // Dashboard Admin
    public function index()
    {
        $karyawans = User::where('role', 'karyawan')->get();
        $admins = User::where('role', 'admin')->get();
        $drivers = User::where('role', 'driver')->get(); // tambahkan ini

        return view('admin.dashboard', compact('karyawans', 'admins'));
    }

    // ===================== KARYAWAN ===================== //
    public function createKaryawan()
    {
        return view('admin.create_karyawan');
    }

    public function storeKaryawan(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'username' => 'required|string|unique:users',
                'password' => 'required|string|min:6',
                'jabatan' => 'required|string',
                'telepon' => 'required|string|max:15',
                'foto' => 'nullable|image|max:2048',
            ]);

            $fotoPath = $request->file('foto') ? $request->file('foto')->store('foto_karyawan', 'public') : null;

            User::create([
                'name' => $request->name,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => 'karyawan',
                'jabatan' => $request->jabatan,
                'telepon' => $request->telepon,
                'foto' => $fotoPath,
            ]);

            return redirect()->route('admin.dashboard')->with('success', 'Karyawan berhasil ditambahkan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function viewKaryawan($id)
    {
        $karyawan = User::findOrFail($id);
        return view('admin.view_karyawan', compact('karyawan'));
    }

    public function editKaryawan($id)
    {
        $karyawan = User::findOrFail($id);
        return view('admin.edit_karyawan', compact('karyawan'));
    }

    public function updateKaryawan(Request $request, $id)
    {
        $karyawan = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $id . ',id',
            'password' => 'nullable|string|min:6',
            'jabatan' => 'required|string',
            'telepon' => 'required|string|max:15',
            'foto' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if (!empty($karyawan->foto) && Storage::disk('public')->exists($karyawan->foto)) {
                Storage::disk('public')->delete($karyawan->foto);
            }
            $karyawan->foto = $request->file('foto')->store('foto_karyawan', 'public');
        }

        $karyawan->update([
            'name' => $request->name,
            'username' => $request->username,
            'jabatan' => $request->jabatan,
            'telepon' => $request->telepon,
            'password' => $request->password ? Hash::make($request->password) : $karyawan->password,
            'foto' => $karyawan->foto,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Karyawan berhasil diperbarui.');
    }

    public function deleteKaryawan($id)
    {
        $karyawan = User::findOrFail($id);
        if (!empty($karyawan->foto) && Storage::disk('public')->exists($karyawan->foto)) {
            Storage::disk('public')->delete($karyawan->foto);
        }
        $karyawan->delete();
        return redirect()->route('admin.dashboard')->with('success', 'Karyawan berhasil dihapus.');
    }

    // ===================== ADMIN ===================== //
    public function createAdmin()
    {
        return view('admin.create_admin');
    }

    public function storeAdmin(Request $request)
    {
        try {
            // Tambahkan log untuk mengecek request yang diterima

            $request->validate([
                'name' => 'required|string|max:255',
                'username' => 'required|string|unique:users',
                'password' => 'required|string|min:6',
                'jabatan' => 'required|string',
                'telepon' => 'required|string|max:15',
                'foto' => 'nullable|image|max:2048',
            ]);

            $fotoPath = $request->file('foto') ? $request->file('foto')->store('foto_admin', 'public') : null;

            User::create([
                'name' => $request->name,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => 'admin',
                'jabatan' => $request->jabatan,
                'telepon' => $request->telepon,
                'foto' => $fotoPath,
            ]);

            return redirect()->route('admin.dashboard')->with('success', 'Admin berhasil ditambahkan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function viewAdmin($id)
    {
        $admin = User::findOrFail($id);
        return view('admin.view_admin', compact('admin'));
    }

    public function editAdmin($id)
    {
        $admin = User::findOrFail($id);
        return view('admin.edit_admin', compact('admin'));
    }

    public function updateAdmin(Request $request, $id)
    {
        try {
            $admin = User::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255',
                'username' => 'required|string|unique:users,username,' . $id . ',id',
                'password' => 'nullable|string|min:6',
                'jabatan' => 'required|string',
                'telepon' => 'required|string|max:15',
                'foto' => 'nullable|image|max:2048',
            ]);

            if ($request->hasFile('foto')) {
                if (!empty($admin->foto) && Storage::disk('public')->exists($admin->foto)) {
                    Storage::disk('public')->delete($admin->foto);
                }
                $admin->foto = $request->file('foto')->store('foto_admin', 'public');
            }

            $admin->update([
                'name' => $request->name,
                'username' => $request->username,
                'jabatan' => $request->jabatan,
                'telepon' => $request->telepon,
                'password' => $request->password ? Hash::make($request->password) : $admin->password,
                'foto' => $admin->foto,
            ]);

            return redirect()->route('admin.dashboard')->with('success', 'Admin berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function deleteAdmin($id)
    {
        $admin = User::findOrFail($id);
        if (!empty($admin->foto) && Storage::disk('public')->exists($admin->foto)) {
            Storage::disk('public')->delete($admin->foto);
        }
        $admin->delete();
        return redirect()->route('admin.dashboard')->with('success', 'Admin berhasil dihapus.');
    }





    // Form Tambah Driver
    public function createDriver()
    {
        return view('admin.create_driver');
    }

    // Simpan Driver Baru
    public function storeDriver(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'username' => 'required|string|unique:users',
                'password' => 'required|string|min:6',
                'jabatan' => 'required|string',
                'telepon' => 'required|string|max:15',
                'foto' => 'nullable|image|max:2048',
            ]);

            $fotoPath = $request->file('foto') ? $request->file('foto')->store('foto_driver', 'public') : null;

            User::create([
                'name' => $request->name,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => 'driver',
                'jabatan' => $request->jabatan,
                'telepon' => $request->telepon,
                'foto' => $fotoPath,
            ]);

            return redirect()->route('admin.dashboard')->with('success', 'Driver berhasil ditambahkan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    public function editDriver($id)
    {
        $driver = User::findOrFail($id);
        return view('admin.edit_driver', compact('driver'));
    }

    public function updateDriver(Request $request, $id)
    {
        $driver = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $id,
            'password' => 'nullable|string|min:6',
            'jabatan' => 'required|string',
            'telepon' => 'required|string|max:15',
            'foto' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if (!empty($driver->foto) && Storage::disk('public')->exists($driver->foto)) {
                Storage::disk('public')->delete($driver->foto);
            }
            $driver->foto = $request->file('foto')->store('foto_driver', 'public');
        }

        $driver->update([
            'name' => $request->name,
            'username' => $request->username,
            'jabatan' => $request->jabatan,
            'telepon' => $request->telepon,
            'password' => $request->password ? Hash::make($request->password) : $driver->password,
            'foto' => $driver->foto,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Data driver berhasil diperbarui.');
    }
    public function viewDriver($id)
    {
        $driver = \App\Models\User::findOrFail($id);
        return view('admin.view_driver', compact('driver'));
    }
}
