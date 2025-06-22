<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tikets', function (Blueprint $table) {
            $table->unsignedInteger('jumlah_penumpang')->default(0)->after('tanggal_pulang');
        });
    }

    public function down()
    {
        Schema::table('tikets', function (Blueprint $table) {
            $table->dropColumn('jumlah_penumpang');
        });
    }
};
