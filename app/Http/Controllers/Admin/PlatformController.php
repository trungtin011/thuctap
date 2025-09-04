<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Platform;
use App\Models\PlatformMetric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlatformController extends Controller
{
    public function index(Request $request)
    {
        $query = Platform::with('metrics')->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $platforms = $query->paginate(10);

        return view('admin.platforms.index', compact('platforms'));
    }

    public function create()
    {
        return view('admin.platforms.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:platforms',
            'metrics' => 'nullable|array',
            'metrics.*.name' => 'required|string|max:100',
            'metrics.*.unit' => 'nullable|string|max:50',
            'metrics.*.data_type' => 'required|in:int,float,string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $platform = Platform::create([
            'name' => $request->name,
        ]);

        if ($request->has('metrics')) {
            foreach ($request->metrics as $metric) {
                PlatformMetric::create([
                    'platform_id' => $platform->id,
                    'name' => $metric['name'],
                    'unit' => $metric['unit'],
                    'data_type' => $metric['data_type'],
                ]);
            }
        }

        return redirect()->route('platforms.index')->with('success', 'Nền tảng đã được tạo thành công.');
    }

    public function edit(Platform $platform)
    {
        $platform->load('metrics');
        return view('admin.platforms.edit', compact('platform'));
    }

    public function update(Request $request, Platform $platform)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:platforms,name,' . $platform->id,
            'metrics' => 'nullable|array',
            'metrics.*.name' => 'required|string|max:100',
            'metrics.*.unit' => 'nullable|string|max:50',
            'metrics.*.data_type' => 'required|in:int,float,string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $platform->update([
            'name' => $request->name,
        ]);

        // Xóa các chỉ số cũ
        $platform->metrics()->delete();

        // Thêm các chỉ số mới
        if ($request->has('metrics')) {
            foreach ($request->metrics as $metric) {
                PlatformMetric::create([
                    'platform_id' => $platform->id,
                    'name' => $metric['name'],
                    'unit' => $metric['unit'],
                    'data_type' => $metric['data_type'],
                ]);
            }
        }

        return redirect()->route('platforms.index')->with('success', 'Nền tảng đã được cập nhật thành công.');
    }

    public function destroy($id)
    {
        $platform = Platform::findOrFail($id);
        $platform->delete();
        return redirect()->route('platforms.index')->with('success', 'Nền tảng đã được xóa thành công.');
    }

    public function destroyMetric($platformId, $metricId)
    {
        $metric = PlatformMetric::where('platform_id', $platformId)->findOrFail($metricId);
        $metric->delete();
        return redirect()->back()->with('success', 'Chỉ số đã được xóa thành công.');
    }
}
