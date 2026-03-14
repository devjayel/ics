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
        'order_request_number',
        'checkin_location',
        'start_date',
        'start_time',
        'remarks',
        'remarks_image_attachment',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
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
