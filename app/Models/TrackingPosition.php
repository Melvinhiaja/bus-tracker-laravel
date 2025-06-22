<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackingPosition extends Model
{
    protected $fillable = [
        'bus_id',
        'lokasi_awal',
        'lokasi_tujuan',
        'posisi_driver',
        'rute',
        'status',
        'waktu_tempuh',
        'jarak_tempuh',
        'rute_asal',
        'rute_tujuan',
        'user_id'
    ];

    protected $casts = [
        'lokasi_awal' => 'array',
        'lokasi_tujuan' => 'array',
        'posisi_driver' => 'array',
        'rute' => 'array',
    ];

// // app/Models/TrackingPosition.php
public function bus()
{
    return $this->belongsTo(\App\Models\Bus::class, 'bus_id');
}


}
