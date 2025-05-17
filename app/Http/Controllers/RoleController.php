<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Department;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('department')->paginate(10);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('admin.roles.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'level' => 'required|in:employee,manager,admin',
            'department_id' => 'required|exists:departments,id',
            'description' => 'nullable|string',
        ]);
        Role::create($request->all());
        return redirect()->route('roles.index')->with('success', 'Tạo vai trò thành công!');
    }

    public function edit(Role $role)
    {
        $departments = Department::all();
        return view('admin.roles.edit', compact('role', 'departments'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'level' => 'required|in:employee,manager,admin',
            'department_id' => 'required|exists:departments,id',
            'description' => 'nullable|string',
        ]);
        $role->update($request->all());
        return redirect()->route('roles.index')->with('success', 'Cập nhật vai trò thành công!');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Xóa vai trò thành công!');
    }
}
