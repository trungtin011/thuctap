<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FinancialRecord;
use App\Models\OfficeRevenue;
use App\Models\Trip;
use App\Models\FinancialTarget;

class ReportController extends Controller
{
    public function financial(Request $request)
    {
        // Lọc và tổng hợp theo đại lý, tuyến, phòng ban, phòng hàng
        // So sánh với mục tiêu (financial_targets)
        // ...truy vấn và xử lý dữ liệu...
        return view('reports.financial', [
            // 'data' => $data,
            // 'targets' => $targets,
        ]);
    }

    public function office(Request $request)
    {
        // Báo cáo doanh thu phòng hàng
        return view('reports.office');
    }

    public function trips(Request $request)
    {
        // Báo cáo số chuyến, số khách theo tuyến đường
        return view('reports.trips');
    }

    public function commission(Request $request)
    {
        $query = \App\Models\FinancialRecord::query()
            ->with(['daily', 'platform', 'route'])
            // Lấy cả bản ghi có commission = 0 hoặc null, nhưng hiển thị đúng giá trị
            ->where('status', 'admin_approved');

        // Lọc theo đại lý
        if ($request->filled('dai_ly_id')) {
            $query->where('dai_ly_id', $request->dai_ly_id);
        }
        // Lọc theo tuyến (nếu có trường route_id)
        if ($request->filled('route_id')) {
            $query->where('route_id', $request->route_id);
        }
        // Lọc theo thời gian
        if ($request->filled('start_date')) {
            $query->where('record_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('record_date', '<=', $request->end_date);
        }

        $records = $query->orderBy('record_date', 'desc')->paginate(20);

        $dailies = \App\Models\Daily::all();
        $routes = \App\Models\Route::all();

        // Đảm bảo trường commission luôn là số (không bị null)
        foreach ($records as $record) {
            if ($record->commission === null) {
                $record->commission = 0;
            }
        }

        return view('admin.reports.commission', compact('records', 'dailies', 'routes'));
    }
}
