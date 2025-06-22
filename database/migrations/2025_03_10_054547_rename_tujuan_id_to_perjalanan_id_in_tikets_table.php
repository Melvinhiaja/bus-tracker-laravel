<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        Schema::table('tikets', function (Blueprint $table) {
            // Ubah nama kolom setelah foreign key dihapus
            DB::statement('ALTER TABLE tikets CHANGE COLUMN tujuan_id perjalanan_id INT NOT NULL');
        });
    }

    public function down()
    {
        Schema::table('tikets', function (Blueprint $table) {
            // Kembalikan nama jika rollback
            DB::statement('ALTER TABLE tikets CHANGE COLUMN perjalanan_id tujuan_id INT NOT NULL');
        });
    }
};
