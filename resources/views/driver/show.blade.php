@extends('layouts.app')

@section('title', 'Detail Perjalanan')

@section('content')
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0">Detail Perjalanan Bus {{ $tiket->bus->nomor_bus ?? '-' }}</h4>
        </div>

        <div class="card-body">
            <p><strong>Rute:</strong> {{ $tiket->perjalanan->asal }} → {{ $tiket->perjalanan->tujuan }}</p>
            <p><strong>Tanggal Berangkat:</strong> {{ \Carbon\Carbon::parse($tiket->tanggal_berangkat)->format('d M Y') }}</p>
            <p><strong>Tanggal Pulang:</strong> {{ $tiket->tanggal_pulang ? \Carbon\Carbon::parse($tiket->tanggal_pulang)->format('d M Y') : '-' }}</p>
            <p><strong>Total Penumpang:</strong> {{ $tiket->penumpangs->count() }} orang</p>

            <hr>
            <h5 class="mb-3">Daftar Penumpang:</h5>

            @if($tiket->penumpangs->isEmpty())
                <div class="alert alert-warning">Tidak ada penumpang.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Desa</th>
                                <th>JK</th>
                                <th>NIK</th>
                                <th>TTL</th>
                                <th>Alasan</th>
                                <th>Kostum</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tiket->penumpangs as $index => $penumpang)
                            @php
                                $status = session('laporan_scan')["{$tiket->id}_{$penumpang->id}"] ?? '-';
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $penumpang->nama }}</td>
                                <td>{{ $penumpang->kelurahan_desa ?? '-' }}</td>
                                <td>{{ $penumpang->jenis_kelamin ?? '-' }}</td>
                                <td>{{ $penumpang->nik }}</td>
                                <td>{{ $penumpang->tempat_tgl_lahir }}</td>
                                <td>{{ $penumpang->alasan->nama ?? '-' }}</td>
                                <td>{{ $penumpang->alasan_kostum ?? '-' }}</td>
                                <td>
                                    @if($status === 'naik')
                                        <span class="text-success fw-bold">Naik</span>
                                    @elseif($status === 'tidak_naik')
                                        <span class="text-danger fw-bold">Tidak Naik</span>
                                    @else
                                        <span>-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="d-flex flex-wrap gap-2 mt-4">
                <a href="{{ route('driver.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>

                <form id="laporanForm" action="{{ route('karyawan.simpan_laporan') }}" method="POST" class="flex-grow-1">
                    @csrf
                    <input type="hidden" name="laporan" id="laporanDataInput">
                    <input type="hidden" name="tiket_id" value="{{ $tiket->id }}">
                    <button type="submit" class="btn btn-primary w-100">Simpan Laporan</button>
                </form>
            </div>
        </div>

        <div id="laporan-info"
             data-bus="{{ $tiket->bus->nomor_bus ?? '-' }}"
             data-asal="{{ $tiket->perjalanan->asal }}"
             data-tujuan="{{ $tiket->perjalanan->tujuan }}"
             data-tanggal="{{ $tiket->tanggal_berangkat }}">
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const hasilLaporan = [];
    const info = document.getElementById('laporan-info');
    const bus = info.dataset.bus;
    const asal = info.dataset.asal;
    const tujuan = info.dataset.tujuan;
    const tanggal = info.dataset.tanggal;

    const rows = document.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const cols = row.querySelectorAll('td');
        const data = {
            nama: cols[1].innerText.trim(),
            desa: cols[2].innerText.trim(),
            jenis_kelamin: cols[3].innerText.trim(),
            nik: cols[4].innerText.trim(),
            ttl: cols[5].innerText.trim(),
            alasan: cols[6].innerText.trim(),
            alasan_kostum: cols[7].innerText.trim(),
            status: cols[8].innerText.includes('Naik') ? 'Naik' : (cols[8].innerText.includes('Tidak Naik') ? 'Tidak Naik' : '-'),
            bus,
            asal,
            tujuan,
            tanggal_berangkat: tanggal
        };
        hasilLaporan.push(data);
    });

    document.getElementById('laporanDataInput').value = JSON.stringify(hasilLaporan);
});
</script>
@endsection
