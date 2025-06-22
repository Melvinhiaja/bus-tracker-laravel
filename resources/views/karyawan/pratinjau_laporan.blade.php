@extends('layouts.app')

@section('title', 'Pratinjau Laporan')

@section('content')
<div class="container mt-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(empty($laporan) || count($laporan) === 0)
        <div class="alert alert-info">Belum ada laporan yang disimpan.</div>
    @else
        @php
        $laporan = array_filter($laporan, fn($item) =>
            isset($item['bus'], $item['tanggal_berangkat'], $item['asal'], $item['tujuan'])
        );
        $grouped = collect($laporan)->groupBy(fn($item) =>
            ($item['bus'] ?? '-') . '|' .
            ($item['tanggal_berangkat'] ?? '-') . '|' .
            ($item['asal'] ?? '-') . '|' .
            ($item['tujuan'] ?? '-')
        );
        @endphp

        @foreach($grouped as $key => $laporanSet)
        @php [$bus, $tanggal, $asal, $tujuan] = explode('|', $key); @endphp

        <div class="card shadow mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Perjalanan Bus {{ $bus }}</h5>
            </div>
            <div class="card-body">
                <p><strong>Rute:</strong> {{ $asal }} → {{ $tujuan }}</p>
                <p><strong>Tanggal Berangkat:</strong> {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}</p>
                <p><strong>Total Penumpang:</strong> {{ count($laporanSet) }} orang</p>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-center">
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
                                <th>Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($laporanSet as $i => $lap)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $lap['nama'] }}</td>
                                <td>{{ $lap['desa'] }}</td>
                                <td>{{ $lap['jenis_kelamin'] }}</td>
                                <td>{{ $lap['nik'] }}</td>
                                <td>{{ $lap['ttl'] }}</td>
                                <td>{{ $lap['alasan'] }}</td>
                                <td>{{ $lap['alasan_kostum'] }}</td>
                                <td>
                                    @if($lap['status'] === 'Naik')
                                        <span class="text-success fw-bold">Naik</span>
                                    @elseif($lap['status'] === 'Tidak Naik')
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

                <div class="d-flex flex-wrap justify-content-between align-items-center mt-3 gap-2">
                    <form action="{{ route('karyawan.reset_laporan') }}" method="POST">
                        @csrf
                        <button class="btn btn-warning btn-sm" onclick="return confirm('Reset semua laporan?')">
                            <i class="bi bi-x-circle"></i> Reset Semua
                        </button>
                    </form>

                    <div class="d-flex flex-wrap gap-2">
                        <form action="{{ route('karyawan.cetak_pdf_per_card') }}" method="POST" target="_blank">
                            @csrf
                            <input type="hidden" name="data" value="{{ base64_encode(json_encode($laporanSet)) }}">
                            <button class="btn btn-secondary btn-sm">
                                <i class="bi bi-file-earmark-pdf"></i> PDF
                            </button>
                        </form>

                        <form action="{{ route('karyawan.export_excel_per_card') }}" method="POST">
                            @csrf
                            <input type="hidden" name="data" value="{{ base64_encode(json_encode($laporanSet)) }}">
                            <button class="btn btn-success btn-sm">
                                <i class="bi bi-file-earmark-excel"></i> Excel
                            </button>
                        </form>

                        <form action="{{ route('karyawan.export_word_per_card') }}" method="POST">
                            @csrf
                            <input type="hidden" name="data" value="{{ base64_encode(json_encode($laporanSet)) }}">
                            <button class="btn btn-primary btn-sm">
                                <i class="bi bi-file-earmark-word"></i> Word
                            </button>
                        </form>

                        <form action="{{ route('karyawan.hapus_laporan') }}" method="POST">
                            @csrf
                            <input type="hidden" name="key" value="{{ $key }}">
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Hapus laporan ini?')">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
        @endforeach
    @endif
</div>
@endsection
