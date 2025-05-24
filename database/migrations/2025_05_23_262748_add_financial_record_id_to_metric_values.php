<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('metric_values', function (Blueprint $table) {
            $table->foreignId('financial_record_id')
                ->nullable()
                ->constrained('financial_records')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('metric_values', function (Blueprint $table) {
            $table->dropForeign(['financial_record_id']);
            $table->dropColumn('financial_record_id');
        });
    }
};
