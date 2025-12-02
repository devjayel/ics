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
        'checkin_date',
        'checkin_time',
        'kind',
        'type',
        'resource_identifier',
        'department',
        'departure_point_of_origin',
        'departure_date',
        'departure_time',
        'departure_method_of_travel',
        'other_qualifications',
        'remarks',
    ];

    public function rul()
    {
        return $this->belongsTo(Rul::class);
    }

    public function personnelCheckinDetails()
    {
        return $this->hasMany(PersonnelCheckinDetailIcs211Record::class);
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
