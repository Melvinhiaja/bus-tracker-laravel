<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tracking_positions', function (Blueprint $table) {
            $table->id();
            $table->json('lokasi_awal');
            $table->json('lokasi_tujuan');
            $table->json('posisi_driver'); // selalu diupdate
            $table->json('rute')->nullable();
            $table->string('status')->default('Berjalan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_positions');
    }
};
