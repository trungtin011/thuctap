<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GoogleScraperController extends Controller
{
    public function searchGoogle(Request $request)
    {
        $keyword = $request->input('keyword', ''); // Không có từ khóa mặc định
        
        if (!$keyword) {
            return view('User.search.index', [
                'keyword' => $keyword,
                'vnexpress' => []
            ]);
        }

        $query = urlencode("site:vnexpress.net $keyword");
        $url = "https://html.duckduckgo.com/html/?q={$query}";
    
        $client = new Client([
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            ],
            'verify' => false
        ]);
    
        try {
            $response = $client->get($url);
            $html = $response->getBody()->getContents();
            Log::info($html);
            $crawler = new Crawler($html);
            $results = [];
    
            $crawler->filter('div.result__body')->each(function ($node) use (&$results) {
                $titleNode = $node->filter('a.result__a');
                $snippetNode = $node->filter('.result__snippet');
    
                $title = $titleNode->count() ? $titleNode->text() : '';
                $rawLink = $titleNode->count() ? $titleNode->attr('href') : '';
                $snippet = $snippetNode->count() ? $snippetNode->text() : '';
    
                // Giải mã link từ DuckDuckGo
                $link = $rawLink;
                if (strpos($rawLink, 'uddg=') !== false) {
                    $parsedUrl = parse_url($rawLink, PHP_URL_QUERY);
                    parse_str($parsedUrl, $queryParams);
                    if (isset($queryParams['uddg'])) {
                        $link = urldecode($queryParams['uddg']);
                    }
                }
    
                if (strpos($link, 'vnexpress.net') !== false) {
                    $results[] = [
                        'title' => $title,
                        'link' => $link,
                        'snippet' => $snippet,
                    ];
                }
            });
    
            return view('User.search.index', [
                'keyword' => $keyword,
                'vnexpress' => $results
            ]);
        } catch (\Exception $e) {
            return view('User.search.index', [
                'keyword' => $keyword,
                'vnexpress' => [[
                    'title' => 'Lỗi khi tìm kiếm',
                    'snippet' => $e->getMessage(),
                    'link' => '#'
                ]]
            ]);
        }
    }
}