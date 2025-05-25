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
    Schema::create('dai_lies', function (Blueprint $table) {
        $table->id();
        $table->string('ten_dai_ly');
        $table->string('email')->unique();
        $table->string('so_dien_thoai');
        $table->string('dia_chi');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dai_lies');
    }
};
