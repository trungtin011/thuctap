<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeRevenue extends Model
{
    protected $fillable = ['office_id', 'cash', 'bank_transfer', 'expense', 'total', 'record_date'];

    // Thêm quan hệ với Office
    public function office()
    {
        return $this->belongsTo(\App\Models\Office::class);
    }
}
