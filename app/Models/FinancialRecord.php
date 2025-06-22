<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class FinancialRecord extends Model
{
    protected $fillable = [
        'department_id',
        'platform_id',
        'dai_ly_id',
        'revenue',
        'record_date',
        'record_time',
        'office_id',
        'route_id',
        'note',
        'commission',
        'status',
        'submitted_by',
        'manager_approved_by',
        'admin_approved_by',
        'manager_note',
        'admin_note',
        'roas', // Thêm roas vào fillable
    ];

    protected $casts = [
        'status' => 'string',
        'revenue' => 'decimal:2',
        'roas' => 'decimal:2',
        'record_date' => 'date',
        'record_time' => 'string',
    ];

    // Quan hệ
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

    public function daiLy()
    {
        return $this->belongsTo(DaiLy::class, 'dai_ly_id');
    }

    public function route()
    {
        return $this->belongsTo(\App\Models\Route::class, 'route_id');
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function metricValues()
    {
        return $this->hasMany(MetricValue::class, 'financial_record_id');
    }

    // Accessor cho revenue_sources
    protected function revenueSources(): Attribute
    {
        return Attribute::make(
            get: fn() => json_decode($this->note, true)['revenue_sources'] ?? []
        );
    }

    // Accessor để lấy metric_values từ database
    protected function metricValuesAttribute(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->metricValues->keyBy('metric_id')->map->value->toArray()
        );
    }
}
