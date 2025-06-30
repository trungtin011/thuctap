<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\OfficeRevenue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OfficeRevenueController extends Controller
{

    public function index()
    {
        // Lấy danh sách bản ghi office_revenues với trạng thái pending
        $officeRevenues = OfficeRevenue::where('status', 'pending')
            ->with(['offices', 'department', 'submittedBy'])
            ->get();

        return view('manager.office_revenues.index', compact('officeRevenues'));
    }

    public function show($id)
    {
        // Kiểm tra quyền manager
        if (Auth::user()->role->level !== 'manager') {
            return redirect()->back()->with('error', 'Bạn không có quyền xem chi tiết.');
        }

        $officeRevenue = OfficeRevenue::with(['offices', 'department', 'submittedBy'])
            ->findOrFail($id);

        return view('manager.office_revenues.show', compact('officeRevenue'));
    }

    public function approve($id)
    {
        Log::info("Attempting to approve OfficeRevenue ID: {$id} by user: " . Auth::id());

        if (Auth::user()->role->level !== 'manager') {
            Log::warning("Unauthorized approve attempt by user: " . Auth::id());
            return redirect()->route('manager.office_revenues.index')->with('error', 'Bạn không có quyền phê duyệt.');
        }

        $officeRevenue = OfficeRevenue::with(['submittedBy', 'department'])->findOrFail($id);

        if ($officeRevenue->status !== 'pending') {
            Log::warning("OfficeRevenue ID: {$id} is not in pending status. Current status: {$officeRevenue->status}");
            return redirect()->route('manager.office_revenues.index')->with('error', 'Bản ghi này đã được xử lý.');
        }

        $officeRevenue->status = 'manager_approved';
        $officeRevenue->reject_reason = null;
        $officeRevenue->save();

        Log::info("OfficeRevenue ID: {$id} approved successfully.");
        return redirect()->route('manager.office_revenues.index')->with('success', 'Bản ghi đã được phê duyệt và gửi lên Admin.');
    }

    public function reject(Request $request, $id)
    {
        // Kiểm tra quyền manager
        if (Auth::user()->role->level !== 'manager') {
            return redirect()->back()->with('error', 'Bạn không có quyền từ chối.');
        }

        $officeRevenue = OfficeRevenue::findOrFail($id);

        // Kiểm tra trạng thái
        if ($officeRevenue->status !== 'pending') {
            return redirect()->back()->with('error', 'Bản ghi này không thể từ chối.');
        }

        // Validate lý do từ chối
        $request->validate([
            'reject_reason' => 'required|string|max:255',
        ]);

        // Cập nhật trạng thái thành rejected và lưu lý do
        $officeRevenue->update([
            'status' => 'rejected',
            'reject_reason' => $request->reject_reason,
        ]);

        return redirect()->back()->with('success', 'Bản ghi đã bị từ chối.');
    }
}
