<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\FinancialRecord;
use App\Models\Expense;
use App\Models\OfficeRevenue;
use App\Models\Department;
use App\Models\Platform;
use App\Models\ExpenseType;
use App\Models\PlatformMetric;
use App\Models\MetricValue;
use App\Models\Office;
use App\Models\Route;
use App\Models\Field;

class FinancialController extends Controller
{
    public function index(Request $request)
    {
        try {
            $employee = Auth::user();
            $departmentName = $employee->department->name;

            $request->validate([
                'platform_id' => 'nullable|exists:platforms,id',
                'status' => 'nullable|in:pending,manager_approved,admin_approved,rejected',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            if ($departmentName === 'Kế toán') {
                $query = OfficeRevenue::query()->with(['office']);
            } else {
                $query = FinancialRecord::where('submitted_by', $employee->id)
                    ->with(['department', 'route', 'expenses', 'platform']);
            }

            // Default to current month if no date filters
            if (!$request->filled('start_date') && !$request->filled('end_date')) {
                $currentMonth = now()->startOfMonth();
                $query->whereYear('record_date', $currentMonth->year)
                    ->whereMonth('record_date', $currentMonth->month);
            } else {
                if ($request->filled('start_date')) {
                    $query->where('record_date', '>=', $request->start_date);
                }
                if ($request->filled('end_date')) {
                    $query->where('record_date', '<=', $request->end_date);
                }
            }

            if ($departmentName !== 'Kế toán' && $request->filled('platform_id')) {
                $query->where('platform_id', $request->platform_id);
            }

            if ($departmentName !== 'Kế toán' && $request->filled('status')) {
                $query->where('status', $request->status);
            }

            $query->orderBy('record_date', 'desc')->orderBy('id', 'desc');

            $records = $query->paginate(10);

            $totalRevenue = $totalCommission = $totalExpense = $totalTransfer = $totalExpenseTotal = 0;
            $revenueBySource = $commissionBySource = $transferBySource = $expenseBySource = [];

            if ($departmentName === 'Kế toán') {
                $totalRevenue = $records->sum('cash');
                $totalTransfer = $records->sum('bank_transfer');
                $totalExpenseTotal = $records->sum('expense');
                foreach ($records as $record) {
                    $sourceName = $record->office->name;
                    $revenueBySource[$sourceName] = ($revenueBySource[$sourceName] ?? 0) + $record->cash;
                    $transferBySource[$sourceName] = ($transferBySource[$sourceName] ?? 0) + $record->bank_transfer;
                    $expenseBySource[$sourceName] = ($expenseBySource[$sourceName] ?? 0) + $record->expense;
                }
            } else {
                $totalRevenue = $records->sum('revenue');
                $totalCommission = $records->sum('commission');
                $totalExpense = $records->sum(function ($record) {
                    return $record->expenses->sum('amount');
                });
                foreach ($records as $record) {
                    $noteData = json_decode($record->note);
                    if (isset($noteData->revenue_sources)) {
                        foreach ($noteData->revenue_sources as $source) {
                            $sourceName = $source->source_name;
                            $revenueBySource[$sourceName] = ($revenueBySource[$sourceName] ?? 0) + $source->amount;
                            $commissionBySource[$sourceName] = ($commissionBySource[$sourceName] ?? 0) + ($source->commission ?? 0);
                            $transferBySource[$sourceName] = ($transferBySource[$sourceName] ?? 0) + ($source->transfer ?? 0);
                            $expenseBySource[$sourceName] = ($expenseBySource[$sourceName] ?? 0) + ($source->expense ?? 0);
                        }
                    }
                }
            }
            arsort($revenueBySource);

            $recordCount = $records->count();
            $platforms = Platform::all();
            $expenseTypes = ExpenseType::all();
            $offices = Office::all();
            $fields = Field::where('department_id', $employee->department_id)
                ->orWhereNull('department_id')
                ->get();

            return view('employee.financial.index', compact(
                'records',
                'platforms',
                'expenseTypes',
                'offices',
                'totalRevenue',
                'totalCommission',
                'totalExpense',
                'revenueBySource',
                'commissionBySource',
                'transferBySource',
                'expenseBySource',
                'totalTransfer',
                'totalExpenseTotal',
                'recordCount',
                'fields'
            ));
        } catch (\Exception $e) {
            Log::error('Index error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return view('employee.financial.index', [
                'records' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10),
                'platforms' => collect([]),
                'expenseTypes' => collect([]),
                'offices' => collect([]),
                'totalRevenue' => 0,
                'totalCommission' => 0,
                'totalExpense' => 0,
                'totalTransfer' => 0,
                'totalExpenseTotal' => 0,
                'recordCount' => 0,
                'revenueBySource' => [],
                'commissionBySource' => [],
                'transferBySource' => [],
                'expenseBySource' => []
            ]);
        }
    }

