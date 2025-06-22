<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('penumpangs', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->unique();
            $table->string('nama');
            $table->string('tempat_tgl_lahir');
            $table->string('jenis_kelamin');
            $table->text('alamat');
            $table->string('rt_rw');
            $table->string('kelurahan_desa');
            $table->string('kecamatan');
            $table->string('agama');
            $table->string('status_perkawinan');
            $table->string('pekerjaan');
            $table->string('kewarganegaraan');
            $table->date('berlaku_hingga');
            $table->string('foto_ktp')->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('penumpangs');
    }
};
