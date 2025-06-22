@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-3xl font-bold mb-6 text-indigo-700">Dashboard Admin</h1>

        <!-- Notifikasi -->
        @if (session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Tombol Aksi -->
        <div class="flex flex-wrap gap-2 mb-6">
            <a href="{{ route('admin.createAdmin') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-800">👤Tambah Admin</a>
            <a href="{{ route('admin.createKaryawan') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-800">👤Tambah Karyawan</a>
            <a href="{{ route('admin.createDriver') }}" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-800">👤Tambah Driver</a>
            <a href="{{ route('admin.createBus') }}" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-800">👤Tambah Bus</a>
        </div>

        <!-- TABEL ADMIN -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-3 text-gray-800">Daftar Admin</h2>
            <div class="overflow-x-auto bg-gray-50 p-4 rounded shadow-sm">
                <table class="w-full text-left border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700">
                            <th class="border p-2">Nama</th>
                            <th class="border p-2">Username</th>
                            <th class="border p-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (\App\Models\User::where('role', 'admin')->get() as $admin)
                            <tr>
                                <td class="border p-2">{{ $admin->name }}</td>
                                <td class="border p-2">{{ $admin->username }}</td>
                                <td class="border p-2 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('admin.viewAdmin', $admin->id) }}" class="text-blue-600 hover:underline">View</a>
                                        <a href="{{ route('admin.editAdmin', $admin->id) }}" class="text-yellow-600 hover:underline">Edit</a>
                                        <form action="{{ route('admin.deleteAdmin', $admin->id) }}" method="POST" onsubmit="return confirmDelete(event, '{{ $admin->name }}')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TABEL KARYAWAN -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-3 text-gray-800">Daftar Karyawan</h2>
            <div class="overflow-x-auto bg-gray-50 p-4 rounded shadow-sm">
                <table class="w-full text-left border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700">
                            <th class="border p-2">Nama</th>
                            <th class="border p-2">Username</th>
                            <th class="border p-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (\App\Models\User::where('role', 'karyawan')->get() as $karyawan)
                            <tr>
                                <td class="border p-2">{{ $karyawan->name }}</td>
                                <td class="border p-2">{{ $karyawan->username }}</td>
                                <td class="border p-2 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('admin.viewKaryawan', $karyawan->id) }}" class="text-blue-600 hover:underline">View</a>
                                        <a href="{{ route('admin.editKaryawan', $karyawan->id) }}" class="text-yellow-600 hover:underline">Edit</a>
                                        <form action="{{ route('admin.deleteKaryawan', $karyawan->id) }}" method="POST" onsubmit="return confirmDelete(event, '{{ $karyawan->name }}')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
<!-- TABEL DRIVER -->
<div class="mb-8">
    <h2 class="text-xl font-semibold mb-3 text-gray-800">Daftar Driver</h2>
    <div class="overflow-x-auto bg-gray-50 p-4 rounded shadow-sm">
        <table class="w-full text-left border border-gray-300">
            <thead>
                <tr class="bg-gray-200 text-gray-700">
                    <th class="border p-2">Nama</th>
                    <th class="border p-2">Username</th>
                    <th class="border p-2 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach (\App\Models\User::where('role', 'driver')->get() as $driver)
                    <tr>
                        <td class="border p-2">{{ $driver->name }}</td>
                        <td class="border p-2">{{ $driver->username }}</td>
                        <td class="border p-2 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('admin.viewDriver', $driver->id) }}" class="text-blue-600 hover:underline">View</a>
                                <a href="{{ route('admin.editDriver', $driver->id) }}" class="text-yellow-600 hover:underline">Edit</a>
                                <form action="{{ route('admin.deleteDriver', $driver->id) }}" method="POST" onsubmit="return confirmDelete(event, '{{ $driver->name }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

        <!-- TABEL BUS -->
        <div>
            <h2 class="text-xl font-semibold mb-3 text-gray-800">Daftar Bus</h2>
            <div class="overflow-x-auto bg-gray-50 p-4 rounded shadow-sm">
                <table class="w-full text-left border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700">
                            <th class="border p-2">Nomor Bus</th>
                            <th class="border p-2">Gambar</th>
                            <th class="border p-2">Kapasitas</th>
                            <th class="border p-2">Jumlah</th>
                            <th class="border p-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (\App\Models\Bus::all() as $bus)
                            <tr>
                                <td class="border p-2">{{ $bus->nomor_bus }}</td>
                                <td class="border p-2">
                                    @if ($bus->gambar)
                                        <img src="{{ asset('storage/' . $bus->gambar) }}" alt="Gambar Bus" class="h-12 rounded object-cover">
                                    @else
                                        <span class="text-gray-400">Tidak ada gambar</span>
                                    @endif
                                </td>
                                <td class="border p-2">{{ $bus->kapasitas }}</td>
                                <td class="border p-2">{{ $bus->jumlah }}</td>
                                <td class="border p-2 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('admin.listBus', $bus->id) }}" class="text-blue-600 hover:underline">View</a>
                                        <a href="{{ route('admin.editBus', $bus->id) }}" class="text-yellow-600 hover:underline">Edit</a>
                                        <form action="{{ route('admin.deleteBus', $bus->id) }}" method="POST" onsubmit="return confirmDelete(event, 'Bus {{ $bus->nomor_bus }}')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(event, name) {
        event.preventDefault();
        Swal.fire({
            title: `Hapus ${name}?`,
            text: "Data yang dihapus tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                event.target.submit();
            }
        });
    }
</script>
@endsection