    // Marketing Department
    public function createMarketing()
    {
        $employee = Auth::user();
        $platforms = Platform::all();
        $routes = Route::all();
        $expenseTypes = ExpenseType::all();
        $offices = collect([]);
        $fields = collect([]);

        return view('employee.financial.create', compact(
            'routes',
            'fields',
            'offices',
            'expenseTypes',
            'platforms'
        ));
    }

    public function storeMarketing(Request $request)
    {
        try {
            $employee = Auth::user();
            $validated = $request->validate([
                'department_id' => [
                    'required',
                    'exists:departments,id',
                    function ($attribute, $value, $fail) use ($employee) {
                        if ($value != $employee->department_id) {
                            $fail('Bạn chỉ có thể nhập dữ liệu cho phòng ban của mình.');
                        }
                    },
                ],
                'platform_id' => 'required|exists:platforms,id',
                'route_id' => 'required|exists:routes,id',
                'expenses' => 'required|array|min:1',
                'expenses.*.expense_type_id' => 'required|exists:expense_types,id',
                'expenses.*.amount' => 'required|numeric|min:0',
                'expenses.*.description' => 'nullable|string|max:255',
            ]);

            $now = now();
            $financialRecord = FinancialRecord::create([
                'department_id' => $validated['department_id'],
                'platform_id' => $validated['platform_id'],
                'route_id' => $validated['route_id'],
                'dai_ly_id' => 1,
                'office_id' => 1,
                'revenue' => 0,
                'commission' => 0,
                'record_date' => $now->toDateString(),
                'record_time' => $now->toTimeString(),
                'note' => '',
                'status' => 'pending',
                'submitted_by' => $employee->id,
            ]);

            foreach ($validated['expenses'] as $expense) {
                $financialRecord->expenses()->create([
                    'expense_type_id' => $expense['expense_type_id'],
                    'amount' => $expense['amount'],
                    'description' => $expense['description'] ?? null,
                ]);
            }

            if ($request->has('metrics')) {
                foreach ($request->input('metrics') as $metricId => $value) {
                    MetricValue::create([
                        'metric_id' => $metricId,
                        'financial_record_id' => $financialRecord->id,
                        'value' => $value,
                        'recorded_at' => $now,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Bản ghi chi phí đã được thêm thành công.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error:', $e->errors());
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Store error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lưu bản ghi.'
            ], 500);
        }
    }

    public function editMarketing($id)
    {
        $employee = Auth::user();
        $financialRecord = FinancialRecord::where('id', $id)
            ->where('submitted_by', $employee->id)
            ->with(['expenses', 'department', 'platform', 'route'])
            ->firstOrFail();

        if ($financialRecord->status !== 'pending') {
            abort(403, 'Bạn chỉ có thể sửa bản ghi đang chờ duyệt.');
        }

        $platforms = Platform::all();
        $routes = Route::all();
        $expenseTypes = ExpenseType::all();
        $offices = collect([]);
        $fields = collect([]);
        $noteData = json_decode($financialRecord->note);
        $financialRecord->revenue_sources = $noteData->revenue_sources ?? [];

        return view('employee.financial.edit', compact(
            'financialRecord',
            'routes',
            'fields',
            'expenseTypes',
            'platforms',
            'offices'
        ));
    }

    public function updateMarketing(Request $request, $id)
    {
        try {
            $employee = Auth::user();
            $financialRecord = FinancialRecord::where('id', $id)
                ->where('submitted_by', $employee->id)
                ->firstOrFail();

            if ($financialRecord->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'errors' => ['status' => ['Bạn chỉ có thể sửa bản ghi đang chờ duyệt.']]
                ], 422);
            }

            $validated = $request->validate([
                'department_id' => [
                    'required',
                    'exists:departments,id',
                    function ($attribute, $value, $fail) use ($employee) {
                        if ($value != $employee->department_id) {
                            $fail('Bạn chỉ có thể nhập dữ liệu cho phòng ban của mình.');
                        }
                    },
                ],
                'platform_id' => 'required|exists:platforms,id',
                'route_id' => 'required|exists:routes,id',
                'expenses' => 'required|array|min:1',
                'expenses.*.expense_type_id' => 'required|exists:expense_types,id',
                'expenses.*.amount' => 'required|numeric|min:0',
                'expenses.*.description' => 'nullable|string|max:255',
            ]);

            $financialRecord->update([
                'department_id' => $validated['department_id'],
                'platform_id' => $validated['platform_id'],
                'route_id' => $validated['route_id'],
            ]);

            $financialRecord->expenses()->delete();
            foreach ($validated['expenses'] as $expense) {
                $financialRecord->expenses()->create([
                    'expense_type_id' => $expense['expense_type_id'],
                    'amount' => $expense['amount'],
                    'description' => $expense['description'] ?? null,
                ]);
            }

            if ($request->has('metrics')) {
                MetricValue::where('financial_record_id', $financialRecord->id)->delete();
                foreach ($request->input('metrics') as $metricId => $value) {
                    MetricValue::create([
                        'metric_id' => $metricId,
                        'financial_record_id' => $financialRecord->id,
                        'value' => $value,
                        'recorded_at' => now(),
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Bản ghi chi phí đã được cập nhật thành công.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error:', $e->errors());
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Update error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi cập nhật bản ghi.'
            ], 500);
        }
    }

    // Accounting Department
    public function createAccounting()
    {
        $employee = Auth::user();
        $offices = Office::all();
        $platforms = collect([]);
        $routes = collect([]);
        $expenseTypes = collect([]);
        $fields = collect([]);

        return view('employee.financial.create', compact(
            'routes',
            'fields',
            'offices',
            'expenseTypes',
            'platforms'
        ));
    }

    public function storeAccounting(Request $request)
    {
        try {
            $employee = Auth::user();
            $validated = $request->validate([
                'department_id' => [
                    'required',
                    'exists:departments,id',
                    function ($attribute, $value, $fail) use ($employee) {
                        if ($value != $employee->department_id) {
                            $fail('Bạn chỉ có thể nhập dữ liệu cho phòng ban của mình.');
                        }
                    },
                ],
                'expense' => 'required|numeric|min:0',
                'bank_transfer' => 'required|numeric|min:0',
                'revenue_sources' => 'required|array|min:1',
                'revenue_sources.*.source_name' => 'required|string|max:255|exists:offices,name',
                'revenue_sources.*.cash' => 'required|numeric|min:0',
            ]);

            $now = now();
            foreach ($validated['revenue_sources'] as $index => $source) {
                $office = Office::where('name', $source['source_name'])->first();
                OfficeRevenue::create([
                    'office_id' => $office->id,
                    'cash' => $source['cash'],
                    'bank_transfer' => $validated['bank_transfer'], // Chuyển khoản chung cho tất cả
                    'expense' => $validated['expense'], // Chi phí chung cho tất cả
                    'total' => $source['cash'] + $validated['bank_transfer'] - $validated['expense'],
                    'record_date' => $now->toDateString(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Bản ghi doanh thu đã được thêm thành công.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error:', $e->errors());
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Store error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lưu bản ghi.'
            ], 500);
        }
    }

    public function editAccounting($id)
    {
        $employee = Auth::user();
        $officeRevenue = OfficeRevenue::where('id', $id)->with(['office'])->firstOrFail();
        $offices = Office::all();
        $platforms = collect([]);
        $routes = collect([]);
        $expenseTypes = collect([]);
        $fields = collect([]);

        // Chuẩn bị dữ liệu để hiển thị
        $officeRevenue->revenue_sources = [[
            'source_name' => $officeRevenue->office->name,
            'cash' => $officeRevenue->cash,
            'bank_transfer' => $officeRevenue->bank_transfer,
            'expense' => $officeRevenue->expense,
        ]];

        return view('employee.financial.edit', compact(
            'officeRevenue',
            'routes',
            'fields',
            'expenseTypes',
            'platforms',
            'offices'
        ));
    }

    public function updateAccounting(Request $request, $id)
    {
        try {
            $employee = Auth::user();
            $officeRevenue = OfficeRevenue::where('id', $id)->firstOrFail();

            $validated = $request->validate([
                'department_id' => [
                    'required',
                    'exists:departments,id',
                    function ($attribute, $value, $fail) use ($employee) {
                        if ($value != $employee->department_id) {
                            $fail('Bạn chỉ có thể nhập dữ liệu cho phòng ban của mình.');
                        }
                    },
                ],
                'expense' => 'required|numeric|min:0',
                'bank_transfer' => 'required|numeric|min:0',
                'revenue_sources' => 'required|array|min:1',
                'revenue_sources.*.source_name' => 'required|string|max:255|exists:offices,name',
                'revenue_sources.*.cash' => 'required|numeric|min:0',
            ]);

            $source = $validated['revenue_sources'][0];
            $office = Office::where('name', $source['source_name'])->first();
            $officeRevenue->update([
                'office_id' => $office->id,
                'cash' => $source['cash'],
                'bank_transfer' => $validated['bank_transfer'],
                'expense' => $validated['expense'],
                'total' => $source['cash'] + $validated['bank_transfer'] - $validated['expense'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bản ghi doanh thu đã được cập nhật thành công.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error:', $e->errors());
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Update error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi cập nhật bản ghi.'
            ], 500);
        }
    }

   

    // Business Department
    public function createBusiness()
    {
        $employee = Auth::user();
        $fields = Field::where('department_id', $employee->department_id)->get();
        $routes = Route::all();
        $platforms = collect([]);
        $expenseTypes = collect([]);
        $offices = collect([]);

        return view('employee.financial.create', compact(
            'routes',
            'fields',
            'offices',
            'expenseTypes',
            'platforms'
        ));
    }

    public function storeBusiness(Request $request)
    {
        try {
            $employee = Auth::user();
            $revenue_sources = $request->input('revenue_sources', []);
            foreach ($revenue_sources as &$source) {
                $source['amount'] = empty($source['amount']) ? 0 : $source['amount'];
                $source['commission'] = empty($source['commission']) ? 0 : $source['commission'];
            }
            $request->merge(['revenue_sources' => $revenue_sources]);

            $validated = $request->validate([
                'department_id' => [
                    'required',
                    'exists:departments,id',
                    function ($attribute, $value, $fail) use ($employee) {
                        if ($value != $employee->department_id) {
                            $fail('Bạn chỉ có thể nhập dữ liệu cho phòng ban của mình.');
                        }
                    },
                ],
                'route_id' => 'required|exists:routes,id',
                'revenue_sources' => 'required|array|min:1',
                'revenue_sources.*.source_name' => 'required|string|max:100|exists:fields,name',
                'revenue_sources.*.amount' => 'required|numeric|min:0',
                'revenue_sources.*.commission' => 'required|numeric|min:0',
            ]);

            $now = now();
            $totalRevenue = collect($request->revenue_sources)->sum('amount');
            $totalCommission = collect($request->revenue_sources)->sum('commission');
            $noteData = [
                'note' => '',
                'revenue_sources' => array_map(function ($source) {
                    return [
                        'source_name' => mb_convert_encoding($source['source_name'], 'UTF-8', 'UTF-8'),
                        'amount' => (float) $source['amount'],
                        'commission' => (float) $source['commission'],
                        'transfer' => 0,
                        'expense' => 0,
                    ];
                }, $request->revenue_sources),
            ];
            $note = json_encode($noteData, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

            FinancialRecord::create([
                'department_id' => $validated['department_id'],
                'platform_id' => 1,
                'dai_ly_id' => 1,
                'office_id' => 1,
                'route_id' => $validated['route_id'],
                'revenue' => $totalRevenue,
                'commission' => $totalCommission,
                'record_date' => $now->toDateString(),
                'record_time' => $now->toTimeString(),
                'note' => $note,
                'status' => 'pending',
                'submitted_by' => $employee->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bản ghi doanh thu đã được thêm thành công.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error:', $e->errors());
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Store error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lưu bản ghi.'
            ], 500);
        }
    }

    public function editBusiness($id)
    {
        $employee = Auth::user();
        $financialRecord = FinancialRecord::where('id', $id)
            ->where('submitted_by', $employee->id)
            ->with(['department', 'route'])
            ->firstOrFail();

        if ($financialRecord->status !== 'pending') {
            abort(403, 'Bạn chỉ có thể sửa bản ghi đang chờ duyệt.');
        }

        $routes = Route::all();
        $fields = Field::where('department_id', $employee->department_id)->get();
        $expenseTypes = collect([]);
        $platforms = collect([]);
        $offices = collect([]);
        $noteData = json_decode($financialRecord->note);
        $financialRecord->revenue_sources = $noteData->revenue_sources ?? [];

        return view('employee.financial.edit', compact(
            'financialRecord',
            'routes',
            'fields',
            'expenseTypes',
            'platforms',
            'offices'
        ));
    }

    public function updateBusiness(Request $request, $id)
    {
        try {
            $employee = Auth::user();
            $financialRecord = FinancialRecord::where('id', $id)
                ->where('submitted_by', $employee->id)
                ->firstOrFail();

            if ($financialRecord->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'errors' => ['status' => ['Bạn chỉ có thể sửa bản ghi đang chờ duyệt.']]
                ], 422);
            }

            $revenue_sources = $request->input('revenue_sources', []);
            foreach ($revenue_sources as &$source) {
                $source['amount'] = empty($source['amount']) ? 0 : $source['amount'];
                $source['commission'] = empty($source['commission']) ? 0 : $source['commission'];
            }
            $request->merge(['revenue_sources' => $revenue_sources]);

            $validated = $request->validate([
                'department_id' => [
                    'required',
                    'exists:departments,id',
                    function ($attribute, $value, $fail) use ($employee) {
                        if ($value != $employee->department_id) {
                            $fail('Bạn chỉ có thể nhập dữ liệu cho phòng ban của mình.');
                        }
                    },
                ],
                'route_id' => 'required|exists:routes,id',
                'revenue_sources' => 'required|array|min:1',
                'revenue_sources.*.source_name' => 'required|string|max:100|exists:fields,name',
                'revenue_sources.*.amount' => 'required|numeric|min:0',
                'revenue_sources.*.commission' => 'required|numeric|min:0',
            ]);

            $totalRevenue = collect($request->revenue_sources)->sum('amount');
            $totalCommission = collect($request->revenue_sources)->sum('commission');
            $noteData = [
                'note' => '',
                'revenue_sources' => array_map(function ($source) {
                    return [
                        'source_name' => mb_convert_encoding($source['source_name'], 'UTF-8', 'UTF-8'),
                        'amount' => (float) $source['amount'],
                        'commission' => (float) $source['commission'],
                        'transfer' => 0,
                        'expense' => 0,
                    ];
                }, $request->revenue_sources),
            ];
            $note = json_encode($noteData, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

            $financialRecord->update([
                'department_id' => $validated['department_id'],
                'route_id' => $validated['route_id'],
                'revenue' => $totalRevenue,
                'commission' => $totalCommission,
                'note' => $note,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bản ghi doanh thu đã được cập nhật thành công.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error:', $e->errors());
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Update error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi cập nhật bản ghi.'
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $employee = Auth::user();
            $departmentName = $employee->department->name;

            if ($departmentName === 'Kế toán') {
                $record = OfficeRevenue::where('id', $id)->firstOrFail();
            } else {
                $record = FinancialRecord::where('id', $id)
                    ->where('submitted_by', $employee->id)
                    ->firstOrFail();
            }

            if ($departmentName !== 'Kế toán' && $record->status !== 'pending') {
                return redirect()->back()->with('error', 'Bạn chỉ có thể xóa bản ghi đang chờ duyệt.');
            }

            if ($departmentName !== 'Kế toán') {
                MetricValue::where('financial_record_id', $record->id)->delete();
                $record->expenses()->delete();
            }
            $record->delete();

            return redirect()->route('employee.financial.index')->with('success', 'Bản ghi đã được xóa thành công.');
        } catch (\Exception $e) {
            Log::error('Delete error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi xóa bản ghi. Vui lòng thử lại.');
        }
    }

    public function getMetrics($platformId)
    {
        $metrics = PlatformMetric::where('platform_id', $platformId)
            ->get(['id', 'name', 'unit', 'data_type']);
        return response()->json(['metrics' => $metrics]);
    }

    public function getMetricValues($metricId, Request $request)
    {
        $query = MetricValue::where('metric_id', $metricId)
            ->select('value', 'recorded_at');

        if ($request->has('record_id')) {
            $record = FinancialRecord::findOrFail($request->record_id);
            $query->where('financial_record_id', $record->id);
        }

        $values = $query->get();
        return response()->json(['values' => $values]);
    }

    public function getMetricValuesForRecord($recordId)
    {
        $record = FinancialRecord::findOrFail($recordId);
        $metricValues = MetricValue::where('financial_record_id', $recordId)
            ->with('metric')
            ->get();
        return response()->json(['metric_values' => $metricValues]);
    }
}
