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
    // Gửi thẻ cào đến API
    public function submitCard(Request $request)
    {
        $request->validate([
            'telco' => 'required|in:Viettel,Vinaphone,Mobifone',
            'amount' => 'required|integer|in:10000,20000,30000,50000,100000,200000,300000,500000,1000000',
            'card_serial' => 'required|string|max:255',
            'card_code' => 'required|string|max:255',
        ]);

        $user = auth()->user();

        // Prepare API request data
        $requestData = [
            'telco' => $request->telco,
            'amount' => $request->amount,
            'serial' => $request->card_serial, // API expects 'serial'
            'code' => $request->card_code, // API expects 'code'
            'request_id' => uniqid(),
            'partner_id' => env('TSR_PARTNER_ID', '71433538534'),
            'callback_url' => route('callback.card'),
            'api_key' => env('TSR_API_KEY', '7b567b57851190d19e16b13bb976e899'),
        ];

        try {
            // Gửi đến API
            $response = Http::timeout(10)->post('https://api.thesieure.com/card/charge', $requestData);

            // Log API response for debugging
            Log::info('API Response:', ['response' => $response->json(), 'status' => $response->status()]);

            // Check if API request was successful
            if ($response->failed()) {
                $errorMessage = $response->json()['message'] ?? 'Lỗi từ API: ' . $response->status();
                Log::error('API Request Failed:', ['response' => $response->json(), 'status' => $response->status()]);
                return back()->with('error', 'Gửi thẻ thất bại: ' . $errorMessage);
            }

            // Lưu thông tin thẻ
            Card::create([
                'user_id' => $user->id,
                'telco' => $request->telco,
                'amount' => $request->amount,
                'card_serial' => $request->card_serial,
                'card_code' => $request->card_code,
                'status' => 'pending',
            ]);

            return back()->with('success', 'Đã gửi thẻ, chờ xử lý...');
        } catch (RequestException $e) {
            Log::error('API Request Exception:', ['error' => $e->getMessage()]);
            if (strpos($e->getMessage(), 'Could not resolve host') !== false) {
                return back()->with('error', 'Không thể kết nối đến API: Máy chủ API không khả dụng hoặc địa chỉ sai.');
            }
            return back()->with('error', 'Không thể kết nối đến API: ' . $e->getMessage());
        }
    }

    // Nhận callback từ đối tác
    public function callback(Request $request)
    {
        Log::info('CALLBACK THẺ CÀO:', $request->all());

        $serial = $request->serial;
        $code = $request->code;
        $status = $request->status; // 'success' | 'fail'
        $real_amount = $request->real_amount;

        $card = Card::where('card_serial', $serial)->where('card_code', $code)->first();

        if (!$card) {
            Log::error('Card not found for serial: ' . $serial . ', code: ' . $code);
            return response('Card not found', 404);
        }

        $card->status = $status;
        $card->response = $real_amount; // Store real_amount as text in response column
        $card->save();

        // Nếu thành công → cộng tiền
        if ($status === 'success' && is_numeric($real_amount) && $real_amount > 0) {
            $user = $card->user;
            $user->balance += (int)$real_amount; // Cast to integer for balance
            $user->save();

            // Lưu lịch sử giao dịch
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

    // Lịch sử nạp thẻ
    public function history()
    {
        $cards = Card::where('user_id', auth()->id())->latest()->paginate(10);
        return view('cards.history', compact('cards'));
    }
}