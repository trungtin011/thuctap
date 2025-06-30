<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tạo bảng cache
        Schema::create('cache', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('key', 191)->unique();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        // Tạo bảng departments
        Schema::create('departments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Tạo bảng roles
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 50);
            $table->enum('level', ['employee', 'manager', 'admin'])->comment('Phân cấp vai trò');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Tạo bảng permissions
        Schema::create('permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->string('code', 50)->unique();
            $table->timestamps();
        });

        // Tạo bảng role_permission
        Schema::create('role_permission', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->primary(['role_id', 'permission_id']);
        });

        // Tạo bảng employees
        Schema::create('employees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->string('name', 100);
            $table->string('position', 100)->nullable();
            $table->string('email', 255)->unique();
            $table->string('password', 255);
            $table->timestamps();
        });

        // Tạo bảng sessions
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id', 255)->primary();
            $table->foreignId('user_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('payload');
            $table->integer('last_activity');
            $table->timestamps();
        });

        // Tạo bảng platforms
        Schema::create('platforms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 50);
            $table->timestamps();
        });

        // Tạo bảng platform_metrics
        Schema::create('platform_metrics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('platform_id')->constrained()->onDelete('cascade');
            $table->string('name', 100);
            $table->string('unit', 50)->nullable();
            $table->enum('data_type', ['int', 'float', 'string']);
            $table->timestamps();
        });

        // Tạo bảng expense_types
        Schema::create('expense_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 50);
            $table->timestamps();
        });

        // Tạo bảng dai_lies
        Schema::create('dai_lies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ten_dai_ly', 255);
            $table->string('email', 255)->unique();
            $table->string('so_dien_thoai', 255);
            $table->string('dia_chi', 255);
            $table->timestamps();
        });

        // Tạo bảng routes
        Schema::create('routes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255);
            $table->timestamps();
        });

        // Tạo bảng offices
        Schema::create('offices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255);
            $table->timestamps();
        });

        // Tạo bảng financial_records
        Schema::create('financial_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('platform_id')->constrained()->onDelete('cascade');
            $table->foreignId('dai_ly_id')->constrained()->onDelete('cascade');
            $table->foreignId('office_id')->constrained()->onDelete('cascade');
            $table->decimal('revenue', 15, 2);
            $table->decimal('commission', 15, 2)->nullable()->default(0);
            $table->decimal('roas', 15, 2)->nullable(); // Thêm cột roas
            $table->date('record_date');
            $table->time('record_time');
            $table->text('note')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('submitted_by')->constrained('employees')->onDelete('cascade'); // Sửa thành employees
            $table->timestamps();
        });

        // Tạo bảng financial_record_revenue_sources
        Schema::create('financial_record_revenue_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_record_id')->constrained()->onDelete('cascade');
            $table->foreignId('route_id')->constrained()->onDelete('cascade');
            $table->string('source_name', 100);
            $table->decimal('amount', 15, 2);
            $table->decimal('commission', 15, 2);
            $table->timestamps();
        });

        // Tạo bảng expenses
        Schema::create('expenses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('financial_record_id')->constrained()->onDelete('cascade');
            $table->foreignId('expense_type_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Tạo trigger cho expenses
        DB::unprepared('
            CREATE TRIGGER calculate_roas_after_expense AFTER INSERT ON expenses FOR EACH ROW
            BEGIN
                DECLARE total_expenses DECIMAL(15, 2);
                DECLARE revenue_amount DECIMAL(15, 2);

                SELECT SUM(amount) INTO total_expenses
                FROM expenses
                WHERE financial_record_id = NEW.financial_record_id;

                SELECT revenue INTO revenue_amount
                FROM financial_records
                WHERE id = NEW.financial_record_id;

                IF total_expenses > 0 THEN
                    UPDATE financial_records
                    SET roas = revenue_amount / total_expenses
                    WHERE id = NEW.financial_record_id;
                ELSE
                    UPDATE financial_records
                    SET roas = NULL
                    WHERE id = NEW.financial_record_id;
                END IF;
            END
        ');

        DB::unprepared('
            CREATE TRIGGER update_roas_after_expense_delete AFTER DELETE ON expenses FOR EACH ROW
            BEGIN
                DECLARE total_expenses DECIMAL(15, 2);
                DECLARE revenue_amount DECIMAL(15, 2);

                SELECT SUM(amount) INTO total_expenses
                FROM expenses
                WHERE financial_record_id = OLD.financial_record_id;

                SELECT revenue INTO revenue_amount
                FROM financial_records
                WHERE id = OLD.financial_record_id;

                IF total_expenses > 0 THEN
                    UPDATE financial_records
                    SET roas = revenue_amount / total_expenses
                    WHERE id = OLD.financial_record_id;
                ELSE
                    UPDATE financial_records
                    SET roas = NULL
                    WHERE id = OLD.financial_record_id;
                END IF;
            END
        ');

        DB::unprepared('
            CREATE TRIGGER update_roas_after_expense_update AFTER UPDATE ON expenses FOR EACH ROW
            BEGIN
                DECLARE total_expenses DECIMAL(15, 2);
                DECLARE revenue_amount DECIMAL(15, 2);

                SELECT SUM(amount) INTO total_expenses
                FROM expenses
                WHERE financial_record_id = NEW.financial_record_id;

                SELECT revenue INTO revenue_amount
                FROM financial_records
                WHERE id = NEW.financial_record_id;

                IF total_expenses > 0 THEN
                    UPDATE financial_records
                    SET roas = revenue_amount / total_expenses
                    WHERE id = NEW.financial_record_id;
                ELSE
                    UPDATE financial_records
                    SET roas = NULL
                    WHERE id = NEW.financial_record_id;
                END IF;
            END
        ');

        // Tạo bảng financial_targets
        Schema::create('financial_targets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->year('year');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('target_amount', 20, 2);
            $table->unique(['year', 'department_id']);
            $table->timestamps();
        });

        // Tạo bảng metric_values
        Schema::create('metric_values', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('metric_id')->constrained('platform_metrics')->onDelete('cascade');
            $table->string('value', 255);
            $table->timestamp('recorded_at')->nullable();
            $table->foreignId('financial_record_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Tạo bảng office_revenues
        Schema::create('office_revenues', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('office_id')->constrained()->onDelete('cascade');
            $table->decimal('cash', 15, 2)->default(0.00);
            $table->decimal('bank_transfer', 15, 2)->default(0.00);
            $table->decimal('expense', 15, 2)->default(0.00);
            $table->decimal('total', 15, 2)->default(0.00);
            $table->date('record_date');
            $table->timestamps();
        });

        // Tạo bảng trips_passengers
        Schema::create('trips_passengers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('route_id')->constrained()->onDelete('cascade');
            $table->integer('trips')->default(0);
            $table->integer('passengers')->default(0);
            $table->date('record_date');
            $table->timestamps();
        });

        Schema::create('fields', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('type');
            $table->boolean('required')->default(false);
            $table->unsignedBigInteger('department_id')->nullable(); // Đặt ngay sau required, không cần after()
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->timestamps();
        });
        // Chèn dữ liệu mẫu
        $this->seedData();
    }

    /**
     * Seed sample data.
     */
    private function seedData(): void
    {
        // Dữ liệu cho bảng departments
        DB::table('departments')->insert([
            ['id' => 1, 'name' => 'Marketing', 'description' => 'Phòng ban phụ trách marketing', 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
            ['id' => 2, 'name' => 'Sales', 'description' => 'Phòng ban phụ trách bán hàng', 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
        ]);

        // Dữ liệu cho bảng roles
        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'Nhân viên Marketing', 'level' => 'employee', 'department_id' => 1, 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
            ['id' => 2, 'name' => 'Quản lý Marketing', 'level' => 'manager', 'department_id' => 1, 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
            ['id' => 3, 'name' => 'Admin Marketing', 'level' => 'admin', 'department_id' => 1, 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
            ['id' => 4, 'name' => 'Nhân viên Sales', 'level' => 'employee', 'department_id' => 2, 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
            ['id' => 5, 'name' => 'Quản lý Sales', 'level' => 'manager', 'department_id' => 2, 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
            ['id' => 6, 'name' => 'Admin Sales', 'level' => 'admin', 'department_id' => 2, 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
        ]);

        // Dữ liệu cho bảng permissions
        DB::table('permissions')->insert([
            ['id' => 1, 'name' => 'Xem bản ghi tài chính', 'code' => 'view_records', 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
            ['id' => 2, 'name' => 'Chỉnh sửa bản ghi tài chính', 'code' => 'edit_records', 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
            ['id' => 3, 'name' => 'Phê duyệt bản ghi tài chính', 'code' => 'approve_records', 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
            ['id' => 4, 'name' => 'Xóa bản ghi tài chính', 'code' => 'delete_records', 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
            ['id' => 5, 'name' => 'Quản lý phòng ban', 'code' => 'manage_departments', 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
            ['id' => 6, 'name' => 'Quản lý vai trò', 'code' => 'manage_roles', 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
        ]);

        // Dữ liệu cho bảng role_permission
        DB::table('role_permission')->insert([
            ['role_id' => 1, 'permission_id' => 1],
            ['role_id' => 2, 'permission_id' => 1],
            ['role_id' => 2, 'permission_id' => 2],
            ['role_id' => 2, 'permission_id' => 3],
            ['role_id' => 3, 'permission_id' => 1],
            ['role_id' => 3, 'permission_id' => 2],
            ['role_id' => 3, 'permission_id' => 3],
            ['role_id' => 3, 'permission_id' => 4],
            ['role_id' => 3, 'permission_id' => 5],
            ['role_id' => 3, 'permission_id' => 6],
            ['role_id' => 4, 'permission_id' => 1],
            ['role_id' => 5, 'permission_id' => 1],
            ['role_id' => 5, 'permission_id' => 2],
            ['role_id' => 5, 'permission_id' => 3],
            ['role_id' => 6, 'permission_id' => 1],
            ['role_id' => 6, 'permission_id' => 2],
            ['role_id' => 6, 'permission_id' => 3],
            ['role_id' => 6, 'permission_id' => 4],
            ['role_id' => 6, 'permission_id' => 5],
            ['role_id' => 6, 'permission_id' => 6],
        ]);

        // Dữ liệu cho bảng employees
        DB::table('employees')->insert([
            [
                'id' => 1,
                'department_id' => 1,
                'role_id' => 1,
                'name' => 'John Doe',
                'position' => 'Chuyên viên Marketing',
                'email' => 'khoaebanypk03641@gmail.com',
                'password' => '$2y$12$vsgKYK0qK0kVSwdzLZbwie5YiSJWa/GTZxK4CZh1cJVhLsJBL4Iym',
                'created_at' => '2025-05-23 19:14:38',
                'updated_at' => '2025-05-23 19:14:38'
            ],
            [
                'id' => 2,
                'department_id' => 1,
                'role_id' => 2,
                'name' => 'Jane Smith',
                'position' => 'Quản lý Marketing',
                'email' => 'ykhoa11a13@gmail.com',
                'password' => '$2y$12$n.T0n0fU7rAfw2FsBfHUkugcgQxf4sKTAzdvrrKgKGvpJxW1qBQqu',
                'created_at' => '2025-05-23 19:14:38',
                'updated_at' => '2025-05-23 19:14:38'
            ],
            [
                'id' => 3,
                'department_id' => 1,
                'role_id' => 3,
                'name' => 'Admin User',
                'position' => 'Quản trị viên',
                'email' => 'dauxanh008@gmail.com',
                'password' => '$2y$12$xQFA1qlqWuWyT54d5rwr../lLjDcYwxTEhdWl.mfsLiRglu4IzZVm',
                'created_at' => '2025-05-23 19:14:38',
                'updated_at' => '2025-05-23 19:14:38'
            ],
        ]);

        // Dữ liệu cho bảng platforms
        DB::table('platforms')->insert([
            ['id' => 1, 'name' => 'Google Ads', 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
            ['id' => 2, 'name' => 'Facebook Ads', 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
            ['id' => 3, 'name' => 'TikTok Ads', 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
            ['id' => 4, 'name' => 'QR', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 5, 'name' => 'WEB/APP', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 6, 'name' => 'Các văn phòng', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 7, 'name' => 'Vé thương gia', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
        ]);

        // Dữ liệu cho bảng platform_metrics
        DB::table('platform_metrics')->insert([
            ['id' => 1, 'platform_id' => 2, 'name' => 'Lượt Click', 'unit' => 'Lượt', 'data_type' => 'int', 'created_at' => '2025-05-23 19:16:23', 'updated_at' => '2025-05-23 19:16:23'],
        ]);

        // Dữ liệu cho bảng expense_types
        DB::table('expense_types')->insert([
            ['id' => 1, 'name' => 'Chi phí quảng cáo', 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
            ['id' => 2, 'name' => 'Chi phí vận hành', 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
            ['id' => 3, 'name' => 'Chi phí nhân sự', 'created_at' => '2025-05-23 19:14:38', 'updated_at' => '2025-05-23 19:14:38'],
        ]);

        // Dữ liệu cho bảng dai_lies
        DB::table('dai_lies')->insert([
            ['id' => 1, 'ten_dai_ly' => 'Adam', 'email' => 'adam@example.com', 'so_dien_thoai' => '0123456789', 'dia_chi' => 'HCM', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 2, 'ten_dai_ly' => 'Vé xe rẻ', 'email' => 'vexe@example.com', 'so_dien_thoai' => '0123456789', 'dia_chi' => 'HCM', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 3, 'ten_dai_ly' => 'Redbus', 'email' => 'redbus@example.com', 'so_dien_thoai' => '0123456789', 'dia_chi' => 'HCM', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 4, 'ten_dai_ly' => 'Mobitrip', 'email' => 'mobitrip@example.com', 'so_dien_thoai' => '0123456789', 'dia_chi' => 'HCM', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 5, 'ten_dai_ly' => 'WEB/APP', 'email' => 'webapp@example.com', 'so_dien_thoai' => '0123456789', 'dia_chi' => 'HCM', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 6, 'ten_dai_ly' => 'QR', 'email' => 'qr@example.com', 'so_dien_thoai' => '0123456789', 'dia_chi' => 'HCM', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 7, 'ten_dai_ly' => 'Các văn phòng', 'email' => 'offices@example.com', 'so_dien_thoai' => '0123456789', 'dia_chi' => 'HCM', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 8, 'ten_dai_ly' => 'Vé thương gia', 'email' => 'thuonggia@example.com', 'so_dien_thoai' => '0123456789', 'dia_chi' => 'HCM', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 9, 'ten_dai_ly' => 'Distribusion', 'email' => 'distribusion@example.com', 'so_dien_thoai' => '0123456789', 'dia_chi' => 'HCM', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
        ]);

        // Dữ liệu cho bảng routes
        DB::table('routes')->insert([
            ['id' => 1, 'name' => 'Sài Gòn - Buôn Ma Thuột', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 2, 'name' => 'Buôn Ma Thuột - Sài Gòn', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 3, 'name' => 'Sài Gòn - Đà Lạt', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 4, 'name' => 'Đà Lạt - Sài Gòn', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 5, 'name' => 'Sài Gòn - Nha Trang', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 6, 'name' => 'Nha Trang - Sài Gòn', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 7, 'name' => 'Sài Gòn - Mũi Né', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 8, 'name' => 'Mũi Né - Sài Gòn', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 9, 'name' => 'Buôn Ma Thuột - Đà Lạt', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 10, 'name' => 'Đà Lạt - Buôn Ma Thuột', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 11, 'name' => 'Xe hợp đồng', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
        ]);

        // Dữ liệu cho bảng offices
        DB::table('offices')->insert([
            ['id' => 1, 'name' => 'VP 49', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 2, 'name' => 'VP BT', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 3, 'name' => 'VP CMG', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 4, 'name' => 'VP Q5', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 5, 'name' => 'VP ĐL', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 6, 'name' => 'VP NT', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
        ]);

        // Dữ liệu cho bảng financial_records
        DB::table('financial_records')->insert([
            [
                'id' => 1,
                'dai_ly_id' => 1,
                'department_id' => 1,
                'platform_id' => 1,
                'office_id' => 1,
                'revenue' => 1101465000.00,
                'commission' => 121264230.00,
                'record_date' => '2025-01-31',
                'record_time' => '23:59:00',
                'note' => 'Doanh thu Adam T1/2025',
                'status' => 'pending',
                'submitted_by' => 1,
                'created_at' => '2025-05-25 17:04:00',
                'updated_at' => '2025-05-25 17:04:00'
            ],
            [
                'id' => 2,
                'dai_ly_id' => 2,
                'department_id' => 1,
                'platform_id' => 2,
                'office_id' => 1,
                'revenue' => 3931184500.00,
                'commission' => 395440870.00,
                'record_date' => '2025-01-31',
                'record_time' => '23:59:00',
                'note' => 'Doanh thu Vé xe rẻ T1/2025',
                'status' => 'pending',
                'submitted_by' => 1,
                'created_at' => '2025-05-25 17:04:00',
                'updated_at' => '2025-05-25 17:04:00'
            ],
            [
                'id' => 3,
                'dai_ly_id' => 3,
                'department_id' => 1,
                'platform_id' => 3,
                'office_id' => 1,
                'revenue' => 111675000.00,
                'commission' => 11167500.00,
                'record_date' => '2025-01-31',
                'record_time' => '23:59:00',
                'note' => 'Doanh thu Redbus T1/2025',
                'status' => 'pending',
                'submitted_by' => 1,
                'created_at' => '2025-05-25 17:04:00',
                'updated_at' => '2025-05-25 17:04:00'
            ],
            [
                'id' => 4,
                'dai_ly_id' => 4,
                'department_id' => 1,
                'platform_id' => 4,
                'office_id' => 1,
                'revenue' => 2800000.00,
                'commission' => 280000.00,
                'record_date' => '2025-01-31',
                'record_time' => '23:59:00',
                'note' => 'Doanh thu Mobitrip T1/2025',
                'status' => 'pending',
                'submitted_by' => 1,
                'created_at' => '2025-05-25 17:04:00',
                'updated_at' => '2025-05-25 17:04:00'
            ],
            [
                'id' => 5,
                'dai_ly_id' => 5,
                'department_id' => 1,
                'platform_id' => 5,
                'office_id' => 1,
                'revenue' => 874894500.00,
                'commission' => 5000000.00,
                'record_date' => '2025-01-31',
                'record_time' => '23:59:00',
                'note' => 'Doanh thu WEB/APP T1/2025',
                'status' => 'pending',
                'submitted_by' => 1,
                'created_at' => '2025-05-25 17:04:00',
                'updated_at' => '2025-05-25 17:04:00'
            ],
            [
                'id' => 6,
                'dai_ly_id' => 6,
                'department_id' => 1,
                'platform_id' => 6,
                'office_id' => 1,
                'revenue' => 6661933500.00,
                'commission' => 36683841.15,
                'record_date' => '2025-01-31',
                'record_time' => '23:59:00',
                'note' => 'Doanh thu QR T1/2025',
                'status' => 'pending',
                'submitted_by' => 1,
                'created_at' => '2025-05-25 17:04:00',
                'updated_at' => '2025-05-25 17:04:00'
            ],
            [
                'id' => 7,
                'dai_ly_id' => 7,
                'department_id' => 1,
                'platform_id' => 7,
                'office_id' => 1,
                'revenue' => 561839000.00,
                'commission' => 1000000.00,
                'record_date' => '2025-01-31',
                'record_time' => '23:59:00',
                'note' => 'Doanh thu Các văn phòng T1/2025',
                'status' => 'pending',
                'submitted_by' => 1,
                'created_at' => '2025-05-25 17:04:00',
                'updated_at' => '2025-05-25 17:04:00'
            ],
            [
                'id' => 8,
                'dai_ly_id' => 8,
                'department_id' => 1,
                'platform_id' => 7,
                'office_id' => 1,
                'revenue' => 92906000.00,
                'commission' => 200000.00,
                'record_date' => '2025-01-31',
                'record_time' => '23:59:00',
                'note' => 'Doanh thu Vé thương gia T1/2025',
                'status' => 'pending',
                'submitted_by' => 1,
                'created_at' => '2025-05-25 17:04:00',
                'updated_at' => '2025-05-25 17:04:00'
            ],
        ]);

        // Dữ liệu cho bảng expenses
        DB::table('expenses')->insert([
            ['id' => 1, 'financial_record_id' => 1, 'expense_type_id' => 3, 'amount' => 17949000.00, 'description' => 'Chi phí VP 49 T1/2025', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 2, 'financial_record_id' => 2, 'expense_type_id' => 3, 'amount' => 13720000.00, 'description' => 'Chi phí VP 49 T2/2025', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 3, 'financial_record_id' => 3, 'expense_type_id' => 3, 'amount' => 12101000.00, 'description' => 'Chi phí VP 49 T3/2025', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 4, 'financial_record_id' => 4, 'expense_type_id' => 3, 'amount' => 10755000.00, 'description' => 'Chi phí VP 49 T4/2025', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
        ]);

        // Dữ liệu cho bảng financial_targets
        DB::table('financial_targets')->insert([
            ['id' => 1, 'year' => 2025, 'department_id' => 1, 'target_amount' => 35017977699.00, 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
        ]);

        // Dữ liệu cho bảng office_revenues
        DB::table('office_revenues')->insert([
            ['id' => 1, 'office_id' => 1, 'cash' => 155876000.00, 'bank_transfer' => 131143000.00, 'expense' => 17949000.00, 'total' => 378786000.00, 'record_date' => '2025-01-31', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 2, 'office_id' => 1, 'cash' => 124920000.00, 'bank_transfer' => 74955000.00, 'expense' => 13720000.00, 'total' => 257705000.00, 'record_date' => '2025-02-28', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 3, 'office_id' => 1, 'cash' => 134343000.00, 'bank_transfer' => 108015000.00, 'expense' => 12101000.00, 'total' => 312278000.00, 'record_date' => '2025-03-31', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 4, 'office_id' => 1, 'cash' => 112500000.00, 'bank_transfer' => 73100000.00, 'expense' => 10755000.00, 'total' => 259540000.00, 'record_date' => '2025-04-30', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 5, 'office_id' => 2, 'cash' => 42318000.00, 'bank_transfer' => 0.00, 'expense' => 0.00, 'total' => 42318000.00, 'record_date' => '2025-01-31', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 6, 'office_id' => 2, 'cash' => 20580000.00, 'bank_transfer' => 0.00, 'expense' => 0.00, 'total' => 20580000.00, 'record_date' => '2025-02-28', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 7, 'office_id' => 2, 'cash' => 30560000.00, 'bank_transfer' => 0.00, 'expense' => 0.00, 'total' => 30560000.00, 'record_date' => '2025-03-31', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 8, 'office_id' => 2, 'cash' => 40355000.00, 'bank_transfer' => 0.00, 'expense' => 0.00, 'total' => 40355000.00, 'record_date' => '2025-04-30', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 9, 'office_id' => 3, 'cash' => 28850000.00, 'bank_transfer' => 0.00, 'expense' => 0.00, 'total' => 28850000.00, 'record_date' => '2025-01-31', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 10, 'office_id' => 3, 'cash' => 21960000.00, 'bank_transfer' => 0.00, 'expense' => 0.00, 'total' => 21960000.00, 'record_date' => '2025-02-28', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 11, 'office_id' => 3, 'cash' => 26200000.00, 'bank_transfer' => 0.00, 'expense' => 0.00, 'total' => 26200000.00, 'record_date' => '2025-03-31', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 12, 'office_id' => 3, 'cash' => 20440000.00, 'bank_transfer' => 0.00, 'expense' => 0.00, 'total' => 20440000.00, 'record_date' => '2025-04-30', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 13, 'office_id' => 4, 'cash' => 1170000.00, 'bank_transfer' => 0.00, 'expense' => 0.00, 'total' => 1170000.00, 'record_date' => '2025-01-31', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 14, 'office_id' => 4, 'cash' => 900000.00, 'bank_transfer' => 0.00, 'expense' => 0.00, 'total' => 900000.00, 'record_date' => '2025-02-28', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 15, 'office_id' => 4, 'cash' => 969000.00, 'bank_transfer' => 0.00, 'expense' => 0.00, 'total' => 969000.00, 'record_date' => '2025-03-31', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 16, 'office_id' => 4, 'cash' => 1500000.00, 'bank_transfer' => 0.00, 'expense' => 0.00, 'total' => 1500000.00, 'record_date' => '2025-04-30', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 17, 'office_id' => 5, 'cash' => 1180000.00, 'bank_transfer' => 0.00, 'expense' => 0.00, 'total' => 1180000.00, 'record_date' => '2025-01-31', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 18, 'office_id' => 5, 'cash' => 670000.00, 'bank_transfer' => 0.00, 'expense' => 0.00, 'total' => 670000.00, 'record_date' => '2025-02-28', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 19, 'office_id' => 5, 'cash' => 40000.00, 'bank_transfer' => 0.00, 'expense' => 0.00, 'total' => 40000.00, 'record_date' => '2025-03-31', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 20, 'office_id' => 5, 'cash' => 800000.00, 'bank_transfer' => 0.00, 'expense' => 0.00, 'total' => 800000.00, 'record_date' => '2025-04-30', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 21, 'office_id' => 6, 'cash' => 300000.00, 'bank_transfer' => 0.00, 'expense' => 0.00, 'total' => 300000.00, 'record_date' => '2025-01-31', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 22, 'office_id' => 6, 'cash' => 0.00, 'bank_transfer' => 0.00, 'expense' => 0.00, 'total' => 0.00, 'record_date' => '2025-02-28', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 23, 'office_id' => 6, 'cash' => 50000.00, 'bank_transfer' => 0.00, 'expense' => 0.00, 'total' => 50000.00, 'record_date' => '2025-03-31', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 24, 'office_id' => 6, 'cash' => 90000.00, 'bank_transfer' => 0.00, 'expense' => 0.00, 'total' => 90000.00, 'record_date' => '2025-04-30', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
        ]);

        // Dữ liệu cho bảng trips_passengers
        DB::table('trips_passengers')->insert([
            ['id' => 1, 'route_id' => 1, 'trips' => 460, 'passengers' => 8573, 'record_date' => '2025-01-31', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 2, 'route_id' => 2, 'trips' => 319, 'passengers' => 3308, 'record_date' => '2025-01-31', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 3, 'route_id' => 3, 'trips' => 214, 'passengers' => 3375, 'record_date' => '2025-01-31', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 4, 'route_id' => 4, 'trips' => 203, 'passengers' => 2106, 'record_date' => '2025-01-31', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 5, 'route_id' => 5, 'trips' => 51, 'passengers' => 622, 'record_date' => '2025-01-31', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 6, 'route_id' => 6, 'trips' => 49, 'passengers' => 288, 'record_date' => '2025-01-31', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 7, 'route_id' => 7, 'trips' => 136, 'passengers' => 459, 'record_date' => '2025-01-31', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 8, 'route_id' => 8, 'trips' => 135, 'passengers' => 366, 'record_date' => '2025-01-31', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 9, 'route_id' => 9, 'trips' => 12, 'passengers' => 80, 'record_date' => '2025-01-31', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 10, 'route_id' => 10, 'trips' => 11, 'passengers' => 121, 'record_date' => '2025-01-31', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
            ['id' => 11, 'route_id' => 11, 'trips' => 0, 'passengers' => 0, 'record_date' => '2025-01-31', 'created_at' => '2025-05-25 17:04:00', 'updated_at' => '2025-05-25 17:04:00'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Xóa các trigger trước
        DB::unprepared('DROP TRIGGER IF EXISTS calculate_roas_after_expense');
        DB::unprepared('DROP TRIGGER IF EXISTS update_roas_after_expense_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS update_roas_after_expense_update');

        // Xóa các bảng theo thứ tự ngược lại để tránh lỗi ràng buộc khóa ngoại
        Schema::dropIfExists('trips_passengers');
        Schema::dropIfExists('office_revenues');
        Schema::dropIfExists('metric_values');
        Schema::dropIfExists('financial_targets');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('financial_records');
        Schema::dropIfExists('financial_record_revenue_sources');
        Schema::dropIfExists('offices');
        Schema::dropIfExists('routes');
        Schema::dropIfExists('dai_lies');
        Schema::dropIfExists('expense_types');
        Schema::dropIfExists('platform_metrics');
        Schema::dropIfExists('platforms');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('cache');
    }
};
