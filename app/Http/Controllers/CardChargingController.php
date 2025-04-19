<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CardChargingController extends Controller
{
    public function showForm()
    {
        return view('card.form');
    }

    public function chargeCard(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'loaithe' => 'required|in:VIETTEL,MOBIFONE,VINAPHONE',
            'menhgia' => 'required|in:10000,20000,30000,50000,100000,200000,300000,500000,1000000',
            'seri' => 'required|numeric',
            'mathe' => 'required|numeric',
        ]);

        $loaithe = $request->loaithe;
        $menhgia = $request->menhgia;
        $seri = $request->seri;
        $mathe = $request->mathe;

        // Generate random request ID
        $ranid = rand(1111111111, 9999999999);
        $partner_id = config('services.thesieure.partner_id', '');
        $partner_key = config('services.thesieure.partner_key', '');

        // Build API URL
        $sign = md5($partner_key . $mathe . $seri);
        $url = "https://thesieure.com/chargingws/v2?sign={$sign}&telco={$loaithe}&code={$mathe}&serial={$seri}&amount={$menhgia}&request_id={$ranid}&partner_id={$partner_id}&command=charging";

        // Make cURL request
        $response = Http::get($url);
        $json = $response->json();

        // Handle API response
        if ($json['status'] == 99) {
            return redirect()->route('card.form')->with('success', 'Gửi thẻ thành công');
        } else {
            return redirect()->route('card.form')->with('error', $json['message'] ?? 'Có lỗi xảy ra');
        }
    }
}