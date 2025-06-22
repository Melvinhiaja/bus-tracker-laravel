@extends('layouts.app')

@section('title', 'Kelola Perjalanan')

@section('head')
    <link rel="stylesheet" href="https://api.tomtom.com/maps-sdk-for-web/cdn/6.x/6.17.0/maps/maps.css" />
    <style>
        #mapAsal, #mapTujuan, #mapEditAsal, #mapEditTujuan {
            height: 300px;
            width: 100%;
            margin-bottom: 15px;
        }
    </style>
@endsection

@section('content')
<div class="container mt-5">
    <h4>Kelola Perjalanan</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.perjalanan.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Asal</label>
            <input type="text" name="asal" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Pilih Koordinat Asal di Map:</label>
            <div class="mb-3">
    <label>Cari Lokasi Asal:</label>
    <div class="input-group">
        <input type="text" id="searchAsal" class="form-control" placeholder="Klik Disini">
        <button type="button" class="btn btn-primary" onclick="searchLocation(mapAsal, 'searchAsal')">Cari</button>
    </div>
</div>
            <div id="mapAsal"></div>
            <input type="hidden" name="lokasi_awal" id="lokasi_awal">
        </div>

        <div class="mb-3">
            <label class="form-label">Tujuan</label>
            <input type="text" name="tujuan" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Pilih Koordinat Tujuan di Map:</label>
            <div class="mb-3">
    <label>Cari Lokasi Tujuan:</label>
    <div class="input-group">
        <input type="text" id="searchTujuan" class="form-control" placeholder="Klik disini">
        <button type="button" class="btn btn-primary" onclick="searchLocation(mapTujuan, 'searchTujuan')">Cari</button>
    </div>
