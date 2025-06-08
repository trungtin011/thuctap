<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\FinancialRecord;
use App\Models\Expense;
use App\Models\Department;
use App\Models\Platform;
use App\Models\ExpenseType;
use App\Models\PlatformMetric;
use App\Models\MetricValue;
use App\Models\Daily;
use App\Models\Office;
use App\Models\Route;
use App\Models\Field;

class FinancialController extends Controller
{
    public function index(Request $request)
    {
        try {
            $employee = Auth::user();
            $isMarketing = $employee->department->name === 'Marketing';
            $isAccountant = $employee->department->name === 'Kế toán';
            $isBusiness = $employee->department->name === 'Kinh doanh';

            $request->validate([
                'platform_id' => 'nullable|exists:platforms,id',
                'status' => 'nullable|in:pending,manager_approved,admin_approved,rejected',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            $query = FinancialRecord::where('submitted_by', $employee->id)
                ->with(['department', 'route', 'expenses']);

            // Nếu không có filter ngày, mặc định lấy tháng hiện tại
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

            if ($request->filled('platform_id')) {
                $query->where('platform_id', $request->platform_id);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Sắp xếp theo ngày ghi và ID
            $query->orderBy('record_date', 'desc')
                ->orderBy('id', 'desc');

            $financialRecords = $query->paginate(10);

            // Calculate totals
            $totalRevenue = $financialRecords ? $financialRecords->sum('revenue') : 0;
            $totalCommission = $financialRecords ? $financialRecords->sum('commission') : 0;
            $totalExpense = $financialRecords ? $financialRecords->sum(function ($record) {
                return $record->expenses->sum('amount');
            }) : 0;

            // Tính tổng theo nguồn doanh thu
            $revenueBySource = [];
            $commissionBySource = [];
            $transferBySource = [];
            $expenseBySource = [];
            if ($financialRecords) {
                foreach ($financialRecords as $record) {
                    $noteData = json_decode($record->note);
                    if (isset($noteData->revenue_sources)) {
                        foreach ($noteData->revenue_sources as $source) {
                            $sourceName = $source->source_name;
                            if (!isset($revenueBySource[$sourceName])) {
                                $revenueBySource[$sourceName] = 0;
                                $commissionBySource[$sourceName] = 0;
                                $transferBySource[$sourceName] = 0;
                                $expenseBySource[$sourceName] = 0;
                            }
                            $revenueBySource[$sourceName] += $source->amount;
                            if (!$isAccountant) {
                                $commissionBySource[$sourceName] += $source->commission ?? 0;
                            }
                            $transferBySource[$sourceName] += $source->transfer ?? 0;
                            $expenseBySource[$sourceName] += $source->expense ?? 0;
                        }
                    }
                }
            }
            arsort($revenueBySource); // Sắp xếp theo doanh thu giảm dần

            // Tính tổng Chuyển khoản và Chi cho Kế toán
            $totalTransfer = $isAccountant && $financialRecords
                ? $financialRecords->sum(function ($record) {
                    $noteData = json_decode($record->note);
                    return $noteData->transfer_total ?? 0;
                })
                : 0;
            $totalExpenseTotal = $isAccountant && $financialRecords
                ? $financialRecords->sum(function ($record) {
                    $noteData = json_decode($record->note);
                    return $noteData->expense_total ?? 0;
                })
                : 0;

            $recordCount = $financialRecords ? $financialRecords->count() : 0;

            $platforms = Platform::all();
            $expenseTypes = ExpenseType::all();
            $dailies = Daily::all();
            $offices = Office::all();
            $fields = Field::where('department_id', $employee->department_id)
                ->orWhereNull('department_id')
                ->get();

            return view('employee.financial.index', compact(
                'financialRecords',
                'platforms',
                'expenseTypes',
                'dailies',
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
            $financialRecords = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
            $platforms = $expenseTypes = $dailies = $offices = collect([]);
            $totalRevenue = $totalCommission = $totalExpense = $totalTransfer = $totalExpenseTotal = $recordCount = 0;
            $revenueBySource = $commissionBySource = $transferBySource = $expenseBySource = [];

            return view('employee.financial.index', compact(
                'financialRecords',
                'platforms',
                'expenseTypes',
                'dailies',
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
                'recordCount'
            ));
        }
    }

    public function create()
    {
        $employee = Auth::user();
        $isMarketing = $employee->department->name === 'Marketing';
        $isAccountant = $employee->department->name === 'Kế toán';
        $isBusiness = $employee->department->name === 'Kinh doanh';

        $routes = !$isMarketing && !$isAccountant ? Route::all() : collect([]);
        $fields = !$isAccountant ? Field::where('department_id', $employee->department_id)
            ->orWhereNull('department_id')
            ->get() : collect([]);
        $offices = $isAccountant ? Office::all() : collect([]);
        $expenseTypes = $isMarketing ? ExpenseType::all() : collect([]);

        return view('employee.financial.create', compact(
            'routes',
            'fields',
            'offices',
            'expenseTypes'
        ));
    }

    public function store(Request $request)
    {
        try {
            Log::info('Store request data:', $request->all());

            $employee = Auth::user();
            $department = Department::find($employee->department_id);
            $isMarketing = $department->name === 'Marketing';
            $isAccountant = $department->name === 'Kế toán';
            $isBusiness = $department->name === 'Kinh doanh';

            $validationRules = [
                'department_id' => [
                    'required',
                    'exists:departments,id',
                    function ($attribute, $value, $fail) use ($employee) {
                        if ($value != $employee->department_id) {
                            $fail('Bạn chỉ có thể nhập dữ liệu cho phòng ban của mình.');
                        }
                    },
                ],
            ];

            if ($isMarketing) {
                $validationRules['expenses'] = 'required|array|min:1';
                $validationRules['expenses.*.expense_type_id'] = 'required|exists:expense_types,id';
                $validationRules['expenses.*.amount'] = 'required|numeric|min:0';
                $validationRules['expenses.*.description'] = 'nullable|string|max:255';
            } else {
                $revenue_sources = $request->input('revenue_sources', []);
                foreach ($revenue_sources as &$source) {
                    if (empty($source['amount'])) {
                        $source['amount'] = 0;
                    }
                    $source['transfer'] = $source['transfer'] ?? 0;
                    $source['expense'] = $source['expense'] ?? 0;
                    if ($isAccountant && isset($source['commission'])) {
                        unset($source['commission']); // Remove commission for Accounting
                    }
                }
                $request->merge(['revenue_sources' => $revenue_sources]);

                $validationRules['revenue_sources'] = 'required|array|min:1';
                $validationRules['revenue_sources.*.source_name'] = $isAccountant
                    ? 'required|string|max:255|exists:offices,name'
                    : 'required|string|max:100|exists:fields,name';
                $validationRules['revenue_sources.*.amount'] = 'required|numeric|min:0';
                $validationRules['revenue_sources.*.transfer'] = 'nullable|numeric|min:0';
                $validationRules['revenue_sources.*.expense'] = 'nullable|numeric|min:0';

                if ($isAccountant) {
                    $validationRules['transfer_total'] = 'nullable|numeric|min:0';
                    $validationRules['expense_total'] = 'nullable|numeric|min:0';
                } else {
                    $validationRules['revenue_sources.*.commission'] = 'required|numeric|min:0';
                    $validationRules['route_id'] = 'required|exists:routes,id';
                }
            }

            $validated = $request->validate($validationRules);

            Log::info('Validated data:', $validated);

            $now = now();
            $totalRevenue = 0;
            $totalCommission = 0;
            $note = '';

            if (!$isMarketing) {
                $totalRevenue = collect($request->revenue_sources)->sum('amount');
                $totalCommission = $isAccountant ? 0 : collect($request->revenue_sources)->sum('commission');
                $noteData = [
                    'note' => '',
                    'transfer_total' => $isAccountant ? (float) ($validated['transfer_total'] ?? 0) : 0,
                    'expense_total' => $isAccountant ? (float) ($validated['expense_total'] ?? 0) : 0,
                    'revenue_sources' => array_map(function ($source) use ($isAccountant) {
                        $data = [
                            'source_name' => mb_convert_encoding($source['source_name'], 'UTF-8', 'UTF-8'),
                            'amount' => (float) $source['amount'],
                            'transfer' => (float) ($source['transfer'] ?? 0),
                            'expense' => (float) ($source['expense'] ?? 0),
                        ];
                        if (!$isAccountant) {
                            $data['commission'] = (float) ($source['commission'] ?? 0);
                        }
                        return $data;
                    }, $request->revenue_sources ?? []),
                ];
                $note = json_encode($noteData, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
            }

            $financialRecord = FinancialRecord::create([
                'department_id' => $validated['department_id'],
                'platform_id' => 1, // Default platform
                'dai_ly_id' => 1, // Default dai ly
                'office_id' => 1, // Default office
                'route_id' => $isMarketing || $isAccountant ? null : $validated['route_id'],
                'revenue' => $totalRevenue,
                'record_date' => $now->toDateString(),
                'record_time' => $now->toTimeString(),
                'note' => $note,
                'status' => 'pending',
                'submitted_by' => Auth::id(),
                'commission' => $totalCommission,
            ]);

            if ($isMarketing && isset($validated['expenses'])) {
                foreach ($validated['expenses'] as $expense) {
                    $financialRecord->expenses()->create([
                        'expense_type_id' => $expense['expense_type_id'],
                        'amount' => $expense['amount'],
                        'description' => $expense['description'] ?? null,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => $isMarketing ? 'Bản ghi chi phí đã được thêm thành công.' : 'Bản ghi doanh thu đã được thêm thành công.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error:', $e->errors());
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Store error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lưu bản ghi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $employee = Auth::user();
        $department = Department::find($employee->department_id);
        $showRoutes = $department->name !== 'Marketing';

        $financialRecord = FinancialRecord::where('id', $id)
            ->where('submitted_by', $employee->id)
            ->with(['expenses', 'department', 'platform', 'daily'])
            ->firstOrFail();

        if ($financialRecord->status !== 'pending') {
            abort(403, 'Bạn chỉ có thể sửa bản ghi đang chờ duyệt.');
        }

        $routes = $showRoutes ? Route::all() : collect();

        // Get fields for the user's department
        $fields = Field::where('department_id', $employee->department_id)
            ->orWhereNull('department_id')
            ->get();

        // Get expense types for marketing department
        $expenseTypes = ExpenseType::all();

        // Đính kèm revenue_sources từ note
        $noteData = json_decode($financialRecord->note);
        $financialRecord->revenue_sources = $noteData->revenue_sources ?? [];
        $isAccountant = $department->name === 'Kế toán';

        return view('employee.financial.edit', compact('financialRecord', 'department', 'routes', 'fields', 'showRoutes', 'expenseTypes', 'isAccountant'));
    }

    public function update(Request $request, $id)
    {
        try {
            $employee = Auth::user();
            $department = Department::find($employee->department_id);
            $isMarketing = $department->name === 'Marketing';
            $isAccountant = $department->name === 'Kế toán';

            $financialRecord = FinancialRecord::where('id', $id)
                ->where('submitted_by', $employee->id)
                ->firstOrFail();

            if ($financialRecord->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'errors' => ['status' => ['Bạn chỉ có thể sửa bản ghi đang chờ duyệt.']]
                ], 422);
            }

            $validationRules = [
                'department_id' => [
                    'required',
                    'exists:departments,id',
                    function ($attribute, $value, $fail) use ($employee) {
                        if ($value != $employee->department_id) {
                            $fail('Bạn chỉ có thể nhập dữ liệu cho phòng ban của mình.');
                        }
                    },
                ],
            ];

            if ($isMarketing) {
                $validationRules['expenses'] = 'required|array|min:1';
                $validationRules['expenses.*.expense_type_id'] = 'required|exists:expense_types,id';
                $validationRules['expenses.*.amount'] = 'required|numeric|min:0';
                $validationRules['expenses.*.description'] = 'nullable|string|max:255';
            } else {
                // Gán 0 cho các amount và commission rỗng hoặc null
                $revenue_sources = $request->input('revenue_sources', []);
                foreach ($revenue_sources as &$source) {
                    if (empty($source['amount'])) {
                        $source['amount'] = 0;
                    }
                    if ($isAccountant && isset($source['commission'])) {
                        unset($source['commission']); // Loại bỏ hoa hồng cho Kế toán
                    } elseif (empty($source['commission'])) {
                        $source['commission'] = 0;
                    }
                    $source['transfer'] = $source['transfer'] ?? 0;
                    $source['expense'] = $source['expense'] ?? 0;
                }
                $request->merge(['revenue_sources' => $revenue_sources]);

                if ($isAccountant) {
                    $validationRules['revenue_sources'] = 'required|array|min:1';
                    $validationRules['revenue_sources.*.source_name'] = 'required|string|max:255|exists:offices,name';
                    $validationRules['transfer_total'] = 'nullable|numeric|min:0';
                    $validationRules['expense_total'] = 'nullable|numeric|min:0';
                } else {
                    $validationRules['revenue_sources'] = 'required|array|min:1';
                    $validationRules['revenue_sources.*.source_name'] = 'required|string|max:100|exists:fields,name';
                    $validationRules['revenue_sources.*.commission'] = 'required|numeric|min:0';
                }
                $validationRules['revenue_sources.*.amount'] = 'required|numeric|min:0';
                $validationRules['revenue_sources.*.transfer'] = 'nullable|numeric|min:0';
                $validationRules['revenue_sources.*.expense'] = 'nullable|numeric|min:0';
                $validationRules['route_id'] = 'required|exists:routes,id';
            }

            $validated = $request->validate($validationRules);

            $updateData = [
                'department_id' => $validated['department_id'],
            ];

            if (!$isMarketing) {
                $totalRevenue = collect($request->revenue_sources)->sum('amount');
                $totalCommission = $isAccountant ? 0 : collect($request->revenue_sources)->sum('commission'); // Không tính commission cho Kế toán
                $noteData = [
                    'note' => '',
                    'transfer_total' => $isAccountant ? (float) ($validated['transfer_total'] ?? 0) : 0,
                    'expense_total' => $isAccountant ? (float) ($validated['expense_total'] ?? 0) : 0,
                    'revenue_sources' => array_map(function ($source) use ($isAccountant) {
                        $data = [
                            'source_name' => mb_convert_encoding($source['source_name'], 'UTF-8', 'UTF-8'),
                            'amount' => (float) $source['amount'],
                            'transfer' => (float) ($source['transfer'] ?? 0),
                            'expense' => (float) ($source['expense'] ?? 0),
                        ];
                        if (!$isAccountant) {
                            $data['commission'] = (float) ($source['commission'] ?? 0);
                        }
                        return $data;
                    }, $request->revenue_sources ?? []),
                ];
                $note = json_encode($noteData, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

                $updateData['revenue'] = $totalRevenue;
                $updateData['commission'] = $totalCommission;
                $updateData['note'] = $note;
                $updateData['route_id'] = $validated['route_id'];
            }

            $financialRecord->update($updateData);

            if ($isMarketing) {
                // Delete existing expenses
                $financialRecord->expenses()->delete();

                // Create new expenses
                foreach ($validated['expenses'] as $expense) {
                    $financialRecord->expenses()->create([
                        'expense_type_id' => $expense['expense_type_id'],
                        'amount' => $expense['amount'],
                        'description' => $expense['description'] ?? null,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => $isMarketing ? 'Bản ghi chi phí đã được cập nhật thành công.' : 'Bản ghi doanh thu đã được cập nhật thành công.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error:', $e->errors());
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Update error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi cập nhật bản ghi. Vui lòng thử lại.'
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $employee = Auth::user();
            $financialRecord = FinancialRecord::where('id', $id)
                ->where('submitted_by', $employee->id)
                ->firstOrFail();

            if ($financialRecord->status !== 'pending') {
                return redirect()->back()->with('error', 'Bạn chỉ có thể xóa bản ghi đang chờ duyệt.');
            }

            $recordedAt = $financialRecord->record_date . ' ' . $financialRecord->record_time;

            MetricValue::whereIn('metric_id', PlatformMetric::where('platform_id', $financialRecord->platform_id)->pluck('id'))
                ->where('recorded_at', $recordedAt)
                ->where('financial_record_id', $financialRecord->id)
                ->delete();

            $financialRecord->delete();

            // Sau khi xóa, chuyển hướng về trang danh sách kèm thông báo
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
            $query->where('recorded_at', $record->record_date . ' ' . $record->record_time)
                ->where('financial_record_id', $record->id);
        }

        $values = $query->get();
        return response()->json(['values' => $values]);
    }
}
