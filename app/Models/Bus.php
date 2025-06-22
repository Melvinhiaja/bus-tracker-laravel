<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    use HasFactory;
    protected $fillable = [
        'nomor_bus', 'gambar', 'kapasitas', 'jumlah'
    ];
        // ✅ Tambahkan relasi ke Tiket
        public function tikets()
        {
            return $this->hasMany(Tiket::class, 'bus_id');
        }
}
