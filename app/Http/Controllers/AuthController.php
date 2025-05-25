<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
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

        if (\Illuminate\Support\Facades\Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = \Illuminate\Support\Facades\Auth::user();

            // Điều hướng dựa trên vai trò (role_id động)
            $roleId = $user->role_id;

            // Nếu là admin (role_id = 3 hoặc role có level = 'admin')
            $role = \App\Models\Role::find($roleId);
            if ($role && $role->level === 'admin') {
                return redirect()->route('dashboard')->with('success', 'Đăng nhập thành công!');
            } elseif ($role && $role->level === 'manager') {
                return redirect()->route('dashboard')->with('success', 'Đăng nhập thành công!');
            } elseif ($role && $role->level === 'employee') {
                return redirect()->route('welcome')->with('success', 'Đăng nhập thành công!');
            } else {
                \Illuminate\Support\Facades\Auth::logout();
                return back()->withErrors(['email' => 'Tài khoản không có quyền truy cập (role không hợp lệ).'])->onlyInput('email');
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

        Log::info('Register attempt', ['email' => $request->email]);

        $defaultDepartment = \App\Models\Department::first();
        Log::info('Default department', ['department' => $defaultDepartment]);

        $defaultRole = null;
        if ($defaultDepartment) {
            $defaultRole = \App\Models\Role::where('level', 'employee')
                ->where('department_id', $defaultDepartment->id)
                ->first();
            Log::info('Default role', ['role' => $defaultRole]);
        }

        if (!$defaultDepartment || !$defaultRole) {
            Log::error('Registration failed: No default department or role');
            return back()->withErrors(['register' => 'Không tìm thấy phòng ban hoặc vai trò mặc định. Vui lòng liên hệ quản trị viên.'])->withInput();
        }

        $employee = \App\Models\Employee::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'department_id' => $defaultDepartment->id,
            'role_id' => $defaultRole->id,
        ]);

        \Illuminate\Support\Facades\Auth::login($employee);

        Log::info('Registration successful', ['email' => $request->email]);
        return redirect()->route('welcome')->with('success', 'Đăng ký thành công!');
    }
}
