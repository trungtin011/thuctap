<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\FinancialRecord;
use App\Models\Expense;
use App\Models\Department;
use App\Models\Platform;
use App\Models\ExpenseType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinancialController extends Controller
{
    public function index(Request $request)
    {
        $employee = Auth::user();

        // Xác thực các tham số lọc
        $request->validate([
            'platform_id' => 'nullable|exists:platforms,id',
            'status' => 'nullable|in:pending,manager_approved,admin_approved,rejected',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Bắt đầu truy vấn
        $query = FinancialRecord::where('submitted_by', $employee->id)
            ->with(['department', 'platform', 'expenses', 'submittedBy'])
            ->orderBy('record_date', 'desc');

        // Áp dụng bộ lọc
        if ($request->filled('platform_id')) {
            $query->where('platform_id', $request->platform_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->where('record_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('record_date', '<=', $request->end_date);
        }

        // Lấy kết quả
        $financialRecords = $query->get();
        $platforms = Platform::all();
        $expenseTypes = ExpenseType::all();

        return view('employee.financial.index', compact('financialRecords', 'platforms', 'expenseTypes'));
    }

    public function create()
    {
        $employee = Auth::user();
        $department = Department::find($employee->department_id);
        if (!$department) {
            abort(403, 'Bạn không thuộc phòng ban nào.');
        }
        $platforms = Platform::all();
        $expenseTypes = ExpenseType::all();
        return view('employee.financial.create', compact('department', 'platforms', 'expenseTypes'));
    }

    public function store(Request $request)
    {
        $employee = Auth::user();

        $request->validate([
            'department_id' => [
                'required',
                'exists:departments,id',
                function ($attribute, $value, $fail) use ($employee) {
                    if ($value != $employee->department_id) {
                        $fail('Bạn chỉ có thể nhập dữ liệu cho phòng ban của mình.');
                    }
                },
            ],
            'platform_id' => 'required|exists:platforms,id',
            'revenue' => 'required|numeric|min:0',
            'record_date' => 'required|date',
            'record_time' => 'required',
            'note' => 'nullable|string',
            'expenses' => 'nullable|array',
            'expenses.*.expense_type_id' => 'required|exists:expense_types,id',
            'expenses.*.amount' => 'required|numeric|min:0',
            'expenses.*.description' => 'nullable|string',
        ]);

        $financialRecord = FinancialRecord::create([
            'department_id' => $request->department_id,
            'platform_id' => $request->platform_id,
            'revenue' => $request->revenue,
            'record_date' => $request->record_date,
            'record_time' => $request->record_time,
            'note' => $request->note,
            'status' => 'pending',
            'submitted_by' => Auth::id(),
        ]);

        if ($request->has('expenses')) {
            foreach ($request->expenses as $expense) {
                Expense::create([
                    'financial_record_id' => $financialRecord->id,
                    'expense_type_id' => $expense['expense_type_id'],
                    'amount' => $expense['amount'],
                    'description' => $expense['description'],
                ]);
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Bản ghi doanh thu và chi phí đã được thêm thành công.'
            ]);
        }

        return redirect()->route('employee.financial.create')
            ->with('success', 'Bản ghi doanh thu và chi phí đã được thêm thành công.');
    }

    public function edit($id)
    {
        $employee = Auth::user();
        $financialRecord = FinancialRecord::where('id', $id)
            ->where('submitted_by', $employee->id)
            ->with(['expenses', 'department', 'platform'])
            ->firstOrFail();

        if ($financialRecord->status !== 'pending') {
            abort(403, 'Bạn chỉ có thể sửa bản ghi đang chờ duyệt.');
        }

        $department = Department::find($employee->department_id);
        $platforms = Platform::all();
        $expenseTypes = ExpenseType::all();

        return view('employee.financial.edit', compact('financialRecord', 'department', 'platforms', 'expenseTypes'));
    }

    public function update(Request $request, $id)
    {
        $employee = Auth::user();
        $financialRecord = FinancialRecord::where('id', $id)
            ->where('submitted_by', $employee->id)
            ->firstOrFail();

        if ($financialRecord->status !== 'pending') {
            abort(403, 'Bạn chỉ có thể sửa bản ghi đang chờ duyệt.');
        }

        $request->validate([
            'department_id' => [
                'required',
                'exists:departments,id',
                function ($attribute, $value, $fail) use ($employee) {
                    if ($value != $employee->department_id) {
                        $fail('Bạn chỉ có thể nhập dữ liệu cho phòng ban của mình.');
                    }
                },
            ],
            'platform_id' => 'required|exists:platforms,id',
            'revenue' => 'required|numeric|min:0',
            'record_date' => 'required|date',
            'record_time' => 'required',
            'note' => 'nullable|string',
            'expenses' => 'nullable|array',
            'expenses.*.expense_type_id' => 'required|exists:expense_types,id',
            'expenses.*.amount' => 'required|numeric|min:0',
            'expenses.*.description' => 'nullable|string',
        ]);

        $financialRecord->update([
            'department_id' => $request->department_id,
            'platform_id' => $request->platform_id,
            'revenue' => $request->revenue,
            'record_date' => $request->record_date,
            'record_time' => $request->record_time,
            'note' => $request->note,
        ]);

        // Delete existing expenses
        $financialRecord->expenses()->delete();

        // Create new expenses
        if ($request->has('expenses')) {
            foreach ($request->expenses as $expense) {
                Expense::create([
                    'financial_record_id' => $financialRecord->id,
                    'expense_type_id' => $expense['expense_type_id'],
                    'amount' => $expense['amount'],
                    'description' => $expense['description'],
                ]);
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Bản ghi doanh thu và chi phí đã được cập nhật thành công.'
            ]);
        }

        return redirect()->route('employee.financial.index')
            ->with('success', 'Bản ghi doanh thu và chi phí đã được cập nhật thành công.');
    }

    public function destroy(Request $request, $id)
    {
        $employee = Auth::user();
        $financialRecord = FinancialRecord::where('id', $id)
            ->where('submitted_by', $employee->id)
            ->firstOrFail();

        if ($financialRecord->status !== 'pending') {
            abort(403, 'Bạn chỉ có thể xóa bản ghi đang chờ duyệt.');
        }

        $financialRecord->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Bản ghi đã được xóa thành công.'
            ]);
        }

        return redirect()->route('employee.financial.index')
            ->with('success', 'Bản ghi đã được xóa thành công.');
    }
}
