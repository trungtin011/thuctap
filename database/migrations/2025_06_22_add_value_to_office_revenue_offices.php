<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('office_revenue_offices', function (Blueprint $table) {
            $table->decimal('value', 15, 2)->default(0.00)->after('office_id');
        });
    }

    public function down(): void
    {
        Schema::table('office_revenue_offices', function (Blueprint $table) {
            $table->dropColumn('value');
        });
    }
};