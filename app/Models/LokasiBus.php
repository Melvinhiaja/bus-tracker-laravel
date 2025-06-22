<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LokasiBus extends Model
{
    use HasFactory;

    protected $table = 'lokasi_bus';

    // Tentukan kolom mana yang dapat diisi
    protected $fillable = [
        'driver_id', 
        'latitude', 
        'longitude', 
        'destination_lat', 
        'destination_lng', 
        'status',
    ];
}
