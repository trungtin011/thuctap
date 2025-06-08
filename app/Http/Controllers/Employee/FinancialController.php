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
        $employee = Auth::user();

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

        $platforms = Platform::all();
        $expenseTypes = ExpenseType::all();
        $dailies = Daily::all();
        $offices = Office::all();

        return view('employee.financial.index', compact(
            'financialRecords',
            'platforms',
            'expenseTypes',
            'dailies',
            'offices'
        ));
    }

    public function create()
    {
        $employee = Auth::user();
        $department = Department::find($employee->department_id);
        if (!$department) {
            abort(403, 'Bạn không thuộc phòng ban nào.');
        }

        // Only show routes for non-marketing departments
        $showRoutes = $department->name !== 'Marketing';
        $routes = $showRoutes ? Route::all() : collect();
        
        // Get fields for the user's department
        $fields = Field::where('department_id', $employee->department_id)
            ->orWhereNull('department_id')
            ->get();

        // Get expense types for marketing department
        $expenseTypes = ExpenseType::all();

        // Debug information
        Log::info('Employee Department ID: ' . $employee->department_id);
        Log::info('Fields count: ' . $fields->count());
        Log::info('Fields data: ', $fields->toArray());

        return view('employee.financial.create', compact('department', 'routes', 'fields', 'showRoutes', 'expenseTypes'));
    }

    public function store(Request $request)
    {
        try {
            Log::info('Store request data:', $request->all());

            $employee = Auth::user();
            $department = Department::find($employee->department_id);
            $isMarketing = $department->name === 'Marketing';

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
                // Gán 0 cho các amount rỗng hoặc null
                $revenue_sources = $request->input('revenue_sources', []);
                foreach ($revenue_sources as &$source) {
                    if (empty($source['amount'])) {
                        $source['amount'] = 0;
                    }
                }
                $request->merge(['revenue_sources' => $revenue_sources]);

                $validationRules['revenue_sources'] = 'required|array|min:1';
                $validationRules['revenue_sources.*.source_name'] = 'required|string|max:100|exists:fields,name';
                $validationRules['revenue_sources.*.amount'] = 'required|numeric|min:0';
                $validationRules['route_id'] = 'required|exists:routes,id';
            }

            $validated = $request->validate($validationRules);

            Log::info('Validated data:', $validated);

            $now = now();
            $totalRevenue = 0;
            $totalCommission = 0;
            $note = '';

            if (!$isMarketing) {
                $totalRevenue = collect($request->revenue_sources)->sum('amount');
                $totalCommission = collect($request->revenue_sources)->sum('commission');
                $noteData = [
                    'note' => '',
                    'revenue_sources' => array_map(function ($source) {
                        return [
                            'source_name' => mb_convert_encoding($source['source_name'], 'UTF-8', 'UTF-8'),
                            'amount' => (float) $source['amount'],
                            'commission' => (float) ($source['commission'] ?? 0),
                        ];
                    }, $request->revenue_sources ?? []),
                ];
                $note = json_encode($noteData, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
            }

            $financialRecord = FinancialRecord::create([
                'department_id' => $validated['department_id'],
                'platform_id' => 1, // Default platform
                'dai_ly_id' => 1, // Default dai ly
                'office_id' => 1, // Default office
                'route_id' => $isMarketing ? null : $validated['route_id'],
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
        $financialRecord->revenue_sources = json_decode($financialRecord->note)->revenue_sources ?? [];

        return view('employee.financial.edit', compact('financialRecord', 'department', 'routes', 'fields', 'showRoutes', 'expenseTypes'));
    }

    public function update(Request $request, $id)
    {
        try {
            $employee = Auth::user();
            $department = Department::find($employee->department_id);
            $isMarketing = $department->name === 'Marketing';

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
                    if (empty($source['commission'])) {
                        $source['commission'] = 0;
                    }
                }
                $request->merge(['revenue_sources' => $revenue_sources]);

                $validationRules['revenue_sources'] = 'required|array|min:1';
                $validationRules['revenue_sources.*.source_name'] = 'required|string|max:100|exists:fields,name';
                $validationRules['revenue_sources.*.amount'] = 'required|numeric|min:0';
                $validationRules['revenue_sources.*.commission'] = 'required|numeric|min:0';
                $validationRules['route_id'] = 'required|exists:routes,id';
            }

            $validated = $request->validate($validationRules);

            $updateData = [
                'department_id' => $validated['department_id'],
            ];

            if (!$isMarketing) {
                $totalRevenue = collect($request->revenue_sources)->sum('amount');
                $totalCommission = collect($request->revenue_sources)->sum('commission');
                $noteData = [
                    'note' => '',
                    'revenue_sources' => array_map(function ($source) {
                        return [
                            'source_name' => mb_convert_encoding($source['source_name'], 'UTF-8', 'UTF-8'),
                            'amount' => (float) $source['amount'],
                            'commission' => (float) ($source['commission'] ?? 0),
                        ];
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
