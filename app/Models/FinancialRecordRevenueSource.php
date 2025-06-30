<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialRecordRevenueSource extends Model
{
    protected $fillable = ['financial_record_id', 'route_id', 'source_name', 'amount', 'commission'];

    public function financialRecord()
    {
        return $this->belongsTo(FinancialRecord::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}
