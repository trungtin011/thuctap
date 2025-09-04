<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class FinancialRecord extends Model
{
    protected $fillable = [
        'department_id',
        'platform_id',
        'revenue',
        'record_date',
        'record_time',
        'note',
        'status',
        'submitted_by',
        'manager_approved_by',
        'admin_approved_by',
        'manager_note',
        'admin_note',
    ];

    protected $casts = [
        'status' => 'string',
        'revenue' => 'decimal:2',
        'roas' => 'decimal:2',
        'record_date' => 'date',
        'record_time' => 'string',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function submittedBy()
    {
        return $this->belongsTo(Employee::class, 'submitted_by');
    }

    public function managerApprovedBy()
    {
        return $this->belongsTo(Employee::class, 'manager_approved_by');
    }

    public function adminApprovedBy()
    {
        return $this->belongsTo(Employee::class, 'admin_approved_by');
    }

    // Lấy metric_values dựa trên platform_id và recorded_at
    public function getMetricValuesAttribute()
    {
        $recordedAt = $this->record_date->format('Y-m-d') . ' ' . $this->record_time;
        return MetricValue::whereIn('metric_id', $this->platform->metrics->pluck('id'))
            ->where('recorded_at', $recordedAt)
            ->get();
    }

    // Accessor cho revenue_sources
    protected function revenueSources(): Attribute
    {
        return Attribute::make(
            get: fn() => json_decode($this->note)->revenue_sources ?? []
        );
    }
}
