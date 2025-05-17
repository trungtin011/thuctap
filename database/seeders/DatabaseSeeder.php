<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Seed departments
        Department::create(['name' => 'Kinh doanh', 'description' => 'Phòng kinh doanh']);
        Department::create(['name' => 'Dịch vụ', 'description' => 'Phòng dịch vụ']);

        // Seed roles
        Role::create([
            'name' => 'Nhân viên',
            'level' => 'employee',
            'department_id' => 1,
            'description' => 'Vai trò nhân viên bình thường'
        ]);
        Role::create([
            'name' => 'Quản lý',
            'level' => 'manager',
            'department_id' => 1,
            'description' => 'Vai trò quản lý phòng ban'
        ]);
        Role::create([
            'name' => 'Admin',
            'level' => 'admin',
            'department_id' => 1,
            'description' => 'Vai trò quản trị hệ thống'
        ]);
    }
}