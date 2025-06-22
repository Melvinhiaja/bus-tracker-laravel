<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        Schema::table('tikets', function (Blueprint $table) {
            // Ubah perjalanan_id menjadi BIGINT UNSIGNED agar cocok dengan perjalanans.id
            $table->unsignedBigInteger('perjalanan_id')->change();
        });
    }

    public function down()
    {
        Schema::table('tikets', function (Blueprint $table) {
            // Kembalikan ke INT(11) jika rollback (hanya jika benar-benar dibutuhkan)
            $table->integer('perjalanan_id')->change();
        });
    }
};
