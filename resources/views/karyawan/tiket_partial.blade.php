<div class="ticket text-center">
    <h4>BUS TICKET</h4>
    <p><strong>Nama:</strong> {{ $penumpang->nama }}</p>
    <p><strong>NIK:</strong> {{ $penumpang->nik }}</p>
    <p><strong>Bus:</strong> {{ $tiket->bus->nomor_bus ?? 'Tidak Tersedia' }}</p>
    <p><strong>Dari:</strong> {{ $tiket->perjalanan->asal }}</p>
    <p><strong>Ke:</strong> {{ $tiket->perjalanan->tujuan }}</p>
    <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($tiket->tanggal_berangkat)->format('d M Y') }}</p>

    <div class="barcode mt-3">
        {!! $barcode !!}
    </div>
</div>
