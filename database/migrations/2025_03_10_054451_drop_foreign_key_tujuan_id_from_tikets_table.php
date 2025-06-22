<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        Schema::table('tikets', function (Blueprint $table) {
            // Hapus foreign key sebelum rename kolom
            DB::statement('ALTER TABLE tikets DROP FOREIGN KEY tikets_tujuan_id_foreign');
        });
    }

    public function down()
    {
        Schema::table('tikets', function (Blueprint $table) {
            // Tambahkan kembali foreign key jika rollback
            $table->foreign('tujuan_id')->references('id')->on('perjalanans')->onDelete('cascade');
        });
    }
};
