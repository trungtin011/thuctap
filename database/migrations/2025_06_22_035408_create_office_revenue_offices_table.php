<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**git config --global core.autocrlf true

     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('office_revenue_offices', function (Blueprint $table) {
            $table->bigInteger('office_revenue_id')->unsigned();
            $table->bigInteger('office_id')->unsigned();
            $table->primary(['office_revenue_id', 'office_id']);
            $table->foreign('office_revenue_id')->references('id')->on('office_revenues')->onDelete('cascade');
            $table->foreign('office_id')->references('id')->on('offices')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('office_revenue_offices');
    }
};
