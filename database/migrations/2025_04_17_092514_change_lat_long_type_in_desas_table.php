<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeLatLongTypeInDesasTable extends Migration
{
    public function up()
    {
        Schema::table('desas', function (Blueprint $table) {
            $table->longText('latitude')->nullable()->change();
            $table->longText('longitude')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('desas', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->change();
            $table->decimal('longitude', 10, 7)->nullable()->change();
        });
    }
}

