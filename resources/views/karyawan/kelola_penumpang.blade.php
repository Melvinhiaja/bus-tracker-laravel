@extends('layouts.app')

@section('title', 'Kelola Penumpang')

@section('content')
<div class="container">
    <h2 class="mb-4">Kelola Penumpang</h2>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Input Pencarian -->
    <div class="mb-3 d-flex flex-column flex-md-row gap-2 align-items-start">
        <input type="text" id="searchInput" class="form-control" placeholder="Cari penumpang (nama, NIK, desa...)">
        <button type="button" class="btn btn-outline-secondary" onclick="resetSearch()">Reset</button>
    </div>

    <!-- TABEL PENUMPANG TERDAFTAR -->
    <h3 class="mt-4">Penumpang Terdaftar</h3>
    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="tableTerdaftar">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>NIK</th>
                    <th>Jenis Kelamin</th>
                    <th>Tempat Tanggal Lahir</th>
                    <th>Desa</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penumpangTerdaftar as $index => $penumpang)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="searchable">{{ $penumpang->nama }}</td>
                    <td class="searchable">{{ $penumpang->nik }}</td>
                    <td class="searchable">{{ $penumpang->jenis_kelamin }}</td>
                    <td class="searchable">{{ $penumpang->tempat_tgl_lahir }}</td>
                    <td class="searchable">{{ $penumpang->kelurahan_desa }}</td>
                    <td>
                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal{{ $penumpang->id }}">View</button>
                        <a href="{{ route('penumpang.edit', $penumpang->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('penumpang.delete', $penumpang->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @include('karyawan.modal_penumpang', ['penumpang' => $penumpang])
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- TABEL PENUMPANG TIDAK TERDAFTAR -->
    <h3 class="mt-4">Penumpang Tidak Terdaftar</h3>
    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="tableTidakTerdaftar">
            <thead class="table-warning">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>NIK</th>
                    <th>Jenis Kelamin</th>
                    <th>Tempat Tanggal Lahir</th>
                    <th>Desa</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penumpangTidakTerdaftar as $index => $penumpang)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="searchable">{{ $penumpang->nama }}</td>
                    <td class="searchable">{{ $penumpang->nik }}</td>
                    <td class="searchable">{{ $penumpang->jenis_kelamin }}</td>
                    <td class="searchable">{{ $penumpang->tempat_tgl_lahir }}</td>
                    <td class="searchable">{{ $penumpang->kelurahan_desa }}</td>
                    <td>
                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal{{ $penumpang->id }}">View</button>
                        <a href="{{ route('penumpang.edit', $penumpang->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('penumpang.delete', $penumpang->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @include('karyawan.modal_penumpang', ['penumpang' => $penumpang])
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- SCRIPT PENCARIAN HIGHLIGHT -->
<script>
function resetSearch() {
    document.getElementById('searchInput').value = '';
    document.querySelectorAll('table tbody tr').forEach(row => {
        row.style.display = '';
        row.querySelectorAll('.searchable').forEach(cell => {
            cell.innerHTML = cell.textContent;
        });
    });
}

document.getElementById('searchInput').addEventListener('keyup', function () {
    const keyword = this.value.toLowerCase();
    document.querySelectorAll('table tbody tr').forEach(row => {
        let match = false;
        row.querySelectorAll('.searchable').forEach(cell => {
            const originalText = cell.textContent;
            const lowerText = originalText.toLowerCase();
            if (keyword && lowerText.includes(keyword)) {
                match = true;
                const regex = new RegExp(`(${keyword})`, 'gi');
                const highlighted = originalText.replace(regex, '<span class="highlight-text">$1</span>');
                cell.innerHTML = highlighted;
            } else {
                cell.innerHTML = originalText;
            }
        });
        row.style.display = match || keyword === '' ? '' : 'none';
    });
});
</script>

<!-- CSS UNTUK TEKS DITEKANKAN -->
<style>
.highlight-text {
    background-color: yellow;
    border: 2px solid blueviolet;
    border-radius: 6px;
    padding: 0 4px;
    font-weight: bold;
}
</style>

<!-- Tambahkan Bootstrap jika belum -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection
