<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\FinancialRecord;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;

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

    // Thêm phương thức này vào controller
    public function setGoal(Request $request)
    {
        $request->validate([
            'year_goal' => 'required|numeric|min:0'
        ]);
        // Lưu mục tiêu vào cache hoặc file hoặc bảng cấu hình (ở đây dùng cache cho đơn giản)
        cache(['year_goal_' . date('Y') => $request->year_goal], now()->addYear());

        return redirect()->back()->with('success', 'Đã lưu mục tiêu doanh thu năm!');
    }

    public function totalRevenue(Request $request)
    {
        // Lấy các tham số từ request
        $start = $request->input('start_date');
        $end = $request->input('end_date');
        $platform = $request->input('platform');
        $yearFilter = $request->input('yearFilter', 'all');
        $timeFilter = $request->input('timeFilter', 'all');

        // Lấy danh sách nền tảng
        $platforms = \App\Models\Platform::all();

        // Xử lý bộ lọc thời gian
        if ($timeFilter === 'month') {
            $start = Carbon::now()->startOfMonth()->toDateString();
            $end = Carbon::now()->endOfMonth()->toDateString();
        } elseif ($timeFilter === '3months') {
            $start = Carbon::now()->subMonths(3)->startOfMonth()->toDateString();
            $end = Carbon::now()->endOfMonth()->toDateString();
        } elseif ($timeFilter === '7') {
            $start = Carbon::now()->subDays(6)->toDateString();
            $end = Carbon::now()->toDateString();
        }

        // 1. Tính toán dữ liệu tổng quan (chỉ lấy bản ghi admin_approved)
        $totalStats = FinancialRecord::where('status', 'admin_approved')
            ->select([
                DB::raw('SUM(revenue) as total_revenue'),
                DB::raw('SUM((SELECT SUM(amount) FROM expenses WHERE expenses.financial_record_id = financial_records.id)) as total_expenses'),
                DB::raw('AVG(roas) as avg_roas'),
                DB::raw('COUNT(*) as record_count')
            ])
            ->first();

        // 2. Tính toán dữ liệu theo bộ lọc
        $filteredQuery = FinancialRecord::where('status', 'admin_approved');

        if ($start && $end) {
            $filteredQuery->whereBetween('record_date', [$start, $end]);
        } elseif ($start) {
            $filteredQuery->where('record_date', '>=', $start);
        } elseif ($end) {
            $filteredQuery->where('record_date', '<=', $end);
        }

        if ($yearFilter !== 'all') {
            $filteredQuery->whereYear('record_date', $yearFilter);
        }

        if ($platform) {
            $filteredQuery->where('platform_id', $platform);
        }

        $filteredStats = $filteredQuery->select([
            DB::raw('SUM(revenue) as total_revenue'),
            DB::raw('SUM((SELECT SUM(amount) FROM expenses WHERE expenses.financial_record_id = financial_records.id)) as total_expenses'),
            DB::raw('AVG(roas) as avg_roas'),
            DB::raw('COUNT(*) as record_count')
        ])
            ->first();

        // 3. Lấy dữ liệu cho biểu đồ doanh thu và chi phí theo ngày
        $recordsQuery = FinancialRecord::where('status', 'admin_approved')
            ->with(['platform', 'expenses']);

        if ($start && $end) {
            $recordsQuery->whereBetween('record_date', [$start, $end]);
        } elseif ($start) {
            $recordsQuery->where('record_date', '>=', $start);
        } elseif ($end) {
            $recordsQuery->where('record_date', '<=', $end);
        }

        if ($yearFilter !== 'all') {
            $recordsQuery->whereYear('record_date', $yearFilter);
        }

        if ($platform) {
            $recordsQuery->where('platform_id', $platform);
        }

        $records = $recordsQuery->get();

        // 4. Gán giá trị tổng quan
        $totalRevenue = $totalStats->total_revenue ?? 0;
        $total_expenses = $totalStats->total_expenses ?? 0;
        $avg_roas = $totalStats->avg_roas ? $totalStats->avg_roas : 0;
        $record_count = $totalStats->record_count ?? 0;

        // 5. Gán giá trị theo bộ lọc
        $filteredTotalRevenue = $filteredStats->total_revenue ?? 0;
        $filteredTotalExpenses = $filteredStats->total_expenses ?? 0;
        $filteredAvgRoas = $filteredStats->avg_roas ? $filteredStats->avg_roas : 0;
        $filteredRecordCount = $filteredStats->record_count ?? 0;

        // 6. Lấy các bản ghi gần đây
        $recent_records = FinancialRecord::where('status', 'admin_approved')
            ->with('platform')
            ->latest()
            ->take(10)
            ->get();

        // 7. Lấy danh sách năm cho bộ lọc
        $years = FinancialRecord::where('status', 'admin_approved')
            ->select(DB::raw('YEAR(record_date) as year'))
            ->distinct()
            ->pluck('year');

        // 8. Dữ liệu cho biểu đồ doanh thu và chi phí theo ngày
        $labels = [];
        $data = [];
        $expenseData = [];

        if ($records->count() > 0) {
            $dates = $records->pluck('record_date')->unique()->sort()->values();

            foreach ($dates as $date) {
                $labels[] = Carbon::parse($date)->format('d/m');
                $dailyRevenueSum = $records->where('record_date', $date)->sum('revenue');
                $data[] = $dailyRevenueSum;

                $dailyExpenseSum = Expense::whereIn(
                    'financial_record_id',
                    $records->where('record_date', $date)->pluck('id')
                )->sum('amount');
                $expenseData[] = $dailyExpenseSum;
            }
        }

        // 9. Dữ liệu cho biểu đồ tròn (phân bổ doanh thu theo nền tảng)
        $platformData = $recent_records->groupBy('platform.name')->map->sum('revenue')->toArray();

        // 10. Thống kê số lượng bản ghi đã duyệt và chưa duyệt
        $approved_count = FinancialRecord::where('status', 'admin_approved')->count();
        $not_approved_count = FinancialRecord::where('status', '!=', 'admin_approved')->count();

        // Lấy mục tiêu doanh thu năm từ cache (hoặc DB nếu bạn lưu ở DB)
        $year_goal = cache('year_goal_' . date('Y'));

        // Nếu là yêu cầu AJAX, trả về JSON
        if ($request->ajax()) {
            return response()->json([
                'labels' => $labels,
                'data' => $data,
                'expenseData' => $expenseData,
                'platformData' => $platformData,
                'filteredTotalRevenue' => $filteredTotalRevenue,
                'filteredTotalExpenses' => $filteredTotalExpenses,
                'filteredAvgRoas' => $filteredAvgRoas,
                'filteredRecordCount' => $filteredRecordCount,
            ]);
        }

        // Truyền dữ liệu vào view
        return view('admin.financial.total_revenue', compact(
            'totalRevenue',
            'total_expenses',
            'avg_roas',
            'record_count',
            'filteredTotalRevenue',
            'filteredTotalExpenses',
            'filteredAvgRoas',
            'filteredRecordCount',
            'recent_records',
            'years',
            'labels',
            'data',
            'expenseData',
            'approved_count',
            'not_approved_count',
            'platforms',
            'year_goal',
            'records'
        ));
    }
}
