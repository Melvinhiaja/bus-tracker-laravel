<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('tikets', function (Blueprint $table) {
            // Tambahkan kembali foreign key setelah tipe data diperbaiki
            $table->foreign('perjalanan_id')->references('id')->on('perjalanans')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('tikets', function (Blueprint $table) {
            $table->dropForeign(['perjalanan_id']);
        });
    }
};
