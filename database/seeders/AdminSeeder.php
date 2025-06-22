<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Utama Bus',
            'username' => 'admintest',    // Username untuk login
            'password' => Hash::make('test123'), // Password terenkripsi
            'role' => 'admin',        // Role sebagai admin
            
        ]);
    }
}
