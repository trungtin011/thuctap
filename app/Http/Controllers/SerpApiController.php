<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SerpApiController extends Controller
{
    public function showSearchForm()
    {
        return view('User.search.index', [
            'results' => [],
            'query' => '',
            'paginator' => null
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->input('q', '');
        $page = $request->input('page', 1);
        $perPage = 5;
        $paginator = null;

        $sentimentFilter = $request->input('sentiment', '');  // Lọc theo cảm xúc nếu có

        $cacheKey = 'search_results_' . md5($query);

        // Dùng cache để lưu kết quả tìm kiếm 1 tiếng
        $results = Cache::remember($cacheKey, now()->addHours(1), function () use ($query) {
            return $this->evaluateContent('https://www.google.com/search?q=' . urlencode($query), ''); // ← gọi Google API
        });

        $apiKey = env('GOOGLE_API_KEY');
        $cx = env('GOOGLE_CX');

        if (!$apiKey || !$cx) {
            return redirect()->back()->with('error', 'Chưa cấu hình GOOGLE_API_KEY hoặc GOOGLE_CX trong .env');
        }

        if (!empty($query)) {
            $start = ($page - 1) * $perPage + 1;
            $cacheKey = 'search_' . md5($query . '_' . $page);

            // Lấy kết quả tìm kiếm từ Google API và cache
            $responseData = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($apiKey, $cx, $query, $start, $perPage) {
                $response = Http::get('https://www.googleapis.com/customsearch/v1', [
                    'key' => $apiKey,
                    'cx' => $cx,
                    'q' => $query,
                    'hl' => 'vi',
                    'gl' => 'vn',
                    'start' => $start,
                    'num' => $perPage,
                    'fields' => 'items(title,link,snippet,pagemap),searchInformation(totalResults)',
                ]);

                if ($response->failed()) {
                    Log::error('Lỗi gọi Google Custom Search API: ' . $response->status() . ' - ' . $response->body());
                    return null;
                }

                return $response->json();
            });

            // Xử lý kết quả tìm kiếm và thêm thông tin nền tảng
            if (!is_null($responseData)) {
                $results = $responseData['items'] ?? [];
                $totalResults = min((int)($responseData['searchInformation']['totalResults'] ?? 0), 100);

                foreach ($results as &$result) {
                    $url = $result['link'] ?? '';
                    $domain = parse_url($url, PHP_URL_HOST);
                    $domain = $domain ? str_replace('www.', '', strtolower($domain)) : 'unknown';

                    // Gán thông tin nền tảng nếu có
                    if (isset($platforms[$domain])) {
                        $result['platform'] = $platforms[$domain]['name'];
                        $result['platform_icon'] = $platforms[$domain]['icon'];
                        $result['platform_color'] = $platforms[$domain]['color'];
                    } else {
                        $result['platform'] = ucfirst(str_replace(['.com', '.vn', '.org', '.net'], '', $domain));
                        $result['platform_icon'] = 'fas fa-globe';
                        $result['platform_color'] = 'text-gray-500';
                    }

                    // Thêm hình ảnh thu nhỏ
                    $result['thumbnail'] = $result['pagemap']['cse_thumbnail'][0]['src'] ?? ($result['pagemap']['cse_image'][0]['src'] ?? null);

                    // Lấy sentiment từ bảng search_logs
                    $sentiment = DB::table('search_logs')
                        ->where('query', $query)
                        ->where('platform', $result['platform'] ?? null)
                        ->value('sentiment');

                    // Gán giá trị cảm xúc
                    $result['sentiment'] = $sentiment ?? $this->evaluateContent($result['link'], $result['snippet'] ?? '');

                    // Cập nhật sentiment vào bảng search_logs
                    DB::table('search_logs')->updateOrInsert(
                        [
                            'query' => $query,
                            'platform' => $result['platform'] ?? null,
                            'user_agent' => $request->header('User-Agent')
                        ],
                        [
                            'sentiment' => $result['sentiment'] ?? null,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]
                    );
                }

                // Nếu có lọc theo cảm xúc, áp dụng vào kết quả
                if ($sentimentFilter) {
                    $results = array_filter($results, function ($result) use ($sentimentFilter) {
                        return stripos($result['sentiment'], $sentimentFilter) !== false;
                    });
                }

                // Tạo phân trang
                $paginator = new LengthAwarePaginator(
                    $results,
                    $totalResults,
                    $perPage,
                    $page,
                    ['path' => $request->url(), 'query' => $request->query()]
                );
            }
        }

        return view('User.search.index', compact('results', 'query', 'paginator'));
    }



    private function evaluateContent($url, $snippet)
    {
        try {
            $response = Http::get($url);
            if (!$response->ok()) {
                Log::warning('Không thể truy cập URL: ' . $url);
                return 'Không thể truy cập trang web';
            }

            $encoding = 'UTF-8';
            $contentType = $response->header('Content-Type');
            if ($contentType && preg_match('/charset=([\w-]+)/i', $contentType, $matches)) {
                $encoding = strtoupper($matches[1]);
            }

            $content = $response->body();
            $content = @mb_convert_encoding($content, 'UTF-8', 'auto');
            $content = strip_tags($content);
            $content = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $content);
            $content = preg_replace('/[^\p{L}\p{N}\s]/u', '', $content);
            $content = trim(mb_substr($content, 0, 1000));

            if (empty($content)) {
                $content = $snippet;
                Log::warning('Nội dung rỗng, sử dụng snippet cho URL: ' . $url);
            }

            Log::info('Nội dung sau khi làm sạch cho URL: ' . $url, ['content' => $content]);

            if (empty($content)) {
                return 'Không có nội dung để đánh giá';
            }

            $geminiApiKey = env('GEMINI_API_KEY');
            $geminiEndpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

            $prompt = "Phân tích nội dung sau và đánh giá xem nội dung có tích cực (Tốt), tiêu cực (Xấu), hay trung bình (Trung bình):\n\n" . $content .
                "\n\nTrả về một câu duy nhất với kết quả: 'Tốt', 'Xấu', hoặc 'Trung bình'.";

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($geminiEndpoint . '?key=' . $geminiApiKey, [
                'contents' => [['parts' => [['text' => $prompt]]]]
            ]);

            if ($response->ok()) {
                return $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? 'Không xác định';
            } else {
                Log::error('Lỗi khi gọi Gemini API cho URL: ' . $url, ['status' => $response->status()]);
                return 'Lỗi khi gọi Gemini API';
            }
        } catch (\Exception $e) {
            Log::error('Lỗi xử lý nội dung URL: ' . $url, ['error' => $e->getMessage()]);
            return 'Lỗi xử lý nội dung';
        }
    }

    public function evaluateUrl(Request $request)
    {
        $url = $request->input('url');
        $snippet = $request->input('snippet') ?? '';

        $sentiment = $this->evaluateContent($url, $snippet);

        return response()->json(['sentiment' => $sentiment]);
    }
}
