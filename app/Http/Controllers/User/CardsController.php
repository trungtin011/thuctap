<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Card;
use App\Models\Transaction;

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
        ], [
            'telco.required' => 'Vui lòng chọn nhà mạng.',
            'amount.required' => 'Vui lòng chọn mệnh giá.',
            'amount.integer' => 'Mệnh giá phải là số nguyên.',
            'amount.in' => 'Mệnh giá không hợp lệ.',
            'card_serial.required' => 'Vui lòng nhập số serial.',
            'card_serial.regex' => 'Số serial chỉ được chứa số.',
            'card_code.required' => 'Vui lòng nhập mã thẻ.',
            'card_code.regex' => 'Mã thẻ chỉ được chứa số.',
        ]);

        $user = Auth::user();

        // Map telco values to API expected format
        $telcoMap = [
            'Viettel' => 'VIETTEL',
            'Vinaphone' => 'VINAPHONE',
            'Mobifone' => 'MOBIFONE',
        ];

        // Prepare API request data
        $requestData = [
            'telco' => $telcoMap[$request->telco],
            'amount' => (int) $request->amount, // Ensure amount is integer
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
                    'User-Agent' => 'Laravel/12.x',
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

            // Map API error messages to user-friendly messages
            $errorMessages = [
                'lang.invalid_card_code' => 'Mã thẻ không hợp lệ, vui lòng kiểm tra lại.',
                'lang.invalid_card_serial' => 'Số serial không hợp lệ, vui lòng kiểm tra lại.',
                'lang.card_used' => 'Thẻ đã được sử dụng trước đó.',
                'lang.card_expired' => 'Thẻ đã hết hạn.',
                'lang.invalid_amount' => 'Mệnh giá không đúng, vui lòng chọn lại.',
                'lang.card_not_found' => 'Không tìm thấy thông tin thẻ, vui lòng thử lại.',
                'lang.system_error' => 'Lỗi hệ thống, vui lòng thử lại sau.',
            ];

            // Handle response
            if ($response->failed()) {
                $errorMessage = $json['message'] ?? 'Lỗi từ API: ' . $response->status();
                $userFriendlyMessage = $errorMessages[$errorMessage] ?? 'Gửi thẻ thất bại: ' . $errorMessage;
                Log::error('API Request Failed:', [
                    'response' => $json,
                    'status' => $response->status(),
                ]);
                return back()->withErrors(['api' => $userFriendlyMessage]);
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
                session()->flash('success', 'Gửi thẻ thành công, chờ xử lý...');
                return back();
            } else {
                $errorMessage = $json['message'] ?? 'Lỗi không xác định từ API';
                $userFriendlyMessage = $errorMessages[$errorMessage] ?? 'Gửi thẻ thất bại: ' . $errorMessage;
                Log::error('API Status Error:', ['response' => $json]);
                return back()->withErrors(['api' => $userFriendlyMessage]);
            }
        } catch (RequestException $e) {
            Log::error('API Request Exception:', ['error' => $e->getMessage()]);
            if (strpos($e->getMessage(), 'Could not resolve host') !== false) {
                return back()->withErrors(['api' => 'Không thể kết nối đến API: Máy chủ API không khả dụng hoặc địa chỉ sai.']);
            }
            return back()->withErrors(['api' => 'Không thể kết nối đến API: ' . $e->getMessage()]);
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

        $serial = $request->serial; // 10010758543665
        $code = $request->code; // 117969500944577
        $status = $request->status; // 1 (success), 2 (wrong value), 3 (wrong card), 99 (pending)
        $real_amount = $request->amount; // Số tiền thực nhận (sau khi trừ phí)
        $request_id = $request->request_id; // ID giao dịch

        $card = Card::where('card_serial', $serial)
            ->where('card_code', $code)
            ->where('request_id', $request_id)
            ->first();

        if (!$card) {
            Log::error('Card not found', ['serial' => $serial, 'code' => $code, 'request_id' => $request_id]);
            return response('Card not found', 404);
        }

        // Cập nhật trạng thái giao dịch
        $card->status = match ($status) {
            '1' => 'success',
            '2', '3' => 'failed',
            '99' => 'pending',
            default => 'failed',
        };
        $card->response = $real_amount; // Lưu số tiền thực nhận
        $card->save();

        if ($status == '1' && is_numeric($real_amount) && $real_amount > 0) {
            $user = $card->user;
            $user->balance += (int)$real_amount; // Cộng số tiền thực nhận vào số dư
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
        $cards = Card::where('user_id', Auth::user()->id)->latest()->paginate(10);
        return view('user.cards.history', compact('cards'));
    }
}
