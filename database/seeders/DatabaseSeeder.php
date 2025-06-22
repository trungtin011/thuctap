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
use App\Models\Office;
use App\Models\OfficeRevenue;
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
            ['name' => 'Kế toán', 'description' => 'Phòng ban phụ trách kế toán'], // Thêm Kế toán
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
            ['name' => 'Nhân viên Kế toán', 'level' => 'employee', 'department_id' => 3], // Thêm Kế toán
            ['name' => 'Quản lý Kế toán', 'level' => 'manager', 'department_id' => 3],
            ['name' => 'Admin Kế toán', 'level' => 'admin', 'department_id' => 3],
        ];
        foreach ($roles as $role) {
            Role::create($role);
        }

        // 4. Liên kết quyền với vai trò qua bảng role_permission
        $rolePermissions = [
            // Nhân viên Marketing (role_id = 1)
            ['role_id' => 1, 'permission_id' => 1],
            // Quản lý Marketing (role_id = 2)
            ['role_id' => 2, 'permission_id' => 1],
            ['role_id' => 2, 'permission_id' => 2],
            ['role_id' => 2, 'permission_id' => 3],
            // Admin Marketing (role_id = 3)
            ['role_id' => 3, 'permission_id' => 1],
            ['role_id' => 3, 'permission_id' => 2],
            ['role_id' => 3, 'permission_id' => 3],
            ['role_id' => 3, 'permission_id' => 4],
            ['role_id' => 3, 'permission_id' => 5],
            ['role_id' => 3, 'permission_id' => 6],
            // Nhân viên Sales (role_id = 4)
            ['role_id' => 4, 'permission_id' => 1],
            // Quản lý Sales (role_id = 5)
            ['role_id' => 5, 'permission_id' => 1],
            ['role_id' => 5, 'permission_id' => 2],
            ['role_id' => 5, 'permission_id' => 3],
            // Admin Sales (role_id = 6)
            ['role_id' => 6, 'permission_id' => 1],
            ['role_id' => 6, 'permission_id' => 2],
            ['role_id' => 6, 'permission_id' => 3],
            ['role_id' => 6, 'permission_id' => 4],
            ['role_id' => 6, 'permission_id' => 5],
            ['role_id' => 6, 'permission_id' => 6],
            // Nhân viên Kế toán (role_id = 7)
            ['role_id' => 7, 'permission_id' => 1],
            // Quản lý Kế toán (role_id = 8)
            ['role_id' => 8, 'permission_id' => 1],
            ['role_id' => 8, 'permission_id' => 2],
            ['role_id' => 8, 'permission_id' => 3],
            // Admin Kế toán (role_id = 9)
            ['role_id' => 9, 'permission_id' => 1],
            ['role_id' => 9, 'permission_id' => 2],
            ['role_id' => 9, 'permission_id' => 3],
            ['role_id' => 9, 'permission_id' => 4],
            ['role_id' => 9, 'permission_id' => 5],
            ['role_id' => 9, 'permission_id' => 6],
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
                'position' => 'Nhân viên Marketing',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'ykhoa11a13@gmail.com',
                'password' => Hash::make('123123123'),
                'department_id' => 1,
                'role_id' => 2, // Quản lý Marketing
                'position' => 'Quản lý',
            ],
            [
                'name' => 'Admin User',
                'email' => 'dauxanh008@gmail.com',
                'password' => Hash::make('123123123'),
                'department_id' => 1,
                'role_id' => 3, // Admin Marketing
                'position' => 'Quản trị viên',
            ],
            [
                'name' => 'Kế toán viên',
                'email' => 'ketoan@example.com',
                'password' => Hash::make('123123123'),
                'department_id' => 3,
                'role_id' => 7, // Nhân viên Kế toán
                'position' => 'Nhân viên Kế toán',
            ],
            [
                'name' => 'Kinh doanh',
                'email' => 'kinhdoanh@example.com',
                'password' => Hash::make('123123123'),
                'department_id' => 4,
                'role_id' => 8, // Quản lý Kế toán
                'position' => 'Quản lý Kinh doanh',
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
            ['name' => 'QR'],
            ['name' => 'WEB/APP'],
            ['name' => 'Các văn phòng'],
            ['name' => 'Vé thương gia'],
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

        // 8. Thêm văn phòng
        $offices = [
            ['name' => 'VP 49'],
            ['name' => 'VP BT'],
            ['name' => 'VP CMG'],
            ['name' => 'VP Q5'],
            ['name' => 'VP ĐL'],
            ['name' => 'VP NT'],
        ];
        foreach ($offices as $office) {
            Office::create($office);
        }

        // 9. Thêm tuyến đường
        $routes = [
            ['name' => 'Sài Gòn - Buôn Ma Thuột', 'department_id' => 1],
            ['name' => 'Buôn Ma Thuột - Sài Gòn', 'department_id' => 1],
            ['name' => 'Sài Gòn - Đà Lạt', 'department_id' => 1],
            ['name' => 'Đà Lạt - Sài Gòn', 'department_id' => 1],
            ['name' => 'Sài Gòn - Nha Trang', 'department_id' => 1],
            ['name' => 'Nha Trang - Sài Gòn', 'department_id' => 1],
            ['name' => 'Sài Gòn - Mũi Né', 'department_id' => 1],
            ['name' => 'Mũi Né - Sài Gòn', 'department_id' => 1],
            ['name' => 'Buôn Ma Thuột - Đà Lạt', 'department_id' => 1],
            ['name' => 'Đà Lạt - Buôn Ma Thuột', 'department_id' => 1],
            ['name' => 'Xe hợp đồng', 'department_id' => 1],
        ];
        foreach ($routes as $route) {
            DB::table('routes')->insert($route);
        }

        // 10. Thêm đại lý
        $daiLies = [
            ['ten_dai_ly' => 'Adam', 'email' => 'adam@example.com', 'so_dien_thoai' => '0123456789', 'dia_chi' => 'HCM'],
            ['ten_dai_ly' => 'Vé xe rẻ', 'email' => 'vexe@example.com', 'so_dien_thoai' => '0123456789', 'dia_chi' => 'HCM'],
            ['ten_dai_ly' => 'Redbus', 'email' => 'redbus@example.com', 'so_dien_thoai' => '0123456789', 'dia_chi' => 'HCM'],
            ['ten_dai_ly' => 'Mobitrip', 'email' => 'mobitrip@example.com', 'so_dien_thoai' => '0123456789', 'dia_chi' => 'HCM'],
            ['ten_dai_ly' => 'WEB/APP', 'email' => 'webapp@example.com', 'so_dien_thoai' => '0123456789', 'dia_chi' => 'HCM'],
            ['ten_dai_ly' => 'QR', 'email' => 'qr@example.com', 'so_dien_thoai' => '0123456789', 'dia_chi' => 'HCM'],
            ['ten_dai_ly' => 'Các văn phòng', 'email' => 'offices@example.com', 'so_dien_thoai' => '0123456789', 'dia_chi' => 'HCM'],
            ['ten_dai_ly' => 'Vé thương gia', 'email' => 'thuonggia@example.com', 'so_dien_thoai' => '0123456789', 'dia_chi' => 'HCM'],
            ['ten_dai_ly' => 'Distribusion', 'email' => 'distribusion@example.com', 'so_dien_thoai' => '0123456789', 'dia_chi' => 'HCM'],
        ];
        foreach ($daiLies as $daiLy) {
            DB::table('dai_lies')->insert($daiLy);
        }

        // 11. Thêm bản ghi tài chính
        $financialRecords = [
            [
                'dai_ly_id' => 1,
                'department_id' => 1,
                'platform_id' => 1,
                'route_id' => 1,
                'office_id' => 1,
                'revenue' => 1101465000.00,
                'commission' => 121264230.00,
                'record_date' => '2025-01-31',
                'record_time' => '23:59:00',
                'note' => 'Doanh thu Adam T1/2025',
                'status' => 'pending',
                'submitted_by' => 1,
            ],
            [
                'dai_ly_id' => 2,
                'department_id' => 1,
                'platform_id' => 2,
                'route_id' => 2,
                'office_id' => 1,
                'revenue' => 3931184500.00,
                'commission' => 395440870.00,
                'record_date' => '2025-01-31',
                'record_time' => '23:59:00',
                'note' => 'Doanh thu Vé xe rẻ T1/2025',
                'status' => 'pending',
                'submitted_by' => 1,
            ],
            [
                'dai_ly_id' => 3,
                'department_id' => 1,
                'platform_id' => 3,
                'route_id' => 3,
                'office_id' => 1,
                'revenue' => 111675000.00,
                'commission' => 11167500.00,
                'record_date' => '2025-01-31',
                'record_time' => '23:59:00',
                'note' => 'Doanh thu Redbus T1/2025',
                'status' => 'pending',
                'submitted_by' => 1,
            ],
        ];
        foreach ($financialRecords as $record) {
            FinancialRecord::create($record);
        }

        // 12. Thêm chi phí
        $expenses = [
            ['financial_record_id' => 1, 'expense_type_id' => 3, 'amount' => 17949000.00, 'description' => 'Chi phí VP 49 T1/2025'],
            ['financial_record_id' => 2, 'expense_type_id' => 3, 'amount' => 13720000.00, 'description' => 'Chi phí VP 49 T2/2025'],
            ['financial_record_id' => 3, 'expense_type_id' => 3, 'amount' => 12101000.00, 'description' => 'Chi phí VP 49 T3/2025'],
        ];
        foreach ($expenses as $expense) {
            Expense::create($expense);
        }

        // 13. Thêm mục tiêu tài chính
        $financialTargets = [
            ['year' => 2025, 'department_id' => 1, 'target_amount' => 35017977699.00],
        ];
        foreach ($financialTargets as $target) {
            FinancialTarget::create($target);
        }

        // 14. Thêm bản ghi doanh thu văn phòng
        $officeRevenues = [
            [
                'cash' => 155876000.00,
                'bank_transfer' => 131143000.00,
                'expense' => 17949000.00,
                'total' => 304932000.00, // Cập nhật lại tổng: 155876000 + 131143000 + 17949000
                'record_date' => '2025-01-31',
                'status' => 'pending',
            ],
            [
                'cash' => 124920000.00,
                'bank_transfer' => 74955000.00,
                'expense' => 13720000.00,
                'total' => 213595000.00, // Cập nhật lại tổng: 124920000 + 74955000 + 13720000
                'record_date' => '2025-02-28',
                'status' => 'pending',
            ],
            [
                'cash' => 134343000.00,
                'bank_transfer' => 108015000.00,
                'expense' => 12101000.00,
                'total' => 254459000.00, // Cập nhật lại tổng: 134343000 + 108015000 + 12101000
                'record_date' => '2025-03-31',
                'status' => 'pending',
            ],
            [
                'cash' => 112500000.00,
                'bank_transfer' => 73100000.00,
                'expense' => 10755000.00,
                'total' => 196655000.00, // Cập nhật lại tổng: 112500000 + 73100000 + 10755000
                'record_date' => '2025-04-30',
                'status' => 'pending',
            ],
            [
                'cash' => 42318000.00,
                'bank_transfer' => 0.00,
                'expense' => 0.00,
                'total' => 42318000.00,
                'record_date' => '2025-01-31',
                'status' => 'pending',
            ],
        ];
        foreach ($officeRevenues as $index => $revenue) {
            $officeRevenue = OfficeRevenue::create($revenue);
            // Gắn các văn phòng tương ứng qua bảng office_revenue_offices
            $officeIds = [1, 2, 3, 4, 5, 6]; // Gắn tất cả văn phòng cho mỗi bản ghi mẫu
            $officeRevenue->offices()->attach(array_slice($officeIds, 0, $index + 1)); // Gắn văn phòng theo thứ tự
        }
    }
}
