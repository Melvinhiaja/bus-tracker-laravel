<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('tikets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penumpang_id')->constrained('penumpangs')->onDelete('cascade');
            $table->foreignId('bus_id')->constrained('buses')->onDelete('cascade');
            $table->enum('alasan', ['berduka', 'ziarah', 'belajar', 'adat']);
            $table->string('alasan_kostum')->nullable();
            $table->date('tanggal_berangkat');
            $table->date('tanggal_pulang')->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('tikets');
    }
};