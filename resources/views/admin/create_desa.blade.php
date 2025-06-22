@extends('layouts.app')

@section('title', 'Kelola Desa')

@section('head')
<link rel="stylesheet" href="https://api.tomtom.com/maps-sdk-for-web/cdn/6.x/6.17.0/maps/maps.css" />
<style>
    #map {
        height: 300px;
        width: 100%;
        margin-bottom: 15px;
    }
</style>
@endsection

@section('content')
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-body">
            <h4 class="mb-3">Kelola Desa</h4>

            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('admin.desa.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Nama Desa:</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Cari Lokasi Desa:</label>
                    <div class="input-group">
                        <input type="text" id="searchDesa" class="form-control" placeholder="Cari Desa">
                        <button type="button" class="btn btn-primary" onclick="searchLocation()">Cari</button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Pilih Lokasi Desa di Map:</label>
                    <div id="map"></div>
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                </div>

                <button type="submit" class="btn btn-success">Tambah Desa</button>
            </form>

            <hr>

            <h5>Daftar Desa</h5>
            <table class="table table-bordered mt-3">
                <thead class="table-dark">
                    <tr>
                        <th>No.</th>
                        <th>Nama Desa</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($desas as $desa)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $desa->nama }}</td>
                        <td>
                            <button class="btn btn-warning btn-edit"
                                data-id="{{ $desa->id }}"
                                data-nama="{{ $desa->nama }}">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <form action="{{ route('admin.desa.destroy', $desa->id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Desa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit-id" name="id">
                    <div class="mb-3">
                        <label class="form-label">Nama Desa:</label>
                        <input type="text" name="nama" id="edit-nama" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://api.tomtom.com/maps-sdk-for-web/cdn/6.x/6.17.0/maps/maps-web.min.js"></script>
<script src="https://api.tomtom.com/maps-sdk-for-web/cdn/6.x/6.17.0/services/services-web.min.js"></script>
<script>
    const key = '{{ config("services.tomtom.key") }}';

    const map = tt.map({
        key: key,
        container: 'map',
        center: [107.6098, -6.9147],
        zoom: 10
    });

    let marker = null;
    map.on('click', function(e) {
        const coord = { lat: e.lngLat.lat, lng: e.lngLat.lng };
        document.getElementById('latitude').value = coord.lat;
        document.getElementById('longitude').value = coord.lng;

        if (marker) marker.remove();
        marker = new tt.Marker().setLngLat([coord.lng, coord.lat]).addTo(map);
    });

    function searchLocation() {
        const query = document.getElementById('searchDesa').value;
        if (!query) {
            alert('Masukkan lokasi yang ingin dicari!');
            return;
        }

        tt.services.fuzzySearch({
            key: key,
            query: query,
            limit: 1
        }).then(result => {
            if (result.results.length) {
                const pos = result.results[0].position;
                map.flyTo({
                    center: [pos.lng, pos.lat],
                    zoom: 14
                });
            } else {
                alert('Lokasi tidak ditemukan!');
            }
        }).catch(() => {
            alert('Gagal mencari lokasi.');
        });
    }

    document.querySelectorAll(".btn-edit").forEach(button => {
        button.addEventListener("click", function() {
            const id = this.getAttribute("data-id");
            const nama = this.getAttribute("data-nama");

            document.getElementById("edit-id").value = id;
            document.getElementById("edit-nama").value = nama;
            document.getElementById("editForm").action = `/admin/desa/${id}`;

            new bootstrap.Modal(document.getElementById("editModal")).show();
        });
    });
</script>
@endpush