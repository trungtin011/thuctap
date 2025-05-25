<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('platform_id')->constrained()->onDelete('cascade');
            $table->string('name', 100);
            $table->string('unit', 50)->nullable();
            $table->enum('data_type', ['int', 'float', 'string']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_metrics');
    }
};