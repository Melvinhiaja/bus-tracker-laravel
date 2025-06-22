<!-- Modal View Penumpang -->
<div class="modal fade" id="viewModal{{ $penumpang->id }}" tabindex="-1" aria-labelledby="viewModalLabel{{ $penumpang->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="viewModalLabel{{ $penumpang->id }}">Detail Penumpang</h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close" style="z-index: 1055;"></button>
            </div>

            <div class="modal-body">
                <div class="row gy-3">
                    <div class="col-12 col-md-6"><strong>NIK:</strong><br>{{ $penumpang->nik }}</div>
                    <div class="col-12 col-md-6"><strong>Nama:</strong><br>{{ $penumpang->nama }}</div>
                    <div class="col-12 col-md-6"><strong>Jenis Kelamin:</strong><br>{{ $penumpang->jenis_kelamin }}</div>
                    <div class="col-12 col-md-6"><strong>Tempat Tanggal Lahir:</strong><br>{{ $penumpang->tempat_tgl_lahir }}</div>
                    <div class="col-12 col-md-6"><strong>Alamat:</strong><br>{{ $penumpang->alamat }}</div>
                    <div class="col-12 col-md-6"><strong>RT/RW:</strong><br>{{ $penumpang->rt_rw }}</div>
                    <div class="col-12 col-md-6"><strong>Kelurahan/Desa:</strong><br>{{ $penumpang->kelurahan_desa }}</div>
                    <div class="col-12 col-md-6"><strong>Kecamatan:</strong><br>{{ $penumpang->kecamatan }}</div>
                    <div class="col-12 col-md-6"><strong>Agama:</strong><br>{{ $penumpang->agama }}</div>
                    <div class="col-12 col-md-6"><strong>Status Perkawinan:</strong><br>{{ $penumpang->status_perkawinan }}</div>
                    <div class="col-12 col-md-6"><strong>Pekerjaan:</strong><br>{{ $penumpang->pekerjaan }}</div>
                    <div class="col-12 col-md-6"><strong>Kewarganegaraan:</strong><br>{{ $penumpang->kewarganegaraan }}</div>
                    <div class="col-12 col-md-6"><strong>Berlaku Hingga:</strong><br>{{ $penumpang->berlaku_hingga }}</div>
                    <div class="col-12 col-md-6"><strong>Nomor HP:</strong><br>{{ $penumpang->nomor_hp }}</div>

                    <div class="col-12 mt-3">
                        <strong>Foto KTP:</strong>
                        <div class="mt-2 text-center">
                            @if($penumpang->foto_ktp)
                                <img src="{{ asset('storage/' . $penumpang->foto_ktp) }}" class="img-fluid rounded border" style="max-height: 250px;" alt="Foto KTP">
                            @else
                                <p class="text-muted">Tidak ada foto</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer d-flex justify-content-between flex-wrap">
                <small class="text-muted mb-2 mb-md-0">Klik "Tutup" untuk kembali</small>
                <button type="button" class="btn btn-secondary w-100 w-md-auto" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Responsive Modal Fix untuk iPhone dan layar kecil -->
<style>
@media (max-width: 480px) {
    .modal-dialog {
        margin: 0;
        max-width: 100%;
        height: 100%;
    }

    .modal-content {
        height: 100%;
        display: flex;
        flex-direction: column;
        border-radius: 0;
    }

    .modal-body {
        overflow-y: auto;
        flex-grow: 1;
        padding: 1rem;
    }

    .modal-footer {
        padding: 0.75rem;
    }

    .btn-close {
        z-index: 1055;
    }
}
</style>
