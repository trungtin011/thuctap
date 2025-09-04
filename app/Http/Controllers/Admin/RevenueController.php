<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialRecord;
<<<<<<< HEAD
use App\Models\Platform;
use App\Models\DaiLy;
use App\Models\Office;
use App\Models\Department;
=======
>>>>>>> origin/khoa
use Illuminate\Http\Request;
use Carbon\Carbon;
use GuzzleHttp\Client;

class RevenueController extends Controller
{
    public function getRevenueData(Request $request)
    {
        $date1 = $request->input('date1', Carbon::now()->subDay()->format('Y-m-d'));
        $date2 = $request->input('date2', Carbon::now()->format('Y-m-d'));
<<<<<<< HEAD
        $platform_id = $request->input('platform_id');
        $dai_ly_id = $request->input('dai_ly_id');
        $office_id = $request->input('office_id');
        $department_id = $request->input('department_id');

        // Khởi tạo truy vấn cơ bản
        $query = FinancialRecord::query();

        // Áp dụng các bộ lọc nếu có
        if ($platform_id) {
            $query->where('platform_id', $platform_id);
        }
        if ($dai_ly_id) {
            $query->where('dai_ly_id', $dai_ly_id);
        }
        if ($office_id) {
            $query->where('office_id', $office_id);
        }
        if ($department_id) {
            $query->where('department_id', $department_id);
        }

        // Tính toán doanh thu
        $revenueDate1 = $query->clone()->whereDate('record_date', $date1)->sum('revenue');
        $revenueDate2 = $query->clone()->whereDate('record_date', $date2)->sum('revenue');

        // Tính toán chi phí
        $expensesDate1 = $query->clone()->whereDate('record_date', $date1)
=======

        $revenueDate1 = FinancialRecord::whereDate('record_date', $date1)->sum('revenue');
        $revenueDate2 = FinancialRecord::whereDate('record_date', $date2)->sum('revenue');

        $expensesDate1 = FinancialRecord::whereDate('record_date', $date1)
>>>>>>> origin/khoa
            ->withSum('expenses', 'amount')
            ->get()
            ->sum('expenses_sum_amount');

<<<<<<< HEAD
        $expensesDate2 = $query->clone()->whereDate('record_date', $date2)
=======
        $expensesDate2 = FinancialRecord::whereDate('record_date', $date2)
>>>>>>> origin/khoa
            ->withSum('expenses', 'amount')
            ->get()
            ->sum('expenses_sum_amount');

<<<<<<< HEAD
        // Lấy danh sách để hiển thị trong form lọc
        $platforms = Platform::select('id', 'name')->get();
        $dai_lies = DaiLy::select('id', 'ten_dai_ly')->get();
        $offices = Office::select('id', 'name')->get();
        $departments = Department::select('id', 'name')->get();

        // Phân tích thay đổi doanh thu
        $analysis = $this->analyzeRevenueChange(
            $revenueDate1,
            $revenueDate2,
            $expensesDate1,
            $expensesDate2,
            $date1,
            $date2,
            $platform_id,
            $dai_ly_id,
            $office_id,
            $department_id
        );
=======
        $analysis = $this->analyzeRevenueChange($revenueDate1, $revenueDate2, $expensesDate1, $expensesDate2, $date1, $date2);
>>>>>>> origin/khoa

        return response()->json([
            'labels' => [Carbon::parse($date1)->format('d/m/Y'), Carbon::parse($date2)->format('d/m/Y')],
            'revenues' => [$revenueDate1, $revenueDate2],
            'expenses' => [$expensesDate1, $expensesDate2],
            'analysis' => $analysis,
<<<<<<< HEAD
            'platforms' => $platforms,
            'dai_lies' => $dai_lies,
            'offices' => $offices,
            'departments' => $departments,
        ]);
    }

    private function analyzeRevenueChange($revenueDate1, $revenueDate2, $expensesDate1, $expensesDate2, $date1, $date2, $platform_id = null, $dai_ly_id = null, $office_id = null, $department_id = null)
    {
        $client = new Client();
        $apiKey = env('GEMINI_API_KEY', 'AIzaSyDnEjDzfRGomjtglYslqzC93faK859MMc8');

        // Tạo câu prompt chi tiết hơn với thông tin lọc
        $filterDetails = '';
        if ($platform_id) {
            $platform = Platform::find($platform_id);
            $filterDetails .= " trên nền tảng {$platform->name}";
        }
        if ($dai_ly_id) {
            $dai_ly = DaiLy::find($dai_ly_id);
            $filterDetails .= ", đại lý {$dai_ly->ten_dai_ly}";
        }
        if ($office_id) {
            $office = Office::find($office_id);
            $filterDetails .= ", văn phòng {$office->name}";
        }
        if ($department_id) {
            $department = Department::find($department_id);
            $filterDetails .= ", phòng ban {$department->name}";
        }

        $prompt = "Phân tích lý do doanh thu thay đổi từ $revenueDate1 VND (chi phí: $expensesDate1 VND) vào ngày $date1 sang $revenueDate2 VND (chi phí: $expensesDate2 VND) vào ngày $date2$filterDetails. Đưa ra các lý do có thể giải thích sự thay đổi, bao gồm yếu tố thị trường, chiến dịch quảng cáo, sự kiện đặc biệt, và hiệu quả chi phí (ROAS). Trả lời bằng tiếng Việt, ngắn gọn, rõ ràng, và chuyên nghiệp.";
=======
        ]);
    }

    private function analyzeRevenueChange($revenueDate1, $revenueDate2, $expensesDate1, $expensesDate2, $date1, $date2)
    {
        $client = new Client();
        $apiKey = env('GEMINI_API_KEY', 'AIzaSyDnEjDzfRGomjtglYslqzC93faK859MMc8');
        $prompt = "Phân tích lý do doanh thu thay đổi từ $revenueDate1 VND (chi phí: $expensesDate1 VND) vào ngày $date1 sang $revenueDate2 VND (chi phí: $expensesDate2 VND) vào ngày $date2. Đưa ra các lý do có thể giải thích sự thay đổi, bao gồm yếu tố thị trường, chiến dịch quảng cáo, sự kiện đặc biệt, và hiệu quả chi phí (ROAS). Trả lời bằng tiếng Việt, ngắn gọn, rõ ràng, và chuyên nghiệp.";
>>>>>>> origin/khoa

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

<<<<<<< HEAD
            $analysis = "Doanh thu ngày $date2 ($revenueDate2 VND) thay đổi " . round($percentageChange, 2) . "% so với ngày $date1 ($revenueDate1 VND)$filterDetails. ";
=======
            $analysis = "Doanh thu ngày $date2 ($revenueDate2 VND) thay đổi " . round($percentageChange, 2) . "% so với ngày $date1 ($revenueDate1 VND). ";
>>>>>>> origin/khoa
            $analysis .= "Chi phí ngày $date2 ($expensesDate2 VND) so với ngày $date1 ($expensesDate1 VND). ";
            $analysis .= "ROAS ngày $date1: " . round($roas1, 2) . ", ngày $date2: " . round($roas2, 2) . ". ";
            $analysis .= "Lý do có thể bao gồm: chiến dịch quảng cáo, sự kiện đặc biệt, hoặc thay đổi nhu cầu thị trường.";
            return $analysis;
        }
    }
}
