@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Tracking Perjalanan Driver</h3>

    <div id="map" style="height: 500px;"></div>

    <form method="POST" id="updateForm">
        @csrf
        <input type="hidden" id="latitude" name="latitude">
        <input type="hidden" id="longitude" name="longitude">

        <!-- Bagian penentuan lokasi tujuan -->
        <div class="mb-3">
            <label>Lokasi Tujuan:</label>
            <input type="text" id="destination" class="form-control" placeholder="Masukkan nama lokasi tujuan...">
            <button type="button" onclick="setDestinationBySearch()" class="btn btn-primary mt-2">Cari Tujuan</button>
            <button type="button" onclick="setModeDestination()" class="btn btn-secondary mt-2">Atur Tujuan Manual</button>
        </div>

        <!-- Opsi untuk mengatur lokasi awal secara manual -->
        <div class="mb-3">
            <button type="button" id="btnSetStartManual" onclick="setModeStart()" class="btn btn-warning">Atur Lokasi Awal Manual</button>
            <button type="button" id="btnResetStart" onclick="resetStartLocation()" class="btn btn-info">Reset Lokasi Awal ke Deteksi Otomatis</button>
        </div>

        <p><strong>Estimasi Tiba:</strong> <span id="eta">-</span></p>
        <p><strong>Jarak ke Tujuan:</strong> <span id="distanceDisplay">-</span> km</p>
    </form>
</div>

<!-- Sertakan Script dan CSS TomTom Maps SDK -->
<script src="https://api.tomtom.com/maps-sdk-for-web/cdn/6.x/6.20.0/maps/maps-web.min.js"></script>
<link rel="stylesheet" href="https://api.tomtom.com/maps-sdk-for-web/cdn/6.x/6.20.0/maps/maps.css">

