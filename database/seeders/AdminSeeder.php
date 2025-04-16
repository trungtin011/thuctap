<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    public function run()
    {
        Admin::create([
            'username' => 'admin',
            'password' => Hash::make('Khoa15082005'),
            'role' => 'admin',
        ]);

        Admin::create([
            'username' => 'superadmin',
            'password' => Hash::make('Khoa15082005'),
            'role' => 'superadmin',
        ]);
    }
}
