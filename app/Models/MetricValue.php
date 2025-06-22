<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetricValue extends Model
{
    protected $fillable = ['metric_id', 'value', 'recorded_at', 'financial_record_id'];

    public function metric()
    {
        return $this->belongsTo(PlatformMetric::class, 'metric_id'); // Đảm bảo model đúng
    }

    public function financialRecord()
    {
        return $this->belongsTo(FinancialRecord::class, 'financial_record_id');
    }
}
