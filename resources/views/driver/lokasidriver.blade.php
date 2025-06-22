@extends('layouts.app')

@section('title', 'Berbagi Lokasi Otomatis')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white text-center">
            <h4 class="mb-0">🛰️ Berbagi Lokasi (Auto-Tracking)</h4>
        </div>
        <div class="card-body">
            <!-- MAP -->
            <div id="map" style="height: 400px; width: 100%;" class="mb-4 rounded shadow-sm"></div>

            <!-- INFO TRACKING -->
            <div class="row g-3 text-center text-md-start info-text mb-3">
                <div class="col-md-6">
                    <span><strong>🚌 Bus:</strong> {{ $namaBus }}</span>
                </div>
                <div class="col-md-6">
                    <span><strong>🗺️ Rute:</strong> {{ $tiket->perjalanan->asal }} - {{ $tiket->perjalanan->tujuan }}</span>
                </div>
                <div class="col-md-6">
                    <span><strong>⏱️ Estimasi Waktu:</strong> <span id="estimasi">-</span></span>
                </div>
                <div class="col-md-6">
                    <span><strong>📏 Jarak:</strong> <span id="jarak">-</span></span>
                </div>
                <div class="col-md-6">
                    <span><strong>📍 Lokasi Awal:</strong> <span id="lokasiAwalTeks">-</span></span>
                </div>
                <div class="col-md-6">
                    <span><strong>🎯 Lokasi Tujuan:</strong> <span id="lokasiTujuanTeks">-</span></span>
                </div>
            </div>

            <!-- BUTTON AKSI -->
            <div class="d-grid">
                <button id="btnSharePosisi" class="btn btn-success btn-lg" onclick="mulaiPantauPosisi()" disabled>
                    🚍 Mulai Share Posisi Realtime
                </button>
            </div>
        </div>
    </div>
</div>


