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
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('driver_id')->nullable(); // ID driver yang terkait
            $table->decimal('start_lat', 10, 6);
            $table->decimal('start_lng', 10, 6);
            $table->string('start_name');
            $table->decimal('end_lat', 10, 6);
            $table->decimal('end_lng', 10, 6);
            $table->string('end_name');
            $table->decimal('distance', 8, 2)->nullable(); // dalam kilometer
            $table->decimal('duration', 8, 2)->nullable(); // dalam menit
            $table->string('status', 20)->default('planned');
            $table->timestamps();
        });        
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
