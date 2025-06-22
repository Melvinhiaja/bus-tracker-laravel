<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyPosisiDriverNullableOnTrackingPositionsTable extends Migration
{
    public function up()
    {
        Schema::table('tracking_positions', function (Blueprint $table) {
            $table->json('posisi_driver')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('tracking_positions', function (Blueprint $table) {
            $table->json('posisi_driver')->nullable(false)->change();
        });
    }
}
