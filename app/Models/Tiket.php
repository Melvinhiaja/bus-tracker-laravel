<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tiket extends Model
{
    use HasFactory;

    protected $fillable = [
        'desa_id',
        'penumpang_id',
        'bus_id',
        'alasan_id',
        'alasan_kostum',
        'perjalanan_id', // ✅ Pastikan ini benar
        'tanggal_berangkat',
        'tanggal_pulang',
        'status',
        'jumlah_penumpang'
    ];

    // ✅ Pastikan relasi ini benar
    public function perjalanan()
    {
        return $this->belongsTo(Perjalanan::class, 'perjalanan_id');
    }

    public function penumpangs()
    {
        return $this->hasMany(Penumpang::class, 'tiket_id');
    }
    

    public function bus()
    {
        return $this->belongsTo(Bus::class, 'bus_id');
    }

    public function alasan()
    {
        return $this->belongsTo(Alasan::class, 'alasan_id');
    }


}
