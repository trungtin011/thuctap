<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SerpApiController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q', 'công ty cà phê Buôn Ma Thuột');

        // Gửi yêu cầu đến SerpAPI
        $response = Http::get('https://serpapi.com/search', [
            'engine' => 'google',
            'q' => $query,
            'api_key' => '76f1ecb39da98160ef051ad16d9e5c7578237f9373c5d6609a43872070a5ea78',
            'hl' => 'vi',
            'gl' => 'vn'
        ]);

        // Lấy kết quả từ SerpAPI
        $results = $response->json()['organic_results'] ?? [];

        // Sắp xếp theo thời gian ngày đăng nếu có
        usort($results, function($a, $b) {
            $dateA = isset($a['date']) ? strtotime($a['date']) : 0;
            $dateB = isset($b['date']) ? strtotime($b['date']) : 0;
            return $dateB - $dateA; // Sắp xếp theo thứ tự giảm dần (mới nhất trước)
        });

        // Trả kết quả về view
        return view('User.search.index', compact('results', 'query'));
    }
}