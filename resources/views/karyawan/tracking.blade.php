@extends('layouts.app')

@section('title', 'Monitoring Lokasi Driver')

@section('content')
<div class="container mt-4">
    <h4 class="mb-3">Monitoring Lokasi Driver</h4>

    <div id="map" style="height: 300px; width: 100%;"></div>

    <div class="mt-3">
        @php $data = $data ?? []; @endphp
        <ul class="list-group">
            @forelse($data as $item)
            <li class="list-group-item d-flex justify-content-between align-items-center" data-bus-id="{{ $item->bus->id }}">
            <div>       
                    <strong>Bus:</strong> {{ $item->bus->nomor_bus ?? '-' }} |
                    <strong>Rute:</strong> {{ $item->rute_asal }} - {{ $item->rute_tujuan }} |
                    <strong>Estimasi Waktu:</strong> {{ $item->waktu_tempuh ?? '-' }} menit |
                    <strong>Jarak:</strong> {{ $item->jarak_tempuh ?? '-' }} km |
                    <strong>Status:</strong>
                    @if($item->status === 'Sampai')
                        <span class="badge bg-success">Sampai</span>
                    @else
                        <span class="badge bg-warning text-dark">Berjalan</span>
                    @endif
                </div>
                <form action="{{ route('tracking.hapus', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger">🗑 Hapus</button>
                </form>
            </li>
            @empty
            <li class="list-group-item">Belum ada lokasi terkirim.</li>
            @endforelse
        </ul>
    </div>
</div>

{{-- TomTom --}}
<script src="https://api.tomtom.com/maps-sdk-for-web/cdn/6.x/6.17.0/maps/maps-web.min.js"></script>
<link rel="stylesheet" href="https://api.tomtom.com/maps-sdk-for-web/cdn/6.x/6.17.0/maps/maps.css" />

<style>
    .bus-marker-0 {
        background-image: url('/images/icon-bus-kostum.png');
    }
    .bus-marker-1 {
        background-image: url('/images/icon-bus-kostum1.png');
    }
    .bus-marker-2 {
        background-image: url('/images/icon-bus-kostum2.png');
    }
    .bus-marker-3 {
        background-image: url('/images/icon-bus-kostum3.png');
    }
    .bus-marker-0, .bus-marker-1, .bus-marker-2, .bus-marker-3 {
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        width: 32px;
        height: 32px;
    }
</style>

<script>
    const data = @json($data);
    const map = tt.map({
        key: '{{ config("services.tomtom.key") }}',
        container: 'map',
        center: [107.6098, -6.9147],
        zoom: 13
    });

    let busMarkers = {}; // Multi bus marker

    function toLngLatArray(pos) {
        if (Array.isArray(pos)) return pos;
        if (pos && typeof pos === 'object' && 'lat' in pos && ('lng' in pos || 'lon' in pos)) {
            return [pos.lng || pos.lon, pos.lat];
        }
        return null;
    }

    function createBusIcon(index) {
        const el = document.createElement('div');
        el.className = `bus-marker-${index % 4}`; // 🔥 Sesuaikan dengan 4 icon
        return el;
    }

    map.on('load', () => {
        data.forEach((item, index) => {
            const start = toLngLatArray(item.lokasi_awal);
            const end = toLngLatArray(item.lokasi_tujuan);

            if (start) {
                new tt.Marker({ color: 'blue' })
                    .setLngLat(start)
                    .setPopup(new tt.Popup().setText(`Lokasi Awal (${item.status})`))
                    .addTo(map);
            }

            if (end) {
                new tt.Marker({ color: 'red' })
                    .setLngLat(end)
                    .setPopup(new tt.Popup().setText(`Tujuan (${item.status})`))
                    .addTo(map);
            }

            if (item.rute && item.rute.type === 'FeatureCollection') {
                const sourceId = `rute-source-${index}`;
                const layerId = `rute-layer-${index}`;

                if (map.getLayer(layerId)) map.removeLayer(layerId);
                if (map.getSource(sourceId)) map.removeSource(sourceId);

                map.addSource(sourceId, { type: 'geojson', data: item.rute });

                map.addLayer({
                    id: layerId,
                    type: 'line',
                    source: sourceId,
                    layout: { 'line-join': 'round', 'line-cap': 'round' },
                    paint: {
                        'line-color': item.status === 'Sampai' ? 'green' : 'orange',
                        'line-width': 5
                    }
                });
            }
        });
    });
    // Tambah fungsi untuk klik item di list
document.querySelectorAll('.list-group-item').forEach(item => {
    item.addEventListener('click', function() {
        const busId = this.getAttribute('data-bus-id');
        if (busMarkers[busId]) {
            const lngLat = busMarkers[busId].getLngLat();
            map.flyTo({
                center: lngLat,
                zoom: 17,
                speed: 0.7
            });
        }
    });
});


    function updatePosisiDriver() {
        fetch("{{ route('tracking.posisi_terkini') }}")
            .then(res => res.json())
            .then(datas => {
                datas.forEach((item, index) => {
                    const busId = item.bus_id;
                    const posisi = item.posisi_driver;

                    if (posisi && posisi.lat && posisi.lng) {
                        const lngLat = [posisi.lng, posisi.lat];

                        if (!busMarkers[busId]) {
                            busMarkers[busId] = new tt.Marker({ element: createBusIcon(index) })
                                .setLngLat(lngLat)
                                .addTo(map);
                        } else {
                            busMarkers[busId].setLngLat(lngLat);
                        }
                    }
                });
            })
            .catch(err => {
                console.error('❌ Gagal ambil posisi:', err);
            });
    }

    setInterval(updatePosisiDriver, 1000); // Update tiap detik
</script>
@endsection
