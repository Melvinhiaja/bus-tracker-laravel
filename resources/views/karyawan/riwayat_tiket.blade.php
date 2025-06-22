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
                            <th>No</th>
                            <th>Bus</th>
                            <th>Tujuan</th>
                            <th>Tanggal Berangkat</th>
                            <th>Tanggal Pulang</th>
                            <th>Jumlah Penumpang</th>
                            <th>Status</th>

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
                            <td>{{ $tiket->jumlah_penumpang }} penumpang</td>
                            <td class="d-flex justify-content-between align-items-center">
    @if($tiket->status === 'selesai')
        <span class="badge bg-success">Selesai</span>
    @else
        <span class="badge bg-secondary">Tidak Diketahui</span>
    @endif

    <!-- Tombol Hapus -->
    <button class="btn btn-danger btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#hapusRiwayatModal{{ $tiket->id }}">
        <i class="bi bi-trash"></i>
    </button>

    <!-- Modal Hapus -->
    <div class="modal fade" id="hapusRiwayatModal{{ $tiket->id }}" tabindex="-1" aria-labelledby="hapusRiwayatModalLabel{{ $tiket->id }}" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form action="{{ route('karyawan.tiket.destroy', $tiket->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-header">
              <h5 class="modal-title" id="hapusRiwayatModalLabel{{ $tiket->id }}">Konfirmasi Hapus</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
              Apakah Anda yakin ingin menghapus tiket ini dari riwayat?
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-danger">Hapus</button>
            </div>
          </form>
        </div>
      </div>
    </div>
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
