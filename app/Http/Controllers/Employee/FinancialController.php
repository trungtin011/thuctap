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

            if ($departmentName === 'Kế Toán') {
                $query = OfficeRevenue::query()->with(['offices']); // Lấy cả các office liên kết
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

            if ($departmentName !== 'Kế Toán' && $request->filled('platform_id')) {
                $query->where('platform_id', $request->platform_id);
            }

            if ($departmentName !== 'Kế Toán' && $request->filled('status')) {
                $query->where('status', $request->status);
            }

            $query->orderBy('record_date', 'desc')->orderBy('id', 'desc');

            $records = $query->paginate(10);

            $totalRevenue = $totalCommission = $totalExpense = $totalTransfer = $totalExpenseTotal = 0;
            $revenueBySource = $commissionBySource = $transferBySource = $expenseBySource = [];

            if ($departmentName === 'Kế Toán') {
                $totalRevenue = $records->sum('cash');
                $totalTransfer = $records->sum('bank_transfer');
                $totalExpenseTotal = $records->sum('expense');
                foreach ($records as $record) {
                    foreach ($record->offices as $office) {
                        $sourceName = $office->name;
                        $revenueBySource[$sourceName] = ($revenueBySource[$sourceName] ?? 0) + $record->cash;
                        $transferBySource[$sourceName] = ($transferBySource[$sourceName] ?? 0) + $record->bank_transfer;
                        $expenseBySource[$sourceName] = ($expenseBySource[$sourceName] ?? 0) + $record->expense;
                    }
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
        $financialRecord = FinancialRecord::with(['expenses', 'metricValues', 'platform.metrics'])->findOrFail($id);
        $routes = Route::all();
        $platforms = Platform::all();
        $expenseTypes = ExpenseType::all();

        // Lấy metric values từ database
        $metricsData = $financialRecord->metricValues->pluck('value', 'metric_id')->toArray();

        Log::info('Editing financial record', [
            'record_id' => $id,
            'submitted_by' => $financialRecord->submitted_by,
            'employee_id' => $employee->id,
            'metrics_data' => $metricsData
        ]);

        return view('employee.financial.edit', compact(
            'financialRecord',
            'routes',
            'platforms',
            'expenseTypes',
            'metricsData'
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
                'revenue_sources.*.office_id' => 'required|exists:offices,id',
                'revenue_sources.*.value' => 'required|numeric|min:0',
            ]);

            $now = now();
            $totalValue = array_sum(array_column($validated['revenue_sources'], 'value'));

            // Tạo bản ghi OfficeRevenue
            $officeRevenue = OfficeRevenue::create([
                'cash' => $totalValue, // Tổng giá trị từ các văn phòng
                'bank_transfer' => $validated['bank_transfer'],
                'expense' => $validated['expense'],
                'total' => $totalValue + $validated['bank_transfer'] + $validated['expense'],
                'record_date' => $now->toDateString(),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Gắn các văn phòng và lưu giá trị vào bảng trung gian
            $officeData = [];
            foreach ($validated['revenue_sources'] as $source) {
                $officeData[$source['office_id']] = ['value' => $source['value']];
            }
            $officeRevenue->offices()->attach($officeData);

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
        $officeRevenue = OfficeRevenue::where('id', $id)->with('offices')->firstOrFail();
        $offices = Office::all();
        $platforms = collect([]);
        $routes = collect([]);
        $expenseTypes = collect([]);
        $fields = collect([]);

        // Chuẩn bị revenue_sources từ bảng trung gian office_revenue_offices
        $revenueSources = $officeRevenue->offices->map(function ($office) {
            return [
                'office_id' => $office->id,
                'value' => $office->pivot->value ?? 0, // Lấy value từ pivot
            ];
        })->toArray();

        // Gán revenue_sources vào officeRevenue
        $officeRevenue->revenue_sources = $revenueSources;

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
                'revenue_sources.*.office_id' => 'required|exists:offices,id',
                'revenue_sources.*.value' => 'required|numeric|min:0',
            ]);

            $totalValue = array_sum(array_column($validated['revenue_sources'], 'value'));

            // Cập nhật bản ghi OfficeRevenue
            $officeRevenue->update([
                'cash' => $totalValue,
                'bank_transfer' => $validated['bank_transfer'],
                'expense' => $validated['expense'],
                'total' => $totalValue + $validated['bank_transfer'] + $validated['expense'],
                'updated_at' => now(),
            ]);

            // Đồng bộ các văn phòng và giá trị
            $officeData = [];
            foreach ($validated['revenue_sources'] as $source) {
                $officeData[$source['office_id']] = ['value' => $source['value']];
            }
            $officeRevenue->offices()->sync($officeData);

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
        try {
            $employee = Auth::user();

            if (!$employee->department) {
                Log::error('Employee has no department assigned', ['employee_id' => $employee->id]);
                return redirect()->back()->with('error', 'Không thể xác định phòng ban của bạn. Vui lòng liên hệ quản trị viên.');
            }

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

            // Giải mã note và chuẩn hóa revenue_sources thành mảng
            $noteData = json_decode($financialRecord->note, true); // Trả về mảng
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning('Invalid JSON in note column', ['record_id' => $id, 'note' => $financialRecord->note]);
                $financialRecord->revenue_sources = [];
            } else {
                // Nếu revenue_sources là đối tượng, chuyển thành mảng đơn
                if (isset($noteData['revenue_sources']) && !is_array($noteData['revenue_sources'])) {
                    $financialRecord->revenue_sources = [$noteData['revenue_sources']];
                } else {
                    $financialRecord->revenue_sources = $noteData['revenue_sources'] ?? [];
                }
            }

            return view('employee.financial.edit', compact(
                'financialRecord',
                'routes',
                'fields',
                'expenseTypes',
                'platforms',
                'offices'
            ));
        } catch (\Exception $e) {
            Log::error('Edit business error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi tải bản ghi. Vui lòng thử lại.');
        }
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

            if ($departmentName === 'Kế Toán') {
                $record = OfficeRevenue::where('id', $id)->firstOrFail();
            } else {
                $record = FinancialRecord::where('id', $id)
                    ->where('submitted_by', $employee->id)
                    ->firstOrFail();
            }

            if ($departmentName !== 'Kế Toán' && $record->status !== 'pending') {
                return redirect()->back()->with('error', 'Bạn chỉ có thể xóa bản ghi đang chờ duyệt.');
            }

            if ($departmentName !== 'Kế Toán') {
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

    public function getMetricValuesForRecord($recordId)
    {
        $record = FinancialRecord::with('metricValues')->findOrFail($recordId);
        $metricValues = $record->metricValues->mapWithKeys(function ($metricValue) {
            return [$metricValue->metric_id => $metricValue->value];
        })->toArray();
        return response()->json(['metric_values' => $metricValues]);
    }
}
