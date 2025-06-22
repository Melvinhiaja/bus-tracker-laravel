<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    public function up()
{
    Schema::create('lokasi_bus', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('driver_id'); // ID driver terkait
        $table->decimal('latitude', 10, 6);
        $table->decimal('longitude', 10, 6);
        $table->decimal('destination_lat', 10, 6)->nullable();
        $table->decimal('destination_lng', 10, 6)->nullable();
        $table->string('status', 20)->default('in_progress');
        $table->timestamps();
    });
    
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lokasi_bus');
    }
};
