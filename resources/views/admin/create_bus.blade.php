@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-lg">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Tambah Bus</h1>

    <!-- Menampilkan error jika ada -->
    @if ($errors->any())
        <div class="bg-red-500 text-white p-4 rounded-lg mb-6">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.storeBus') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        <div>
            <label class="block font-semibold text-gray-700">Nomor Bus:</label>
            <input type="text" name="nomor_bus" required class="w-full border border-gray-300 rounded-lg p-3 focus:ring focus:ring-indigo-300">
        </div>

        <div>
            <label class="block font-semibold text-gray-700">Gambar Bus:</label>
            <input type="file" name="gambar" accept="image/*" class="w-full border border-gray-300 rounded-lg p-3 focus:ring focus:ring-indigo-300">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold text-gray-700">Kapasitas:</label>
                <input type="number" name="kapasitas" required min="1" class="w-full border border-gray-300 rounded-lg p-3 focus:ring focus:ring-indigo-300">
            </div>

            <div>
                <label class="block font-semibold text-gray-700">Jumlah:</label>
                <input type="number" name="jumlah" required min="1" class="w-full border border-gray-300 rounded-lg p-3 focus:ring focus:ring-indigo-300">
            </div>
        </div>

        <div class="flex justify-between mt-6">
            <a href="{{ route('admin.listBus') }}" class="bg-gray-500 text-white px-5 py-2 rounded-lg hover:bg-gray-700 transition">
                Batal
            </a>
        
            <button type="submit" class="btn btn-primary">Tambah Bus</button>

        </div>
    </form>
</div>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@endsection
