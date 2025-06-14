<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\FinancialRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarketingFinancialController extends Controller
{
    public function index()
    {
        // Lấy các bản ghi từ financial_records thuộc phòng Kế toán
        $pendingRecords = FinancialRecord::with(['office', 'dai_ly', 'expenses'])
            ->where('department_id', 3)
            ->orderBy('record_date', 'desc')
            ->get();

        // Map dữ liệu để khớp với template
        $records = $pendingRecords->map(function ($record) {
            // Lấy tổng phí từ bảng expenses
            $expense_total = $record->expenses->sum('amount') ?? 0;
            // Lấy transferred_amount từ JSON trong note (nếu có)
            $noteData = json_decode($record->note, true);
            $transferred_amount = $noteData['transfer_total'] ?? 0;

            return [
                'id' => $record->id,
                'department' => ['name' => 'Kế toán'],
                'record_date' => $record->record_date,
                'total_amount' => $record->revenue,
                'transferred_amount' => $transferred_amount,
                'fee' => $expense_total,
                'source_total' => $record->dai_ly->ten_dai_ly ?? 'N/A',
                'is_processed' => $record->status !== 'pending',
                'action' => $record->status,
                'note' => $record->note,
            ];
        });

        return view('manager.marketing.index', compact('records'));
    }

    public function show($id)
    {
        $record = FinancialRecord::with(['office', 'dai_ly', 'expenses'])
            ->where('department_id', 3)
            ->findOrFail($id);

        // Lấy tổng phí từ bảng expenses
        $expense_total = $record->expenses->sum('amount') ?? 0;
        // Lấy transferred_amount từ JSON trong note
        $noteData = json_decode($record->note, true);
        $transferred_amount = $noteData['transfer_total'] ?? 0;

        $financialRecord = [
            'id' => $record->id,
            'department' => ['name' => 'Kế toán'],
            'record_date' => $record->record_date,
            'total_amount' => $record->revenue,
            'transferred_amount' => $transferred_amount,
            'fee' => $expense_total,
            'source_total' => $record->dai_ly->ten_dai_ly ?? 'N/A',
            'action' => $record->status,
            'note' => $record->note,
        ];

        return view('manager.marketing.show', compact('financialRecord'));
    }

    public function approve($id)
    {
        $record = FinancialRecord::where('department_id', 3)
            ->findOrFail($id);

        // Kiểm tra xem bản ghi đã được xử lý chưa
        if ($record->status !== 'pending') {
            return redirect()->route('manager.marketing.index')->with('error', 'Bản ghi này đã được xử lý.');
        }

        // Cập nhật trạng thái
        $record->update([
            'status' => 'manager_approved',
            'submitted_by' => Auth::id(),
        ]);

        return redirect()->route('manager.marketing.index')->with('success', 'Đơn đã được gửi lên Admin để phê duyệt.');
    }

    public function reject(Request $request, $id)
    {
        $record = FinancialRecord::where('department_id', 3)
            ->findOrFail($id);

        // Kiểm tra xem bản ghi đã được xử lý chưa
        if ($record->status !== 'pending') {
            return redirect()->back()->with('error', 'Bản ghi này đã được xử lý.');
        }

        // Validate ghi chú
        $request->validate(['note' => 'required|string|max:255']);

        // Cập nhật trạng thái và ghi chú
        $record->update([
            'status' => 'rejected',
            'note' => $request->input('note'),
            'submitted_by' => Auth::id(),
        ]);

        return redirect()->route('manager.marketing.index')->with('success', 'Bản ghi đã bị từ chối.');
    }
}