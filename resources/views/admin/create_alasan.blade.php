@extends('layouts.app')

@section('title', 'Kelola Alasan')

@section('content')
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-body">
            <h4 class="mb-3">Kelola Alasan</h4>

 

            <!-- Form Tambah Alasan -->
            <form action="{{ route('admin.alasan.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Alasan</label>
                    <input type="text" name="nama" id="nama" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>

            <!-- Daftar Alasan -->
            <table class="table table-bordered mt-4">
                <thead class="thead-dark">
                    <tr>
                        <th>No.</th>
                        <th>Nama Alasan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($alasans as $alasan)
                    <tr>
                    <td>{{ $loop->iteration }}</td> <!-- Menggunakan nomor urut -->
                    <td>{{ $alasan->nama }}</td>
                        <td>
                            <!-- Tombol Edit -->
                            <button class="btn btn-warning btn-edit" data-bs-toggle="modal" 
                                data-bs-target="#editModal" data-id="{{ $alasan->id }}" 
                                data-nama="{{ $alasan->nama }}">
                                Edit
                            </button>

                            <!-- Tombol Hapus -->
                            <form action="{{ route('admin.alasan.destroy', $alasan->id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger btn-delete">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Edit Alasan -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Alasan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit-id">
                    <div class="mb-3">
                        <label class="form-label">Nama Alasan:</label>
                        <input type="text" name="nama" id="edit-nama" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Event listener untuk tombol Edit
        document.querySelectorAll(".btn-edit").forEach(button => {
            button.addEventListener("click", function() {
                let id = this.getAttribute("data-id");
                let nama = this.getAttribute("data-nama");

                console.log("Edit clicked:", id, nama); // Debugging

                document.getElementById("edit-id").value = id;
                document.getElementById("edit-nama").value = nama;
                document.getElementById("editForm").action = `/alasan/${id}`;
            });
        });

        // Konfirmasi Hapus dengan SweetAlert
        document.querySelectorAll(".btn-delete").forEach(button => {
            button.addEventListener("click", function() {
                Swal.fire({
                    title: "Yakin ingin menghapus?",
                    text: "Data ini akan dihapus secara permanen!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Ya, hapus!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        button.closest("form").submit();
                    }
                });
            });
        });
    });
</script>
<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endsection
