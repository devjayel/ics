<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ics211Record;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Get all ICS form records with relations
        $icsRecords = Ics211Record::with([
            'rul:id,uuid,name,contact_number,serial_number,department,signature',
            'checkInDetails.personnel:id,uuid,name,contact_number,serial_number,department',
        ])
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($record) {
            return [
                'uuid' => $record->uuid,
                'name' => $record->name,
                'order_number' => $record->order_number ?? null,
                'start_date' => $record->start_date,
                'start_time' => $record->start_time,
                'checkin_location' => $record->checkin_location,
                'remarks' => $record->remarks,
                'status' => $record->status,
                'created_at' => $record->created_at,
                'updated_at' => $record->updated_at,
                'rul' => [
                    'uuid' => $record->rul->uuid,
                    'name' => $record->rul->name,
                    'contact_number' => $record->rul->contact_number,
                    'serial_number' => $record->rul->serial_number,
                    'department' => $record->rul->department,
                    'signature' => $record->rul->signature ? asset('storage/' . $record->rul->signature) : null,
                ],
                'check_in_details' => $record->checkInDetails->map(function ($detail) {
                    return [
                        'id' => $detail->id,
                        'category' => $detail->category,
                        'item_being_tracked' => $detail->item_being_tracked,
                        'ics_position' => $detail->ics_position,
                        'home_base' => $detail->home_base,
                        'departure_point' => $detail->departure_point,
                        'eta' => $detail->eta,
                        'etd' => $detail->etd,
                        'ata' => $detail->ata,
                        'atd' => $detail->atd,
                        'incident_location' => $detail->incident_location,
                        'destination' => $detail->destination,
                        'date' => $detail->date,
                        'method_of_travel' => $detail->method_of_travel,
                        'remarks' => $detail->remarks,
                        'status' => $detail->status,
                        'personnel' => $detail->personnel ? [
                            'uuid' => $detail->personnel->uuid,
                            'name' => $detail->personnel->name,
                            'contact_number' => $detail->personnel->contact_number,
                            'serial_number' => $detail->personnel->serial_number,
                            'department' => $detail->personnel->department,
                        ] : null,
                    ];
                }),
                'total_check_ins' => $record->checkInDetails->count(),
                'completed_check_ins' => $record->checkInDetails->where('status', 'completed')->count(),
                'pending_check_ins' => $record->checkInDetails->where('status', 'pending')->count(),
                'ongoing_check_ins' => $record->checkInDetails->where('status', 'ongoing')->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $icsRecords,
            'total' => $icsRecords->count(),
        ]);
    }
}
