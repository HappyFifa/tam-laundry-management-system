<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // 1. BUAT AKUN ADMIN OTOMATIS
        User::create([
            'name'     => 'Super Admin',
            'email'    => 'admin@tamlaundry.com',
            'password' => Hash::make('admin123'),
            'role'     => 'Admin',
        ]);

        // 2. BUAT AKUN KURIR OTOMATIS
        User::create([
            'name'     => 'Kurir Andi',
            'email'    => 'kurir@tamlaundry.com',
            'password' => Hash::make('kurir123'),
            'role'     => 'Kurir',
        ]);
    }
}