<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showAdminLoginForm()
    {
        return view('auth.login_admin');
    }

    // Đăng nhập người dùng
    public function loginUser(Request $request)
    {
        $errors = [];

        // Kiểm tra email
        if (empty($request->email) || !filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không hợp lệ.';
        }

        // Kiểm tra password
        if (empty($request->password) || strlen($request->password) < 8) {
            $errors['password'] = 'Mật khẩu phải có ít nhất 8 ký tự.';
        }

        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }


        $user = User::where('email', $request->email)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user);
            return redirect()->route('home')->with('success', 'Đăng nhập thành công!');
        }

        return back()->withErrors(['email' => 'Email hoặc mật khẩu không đúng'])->withInput();
    }

    // Đăng nhập quản trị viên
    public function loginAdmin(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:8',
        ]);

        $admin = Admin::where('username', $request->username)->first();
        if ($admin && Hash::check($request->password, $admin->password)) {
            Auth::guard('admin')->login($admin);

            if ($admin->role === 'superadmin') {
                return redirect()->route('superadmin.dashboard')->with('success', 'Đăng nhập Super Admin thành công!');
            }
            return redirect()->route('admin.dashboard')->with('success', 'Đăng nhập Admin thành công!');
        }

        return back()->withErrors(['username' => 'Tên đăng nhập hoặc mật khẩu không đúng'])->withInput();
    }

    // Đăng xuất
    public function logout(Request $request)
    {
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
            return redirect()->route('login')->with('success', 'Bạn đã đăng xuất.');
        } elseif (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
            return redirect()->route('login.admin')->with('success', 'Bạn đã đăng xuất.');
        }

        return redirect()->route('home')->with('error', 'Bạn chưa đăng nhập.');
    }
}
