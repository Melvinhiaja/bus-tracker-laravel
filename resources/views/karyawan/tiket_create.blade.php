@extends('layouts.app')

@section('title', 'Pesan Tiket')

@section('content')


<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Pesan Tiket</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('karyawan.tiket.store') }}" method="POST">
                @csrf


                <!-- Pilih Desa -->
                <div class="mb-3">
                    <label class="form-label">Pilih Desa:</label>
                    <div id="desaCheckboxContainer" class="desa-checkbox-container">
                        @foreach ($desas as $desa)
                        <div class="form-check">
                            <input type="checkbox" name="desa_id[]" class="form-check-input desa-checkbox" value="{{ $desa->id }}" id="desa{{ $desa->id }}">
                            <label class="form-check-label" for="desa{{ $desa->id }}">{{ $desa->nama }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>


                <!-- Pilih Penumpang -->
                <div class="mb-3">
                    <label class="form-label">Pilih Penumpang:</label>
                    <div>
                        <input type="checkbox" id="selectAllPenumpang"> <label for="selectAllPenumpang">Pilih Semua</label>
                    </div>

                    <!-- Input Search -->
                    <div class="mb-2">
                        <input type="text" id="searchPenumpang" class="form-control" placeholder="Cari penumpang...">
                    </div>

                    <div class="scroll-container">
                        <div class="scroll-content" id="penumpangContainer">
                            @foreach ($penumpangs as $penumpang)
                            @php
                            // Cek apakah penumpang sudah memiliki tiket

                            $sudahTerdaftar = \App\Models\Penumpang::where('id', $penumpang->id)
                            ->whereHas('tiket', function($query) {
                            $query->whereDate('tanggal_berangkat', '>=', \Carbon\Carbon::today());
                            })
                            ->exists();
                            @endphp

                            <div class="penumpang-card 
                    {{ $sudahTerdaftar ? 'border-danger disabled-penumpang' : 'border-success' }}"
                                data-desa="{{ $penumpang->kelurahan_desa }}"
                                data-nama="{{ strtolower($penumpang->nama) }}"
                                data-nik="{{ $penumpang->nik }}"
                                title="{{ $sudahTerdaftar ? 'Penumpang ini sudah memiliki tiket' : '' }}">

                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <input type="checkbox" name="penumpang_id[]"
                                            class="form-check-input penumpang-checkbox"
                                            value="{{ $penumpang->id }}"
                                            {{ $sudahTerdaftar ? 'disabled' : '' }}>
                                        <img src="{{ asset('storage/' . $penumpang->foto_ktp) }}" class="penumpang-foto">
                                        <div class="penumpang-info">
                                            <p><strong>NAMA:</strong> {{ $penumpang->nama }}</p>
                                            <p><strong>NIK:</strong> {{ $penumpang->nik }}</p>
                                            <p><strong>TTL:</strong> {{ $penumpang->tempat_tgl_lahir }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>


                <!-- Pilih Bus -->
                <div class="mb-3">
                    <label class="form-label">Bus:</label>
                    <select name="bus_id" id="busSelect" class="form-select" required>
                        <option value="">Pilih Bus</option>
                        @foreach ($buses as $bus)
                        @php
                        // Ambil semua tiket yang masih aktif atau berjalan
                        $tiket_terpakai = \App\Models\Tiket::where('bus_id', $bus->id)
                        ->whereIn('status', ['aktif', 'berjalan']) // ✅ hanya status aktif & berjalan
                        ->whereDate('tanggal_berangkat', '>=', \Carbon\Carbon::today())
                        ->with('perjalanan')
                        ->withCount('penumpangs')
                        ->get();

                        $kapasitas_bus = $bus->kapasitas;
                        $jumlah_unit = $bus->jumlah;
                        $unit_terpakai = 0;
                        $ada_yang_berjalan = false;
                        @endphp

                        {{-- Cek semua tiket yang sudah terpakai pada bus ini --}}
                        @foreach ($tiket_terpakai as $tiket)
                        @php
                        $jumlah_penumpang = $tiket->penumpangs_count;
                        $sisa_kursi = max(0, $kapasitas_bus - $jumlah_penumpang);
                        $bus_text = "🚍 Bus: {$bus->nomor_bus} - Tersisa: {$sisa_kursi} Kursi - Tujuan: " .
                        optional($tiket->perjalanan)->asal . " → " . optional($tiket->perjalanan)->tujuan .
                        " - Tanggal: " . \Carbon\Carbon::parse($tiket->tanggal_berangkat)->format('d-m-Y');

                        if ($tiket->tanggal_pulang) {
                        $bus_text .= " → " . \Carbon\Carbon::parse($tiket->tanggal_pulang)->format('d-m-Y');
                        }

                        // Cek apakah tiket sedang berjalan
                        if ($tiket->status === 'berjalan') {
                        $ada_yang_berjalan = true;
                        }

                        // Hitung unit bus yang sedang terpakai
                        if ($jumlah_penumpang > 0) {
                        $unit_terpakai++;
                        }
                        @endphp

                        @if ($sisa_kursi > 0 && $tiket->status !== 'berjalan')
                        <option value="{{ $bus->id }}"
                            data-tujuan="{{ $tiket->perjalanan_id }}"
                            data-tanggal="{{ $tiket->tanggal_berangkat }}"
                            data-pulang="{{ $tiket->tanggal_pulang }}"
                            data-jenis="{{ optional($tiket->perjalanan)->jenis }}"
                            title="{{ $bus_text }}">
                            {{ $bus_text }}
                        </option>
                        @endif
                        @endforeach

                        {{-- Tampilkan opsi unit baru hanya jika tidak ada yang sedang berjalan --}}
                        @php
                        $sisa_unit = max(0, $jumlah_unit - $unit_terpakai);
                        @endphp

                        @if ($sisa_unit > 0 && !$ada_yang_berjalan)
                        <option value="{{ $bus->id }}"
                            data-tujuan=""
                            data-tanggal=""
                            data-pulang=""
                            data-jenis=""
                            title="🚍 Bus: {{ $bus->nomor_bus }} - Tersisa: {{ $kapasitas_bus }} Kursi (Unit Baru)">
                            🚍 Bus: {{ $bus->nomor_bus }} - Tersisa: {{ $kapasitas_bus }} Kursi
                        </option>
                        @endif
                        @endforeach
                    </select>
                </div>










                <!-- Tambah per penumpang -->
                <div id="alasanSection" style="display: none">
                    <label class="form-label">Alasan & Alasan Kostum untuk Penumpang:</label>
                    <div id="alasanPerPenumpang"></div>
                </div>



                <!-- Pilih Tujuan Perjalanan -->
                <div class="mb-3">
                    <label class="form-label">Perjalanan:</label>
                    <select name="perjalanan_id" id="perjalanan_id" class="form-select" required>
                        <option value="">Pilih Perjalanan</option>
                        @foreach ($perjalanans as $perjalanan)
                        <option value="{{ $perjalanan->id }}" data-jenis="{{ $perjalanan->jenis }}">
                            {{ $perjalanan->asal }} - {{ $perjalanan->tujuan }} ({{ ucfirst($perjalanan->jenis) }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tanggal Berangkat -->
                <div class="mb-3">
                    <label class="form-label">Tanggal Berangkat:</label>
                    <input type="date" name="tanggal_berangkat" id="tanggal_berangkat" class="form-control" required>
                </div>

                <!-- Tanggal Pulang -->
                <!-- Tanggal Pulang (Akan disembunyikan jika sekali jalan) -->
                <div class="mb-3" id="tanggalPulangDiv">
                    <label class="form-label">Tanggal Pulang:</label>
                    <input type="date" name="tanggal_pulang" id="tanggal_pulang" class="form-control">
                </div>

                <!-- Tombol Submit -->
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-ticket"></i> Pesan Tiket
                </button>
                <a href="{{ route('karyawan.tiket') }}" class="btn btn-danger">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>

            </form>
        </div>
    </div>
</div>

<!-- Modal untuk gambar yang diperbesar -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <img id="zoomedImage" src="" alt="Zoomed Image" class="img-fluid" />
            </div>
        </div>
    </div>
</div>

<style>
    .scroll-container {
        width: 100%;
        max-height: 300px;
        overflow-y: auto;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 10px;
    }

    .scroll-content {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 10px;
    }

    .penumpang-card {
        width: 100%;
        max-width: 200px;
    }

    .penumpang-card .card {
        width: 100%;
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        padding: 10px;
    }

    .penumpang-card img {
        border: 3px solid #ddd;
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
        cursor: pointer;
    }

    .penumpang-card input {
        display: block;
        margin: 0 auto 5px auto;
    }

    .penumpang-card .card-body {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: left;
    }

    .penumpang-foto {
        width: 100%;
        height: 100px;
        object-fit: cover;
        border: 3px solid #ddd;
        border-radius: 5px;
        cursor: pointer;
    }

    /* Untuk gambar yang tampil di modal, agar tidak terlalu besar */
    .modal-content img {
        max-width: 100%;
        max-height: 90vh;
        object-fit: contain;
    }

    .penumpang-info p {
        font-size: 0.75rem;
        /* Ukuran font lebih kecil */
    }

    /* Menggunakan Flexbox untuk menata checkbox desa secara horizontal */
    .desa-checkbox-container {
        display: flex;
        flex-wrap: wrap;
        /* Membuat elemen menyesuaikan baris baru jika ruang terbatas */
        gap: 15px;
        /* Memberikan jarak antar checkbox */
    }

    .form-check {
        margin: 0;
        /* Menghilangkan margin agar lebih rapat */
    }

    /* Jika penumpang sudah terdaftar, beri border merah */
    .border-danger {
        border: 3px solid red !important;
    }

    /* Jika penumpang masih bisa dipilih, beri border hijau */
    .border-success {
        border: 3px solid green !important;
    }

    /* Jika penumpang sudah terdaftar, buat transparan saat hover */
    .disabled-penumpang {
        opacity: 0.6;
        pointer-events: none;
    }

    /* Pastikan dropdown bisa menampilkan seluruh teks */
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const modal = new bootstrap.Modal(document.getElementById('imageModal'));
        const zoomedImage = document.getElementById("zoomedImage");
        const desaCheckboxes = document.querySelectorAll(".desa-checkbox");
        const penumpangCards = document.querySelectorAll(".penumpang-card");
        const penumpangContainer = document.getElementById("penumpangContainer");
        const selectAllPenumpang = document.getElementById("selectAllPenumpang");
        const penumpangCheckboxes = document.querySelectorAll(".penumpang-checkbox");
        const searchPenumpang = document.getElementById("searchPenumpang");
        const perjalananSelect = document.getElementById("perjalanan_id");
        const tanggalPulangDiv = document.getElementById("tanggalPulangDiv");
        const tanggalBerangkat = document.getElementById("tanggal_berangkat");
        const tanggalPulang = document.getElementById("tanggal_pulang");
        const busSelect = document.getElementById("busSelect");
        const alasanSection = document.getElementById("alasanSection");
        const alasanPerPenumpang = document.getElementById("alasanPerPenumpang");

        let isZoomed = false;

        function toggleImageSize(image) {
            if (isZoomed) {
                modal.hide();
                isZoomed = false;
            } else {
                zoomedImage.src = image.src;
                modal.show();
                isZoomed = true;
            }
        }

        document.querySelectorAll(".penumpang-foto").forEach(img => {
            img.addEventListener("click", function() {
                toggleImageSize(this);
            });
        });

        function filterPenumpang() {
            let selectedDesas = Array.from(desaCheckboxes)
                .filter(checkbox => checkbox.checked)
                .map(checkbox => checkbox.nextElementSibling.textContent.trim());

            penumpangContainer.innerHTML = "";

            if (selectedDesas.length === 0) {
                penumpangContainer.innerHTML = `
                <div class="alert alert-warning text-center">
                    <strong>Silakan pilih desa terlebih dahulu</strong>
                </div>`;
                return;
            }

            let hasPenumpang = false;

            penumpangCards.forEach(card => {
                let desa = card.dataset.desa;
                if (selectedDesas.includes(desa)) {
                    penumpangContainer.appendChild(card);
                    hasPenumpang = true;
                }
            });

            if (!hasPenumpang) {
                let desaNames = selectedDesas.join(", ");
                penumpangContainer.innerHTML = `
                <div class="alert alert-danger text-center">
                    <strong>Data penumpang pada desa ${desaNames} tidak tersedia.</strong><br>
                    Harap melakukan pendaftaran terlebih dahulu.<br>
                    <a href="{{ route('karyawan.storePenumpang') }}" class="btn btn-primary mt-2">
                        <i class="bi bi-person-plus"></i> Daftar Penumpang
                    </a>
                </div>`;
            }
        }

        function handleSelectAllPenumpang() {
            penumpangCheckboxes.forEach(cb => cb.checked = selectAllPenumpang.checked);
            renderAlasanForm();
        }

        function filterSearchPenumpang() {
            let searchTerm = searchPenumpang.value.toLowerCase();
            penumpangCards.forEach(card => {
                let nama = card.dataset.nama;
                let nik = card.dataset.nik;
                card.style.display = (nama.includes(searchTerm) || nik.includes(searchTerm)) ? "block" : "none";
            });
        }

        function toggleTanggalPulang() {
            let selected = perjalananSelect.options[perjalananSelect.selectedIndex];
            let jenis = selected?.dataset?.jenis;

            if (jenis === "sekali jalan") {
                tanggalPulangDiv.style.display = "none";
                tanggalPulang.value = "";
            } else {
                tanggalPulangDiv.style.display = "block";
            }
        }

        function setMinDate() {
            let today = new Date().toISOString().split("T")[0];
            tanggalBerangkat.setAttribute("min", today);
            tanggalPulang.setAttribute("min", today);
        }

        function renderAlasanForm() {
            const checkedPenumpangs = document.querySelectorAll(".penumpang-checkbox:checked");
            alasanPerPenumpang.innerHTML = "";
            if (checkedPenumpangs.length === 0) {
                alasanSection.style.display = "none";
                return;
            }

            alasanSection.style.display = "block";

            if (checkedPenumpangs.length === 1) {
                let id = checkedPenumpangs[0].value;
                alasanPerPenumpang.innerHTML = `
                <div class="mb-3">
                    <label>Alasan untuk Penumpang</label>
                    <select name="alasan_id[${id}]" class="form-select" required>
                        <option value="">Pilih Alasan</option>
                        @foreach($alasans as $alasan)
                        <option value="{{ $alasan->id }}">{{ $alasan->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label>Alasan Kostum</label>
                    <input type="text" name="alasan_kostum[${id}]" class="form-control">
                </div>`;
            } else {
                alasanPerPenumpang.innerHTML = `
                <div class="mb-3">
                    <label>Alasan (Untuk Semua Penumpang)</label>
                    <select name="alasan_id[all]" class="form-select" required>
                        <option value="">Pilih Alasan</option>
                        @foreach($alasans as $alasan)
                        <option value="{{ $alasan->id }}">{{ $alasan->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label>Alasan Kostum</label>
                    <input type="text" name="alasan_kostum[all]" class="form-control">
                </div>`;
            }
        }

        // ✅ Event bindings
        desaCheckboxes.forEach(cb => cb.addEventListener("change", filterPenumpang));
        penumpangCheckboxes.forEach(cb => cb.addEventListener("change", renderAlasanForm));
        selectAllPenumpang.addEventListener("change", handleSelectAllPenumpang);
        searchPenumpang.addEventListener("input", filterSearchPenumpang);
        perjalananSelect.addEventListener("change", toggleTanggalPulang);

        setMinDate();
        filterPenumpang();
        toggleTanggalPulang();

        // 🚍 Ketika Bus dipilih
        busSelect.addEventListener("change", function() {
            const selected = this.options[this.selectedIndex];
            const tujuanId = selected.getAttribute("data-tujuan");
            const tanggal = selected.getAttribute("data-tanggal");
            const tanggalPulangVal = selected.getAttribute("data-pulang");
            const jenis = selected.getAttribute("data-jenis");

            if (tujuanId) {
                perjalananSelect.value = tujuanId;
                perjalananSelect.setAttribute('readonly', true);
            } else {
                perjalananSelect.value = "";
                perjalananSelect.removeAttribute('readonly');
            }

            if (tanggal) {
                tanggalBerangkat.value = tanggal;
                tanggalBerangkat.setAttribute('readonly', true);
            } else {
                tanggalBerangkat.value = "";
                tanggalBerangkat.removeAttribute('readonly');
            }

            if (jenis === "sekali jalan") {
                tanggalPulangDiv.style.display = "none";
                tanggalPulang.value = "";
                tanggalPulang.removeAttribute('readonly');
            } else {
                tanggalPulangDiv.style.display = "block";
                if (tanggalPulangVal) {
                    tanggalPulang.value = tanggalPulangVal;
                    tanggalPulang.setAttribute('readonly', true);
                } else {
                    tanggalPulang.value = "";
                    tanggalPulang.removeAttribute('readonly');
                }
            }
        });
    });
</script>



@endsection