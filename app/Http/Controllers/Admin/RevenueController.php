<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialRecord;
use Illuminate\Http\Request;
use Carbon\Carbon;
use GuzzleHttp\Client;

class RevenueController extends Controller
{
    public function getRevenueData(Request $request)
    {
        $date1 = $request->input('date1', Carbon::now()->subDay()->format('Y-m-d'));
        $date2 = $request->input('date2', Carbon::now()->format('Y-m-d'));

        $revenueDate1 = FinancialRecord::whereDate('record_date', $date1)->sum('revenue');
        $revenueDate2 = FinancialRecord::whereDate('record_date', $date2)->sum('revenue');

        $expensesDate1 = FinancialRecord::whereDate('record_date', $date1)
            ->withSum('expenses', 'amount')
            ->get()
            ->sum('expenses_sum_amount');

        $expensesDate2 = FinancialRecord::whereDate('record_date', $date2)
            ->withSum('expenses', 'amount')
            ->get()
            ->sum('expenses_sum_amount');

        $analysis = $this->analyzeRevenueChange($revenueDate1, $revenueDate2, $expensesDate1, $expensesDate2, $date1, $date2);

        return response()->json([
            'labels' => [Carbon::parse($date1)->format('d/m/Y'), Carbon::parse($date2)->format('d/m/Y')],
            'revenues' => [$revenueDate1, $revenueDate2],
            'expenses' => [$expensesDate1, $expensesDate2],
            'analysis' => $analysis,
        ]);
    }

    private function analyzeRevenueChange($revenueDate1, $revenueDate2, $expensesDate1, $expensesDate2, $date1, $date2)
    {
        $client = new Client();
        $apiKey = env('GEMINI_API_KEY', 'AIzaSyDnEjDzfRGomjtglYslqzC93faK859MMc8');
        $prompt = "Phân tích lý do doanh thu thay đổi từ $revenueDate1 VND (chi phí: $expensesDate1 VND) vào ngày $date1 sang $revenueDate2 VND (chi phí: $expensesDate2 VND) vào ngày $date2. Đưa ra các lý do có thể giải thích sự thay đổi, bao gồm yếu tố thị trường, chiến dịch quảng cáo, sự kiện đặc biệt, và hiệu quả chi phí (ROAS). Trả lời bằng tiếng Việt, ngắn gọn, rõ ràng, và chuyên nghiệp.";

        try {
            $response = $client->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $apiKey, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ]
                ],
            ]);

            $result = json_decode($response->getBody(), true);
            return $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Không thể phân tích dữ liệu.';
        } catch (\Exception $e) {
            $percentageChange = $revenueDate1 > 0 ? ($revenueDate2 - $revenueDate1) / $revenueDate1 * 100 : 0;
            $roas1 = $expensesDate1 > 0 ? $revenueDate1 / $expensesDate1 : 0;
            $roas2 = $expensesDate2 > 0 ? $revenueDate2 / $expensesDate2 : 0;

            $analysis = "Doanh thu ngày $date2 ($revenueDate2 VND) thay đổi " . round($percentageChange, 2) . "% so với ngày $date1 ($revenueDate1 VND). ";
            $analysis .= "Chi phí ngày $date2 ($expensesDate2 VND) so với ngày $date1 ($expensesDate1 VND). ";
            $analysis .= "ROAS ngày $date1: " . round($roas1, 2) . ", ngày $date2: " . round($roas2, 2) . ". ";
            $analysis .= "Lý do có thể bao gồm: chiến dịch quảng cáo, sự kiện đặc biệt, hoặc thay đổi nhu cầu thị trường.";
            return $analysis;
        }
    }
}
