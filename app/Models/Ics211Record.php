<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ics211Record extends Model
{
    protected $fillable = [
        'uuid',
        'rul_id',
        'name',
        'order_number',
        'start_date',
        'start_time',
        'checkin_location',
        'remarks',
        'status',
    ];

    public function rul()
    {
        return $this->belongsTo(Rul::class);
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