</div>
            <div id="mapTujuan"></div>
            <input type="hidden" name="lokasi_tujuan" id="lokasi_tujuan">
        </div>

        <div class="mb-3">
            <label class="form-label">Jenis Perjalanan</label>
            <select name="jenis" class="form-select" required>
                <option value="sekali jalan">Sekali Jalan</option>
                <option value="pulang pergi">Pulang Pergi</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Tambah</button>
    </form>

    <hr>

    <h4>Daftar Perjalanan</h4>
    <table class="table table-bordered text-center">
        <thead class="table-dark">
            <tr>
                <th>No.</th>
                <th>Nama Perjalanan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($perjalanans as $perjalanan)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $perjalanan->nama_perjalanan }}</td>
                <td>
                    <button class="btn btn-warning btn-edit"
                        data-id="{{ $perjalanan->id }}"
                        data-asal="{{ $perjalanan->asal }}"
                        data-tujuan="{{ $perjalanan->tujuan }}"
                        data-jenis="{{ $perjalanan->jenis }}">
                        Edit
                    </button>

                    <form action="{{ route('admin.perjalanan.destroy', $perjalanan->id) }}" method="POST" class="d-inline delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-delete">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Perjalanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit-id" name="id">
                    <div class="mb-3">
                        <label class="form-label">Asal</label>
                        <input type="text" id="edit-asal" name="asal" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tujuan</label>
                        <input type="text" id="edit-tujuan" name="tujuan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Perjalanan</label>
                        <select id="edit-jenis" name="jenis" class="form-select" required>
                            <option value="sekali jalan">Sekali Jalan</option>
                            <option value="pulang pergi">Pulang Pergi</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Koordinat Asal</label>
                        <div id="mapEditAsal"></div>
                        <input type="hidden" name="lokasi_awal" id="edit-lokasi-awal">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Koordinat Tujuan</label>
                        <div id="mapEditTujuan"></div>
                        <input type="hidden" name="lokasi_tujuan" id="edit-lokasi-tujuan">
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
    function searchLocation(mapInstance, inputId) {
    const query = document.getElementById(inputId).value;

    if (!query) {
        alert('Masukkan lokasi yang ingin dicari!');
        return;
    }

    tt.services.fuzzySearch({
        key: '{{ config("services.tomtom.key") }}',
        query: query,
        limit: 1
    }).then(result => {
        if (result.results.length) {
            const position = result.results[0].position;
            mapInstance.flyTo({ center: [position.lng, position.lat], zoom: 16 });
        } else {
            alert('Lokasi tidak ditemukan!');
        }
    }).catch(() => {
        alert('Gagal mencari lokasi.');
    });
}

    const key = '{{ config("services.tomtom.key") }}';

    let markerAsal, markerTujuan;
    const mapAsal = tt.map({ key, container: 'mapAsal', center: [107.6098, -6.9147], zoom: 16 });
    const mapTujuan = tt.map({ key, container: 'mapTujuan', center: [107.6098, -6.9147], zoom: 16 });

    mapAsal.on('click', e => {
        const coord = { lat: e.lngLat.lat, lng: e.lngLat.lng };
        document.getElementById('lokasi_awal').value = JSON.stringify(coord);
        if (markerAsal) markerAsal.remove();
        markerAsal = new tt.Marker().setLngLat([coord.lng, coord.lat]).addTo(mapAsal);
    });

    mapTujuan.on('click', e => {
        const coord = { lat: e.lngLat.lat, lng: e.lngLat.lng };
        document.getElementById('lokasi_tujuan').value = JSON.stringify(coord);
        if (markerTujuan) markerTujuan.remove();
        markerTujuan = new tt.Marker().setLngLat([coord.lng, coord.lat]).addTo(mapTujuan);
    });

    let mapEditAsal, mapEditTujuan, markerEditAsal, markerEditTujuan;
    document.querySelectorAll(".btn-edit").forEach(button => {
        button.addEventListener("click", () => {
            const id = button.getAttribute("data-id");
            fetch(`/admin/perjalanan/${id}/edit`).then(res => res.json()).then(data => {
                const lokasiAwal = JSON.parse(data.lokasi_awal);
                const lokasiTujuan = JSON.parse(data.lokasi_tujuan);

                document.getElementById("edit-id").value = id;
                document.getElementById("edit-asal").value = data.asal;
                document.getElementById("edit-tujuan").value = data.tujuan;
                document.getElementById("edit-jenis").value = data.jenis;
                document.getElementById("editForm").action = `/admin/perjalanan/${id}`;
                document.getElementById("edit-lokasi-awal").value = data.lokasi_awal;
                document.getElementById("edit-lokasi-tujuan").value = data.lokasi_tujuan;

                mapEditAsal = tt.map({ key, container: 'mapEditAsal', center: [lokasiAwal.lng, lokasiAwal.lat], zoom: 10 });
                markerEditAsal = new tt.Marker().setLngLat([lokasiAwal.lng, lokasiAwal.lat]).addTo(mapEditAsal);
                mapEditAsal.on('click', e => {
                    const coord = { lat: e.lngLat.lat, lng: e.lngLat.lng };
                    document.getElementById('edit-lokasi-awal').value = JSON.stringify(coord);
                    if (markerEditAsal) markerEditAsal.remove();
                    markerEditAsal = new tt.Marker().setLngLat([coord.lng, coord.lat]).addTo(mapEditAsal);
                });

                mapEditTujuan = tt.map({ key, container: 'mapEditTujuan', center: [lokasiTujuan.lng, lokasiTujuan.lat], zoom: 10 });
                markerEditTujuan = new tt.Marker().setLngLat([lokasiTujuan.lng, lokasiTujuan.lat]).addTo(mapEditTujuan);
                mapEditTujuan.on('click', e => {
                    const coord = { lat: e.lngLat.lat, lng: e.lngLat.lng };
                    document.getElementById('edit-lokasi-tujuan').value = JSON.stringify(coord);
                    if (markerEditTujuan) markerEditTujuan.remove();
                    markerEditTujuan = new tt.Marker().setLngLat([coord.lng, coord.lat]).addTo(mapEditTujuan);
                });

                new bootstrap.Modal(document.getElementById("editModal")).show();
            });
        });
    });
</script>
@endpush