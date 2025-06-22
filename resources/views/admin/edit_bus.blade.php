@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Edit Bus</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.updateBus', $bus->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nomor_bus" class="form-label">Nomor Bus</label>
            <input type="text" class="form-control" id="nomor_bus" name="nomor_bus" value="{{ $bus->nomor_bus }}" required>
        </div>

        <div class="mb-3">
            <label for="gambar" class="form-label">Gambar Bus</label>
            <input type="file" class="form-control" id="gambar" name="gambar">
            @if($bus->gambar)
                <div class="mt-2">
                    <img src="{{ asset('storage/' . $bus->gambar) }}" alt="Bus Image" width="100">
                </div>
            @endif
        </div>

        <div class="mb-3">
            <label for="kapasitas" class="form-label">Kapasitas</label>
            <input type="number" class="form-control" id="kapasitas" name="kapasitas" value="{{ $bus->kapasitas }}" required min="1">
        </div>

        <div class="mb-3">
            <label for="jumlah" class="form-label">Jumlah</label>
            <input type="number" class="form-control" id="jumlah" name="jumlah" value="{{ $bus->jumlah }}" required min="1">
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('admin.listBus') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
