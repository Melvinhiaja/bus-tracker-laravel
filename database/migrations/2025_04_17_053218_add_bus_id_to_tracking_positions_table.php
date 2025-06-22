<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tracking_positions', function (Blueprint $table) {
            $table->unsignedBigInteger('bus_id')->nullable()->after('id');
            $table->foreign('bus_id')->references('id')->on('buses')->onDelete('set null');
        });
    }
    
    public function down()
    {
        Schema::table('tracking_positions', function (Blueprint $table) {
            $table->dropForeign(['bus_id']);
            $table->dropColumn('bus_id');
        });
    }
    
};
