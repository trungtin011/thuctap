<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Employee;
use App\Models\Platform;
use App\Models\ExpenseType;
use App\Models\FinancialTarget;
use App\Models\FinancialRecord;
use App\Models\Expense;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Thêm phòng ban
        $departments = [
            ['name' => 'Marketing', 'description' => 'Phòng ban phụ trách marketing'],
            ['name' => 'Sales', 'description' => 'Phòng ban phụ trách bán hàng'],
        ];
        foreach ($departments as $dept) {
            Department::create($dept);
        }

        // 2. Thêm vai trò
        $roles = [
            ['name' => 'Nhân viên Marketing', 'level' => 'employee', 'department_id' => 1],
            ['name' => 'Quản lý Marketing', 'level' => 'manager', 'department_id' => 1],
            ['name' => 'Admin Marketing', 'level' => 'admin', 'department_id' => 1],
            ['name' => 'Nhân viên Sales', 'level' => 'employee', 'department_id' => 2],
            ['name' => 'Quản lý Sales', 'level' => 'manager', 'department_id' => 2],
            ['name' => 'Admin Sales', 'level' => 'admin', 'department_id' => 2],
        ];
        foreach ($roles as $role) {
            Role::create($role);
        }

        // 3. Thêm quyền
        $permissions = [
            ['name' => 'Xem bản ghi tài chính', 'code' => 'view_records'],
            ['name' => 'Chỉnh sửa bản ghi tài chính', 'code' => 'edit_records'],
            ['name' => 'Phê duyệt bản ghi tài chính', 'code' => 'approve_records'],
            ['name' => 'Xóa bản ghi tài chính', 'code' => 'delete_records'],
            ['name' => 'Quản lý phòng ban', 'code' => 'manage_departments'],
            ['name' => 'Quản lý vai trò', 'code' => 'manage_roles'],
        ];
        foreach ($permissions as $perm) {
            Permission::create($perm);
        }

        // 4. Liên kết quyền với vai trò qua bảng role_permission
        $rolePermissions = [
            // Nhân viên Marketing (role_id = 1)
            ['role_id' => 1, 'permission_id' => 1], // view_records
            // Quản lý Marketing (role_id = 2)
            ['role_id' => 2, 'permission_id' => 1], // view_records
            ['role_id' => 2, 'permission_id' => 2], // edit_records
            ['role_id' => 2, 'permission_id' => 3], // approve_records
            // Admin Marketing (role_id = 3)
            ['role_id' => 3, 'permission_id' => 1], // view_records
            ['role_id' => 3, 'permission_id' => 2], // edit_records
            ['role_id' => 3, 'permission_id' => 3], // approve_records
            ['role_id' => 3, 'permission_id' => 4], // delete_records
            ['role_id' => 3, 'permission_id' => 5], // manage_departments
            ['role_id' => 3, 'permission_id' => 6], // manage_roles
            // Nhân viên Sales (role_id = 4)
            ['role_id' => 4, 'permission_id' => 1], // view_records
            // Quản lý Sales (role_id = 5)
            ['role_id' => 5, 'permission_id' => 1], // view_records
            ['role_id' => 5, 'permission_id' => 2], // edit_records
            ['role_id' => 5, 'permission_id' => 3], // approve_records
            // Admin Sales (role_id = 6)
            ['role_id' => 6, 'permission_id' => 1], // view_records
            ['role_id' => 6, 'permission_id' => 2], // edit_records
            ['role_id' => 6, 'permission_id' => 3], // approve_records
            ['role_id' => 6, 'permission_id' => 4], // delete_records
            ['role_id' => 6, 'permission_id' => 5], // manage_departments
            ['role_id' => 6, 'permission_id' => 6], // manage_roles
        ];
        foreach ($rolePermissions as $rp) {
            DB::table('role_permission')->insert($rp);
        }

        // 5. Thêm nhân viên mẫu
        $employees = [
            [
                'name' => 'John Doe',
                'email' => 'khoaebanypk03641@gmail.com',
                'password' => Hash::make('123123123'),
                'department_id' => 1,
                'role_id' => 1, // Nhân viên Marketing
                'position' => 'Chuyên viên Marketing',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'ykhoa11a13@gmail.com',
                'password' => Hash::make('123123123'),
                'department_id' => 1,
                'role_id' => 2, // Quản lý Marketing
                'position' => 'Quản lý Marketing',
            ],
            [
                'name' => 'Admin User',
                'email' => 'dauxanh008@gmail.com',
                'password' => Hash::make('123123123'),
                'department_id' => 1,
                'role_id' => 3, // Admin Marketing
                'position' => 'Quản trị viên',
            ],
        ];
        foreach ($employees as $emp) {
            Employee::create($emp);
        }

        // 6. Thêm nền tảng
        $platforms = [
            ['name' => 'Google Ads'],
            ['name' => 'Facebook Ads'],
            ['name' => 'TikTok Ads'],
        ];
        foreach ($platforms as $platform) {
            Platform::create($platform);
        }

        // 7. Thêm loại chi phí
        $expenseTypes = [
            ['name' => 'Chi phí quảng cáo'],
            ['name' => 'Chi phí vận hành'],
            ['name' => 'Chi phí nhân sự'],
        ];
        foreach ($expenseTypes as $type) {
            ExpenseType::create($type);
        }
    }
}