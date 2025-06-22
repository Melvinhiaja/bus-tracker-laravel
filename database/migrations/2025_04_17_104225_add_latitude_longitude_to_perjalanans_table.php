<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perjalanans', function (Blueprint $table) {
            $table->longText('lokasi_awal')->nullable()->after('jenis');
            $table->longText('lokasi_tujuan')->nullable()->after('lokasi_awal');
        });
    }

    public function down(): void
    {
        Schema::table('perjalanans', function (Blueprint $table) {
            $table->dropColumn(['lokasi_awal', 'lokasi_tujuan']);
        });
    }
};