<style>
    @media (max-width: 768px) {
        #map {
            height: 300px !important;
        }

        .info-text span {
            display: block;
            margin-bottom: 5px;
        }
    }

    .bus-marker {
        background-image: url('/images/icon-bus-kostum.png');
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        width: 32px;
        height: 32px;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>
<script src="https://api.tomtom.com/maps-sdk-for-web/cdn/6.x/6.17.0/maps/maps-web.min.js"></script>
<script src="https://api.tomtom.com/maps-sdk-for-web/cdn/6.x/6.17.0/services/services-web.min.js"></script>
<link rel="stylesheet" href="https://api.tomtom.com/maps-sdk-for-web/cdn/6.x/6.17.0/maps/maps.css" />
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const apiKey = '{{ config("services.tomtom.key") }}';
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    let map, startMarker, endMarker, busMarker;
    let lokasiAwal = {!! $tiket->perjalanan->lokasi_awal !!};
    let lokasiTujuan = {!! $tiket->perjalanan->lokasi_tujuan !!};
    let ruteData = null;
    let sudahMulaiTracking = false;
    let sudahSampai = false;

    function createBusIcon() {
        const el = document.createElement('div');
        el.className = 'bus-marker';
        return el;
    }

    function hitungJarakMeter(lat1, lon1, lat2, lon2) {
        const R = 6371000;
        const toRad = deg => deg * Math.PI / 180;
        const dLat = toRad(lat2 - lat1);
        const dLon = toRad(lon2 - lon1);
        const a = Math.sin(dLat / 2) ** 2 + Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLon / 2) ** 2;
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }

    function initMap() {
        map = tt.map({
            key: apiKey,
            container: 'map',
            center: [lokasiAwal.lng, lokasiAwal.lat],
            zoom: 13
        });

        map.scrollZoom.enable();
        map.dragPan.enable();

        // Tambahkan marker awal dan tujuan
        startMarker = new tt.Marker({ color: 'green' }).setLngLat([lokasiAwal.lng, lokasiAwal.lat]).addTo(map);
        endMarker = new tt.Marker({ color: 'red' }).setLngLat([lokasiTujuan.lng, lokasiTujuan.lat]).addTo(map);

        document.getElementById('lokasiAwalTeks').textContent = `${lokasiAwal.lat}, ${lokasiAwal.lng}`;
        document.getElementById('lokasiTujuanTeks').textContent = `${lokasiTujuan.lat}, ${lokasiTujuan.lng}`;

        getRouteAndSend([lokasiAwal.lng, lokasiAwal.lat], [lokasiTujuan.lng, lokasiTujuan.lat]);
    }

    function getRouteAndSend(start, end) {
        tt.services.calculateRoute({
            key: apiKey,
            locations: [start, end]
        }).then(result => {
            ruteData = result.toGeoJson();
            const summary = result.routes[0].summary;

            document.getElementById('estimasi').textContent = `${Math.ceil(summary.travelTimeInSeconds / 60)} menit`;
            document.getElementById('jarak').textContent = `${(summary.lengthInMeters / 1000).toFixed(2)} km`;

            map.addLayer({
                id: 'routeLine',
                type: 'line',
                source: { type: 'geojson', data: ruteData },
                paint: { 'line-color': '#4a90e2', 'line-width': 6 }
            });

            // Kirim ke backend
            fetch("{{ secure_url(route('tracking.kirim', [], false)) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    lokasi_awal: start,
                    lokasi_tujuan: end,
                    status: 'Berjalan',
                    rute: ruteData,
                    waktu_tempuh: Math.ceil(summary.travelTimeInSeconds / 60),
                    jarak_tempuh: (summary.lengthInMeters / 1000).toFixed(2),
                    bus_id: {{ $tiket->bus_id ?? 'null' }},
                            rute_asal: "{{ $tiket->perjalanan->asal }}",
        rute_tujuan: "{{ $tiket->perjalanan->tujuan }}"
                })
            }).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Lokasi berhasil dikirim!',
                    timer: 1500,
                    showConfirmButton: false
                });

                document.getElementById('btnSharePosisi').disabled = false;
            });
        });
    }

    function mulaiPantauPosisi() {
        document.getElementById('btnSharePosisi').disabled = true;
        Swal.fire({
            icon: 'success',
            title: 'Share Posisi Dimulai',
            timer: 1500,
            showConfirmButton: false
        });

        navigator.geolocation.watchPosition(pos => {
            const posisi = {
                lat: pos.coords.latitude,
                lng: pos.coords.longitude
            };

            const jarakDariAwal = hitungJarakMeter(posisi.lat, posisi.lng, lokasiAwal.lat, lokasiAwal.lng);
            if (!sudahMulaiTracking && jarakDariAwal < 10) return;
            if (!sudahMulaiTracking) sudahMulaiTracking = true;

            console.log('🚀 Mengirim posisi:', posisi, 'Bus ID:', {{ $tiket->bus_id }}); // 🔥 log sebelum fetch

fetch("{{ route('tracking.update_posisi') }}", {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify({ 
        posisi_driver: posisi,
        bus_id: {{ $tiket->bus_id }} // 🔥 pastikan ini bener
    })
});



            if (!busMarker) {
    busMarker = new tt.Marker({ element: createBusIcon() }).setLngLat([posisi.lng, posisi.lat]).addTo(map);
} else {
    busMarker.setLngLat([posisi.lng, posisi.lat]);
}

// 🔄 Auto center & zoom mengikuti posisi driver
map.flyTo({
    center: [posisi.lng, posisi.lat],
    zoom: 17,
    speed: 0.9 // kecepatan gerakan kamera
});


            const jarakKeTujuan = hitungJarakMeter(posisi.lat, posisi.lng, lokasiTujuan.lat, lokasiTujuan.lng);
            if (jarakKeTujuan < 20 && !sudahSampai) {
                sudahSampai = true;
                Swal.fire({
                    icon: 'success',
                    title: 'Anda telah sampai di tujuan!',
                    timer: 2500,
                    showConfirmButton: false
                });

                fetch("{{ route('tracking.update_status_otomatis') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ status: 'Sampai' })
                });
            }
        }, () => {
            alert('Gagal mendapatkan lokasi!');
        }, {
            enableHighAccuracy: true
            
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        initMap();
    });
</script>
@endsection
