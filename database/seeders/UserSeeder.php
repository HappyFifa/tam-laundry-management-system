<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin TAM Laundry',
                'email' => 'admin@tamlaundry.com',
                'no_wa' => '0811111111',
                'password' => Hash::make('123456'),
                'role' => 'admin',
            ],
            [
                'name' => 'Kurir Bayu',
                'email' => 'Kurir@tamlaundry.com',
                'no_wa' => '0822222222',
                'password' => Hash::make('123456'),
                'role' => 'kurir',
            ]
        ]);
    }
}