@extends('layouts.app')

@section('title', 'Riwayat Tiket')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <h4 class="mb-0">Riwayat Tiket</h4>
        </div>
        <div class="card-body">
            @if($tikets->isEmpty())
            <div class="alert alert-info text-center">Tidak ada tiket yang sudah lewat.</div>
            @else
            <table class="table table-bordered">
            <thead class="table-dark">
    <tr>
        <th>No0</th>
        <th>Bus</th>
        <th>Tujuan</th>
        <th>Tanggal Berangkat</th>
        <th>Tanggal Pulang</th>
        <th>Jumlah Penumpang</th>
        <th>Status</th> <!-- ✅ Tambahan -->
    </tr>
</thead>

<tbody>
    @foreach($tikets as $tiket)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $tiket->bus->nomor_bus ?? '-' }}</td>
        <td>{{ $tiket->perjalanan->asal ?? '-' }} - {{ $tiket->perjalanan->tujuan ?? '-' }}</td>
        <td>{{ \Carbon\Carbon::parse($tiket->tanggal_berangkat)->format('d M Y') }}</td>
        <td>{{ $tiket->tanggal_pulang ? \Carbon\Carbon::parse($tiket->tanggal_pulang)->format('d M Y') : '-' }}</td>
        <td>{{ $tiket->penumpangs->count() }} penumpang</td>
        <td>
            @if($tiket->status === 'selesai')
                <span class="badge bg-success">Selesai</span>
            @else
                <span class="badge bg-secondary">Tidak Diketahui</span>
            @endif
        </td>
    </tr>
    @endforeach
</tbody>

            </table>
            @endif
        </div>
    </div>
</div>
@endsection