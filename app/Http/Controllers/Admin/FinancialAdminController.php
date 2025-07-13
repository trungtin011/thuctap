<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\FinancialRecord;
use App\Models\Expense;
use App\Models\OfficeRevenue;
use App\Models\Platform;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class FinancialAdminController extends Controller
{
    public function index(Request $request)
    {
        $departments = \App\Models\Department::all();
        $platforms = \App\Models\Platform::all();

        // Khởi tạo các query cho từng bảng, chỉ lấy bản ghi có status = 'manager_approved'
        $financialRecordsQuery = FinancialRecord::query()->with(['department', 'platform', 'submittedBy'])->where('status', 'manager_approved');
        $expensesQuery = Expense::query()->with(['financialRecord.department', 'financialRecord.platform', 'financialRecord.submittedBy', 'expenseType'])->where('status', 'manager_approved');
        $officeRevenuesQuery = OfficeRevenue::query()->with(['department', 'submittedBy', 'offices'])->where('status', 'manager_approved');

        // Lọc theo phòng ban
        if ($request->filled('department_id')) {
            $financialRecordsQuery->where('department_id', $request->department_id);
            $expensesQuery->whereHas('financialRecord', function ($query) use ($request) {
                $query->where('department_id', $request->department_id);
            });
            $officeRevenuesQuery->where('department_id', $request->department_id);
        }

        // Lọc theo nền tảng (chỉ áp dụng cho financial_records và expenses)
        if ($request->filled('platform_id')) {
            $financialRecordsQuery->where('platform_id', $request->platform_id);
            $expensesQuery->whereHas('financialRecord', function ($query) use ($request) {
                $query->where('platform_id', $request->platform_id);
            });
        }

        // Lọc theo ngày bắt đầu
        if ($request->filled('start_date')) {
            $financialRecordsQuery->whereDate('record_date', '>=', $request->start_date);
            $expensesQuery->whereHas('financialRecord', function ($query) use ($request) {
                $query->whereDate('record_date', '>=', $request->start_date);
            });
            $officeRevenuesQuery->whereDate('record_date', '>=', $request->start_date);
        }

        // Lọc theo ngày kết thúc
        if ($request->filled('end_date')) {
            $financialRecordsQuery->whereDate('record_date', '<=', $request->end_date);
            $expensesQuery->whereHas('financialRecord', function ($query) use ($request) {
                $query->whereDate('record_date', '<=', $request->end_date);
            });
            $officeRevenuesQuery->whereDate('record_date', '<=', $request->end_date);
        }

        // Lấy dữ liệu từ các bảng
        $financialRecords = $financialRecordsQuery->orderBy('record_date', 'desc')->get()->map(function ($record) {
            return [
                'id' => $record->id,
                'type' => 'financial_record',
                'department' => $record->department->name ?? 'N/A',
                'platform' => $record->platform->name ?? 'N/A',
                'record_date' => $record->record_date,
                'submitted_by' => $record->submittedBy->name ?? 'N/A',
                'amount' => $record->revenue,
                'status' => $record->status,
                'commission' => $record->commission ?? 0,
                'description' => $record->description ?? 'N/A',
            ];
        });

        $expenses = $expensesQuery->orderBy('created_at', 'desc')->get()->map(function ($expense) {
            return [
                'id' => $expense->id,
                'type' => 'expense',
                'department' => $expense->financialRecord->department->name ?? 'N/A',
                'platform' => $expense->financialRecord->platform->name ?? 'N/A',
                'record_date' => $expense->financialRecord->record_date,
                'submitted_by' => $expense->financialRecord->submittedBy->name ?? 'N/A',
                'amount' => $expense->amount,
                'status' => $expense->status,
                'commission' => 0,
                'description' => $expense->description ?? ($expense->expenseType->name ?? 'N/A'),
            ];
        });

        $officeRevenues = $officeRevenuesQuery->orderBy('record_date', 'desc')->get()->map(function ($revenue) {
            $offices = $revenue->offices->pluck('name')->implode(', ');
            return [
                'id' => $revenue->id,
                'type' => 'office_revenue',
                'department' => $revenue->department->name ?? 'N/A',
                'platform' => 'N/A',
                'record_date' => $revenue->record_date,
                'submitted_by' => $revenue->submittedBy->name ?? 'N/A',
                'amount' => $revenue->total,
                'status' => $revenue->status,
                'commission' => 0,
                'description' => 'Doanh thu văn phòng: ' . $offices,
            ];
        });

        // Gộp tất cả bản ghi thành một collection
        $recordsCollection = collect(array_merge($financialRecords->toArray(), $expenses->toArray(), $officeRevenues->toArray()))
            ->sortByDesc('record_date');

        // Phân trang thủ công
        $perPage = 15;
        $currentPage = $request->input('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedItems = $recordsCollection->slice($offset, $perPage)->values();
        $records = new LengthAwarePaginator(
            $paginatedItems,
            $recordsCollection->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Gán giá trị mặc định cho $status
        $status = 'manager_approved';

        return view('admin.financial.index', compact('records', 'departments', 'platforms', 'status'));
    }

    public function approve($id)
    {
        Log::info("Approving record - ID: $id");

        // Kiểm tra loại bản ghi dựa trên sự tồn tại trong các bảng
        $record = FinancialRecord::find($id);
        if ($record && $record->status === 'manager_approved') {
            $record->status = 'admin_approved';
            $record->save();
            return redirect()->route('admin.financial.index')->with('success', 'Bản ghi đã được admin phê duyệt thành công.');
        }

        $expense = Expense::find($id);
        if ($expense && $expense->status === 'manager_approved') {
            $expense->status = 'admin_approved';
            $expense->save();
            return redirect()->route('admin.financial.index')->with('success', 'Bản ghi đã được admin phê duyệt thành công.');
        }

        $officeRevenue = OfficeRevenue::find($id);
        if ($officeRevenue && $officeRevenue->status === 'manager_approved') {
            $officeRevenue->status = 'admin_approved';
            $officeRevenue->save();
            return redirect()->route('admin.financial.index')->with('success', 'Bản ghi đã được admin phê duyệt thành công.');
        }

        return redirect()->route('admin.financial.index')->with('error', 'Bản ghi không hợp lệ hoặc không thể phê duyệt.');
    }

    public function reject(Request $request, $id)
    {
        Log::info("Rejecting record - ID: $id");

        // Kiểm tra loại bản ghi dựa trên sự tồn tại trong các bảng
        $record = FinancialRecord::find($id);
        if ($record && $record->status === 'manager_approved') {
            $request->validate(['reject_reason' => 'required|string|max:255']);
            $record->status = 'rejected';
            $record->reject_reason = $request->reject_reason;
            $record->save();
            return redirect()->route('admin.financial.index')->with('success', 'Bản ghi đã bị từ chối.');
        }

        $expense = Expense::find($id);
        if ($expense && $expense->status === 'manager_approved') {
            $request->validate(['reject_reason' => 'required|string|max:255']);
            $expense->status = 'rejected';
            $expense->reject_reason = $request->reject_reason;
            $expense->save();
            return redirect()->route('admin.financial.index')->with('success', 'Bản ghi đã bị từ chối.');
        }

        $officeRevenue = OfficeRevenue::find($id);
        if ($officeRevenue && $officeRevenue->status === 'manager_approved') {
            $request->validate(['reject_reason' => 'required|string|max:255']);
            $officeRevenue->status = 'rejected';
            $officeRevenue->reject_reason = $request->reject_reason;
            $officeRevenue->save();
            return redirect()->route('admin.financial.index')->with('success', 'Bản ghi đã bị từ chối.');
        }

        return redirect()->route('admin.financial.index')->with('error', 'Bản ghi không hợp lệ hoặc không thể từ chối.');
    }

    public function history()
    {
        $financialRecords = FinancialRecord::where('status', 'admin_approved')
            ->with(['department', 'platform'])
            ->get()
            ->map(function ($record) {
                return [
                    'id' => $record->id,
                    'type' => 'financial_record',
                    'department' => $record->department->name ?? 'N/A',
                    'platform' => $record->platform->name ?? 'N/A',
                    'record_date' => $record->record_date,
                    'submitted_by' => $record->submittedBy->name ?? 'N/A',
                    'amount' => $record->revenue,
                    'status' => $record->status,
                    'commission' => $record->commission ?? 0,
                    'description' => $record->description ?? 'N/A',
                ];
            });

        $expenses = Expense::where('status', 'admin_approved')
            ->with(['financialRecord.department', 'financialRecord.platform', 'financialRecord.submittedBy', 'expenseType'])
            ->get()
            ->map(function ($expense) {
                return [
                    'id' => $expense->id,
                    'type' => 'expense',
                    'department' => $expense->financialRecord->department->name ?? 'N/A',
                    'platform' => $expense->financialRecord->platform->name ?? 'N/A',
                    'record_date' => $expense->financialRecord->record_date,
                    'submitted_by' => $expense->financialRecord->submittedBy->name ?? 'N/A',
                    'amount' => $expense->amount,
                    'status' => $expense->status,
                    'commission' => 0,
                    'description' => $expense->description ?? ($expense->expenseType->name ?? 'N/A'),
                ];
            });

        $officeRevenues = OfficeRevenue::where('status', 'admin_approved')
            ->with(['department', 'submittedBy', 'offices'])
            ->get()
            ->map(function ($revenue) {
                $offices = $revenue->offices->pluck('name')->implode(', ');
                return [
                    'id' => $revenue->id,
                    'type' => 'office_revenue',
                    'department' => $revenue->department->name ?? 'N/A',
                    'platform' => 'N/A',
                    'record_date' => $revenue->record_date,
                    'submitted_by' => $revenue->submittedBy->name ?? 'N/A',
                    'amount' => $revenue->total,
                    'status' => $revenue->status,
                    'commission' => 0,
                    'description' => 'Doanh thu văn phòng: ' . $offices,
                ];
            });

        $records = collect(array_merge($financialRecords->toArray(), $expenses->toArray(), $officeRevenues->toArray()))
            ->sortByDesc('record_date')
            ->values();

        return view('admin.financial.history', compact('records'));
    }

    public function setGoal(Request $request)
    {
        $request->validate([
            'year_goal' => 'required|numeric|min:0'
        ]);
        cache(['year_goal_' . date('Y') => $request->year_goal], now()->addYear());

        return redirect()->back()->with('success', 'Đã lưu mục tiêu doanh thu năm!');
    }

    public function totalRevenue(Request $request)
    {
        // Áp dụng bộ lọc thời gian
        $start = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : null;
        $end = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : null;
        $platform = $request->input('platform_id');
        $yearFilter = $request->input('year', 'all');
        $timeFilter = $request->input('timeFilter', 'all');

        // Chuyển đổi định dạng ngày nếu có
        $start = $start ? Carbon::parse($start)->startOfDay() : null;
        $end = $end ? Carbon::parse($end)->endOfDay() : null;

        // Xử lý timeFilter
        if ($timeFilter === 'month') {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
        } elseif ($timeFilter === '3months') {
            $start = Carbon::now()->subMonths(2)->startOfMonth();
            $end = Carbon::now()->endOfMonth();
        }

        // Tính toán tổng quan
        $totalStats = DB::selectOne("
            SELECT 
                (SUM(fr.revenue) + SUM(orv.total)) AS total_revenue,
                (SELECT SUM(amount) FROM expenses WHERE status = 'admin_approved') AS total_expenses,
                CASE 
                    WHEN (SELECT SUM(amount) FROM expenses WHERE status = 'admin_approved') > 0 
                    THEN (SUM(fr.revenue) + SUM(orv.total)) / (SELECT SUM(amount) FROM expenses WHERE status = 'admin_approved')
                    ELSE NULL
                END AS roas,
                COUNT(DISTINCT fr.id) + COUNT(DISTINCT e.id) + COUNT(DISTINCT orv.id) AS record_count,
                SUM(fr.commission) AS total_commission
            FROM financial_records fr
            LEFT JOIN expenses e ON e.financial_record_id = fr.id
            LEFT JOIN office_revenues orv ON orv.status = 'admin_approved'
            WHERE fr.status = 'admin_approved'
        ");

        // Tính toán dữ liệu được lọc
        $filteredQueryFr = FinancialRecord::where('status', 'admin_approved');
        $filteredQueryOrv = OfficeRevenue::where('status', 'admin_approved');
        $filteredQueryE = Expense::where('status', 'admin_approved');

        if ($start && $end) {
            $filteredQueryFr->whereBetween('record_date', [$start, $end]);
            $filteredQueryOrv->whereBetween('record_date', [$start, $end]);
            $filteredQueryE->whereBetween('created_at', [$start, $end]); // Sử dụng created_at cho expenses
        }

        if ($yearFilter !== 'all') {
            $filteredQueryFr->whereYear('record_date', $yearFilter);
            $filteredQueryOrv->whereYear('record_date', $yearFilter);
            $filteredQueryE->whereYear('created_at', $yearFilter);
        }

        if ($platform) {
            $filteredQueryFr->where('platform_id', $platform);
            $filteredQueryE->whereHas('financialRecord', function ($query) use ($platform) {
                $query->where('platform_id', $platform);
            });
        }

        // Truy vấn dữ liệu được lọc
        $filteredStats = DB::selectOne("
            SELECT 
                (SUM(fr.revenue) + SUM(orv.total)) AS total_revenue,
                (SELECT SUM(amount) FROM expenses WHERE status = 'admin_approved' 
                    AND (created_at BETWEEN ? AND ? OR ? IS NULL)
                    AND (YEAR(created_at) = ? OR ? = 'all')) AS total_expenses,
                CASE 
                    WHEN (SELECT SUM(amount) FROM expenses WHERE status = 'admin_approved' 
                        AND (created_at BETWEEN ? AND ? OR ? IS NULL)
                        AND (YEAR(created_at) = ? OR ? = 'all')) > 0 
                    THEN (SUM(fr.revenue) + SUM(orv.total)) / 
                        (SELECT SUM(amount) FROM expenses WHERE status = 'admin_approved' 
                        AND (created_at BETWEEN ? AND ? OR ? IS NULL)
                        AND (YEAR(created_at) = ? OR ? = 'all'))
                    ELSE NULL
                END AS roas,
                COUNT(DISTINCT fr.id) + COUNT(DISTINCT e.id) + COUNT(DISTINCT orv.id) AS record_count,
                SUM(fr.commission) AS total_commission
            FROM financial_records fr
            LEFT JOIN expenses e ON e.financial_record_id = fr.id
            LEFT JOIN office_revenues orv ON orv.status = 'admin_approved'
            WHERE fr.status = 'admin_approved'
                AND (fr.record_date BETWEEN ? AND ? OR ? IS NULL)
                AND (orv.record_date BETWEEN ? AND ? OR ? IS NULL)
                AND (fr.platform_id = ? OR ? IS NULL)
                AND (YEAR(fr.record_date) = ? OR ? = 'all')
                AND (YEAR(orv.record_date) = ? OR ? = 'all')
        ", [
            $start,
            $end,
            $start,
            $yearFilter,
            $yearFilter,
            $start,
            $end,
            $start,
            $yearFilter,
            $yearFilter,
            $start,
            $end,
            $start,
            $yearFilter,
            $yearFilter,
            $start,
            $end,
            $start,
            $start,
            $end,
            $start,
            $platform,
            $platform,
            $yearFilter,
            $yearFilter,
            $yearFilter,
            $yearFilter
        ]);

        // Gán giá trị tổng quan, đảm bảo không null
        $totalRevenue = $totalStats->total_revenue ?? 0;
        $totalExpenses = $totalStats->total_expenses ?? 0;
        $avgRoas = $totalStats->roas ?? 0;
        $recordCount = $totalStats->record_count ?? 0;
        $totalCommission = $totalStats->total_commission ?? 0;

        // Gán giá trị được lọc
        $filteredTotalRevenue = $filteredStats->total_revenue ?? 0;
        $filteredTotalExpenses = $filteredStats->total_expenses ?? 0;
        $filteredAvgRoas = $filteredStats->roas ?? 0;
        $filteredRecordCount = $filteredStats->record_count ?? 0;
        $filteredTotalCommission = $filteredStats->total_commission ?? 0;

        Log::info('Total Expenses: ' . $totalExpenses);

        // Lấy danh sách các năm
        $years = FinancialRecord::where('status', 'admin_approved')
            ->pluck('record_date')
            ->merge(OfficeRevenue::where('status', 'admin_approved')->pluck('record_date'))
            ->map(function ($date) {
                return Carbon::parse($date)->year;
            })
            ->unique()
            ->sort()
            ->values();

        // Lấy danh sách nền tảng
        $platforms = Platform::all();

        // Lấy bản ghi gần đây
        $recentRecordsQuery = FinancialRecord::where('status', 'admin_approved')
            ->with(['platform', 'submittedBy', 'department'])
            ->orderBy('record_date', 'desc')
            ->take(10);

        if ($platform) {
            $recentRecordsQuery->where('platform_id', $platform);
        }
        if ($start && $end) {
            $recentRecordsQuery->whereBetween('record_date', [$start, $end]);
        }
        if ($yearFilter !== 'all') {
            $recentRecordsQuery->whereYear('record_date', $yearFilter);
        }

        $recent_records = $recentRecordsQuery->get();

        // Lấy bản ghi financial_records và office_revenues
        $records = $filteredQueryFr->with(['platform', 'submittedBy', 'department'])->get();
        $office_revenues = $filteredQueryOrv->with(['department', 'submittedBy', 'offices'])->get();

        // Tính số lượng bản ghi đã duyệt và chưa duyệt
        $approved_count = FinancialRecord::where('status', 'admin_approved')->count() +
            Expense::where('status', 'admin_approved')->count() +
            OfficeRevenue::where('status', 'admin_approved')->count();
        $not_approved_count = FinancialRecord::where('status', 'manager_approved')->count() +
            Expense::where('status', 'manager_approved')->count() +
            OfficeRevenue::where('status', 'manager_approved')->count();

        // Tạo dữ liệu cho biểu đồ
        $dates = [];
        if ($start && $end) {
            $dates = collect(Carbon::parse($start)->toPeriod($end))->map(function ($date) {
                return $date->format('Y-m-d');
            })->toArray();
        } else {
            $dates = FinancialRecord::where('status', 'admin_approved')
                ->pluck('record_date')
                ->merge(OfficeRevenue::where('status', 'admin_approved')->pluck('record_date'))
                ->unique()
                ->map(function ($date) {
                    return Carbon::parse($date)->format('Y-m-d');
                })
                ->sort()
                ->values()
                ->toArray();
        }

        // Lấy dữ liệu doanh thu, chi phí và ROAS theo ngày
        $data = [];
        $expenseData = [];
        $roasData = [];
        foreach ($dates as $date) {
            $dailyRevenueSum = $records->where('record_date', $date)->sum('revenue') +
                $office_revenues->where('record_date', $date)->sum('total');
            $data[] = $dailyRevenueSum;

            $dailyExpenseSum = Expense::whereIn(
                'financial_record_id',
                $records->where('record_date', $date)->pluck('id')
            )->sum('amount');
            $expenseData[] = $dailyExpenseSum;

            $roasData[] = $dailyExpenseSum > 0 ? $dailyRevenueSum / $dailyExpenseSum : 0;
        }

        // Chuẩn bị nhãn cho biểu đồ
        $labels = array_map(function ($date) {
            return Carbon::parse($date)->format('d/m/Y');
        }, $dates);

        // Xử lý so sánh tháng
        $compareMonth1 = $request->input('compare_month1', 1);
        $compareMonth2 = $request->input('compare_month2', 2);
        $compareYear = $request->input('compare_year', now()->year);
        $platform = $request->input('platform_id');

        $monthlyComparison = [];

        foreach ([$compareMonth1, $compareMonth2] as $month) {
            $startOfMonth = Carbon::create($compareYear, $month, 1)->startOfMonth();
            $endOfMonth = $startOfMonth->copy()->endOfMonth();

            $stats = DB::selectOne("
                SELECT 
                    (SUM(fr.revenue) + SUM(orv.total)) AS total_revenue,
                    (SELECT SUM(amount) FROM expenses WHERE status = 'admin_approved' 
                        AND created_at BETWEEN ? AND ?) AS total_expenses,
                    CASE 
                        WHEN (SELECT SUM(amount) FROM expenses WHERE status = 'admin_approved' 
                            AND created_at BETWEEN ? AND ?) > 0 
                        THEN (SUM(fr.revenue) + SUM(orv.total)) / 
                             (SELECT SUM(amount) FROM expenses WHERE status = 'admin_approved' 
                             AND created_at BETWEEN ? AND ?)
                        ELSE NULL
                    END AS roas,
                    SUM(fr.commission) AS total_commission
                FROM financial_records fr
                LEFT JOIN office_revenues orv ON orv.status = 'admin_approved'
                    AND orv.record_date BETWEEN ? AND ?
                WHERE fr.status = 'admin_approved'
                    AND fr.record_date BETWEEN ? AND ?
                    AND (fr.platform_id = ? OR ? IS NULL)
            ", [
                $startOfMonth,
                $endOfMonth,
                $startOfMonth,
                $endOfMonth,
                $startOfMonth,
                $endOfMonth,
                $startOfMonth,
                $endOfMonth,
                $startOfMonth,
                $endOfMonth,
                $platform,
                $platform
            ]);

            $monthlyComparison['month' . ($month == $compareMonth1 ? 1 : 2)] = [
                'name' => 'Tháng ' . $month,
                'revenue' => $stats->total_revenue ?? 0,
                'expenses' => $stats->total_expenses ?? 0,
                'roas' => $stats->roas ?? 0,
                'commission' => $stats->total_commission ?? 0,
            ];
        }

        $monthlyComparison['year'] = $compareYear;
        $monthlyComparison['difference'] = [
            'revenue' => $monthlyComparison['month2']['revenue'] - $monthlyComparison['month1']['revenue'],
            'expenses' => $monthlyComparison['month2']['expenses'] - $monthlyComparison['month1']['expenses'],
            'roas' => $monthlyComparison['month2']['roas'] - $monthlyComparison['month1']['roas'],
            'commission' => $monthlyComparison['month2']['commission'] - $monthlyComparison['month1']['commission'],
        ];

        Log::info('Monthly Comparison: ' . json_encode($monthlyComparison));

        // Trả về view với dữ liệu
        return view('admin.financial.total_revenue', compact(
            'totalRevenue',
            'totalExpenses',
            'avgRoas',
            'recordCount',
            'totalCommission',
            'filteredTotalRevenue',
            'filteredTotalExpenses',
            'filteredAvgRoas',
            'filteredRecordCount',
            'filteredTotalCommission',
            'labels',
            'data',
            'expenseData',
            'roasData',
            'platforms',
            'years',
            'recent_records',
            'approved_count',
            'not_approved_count',
            'records',
            'office_revenues',
            'monthlyComparison'
        ));
    }
}
