<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanPerCardExport implements FromArray, WithHeadings
{
    protected $laporan;

    public function __construct(array $laporan)
    {
        $this->laporan = $laporan;
    }

    public function array(): array
    {
        return array_map(function ($row) {
            return [
                $row['nama'],
                $row['desa'],
                $row['jenis_kelamin'],
                $row['nik'],
                $row['ttl'],
                $row['alasan'],
                $row['alasan_kostum'],
                $row['status']
            ];
        }, $this->laporan);
    }

    public function headings(): array
    {
        return [
            'Nama', 'Desa', 'Jenis Kelamin', 'NIK', 'TTL', 'Alasan', 'Alasan Kostum', 'Status'
        ];
    }
}
