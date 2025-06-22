@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold text-indigo-700 mb-4">Detail Driver</h2>

        <div class="mb-4">
            <strong>Nama:</strong> {{ $driver->name }}
        </div>

        <div class="mb-4">
            <strong>Username:</strong> {{ $driver->username }}
        </div>

        <div class="mb-4">
            <strong>Jabatan:</strong> {{ $driver->jabatan }}
        </div>

        <div class="mb-4">
            <strong>Nomor Telepon:</strong> {{ $driver->telepon }}
        </div>

        <div class="mb-4">
            <strong>Foto:</strong><br>
            @if ($driver->foto)
                <img src="{{ asset('storage/' . $driver->foto) }}" alt="Foto Driver" class="w-32 rounded shadow mt-2">
            @else
                <span class="text-gray-500">Belum ada foto</span>
            @endif
        </div>

        <a href="{{ route('admin.dashboard') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-800">Kembali</a>
    </div>
</div>
@endsection
