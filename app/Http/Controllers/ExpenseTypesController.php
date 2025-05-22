<?php

namespace App\Http\Controllers;

use App\Models\ExpenseType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseTypesController extends Controller
{
    /**
     * Hiển thị danh sách các loại chi phí.
     */
    public function index(Request $request)
    {
        $query = ExpenseType::query();

        // Chức năng tìm kiếm
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $expenseTypes = $query->paginate(10); // Số mục hiển thị trên mỗi trang
        return view('admin.expense-types.index', compact('expenseTypes'));
    }


    /**
     * Hiển thị form tạo loại chi phí mới.
     */
    public function create()
    {
        return view('admin.expense-types.create');
    }

    /**
     * Lưu loại chi phí mới vào cơ sở dữ liệu.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:expense_types',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        ExpenseType::create([
            'name' => $request->name,
        ]);

        return redirect()->route('expense-types.index')->with('success', 'Tạo loại chi phí thành công');
    }

    /**
     * Hiển thị form sửa loại chi phí.
     */
    public function edit(ExpenseType $expenseType)
    {
        return view('admin.expense-types.edit', compact('expenseType'));
    }

    /**
     * Cập nhật loại chi phí.
     */
    public function update(Request $request, ExpenseType $expenseType)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:expense_types,name,' . $expenseType->id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $expenseType->update([
            'name' => $request->name,
        ]);

        return redirect()->route('expense-types.index')->with('success', 'Cập nhật loại chi phí thành công');
    }

    /**
     * Xóa loại chi phí.
     */
    public function destroy(ExpenseType $expenseType)
    {
        $expenseType->delete();
        return redirect()->route('expense-types.index')->with('success', 'Xóa loại chi phí thành công');
    }
}
