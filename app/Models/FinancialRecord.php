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
        'status' => 'string', // Hoặc dùng custom Enum nếu Laravel 9+ và có class Enum
        'revenue' => 'decimal:2',
        'roas' => 'decimal:2',
        'record_date' => 'date',
        'record_time' => 'string', // Giữ string vì cột time không cast thành datetime
    ];

    // Quan hệ với departments
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Quan hệ với platforms
    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    // Quan hệ với expenses
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    // Quan hệ với employee (submitted_by)
    public function submittedBy()
    {
        return $this->belongsTo(Employee::class, 'submitted_by');
    }

    // Quan hệ với employee (manager_approved_by)
    public function managerApprovedBy()
    {
        return $this->belongsTo(Employee::class, 'manager_approved_by');
    }

    // Quan hệ với employee (admin_approved_by)
    public function adminApprovedBy()
    {
        return $this->belongsTo(Employee::class, 'admin_approved_by');
    }

    // Lấy metric_values thông qua platform và recorded_at
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
