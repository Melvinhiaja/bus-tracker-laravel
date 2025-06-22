<!DOCTYPE html>
<html>
<head>
    <title>Laporan Perjalanan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .judul { text-align: center; font-size: 16px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background-color: #eee; }
    </style>
</head>
<body>
    @php
        $first = $laporan[0];
    @endphp

    <div class="judul">Laporan Perjalanan Bus {{ $first['bus'] ?? '-' }}</div>
    <p><strong>Rute:</strong> {{ $first['asal'] ?? '-' }} ==> {{ $first['tujuan'] ?? '-' }}</p>
    <p><strong>Tanggal Berangkat:</strong> {{ \Carbon\Carbon::parse($first['tanggal_berangkat'] ?? now())->format('d M Y') }}</p>
    <p><strong>Total Penumpang:</strong> {{ count($laporan) }} orang</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Desa</th>
                <th>Jenis Kelamin</th>
                <th>NIK</th>
                <th>TTL</th>
                <th>Alasan</th>
                <th>Alasan Kostum</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporan as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $row['nama'] }}</td>
                <td>{{ $row['desa'] }}</td>
                <td>{{ $row['jenis_kelamin'] }}</td>
                <td>{{ $row['nik'] }}</td>
                <td>{{ $row['ttl'] }}</td>
                <td>{{ $row['alasan'] }}</td>
                <td>{{ $row['alasan_kostum'] }}</td>
                <td>{{ $row['status'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
