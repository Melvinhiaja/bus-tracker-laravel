@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-lg">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Detail Admin</h1>

    <!-- Notifikasi Sukses -->
    @if (session('success'))
        <div class="bg-green-500 text-white p-3 rounded-lg mb-4 text-center">
            {{ session('success') }}
        </div>
    @endif

    <!-- Foto Admin -->
    <div class="flex justify-center mb-6">
        @if ($admin->foto)
            <img src="{{ asset('storage/' . $admin->foto) }}" alt="Foto Admin" 
                 class="w-32 h-32 object-cover rounded-lg shadow-md">
        @else
            <p class="text-gray-500 italic">Tidak ada foto.</p>
        @endif
    </div>

    <!-- Detail Informasi Admin -->
    <div class="bg-gray-100 p-4 rounded-lg">
        <ul class="space-y-3 text-gray-700">
            <li><strong>Nama:</strong> {{ $admin->name }}</li>
            <li><strong>Username:</strong> {{ $admin->username }}</li>
            <li><strong>Jabatan:</strong> {{ $admin->jabatan ?? '-' }}</li>
            <li><strong>Nomor Telepon:</strong> {{ $admin->telepon ?? '-' }}</li>
        </ul>
    </div>

    <!-- Tombol Aksi -->
    <div class="flex justify-between mt-6">
        <a href="{{ route('admin.editAdmin', $admin->id) }}" 
           class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-800 transition">
            Edit
        </a>

        <form action="{{ route('admin.deleteAdmin', $admin->id) }}" method="POST" 
              onsubmit="return confirm('Yakin ingin menghapus admin ini?')">
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
