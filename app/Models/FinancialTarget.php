<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialTarget extends Model
{
    protected $fillable = [
        'year',
        'department_id',
        'target_amount',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
