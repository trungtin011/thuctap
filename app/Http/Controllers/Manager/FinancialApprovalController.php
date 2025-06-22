<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\FinancialRecord;
use Illuminate\Http\Request;

class FinancialApprovalController extends Controller
{
    public function index()
    {
        // Lấy tất cả bản ghi tài chính chưa được duyệt (status = 'pending')
        // từ các nhân viên có vai trò Kinh Doanh (role_id = 7)
        $pendingRecords = FinancialRecord::where('status', 'pending')
            ->whereHas('submittedBy', function ($query) {
                $query->where('role_id', 7); // Vai trò Kinh Doanh
            })
            ->with(['department', 'platform', 'daiLy', 'office', 'route', 'submittedBy', 'expenses'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('manager.financial.index', compact('pendingRecords'));
    }

    public function show($id)
    {
        // Lấy bản ghi chi tiết dựa trên ID và đảm bảo nó được nhập bởi nhân viên có vai trò Kinh Doanh
        $financialRecord = FinancialRecord::whereHas('submittedBy', function ($query) {
                $query->where('role_id', 7); // Vai trò Kinh Doanh
            })
            ->with(['department', 'platform', 'daiLy', 'office', 'route', 'submittedBy', 'expenses'])
            ->findOrFail($id);

        return view('manager.financial.show', compact('financialRecord'));
    }

    public function approve($id)
    {
        // Tìm bản ghi theo ID
        $record = FinancialRecord::whereHas('submittedBy', function ($query) {
                $query->where('role_id', 7); // Vai trò Kinh Doanh
            })
            ->findOrFail($id);

        // Kiểm tra trạng thái để đảm bảo bản ghi chưa được xử lý
        if ($record->status !== 'pending') {
            return redirect()->route('manager.financial.index')->with('error', 'Bản ghi này đã được xử lý.');
        }

        // Cập nhật trạng thái thành 'manager_approved'
        $record->status = 'manager_approved';
        $record->save();

        return redirect()->route('manager.financial.index')->with('success', 'Bản ghi đã được phê duyệt và gửi lên Admin.');
    }

    public function reject(Request $request, $id)
    {
        // Tìm bản ghi theo ID
        $financialRecord = FinancialRecord::whereHas('submittedBy', function ($query) {
                $query->where('role_id', 7); // Vai trò Kinh Doanh
            })
            ->findOrFail($id);

        // Kiểm tra trạng thái để đảm bảo bản ghi chưa được xử lý
        if ($financialRecord->status !== 'pending') {
            return redirect()->route('manager.financial.index')->with('error', 'Bản ghi này đã được xử lý.');
        }

        // Cập nhật trạng thái thành 'rejected' và lưu ghi chú
        $financialRecord->update([
            'status' => 'rejected',
            'note' => $request->input('note', '')
        ]);

        return redirect()->route('manager.financial.index')
            ->with('success', 'Bản ghi đã bị từ chối.');
    }
}