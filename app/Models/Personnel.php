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
        'contact_number',
        'serial_number',
        'department',
        'fcm_token',
        'token',
    ];


    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
