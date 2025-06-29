<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Employee;
use App\Models\Platform;
use App\Models\PlatformMetric;
use App\Models\ExpenseType;
use App\Models\DaiLy;
use App\Models\Route;
use App\Models\Office;
use App\Models\FinancialRecord;
use App\Models\Expense;
use App\Models\FinancialTarget;
use App\Models\OfficeRevenue;
use App\Models\TripsPassenger;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Điền dữ liệu cho departments (thực thi trước)
        $departments = [
            ['name' => 'Marketing', 'description' => 'Phòng ban phụ trách marketing', 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
            ['name' => 'Sales', 'description' => 'Phòng ban phụ trách bán hàng', 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
            ['name' => 'Kinh Doanh', 'description' => 'Phòng ban phụ trách kinh doanh', 'created_at' => '2025-06-29 21:55:00', 'updated_at' => '2025-06-29 21:55:00'],
            ['name' => 'Kế Toán', 'description' => 'Phòng ban phụ trách kế toán', 'created_at' => '2025-06-29 21:55:00', 'updated_at' => '2025-06-29 21:55:00'],
        ];
        foreach ($departments as $dept) {
            Department::firstOrCreate(['name' => $dept['name']], $dept);
        }

        // 2. Điền dữ liệu cho roles (thực thi sau departments)
        $roles = [
            ['name' => 'Nhân viên Marketing', 'level' => 'employee', 'department_id' => 1, 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
            ['name' => 'Quản lý Marketing', 'level' => 'manager', 'department_id' => 1, 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
            ['name' => 'Admin Marketing', 'level' => 'admin', 'department_id' => 1, 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
            ['name' => 'Nhân viên Sales', 'level' => 'employee', 'department_id' => 2, 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
            ['name' => 'Quản lý Sales', 'level' => 'manager', 'department_id' => 2, 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
            ['name' => 'Admin Sales', 'level' => 'admin', 'department_id' => 2, 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
            ['name' => 'Nhân viên Kinh Doanh', 'level' => 'employee', 'department_id' => 3, 'created_at' => '2025-06-29 21:55:00', 'updated_at' => '2025-06-29 21:55:00'],
            ['name' => 'Quản lý Kinh Doanh', 'level' => 'manager', 'department_id' => 3, 'created_at' => '2025-06-29 21:55:00', 'updated_at' => '2025-06-29 21:55:00'],
            ['name' => 'Admin Kinh Doanh', 'level' => 'admin', 'department_id' => 3, 'created_at' => '2025-06-29 21:55:00', 'updated_at' => '2025-06-29 21:55:00'],
            ['name' => 'Nhân viên Kế Toán', 'level' => 'employee', 'department_id' => 4, 'created_at' => '2025-06-29 21:55:00', 'updated_at' => '2025-06-29 21:55:00'],
            ['name' => 'Quản lý Kế Toán', 'level' => 'manager', 'department_id' => 4, 'created_at' => '2025-06-29 21:55:00', 'updated_at' => '2025-06-29 21:55:00'],
            ['name' => 'Admin Kế Toán', 'level' => 'admin', 'department_id' => 4, 'created_at' => '2025-06-29 21:55:00', 'updated_at' => '2025-06-29 21:55:00'],
        ];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }

        // 3. Điền dữ liệu cho permissions
        $permissions = [
            ['name' => 'Xem bản ghi tài chính', 'code' => 'view_records', 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
            ['name' => 'Chỉnh sửa bản ghi tài chính', 'code' => 'edit_records', 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
            ['name' => 'Phê duyệt bản ghi tài chính', 'code' => 'approve_records', 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
            ['name' => 'Xóa bản ghi tài chính', 'code' => 'delete_records', 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
            ['name' => 'Quản lý phòng ban', 'code' => 'manage_departments', 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
            ['name' => 'Quản lý vai trò', 'code' => 'manage_roles', 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
        ];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['code' => $perm['code']], $perm);
        }

        // 4. Điền dữ liệu cho role_permission
        $rolePermissions = [
            ['role_id' => 1, 'permission_id' => 1],
            ['role_id' => 2, 'permission_id' => 1],
            ['role_id' => 2, 'permission_id' => 2],
            ['role_id' => 2, 'permission_id' => 3],
        ];
        foreach ($rolePermissions as $rp) {
            DB::table('role_permission')->updateOrInsert(
                ['role_id' => $rp['role_id'], 'permission_id' => $rp['permission_id']],
                $rp
            );
        }

        // 5. Điền dữ liệu cho employees
        $employees = [
            [
                'department_id' => 1,
                'role_id' => 1,
                'name' => 'John Doe',
                'position' => 'Chuyên viên Marketing',
                'email' => 'john.doe@example.com',
                'password' => Hash::make('password'),
                'created_at' => '2025-05-23 19:14:38',
                'updated_at' => '2025-05-23 19:14:38',
            ],
            [
                'department_id' => 1,
                'role_id' => 2,
                'name' => 'Jane Smith',
                'position' => 'Quản lý Marketing',
                'email' => 'jane.smith@example.com',
                'password' => Hash::make('password'),
                'created_at' => '2025-05-23 19:14:38',
                'updated_at' => '2025-05-23 19:14:38',
            ],
            [
                'department_id' => 1,
                'role_id' => 3,
                'name' => 'Admin User',
                'position' => 'Quản trị viên',
                'email' => 'admin.user@example.com',
                'password' => Hash::make('password'),
                'created_at' => '2025-05-23 19:14:38',
                'updated_at' => '2025-05-23 19:14:38',
            ],
            [
                'department_id' => 3, // Kinh Doanh
                'role_id' => 7, // Nhân viên Kinh Doanh (id 7 sau 6 role cũ)
                'name' => 'Nguyen Van A',
                'position' => 'Nhân viên Kinh Doanh',
                'email' => 'kinhdoanh@gmail.com',
                'password' => Hash::make('123123123'),
                'created_at' => '2025-06-29 21:59:00',
                'updated_at' => '2025-06-29 21:59:00',
            ],
            [
                'department_id' => 4, // Kế Toán
                'role_id' => 10, // Nhân viên Kế Toán (id 10 sau 9 role)
                'name' => 'Tran Thi B',
                'position' => 'Nhân viên Kế Toán',
                'email' => 'ketoan@gmail.com',
                'password' => Hash::make('123123123'),
                'created_at' => '2025-06-29 21:59:00',
                'updated_at' => '2025-06-29 21:59:00',
            ],
        ];
        foreach ($employees as $emp) {
            Employee::firstOrCreate(['email' => $emp['email']], $emp);
        }

        // 6. Điền dữ liệu cho platforms
        $platforms = [
            ['name' => 'Google Ads', 'foco_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'], // Sửa 'foco_at' thành 'created_at'
            ['name' => 'Facebook Ads', 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
            ['name' => 'TikTok Ads', 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
            ['name' => 'QR', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['name' => 'WEB/APP', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['name' => 'Các văn phòng', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['name' => 'Vé thương gia', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
        ];
        foreach ($platforms as $platform) {
            Platform::firstOrCreate(['name' => $platform['name']], $platform);
        }

        // 7. Điền dữ liệu cho platform_metrics
        $platformMetrics = [
            ['platform_id' => 2, 'name' => 'Lượt Click', 'unit' => 'Lượt', 'data_type' => 'int', 'created_at' => '2025-05-23 19:16:23', 'updated_at' => '2025-05-23 19:16:23'],
        ];
        foreach ($platformMetrics as $metric) {
            PlatformMetric::firstOrCreate(['name' => $metric['name'], 'platform_id' => $metric['platform_id']], $metric);
        }

        // 8. Điền dữ liệu cho expense_types
        $expenseTypes = [
            ['name' => 'Chi phí quảng cáo', 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
            ['name' => 'Chi phí vận hành', 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
            ['name' => 'Chi phí nhân sự', 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
        ];
        foreach ($expenseTypes as $type) {
            ExpenseType::firstOrCreate(['name' => $type['name']], $type);
        }

        // 9. Điền dữ liệu cho dai_lies
        $daiLies = [
            ['ten_dai_ly' => 'Adam', 'email' => 'adam@example.com', 'so_dien_thoai' => '0123456789', 'dia_chi' => 'HCM', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['ten_dai_ly' => 'Vé xe rẻ', 'email' => 'vexe@example.com', 'so_dien_thoai' => '0123456789', 'dia_chi' => 'HCM', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['ten_dai_ly' => 'Redbus', 'email' => 'redbus@example.com', 'so_dien_thoai' => '0123456789', 'dia_chi' => 'HCM', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['ten_dai_ly' => 'Mobitrip', 'email' => 'mobitrip@example.com', 'so_dien_thoai' => '0123456789', 'dia_chi' => 'HCM', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['ten_dai_ly' => 'WEB/APP', 'email' => 'webapp@example.com', 'so_dien_thoai' => '0123456789', 'dia_chi' => 'HCM', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['ten_dai_ly' => 'QR', 'email' => 'qr@example.com', 'so_dien_thoai' => '0123456789', 'dia_chi' => 'HCM', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['ten_dai_ly' => 'Các văn phòng', 'email' => 'offices@example.com', 'so_dien_thoai' => '0123456789', 'dia_chi' => 'HCM', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['ten_dai_ly' => 'Vé thương gia', 'email' => 'thuonggia@example.com', 'so_dien_thoai' => '0123456789', 'dia_chi' => 'HCM', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['ten_dai_ly' => 'Distribusion', 'email' => 'distribusion@example.com', 'so_dien_thoai' => '0123456789', 'dia_chi' => 'HCM', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
        ];
        foreach ($daiLies as $daiLy) {
            DaiLy::firstOrCreate(['email' => $daiLy['email']], $daiLy);
        }

        // 10. Điền dữ liệu cho routes
        $routes = [
            ['name' => 'Sài Gòn - Buôn Ma Thuột', 'department_id' => 1, 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['name' => 'Buôn Ma Thuột - Sài Gòn', 'department_id' => 1, 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['name' => 'Sài Gòn - Đà Lạt', 'department_id' => 1, 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['name' => 'Đà Lạt - Sài Gòn', 'department_id' => 1, 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['name' => 'Sài Gòn - Nha Trang', 'department_id' => 1, 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['name' => 'Nha Trang - Sài Gòn', 'department_id' => 1, 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['name' => 'Sài Gòn - Mũi Né', 'department_id' => 1, 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['name' => 'Mũi Né - Sài Gòn', 'department_id' => 1, 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['name' => 'Buôn Ma Thuột - Đà Lạt', 'department_id' => 1, 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['name' => 'Đà Lạt - Buôn Ma Thuột', 'department_id' => 1, 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['name' => 'Xe hợp đồng', 'department_id' => 1, 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
        ];
        foreach ($routes as $route) {
            Route::firstOrCreate(['name' => $route['name']], $route);
        }

        // 11. Điền dữ liệu cho offices
        $offices = [
            ['name' => 'VP 49', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['name' => 'VP BT', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['name' => 'VP CMG', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['name' => 'VP Q5', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['name' => 'VP ĐL', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['name' => 'VP NT', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
        ];
        foreach ($offices as $office) {
            Office::firstOrCreate(['name' => $office['name']], $office);
        }

        // 12. Điền dữ liệu cho financial_records
        $financialRecords = [
            [
                'department_id' => 1,
                'platform_id' => 1,
                'dai_ly_id' => 1,
                'office_id' => 1,
                'route_id' => null,
                'revenue' => 1101465000.00,
                'commission' => 121264230.00,
                'roas' => 61.37,
                'record_date' => '2025-01-31',
                'record_time' => '23:59:00',
                'note' => 'Doanh thu Adam T1/2025',
                'status' => 'pending',
                'submitted_by' => 1,
                'created_at' => '2025-05-25 17:04:00',
                'updated_at' => '2025-05-25 17:04:00',
            ],
        ];
        foreach ($financialRecords as $record) {
            FinancialRecord::create($record);
        }

        // 13. Điền dữ liệu cho expenses
        $expenses = [
            [
                'financial_record_id' => 1,
                'expense_type_id' => 3,
                'amount' => 17949000.00,
                'description' => 'Chi phí VP 49 T1/2025',
                'status' => 'pending',
                'reject_reason' => null,
                'created_at' => '2025-05-25 17:04:00',
                'updated_at' => '2025-05-25 17:04:00',
            ],
        ];
        foreach ($expenses as $expense) {
            Expense::create($expense);
        }

        // 14. Điền dữ liệu cho financial_targets
        $financialTargets = [
            [
                'year' => 2025,
                'department_id' => 1,
                'target_amount' => 35017977699.00,
                'created_at' => '2025-05-25 17:04:00',
                'updated_at' => '2025-05-25 17:04:00',
            ],
        ];
        foreach ($financialTargets as $target) {
            FinancialTarget::firstOrCreate(['year' => $target['year'], 'department_id' => $target['department_id']], $target);
        }

        // 15. Điền dữ liệu cho office_revenues
        $officeRevenues = [
            [
                'department_id' => null,
                'submitted_by' => null,
                'cash' => 155876000.00,
                'bank_transfer' => 131143000.00,
                'expense' => 17949000.00,
                'total' => 378786000.00,
                'status' => 'pending',
                'reject_reason' => null,
                'record_date' => '2025-01-31',
                'created_at' => '2025-05-25 17:04:00',
                'updated_at' => '2025-05-25 17:04:00',
            ],
        ];
        foreach ($officeRevenues as $revenue) {
            OfficeRevenue::create($revenue);
        }

        // 16. Điền dữ liệu cho trips_passengers
        $tripsPassengers = [
            ['route_id' => 1, 'trips' => 460, 'passengers' => 8573, 'record_date' => '2025-01-31', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
        ];
        foreach ($tripsPassengers as $trip) {
            TripsPassenger::create($trip);
        }

        // 17. Điền dữ liệu cho fields
        $fields = [
            ['name' => 'Vé xe rẻ', 'slug' => 've-xe-re', 'type' => 'Redbus', 'required' => 0, 'department_id' => 3, 'created_at' => '2025-06-29 21:44:00', 'updated_at' => '2025-06-29 21:44:00'],
            ['name' => 'Redbus', 'slug' => 'redbus', 'type' => 'Redbus', 'required' => 0, 'department_id' => 3, 'created_at' => '2025-06-29 21:44:00', 'updated_at' => '2025-06-29 21:44:00'],
            ['name' => 'Mobitrip', 'slug' => 'mobitrip', 'type' => 'Mobitrip', 'required' => 0, 'department_id' => 3, 'created_at' => '2025-06-29 21:44:00', 'updated_at' => '2025-06-29 21:44:00'],
            ['name' => 'Distribusion', 'slug' => 'distribusion', 'type' => 'Distribusion', 'required' => 0, 'department_id' => 3, 'created_at' => '2025-06-29 21:44:00', 'updated_at' => '2025-06-29 21:44:00'],
            ['name' => 'WEB/APP', 'slug' => 'web-app', 'type' => 'WEB/APP', 'required' => 0, 'department_id' => 3, 'created_at' => '2025-06-29 21:44:00', 'updated_at' => '2025-06-29 21:44:00'],
            ['name' => 'QR', 'slug' => 'qr', 'type' => 'QR', 'required' => 0, 'department_id' => 3, 'created_at' => '2025-06-29 21:44:00', 'updated_at' => '2025-06-29 21:44:00'],
            ['name' => 'Các văn phòng', 'slug' => 'cac-van-phong', 'type' => 'Office', 'required' => 0, 'department_id' => 3, 'created_at' => '2025-06-29 21:44:00', 'updated_at' => '2025-06-29 21:44:00'],
            ['name' => 'Vé thương gia', 'slug' => 've-thuong-gia', 'type' => 'Business', 'required' => 0, 'department_id' => 3, 'created_at' => '2025-06-29 21:44:00', 'updated_at' => '2025-06-29 21:44:00'],
        ];
        foreach ($fields as $field) {
            \App\Models\Field::firstOrCreate(['name' => $field['name']], $field);
        }
    }
}
