<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penumpang extends Model
{
    use HasFactory;


    protected $fillable = [
        'nik', 'nama', 'tempat_tgl_lahir', 'jenis_kelamin', 'alamat',
        'rt_rw', 'kelurahan_desa', 'kecamatan', 'agama', 'status_perkawinan',
        'pekerjaan', 'kewarganegaraan', 'berlaku_hingga', 'foto_ktp','tiket_id',
    ];
    /**
     * Scope untuk mengambil penumpang yang desanya terdaftar di tabel desas.
     */
    public function scopeTerdaftar($query)
    {
        $desaTerdaftar = Desa::pluck('nama')->toArray();
        return $query->whereIn('kelurahan_desa', $desaTerdaftar);
    }

    /**
     * Scope untuk mengambil penumpang yang desanya tidak terdaftar di tabel desas.
     */
    public function scopeTidakTerdaftar($query)
    {
        $desaTerdaftar = Desa::pluck('nama')->toArray();
        return $query->whereNotIn('kelurahan_desa', $desaTerdaftar);
    }
    public function tiket()
{
    return $this->belongsTo(Tiket::class, 'tiket_id');
}
public function alasan()
{
    return $this->belongsTo(Alasan::class, 'alasan_id');
}



}
