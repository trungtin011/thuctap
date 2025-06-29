<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripsPassenger extends Model
{
    protected $fillable = ['route_id', 'trips', 'passengers', 'record_date', 'created_at', 'updated_at'];
}
