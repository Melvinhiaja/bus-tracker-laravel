@extends('layouts.app')

@section('title', 'Edit Tiket')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-white">
            <h4 class="mb-0">Edit Tiket</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('karyawan.tiket.update', $tiket->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Bus (Tidak Bisa Diubah) --}}
                <div class="mb-3">
                    <label class="form-label">Bus</label>
                    <input type="text" class="form-control" value=" {{ $tiket->bus->nomor_bus }} (Kapasitas: {{ $tiket->bus->kapasitas }})" disabled>
                    <input type="hidden" name="bus_id" value="{{ $tiket->bus_id }}">
                </div>

                {{-- Tujuan (Tidak Bisa Diubah) --}}
                <div class="mb-3">
                    <label class="form-label">Tujuan</label>
                    <input type="text" class="form-control" value="{{ $tiket->perjalanan->asal }} - {{ $tiket->perjalanan->tujuan }} ({{ $tiket->perjalanan->jenis }})" disabled>
                    <input type="hidden" name="perjalanan_id" value="{{ $tiket->perjalanan_id }}">
                </div>

                {{-- Tanggal Berangkat (Tidak Bisa Diubah) --}}
                <div class="mb-3">
                    <label class="form-label">Tanggal Berangkat</label>
                    <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($tiket->tanggal_berangkat)->format('d M Y') }}" disabled>
                    <input type="hidden" name="tanggal_berangkat" value="{{ $tiket->tanggal_berangkat }}">
                </div>

                {{-- Tanggal Pulang (Tidak Bisa Diubah) --}}
                <div class="mb-3">
                    <label class="form-label">Tanggal Pulang</label>
                    <input type="text" class="form-control" value="{{ $tiket->tanggal_pulang ? \Carbon\Carbon::parse($tiket->tanggal_pulang)->format('d M Y') : '-' }}" disabled>
                    <input type="hidden" name="tanggal_pulang" value="{{ $tiket->tanggal_pulang }}">
                </div>

                {{-- Daftar Penumpang yang Terdaftar --}}
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <label class="form-label">Penumpang Terdaftar:</label>
                        <input type="text" id="searchPenumpang" class="form-control w-25" placeholder="‍‍ Cari Penumpang...">
                    </div>
                    <div class="table-responsive" style="max-height: 250px; overflow-y: auto; border: 1px solid #ddd;">
                        <table class="table table-bordered" id="penumpangTable">
                        <thead class="table-dark">
    <tr>
        <th>No</th>
        <th>Nama</th>
        <th>NIK</th>
        <th>Desa</th>
        <th>Jenis Kelamin</th>
        <th>Tempat/Tgl Lahir</th>
        <th>Alasan</th>
        <th>Alasan Kostum</th>
        <th>Aksi</th>
    </tr>
</thead>

                            <tbody>
                                @forelse($tiket->penumpangs as $index => $penumpang)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="searchable">{{ $penumpang->nama }}</td>
                                    <td class="searchable">{{ $penumpang->nik }}</td>
                                    <td class="searchable">{{ $penumpang->kelurahan_desa }}</td>
                                    <td class="searchable">{{ $penumpang->jenis_kelamin }}</td>
                                    <td class="searchable">{{ $penumpang->tempat_tgl_lahir }}</td>
                                    <td class="searchable">
    {{ $penumpang->alasan->nama ?? '-' }}
</td>
<td class="searchable">
    {{ $penumpang->alasan_kostum ?? '-' }}
</td>

<td>

                                        {{-- Tombol Hapus --}}
                                        <button class="btn btn-danger btn-sm hapus-penumpang" data-tiket="{{ $tiket->id }}" data-id="{{ $penumpang->id }}">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-danger">Tidak ada penumpang terdaftar.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Tombol Submit --}}
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan Perubahan
                </button>

                <a href="{{ route('karyawan.tiket') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </form>
        </div>
    </div>
</div>

{{-- JavaScript untuk Pencarian dan Penghapusan --}}
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.getElementById("searchPenumpang");
        const rows = document.querySelectorAll("#penumpangTable tbody tr");

        searchInput.addEventListener("input", function () {
            const searchTerm = this.value.toLowerCase();

            rows.forEach(row => {
                let found = false;
                row.querySelectorAll(".searchable").forEach(cell => {
                    let text = cell.textContent.toLowerCase();
                    let originalText = cell.textContent;

                    if (text.includes(searchTerm)) {
                        found = true;
                        let regex = new RegExp(`(${searchTerm})`, "gi");
                        cell.innerHTML = originalText.replace(regex, `<span class="highlight">$1</span>`);
                    } else {
                        cell.innerHTML = originalText; 
                    }
                });

                row.style.display = found ? "" : "none";
            });
        });

        // Hapus Penumpang dari Tiket (AJAX)
        document.querySelectorAll('.hapus-penumpang').forEach(button => {
            button.addEventListener('click', function () {
                let penumpangId = this.getAttribute('data-id');
                let tiketId = this.getAttribute('data-tiket');

                if (confirm("Apakah Anda yakin ingin menghapus penumpang ini dari tiket?")) {
                    fetch(`/karyawan/tiket/${tiketId}/hapus-penumpang/${penumpangId}`, {
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            "Content-Type": "application/json"
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        alert(data.message);
                        location.reload();
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        alert("Terjadi kesalahan saat menghapus penumpang.");
                    });
                }
            });
        });
    });
</script>

{{-- CSS untuk Highlight --}}
<style>
    .highlight {
        background-color: yellow;
        font-weight: bold;
    }
</style>

@endsection