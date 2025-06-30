<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeRevenue extends Model
{
    protected $table = 'office_revenues';
    protected $fillable = ['department_id', 'submitted_by', 'cash', 'bank_transfer', 'expense', 'total', 'status', 'reject_reason', 'record_date'];

    public function submittedBy()
    {
        return $this->belongsTo(Employee::class, 'submitted_by', 'id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function offices()
    {
        return $this->belongsToMany(Office::class, 'office_revenue_offices', 'office_revenue_id', 'office_id')->withPivot('value');
    }
}
