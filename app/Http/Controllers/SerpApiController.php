<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Gemini\Client;
use Gemini\Transporters\HttpTransporter;
use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Client\ClientInterface;
use Closure;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class SerpApiController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q', 'Tin tức mới nhất');
        $page = $request->input('page', 1);
        $perPage = 10;

        $apiKey = env('GOOGLE_API_KEY', 'AIzaSyANBGy9NsRYiX9THqHiWDgOsqbX1H34zPM');
        $cx = env('GOOGLE_CX', '402b811b68dfb4029');

        if (!$apiKey || !$cx) {
            Log::error('GOOGLE_API_KEY or GOOGLE_CX is not set in .env file.');
            return response()->json(['error' => 'GOOGLE_API_KEY or GOOGLE_CX is not set. Please configure your .env file.'], 500);
        }

        $start = ($page - 1) * $perPage + 1;

        $response = Http::get('https://www.googleapis.com/customsearch/v1', [
            'key' => $apiKey,
            'cx' => $cx,
            'q' => $query,
            'hl' => 'vi',
            'gl' => 'vn',
            'start' => $start,
            'num' => $perPage,
        ]);

        if ($response->failed()) {
            Log::error('Google Custom Search API request failed: ' . $response->status() . ' - ' . $response->body());
            return response()->json(['error' => 'Google Custom Search API request failed.', 'status' => $response->status(), 'body' => $response->body()], $response->status());
        }

        $results = $response->json()['items'] ?? [];
        $totalResults = min((int)($response->json()['searchInformation']['totalResults'] ?? 0), 100);

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
        ];

        foreach ($results as &$result) {
            $url = $result['link'] ?? '';
            $domain = parse_url($url, PHP_URL_HOST);
            $domain = $domain ? str_replace('www.', '', strtolower($domain)) : 'unknown';

            if (isset($platforms[$domain])) {
                $result['platform'] = $platforms[$domain]['name'];
                $result['platform_icon'] = $platforms[$domain]['icon'];
                $result['platform_color'] = $platforms[$domain]['color'];
            } else {
                $result['platform'] = ucfirst(str_replace(['.com', '.vn', '.org', '.net'], '', $domain));
                $result['platform_icon'] = 'fas fa-globe';
                $result['platform_color'] = 'text-gray-500';
            }

            $result['thumbnail'] = $result['pagemap']['cse_thumbnail'][0]['src'] ?? ($result['pagemap']['cse_image'][0]['src'] ?? null);

            $result['sentiment'] = $this->evaluateContent($result['link'], $result['snippet'] ?? '');
        }


        $selectedPlatform = $request->input('platform'); // Lấy giá trị nền tảng từ query string

        foreach ($results as &$result) {
            $url = $result['link'] ?? '';
            $domain = parse_url($url, PHP_URL_HOST);
            $domain = $domain ? str_replace('www.', '', strtolower($domain)) : 'unknown';

            if (isset($platforms[$domain])) {
                $result['platform'] = $platforms[$domain]['name'];
                $result['platform_icon'] = $platforms[$domain]['icon'];
                $result['platform_color'] = $platforms[$domain]['color'];
            } else {
                $result['platform'] = ucfirst(str_replace(['.com', '.vn', '.org', '.net'], '', $domain));
                $result['platform_icon'] = 'fas fa-globe';
                $result['platform_color'] = 'text-gray-500';
            }

            $result['thumbnail'] = $result['pagemap']['cse_thumbnail'][0]['src'] ?? ($result['pagemap']['cse_image'][0]['src'] ?? null);
            $result['sentiment'] = $this->evaluateContent($result['link'], $result['snippet'] ?? '');
        }

        // 🟡 Lọc theo nền tảng nếu có yêu cầu
        if ($selectedPlatform) {
            $results = array_filter($results, function ($result) use ($selectedPlatform) {
                return strtolower($result['platform']) === strtolower($selectedPlatform);
            });
        }


        $results = array_values($results); // Reset chỉ mục sau khi lọc
        $totalFiltered = count($results);

        $paginator = new LengthAwarePaginator(
            $results,
            $totalFiltered,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );


        return view('User.search.index', compact('results', 'query', 'paginator'));
    }

    /**
     * Hàm đánh giá nội dung trang web bằng Gemini API
     */
    private function evaluateContent($url, $snippet)
    {
        try {
            // Lấy nội dung từ URL
            $response = Http::get($url);
            if (!$response->ok()) {
                Log::warning('Không thể truy cập URL: ' . $url);
                return 'Không thể truy cập trang web';
            }

            // Xác định mã hóa từ header hoặc mặc định là UTF-8
            $encoding = 'UTF-8';
            $contentType = $response->header('Content-Type');
            if ($contentType && preg_match('/charset=([\w-]+)/i', $contentType, $matches)) {
                $encoding = strtoupper($matches[1]);
            }

            // Lấy nội dung và chuyển đổi mã hóa
            $content = $response->body();

            // Chuyển mã hóa
            $content = @mb_convert_encoding($content, 'UTF-8', 'auto');

            // Loại bỏ thẻ HTML
            $content = strip_tags($content);

            // Loại ký tự không hợp lệ
            $content = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $content);

            // Làm sạch ký tự không phải chữ hoặc số
            $content = preg_replace('/[^\p{L}\p{N}\s]/u', '', $content);

            // Cắt ngắn nội dung
            $content = trim(mb_substr($content, 0, 1000));


            // Nếu nội dung rỗng, sử dụng snippet
            if (empty($content)) {
                $content = $snippet;
                Log::warning('Nội dung rỗng, sử dụng snippet cho URL: ' . $url);
            }

            // Log nội dung để kiểm tra
            Log::info('Nội dung sau khi làm sạch cho URL: ' . $url, ['content' => $content]);

            // Kiểm tra nội dung rỗng
            if (empty($content)) {
                Log::warning('Nội dung rỗng sau khi làm sạch: ' . $url);
                return 'Không có nội dung để đánh giá';
            }

            // Gọi Gemini API
            $geminiApiKey = env('GEMINI_API_KEY', 'AIzaSyDhb0kguqfFXdsknNviCU7dLI6NeCQS8hs');
            $geminiEndpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

            $prompt = "Phân tích nội dung sau và đánh giá xem nội dung có tích cực (Tốt), tiêu cực (Xấu), hay trung tính (Trung tính). \n\n" .
                "- Nội dung được coi là 'Tốt' nếu: \n" .
                "  - Cung cấp thông tin hữu ích, đáng tin cậy về sản phẩm, dịch vụ, hoặc công ty.\n" .
                "  - Chứa từ ngữ tích cực như 'chất lượng cao', 'uy tín', 'đáng tin cậy', hoặc đánh giá tốt.\n" .
                "  - Quảng bá sản phẩm/dịch vụ một cách chân thực, không có dấu hiệu lừa đảo.\n\n" .
                "- Nội dung được coi là 'Xấu' nếu: \n" .
                "  - Chứa thông tin sai lệch, lừa đảo, hoặc gây hiểu lầm.\n" .
                "  - Có từ ngữ tiêu cực như 'kém chất lượng', 'lừa đảo', 'phàn nàn', hoặc đánh giá xấu.\n" .
                "  - Nội dung kích động, xúc phạm, hoặc không phù hợp.\n\n" .
                "- Nội dung được coi là 'Trung tính' nếu: \n" .
                "  - Chỉ cung cấp thông tin thực tế, không có cảm xúc tích cực hoặc tiêu cực rõ ràng (ví dụ: thông tin lịch sử, số liệu, hoặc mô tả kỹ thuật).\n" .
                "  - Không đủ thông tin để đánh giá là Tốt hoặc Xấu.\n\n" .
                "Trả về một câu ngắn gọn chỉ với kết quả: 'Tốt', 'Xấu', hoặc 'Trung tính'. Nội dung: \n\n" . $content;

            // Gửi yêu cầu đến Gemini API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($geminiEndpoint . '?key=' . $geminiApiKey, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);

            if ($response->ok()) {
                $result = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? 'Không xác định';
                Log::info('Kết quả từ Gemini cho URL: ' . $url, ['result' => $result]);
                return $result;
            } else {
                Log::error('Lỗi khi gọi Gemini API cho URL: ' . $url, ['status' => $response->status()]);
                return 'Lỗi khi gọi Gemini API: ' . $response->status();
            }
        } catch (\Exception $e) {
            Log::error('Lỗi xử lý URL: ' . $url, ['error' => $e->getMessage()]);
            return 'Lỗi: ' . $e->getMessage();
        }
    }
}
