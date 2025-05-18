<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Department::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $departments = $query->paginate(10); // Adjust the number of items per page as needed
        return view('admin.departments.index', compact('departments'));
    }


    public function create()
    {
        return view('admin.departments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);
        Department::create($request->all());
        return redirect()->route('departments.index')->with('success', 'Tạo phòng ban thành công!');
    }

    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);
        $department->update($request->all());
        return redirect()->route('departments.index')->with('success', 'Cập nhật phòng ban thành công!');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->route('departments.index')->with('success', 'Xóa phòng ban thành công!');
    }
}