<script>
    // Variabel global
    let map, driverMarker, destinationMarker;
    let driverLocation = { lat: 0, lng: 0 };
    let destination = null;
    // currentSelectionMode: '' (tidak aktif), 'start' untuk pengaturan lokasi awal manual, 'destination' untuk pengaturan tujuan manual.
    let currentSelectionMode = '';
    // Flag untuk menandai apakah lokasi awal telah diatur secara manual
    let isManualStart = false;
    const tomtomKey = '{{ config("services.tomtom.key") }}';
    console.log("TomTom API Key:", tomtomKey);

    // Inisialisasi peta dan marker awal
    function initMap() {
        map = tt.map({
            key: tomtomKey,
            container: "map",
            center: [107.577740, -6.803405], // Default: Bandung
            zoom: 14
        });

        // Marker untuk lokasi driver (warna biru)
        driverMarker = new tt.Marker({ color: "blue" })
            .setLngLat([107.577740, -6.803405])
            .addTo(map);

        // Mendengarkan klik pada peta untuk memilih lokasi (awal atau tujuan)
        map.on('click', function(event) {
            let lngLat = event.lngLat;
            console.log("Peta diklik pada:", lngLat);
            if (currentSelectionMode === 'destination') {
                setDestinationByClick(lngLat.lat, lngLat.lng);
            } else if (currentSelectionMode === 'start') {
                setManualStart(lngLat.lat, lngLat.lng);
            }
        });
    }

    // Update lokasi driver secara otomatis menggunakan geolocation (jika belum diatur secara manual)
    function updateDriverLocation() {
        if (navigator.geolocation && !isManualStart) {
            navigator.geolocation.watchPosition(position => {
                let lat = parseFloat(position.coords.latitude.toFixed(6));
                let lng = parseFloat(position.coords.longitude.toFixed(6));
                driverLocation = { lat, lng };
                console.log("Driver location updated:", driverLocation);

                // Perbarui marker lokasi driver
                driverMarker.setLngLat([lng, lat]);

                // Perbarui nilai pada input tersembunyi
                document.getElementById("latitude").value = lat;
                document.getElementById("longitude").value = lng;

                // Jika tujuan sudah ditentukan, perbarui rute
                if (destination) {
                    updateRoute();
                }
            }, showError, { enableHighAccuracy: true });
        } else {
            console.warn("Geolocation tidak didukung atau lokasi manual aktif.");
        }
    }

    // Fungsi untuk memilih lokasi awal secara manual (mode 'start')
    function setManualStart(lat, lng) {
        isManualStart = true; // Nonaktifkan auto-detect
        driverLocation = { lat, lng };
        console.log("Manual start set to:", driverLocation);
        driverMarker.setLngLat([lng, lat]);
        document.getElementById("latitude").value = lat;
        document.getElementById("longitude").value = lng;
        alert("Lokasi awal berhasil diatur secara manual.");
        // Reset mode pemilihan
        currentSelectionMode = '';
        // Jika tujuan sudah ada, perbarui rute
        if (destination) {
            updateRoute();
        }
    }

    // Fungsi untuk mengembalikan deteksi lokasi secara otomatis
    function resetStartLocation() {
        isManualStart = false;
        alert("Lokasi awal telah direset ke deteksi lokasi saat ini.");
        updateDriverLocation();
    }

    // Aktifkan mode untuk memilih lokasi awal secara manual
    function setModeStart() {
        currentSelectionMode = 'start';
        alert("Klik di peta untuk memilih lokasi awal secara manual.");
    }

    // Aktifkan mode untuk memilih lokasi tujuan secara manual
    function setModeDestination() {
        currentSelectionMode = 'destination';
        alert("Klik di peta untuk memilih lokasi tujuan secara manual.");
    }

    // Fungsi untuk menetapkan lokasi tujuan berdasarkan pencarian teks
    function setDestinationBySearch() {
        let query = document.getElementById("destination").value;
        if (!query) {
            alert("Masukkan lokasi tujuan!");
            return;
        }
        console.log("Searching destination for query:", query);
        fetch(`https://api.tomtom.com/search/2/geocode/${encodeURIComponent(query)}.json?key=${tomtomKey}`)
            .then(response => response.json())
            .then(data => {
                console.log("Geocode result:", data);
                if (!data.results || data.results.length === 0) {
                    alert("Tujuan tidak ditemukan!");
                    return;
                }
                let dest = data.results[0].position;
                setDestination(dest.lat, dest.lon);
            })
            .catch(error => {
                console.error("Error pencarian lokasi:", error);
                alert("Terjadi kesalahan saat mencari lokasi tujuan.");
            });
    }

    // Fungsi untuk menetapkan tujuan melalui klik peta (mode 'destination')
    function setDestinationByClick(lat, lng) {
        setDestination(lat, lng);
    }

    // Fungsi untuk menetapkan tujuan, membuat marker tujuan (warna merah), dan memanggil updateRoute()
    function setDestination(lat, lng) {
        destination = { lat, lng };
        console.log("Destination set to:", destination);
        if (destinationMarker) destinationMarker.remove();
        destinationMarker = new tt.Marker({ color: "red" })
            .setLngLat([lng, lat])
            .addTo(map);

        updateRoute();
    }

    // Fungsi untuk mengambil rute, menghitung ETA dan jarak
    function updateRoute() {
        if (!destination) return;
        console.log("Updating route with driverLocation:", driverLocation, "and destination:", destination);

        // Pastikan urutan koordinat: longitude,latitude dan tambahkan parameter routeType=fastest
        let url = `https://api.tomtom.com/routing/1/calculateRoute/${driverLocation.lng},${driverLocation.lat}:${destination.lng},${destination.lat}/json?key=${tomtomKey}&computeTravelTimeFor=all&routeType=fastest`;
        console.log("Routing URL:", url);

        fetch(url)
            .then(response => response.json())
            .then(data => {
                console.log("Response TomTom:", data);
                if (!data.routes || data.routes.length === 0) {
                    alert("Gagal menghitung rute! Periksa API key atau pastikan rute tersedia.");
                    return;
                }

                let route = data.routes[0];
                // Estimasi waktu tiba (dalam menit)
                let travelTime = Math.round(route.summary.travelTimeInSeconds / 60);
                document.getElementById("eta").innerText = `${travelTime} menit`;

                // Tampilkan jarak perjalanan (dalam km)
                let distanceKm = (route.summary.lengthInMeters / 1000).toFixed(2);
                document.getElementById("distanceDisplay").innerText = distanceKm;

                // Hapus layer rute sebelumnya jika ada
                if (map.getLayer('routeLayer')) {
                    map.removeLayer('routeLayer');
                    map.removeSource('routeSource');
                }

                // Buat GeoJSON dari data rute
                let routeGeoJSON = {
                    type: "Feature",
                    geometry: {
                        type: "LineString",
                        coordinates: route.legs[0].points.map(p => [p.longitude, p.latitude])
                    }
                };

                // Tambahkan source dan layer untuk rute
                map.addSource('routeSource', {
                    type: 'geojson',
                    data: {
                        type: "FeatureCollection",
                        features: [routeGeoJSON]
                    }
                });

                map.addLayer({
                    id: 'routeLayer',
                    type: 'line',
                    source: 'routeSource',
                    paint: {
                        "line-color": "#007AFF",
                        "line-width": 5
                    }
                });

                // Periksa apakah driver sudah tiba (jarak < 50 meter)
                checkArrival();
            })
            .catch(error => {
                console.error("Error fetching route:", error);
                alert("Terjadi kesalahan saat mengambil rute. Cek console untuk detail.");
            });
    }

    // Fungsi memeriksa apakah driver telah tiba di tujuan (jarak kurang dari 0.05 km)
    function checkArrival() {
        if (!destination) return;
        let distance = getDistance(driverLocation.lat, driverLocation.lng, destination.lat, destination.lng);
        console.log("Distance to destination:", distance, "km");
        if (distance < 0.05) {
            alert("Anda sudah tiba di tujuan!");
            destination = null;
            if (destinationMarker) {
                destinationMarker.remove();
                destinationMarker = null;
            }
        }
    }

    // Fungsi perhitungan jarak dengan rumus Haversine (km)
    function getDistance(lat1, lng1, lat2, lng2) {
        const R = 6371; // radius bumi (km)
        let dLat = (lat2 - lat1) * Math.PI / 180;
        let dLng = (lng2 - lng1) * Math.PI / 180;
        let a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLng / 2) * Math.sin(dLng / 2);
        let c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }

    // Fungsi error handling geolocation
    function showError(error) {
        console.error("Geolocation error:", error);
        alert("Gagal mendapatkan lokasi: " + error.message);
    }

    // Update rute secara periodik setiap 3 detik
    setInterval(() => {
        if (destination) {
            updateRoute();
        }
    }, 3000);

    document.addEventListener("DOMContentLoaded", () => {
        initMap();
        updateDriverLocation();
    });
</script>
@endsection
