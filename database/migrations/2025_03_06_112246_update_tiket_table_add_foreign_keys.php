<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up() {
        Schema::table('tikets', function (Blueprint $table) {
            // Tambah foreign key alasan_id
            $table->foreignId('alasan_id')->nullable()->constrained('alasans')->onDelete('cascade');
            
            // Tambah foreign key tujuan_id
            $table->foreignId('tujuan_id')->nullable()->constrained('perjalanans')->onDelete('cascade');
            
            // Hapus kolom alasan lama
            $table->dropColumn('alasan');
        });
    }

    public function down() {
        Schema::table('tikets', function (Blueprint $table) {
            // Tambah kembali kolom alasan lama (jika rollback)
            $table->enum('alasan', ['berduka', 'ziarah', 'belajar', 'adat']);
            
            // Hapus foreign key dan kolom baru
            $table->dropForeign(['alasan_id']);
            $table->dropColumn('alasan_id');
            $table->dropForeign(['tujuan_id']);
            $table->dropColumn('tujuan_id');
        });
    }
};
