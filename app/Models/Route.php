<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $table = 'routes';

    // Tentukan kolom mana yang dapat diisi
    protected $fillable = [
        'driver_id',
        'start_lat',
        'start_lng',
        'start_name',
        'end_lat',
        'end_lng',
        'end_name',
        'distance',
        'duration',
        'status',
    ];
}
