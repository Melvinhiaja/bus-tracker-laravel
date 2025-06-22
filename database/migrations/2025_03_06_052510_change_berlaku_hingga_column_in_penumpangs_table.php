<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('penumpangs', function (Blueprint $table) {
            $table->string('berlaku_hingga')->change(); // Ubah dari DATE ke STRING
        });
    }

    public function down() {
        Schema::table('penumpangs', function (Blueprint $table) {
            $table->date('berlaku_hingga')->change(); // Kembalikan ke DATE jika rollback
        });
    }
};
