<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTrackingPositionsToJson extends Migration
{
    public function up()
    {
        Schema::table('tracking_positions', function (Blueprint $table) {
            $table->json('lokasi_awal')->change();
            $table->json('lokasi_tujuan')->change();
            $table->json('rute')->nullable()->change();
            $table->json('posisi_driver')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('tracking_positions', function (Blueprint $table) {
            $table->longText('lokasi_awal')->change();
            $table->longText('lokasi_tujuan')->change();
            $table->longText('rute')->nullable()->change();
            $table->longText('posisi_driver')->nullable()->change();
        });
    }
}
