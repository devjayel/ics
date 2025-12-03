<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Personnel extends Model
{   
    use HasApiTokens;

    protected $hidden = [
        'id',
    ];
    protected $fillable = [
        'uuid',
        'name',
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


    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
