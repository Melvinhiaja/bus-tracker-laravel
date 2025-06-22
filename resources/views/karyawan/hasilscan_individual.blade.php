<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Scan Tiket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">

<div class="container mt-4">
    <h3 class="text-center mb-4">Detail Penumpang</h3>

    <div class="card shadow">
        <div class="card-body">
            <p><strong>Nama:</strong> {{ $penumpang->nama }}</p>
            <p><strong>NIK:</strong> {{ $penumpang->nik }}</p>
            <p><strong>Desa:</strong> {{ $penumpang->kelurahan_desa }}</p>
            <p><strong>Jenis Kelamin:</strong> {{ $penumpang->jenis_kelamin }}</p>
            <p><strong>Tempat/Tgl Lahir:</strong> {{ $penumpang->tempat_tgl_lahir }}</p>
            <p><strong>Alasan:</strong> {{ $penumpang->alasan->nama ?? '-' }}</p>
            <p><strong>Kostum:</strong> {{ $penumpang->alasan_kostum ?? '-' }}</p>
            <hr>
            <p><strong>Bus:</strong> {{ $tiket->bus->nomor_bus ?? 'Tidak Tersedia' }}</p>
            <p><strong>Dari:</strong> {{ $tiket->perjalanan->asal }}</p>
            <p><strong>Ke:</strong> {{ $tiket->perjalanan->tujuan }}</p>
            <p><strong>Tanggal Berangkat:</strong> {{ \Carbon\Carbon::parse($tiket->tanggal_berangkat)->format('d M Y') }}</p>
            <p><strong>Tanggal Pulang:</strong> {{ \Carbon\Carbon::parse($tiket->tanggal_pulang)->format('d M Y') }}</p>
        </div>
    </div>

    @auth
    <form id="statusForm" action="{{ route('penumpang.aksi', ['id' => $penumpang->id]) }}" method="POST" class="mt-4">
        @csrf
        <input type="hidden" name="tiket_id" value="{{ $tiket->id }}">
        <input type="hidden" name="status" value="naik">
        <button type="submit" class="btn btn-success w-100 mb-3">Simpan Status</button>
    </form>

    <form id="selesaiForm" action="{{ route('driver.show', ['id' => $tiket->id]) }}" method="GET">
        <button type="submit" class="btn btn-primary w-100">Selesai</button>
    </form>
    @endauth
</div>

<script>
    @if(session('status_disimpan'))
    Swal.fire({
        icon: 'success',
        title: 'Status Disimpan',
        text: 'Penumpang Naik',
        showConfirmButton: false,
        timer: 1500
    });
    @endif
</script>

</body>
</html>
