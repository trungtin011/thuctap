<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancialTargetsTable extends Migration
{
    public function up()
    {
        Schema::create('financial_targets', function (Blueprint $table) {
            $table->id();
            $table->year('year');
            $table->unsignedBigInteger('department_id')->nullable(); // null = toàn công ty
            $table->decimal('target_amount', 20, 2);
            $table->timestamps();

            $table->unique(['year', 'department_id']);
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('financial_targets');
    }
}
