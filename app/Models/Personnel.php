<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Personnel extends Model
{   
    use HasApiTokens;
    protected $fillable = [
        'uuid',
        'name',
        'rul_id',
        'contact_number',
        'serial_number',
        'department',
        'fcm_token',
        'token',
        'status',
    ];

    public function tasks(){
        return $this->hasMany(CheckInDetails::class);
    }

    public function rul()
    {
        return $this->belongsTo(Rul::class);
    }


    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
