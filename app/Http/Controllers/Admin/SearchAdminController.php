<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SearchAdminController extends Controller
{
    // Hiển thị lịch sử tìm kiếm
    public function index()
    {
        // Sửa tên biến từ $histories thành $searchHistories để đồng bộ
        $searchHistories = DB::table('search_logs')->orderByDesc('created_at')->paginate(20);
        return view('admin.search.history', compact('searchHistories')); // Trả về view với biến đồng bộ
    }

    // Thống kê các tìm kiếm phổ biến
    public function statistics()
    {
        // Lấy các truy vấn tìm kiếm phổ biến
        $topQueries = DB::table('search_logs')
            ->select('query', DB::raw('COUNT(*) as total'))
            ->groupBy('query')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        // Thống kê cảm xúc của các tìm kiếm
        $sentimentStats = DB::table('search_logs')
            ->select('sentiment', DB::raw('COUNT(*) as total'))
            ->groupBy('sentiment')
            ->get();

        // Trả về view thống kê với các biến đồng bộ
        return view('admin.search.statistics', compact('topQueries', 'sentimentStats'));
    }

    // Mở form đánh giá
    public function evaluateForm()
    {
        return view('admin.search.evaluate');
    }

    // Xử lý đánh giá thủ công từ form
    public function evaluateManual(Request $request)
    {
        $url = $request->input('url');
        $snippet = $request->input('snippet', '');

        // Gọi phương thức từ SerpApiController để đánh giá cảm xúc nội dung
        $sentiment = app(\App\Http\Controllers\SerpApiController::class)->evaluateContent($url, $snippet);

        // Trả về view đánh giá với các biến đồng bộ
        return view('admin.search.evaluate', compact('url', 'sentiment'));
    }
}
