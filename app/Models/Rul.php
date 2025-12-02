<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Rul extends Model
{
    protected $table = 'resident_unit_leaders';

    protected $fillable = [
        'uuid',
        'name',
        'contact_number',
        'serial_number',
        'department',
        'fcm_token',
        'signature',
        'token',
    ];

    protected static function booted()
    {
        static::deleting(function ($rul) {
            // Delete signature file if exists
            if ($rul->signature) {
                Storage::disk('public')->delete($rul->signature);
            }

            // Delete all certificate files and records
            foreach ($rul->certificates as $certificate) {
                if ($certificate->file_path) {
                    Storage::disk('public')->delete($certificate->file_path);
                }
                $certificate->delete();
            }
        });
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function ics211Records()
    {
        return $this->hasMany(Ics211Record::class);
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
