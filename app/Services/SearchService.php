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

            // Gọi Gemini API
            $geminiApiKey = env('GEMINI_API_KEY', 'AIzaSyDhb0kguqfFXdsknNviCU7dLI6NeCQS8hs');
            $geminiEndpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

            $prompt = "Phân tích nội dung sau và đánh giá xem nội dung có tích cực (Tốt), tiêu cực (Xấu), hay trung tính (Trung bình). \n\n" .
                "- Nội dung được coi là 'Tốt' nếu: \n" .
                "  - Cung cấp thông tin hữu ích, đáng tin cậy về sản phẩm, dịch vụ, hoặc công ty.\n" .
                "  - Chứa từ ngữ tích cực như 'chất lượng cao', 'uy tín', 'đáng tin cậy', hoặc đánh giá tốt.\n" .
                "  - Quảng bá sản phẩm/dịch vụ một cách chân thực, không có dấu hiệu lừa đảo.\n\n" .
                "- Nội dung được coi là 'Xấu' nếu: \n" .
                "  - Chứa thông tin sai lệch, lừa đảo, hoặc gây hiểu lầm.\n" .
                "  - Có từ ngữ tiêu cực như 'kém chất lượng', 'lừa đảo', 'phàn nàn', hoặc đánh giá xấu.\n" .
                "  - Nội dung kích động, xúc phạm, hoặc không phù hợp.\n\n" .
                "- Nội dung được coi là 'Trung bình' nếu: \n" .
                "  - Chỉ cung cấp thông tin thực tế, không có cảm xúc tích cực hoặc tiêu cực rõ ràng (ví dụ: thông tin lịch sử, số liệu, hoặc mô tả kỹ thuật).\n" .
                "  - Không đủ thông tin để đánh giá là Tốt hoặc Xấu.\n\n" .
                "Trả về một câu ngắn gọn chỉ với kết quả: 'Tốt', 'Xấu', hoặc 'Trung bình'. Nội dung: \n\n" . $content;

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

    public function translateText($text, $targetLang = 'vi')
    {
        if (empty($targetLang)) $targetLang = 'vi';

        // Nếu text rỗng, trả lại luôn
        if (empty($text)) return $text;

        // Tạo cache key duy nhất từ đoạn văn và ngôn ngữ
        $cacheKey = 'translation_' . md5($targetLang . '_' . $text);

        // Dùng cache trong 24 giờ
        return Cache::remember($cacheKey, now()->addHours(24), function () use ($text, $targetLang) {
            $apiKey = env('GOOGLE_TRANSLATE_API_KEY');

            if (!$apiKey) {
                return $text; // Trả lại nguyên văn nếu không có API key
            }

            $response = Http::post("https://translation.googleapis.com/language/translate/v2", [
                'q' => $text,
                'target' => $targetLang,
                'format' => 'text',
                'key' => $apiKey
            ]);

            if ($response->ok()) {
                return $response->json()['data']['translations'][0]['translatedText'] ?? $text;
            }

            Log::error('Lỗi dịch văn bản: ' . $response->body());
            return $text;
        });
    }
}
