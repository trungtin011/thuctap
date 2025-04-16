<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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
        $errors = [];

        // Kiểm tra name
        if (empty($request->name) || strlen($request->name) > 255) {
            $errors['name'] = 'Tên không được để trống và tối đa 255 ký tự.';
        }

        // Kiểm tra email
        if (empty($request->email) || !filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không hợp lệ.';
        } elseif (User::where('email', $request->email)->exists()) {
            $errors['email'] = 'Email đã tồn tại.';
        }

        // Kiểm tra password
        if (empty($request->password) || strlen($request->password) < 8) {
            $errors['password'] = 'Mật khẩu phải có ít nhất 8 ký tự.';
        }

        // Kiểm tra password cấp 2
        if (empty($request->password_level2) || strlen($request->password_level2) < 8) {
            $errors['password_level2'] = 'Mật khẩu cấp 2 phải có ít nhất 8 ký tự.';
        } elseif ($request->password_level2 === $request->password) {
            $errors['password_level2'] = 'Mật khẩu cấp 2 không được giống mật khẩu đăng nhập.';
        }

        // Nếu có lỗi, trả về view với lỗi
        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors)->withInput();
        }

        // Nếu không có lỗi, tiếp tục tạo user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'password_level2' => Hash::make($request->password_level2),
        ]);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Đăng ký thành công! Hãy đăng nhập.');
    }
}
