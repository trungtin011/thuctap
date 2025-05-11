<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function loginUser(Request $request)
    {
        // Validate email và password
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        // Kiểm tra thông tin đăng nhập, nếu là admin thì chuyển hướng đến trang admin
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Đăng nhập thành công với tư cách quản trị viên.');
            }

            // Nếu không phải admin, chuyển hướng đến trang người dùng
            return redirect()->route('home')->with('success', 'Đăng nhập thành công.');
        }

        return back()->withErrors(['email' => 'Email hoặc mật khẩu không đúng'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect()->route('login')->with('success', 'Bạn đã đăng xuất.');
    }
}
