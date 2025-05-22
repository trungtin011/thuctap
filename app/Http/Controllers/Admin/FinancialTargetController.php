<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FinancialTarget;
use App\Models\Department;

class FinancialTargetController extends Controller
{
    public function create()
    {
        $departments = Department::all();
        return view('admin.targets.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'year' => 'required|digits:4|integer|min:2020',
            'target_amount' => 'required|numeric|min:0',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        FinancialTarget::updateOrCreate(
            [
                'year' => $request->year,
                'department_id' => $request->department_id,
            ],
            [
                'target_amount' => $request->target_amount,
            ]
        );

        return redirect()->route('admin.targets.create')->with('success', 'Đã lưu mục tiêu doanh thu!');
    }

    public function destroy($id)
    {
        $target = \App\Models\FinancialTarget::findOrFail($id);
        $target->delete();
        return redirect()->back()->with('success', 'Đã xóa mục tiêu thành công!');
    }
}
