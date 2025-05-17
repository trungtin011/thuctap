<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

}