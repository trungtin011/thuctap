<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialRecord;
use Illuminate\Http\Request;
use Carbon\CarbonPeriod;


class FinancialAdminController extends Controller
{
public function index(Request $request)
    {
        $departments = \App\Models\Department::all();
        $platforms = \App\Models\Platform::all();

        $query = FinancialRecord::query()->with(['department', 'platform', 'submittedBy']);

        // Lọc theo trạng thái
        $status = $request->input('status', 'manager_approved');
        if ($status) {
            $query->where('status', $status);
        }

        // Lọc theo phòng ban
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Lọc theo nền tảng
        if ($request->filled('platform_id')) {
            $query->where('platform_id', $request->platform_id);
        }

        // Lọc theo ngày bắt đầu
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        // Lọc theo ngày kết thúc
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $financialRecords = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.financial.index', compact('financialRecords', 'departments', 'platforms', 'status'));
    }

    public function approve($id)
    {
        $record = FinancialRecord::findOrFail($id);
     
        $record->status = 'admin_approved'; 
        $record->save();

        return redirect()->route('admin.financial.index')->with('success', 'Đơn đã được admin phê duyệt thành công.');
    }
public function history()
{
    // Lấy các đơn đã được admin phê duyệt
    $financialRecords = FinancialRecord::where('status', 'admin_approved')->get();

    return view('admin.financial.history', compact('financialRecords'));
}
public function totalRevenue(Request $request)
{
    $start = $request->input('start_date');
    $end = $request->input('end_date');

    $query = FinancialRecord::where('status', 'admin_approved');

    if ($start && $end) {
        $query->whereBetween('record_date', [$start, $end]);
    } elseif ($start) {
        $query->where('record_date', '>=', $start);
    } elseif ($end) {
        $query->where('record_date', '<=', $end);
    }

    $records = $query->get();

    $totalRevenue = $records->sum('revenue');

    $labels = [];
    $data = [];

    // Lấy các ngày có dữ liệu thực tế theo record_date
    if ($records->count() > 0) {
        $dates = $records->pluck('record_date')->unique()->sort()->values();

        foreach ($dates as $date) {
            $labels[] = $date;
            $dailySum = $records->where('record_date', $date)->sum('revenue');
            $data[] = $dailySum;
        }
    }

    return view('admin.financial.total_revenue', compact('totalRevenue', 'labels', 'data'));
}

}
