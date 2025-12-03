<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckInDetails extends Model
{
    protected $fillable = [
        'uuid',
        'personnel_id',
        'ics211_record_id',
        'order_request_number',
        'checkin_date',
        'checkin_time',
        'kind',
        'category',
        'type',
        'resource_identifier',
        'name_of_leader',
        'contact_information',
        'quantity',
        'department',
        'departure_point_of_origin',
        'departure_date',
        'departure_time',
        'departure_method_of_travel',
        'with_manifest',
        'incident_assignment',
        'other_qualifications',
        'sent_resl',
        'status',
    ];

    protected $casts = [
        'checkin_date' => 'date',
        'departure_date' => 'date',
        'with_manifest' => 'boolean',
        'sent_resl' => 'boolean',
    ];

    public function personnel(){
        return $this->belongsTo(Personnel::class);
    }

    public function ics211Record()
    {
        return $this->belongsTo(Ics211Record::class);
    }

    public function histories()
    {
        return $this->hasMany(CheckInDetailHistories::class);
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
