@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <pre id="tesRute"></pre>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Tracking Rute</h3>
        </div>
        <div class="card-body">
            <div id="map" style="height: 500px;"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://api.tomtom.com/maps-sdk-for-web/cdn/6.x/6.20.0/maps/maps-web.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://api.tomtom.com/maps-sdk-for-web/cdn/6.x/6.20.0/maps/maps.css">

<script>
document.addEventListener("DOMContentLoaded", function() {
    var ruteData = {!! json_encode($rute) !!};
    var apiKey = "{{ $tomtomKey }}";
        console.log("Data Rute:", ruteData);
    console.log("TomTom API Key:", apiKey);

    if (ruteData.length === 0) {
        console.error("Data rute kosong!");
        return;
    }

    var map = tt.map({
        key: apiKey,
        container: 'map',
        center: ruteData[0], // Titik awal sebagai pusat peta
        zoom: 13
    });

    map.addControl(new tt.FullscreenControl());
    map.addControl(new tt.NavigationControl());

    // Tambahkan Marker
    new tt.Marker().setLngLat(ruteData[0]).addTo(map); // Start
    new tt.Marker().setLngLat(ruteData[1]).addTo(map); // End

    // Gambar Garis Rute
    var geojson = {
        'type': 'Feature',
        'geometry': {
            'type': 'LineString',
            'coordinates': ruteData
        }
    };

    map.on('load', function () {
        map.addLayer({
            'id': 'route',
            'type': 'line',
            'source': {
                'type': 'geojson',
                'data': geojson
            },
            'layout': {
                'line-join': 'round',
                'line-cap': 'round'
            },
            'paint': {
                'line-color': '#1DA1F2',
                'line-width': 4
            }
        });
    });
});
</script>

@endpush
