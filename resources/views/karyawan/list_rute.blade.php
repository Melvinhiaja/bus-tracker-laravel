@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">List Rute Perjalanan Bus</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr class="text-center">
                            <th>List</th>
                            <th>Lokasi</th>
                            <th>Tujuan</th>
                            <th>Waktu Dibuat</th>
                            <th>Aksi</th>

                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rutes as $rute)
                        <tr class="text-center">
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $rute->start_name }}</strong> <br>
                                <small class="text-muted">{{ $rute->start_lat }}, {{ $rute->start_lng }}</small>
                            </td>
                            <td>
                                <strong>{{ $rute->end_name }}</strong> <br>
                                <small class="text-muted">{{ $rute->end_lat }}, {{ $rute->end_lng }}</small>
                            </td>
                            <td>
                                <span class="badge bg-success">
                                    {{ \Carbon\Carbon::parse($rute->created_at)->format('d M Y, H:i') }}
                                </span>
                            </td>
                            <td>
            <a href="{{ route('lihat.rute', $rute->id) }}" class="btn btn-sm btn-info">
                <i class="fas fa-map-marked-alt"></i> Lihat Rute
            </a>
        </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-danger">
                                <strong>Belum ada data rute</strong>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
