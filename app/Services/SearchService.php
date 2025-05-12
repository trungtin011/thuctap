<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SearchService
{
    public function evaluateContent($url, $snippet = '')
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

            $geminiApiKey = env('GEMINI_API_KEY');
            $geminiEndpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

            $prompt = "Phân tích nội dung sau và đánh giá xem nội dung có tích cực (Tốt), tiêu cực (Xấu), hay trung bình (Trung bình):\n\n" . $content .
                "\n\nTrả về một câu duy nhất với kết quả: 'Tốt', 'Xấu', hoặc 'Trung bình'.";

            $geminiResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($geminiEndpoint . '?key=' . $geminiApiKey, [
                'contents' => [['parts' => [['text' => $prompt]]]]
            ]);

            if ($geminiResponse->ok()) {
                return $geminiResponse->json()['candidates'][0]['content']['parts'][0]['text'] ?? 'Không xác định';
            } else {
                Log::error('Lỗi khi gọi Gemini API', ['status' => $geminiResponse->status()]);
                return 'Lỗi khi gọi Gemini API';
            }
        } catch (\Exception $e) {
            Log::error('Lỗi khi xử lý evaluateContent', ['error' => $e->getMessage()]);
            return 'Lỗi xử lý nội dung';
        }
    }

    public function translateText($text, $targetLang = 'vi')
    {
        if (empty($targetLang)) $targetLang = 'vi';

        // Nếu text rỗng, trả lại luôn
        if (empty($text)) return $text;

        // Tạo cache key duy nhất từ đoạn văn và ngôn ngữ
        $cacheKey = 'translation_' . md5($targetLang . '_' . $text);

        // Dùng cache trong 24 giờ
        return Cache::remember($cacheKey, now()->addHours(24), function () use ($text, $targetLang) {
            $response = Http::get("https://translate.googleapis.com/translate_a/single", [
                'client' => 'gtx',
                'sl' => 'auto',
                'tl' => $targetLang,
                'dt' => 't',
                'q' => $text,
            ]);

            $data = $response->json();
            return $data[0][0][0] ?? $text;
        });
    }
}
