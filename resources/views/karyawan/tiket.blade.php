@extends('layouts.app')

@section('title', 'Daftar Tiket')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Daftar Tiket</h4>
            <a href="{{ route('karyawan.tiket.riwayat') }}" class="btn btn-light">
                <i class="bi bi-clock-history"></i> Riwayat Tiket
            </a>
        </div>

        @if(session('success'))
        <div class="alert alert-success m-3">{{ session('success') }}</div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger m-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
        @endif

        <div class="card-body">
            <a href="{{ route('karyawan.tiket.create') }}" class="btn btn-success mb-3">
                <i class="bi bi-ticket"></i> Pesan Tiket
            </a>

            @if($tikets->isEmpty())
            <div class="alert alert-warning text-center"><strong>Belum ada tiket yang dipesan.</strong></div>
            @else
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Bus</th>
                            <th>Tujuan</th>
                            <th>Tanggal Berangkat</th>
                            <th>Tanggal Pulang</th>
                            <th>Jumlah Penumpang</th>
                            <th>Sisa Kapasitas</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tikets as $tiket)
                        @php
                            $kapasitas_bus = $tiket->bus->kapasitas ?? 0;
                            $tiket_terpakai = $tiket->penumpangs->count();
                            $sisa_kapasitas = max(0, $kapasitas_bus - $tiket_terpakai);
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                @if($tiket->bus)
                                🚍 {{ $tiket->bus->nomor_bus }}
                                @else
                                <span class="text-danger">Bus Tidak Ditemukan</span>
                                @endif
                            </td>
                            <td>
                                @if($tiket->perjalanan)
                                {{ $tiket->perjalanan->asal }} - {{ $tiket->perjalanan->tujuan }} ({{ $tiket->perjalanan->jenis }})
                                @else
                                <span class="text-danger">Perjalanan Tidak Ditemukan</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($tiket->tanggal_berangkat)->format('d M Y') }}</td>
                            <td>{{ $tiket->tanggal_pulang ? \Carbon\Carbon::parse($tiket->tanggal_pulang)->format('d M Y') : '-' }}</td>
                            <td>{{ $tiket->penumpangs->count() }} penumpang👤</td>
                            <td>{{ $sisa_kapasitas }} kursi tersisa</td>
                            <td>
                                @if($tiket->status === 'aktif')
                                <span class="badge bg-warning text-dark">Aktif</span>
                                @elseif($tiket->status === 'berjalan')
                                <span class="badge bg-info text-dark">Berjalan</span>
                                @elseif($tiket->status === 'selesai')
                                <span class="badge bg-success">Selesai</span>
                                @endif
                            </td>
                            <td>
                                {{-- Tombol Edit --}}
                                @if($tiket->status === 'berjalan')
                                <button class="btn btn-secondary btn-sm" onclick="swalTidakBisaEdit()">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                @else
                                <a href="{{ route('karyawan.tiket.edit', $tiket->id) }}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                @endif

                                {{-- Tombol Hapus --}}
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#hapusModal{{ $tiket->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>

                                {{-- Tombol Cetak --}}
                                <a href="{{ route('karyawan.tiket.cetak', $tiket->id) }}" class="btn btn-info btn-sm">
                                    <i class="bi bi-printer"></i>
                                </a>

                                {{-- Tombol Mulai --}}
                                @if($tiket->status === 'aktif')
                                <form action="{{ route('karyawan.tiket.mulai', $tiket->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-primary btn-sm" onclick="return confirm('Mulai perjalanan ini?')">
                                        <i class="bi bi-play-circle"></i> Mulai
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>

                        {{-- Modal Hapus --}}
                        <div class="modal fade" id="hapusModal{{ $tiket->id }}" tabindex="-1" aria-labelledby="hapusModalLabel{{ $tiket->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('karyawan.tiket.destroy', $tiket->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Konfirmasi Hapus</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                        </div>
                                        <div class="modal-body">
                                            Apakah Anda yakin ingin menghapus tiket ini?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger">Hapus</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function swalTidakBisaEdit() {
        Swal.fire({
            icon: 'error',
            title: 'Tidak Bisa Mengedit!',
            text: 'Tiket sedang dalam perjalanan dan tidak dapat diedit.'
        });
    }
</script>
@endpush