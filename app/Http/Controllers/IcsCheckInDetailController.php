<?php

namespace App\Http\Controllers;

use App\Models\CheckInDetails;
use App\Models\Ics211Record;
use App\Models\IcsLog;
use App\Models\Personnel;
use App\Services\PusherChannelServices;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class IcsCheckInDetailController extends Controller
{
    protected $pusherService;

    public function __construct(PusherChannelServices $pusherService)
    {
        $this->pusherService = $pusherService;
    }

    private function logAction(Ics211Record $ics211Record, string $action, string $description, ?array $oldValues = null, ?array $newValues = null, ?int $rulId = null)
    {
        IcsLog::create([
            'uuid' => Str::uuid(),
            'ics211_record_id' => $ics211Record->id,
            'rul_id' => $rulId ?? auth()->id(),
            'action' => $action,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }

    /**
     * Display all check-in details for a specific ICS record
     */
    public function index($icsUuid)
    {
        $ics211Record = Ics211Record::where('uuid', $icsUuid)->first();
        
        if (!$ics211Record) {
            return response()->json([
                'success' => false,
                'message' => 'ICS 211 record not found',
            ], 404);
        }

        $checkInDetails = $ics211Record->checkInDetails()->with('personnel')->get();

        return response()->json([
            'success' => true,
            'data' => $checkInDetails,
        ]);
    }

    /**
     * Display a specific check-in detail
     */
    public function show($uuid)
    {
        $checkInDetail = CheckInDetails::where('uuid', $uuid)->with('personnel')->first();

        if (!$checkInDetail) {
            return response()->json([
                'success' => false,
                'message' => 'Check-in detail not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $checkInDetail,
        ]);
    }

    /**
     * Store a new check-in detail for an ICS record
     */
    public function store(Request $request, $icsUuid)
    {
        $ics211Record = Ics211Record::where('uuid', $icsUuid)->first();

        if (!$ics211Record) {
            return response()->json([
                'success' => false,
                'message' => 'ICS 211 record not found',
            ], 404);
        }

        $validated = $request->validate([
            'personnel_id' => 'nullable|exists:personnels,id',
            'order_request_number' => 'required|string',
            'checkin_date' => 'required|date',
            'checkin_time' => 'required|date_format:H:i',
            'kind' => 'required|string',
            'category' => 'required|string',
            'type' => 'required|string',
            'resource_identifier' => 'required|string',
            'name_of_leader' => 'required|string',
            'contact_information' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'department' => 'required|string',
            'departure_point_of_origin' => 'required|string',
            'departure_date' => 'required|date',
            'departure_time' => 'required|date_format:H:i',
            'departure_method_of_travel' => 'required|string',
            'with_manifest' => 'nullable|boolean',
            'incident_assignment' => 'nullable|string',
            'other_qualifications' => 'nullable|string',
            'sent_resl' => 'nullable|boolean',
        ]);

        $rulId = $request->user()->id;

        $checkInDetail = CheckInDetails::create([
            'uuid' => Str::uuid(),
            'ics211_record_id' => $ics211Record->id,
            'personnel_id' => $validated['personnel_id'] ?? null,
            'order_request_number' => $validated['order_request_number'],
            'checkin_date' => $validated['checkin_date'],
            'checkin_time' => $validated['checkin_time'],
            'kind' => $validated['kind'],
            'category' => $validated['category'],
            'type' => $validated['type'],
            'resource_identifier' => $validated['resource_identifier'],
            'name_of_leader' => $validated['name_of_leader'],
            'contact_information' => $validated['contact_information'],
            'quantity' => $validated['quantity'],
            'department' => $validated['department'],
            'departure_point_of_origin' => $validated['departure_point_of_origin'],
            'departure_date' => $validated['departure_date'],
            'departure_time' => $validated['departure_time'],
            'departure_method_of_travel' => $validated['departure_method_of_travel'],
            'with_manifest' => $validated['with_manifest'] ?? false,
            'incident_assignment' => $validated['incident_assignment'] ?? null,
            'other_qualifications' => $validated['other_qualifications'] ?? null,
            'sent_resl' => $validated['sent_resl'] ?? false,
        ]);

        // Update personnel status to standby if personnel_id exists
        if (!empty($validated['personnel_id'])) {
            Personnel::where('id', $validated['personnel_id'])->update(['status' => 'standby']);

            // Log personnel addition
            $personnel = Personnel::find($validated['personnel_id']);
            $this->logAction(
                $ics211Record,
                'personnel_added',
                'Personnel ' . $personnel->name . ' (' . $personnel->serial_number . ') added to ICS',
                null,
                ['personnel_id' => $validated['personnel_id'], 'personnel_name' => $personnel->name],
                $rulId
            );
        }

        $checkInDetail->load('personnel');

        return response()->json([
            'success' => true,
            'message' => 'Check-in detail created successfully',
            'data' => $checkInDetail,
        ], 201);
    }

    /**
     * Update a specific check-in detail
     */
    public function update(Request $request, $uuid)
    {
        $checkInDetail = CheckInDetails::where('uuid', $uuid)->first();

        if (!$checkInDetail) {
            return response()->json([
                'success' => false,
                'message' => 'Check-in detail not found',
            ], 404);
        }

        $validated = $request->validate([
            'personnel_id' => 'nullable|exists:personnels,id',
            'order_request_number' => 'required|string',
            'checkin_date' => 'required|date',
            'checkin_time' => 'required|date_format:H:i',
            'kind' => 'required|string',
            'category' => 'required|string',
            'type' => 'required|string',
            'resource_identifier' => 'required|string',
            'name_of_leader' => 'required|string',
            'contact_information' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'department' => 'required|string',
            'departure_point_of_origin' => 'required|string',
            'departure_date' => 'required|date',
            'departure_time' => 'required|date_format:H:i',
            'departure_method_of_travel' => 'required|string',
            'with_manifest' => 'nullable|boolean',
            'incident_assignment' => 'nullable|string',
            'other_qualifications' => 'nullable|string',
            'sent_resl' => 'nullable|boolean',
        ]);

        $rulId = $request->user()->id;
        $ics211Record = $checkInDetail->ics211Record;
        
        $oldPersonnelId = $checkInDetail->personnel_id;
        $newPersonnelId = $validated['personnel_id'] ?? null;

        $checkInDetail->update([
            'personnel_id' => $newPersonnelId,
            'order_request_number' => $validated['order_request_number'],
            'checkin_date' => $validated['checkin_date'],
            'checkin_time' => $validated['checkin_time'],
            'kind' => $validated['kind'],
            'category' => $validated['category'],
            'type' => $validated['type'],
            'resource_identifier' => $validated['resource_identifier'],
            'name_of_leader' => $validated['name_of_leader'],
            'contact_information' => $validated['contact_information'],
            'quantity' => $validated['quantity'],
            'department' => $validated['department'],
            'departure_point_of_origin' => $validated['departure_point_of_origin'],
            'departure_date' => $validated['departure_date'],
            'departure_time' => $validated['departure_time'],
            'departure_method_of_travel' => $validated['departure_method_of_travel'],
            'with_manifest' => $validated['with_manifest'] ?? false,
            'incident_assignment' => $validated['incident_assignment'] ?? null,
            'other_qualifications' => $validated['other_qualifications'] ?? null,
            'sent_resl' => $validated['sent_resl'] ?? false,
        ]);

        // Handle personnel status changes
        if ($oldPersonnelId != $newPersonnelId) {
            // Reset old personnel status to available if it was changed
            if ($oldPersonnelId) {
                Personnel::where('id', $oldPersonnelId)->update(['status' => 'available']);
                
                // Log personnel removal
                $oldPersonnel = Personnel::find($oldPersonnelId);
                if ($oldPersonnel) {
                    $this->logAction(
                        $ics211Record,
                        'personnel_removed',
                        'Personnel ' . $oldPersonnel->name . ' (' . $oldPersonnel->serial_number . ') removed from ICS',
                        ['personnel_id' => $oldPersonnelId, 'personnel_name' => $oldPersonnel->name],
                        null,
                        $rulId
                    );
                }
            }

            // Set new personnel status to standby if personnel_id exists
            if ($newPersonnelId) {
                Personnel::where('id', $newPersonnelId)->update(['status' => 'standby']);
                
                // Log personnel addition
                $newPersonnel = Personnel::find($newPersonnelId);
                if ($newPersonnel) {
                    $this->logAction(
                        $ics211Record,
                        'personnel_added',
                        'Personnel ' . $newPersonnel->name . ' (' . $newPersonnel->serial_number . ') added to ICS',
                        null,
                        ['personnel_id' => $newPersonnelId, 'personnel_name' => $newPersonnel->name],
                        $rulId
                    );
                }
            }
        }

        $checkInDetail->load('personnel');

        return response()->json([
            'success' => true,
            'message' => 'Check-in detail updated successfully',
            'data' => $checkInDetail,
        ]);
    }

    /**
     * Delete a specific check-in detail
     */
    public function destroy(Request $request, $uuid)
    {
        $checkInDetail = CheckInDetails::where('uuid', $uuid)->first();

        if (!$checkInDetail) {
            return response()->json([
                'success' => false,
                'message' => 'Check-in detail not found',
            ], 404);
        }

        $rulId = $request->user()->id;
        $ics211Record = $checkInDetail->ics211Record;

        // Reset personnel status to available when removed from ICS
        if ($checkInDetail->personnel_id) {
            Personnel::where('id', $checkInDetail->personnel_id)->update(['status' => 'available']);
            
            // Log personnel removal
            $personnel = Personnel::find($checkInDetail->personnel_id);
            if ($personnel) {
                $this->logAction(
                    $ics211Record,
                    'personnel_removed',
                    'Personnel ' . $personnel->name . ' (' . $personnel->serial_number . ') removed from ICS',
                    ['personnel_id' => $checkInDetail->personnel_id, 'personnel_name' => $personnel->name],
                    null,
                    $rulId
                );
            }
        }

        $checkInDetail->delete();

        return response()->json([
            'success' => true,
            'message' => 'Check-in detail deleted successfully',
        ]);
    }
}
