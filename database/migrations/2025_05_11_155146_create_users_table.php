<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // ID của người dùng (tự tăng)
            $table->string('name'); // Tên người dùng
            $table->string('email')->unique(); // Email duy nhất
            $table->string('password'); // Mật khẩu
            $table->string('role')->default('user'); // Phân quyền (user, admin, v.v.)
            $table->rememberToken(); // Cho chức năng nhớ đăng nhập
            $table->timestamps(); // Thời gian tạo và cập nhật
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
