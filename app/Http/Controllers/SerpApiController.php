<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use App\Services\SearchService;

class SerpApiController extends Controller
{

    protected $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function showSearchForm()
    {
        return view('User.search.index', [
            'results' => [],
            'query' => '',
            'paginator' => null
        ]);
    }

    // Function to get icon based on domain name
    protected function getIconForDomain($domain)
    {
        // Một số ánh xạ tên miền phổ biến với các icon và màu sắc tương ứng
        $domainIconMap = [
            'facebook.com' => ['icon' => 'fab fa-facebook', 'color' => 'text-blue-600'],
            'google.com' => ['icon' => 'fab fa-google', 'color' => 'text-red-500'],
            'twitter.com' => ['icon' => 'fab fa-twitter', 'color' => 'text-blue-400'],
            'youtube.com' => ['icon' => 'fab fa-youtube', 'color' => 'text-red-600'],
            'instagram.com' => ['icon' => 'fab fa-instagram', 'color' => 'text-pink-600'],
            'linkedin.com' => ['icon' => 'fab fa-linkedin', 'color' => 'text-blue-700'],
            'github.com' => ['icon' => 'fab fa-github', 'color' => 'text-gray-700'],
            'amazon.com' => ['icon' => 'fab fa-amazon', 'color' => 'text-yellow-500'],
            'wikipedia.org' => ['icon' => 'fab fa-wikipedia-w', 'color' => 'text-black'],
            'tumblr.com' => ['icon' => 'fab fa-tumblr', 'color' => 'text-blue-500'],
            'pinterest.com' => ['icon' => 'fab fa-pinterest', 'color' => 'text-red-600'],
            'reddit.com' => ['icon' => 'fab fa-reddit-alien', 'color' => 'text-orange-600'],
            // Thêm các ánh xạ khác nếu cần
        ];

        // Trả về icon và color tương ứng từ mảng ánh xạ
        if (isset($domainIconMap[$domain])) {
            return $domainIconMap[$domain]; // Trả về mảng với icon và color
        }

        return ['icon' => 'fas fa-globe', 'color' => 'text-gray-500']; // Nếu không có trong danh sách, trả về icon và color mặc định
    }

    public function search(Request $request)
    {
        $query = $request->input('q', '');
        $page = $request->input('page', 1);
        $lang = $request->input('lang', 'vi');
        $perPage = 5;
        $paginator = null;

        $sentimentFilter = $request->input('sentiment', '');  // Lọc theo cảm xúc nếu có

        $cacheKey = 'search_results_' . md5($query);

        // Dùng cache để lưu kết quả tìm kiếm 1 tiếng
        $results = Cache::remember($cacheKey, now()->addHours(1), function () use ($query) {
            return $this->searchService->evaluateContent('https://www.google.com/search?q=' . urlencode($query), ''); // ← gọi Google API
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

                // Lấy danh sách nền tảng từ bảng platforms
                $platforms = DB::table('platforms')->get()->keyBy('domain');

                foreach ($results as &$result) {
                    $url = $result['link'] ?? '';
                    $domain = parse_url($url, PHP_URL_HOST);
                    $domain = $domain ? str_replace('www.', '', strtolower($domain)) : 'unknown';

                    $result['domain'] = $domain;

                    $icon = $this->getIconForDomain($domain);

                    if ($platforms->has($domain)) {
                        $platform = $platforms->get($domain);
                        $result['platform'] = $platform->name;
                        $result['platform_icon'] = $platform->icon;
                        $result['platform_color'] = $platform->color;
                    } else {
                        $newName = ucfirst(str_replace(['.com', '.vn', '.org', '.net'], '', $domain));

                        DB::table('platforms')->updateOrInsert(
                            ['domain' => $domain],
                            [
                                'name' => $newName,
                                'icon' => $icon['icon'],
                                'color' => $icon['color'],
                                'created_at' => now(),
                                'updated_at' => now()
                            ]
                        );

                        $result['platform'] = $newName;
                        $result['platform_icon'] = $icon['icon'];
                        $result['platform_color'] = $icon['color'];
                    }

                    // Thêm hình ảnh thu nhỏ
                    $result['thumbnail'] = $result['pagemap']['cse_thumbnail'][0]['src'] ?? ($result['pagemap']['cse_image'][0]['src'] ?? null);

                    // Lấy sentiment từ bảng search_logs
                    $sentiment = DB::table('search_logs')
                        ->where('query', $query)
                        ->where('platform', $result['platform'] ?? null)
                        ->value('sentiment');

                    // Gán giá trị cảm xúc
                    $result['sentiment'] = $sentiment ?? $this->searchService->evaluateContent($result['link'], $result['snippet'] ?? '');

                    // Dịch tiêu đề nếu cần
                    $result['title'] = $this->searchService->translateText($result['title'], $lang);

                    // Dịch mô tả (snippet) nếu có
                    $result['snippet'] = $this->searchService->translateText($result['snippet'] ?? '', $lang);

                    // Lưu hoặc cập nhật vào bảng search_logs
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


    public function evaluateUrl(Request $request)
    {
        $url = $request->input('url');
        $snippet = $request->input('snippet') ?? '';

        $sentiment = $this->searchService->evaluateContent($url, $snippet);

        return response()->json(['sentiment' => $sentiment]);
    }
}
