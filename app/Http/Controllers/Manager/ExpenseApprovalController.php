<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseApprovalController extends Controller
{
    public function index()
    {
        // Lấy các bản ghi expenses từ phòng Marketing với trạng thái pending
        $expenses = Expense::whereHas('financialRecord.submittedBy', function ($query) {
            $query->where('department_id', 1); // 1 là ID của phòng Marketing
        })->where('status', 'pending')->with('financialRecord.submittedBy')->get();

        return view('manager.expenses.index', compact('expenses'));
    }

    public function show($id)
    {
        $expense = Expense::with([
            'financialRecord.department',
            'financialRecord.route',
            'financialRecord.platform',
            'financialRecord.submittedBy',
            'expenseType'
        ])->findOrFail($id);

        // Kiểm tra quyền của manager
        if (!Auth::user()->role->permissions()->whereIn('code', ['view_records', 'approve_records'])->exists()) {
            abort(403, 'Unauthorized action.');
        }

        // Kiểm tra xem expense có thuộc phòng Marketing không
        if ($expense->financialRecord->submittedBy->department_id != 1) {
            abort(403, 'This expense is not from Marketing department.');
        }

        return view('manager.expenses.show', compact('expense'));
    }

    public function approve($id)
    {
        // Tìm bản ghi theo ID
        $expense = Expense::whereHas('financialRecord.submittedBy', function ($query) {
            $query->where('department_id', 1); // 1 là ID của phòng Marketing
        })->findOrFail($id);

        // Kiểm tra quyền của manager
        if (!Auth::user()->role->permissions()->where('code', 'approve_records')->exists()) {
            abort(403, 'Unauthorized action.');
        }

        // Kiểm tra trạng thái để đảm bảo bản ghi chưa được xử lý
        if ($expense->status !== 'pending') {
            return redirect()->route('manager.expenses.index')->with('error', 'Bản ghi này đã được xử lý.');
        }

        // Cập nhật trạng thái thành 'manager_approved'
        $expense->status = 'manager_approved';
        $expense->save();

        return redirect()->route('manager.expenses.index')->with('success', 'Bản ghi đã được phê duyệt và gửi lên Admin.');
    }
public function reject(Request $request, $id)
    {
        // Tìm bản ghi theo ID
        $expense = Expense::whereHas('financialRecord.submittedBy', function ($query) {
            $query->where('department_id', 1); // 1 là ID của phòng Marketing
        })->findOrFail($id);

        // Kiểm tra quyền của manager
        if (!Auth::user()->role->permissions()->where('code', 'approve_records')->exists()) {
            abort(403, 'Unauthorized action.');
        }

        // Kiểm tra trạng thái để đảm bảo bản ghi chưa được xử lý
        if ($expense->status !== 'pending') {
            return redirect()->route('manager.expenses.index')->with('error', 'Bản ghi này đã được xử lý.');
        }

        // Validate lý do từ chối
        $request->validate([
            'reject_reason' => 'required|string|max:255',
        ]);

        // Cập nhật trạng thái thành 'rejected' và lưu lý do từ chối
        $expense->update([
            'status' => 'rejected',
            'reject_reason' => $request->input('reject_reason', ''),
        ]);

        return redirect()->route('manager.expenses.index')->with('success', 'Bản ghi đã bị từ chối.');
    }
}

