@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Tentukan Lokasi dan Rute</h4>
        </div>
        <div class="card-body">
            <div id="map" style="height: 500px;" class="mb-3"></div>

            <div class="btn-group mb-3 w-100">
                <button class="btn btn-success" id="setStartBtn">📍 Lokasi Awal</button>
                <button class="btn btn-danger" id="setEndBtn">🏁 Lokasi Tujuan</button>
                <button class="btn btn-secondary" id="detectLocationBtn">🔄 Deteksi Lokasi Saya</button>
            </div>

            <div id="targetInfo" class="alert alert-secondary text-center" style="display: none;"></div>
            <div id="status" class="alert alert-info text-center" style="display: none;"></div>
        </div>
    </div>
</div>

<script src="https://api.tomtom.com/maps-sdk-for-web/cdn/6.x/6.20.0/maps/maps-web.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://api.tomtom.com/maps-sdk-for-web/cdn/6.x/6.20.0/maps/maps.css">

<script>
const apiKey = '{{ env("TOMTOM_API_KEY") }}';

const map = tt.map({
    key: apiKey,
    container: 'map',
    center: [107.6028, -6.8331],
    zoom: 15
});

let userMarker, targetMarker, userLocation, targetLocation;
let mode = null;

// Status helper
function setStatus(msg, isError = false) {
    const el = document.getElementById('status');
    el.textContent = msg;
    el.style.display = 'block';
    el.className = isError ? 'alert alert-danger text-center' : 'alert alert-info text-center';
}

// Reverse Geocode
async function getLocationName(lat, lng) {
    const url = `https://api.tomtom.com/search/2/reverseGeocode/${lng},${lat}.json?key=${apiKey}`;
    const res = await fetch(url);
    const data = await res.json();
    return data.addresses[0]?.address?.freeformAddress || '';
}

// Fungsi createRoute (Final & Corrected)
async function createRoute() {
    const [startLng, startLat] = userLocation;
    const [endLng, endLat] = targetLocation;

    const url = `https://api.tomtom.com/routing/1/calculateRoute/${startLat},${startLng}:${endLat},${endLng}/json?key=${apiKey}`;

    const res = await fetch(url);
    const data = await res.json();
    const route = data.routes[0];

    if (!route) {
        setStatus('Tidak ada rute ditemukan.', true);
        return;
    }

    const distanceInKm = (route.summary.lengthInMeters / 1000).toFixed(2);
    const timeInMinutes = Math.ceil(route.summary.travelTimeInSeconds / 60);

    const geojson = route.legs[0].points.map(p => [p.longitude, p.latitude]);

    if (map.getLayer('route')) {
        map.removeLayer('route');
        map.removeSource('route');
    }

    map.addLayer({
        id: 'route',
        type: 'line',
        source: {
            type: 'geojson',
            data: { type: 'Feature', geometry: { type: 'LineString', coordinates: geojson } }
        },
        paint: { 'line-color': '#0000ff', 'line-width': 4 }
    });

    document.getElementById('targetInfo').style.display = 'block';
    document.getElementById('targetInfo').textContent = `Jarak: ${distanceInKm} km, Estimasi waktu: ${timeInMinutes} menit`;
}

// Cek otomatis apakah siap buat rute
async function checkAndCreateRoute() {
    if (userLocation && targetLocation) {
        setStatus('Membuat rute...');
        try {
            await createRoute();
            setStatus('✅ Rute berhasil dibuat.');
        } catch (error) {
            console.error('Error creating route:', error);
            setStatus('Terjadi kesalahan saat membuat rute.', true);
        }
    }
}

// Klik Map
map.on('click', async (e) => {
    const { lng, lat } = e.lngLat;

    if (mode === 'start') {
        userLocation = [lng, lat];
        if (!userMarker) {
            userMarker = new tt.Marker({ color: 'green' }).setLngLat(userLocation).addTo(map);
        } else {
            userMarker.setLngLat(userLocation);
        }
        const locName = await getLocationName(lat, lng);
        setStatus(`✅ Lokasi awal: ${locName}`);
        mode = null;  // Reset hanya setelah selesai set lokasi awal
    } else if (mode === 'end') {
        targetLocation = [lng, lat];
        if (!targetMarker) {
            targetMarker = new tt.Marker({ color: 'red' }).setLngLat(targetLocation).addTo(map);
        } else {
            targetMarker.setLngLat(targetLocation);
        }
        const locName = await getLocationName(lat, lng);
        setStatus(`✅ Lokasi tujuan: ${locName}`);
        mode = null; // Reset hanya setelah selesai set lokasi tujuan
    } else {
        // Jangan tampilkan pesan kesalahan jika lokasi sudah lengkap
        if (!userLocation) {
            setStatus('⚠️ Pilih dulu mode: Lokasi Awal.', true);
        } else if (!targetLocation) {
            setStatus('⚠️ Pilih dulu mode: Lokasi Tujuan.', true);
        }
        return;
    }

    await checkAndCreateRoute();
});


// Tombol Lokasi Awal
document.getElementById('setStartBtn').onclick = () => {
    mode = 'start';
    setStatus('Klik peta untuk lokasi awal.');
};

// Tombol Lokasi Tujuan
document.getElementById('setEndBtn').onclick = () => {
    mode = 'end';
    setStatus('Klik peta untuk lokasi tujuan.');
};

// Deteksi otomatis lokasi pengguna
document.getElementById('detectLocationBtn').onclick = () => {
    if ('geolocation' in navigator) {
        setStatus('Mencari lokasi Anda...');
        navigator.geolocation.getCurrentPosition(async pos => {
            const { latitude, longitude } = pos.coords;
            userLocation = [longitude, latitude];
            if (!userMarker) {
                userMarker = new tt.Marker({ color: 'green' }).setLngLat(userLocation).addTo(map);
            } else {
                userMarker.setLngLat(userLocation);
            }
            map.flyTo({ center: userLocation, zoom: 15 });
            const locName = await getLocationName(latitude, longitude);
            setStatus(`✅ Lokasi Anda: ${locName}`);

            await checkAndCreateRoute();
        }, () => setStatus('Gagal mendeteksi lokasi.', true));
    } else {
        setStatus('Browser tidak mendukung Geolocation.', true);
    }
};

// Tombol Lokasi Awal
document.getElementById('setStartBtn').onclick = () => {
    mode = 'start';
    setStatus('Klik peta untuk lokasi awal.');
    document.getElementById('targetInfo').style.display = 'none';
};

// Tombol Lokasi Tujuan
document.getElementById('setEndBtn').onclick = () => {
    mode = 'end';
    setStatus('Klik peta untuk lokasi tujuan.');
    document.getElementById('targetInfo').style.display = 'none';
};

</script>
@endsection

