<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ics211Record extends Model
{
    protected $fillable = [
        'uuid',
        'token',
        'name',
        'type',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'checkin_location',
        'start_coordinates',
        'end_coordinates',
        'start_location',
        'end_location',
        'region',
        'remarks',
        'remarks_image_attachment',
        'status',
    ];

    public function operators()
    {
        return $this->belongsToMany(Rul::class, 'ics_operators', 'ics_id', 'rul_id');
    }

    public function checkInDetails()
    {
        return $this->hasMany(CheckInDetails::class);
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
