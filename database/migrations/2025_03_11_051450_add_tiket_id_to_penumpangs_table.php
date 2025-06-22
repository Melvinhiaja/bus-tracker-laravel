<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('penumpangs', function (Blueprint $table) {
            $table->foreignId('tiket_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('penumpangs', function (Blueprint $table) {
            $table->dropForeign(['tiket_id']);
            $table->dropColumn('tiket_id');
        });
    }
};
