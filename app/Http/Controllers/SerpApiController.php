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
            'api_key' => env('SERPAPI_KEY', '76f1ecb39da98160ef051ad16d9e5c7578237f9373c5d6609a43872070a5ea78'),
            'hl' => 'vi',
            'gl' => 'vn'
        ]);

        // Lấy kết quả từ SerpAPI
        $results = $response->json()['organic_results'] ?? [];

        // Danh sách ánh xạ nền tảng, icon và màu
        $platforms = [
            'youtube.com' => ['name' => 'YouTube', 'icon' => 'fab fa-youtube', 'color' => 'text-red-600'],
            'facebook.com' => ['name' => 'Facebook', 'icon' => 'fab fa-facebook', 'color' => 'text-blue-600'],
            'wikipedia.org' => ['name' => 'Wikipedia', 'icon' => 'fab fa-wikipedia-w', 'color' => 'text-gray-600'],
            'twitter.com' => ['name' => 'X', 'icon' => 'fab fa-x-twitter', 'color' => 'text-black'],
            'x.com' => ['name' => 'X', 'icon' => 'fab fa-x-twitter', 'color' => 'text-black'],
            'instagram.com' => ['name' => 'Instagram', 'icon' => 'fab fa-instagram', 'color' => 'text-pink-600'],
            'linkedin.com' => ['name' => 'LinkedIn', 'icon' => 'fab fa-linkedin', 'color' => 'text-blue-700'],
            'tiktok.com' => ['name' => 'TikTok', 'icon' => 'fab fa-tiktok', 'color' => 'text-teal-500'],
            'pinterest.com' => ['name' => 'Pinterest', 'icon' => 'fab fa-pinterest', 'color' => 'text-red-700'],
            'reddit.com' => ['name' => 'Reddit', 'icon' => 'fab fa-reddit', 'color' => 'text-orange-600'],
            'vnexpress.net' => ['name' => 'VNExpress', 'icon' => 'fas fa-newspaper', 'color' => 'text-blue-500'],
            'tuoitre.vn' => ['name' => 'Tuổi Trẻ', 'icon' => 'fas fa-newspaper', 'color' => 'text-red-500'],
            'thanhnien.vn' => ['name' => 'Thanh Niên', 'icon' => 'fas fa-newspaper', 'color' => 'text-blue-600'],
            'zingnews.vn' => ['name' => 'Zing News', 'icon' => 'fas fa-newspaper', 'color' => 'text-purple-600'],
            'shopee.vn' => ['name' => 'Shopee', 'icon' => 'fas fa-shopping-cart', 'color' => 'text-orange-500'],
            'lazada.vn' => ['name' => 'Lazada', 'icon' => 'fas fa-shopping-cart', 'color' => 'text-blue-800'],
            'tiki.vn' => ['name' => 'Tiki', 'icon' => 'fas fa-shopping-cart', 'color' => 'text-blue-500'],
            // Thêm các nền tảng khác nếu cần
        ];

        // Xử lý kết quả và gán nền tảng/icon/màu
        foreach ($results as &$result) {
            $url = $result['link'] ?? '';
            // Trích xuất domain từ URL
            $domain = parse_url($url, PHP_URL_HOST);
            $domain = $domain ? str_replace('www.', '', strtolower($domain)) : 'unknown';

            // Tìm nền tảng trong danh sách
            if (isset($platforms[$domain])) {
                $result['platform'] = $platforms[$domain]['name'];
                $result['platform_icon'] = $platforms[$domain]['icon'];
                $result['platform_color'] = $platforms[$domain]['color'];
            } else {
                // Fallback: dùng domain làm tên nền tảng
                $result['platform'] = ucfirst(str_replace(['.com', '.vn', '.org', '.net'], '', $domain));
                $result['platform_icon'] = 'fas fa-globe';
                $result['platform_color'] = 'text-gray-500'; // Màu mặc định
            }
        }

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