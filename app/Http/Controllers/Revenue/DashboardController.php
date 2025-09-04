<?php

namespace App\Http\Controllers\Revenue;

use App\Http\Controllers\Controller;
use App\Models\FinancialRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Lấy thông tin nhân viên đang đăng nhập
        $employee = Auth::user();

        // Lấy dữ liệu thống kê cho các bản ghi đã được admin duyệt
        $stats = FinancialRecord::where('status', 'admin_approved')
            ->with(['expenses', 'platform'])
            ->select([
                DB::raw('SUM(revenue) as total_revenue'), // Tổng doanh thu
                DB::raw('SUM((SELECT SUM(amount) FROM expenses WHERE expenses.financial_record_id = financial_records.id)) as total_expenses'), // Tổng chi phí
                DB::raw('AVG(roas) as avg_roas'), // ROAS trung bình
                DB::raw('COUNT(*) as record_count') // Số lượng bản ghi
            ])
            ->first();

        // Lấy dữ liệu doanh thu theo tháng
        $monthlyRevenue = FinancialRecord::where('status', 'admin_approved')
            ->select([
                DB::raw("DATE_FORMAT(record_date, '%Y-%m') as month"),
                DB::raw('SUM(revenue) as total_revenue')
            ])
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // Lấy danh sách các năm có bản ghi admin_approved
        $years = FinancialRecord::where('status', 'admin_approved')
            ->select(DB::raw("YEAR(record_date) as year"))
            ->groupBy('year')
            ->orderBy('year', 'asc')
            ->pluck('year');

        // Lấy danh sách 6 bản ghi gần đây nhất đã được admin duyệt
        $recentRecords = FinancialRecord::where('status', 'admin_approved')
            ->with(['platform', 'expenses'])
            ->orderBy('record_date', 'desc') // Sửa created_at thành record_date
            ->take(6)
            ->get();
            
        // Trả về giao diện dashboard với dữ liệu thống kê
        return view('admin.index', [
            'total_revenue' => $stats->total_revenue ?? 0,
            'total_expenses' => $stats->total_expenses ?? 0,
            'avg_roas' => $stats->avg_roas ? number_format($stats->avg_roas, 2) : 'N/A',
            'record_count' => $stats->record_count ?? 0,
            'recent_records' => $recentRecords,
            'monthly_revenue' => $monthlyRevenue,
            'years' => $years
        ]);
    }
}
