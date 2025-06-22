@extends('layouts.app')

@section('title', 'Scan Tiket')

@section('content')
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
            <h4 class="mb-0">Scan Tiket</h4>
            <input type="file" id="qr-file" accept="image/*" class="form-control form-control-sm w-100 w-md-auto">
        </div>

        <div class="card-body text-center">
            <!-- Pilih Kamera + Tombol -->
            <div class="mb-3 d-flex flex-column flex-md-row justify-content-center align-items-center gap-2">
                <select id="cameraSelect" class="form-select w-100 w-md-auto"></select>
                <button id="startBtn" class="btn btn-success btn-sm w-100 w-md-auto">Mulai Kamera</button>
                <button id="stopBtn" class="btn btn-danger btn-sm w-100 w-md-auto d-none">Stop Kamera</button>
            </div>

            <!-- Kamera Scanner -->
            <div id="qr-reader" class="mx-auto" style="width: 100%; max-width: 400px; display: none;"></div>

            <!-- Hasil -->
            <div class="mt-4">
                <h5>Hasil Scan:</h5>
                <p id="qr-result" class="fw-bold text-success">Belum ada scan</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
    const resultEl = document.getElementById('qr-result');
    const cameraSelect = document.getElementById('cameraSelect');
    const fileInput = document.getElementById('qr-file');
    const startBtn = document.getElementById('startBtn');
    const stopBtn = document.getElementById('stopBtn');
    const qrReaderDiv = document.getElementById('qr-reader');

    const qrScanner = new Html5Qrcode("qr-reader");

    let selectedCameraId = null;
    let scannerRunning = false;

    Html5Qrcode.getCameras().then(devices => {
        if (devices.length === 0) {
            resultEl.innerText = "Tidak ada kamera terdeteksi.";
            return;
        }

        devices.forEach(device => {
            const option = document.createElement('option');
            option.value = device.id;
            option.text = device.label || `Kamera ${cameraSelect.length + 1}`;
            cameraSelect.appendChild(option);
        });

        selectedCameraId = devices[0].id;
    });

    cameraSelect.addEventListener('change', function () {
        selectedCameraId = this.value;
    });

    startBtn.addEventListener('click', function () {
        if (!selectedCameraId) {
            resultEl.innerText = "Kamera belum dipilih.";
            return;
        }

        qrReaderDiv.style.display = "block";
        startBtn.classList.add('d-none');
        stopBtn.classList.remove('d-none');

        qrScanner.start(
            { deviceId: { exact: selectedCameraId } },
            { fps: 10, qrbox: 250 },
            onScanSuccess
        ).then(() => {
            scannerRunning = true;
        }).catch(err => {
            resultEl.innerText = "Gagal membuka kamera.";
            console.error("Start error:", err);
        });
    });

    stopBtn.addEventListener('click', function () {
        qrScanner.stop().then(() => {
            qrReaderDiv.style.display = "none";
            startBtn.classList.remove('d-none');
            stopBtn.classList.add('d-none');
            scannerRunning = false;
        }).catch(err => {
            console.error("Stop error:", err);
        });
    });

    function onScanSuccess(decodedText) {
        resultEl.innerText = decodedText;

        // Jika hasil scan adalah URL valid, redirect ke sana
        if (decodedText.startsWith("http")) {
            window.location.href = decodedText;
        }

        if (scannerRunning) {
            qrScanner.stop().then(() => {
                qrReaderDiv.style.display = "none";
                startBtn.classList.remove('d-none');
                stopBtn.classList.add('d-none');
                scannerRunning = false;
            });
        }
    }

    // Upload file QR
    fileInput.addEventListener('change', function () {
        if (!fileInput.files.length) return;
        const file = fileInput.files[0];

        qrScanner.scanFile(file, true)
            .then(decodedText => {
                resultEl.innerText = decodedText;

                if (decodedText.startsWith("http")) {
                    window.location.href = decodedText;
                }
            })
            .catch(err => {
                resultEl.innerText = "QR tidak terbaca dari gambar.";
                console.error("Scan file error:", err);
            });
    });
</script>
@endpush
