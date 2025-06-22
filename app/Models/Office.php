<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    protected $fillable = ['name'];


    public function officeRevenues()
    {
        return $this->belongsToMany(OfficeRevenue::class, 'office_revenue_offices', 'office_id', 'office_revenue_id');
    }
}
