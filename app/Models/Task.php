<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'uuid',
        'title',
        'description',
        'status',
    ];

    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
