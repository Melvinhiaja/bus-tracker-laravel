@extends('layouts.app')

@section('head')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="container mt-4">
    <div class="row gy-4">
        <!-- Kalender -->
        <div class="col-lg-6 col-md-12">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Kalender Agenda</h5>
                </div>
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>

        <!-- Carousel -->
        <div class="col-lg-6 col-md-12">
            <div class="card shadow-sm h-100">
                <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
                    <!-- Indicators -->
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="0" class="active"></button>
                        <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="1"></button>
                        <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="2"></button>
                    </div>

                    <!-- Slides -->
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="/images/slide1.jpeg" class="carousel-image d-block w-100" alt="Slide 1">
                        </div>
                        <div class="carousel-item">
                            <img src="/images/slide2.jpeg" class="carousel-image d-block w-100" alt="Slide 2">
                        </div>
                        <div class="carousel-item">
                            <img src="/images/slide3.jpeg" class="carousel-image d-block w-100" alt="Slide 3">
                        </div>
                    </div>

                    <!-- Controls -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Google Maps -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Lokasi Kami</h5>
                </div>
                <div class="card-body">
                    <!-- Google Maps Embed -->
                    <iframe src="https://maps.google.com/maps?q=Bandung&t=&z=13&ie=UTF8&iwloc=&output=embed"
                        width="100%" height="300" frameborder="0" style="border:0;" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .carousel-image {
        height: 300px;
        object-fit: cover;
    }

    @media (max-width: 768px) {
        .carousel-image {
            height: 200px;
        }
    }

    #calendar {
        min-height: 500px;
    }

    .card {
        border-radius: 0.75rem;
    }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'id',
        editable: true,
        selectable: true,
        events: '/api/events',

        select: function (info) {
            var title = prompt('Masukkan Judul Acara:');
            if (title) {
                fetch('/api/events', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        title: title,
                        start: info.startStr,
                        end: info.endStr
                    })
                }).then(() => calendar.refetchEvents());
            }
            calendar.unselect();
        },

        eventClick: function (info) {
            var edit = confirm('Edit judul? OK = Edit, Cancel = Hapus');
            if (edit) {
                var newTitle = prompt('Edit Judul:', info.event.title);
                if (newTitle) {
                    fetch(`/api/events/${info.event.id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            title: newTitle,
                            start: info.event.startStr,
                            end: info.event.endStr
                        })
                    }).then(() => calendar.refetchEvents());
                }
            } else {
                if (confirm('Yakin ingin menghapus acara ini?')) {
                    fetch(`/api/events/${info.event.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(() => calendar.refetchEvents());
                }
            }
        },

        eventDrop: function (info) {
            fetch(`/api/events/${info.event.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    title: info.event.title,
                    start: info.event.startStr,
                    end: info.event.endStr
                })
            });
        }
    });

    calendar.render();
});
</script>
@endpush
