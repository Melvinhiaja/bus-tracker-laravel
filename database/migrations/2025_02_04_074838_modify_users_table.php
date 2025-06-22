<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus kolom yang tidak diperlukan
            $table->dropColumn(['email', 'email_verified_at', 'remember_token']);

            // Tambahkan kolom username
            $table->string('username')->unique()->after('name');

            // Pastikan kolom role ada
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'karyawan'])->default('karyawan')->after('password');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Rollback: tambahkan kembali kolom yang dihapus
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();

            // Hapus kolom yang baru ditambahkan
            $table->dropColumn(['username', 'role']);
        });
    }
};
