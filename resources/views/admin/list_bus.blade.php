@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4 text-center">Daftar Bus</h2>
    <!-- Tabel Daftar Bus -->
    <div class="table-responsive bg-light p-4 rounded shadow">
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-primary">
                <tr>
                    <th>No</th>
                    <th>Nomor Bus</th>
                    <th>Gambar</th>
                    <th>Kapasitas</th>
                    <th>Jumlah</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($buses as $bus)
                <tr>
                <td>{{ $loop->iteration }}</td> <!-- Menampilkan nomor urut -->
                <td>{{ $bus->nomor_bus }}</td>
                    <td class="text-center">
                        @if($bus->gambar)
                            <img src="{{ asset('storage/' . $bus->gambar) }}" alt="Bus Image" class="img-thumbnail" width="100">
                        @else
                            <span class="text-muted">Tidak ada gambar</span>
                        @endif
                    </td>
                    <td>{{ $bus->kapasitas }}</td>
                    <td>{{ $bus->jumlah }}</td>
                    <td class="text-center">
                        <a href="{{ route('admin.editBus', $bus->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('admin.deleteBus', $bus->id) }}" method="POST" class="d-inline" onsubmit="return confirmDelete(event, '{{ $bus->nomor_bus }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function confirmDelete(event, nomorBus) {
        event.preventDefault();
        Swal.fire({
            title: `Hapus Bus ${nomorBus}?`,
            text: "Data yang dihapus tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                event.target.submit();
            }
        });
    }
</script>

<style>
    .table-hover tbody tr:hover {
        background-color: #f1f1f1;
    }
    .btn-warning:hover {
        background-color: #e0a800;
        border-color: #d39e00;
    }
    .btn-danger:hover {
        background-color: #c82333;
        border-color: #bd2130;
    }
</style>

@endsection