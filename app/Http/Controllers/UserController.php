<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Danh sách user
    public function index()
    {
        $users = Employee::with('department', 'role')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    // Form tạo user
    public function create()
    {
        $departments = Department::all();
        $roles = Role::all();
        return view('admin.users.create', compact('departments', 'roles'));
    }

    // Lưu user mới
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:employees,email',
            'password' => 'required|string|min:8|confirmed',
            'department_id' => 'required|exists:departments,id',
            'role_id' => 'required|exists:roles,id',
        ]);
        Employee::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'department_id' => $request->department_id,
            'role_id' => $request->role_id,
        ]);
        return redirect()->route('users.index')->with('success', 'Tạo người dùng thành công!');
    }

    // Form sửa user
    public function edit(Employee $user)
    {
        $departments = Department::all();
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'departments', 'roles'));
    }

    // Cập nhật user
    public function update(Request $request, Employee $user)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:employees,email,' . $user->id,
            'department_id' => 'required|exists:departments,id',
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->department_id = $request->department_id;
        $user->role_id = $request->role_id;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();
        return redirect()->route('users.index')->with('success', 'Cập nhật người dùng thành công!');
    }

    // Xóa user
    public function destroy(Employee $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Xóa người dùng thành công!');
    }
}
