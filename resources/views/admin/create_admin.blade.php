@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
    <h1 class="text-2xl font-bold mb-4">Tambah Admin </h1>

    @if (session('error'))
        <div class="bg-red-500 text-white p-2 mb-4 rounded">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('admin.storeAdmin') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <div>
            <label class="block font-semibold">Nama:</label>
            <input type="text" name="name" required class="w-full border-gray-300 rounded-lg p-2">
        </div>

        <div>
            <label class="block font-semibold">Username:</label>
            <input type="text" name="username" required class="w-full border-gray-300 rounded-lg p-2">
        </div>

        <div>
            <label class="block font-semibold">Password:</label>
            <input type="password" name="password" required class="w-full border-gray-300 rounded-lg p-2">
        </div>

        <div>
            <label class="block font-semibold">Jabatan:</label>
            <input type="text" name="jabatan" class="w-full border-gray-300 rounded-lg p-2">
        </div>

        <div>
            <label class="block font-semibold">Nomor Telepon:</label>
            <input type="text" name="telepon" class="w-full border-gray-300 rounded-lg p-2">
        </div>

        <div>
            <label class="block font-semibold">Foto:</label>
            <input type="file" name="foto" accept="image/*" class="w-full border-gray-300 rounded-lg p-2">
        </div>

        <div class="flex justify-between">
            <a href="{{ route('admin.dashboard') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Kembali</a>
            <button type="submit" class="btn btn-primary">Tambah Admin</button>
        </div>
    </form>
</div>
@endsection
