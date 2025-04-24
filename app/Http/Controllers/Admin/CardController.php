<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CardController extends Controller
{
    public function index()
    {
        // Lấy tất cả các thẻ nạp
        $cards = Card::latest()->paginate(10);
        return view('admin.cards.index', compact('cards'));
    }

    public function updateStatus(Request $request, $id)
    {
        $card = Card::findOrFail($id);
        $card->status = $request->status;
        $card->save();

        // Ghi vào transactions nếu trạng thái là 'success' và thẻ chưa từng thành công trước đó
        if ($request->status === 'success' && $card->status !== 'success_before') {
            $user = User::find($card->user_id); // Giả sử có user_id trong bảng card

            // Cộng tiền
            $user->balance += $card->amount;
            $user->save();

            // Ghi log giao dịch
            Transaction::create([
                'user_id'         => $user->id,
                'transaction_type' => 'deposit',
                'amount'          => $card->amount,
                'description'     => 'Nạp tiền từ thẻ ' . $card->telco,
                'balance_after'   => $user->balance,
            ]);
        }

        return redirect()->back()->with('success', 'Cập nhật trạng thái thành công!');
    }
}
