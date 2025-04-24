<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Card;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;

class CardsController extends Controller
{
    public function submitCard(Request $request)
    {
        // Validate request data
        $request->validate([
            'telco' => 'required|in:Viettel,Vinaphone,Mobifone',
            'amount' => 'required|integer|in:10000,20000,30000,50000,100000,200000,300000,500000,1000000',
            'card_serial' => 'required|string|min:10|max:20|regex:/^[0-9]+$/', // Numbers only
            'card_code' => 'required|string|min:10|max:20|regex:/^[0-9]+$/', // Numbers only
        ]);

        $user = auth()->user();

        // Map telco values to API expected format
        $telcoMap = [
            'Viettel' => 'VIETTEL',
            'Vinaphone' => 'VINAPHONE',
            'Mobifone' => 'MOBIFONE',
        ];

        // Prepare API request data
        $requestData = [
            'telco' => $telcoMap[$request->telco],
            'amount' => $request->amount,
            'serial' => $request->card_serial,
            'code' => $request->card_code,
            'request_id' => uniqid($user->id . '_', true),
            'partner_id' => env('TSR_PARTNER_ID'),
            'command' => 'charging',
        ];

        // Generate signature
        $partner_key = env('TSR_PARTNER_KEY');
        $sign = md5($partner_key . $request->card_code . $request->card_serial);

        try {
            // Send API request (GET with query string)
            $response = Http::timeout(30)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'User-Agent' => 'Laravel/10.x',
                ])
                ->get('https://thesieure.com/chargingws/v2', [
                    'sign' => $sign,
                    'telco' => $requestData['telco'],
                    'code' => $requestData['code'],
                    'serial' => $requestData['serial'],
                    'amount' => $requestData['amount'],
                    'request_id' => $requestData['request_id'],
                    'partner_id' => $requestData['partner_id'],
                    'command' => $requestData['command'],
                ]);

            // Log API response
            $json = $response->json();
            Log::info('API Response:', [
                'response' => $json,
                'status' => $response->status(),
                'request_data' => $requestData,
                'sign' => $sign,
            ]);

            // Handle response
            if ($response->failed()) {
                $errorMessage = $json['message'] ?? 'Lỗi từ API: ' . $response->status();
                Log::error('API Request Failed:', [
                    'response' => $json,
                    'status' => $response->status(),
                ]);
                return back()->with('error', 'Gửi thẻ thất bại: ' . $errorMessage);
            }

            // Check API status
            if ($json['status'] == 99) {
                // Save card information
                Card::create([
                    'user_id' => $user->id,
                    'telco' => $request->telco,
                    'amount' => $request->amount,
                    'card_serial' => $request->card_serial,
                    'card_code' => $request->card_code,
                    'status' => 'pending',
                    'request_id' => $requestData['request_id'],
                ]);
                return back()->with('success', 'Gửi thẻ thành công, chờ xử lý...');
            } else {
                $errorMessage = $json['message'] ?? 'Lỗi không xác định từ API';
                Log::error('API Status Error:', ['response' => $json]);
                return back()->with('error', 'Gửi thẻ thất bại: ' . $errorMessage);
            }
        } catch (RequestException $e) {
            Log::error('API Request Exception:', ['error' => $e->getMessage()]);
            if (strpos($e->getMessage(), 'Could not resolve host') !== false) {
                return back()->with('error', 'Không thể kết nối đến API: Máy chủ API không khả dụng hoặc địa chỉ sai.');
            }
            return back()->with('error', 'Không thể kết nối đến API: ' . $e->getMessage());
        }
    }

    public function callback(Request $request)
    {
        Log::info('CALLBACK THẺ CÀO:', $request->all());

        // Verify callback signature
        $callbackSign = $request->callback_sign;
        $partner_key = env('TSR_PARTNER_KEY');
        $expectedSign = md5($partner_key . $request->serial . $request->code);
        if ($callbackSign !== $expectedSign) {
            Log::error('Invalid callback signature', ['received' => $callbackSign, 'expected' => $expectedSign]);
            return response('Invalid signature', 403);
        }

        $serial = $request->serial;
        $code = $request->code;
        $status = $request->status; // 1: success, 2: wrong value, 3: wrong card, 99: pending
        $real_amount = $request->amount;
        $request_id = $request->request_id;

        $card = Card::where('card_serial', $serial)
            ->where('card_code', $code)
            ->where('request_id', $request_id)
            ->first();

        if (!$card) {
            Log::error('Card not found', ['serial' => $serial, 'code' => $code, 'request_id' => $request_id]);
            return response('Card not found', 404);
        }

        // Map API status to internal status
        $card->status = match ($status) {
            '1' => 'success',
            '2', '3' => 'failed',
            '99' => 'pending',
            default => 'failed',
        };
        $card->response = $real_amount;
        $card->save();

        // Update user balance if successful
        if ($status == '1' && is_numeric($real_amount) && $real_amount > 0) {
            $user = $card->user;
            $user->balance += (int)$real_amount;
            $user->save();

            Transaction::create([
                'user_id' => $user->id,
                'transaction_type' => 'deposit',
                'amount' => (int)$real_amount,
                'description' => 'Nạp thẻ thành công',
                'balance_after' => $user->balance,
            ]);
        }

        return response('OK', 200);
    }

    public function history()
    {
        $cards = Card::where('user_id', auth()->id())->latest()->paginate(10);
        return view('user.cards.history', compact('cards'));
    }
}
