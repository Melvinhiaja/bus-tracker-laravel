@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="max-w-md mx-auto bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 p-6">
        <h2 class="mb-4 text-2xl font-bold text-gray-900 dark:text-white">Scan KTP</h2>

        <!-- Form Upload KTP -->
        <form id="scanKtpForm" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label for="foto_ktp" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unggah Foto KTP</label>
                <input type="file" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 dark:bg-gray-700 dark:border-gray-600 focus:outline-none" id="foto_ktp" name="foto_ktp" accept="image/*" required>
            </div>
            <button type="submit" class="w-full px-4 py-2 text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Scan KTP</button>
        </form>

        <!-- Loading Indicator -->
        <div id="loading" class="mt-4 text-center text-gray-600 dark:text-gray-300 hidden">
            <p>Memproses scan KTP... Mohon tunggu.</p>
        </div>

        <!-- Hasil Scan & Form Manual -->
        <div id="result" class="mt-6 hidden">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Hasil Scan</h3>
            <img id="ktpImage" src="" alt="KTP" class="w-full max-w-xs mx-auto mt-4 rounded-lg shadow-md">

            <h4 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">Isi Data KTP</h4>
            <form id="manualForm" class="mt-4 space-y-3">
                <input type="text" class="form-input" id="manual_nik" placeholder="NIK">
                <input type="text" class="form-input" id="manual_nama" placeholder="Nama">
                <input type="text" class="form-input" id="manual_tempat_tgl_lahir" placeholder="Tempat/Tgl Lahir">
                <input type="text" class="form-input" id="manual_jenis_kelamin" placeholder="Jenis Kelamin">
                <input type="text" class="form-input" id="manual_alamat" placeholder="Alamat">
                <input type="text" class="form-input" id="manual_rt_rw" placeholder="RT/RW">
                <input type="text" class="form-input" id="manual_kel_desa" placeholder="Kelurahan/Desa">
                <input type="text" class="form-input" id="manual_kecamatan" placeholder="Kecamatan">
                <input type="text" class="form-input" id="manual_agama" placeholder="Agama">
                <input type="text" class="form-input" id="manual_status_perkawinan" placeholder="Status Perkawinan">
                <input type="text" class="form-input" id="manual_pekerjaan" placeholder="Pekerjaan">
                <input type="text" class="form-input" id="manual_kewarganegaraan" placeholder="Kewarganegaraan">
                <input type="text" class="form-input" id="manual_berlaku_hingga" placeholder="Berlaku Hingga">

                <button type="submit" class="w-full px-4 py-2 text-white bg-green-600 rounded-lg hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300">Simpan Data</button>
            </form>
        </div>
    </div>
</div>

<script>
  document.getElementById('scanKtpForm').addEventListener('submit', function(event) {
    event.preventDefault();
    let formData = new FormData(this);
    
    document.getElementById('loading').style.display = 'block';
    document.getElementById('result').style.display = 'none';

    fetch("{{ secure_url('scan-ktp') }}", {
        method: "POST",
        body: formData,
        headers: {
            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error("Gagal memproses scan KTP. Kode: " + response.status);
        }
        return response.json();
    })
    .then(data => {
        document.getElementById('loading').style.display = 'none';

        if (data.error) {
            alert(data.error);
            return;
        }

        document.getElementById('ktpImage').src = data.image_path.replace('http://', 'https://');
        let parsed = data.parsed_data || {};

        document.getElementById('manual_nik').value = parsed.nik || "";
        document.getElementById('manual_nama').value = parsed.nama || "";
        document.getElementById('manual_tempat_tgl_lahir').value = parsed.tempat_tgl_lahir || "";
        document.getElementById('manual_jenis_kelamin').value = parsed.jenis_kelamin || "";
        document.getElementById('manual_alamat').value = parsed.alamat || "";
        document.getElementById('manual_rt_rw').value = parsed.rt_rw || "";
        document.getElementById('manual_kel_desa').value = parsed.kel_desa || "";
        document.getElementById('manual_kecamatan').value = parsed.kecamatan || "";
        document.getElementById('manual_agama').value = parsed.agama || "";
        document.getElementById('manual_status_perkawinan').value = parsed.status_perkawinan || "";
        document.getElementById('manual_pekerjaan').value = parsed.pekerjaan || "";
        document.getElementById('manual_kewarganegaraan').value = parsed.kewarganegaraan || "";
        document.getElementById('manual_berlaku_hingga').value = parsed.berlaku_hingga || "";

        document.getElementById('result').style.display = 'block';
    })
    .catch(error => {
        document.getElementById('loading').style.display = 'none';
        console.error("Error:", error);
        alert("Gagal memproses scan KTP.");
    });
});

// Simpan data manual
document.getElementById('manualForm').addEventListener('submit', function(event) {
    event.preventDefault();

    let formData = new FormData();
    formData.append('_token', document.querySelector('input[name="_token"]').value);
    formData.append('nik', document.getElementById('manual_nik').value);
    formData.append('nama', document.getElementById('manual_nama').value);
    formData.append('tempat_tgl_lahir', document.getElementById('manual_tempat_tgl_lahir').value);
    formData.append('jenis_kelamin', document.getElementById('manual_jenis_kelamin').value);
    formData.append('alamat', document.getElementById('manual_alamat').value);
    formData.append('rt_rw', document.getElementById('manual_rt_rw').value);
    formData.append('kelurahan_desa', document.getElementById('manual_kel_desa').value);
    formData.append('kecamatan', document.getElementById('manual_kecamatan').value);
    formData.append('agama', document.getElementById('manual_agama').value);
    formData.append('status_perkawinan', document.getElementById('manual_status_perkawinan').value);
    formData.append('pekerjaan', document.getElementById('manual_pekerjaan').value);
    formData.append('kewarganegaraan', document.getElementById('manual_kewarganegaraan').value);
    formData.append('berlaku_hingga', document.getElementById('manual_berlaku_hingga').value);

    let fileInput = document.getElementById('foto_ktp');
    if (fileInput.files.length > 0) {
        formData.append('foto_ktp', fileInput.files[0]);
    }

    fetch("{{ secure_url('karyawan/store-penumpang') }}", {
        method: "POST",
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert("Gagal menyimpan: " + data.error);
            return;
        }
        alert(data.message);
        location.reload();
    })
    .catch(error => {
        console.error("Error:", error);
        alert("Gagal menyimpan data penumpang.");
    });
});


</script>
<style>
.form-input {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ccc;
    border-radius: 0.375rem;
    background-color: #f9f9f9;
}
</style>
@endsection
