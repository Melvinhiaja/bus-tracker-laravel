<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perjalanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'asal',
        'tujuan',
        'jenis',
        'lokasi_awal',
        'lokasi_tujuan',

    ];

    public function getNamaPerjalananAttribute()
    {
        return "{$this->asal}-{$this->tujuan} ({$this->jenis})";
    }
}
