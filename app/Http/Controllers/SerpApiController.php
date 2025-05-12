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
            'paginator' => null,
            'platform' => '',
        ]);
    }

    protected function getIconForDomain($domain)
    {
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
        ];

        return $domainIconMap[$domain] ?? ['icon' => 'fas fa-globe', 'color' => 'text-gray-500'];
    }

    public function search(Request $request)
    {
        $query = $request->input('q', '');
        $page = $request->input('page', 1);
        $lang = $request->input('lang', 'vi');
        $platform = $request->input('platform', '');
        $perPage = 5;
        $sentimentFilter = $request->input('sentiment', '');
        $timeFilter = $request->input('time', '');
        $realTime = $request->input('real_time', false);
        $paginator = null;

        $cacheKey = 'search_results_' . md5($query . $platform . $timeFilter);

        $apiKey = env('GOOGLE_API_KEY');
        $cx = env('GOOGLE_CX');

        if (!$apiKey || !$cx) {
            return redirect()->back()->with('error', 'Chưa cấu hình GOOGLE_API_KEY hoặc GOOGLE_CX trong .env');
        }

        $results = [];
        $totalResults = 0;

        if (!empty($query)) {
            if ($platform) {
                // Tìm kiếm trên mạng xã hội cụ thể
                $results = $this->searchOnSocialMedia($query, $platform, $page, $perPage, $realTime);
                $totalResults = count($results);
            } else {
                // Tìm kiếm mặc định qua Google API
                $start = ($page - 1) * $perPage + 1;

                if ($realTime) {
                    // Tìm kiếm thời gian thực, không dùng cache
                    $responseData = $this->fetchGoogleSearch($apiKey, $cx, $query, $start, $perPage, $timeFilter);
                } else {
                    // Tìm kiếm có cache
                    $responseData = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($apiKey, $cx, $query, $start, $perPage, $timeFilter) {
                        return $this->fetchGoogleSearch($apiKey, $cx, $query, $start, $perPage, $timeFilter);
                    });
                }

                if (!is_null($responseData)) {
                    $results = $responseData['items'] ?? [];
                    $totalResults = min((int)($responseData['searchInformation']['totalResults'] ?? 0), 100);
                }
            }

            // Xử lý kết quả tìm kiếm
            if (!empty($results)) {
                $platforms = DB::table('platforms')->get()->keyBy('domain');

                foreach ($results as &$result) {
                    $url = $result['link'] ?? '';
                    $domain = parse_url($url, PHP_URL_HOST);
                    $domain = $domain ? str_replace('www.', '', strtolower($domain)) : 'unknown';

                    $result['domain'] = $domain;
                    $icon = $this->getIconForDomain($domain);

                    if ($platforms->has($domain)) {
                        $platformData = $platforms->get($domain);
                        $result['platform'] = $platformData->name;
                        $result['platform_icon'] = $platformData->icon;
                        $result['platform_color'] = $platformData->color;
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

                    // Lấy hoặc đánh giá cảm xúc
                    $sentiment = DB::table('search_logs')
                        ->where('query', $query)
                        ->where('platform', $result['platform'] ?? null)
                        ->value('sentiment');

                    $result['sentiment'] = $sentiment ?? $this->searchService->evaluateContent($result['link'], $result['snippet'] ?? '');

                    // Dịch tiêu đề và mô tả
                    $result['title'] = $this->searchService->translateText($result['title'] ?? '', $lang);
                    $result['snippet'] = $this->searchService->translateText($result['snippet'] ?? '', $lang);

                    // Lưu vào search_logs
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

                    // Chuẩn hóa link bài viết
                    $result['link'] = $this->normalizeSocialMediaLink($result['link'], $domain);
                }

                // Lọc theo cảm xúc
                if ($sentimentFilter) {
                    $results = array_filter($results, function ($result) use ($sentimentFilter) {
                        return stripos($result['sentiment'], $sentimentFilter) !== false;
                    });
                }

                // Lọc theo thời gian (đã áp dụng qua dateRestrict trong Google API)
                $paginator = new LengthAwarePaginator(
                    $results,
                    $totalResults,
                    $perPage,
                    $page,
                    ['path' => $request->url(), 'query' => $request->query()]
                );
            }
        }

        return view('User.search.index', compact('results', 'query', 'paginator', 'platform'));
    }

    protected function fetchGoogleSearch($apiKey, $cx, $query, $start, $perPage, $timeFilter)
    {
        $params = [
            'key' => $apiKey,
            'cx' => $cx,
            'q' => $query,
            'hl' => 'vi',
            'gl' => 'vn',
            'start' => $start,
            'num' => $perPage,
            'fields' => 'items(title,link,snippet,pagemap),searchInformation(totalResults)',
        ];

        if ($timeFilter) {
            $params['dateRestrict'] = $this->mapTimeFilter($timeFilter);
        }

        $response = Http::get('https://www.googleapis.com/customsearch/v1', $params);

        if ($response->failed()) {
            Log::error('Lỗi gọi Google Custom Search API: ' . $response->status() . ' - ' . $response->body());
            return null;
        }

        return $response->json();
    }

    protected function mapTimeFilter($timeFilter)
    {
        $mapping = [
            '24h' => 'd1',
            '7d' => 'w1',
            '1m' => 'm1',
        ];

        return $mapping[$timeFilter] ?? '';
    }

    protected function searchOnSocialMedia($query, $platform, $page, $perPage, $realTime)
    {
        $apiKey = env('SOCIAL_MEDIA_API_KEY');
        $accessToken = Cache::get('social_media_token_' . $platform);

        if (!$accessToken) {
            $accessToken = $this->authenticateSocialMedia($platform);
            Cache::put('social_media_token_' . $platform, $accessToken, now()->addHours(1));
        }

        $endpoint = $this->getSocialMediaEndpoint($platform);
        $params = [
            'q' => $query,
            'page' => $page,
            'per_page' => $perPage,
        ];

        if ($timeFilter = request()->input('time')) {
            $params['time_filter'] = $timeFilter;
        }

        $response = Http::withToken($accessToken)->get($endpoint, $params);

        if ($response->failed()) {
            Log::error("Lỗi gọi API mạng xã hội {$platform}: " . $response->status());
            return [];
        }

        // Chuẩn hóa dữ liệu từ API mạng xã hội
        $data = $response->json()['data'] ?? [];
        $results = [];

        foreach ($data as $item) {
            $results[] = [
                'title' => $item['title'] ?? $item['text'] ?? '',
                'link' => $item['url'] ?? '',
                'snippet' => $item['description'] ?? $item['text'] ?? '',
                'pagemap' => ['cse_thumbnail' => [['src' => $item['thumbnail'] ?? null]]],
            ];
        }

        return $results;
    }

    protected function authenticateSocialMedia($platform)
    {
        // Logic xác thực với API mạng xã hội (OAuth hoặc API Key)
        // Thay bằng logic thực tế, ví dụ gọi endpoint xác thực
        return 'mock_access_token';
    }

    protected function getSocialMediaEndpoint($platform)
    {
        $endpoints = [
            'facebook.com' => 'https://graph.facebook.com/v12.0/search',
            'twitter.com' => 'https://api.twitter.com/2/tweets/search/recent',
            'instagram.com' => 'https://api.instagram.com/v1/search',
        ];

        return $endpoints[$platform] ?? '';
    }

    protected function normalizeSocialMediaLink($link, $platform)
    {
        // Chuẩn hóa link để dẫn trực tiếp đến bài viết
        // Ví dụ: Loại bỏ query parameters không cần thiết
        return $link; // Thay bằng logic thực tế nếu cần
    }

    public function evaluateUrl(Request $request)
    {
        $url = $request->input('url');
        $snippet = $request->input('snippet') ?? '';

        $sentiment = $this->searchService->evaluateContent($url, $snippet);

        return response()->json(['sentiment' => $sentiment]);
    }

    public function autocomplete(Request $request)
    {
        $query = $request->input('query', '');
        $suggestions = [];

        if (strlen($query) >= 2) {
            $cacheKey = 'autocomplete_' . md5($query);
            $suggestions = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($query) {
                $response = Http::get('https://suggestqueries.google.com/complete/search', [
                    'client' => 'firefox',
                    'q' => $query,
                    'hl' => 'vi',
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return $data[1] ?? [];
                }

                return [];
            });
        }

        return response()->json($suggestions);
    }
}
