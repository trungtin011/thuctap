<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DaiLy;

class DaiLyController extends Controller
{
    // Display a listing of the resource
    public function index()
    {
        $daiLys = DaiLy::latest()->paginate(10);
        return view('admin.dai_ly.index', compact('daiLys'));
    }

    // Show the form for creating a new resource
    public function create()
    {
        return view('admin.dai_ly.create');
    }

    // Store a newly created resource in storage
    public function store(Request $request)
    {
        $request->validate([
            'ten_dai_ly' => 'required|string|max:255',
            'email' => 'required|email|unique:dai_lies,email',
            'so_dien_thoai' => 'required|string|max:20',
            'dia_chi' => 'required|string',
        ]);

        DaiLy::create($request->all());

        return redirect()->route('admin.dai_ly.index')->with('success', 'Thêm đại lý thành công!');
    }

    // Show the form for editing the specified resource
    public function edit(DaiLy $daiLy)
    {
        return view('admin.dai_ly.edit', compact('daiLy'));
    }

    // Update the specified resource in storage
    public function update(Request $request, DaiLy $daiLy)
    {
        $request->validate([
            'ten_dai_ly' => 'required|string|max:255',
            'email' => 'required|email|unique:dai_lies,email,'.$daiLy->id,
            'so_dien_thoai' => 'required|string|max:20',
            'dia_chi' => 'required|string',
        ]);

        $daiLy->update($request->all());

        return redirect()->route('admin.dai_ly.index')->with('success', 'Cập nhật đại lý thành công!');
    }

    // Remove the specified resource from storage
    public function destroy(DaiLy $daiLy)
    {
        $daiLy->delete();
        return redirect()->route('admin.dai_ly.index')->with('success', 'Xóa đại lý thành công!');
    }
}