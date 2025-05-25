<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::table('financial_records', function (Blueprint $table) {
        $table->unsignedBigInteger('dai_ly_id')->nullable()->after('id');
        $table->foreign('dai_ly_id')->references('id')->on('dai_lies')->onDelete('set null');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('financial_records', function (Blueprint $table) {
            //
        });
    }
};
