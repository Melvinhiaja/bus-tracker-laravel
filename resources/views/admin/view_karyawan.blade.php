@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-lg">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Detail Karyawan</h1>

    <!-- Notifikasi Sukses -->
    @if (session('success'))
        <div class="bg-green-500 text-white p-3 rounded-lg mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Tampilkan Foto Karyawan -->
    <div class="flex justify-center mb-6">
        @if ($karyawan->foto)
            <img src="{{ asset('storage/' . $karyawan->foto) }}" alt="Foto Karyawan" 
                 class="w-32 h-32 object-cover rounded-lg shadow-md">
        @else
            <p class="text-gray-500 italic">Tidak ada foto.</p>
        @endif
    </div>

    <!-- Detail Informasi Karyawan -->
    <div class="bg-gray-100 p-4 rounded-lg">
        <ul class="space-y-3 text-gray-700">
            <li><strong>Nama:</strong> {{ $karyawan->name }}</li>
            <li><strong>Username:</strong> {{ $karyawan->username }}</li>
            <li><strong>Jabatan:</strong> {{ $karyawan->jabatan ?? '-' }}</li>
            <li><strong>Nomor Telepon:</strong> {{ $karyawan->telepon ?? '-' }}</li>
            <li><strong>Status:</strong> 
                <span class="font-semibold text-{{ $karyawan->is_active ? 'green' : 'red' }}-600">
                    {{ $karyawan->is_active ? 'Active' : 'Inactive' }}
                </span>
            </li>
        </ul>
    </div>

    <!-- Tombol Aksi -->
    <div class="flex justify-between mt-6">
        <a href="{{ route('admin.editKaryawan', $karyawan->id) }}" 
           class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-800 transition">
            Edit
        </a>

        <form action="{{ route('admin.deleteKaryawan', $karyawan->id) }}" method="POST" 
              onsubmit="return confirm('Yakin ingin menghapus akun ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 text-white px-5 py-2 rounded-lg hover:bg-red-800 transition">
                Hapus
            </button>
        </form>

        <a href="{{ route('admin.dashboard') }}" 
           class="bg-gray-500 text-white px-5 py-2 rounded-lg hover:bg-gray-700 transition">
            Kembali
        </a>
    </div>
</div>
@endsection
