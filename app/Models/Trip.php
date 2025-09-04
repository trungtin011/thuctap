<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    protected $table = 'trips_passengers';
    protected $fillable = ['route_id', 'trips', 'passengers', 'record_date'];
}
