<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = [
        'uuid',
        'rul_id',
        'certificate_name',
        'file_path',
    ];

    public function rul()
    {
        return $this->belongsTo(Rul::class);
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
