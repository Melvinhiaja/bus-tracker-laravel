<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('penumpangs', function (Blueprint $table) {
            $table->unsignedBigInteger('alasan_id')->nullable()->after('tiket_id');
            $table->string('alasan_kostum')->nullable()->after('alasan_id');
    
            $table->foreign('alasan_id')->references('id')->on('alasans')->onDelete('set null');
        });
    }
    
    public function down()
    {
        Schema::table('penumpangs', function (Blueprint $table) {
            $table->dropForeign(['alasan_id']);
            $table->dropColumn(['alasan_id', 'alasan_kostum']);
        });
    }
    
};
