<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialRecord;
use Illuminate\Http\Request;
use Carbon\CarbonPeriod;


class FinancialAdminController extends Controller
{
public function index()
    {
       
        $financialRecords = FinancialRecord::where('status', 'manager_approved')->get();

        return view('admin.financial.index', compact('financialRecords'));
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
        $query->whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59']);
    }

    $records = $query->get();

    $totalRevenue = $records->sum('amount');

    $labels = [];
    $data = [];

    if ($start && $end) {
        $period = \Carbon\CarbonPeriod::create($start, $end);

        foreach ($period as $date) {
            $labels[] = $date->format('Y-m-d');

            $dailySum = $records->filter(function ($record) use ($date) {
                return $record->created_at->format('Y-m-d') === $date->format('Y-m-d');
            })->sum('amount');

            $data[] = $dailySum;
        }
    }

    return view('admin.financial.total_revenue', compact('totalRevenue', 'labels', 'data'));
}

}
