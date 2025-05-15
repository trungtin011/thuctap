<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancialRecordsTable extends Migration
{
    public function up()
    {
        Schema::create('financial_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('platform_id')->constrained()->onDelete('cascade');
            $table->decimal('revenue', 15, 2);
            $table->date('record_date');
            $table->time('record_time');
            $table->text('note')->nullable();
            $table->enum('status', ['pending', 'manager_approved', 'admin_approved', 'rejected'])->default('pending');
            $table->foreignId('submitted_by')->constrained('employees')->onDelete('cascade');
            $table->foreignId('manager_approved_by')->nullable()->constrained('employees')->onDelete('cascade');
            $table->foreignId('admin_approved_by')->nullable()->constrained('employees')->onDelete('cascade');
            $table->text('manager_note')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('financial_records');
    }
}