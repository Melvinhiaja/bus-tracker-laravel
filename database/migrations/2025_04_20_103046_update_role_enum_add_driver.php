<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Mengganti enum lama dengan enum baru menggunakan raw SQL
        DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'karyawan', 'driver') DEFAULT 'karyawan'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'karyawan') DEFAULT 'karyawan'");
    }
};

