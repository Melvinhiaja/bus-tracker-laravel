@extends('layouts.app')

@section('title', 'Cetak Tiket')

@section('content')
<div class="container mt-4">
    <div class="row">
        {{-- Kolom Tiket --}}
        <div class="col-md-4 d-flex justify-content-center align-items-start">
            <div class="ticket shadow-lg p-4 bg-white rounded w-100" id="print-area">
                <h2 class="mb-3 text-center">BUS TICKET</h2>
                <div class="details d-grid gap-2">
                    <div class="p-2 bg-light rounded text-center">
                        <strong>BUS</strong><br>{{ $tiket->bus->nomor_bus ?? 'Tidak Tersedia' }}
                    </div>
                    <div class="p-2 bg-light rounded text-center">
                        <strong>DATE</strong><br>
                        {{ \Carbon\Carbon::parse($tiket->tanggal_berangkat)->format('d M Y') }} ➡️
                        {{ \Carbon\Carbon::parse($tiket->tanggal_pulang)->format('d M Y') }}
                    </div>
                    <div class="p-2 bg-light rounded text-center">
                        <strong>FROM</strong><br>{{ $tiket->perjalanan->asal }}
                    </div>
                    <div class="p-2 bg-light rounded text-center">
                        <strong>TO</strong><br>{{ $tiket->perjalanan->tujuan }}
                    </div>
                </div>
                <div class="barcode text-center mt-3">
                    {!! $barcode !!}
                </div>
                <div class="print-button text-center mt-3">
                    <button class="btn btn-primary w-100" onclick="printTiket()">Cetak Tiket Keseluruhan</button>
                </div>
            </div>
        </div>

        {{-- Kolom Daftar Penumpang --}}
        <div class="col-md-8" id="hide-on-print">
            <div class="card w-100 shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Penumpang Terdaftar</h5>
                </div>
                <div class="card-body">
                    @if($tiket->penumpangs->isEmpty())
                    <div class="alert alert-warning text-center">Tidak ada penumpang terdaftar.</div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>NIK</th>
                                    <th>Desa</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Tempat/Tgl Lahir</th>
                                    <th>Cetak Tiket</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tiket->penumpangs as $index => $penumpang)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $penumpang->nama }}</td>
                                    <td>{{ $penumpang->nik }}</td>
                                    <td>{{ $penumpang->kelurahan_desa }}</td>
                                    <td>{{ $penumpang->jenis_kelamin }}</td>
                                    <td>{{ $penumpang->tempat_tgl_lahir }}</td>
                                    <td>
                                        @php
                                        $tiketId = json_encode($tiket->id);
                                        $penumpangId = json_encode($penumpang->id);
                                        @endphp

                                        <button class="btn btn-success btn-sm"
                                            onclick="loadTiketPenumpang({{ $tiketId }}, {{ $penumpangId }})">
                                            Cetak
                                        </button>


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
    </div>
</div>

{{-- Modal Cetak Tiket --}}
<div class="modal fade" id="cetakTiketModal" tabindex="-1" aria-labelledby="cetakTiketModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cetakTiketModalLabel">Tiket Penumpang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="tiket-content">
                <!-- Tiket akan dimuat di sini -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="printTiketModal()">Cetak</button>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript --}}
<script>
    function printTiket() {
        var printContents = document.getElementById("print-area").innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        location.reload(); // Refresh halaman setelah print
    }

    function loadTiketPenumpang(tiket_id, penumpang_id) {
        fetch(`{{ url('/tiket/cetak') }}/${tiket_id}/${penumpang_id}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                document.getElementById("tiket-content").innerHTML = html;
                var cetakModal = new bootstrap.Modal(document.getElementById('cetakTiketModal'));
                cetakModal.show();
            })
            .catch(error => console.error("Error:", error));
    }

    function printTiketModal() {
        var printContents = document.getElementById("tiket-content").innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        location.reload();
    }
</script>
@endsection