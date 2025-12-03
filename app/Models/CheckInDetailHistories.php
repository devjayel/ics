<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckInDetailHistories extends Model
{
    protected $fillable = [
        'uuid',
        'ics211_record_id',
        'order_request_number',
        'remarks',
        'description',
        'status',
    ];

    public function ics211Record()
    {
        return $this->belongsTo(Ics211Record::class);
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
