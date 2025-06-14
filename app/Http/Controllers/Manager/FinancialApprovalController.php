<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\FinancialRecord;
use Illuminate\Http\Request;

class FinancialApprovalController extends Controller
{
    public function index()
    {
        // Lấy tất cả bản ghi chưa được duyệt từ các nhân viên thuộc phòng ban Kinh Doanh (department_id = 4)
        $pendingRecords = FinancialRecord::where('status', 'pending')
            ->whereHas('submittedBy', function ($query) {
                $query->where('department_id', 4); // Phòng ban Kinh Doanh
            })
            ->with(['department', 'platform', 'expenses', 'submittedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('manager.financial.index', compact('pendingRecords'));
    }

    public function show($id)
    {
        // Lấy bản ghi chi tiết và kiểm tra xem nó có được nhập bởi nhân viên thuộc phòng ban Kinh Doanh không
        $financialRecord = FinancialRecord::with(['expenses', 'department', 'platform', 'submittedBy'])
            ->whereHas('submittedBy', function ($query) {
                $query->where('department_id', 4); // Phòng ban Kinh Doanh
            })
            ->findOrFail($id);

        return view('manager.financial.show', compact('financialRecord'));
    }

    public function approve($id)
    {
        $record = FinancialRecord::findOrFail($id);
        $record->status = 'manager_approved';
        $record->save();

        return redirect()->route('manager.financial.index')->with('success', 'Đơn đã được gửi lên Admin để phê duyệt.');
    }

    public function reject(Request $request, $id)
    {
        $financialRecord = FinancialRecord::findOrFail($id);

        if ($financialRecord->status !== 'pending') {
            return redirect()->back()->with('error', 'Bản ghi này đã được xử lý.');
        }

        $financialRecord->update([
            'status' => 'rejected',
            'note' => $request->input('note')
        ]);

        return redirect()->route('manager.financial.index')
            ->with('success', 'Bản ghi đã bị từ chối.');
    }
}