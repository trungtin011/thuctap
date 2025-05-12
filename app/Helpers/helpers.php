<?php

use Illuminate\Support\Facades\Http;

if (!function_exists('translateText')) {
    function translateText($text, $targetLang = 'vi')
    {
        if ($targetLang === 'vi' || empty($targetLang)) return $text;

        $response = Http::get("https://translate.googleapis.com/translate_a/single", [
            'client' => 'gtx',
            'sl' => 'auto',
            'tl' => $targetLang,
            'dt' => 't',
            'q' => $text,
        ]);

        $data = $response->json();

        return $data[0][0][0] ?? $text;
    }
}
