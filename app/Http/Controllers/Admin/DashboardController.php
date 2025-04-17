<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Admin;

class DashboardController extends Controller
{
    public function index()
    {
        $admin = Admin::where('username', auth('admin')->user()->username)->first();
        return view('admin.index' , compact('admin'));
    }
}