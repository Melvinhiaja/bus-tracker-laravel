<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

<h3 class="mt-6 text-lg sm:text-xl font-semibold text-gray-800 dark:text-white text-center">Daftar Penumpang</h3>

<div class="overflow-x-auto mt-4 rounded shadow-md">
    <table class="min-w-full text-sm sm:text-base text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs sm:text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-800 dark:text-gray-400">
            <tr>
                <th class="px-4 py-3 whitespace-nowrap">No</th>
                <th class="px-4 py-3 whitespace-nowrap">Nama</th>
                <th class="px-4 py-3 whitespace-nowrap">NIK</th>
                <th class="px-4 py-3 whitespace-nowrap">Desa</th>
                <th class="px-4 py-3 whitespace-nowrap">From</th>
                <th class="px-4 py-3 whitespace-nowrap">To</th>
                <th class="px-4 py-3 whitespace-nowrap">Tgl Berangkat</th>
                <th class="px-4 py-3 whitespace-nowrap">Tgl Pulang</th>
                <th class="px-4 py-3 whitespace-nowrap">Alasan</th>
                <th class="px-4 py-3 whitespace-nowrap">Kostum</th>
                <th class="px-4 py-3 whitespace-nowrap">No Bus</th>
                <th class="px-4 py-3 whitespace-nowrap">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tiket->penumpangs as $index => $penumpang)
                @php
                    $statusSession = session('laporan_scan')["{$tiket->id}_{$penumpang->id}"] ?? '-';
                @endphp
                <tr class="border-b dark:border-gray-700">
                    <td class="px-4 py-3 font-medium text-gray-900 bg-gray-50 dark:text-white dark:bg-gray-800">{{ $index + 1 }}</td>
                    <td class="px-4 py-3 break-words">{{ $penumpang->nama }}</td>
                    <td class="px-4 py-3 break-words">{{ $penumpang->nik }}</td>
                    <td class="px-4 py-3">{{ $penumpang->kelurahan_desa }}</td>
                    <td class="px-4 py-3">{{ $tiket->perjalanan->asal }}</td>
                    <td class="px-4 py-3">{{ $tiket->perjalanan->tujuan }}</td>
                    <td class="px-4 py-3">{{ \Carbon\Carbon::parse($tiket->tanggal_berangkat)->format('d M Y') }}</td>
                    <td class="px-4 py-3">{{ \Carbon\Carbon::parse($tiket->tanggal_pulang)->format('d M Y') }}</td>
                    <td class="px-4 py-3">{{ $penumpang->alasan->nama ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $penumpang->alasan_kostum ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $tiket->bus->nomor_bus ?? 'Tidak Tersedia' }}</td>
                    <td class="px-4 py-3" id="status-{{ $penumpang->id }}">
                        @auth
                            <span class="text-green-600 font-bold">Naik</span>
                        @else
                            <span>-</span>
                        @endauth
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@auth
<div class="mt-6 text-center">
    <a href="{{ route('driver.show', ['id' => $tiket->id]) }}" class="bg-blue-600 hover:bg-blue-800 text-white px-6 py-2 rounded text-sm sm:text-base">
        Selesai
    </a>
</div>
@endauth
