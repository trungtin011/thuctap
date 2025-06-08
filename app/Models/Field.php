<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'required',
        'department_id', // Thêm cột department_id
    ];

    protected $casts = [
        'required' => 'boolean',
    ];
     public function department()
    {
        return $this->belongsTo(Department::class);
    }
}