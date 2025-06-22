<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Tiket</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100 dark:bg-gray-900">
<div class="max-w-sm bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
    <div class="p-5">
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Tiket Perjalanan</h5>
        <p class="mb-3 font-normal text-gray-700 dark:text-gray-400"><strong>Nama:</strong> {{ $penumpang->nama }}</p>
        <p class="mb-3 font-normal text-gray-700 dark:text-gray-400"><strong>NIK:</strong> {{ $penumpang->nik }}</p>
        <p class="mb-3 font-normal text-gray-700 dark:text-gray-400"><strong>Bus:</strong> {{ $tiket->bus->nomor_bus ?? 'Tidak Tersedia' }}</p>
        <p class="mb-3 font-normal text-gray-700 dark:text-gray-400"><strong>Perjalanan:</strong> {{ $tiket->perjalanan->asal }} → {{ $tiket->perjalanan->tujuan }}</p>
        <p class="mb-3 font-normal text-gray-700 dark:text-gray-400"><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($tiket->tanggal_berangkat)->format('d M Y') }}</p>
        
        <div class="flex justify-center">
            {!! $barcode !!}
        </div>
    </div>
</div>

</body>
</html>