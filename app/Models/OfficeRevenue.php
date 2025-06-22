<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeRevenue extends Model


{
    protected $fillable = [
        'cash',
        'bank_transfer',
        'expense',
        'total',
        'status',
        'reject_reason',
        'record_date',
        'department_id',
        'submitted_by',
    ];

    public function offices()
    {
        return $this->belongsToMany(Office::class, 'office_revenue_offices')
                    ->withPivot('value');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function submittedBy()
    {
        return $this->belongsTo(Employee::class, 'submitted_by');
    }
}
    

