<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IcsLog extends Model
{
    protected $fillable = [
        'uuid',
        'ics211_record_id',
        'rul_id',
        'action',
        'description',
        'old_values',
        'new_values',
    ];

    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
    ];

    public function ics211Record()
    {
        return $this->belongsTo(Ics211Record::class);
    }

    public function rul()
    {
        return $this->belongsTo(Rul::class);
    }
}
