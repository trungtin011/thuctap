<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserControllerUser extends Controller
{
    // Danh sách user
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    // Xem chi tiết user
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    // Form sửa user
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    // Lưu user đã sửa
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->password) {
            $user->password = bcrypt($request->password);
        }

        if ($request->password_level2) {
            $user->password_level2 = bcrypt($request->password_level2);
        }

        if ($request->pin) {
            $user->pin = bcrypt($request->pin);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật người dùng thành công!');
    }

    // Cộng tiền cho user
    public function addBalance(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'amount' => 'required|numeric|min:1000',
        ]);

        $user->balance += $request->amount;
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Đã cộng ' . number_format($request->amount) . ' VNĐ vào tài khoản!');
    }

    // Khóa hoặc mở khóa tài khoản
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->status = ($user->status === 'locked') ? 'active' : 'locked';
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Trạng thái tài khoản đã được cập nhật!');
    }
}
