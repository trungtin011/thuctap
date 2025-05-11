<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Validate dữ liệu đầu vào
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed', // password_confirmation phải có
        ]);

        // Tạo người dùng mới
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Mã hóa mật khẩu
            'role' => 'user', // Mặc định là 'user', có thể thay đổi sau
        ]);

        // Đăng nhập người dùng
        Auth::login($user);

        return redirect()->route('home')->with('success', 'Đăng ký thành công! Hãy đăng nhập.');
    }
}
