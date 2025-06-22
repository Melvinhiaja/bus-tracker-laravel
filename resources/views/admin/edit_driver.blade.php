@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold text-indigo-700 mb-4">Edit Data Driver</h2>

        <form action="{{ url('/admin/driver/edit/' . $driver->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block font-semibold">Nama:</label>
                <input type="text" name="name" value="{{ old('name', $driver->name) }}" required class="w-full border-gray-300 rounded-lg p-2">
            </div>

            <div>
                <label class="block font-semibold">Username:</label>
                <input type="text" name="username" value="{{ old('username', $driver->username) }}" required class="w-full border-gray-300 rounded-lg p-2">
            </div>

            <div>
                <label class="block font-semibold">Password (Kosongkan jika tidak diganti):</label>
                <input type="password" name="password" class="w-full border-gray-300 rounded-lg p-2">
            </div>

            <div>
                <label class="block font-semibold">Jabatan:</label>
                <input type="text" name="jabatan" value="{{ old('jabatan', $driver->jabatan) }}" class="w-full border-gray-300 rounded-lg p-2">
            </div>

            <div>
                <label class="block font-semibold">Nomor Telepon:</label>
                <input type="text" name="telepon" value="{{ old('telepon', $driver->telepon) }}" class="w-full border-gray-300 rounded-lg p-2">
            </div>

            <div>
                <label class="block font-semibold">Foto (Opsional):</label>
                @if ($driver->foto)
                    <img src="{{ asset('storage/' . $driver->foto) }}" alt="Foto Driver" class="h-16 rounded shadow mb-2">
                @endif
                <input type="file" name="foto" accept="image/*" class="w-full border-gray-300 rounded-lg p-2">
            </div>

            <div class="flex justify-between">
                <a href="{{ route('admin.dashboard') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-800">Batal</a>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-800">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
