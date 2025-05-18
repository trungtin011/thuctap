<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlatformController extends Controller
{
    public function index(Request $request)
    {
        $query = Platform::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $platforms = $query->orderBy('created_at', 'desc')->paginate(10);

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
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Platform::create([
            'name' => $request->name,
        ]);

        return redirect()->route('platforms.index')->with('success', 'Platform created successfully');
    }

    public function edit(Platform $platform)
    {
        return view('admin.platforms.edit', compact('platform'));
    }

    public function update(Request $request, Platform $platform)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:platforms,name,' . $platform->id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $platform->update([
            'name' => $request->name,
        ]);

        return redirect()->route('platforms.index')->with('success', 'Platform updated successfully');
    }

    public function destroy($id)
    {
        $platform = Platform::findOrFail($id);
        $platform->delete();
        return redirect()->route('platforms.index')->with('success', 'Platform deleted successfully.');
    }
}
