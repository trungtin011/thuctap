<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\FinancialRecord;
use App\Models\Expense;
use App\Models\OfficeRevenue;
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
        // Lấy các tham số từ request
        $start = $request->input('start_date');
        $end = $request->input('end_date');
        $platform = $request->input('platform');
        $yearFilter = $request->input('yearFilter', 'all');
        $timeFilter = $request->input('timeFilter', 'all');
        $compareMonth1 = $request->input('compare_month1');
        $compareMonth2 = $request->input('compare_month2');
        $compareYear = $request->input('compare_year', date('Y'));
        $monthlyComparison = null;

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

        // Xử lý so sánh doanh thu giữa các tháng
        if ($compareMonth1 && $compareMonth2) {
            $month1Start = Carbon::create($compareYear, $compareMonth1, 1)->startOfMonth();
            $month1End = Carbon::create($compareYear, $compareMonth1, 1)->endOfMonth();
            $month2Start = Carbon::create($compareYear, $compareMonth2, 1)->startOfMonth();
            $month2End = Carbon::create($compareYear, $compareMonth2, 1)->endOfMonth();

            // Lấy doanh thu tháng 1 từ financial_records và office_revenues
            $month1FinancialRecords = FinancialRecord::where('status', 'admin_approved')
                ->whereBetween('record_date', [$month1Start, $month1End])
                ->when($platform, function($query) use ($platform) {
                    return $query->where('platform_id', $platform);
                })
                ->get();

            $month1OfficeRevenues = OfficeRevenue::where('status', 'admin_approved')
                ->whereBetween('record_date', [$month1Start, $month1End])
                ->get();

            $month1Expenses = Expense::where('status', 'admin_approved')
                ->whereHas('financialRecord', function($query) use ($month1Start, $month1End, $platform) {
                    $query->where('status', 'admin_approved')
                          ->whereBetween('record_date', [$month1Start, $month1End])
                          ->when($platform, function($q) use ($platform) {
                              return $q->where('platform_id', $platform);
                          });
                })
                ->get();

            // Lấy doanh thu tháng 2 từ financial_records và office_revenues
            $month2FinancialRecords = FinancialRecord::where('status', 'admin_approved')
                ->whereBetween('record_date', [$month2Start, $month2End])
                ->when($platform, function($query) use ($platform) {
                    return $query->where('platform_id', $platform);
                })
                ->get();

            $month2OfficeRevenues = OfficeRevenue::where('status', 'admin_approved')
                ->whereBetween('record_date', [$month2Start, $month2End])
                ->get();

            $month2Expenses = Expense::where('status', 'admin_approved')
                ->whereHas('financialRecord', function($query) use ($month2Start, $month2End, $platform) {
                    $query->where('status', 'admin_approved')
                          ->whereBetween('record_date', [$month2Start, $month2End])
                          ->when($platform, function($q) use ($platform) {
                              return $q->where('platform_id', $platform);
                          });
                })
                ->get();

            // Tính toán tổng doanh thu tháng 1
            $month1TotalRevenue = $month1FinancialRecords->sum('revenue') + $month1OfficeRevenues->sum('total');
            $month1TotalExpenses = $month1Expenses->sum('amount');
            $month1TotalCommission = $month1FinancialRecords->sum('commission');
            $month1AvgRoas = $month1FinancialRecords->avg('roas') ?? 0;

            // Tính toán tổng doanh thu tháng 2
            $month2TotalRevenue = $month2FinancialRecords->sum('revenue') + $month2OfficeRevenues->sum('total');
            $month2TotalExpenses = $month2Expenses->sum('amount');
            $month2TotalCommission = $month2FinancialRecords->sum('commission');
            $month2AvgRoas = $month2FinancialRecords->avg('roas') ?? 0;

            $monthlyComparison = [
                'month1' => [
                    'name' => 'Tháng ' . $compareMonth1,
                    'revenue' => $month1TotalRevenue,
                    'expenses' => $month1TotalExpenses,
                    'roas' => $month1AvgRoas,
                    'commission' => $month1TotalCommission
                ],
                'month2' => [
                    'name' => 'Tháng ' . $compareMonth2,
                    'revenue' => $month2TotalRevenue,
                    'expenses' => $month2TotalExpenses,
                    'roas' => $month2AvgRoas,
                    'commission' => $month2TotalCommission
                ],
                'difference' => [
                    'revenue' => $month2TotalRevenue - $month1TotalRevenue,
                    'expenses' => $month2TotalExpenses - $month1TotalExpenses,
                    'roas' => $month2AvgRoas - $month1AvgRoas,
                    'commission' => $month2TotalCommission - $month1TotalCommission
                ]
            ];
        }

        // Tính toán tổng quan (bao gồm cả office_revenues)
        $totalStats = DB::selectOne("
            SELECT 
                SUM(fr.revenue) + SUM(orv.total) AS total_revenue,
                SUM(e.amount) AS total_expenses,
                AVG(fr.roas) AS avg_roas,
                COUNT(DISTINCT fr.id) + COUNT(DISTINCT e.id) + COUNT(DISTINCT orv.id) AS record_count,
                SUM(fr.commission) AS total_commission
            FROM financial_records fr
            LEFT JOIN expenses e ON e.financial_record_id = fr.id
            LEFT JOIN office_revenues orv ON orv.status = 'admin_approved'
            WHERE fr.status = 'admin_approved'
        ");

        // Tính toán theo bộ lọc
        $filteredQueryFr = FinancialRecord::where('status', 'admin_approved');
        $filteredQueryOrv = OfficeRevenue::where('status', 'admin_approved');
        $filteredQueryE = Expense::where('status', 'admin_approved');

        if ($start && $end) {
            $filteredQueryFr->whereBetween('record_date', [$start, $end]);
            $filteredQueryOrv->whereBetween('record_date', [$start, $end]);
            $filteredQueryE->whereHas('financialRecord', function ($query) use ($start, $end) {
                $query->whereBetween('record_date', [$start, $end]);
            });
        }

        if ($yearFilter !== 'all') {
            $filteredQueryFr->whereYear('record_date', $yearFilter);
            $filteredQueryOrv->whereYear('record_date', $yearFilter);
            $filteredQueryE->whereHas('financialRecord', function ($query) use ($yearFilter) {
                $query->whereYear('record_date', $yearFilter);
            });
        }

        if ($platform) {
            $filteredQueryFr->where('platform_id', $platform);
            $filteredQueryE->whereHas('financialRecord', function ($query) use ($platform) {
                $query->where('platform_id', $platform);
            });
        }

        $filteredStats = DB::selectOne("
            SELECT 
                SUM(fr.revenue) + SUM(orv.total) AS total_revenue,
                SUM(e.amount) AS total_expenses,
                AVG(fr.roas) AS avg_roas,
                COUNT(DISTINCT fr.id) + COUNT(DISTINCT e.id) + COUNT(DISTINCT orv.id) AS record_count,
                SUM(fr.commission) AS total_commission
            FROM financial_records fr
            LEFT JOIN expenses e ON e.financial_record_id = fr.id
            LEFT JOIN office_revenues orv ON orv.status = 'admin_approved'
            WHERE fr.status = 'admin_approved'
                AND (fr.record_date BETWEEN ? AND ? OR orv.record_date BETWEEN ? AND ?)
                AND (fr.platform_id = ? OR ? IS NULL)
                AND (YEAR(fr.record_date) = ? OR ? = 'all')
                AND (YEAR(orv.record_date) = ? OR ? = 'all')
        ", [$start, $end, $start, $end, $platform, $platform, $yearFilter, $yearFilter, $yearFilter, $yearFilter]);

        // Lấy dữ liệu cho biểu đồ
        $recordsQuery = FinancialRecord::where('status', 'admin_approved')->with(['platform', 'expenses']);
        $officeRevenuesQuery = OfficeRevenue::where('status', 'admin_approved');

        if ($start && $end) {
            $recordsQuery->whereBetween('record_date', [$start, $end]);
            $officeRevenuesQuery->whereBetween('record_date', [$start, $end]);
        }

        if ($yearFilter !== 'all') {
            $recordsQuery->whereYear('record_date', $yearFilter);
            $officeRevenuesQuery->whereYear('record_date', $yearFilter);
        }

        if ($platform) {
            $recordsQuery->where('platform_id', $platform);
        }

        $records = $recordsQuery->get();
        $officeRevenues = $officeRevenuesQuery->get();

        // Gán giá trị tổng quan
        $totalRevenue = $totalStats->total_revenue ?? 0;
        $total_expenses = $totalStats->total_expenses ?? 0;
        $avg_roas = $totalStats->avg_roas ?? 0;
        $record_count = $totalStats->record_count ?? 0;
        $totalCommission = $totalStats->total_commission ?? 0;

        // Gán giá trị theo bộ lọc
        $filteredTotalRevenue = $filteredStats->total_revenue ?? 0;
        $filteredTotalExpenses = $filteredStats->total_expenses ?? 0;
        $filteredAvgRoas = $filteredStats->avg_roas ?? 0;
        $filteredRecordCount = $filteredStats->record_count ?? 0;
        $filteredTotalCommission = $filteredStats->total_commission ?? 0;

        // Lấy các bản ghi gần đây
        $recent_records = FinancialRecord::where('status', 'admin_approved')
            ->with('platform')
            ->latest()
            ->take(10)
            ->get();

        // Lấy danh sách năm
        $years = collect([
            FinancialRecord::where('status', 'admin_approved')->select(DB::raw('YEAR(record_date) as year'))->distinct()->pluck('year'),
            OfficeRevenue::where('status', 'admin_approved')->select(DB::raw('YEAR(record_date) as year'))->distinct()->pluck('year'),
        ])->flatten()->unique()->sort()->values();

        // Dữ liệu biểu đồ
        $labels = [];
        $data = [];
        $expenseData = [];

        $allRecords = $records->concat($officeRevenues);
        if ($allRecords->count() > 0) {
            $dates = $allRecords->pluck('record_date')->unique()->sort()->values();

            foreach ($dates as $date) {
                $labels[] = Carbon::parse($date)->format('d/m');
                $dailyRevenueSum = $records->where('record_date', $date)->sum('revenue') +
                                   $officeRevenues->where('record_date', $date)->sum('total');
                $data[] = $dailyRevenueSum;

                $dailyExpenseSum = Expense::whereIn(
                    'financial_record_id',
                    $records->where('record_date', $date)->pluck('id')
                )->sum('amount');
                $expenseData[] = $dailyExpenseSum;
            }
        }

        // Biểu đồ tròn
        $platformData = $recent_records->groupBy('platform.name')->map->sum('revenue')->toArray();

        // Thống kê bản ghi
        $approved_count = FinancialRecord::where('status', 'admin_approved')->count() +
                          Expense::where('status', 'admin_approved')->count() +
                          OfficeRevenue::where('status', 'admin_approved')->count();
        $not_approved_count = FinancialRecord::where('status', '!=', 'admin_approved')->count() +
                              Expense::where('status', '!=', 'admin_approved')->count() +
                              OfficeRevenue::where('status', '!=', 'admin_approved')->count();

        $year_goal = cache('year_goal_' . date('Y'));

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
                'filteredTotalCommission' => $filteredTotalCommission,
                'monthlyComparison' => $monthlyComparison
            ]);
        }

        return view('admin.financial.total_revenue', compact(
            'totalRevenue',
            'total_expenses',
            'avg_roas',
            'record_count',
            'totalCommission',
            'filteredTotalRevenue',
            'filteredTotalExpenses',
            'filteredAvgRoas',
            'filteredRecordCount',
            'filteredTotalCommission',
            'recent_records',
            'years',
            'labels',
            'data',
            'expenseData',
            'approved_count',
            'not_approved_count',
            'platforms',
            'year_goal',
            'records',
            'monthlyComparison'
        ));
    }
}