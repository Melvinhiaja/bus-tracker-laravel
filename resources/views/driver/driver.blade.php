@extends('layouts.app')

@section('title', 'Perjalanan Aktif Driver')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">Perjalanan Aktif</h4>
        </div>

        <div class="card-body">
            @if($tikets->isEmpty())
            <div class="alert alert-info text-center">
                <strong>Tidak ada perjalanan aktif untuk saat ini.</strong>
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Bus</th>
                            <th>Rute</th>
                            <th>Berangkat</th>
                            <th>Pulang</th>
                            <th>Penumpang</th>
                            <th>Aksi</th>
                            <th>Lokasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tikets as $index => $tiket)
                        @php
                        // Cek apakah tracking bus ini sedang aktif (status "Berjalan")
                        $trackingAktif = \App\Models\TrackingPosition::where('bus_id', $tiket->bus_id)
                        ->where('status', 'Berjalan')
                        ->where('user_id', '!=', auth()->id()) // 🔥 Tambahin ini
                        ->exists();
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $tiket->bus->nomor_bus ?? '-' }}</td>
                            <td>{{ $tiket->perjalanan->asal }} - {{ $tiket->perjalanan->tujuan }}</td>
                            <td>{{ \Carbon\Carbon::parse($tiket->tanggal_berangkat)->format('d M Y') }}</td>
                            <td>{{ $tiket->tanggal_pulang ? \Carbon\Carbon::parse($tiket->tanggal_pulang)->format('d M Y') : '-' }}</td>
                            <td>{{ $tiket->penumpangs->count() }} penumpang</td>
                            <td class="text-nowrap">
                                <a href="{{ route('driver.show', $tiket->id) }}" class="btn btn-info btn-sm mb-1 w-100">
                                    <i class="bi bi-eye"></i> Detail
                                </a>

                                {{-- 🔥 Cek role admin/karyawan --}}
                                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'karyawan')
                                <form action="{{ route('tiket.selesaikan', $tiket->id) }}" method="POST" onsubmit="return confirm('Apakah perjalanan sudah selesai?')">
                                    @csrf
                                    <button class="btn btn-success btn-sm w-100">
                                        <i class="bi bi-check-circle"></i> Selesai
                                    </button>
                                </form>
                                @endif
                            </td>

                            <td class="text-nowrap">
    @if(auth()->user()->role === 'driver')
        @if($trackingAktif)
        <button type="button" class="btn btn-secondary btn-sm w-100" onclick="showTrackingAktifAlert()">
            <i class="bi bi-geo-alt-fill"></i> Berbagi Lokasi
        </button>
        @else
        <a href="{{ route('driver.lokasidriver', $tiket->id) }}" class="btn btn-warning btn-sm mb-1 w-100">
            <i class="bi bi-geo-alt-fill"></i> Berbagi Lokasi
        </a>
        @endif
    @else
        <span class="badge bg-secondary">-</span> {{-- 🔥 Kalau bukan driver, cuma tampilin strip --}}
    @endif
</td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- SweetAlert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function showTrackingAktifAlert() {
        Swal.fire({
            icon: 'info',
            title: 'Tiket ini sedang berjalan.',
            text: 'Silakan selesaikan perjalanan terlebih dahulu sebelum berbagi lokasi lagi.',
            confirmButtonText: 'Oke'
        });
    }

    @if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: '{{ session("error") }}',
        confirmButtonText: 'Oke'
    });
    @endif
</script>
@endsection