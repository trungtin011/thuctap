<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DaiLy;

class DaiLyController extends Controller
{
    public function create()
    {
          $daiLys = DaiLy::latest()->get();
    return view('admin.dai_ly.create', compact('daiLys'));
        return view('admin.dai_ly.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_dai_ly' => 'required|string|max:255',
            'email' => 'required|email|unique:dai_lies,email',
            'so_dien_thoai' => 'required|string|max:20',
            'dia_chi' => 'required|string',
        ]);

        DaiLy::create($request->all());

        return redirect()->back()->with('success', 'Thêm đại lý thành công!');
    }
}
