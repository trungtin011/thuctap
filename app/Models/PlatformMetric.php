<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformMetric extends Model
{
    protected $fillable = ['platform_id', 'name', 'unit', 'data_type'];

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }
}
