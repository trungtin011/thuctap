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
        // Tạo bảng departments
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Tạo bảng platforms
        Schema::create('platforms', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->timestamps();
        });

        // Tạo bảng expense_types
        Schema::create('expense_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->timestamps();
        });

        // Tạo bảng roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->enum('level', ['employee', 'manager', 'admin'])->comment('Phân cấp vai trò');
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Tạo bảng employees
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->string('name', 100);
            $table->string('position', 100)->nullable();
            $table->string('email', 255)->unique();
            $table->string('password', 255);
            $table->timestamps();
        });

        // Tạo bảng permissions
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 50)->unique();
            $table->timestamps();
        });

        // Tạo bảng role_permission
        Schema::create('role_permission', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->primary(['role_id', 'permission_id']);
        });

        // Tạo bảng financial_targets
        Schema::create('financial_targets', function (Blueprint $table) {
            $table->id();
            $table->year('year');
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('cascade');
            $table->decimal('target_amount', 20, 2);
            $table->timestamps();
            $table->unique(['year', 'department_id']);
        });

        // Tạo bảng financial_records
        Schema::create('financial_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->foreignId('platform_id')->constrained('platforms')->onDelete('cascade');
            $table->decimal('revenue', 15, 2);
            $table->date('record_date');
            $table->time('record_time');
            $table->text('note')->nullable();
            $table->enum('status', ['pending', 'manager_approved', 'admin_approved', 'rejected'])->default('pending');
            $table->foreignId('submitted_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->foreignId('manager_approved_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->foreignId('admin_approved_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->text('manager_note')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();
            $table->decimal('roas', 15, 2)->nullable()->comment('ROAS = Revenue / Total Expenses');
        });

        // Tạo bảng expenses
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_record_id')->constrained('financial_records')->onDelete('cascade');
            $table->foreignId('expense_type_id')->constrained('expense_types')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Tạo bảng cache
        Schema::create('cache', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('key', 191)->unique();
            $table->mediumText('value');
            $table->integer('expiration');
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

        // Tạo các trigger cho bảng expenses
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Xóa các trigger trước khi xóa bảng
        DB::unprepared('DROP TRIGGER IF EXISTS calculate_roas_after_expense');
        DB::unprepared('DROP TRIGGER IF EXISTS update_roas_after_expense_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS update_roas_after_expense_update');

        // Xóa các bảng theo thứ tự ngược lại để tránh lỗi ràng buộc khóa ngoại
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('financial_records');
        Schema::dropIfExists('financial_targets');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('expense_types');
        Schema::dropIfExists('platforms');
        Schema::dropIfExists('departments');
    }
};