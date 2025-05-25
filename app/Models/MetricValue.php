<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetricValue extends Model
{
    protected $fillable = ['metric_id', 'value', 'recorded_at'];

    public function metric()
    {
        return $this->belongsTo(PlatformMetric::class, 'metric_id');
    }
}