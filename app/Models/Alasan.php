<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alasan extends Model
{
    use HasFactory;
    protected $fillable = ['nama'];
     // Relasi ke Tiket (Satu alasan bisa digunakan di banyak tiket)
     public function tikets()
     {
         return $this->hasMany(Tiket::class);
     }


}
