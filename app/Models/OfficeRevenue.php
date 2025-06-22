<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeRevenue extends Model
{
    protected $fillable = ['cash', 'bank_transfer', 'expense', 'total', 'record_date'];

    // Thêm quan hệ với Office
    public function offices()
    {
        return $this->belongsToMany(Office::class, 'office_revenue_offices')
            ->withPivot('value'); // Chỉ định cột value từ bảng pivot
    }
}
