@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-lg">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Edit Admin</h1>

    <form action="{{ route('admin.updateAdmin', $admin->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <!-- Nama -->
        <div>
            <label class="block font-semibold text-gray-700">Nama:</label>
            <input type="text" name="name" value="{{ $admin->name }}" required 
                   class="w-full border-gray-300 rounded-lg p-2 focus:ring focus:ring-blue-300">
        </div>

        <!-- Username -->
        <div>
            <label class="block font-semibold text-gray-700">Username:</label>
            <input type="text" name="username" value="{{ $admin->username }}" required 
                   class="w-full border-gray-300 rounded-lg p-2 focus:ring focus:ring-blue-300">
        </div>

        <!-- Password -->
        <div>
            <label class="block font-semibold text-gray-700">Password (kosongkan jika tidak ingin diubah):</label>
            <input type="password" name="password" 
                   class="w-full border-gray-300 rounded-lg p-2 focus:ring focus:ring-blue-300">
        </div>

        <!-- Jabatan -->
        <div>
            <label class="block font-semibold text-gray-700">Jabatan:</label>
            <input type="text" name="jabatan" value="{{ $admin->jabatan }}" required 
                   class="w-full border-gray-300 rounded-lg p-2 focus:ring focus:ring-blue-300">
        </div>

        <!-- Nomor Telepon -->
        <div>
            <label class="block font-semibold text-gray-700">Telepon:</label>
            <input type="text" name="telepon" value="{{ $admin->telepon }}" required 
                   class="w-full border-gray-300 rounded-lg p-2 focus:ring focus:ring-blue-300">
        </div>

        <!-- Foto -->
        <div>
            <label class="block font-semibold text-gray-700">Foto:</label>
            @if ($admin->foto)
                <img src="{{ asset('storage/' . $admin->foto) }}" alt="Foto Admin" 
                     class="w-24 h-24 object-cover rounded-lg shadow-md mb-2">
            @endif
            <input type="file" name="foto" accept="image/*" 
                   class="w-full border-gray-300 rounded-lg p-2">
        </div>

        <!-- Tombol Aksi -->
        <div class="flex justify-between mt-6">
            <a href="{{ route('admin.dashboard') }}" 
               class="bg-gray-500 text-white px-5 py-2 rounded-lg hover:bg-gray-700 transition">
                Kembali
            </a>

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>

        </div>
    </form>
</div>
@endsection
