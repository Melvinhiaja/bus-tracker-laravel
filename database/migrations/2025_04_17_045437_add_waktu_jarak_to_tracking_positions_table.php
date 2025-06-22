<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWaktuJarakToTrackingPositionsTable extends Migration
{
    public function up()
    {
        Schema::table('tracking_positions', function (Blueprint $table) {
            $table->string('waktu_tempuh')->nullable()->after('status'); // contoh: "12 menit"
            $table->string('jarak_tempuh')->nullable()->after('waktu_tempuh'); // contoh: "5.2 km"
        });
    }

    public function down()
    {
        Schema::table('tracking_positions', function (Blueprint $table) {
            $table->dropColumn('waktu_tempuh');
            $table->dropColumn('jarak_tempuh');
        });
    }
}
