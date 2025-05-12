<?php

// app/Http/Controllers/AutocompleteController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AutocompleteController extends Controller
{
    public function suggest(Request $request)
    {
        $keyword = $request->get('query');

        // Lấy danh sách từ khóa đã từng tìm gần giống
        $suggestions = DB::table('search_logs')
            ->where('query', 'LIKE', '%' . $keyword . '%')
            ->distinct()
            ->pluck('query')
            ->take(10);

        return response()->json($suggestions);
    }
}
