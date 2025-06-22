<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubmittedByAndDepartmentIdToOfficeRevenuesTable extends Migration
{
    public function up()
    {
        Schema::table('office_revenues', function (Blueprint $table) {
            $table->unsignedBigInteger('department_id')->nullable()->after('id');
            $table->unsignedBigInteger('submitted_by')->nullable()->after('department_id');

            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('submitted_by')->references('id')->on('employees')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('office_revenues', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropForeign(['submitted_by']);
            $table->dropColumn(['department_id', 'submitted_by']);
        });
    }
}