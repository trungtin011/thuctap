<?php

namespace App\Imports;

use App\Models\FinancialRecord;
use Maatwebsite\Excel\Concerns\ToModel;

class FinancialRecordsImport implements ToModel
{
    public function model(array $row)
    {
        return new FinancialRecord([
            'department_id' => $row[0],
            'platform_id' => $row[1],
            'dai_ly_id' => $row[2],
            'revenue' => $row[3],
            'commission' => $row[4],
            'record_date' => $row[5],
            'record_time' => $row[6],
            // ...other fields...
        ]);
    }
}
