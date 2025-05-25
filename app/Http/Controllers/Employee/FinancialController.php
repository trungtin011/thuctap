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
            ->with(['department', 'platform', 'expenses', 'submittedBy', 'daiLy'])
            ->orderBy('record_date', 'desc');

        if ($request->filled('platform_id')) {
            $query->where('platform_id', $request->platform_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->where('record_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('record_date', '<=', $request->end_date);
        }
       

        $financialRecords = $query->get();
        $platforms = Platform::all();
        $expenseTypes = ExpenseType::all();
         $dailies = Daily::all();


        return view('employee.financial.index', compact('financialRecords', 'platforms', 'expenseTypes', 'dailies'));
    }

    public function create()
    {
        $employee = Auth::user();
        $department = Department::find($employee->department_id);
        if (!$department) {
            abort(403, 'Bạn không thuộc phòng ban nào.');
        }
        $platforms = Platform::all();
        $expenseTypes = ExpenseType::all();
         $dailies = Daily::all();
        return view('employee.financial.create', compact('department', 'platforms', 'expenseTypes', 'dailies'));
    }

    public function store(Request $request)
    {
        try {
            Log::info('Store request data:', $request->all());

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
                'dai_ly_id' => 'required|exists:dai_lies,id',
                'metric_values' => 'nullable|array',
                'metric_values.*.metric_id' => [
                    'required',
                    'exists:platform_metrics,id',
                    function ($attribute, $value, $fail) use ($request) {
                        if (!PlatformMetric::where('id', $value)->where('platform_id', $request->platform_id)->exists()) {
                            $fail('Chỉ số không thuộc nền tảng đã chọn.');
                        }
                    },
                ],
                'metric_values.*.value' => [
                    'required',
                    function ($attribute, $value, $fail) use ($request) {
                        $index = explode('.', $attribute)[1];
                        if (!isset($request->metric_values[$index]['metric_id'])) {
                            $fail('ID chỉ số không hợp lệ.');
                            return;
                        }
                        $metricId = $request->metric_values[$index]['metric_id'];
                        $metric = PlatformMetric::find($metricId);
                        if (!$metric) {
                            $fail('Chỉ số không tồn tại.');
                            return;
                        }
                        if ($metric->data_type === 'int' && (!is_numeric($value) || floor($value) != $value)) {
                            $fail('Giá trị phải là số nguyên.');
                        }
                        if ($metric->data_type === 'float' && !is_numeric($value)) {
                            $fail('Giá trị phải là số thực.');
                        }
                        if ($metric->data_type === 'string' && !is_string($value)) {
                            $fail('Giá trị phải là chuỗi.');
                        }
                    },
                ],
                'metric_values.*.recorded_at' => 'required|date',
                'revenue_sources' => 'required|array|min:1',
                'revenue_sources.*.source_name' => 'required|string|max:100',
                'revenue_sources.*.amount' => 'required|numeric|min:0',
                'record_date' => 'required|date',
                'record_time' => 'required',
                'note' => 'nullable|string',
                'expenses' => 'nullable|array',
                'expenses.*.expense_type_id' => 'required|exists:expense_types,id',
                'expenses.*.amount' => 'required|numeric|min:0',
                'expenses.*.description' => 'nullable|string|max:255',
            ]);

            $totalRevenue = collect($request->revenue_sources)->sum('amount');

            $noteData = [
                'note' => $request->note ?? '',
                'revenue_sources' => $request->revenue_sources ?? [],
            ];
            $note = json_encode($noteData);

            $financialRecord = FinancialRecord::create([
                'department_id' => $validated['department_id'],
                'platform_id' => $validated['platform_id'],
                'dai_ly_id' => $validated['dai_ly_id'],
                'revenue' => $totalRevenue,
                'record_date' => $validated['record_date'],
                'record_time' => $validated['record_time'],
                'note' => $note,
                'status' => 'pending',
                'submitted_by' => Auth::id(),
            ]);

            if ($request->has('expenses') && is_array($request->expenses)) {
                foreach ($request->expenses as $expense) {
                    Expense::create([
                        'financial_record_id' => $financialRecord->id,
                        'expense_type_id' => $expense['expense_type_id'],
                        'amount' => $expense['amount'],
                        'description' => $expense['description'] ?? '',
                    ]);
                }
            }

            if ($request->has('metric_values') && is_array($request->metric_values)) {
                foreach ($request->metric_values as $metricValue) {
                    if (isset($metricValue['metric_id'], $metricValue['value'], $metricValue['recorded_at'])) {
                        MetricValue::create([
                            'metric_id' => $metricValue['metric_id'],
                            'value' => $metricValue['value'],
                            'recorded_at' => $metricValue['recorded_at'],
                        ]);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Bản ghi doanh thu và chi phí đã được thêm thành công.'
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
                'message' => 'Đã xảy ra lỗi khi lưu bản ghi. Vui lòng thử lại.'
            ], 500);
        }
    }

    public function edit($id)
    {
        $employee = Auth::user();
        $financialRecord = FinancialRecord::where('id', $id)
            ->where('submitted_by', $employee->id)
            ->with(['expenses', 'department', 'platform'])
            ->firstOrFail();

        if ($financialRecord->status !== 'pending') {
            abort(403, 'Bạn chỉ có thể sửa bản ghi đang chờ duyệt.');
        }

        $department = Department::find($employee->department_id);
        $platforms = Platform::all();
        $expenseTypes = ExpenseType::all();
         $dailies = Daily::all();

        return view('employee.financial.edit', compact('financialRecord', 'department', 'platforms', 'expenseTypes', 'dailies'));
    }

  public function update(Request $request, $id)
{
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

    $request->validate([
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
        'dai_ly_id' => 'nullable|exists:dai_lies,id', // ✅ validate thêm dòng này
        'metric_values' => 'nullable|array',
        'metric_values.*.metric_id' => [
            'required',
            'exists:platform_metrics,id',
            function ($attribute, $value, $fail) use ($request) {
                if (!PlatformMetric::where('id', $value)->where('platform_id', $request->platform_id)->exists()) {
                    $fail('Chỉ số không thuộc nền tảng đã chọn.');
                }
            },
        ],
        'metric_values.*.value' => [
            'required',
            function ($attribute, $value, $fail) use ($request) {
                $index = explode('.', $attribute)[1];
                $metricId = $request->metric_values[$index]['metric_id'];
                $metric = PlatformMetric::find($metricId);
                if ($metric->data_type === 'int' && (!is_numeric($value) || floor($value) != $value)) {
                    $fail('Giá trị phải là số nguyên.');
                }
                if ($metric->data_type === 'float' && !is_numeric($value)) {
                    $fail('Giá trị phải là số thực.');
                }
                if ($metric->data_type === 'string' && !is_string($value)) {
                    $fail('Giá trị phải là chuỗi.');
                }
            },
        ],
        'metric_values.*.recorded_at' => 'required|date',
        'revenue_sources' => 'required|array|min:1',
        'revenue_sources.*.source_name' => 'required|string|max:100',
        'revenue_sources.*.amount' => 'required|numeric|min:0',
        'record_date' => 'required|date',
        'record_time' => 'required',
        'note' => 'nullable|string',
        'expenses' => 'nullable|array',
        'expenses.*.expense_type_id' => 'required|exists:expense_types,id',
        'expenses.*.amount' => 'required|numeric|min:0',
        'expenses.*.description' => 'nullable|string|max:255',
    ]);

    $totalRevenue = collect($request->revenue_sources)->sum('amount');

    $noteData = [
        'note' => $request->note ?? '',
        'revenue_sources' => $request->revenue_sources,
    ];
    $note = json_encode($noteData);

    $financialRecord->update([
        'department_id' => $request->department_id,
        'platform_id' => $request->platform_id,
        'dai_ly_id' => $request->dai_ly_id, // ✅ cập nhật đại lý tại đây
        'revenue' => $totalRevenue,
        'record_date' => $request->record_date,
        'record_time' => $request->record_time,
        'note' => $note,
    ]);

    // Xóa và thêm lại chi phí
    $financialRecord->expenses()->delete();

    if ($request->has('expenses')) {
        foreach ($request->expenses as $expense) {
            Expense::create([
                'financial_record_id' => $financialRecord->id,
                'expense_type_id' => $expense['expense_type_id'],
                'amount' => $expense['amount'],
                'description' => $expense['description'],
            ]);
        }
    }

    // Cập nhật lại metric_values
    $oldRecordedAt = $financialRecord->record_date . ' ' . $financialRecord->record_time;
    MetricValue::whereIn('metric_id', PlatformMetric::where('platform_id', $financialRecord->platform_id)->pluck('id'))
        ->where('recorded_at', $oldRecordedAt)
        ->delete();

    if ($request->has('metric_values')) {
        foreach ($request->metric_values as $metricValue) {
            MetricValue::create([
                'metric_id' => $metricValue['metric_id'],
                'value' => $metricValue['value'],
                'recorded_at' => $metricValue['recorded_at'],
            ]);
        }
    }

    if ($request->ajax()) {
        return response()->json([
            'success' => true,
            'message' => 'Bản ghi doanh thu và chi phí đã được cập nhật thành công.'
        ]);
    }

    return redirect()->route('employee.financial.index')
        ->with('success', 'Bản ghi doanh thu và chi phí đã được cập nhật thành công.');
}


    public function destroy(Request $request, $id)
    {
        $employee = Auth::user();
        $financialRecord = FinancialRecord::where('id', $id)
            ->where('submitted_by', $employee->id)
            ->firstOrFail();

        if ($financialRecord->status !== 'pending') {
            abort(403, 'Bạn chỉ có thể xóa bản ghi đang chờ duyệt.');
        }

        $recordedAt = $financialRecord->record_date . ' ' . $financialRecord->record_time;
        MetricValue::whereIn('metric_id', PlatformMetric::where('platform_id', $financialRecord->platform_id)->pluck('id'))
            ->where('recorded_at', $recordedAt)
            ->delete();

        $financialRecord->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Bản ghi đã được xóa thành công.'
            ]);
        }

        return redirect()->route('employee.financial.index')
            ->with('success', 'Bản ghi đã được xóa thành công.');
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
            $query->where('recorded_at', $record->record_date . ' ' . $record->record_time);
        }

        $values = $query->get();
        return response()->json(['values' => $values]);
    }

    public function getMetricValuesForRecord($recordId)
    {
        $metricValues = MetricValue::where('financial_record_id', $recordId)
            ->join('platform_metrics', 'metric_values.metric_id', '=', 'platform_metrics.id')
            ->select('metric_values.*', 'platform_metrics.name as metric_name', 'platform_metrics.unit as metric_unit')
            ->get();

        return response()->json(['metricValues' => $metricValues]);
    }
}
