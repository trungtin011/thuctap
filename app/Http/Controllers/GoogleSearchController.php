<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class GoogleSearchController extends Controller
{
    public function search(Request $request)
    {
        $keyword = $request->input('keyword', '');
        if (!$keyword) {
            return view('user.search.index', [
                'keyword' => '',
                'vnexpress' => []
            ]);
        }

        $client = new Client();
        $apiKey = env('GOOGLE_API_KEY'); // API Key từ .env
        $searchEngineId = env('GOOGLE_CX'); // Search Engine ID từ .env
        $url = "https://www.googleapis.com/customsearch/v1?q=site:vnexpress.net+$keyword&key=$apiKey&cx=$searchEngineId";

        try {
            $response = $client->get($url);
            $data = json_decode($response->getBody(), true);
            $results = [];

            foreach ($data['items'] ?? [] as $item) {
                $results[] = [
                    'title' => $item['title'],
                    'link' => $item['link'],
                    'snippet' => $item['snippet']
                ];
            }

            return view('user.search.index', [
                'keyword' => $keyword,
                'vnexpress' => $results
            ]);
        } catch (\Exception $e) {
            return view('user.search.index', [
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
