<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // Hiển thị form đăng ký
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Xử lý đăng ký
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:employees',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $employee = Employee::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'department_id' => 1, // Giả sử mặc định là phòng ban ID=1
            'role_id' => 1, // Giả sử mặc định là vai trò ID=1 (nhân viên)
        ]);

        Auth::login($employee);

        return redirect()->route('welcome')->with('success', 'Đăng ký thành công!');
    }

    // Hiển thị form đăng nhập
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Xử lý đăng nhập
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        Log::info('Login attempt', ['email' => $credentials['email']]);

        if (Auth::attempt($credentials)) {
            Log::info('Login successful', ['email' => $credentials['email']]);
            $request->session()->regenerate();

            // Lấy thông tin người dùng
            $user = Auth::user();

            // Điều hướng dựa trên vai trò
            if ($user->role_id == 3) { // Admin
                return redirect()->route('dashboard')->with('success', 'Đăng nhập thành công!');
            } elseif ($user->role_id == 2) { // Manager
                return redirect()->route('dashboard')->with('success', 'Đăng nhập thành công!');
            } elseif ($user->role_id == 1) { // Employee
                return redirect()->route('welcome')->with('success', 'Đăng nhập thành công!');
            }
        }

        Log::warning('Login failed', ['email' => $credentials['email']]);

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không đúng.',
        ])->onlyInput('email');
    }

    // Đăng xuất
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('success', 'Đăng xuất thành công!');
    }
}
