<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ics211Record extends Model
{
    protected $fillable = [
        'uuid',
        'token',
        'order_request_number',
        'name',
        'type',
        'checkin_location',
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
