<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIcs211RecordRequest;
use App\Http\Requests\UpdateIcs211RecordRequest;
use App\Http\Resources\Ics211RecordResource;
use App\Models\Ics211Record;
use App\Models\CheckInDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class IcsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $records = Ics211Record::with(['rul', 'checkInDetails.personnel'])->get();
        
        return response()->json([
            'success' => true,
            'data' => Ics211RecordResource::collection($records),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIcs211RecordRequest $request)
    {
        $rul_id = $request->user()->id;

        $validated = $request->validated();


        // Create ICS 211 Record
        $ics211Record = Ics211Record::create([
            'uuid' => Str::uuid(),
            'rul_id' => $rul_id,
            'name' => $validated['name'],
            'start_date' => $validated['start_date'],
            'start_time' => $validated['start_time'],
            'checkin_location' => $validated['checkin_location'],
            'remarks' => $validated['remarks'] ?? null,
        ]);

        // Create Check-in Details if provided
        if (!empty($validated['check_in_details'])) {
            foreach ($validated['check_in_details'] as $checkInDetail) {
                CheckInDetails::create([
                    'uuid' => Str::uuid(),
                    'ics211_record_id' => $ics211Record->id,
                    'personnel_id' => $checkInDetail['personnel_id'] ?? null,
                    'order_request_number' => $checkInDetail['order_request_number'],
                    'checkin_date' => $checkInDetail['checkin_date'],
                    'checkin_time' => $checkInDetail['checkin_time'],
                    'kind' => $checkInDetail['kind'],
                    'category' => $checkInDetail['category'],
                    'type' => $checkInDetail['type'],
                    'resource_identifier' => $checkInDetail['resource_identifier'],
                    'name_of_leader' => $checkInDetail['name_of_leader'],
                    'contact_information' => $checkInDetail['contact_information'],
                    'quantity' => $checkInDetail['quantity'],
                    'department' => $checkInDetail['department'],
                    'departure_point_of_origin' => $checkInDetail['departure_point_of_origin'],
                    'departure_date' => $checkInDetail['departure_date'],
                    'departure_time' => $checkInDetail['departure_time'],
                    'departure_method_of_travel' => $checkInDetail['departure_method_of_travel'],
                    'with_manifest' => $checkInDetail['with_manifest'] ?? false,
                    'incident_assignment' => $checkInDetail['incident_assignment'] ?? null,
                    'other_qualifications' => $checkInDetail['other_qualifications'] ?? null,
                    'sent_resl' => $checkInDetail['sent_resl'] ?? false,
                ]);
            }
        }

        // Load relationships
        $ics211Record->load(['rul', 'checkInDetails.personnel']);

        return response()->json([
            'success' => true,
            'message' => 'ICS 211 record created successfully',
            'data' => new Ics211RecordResource($ics211Record),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($ics211Record)
    {
        $ics211Record = Ics211Record::where('uuid', $ics211Record)->first();
        if (!$ics211Record) {
            return response()->json([
                'success' => false,
                'message' => 'ICS 211 record not found',
            ], 404);
        }
        $ics211Record->load(['rul', 'checkInDetails.personnel']);

        return response()->json([
            'success' => true,
            'data' => new Ics211RecordResource($ics211Record),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIcs211RecordRequest $request, $ics211Record)
    {
        $ics211Record = Ics211Record::where('uuid', $ics211Record)->first();
        if (!$ics211Record) {
            return response()->json([
                'success' => false,
                'message' => 'ICS 211 record not found',
            ], 404);
        }
        $validated = $request->validated();

        // Update ICS 211 Record
        $ics211Record->update(array_filter([
            'name' => $validated['name'] ?? $ics211Record->name,
            'start_date' => $validated['start_date'] ?? $ics211Record->start_date,
            'start_time' => $validated['start_time'] ?? $ics211Record->start_time,
            'checkin_location' => $validated['checkin_location'] ?? $ics211Record->checkin_location,
            'remarks' => $validated['remarks'] ?? $ics211Record->remarks,
        ]));

        // Update Check-in Details if provided
        if (isset($validated['check_in_details'])) {
            // Get existing check-in detail UUIDs
            $existingUuids = collect($validated['check_in_details'])
                ->pluck('uuid')
                ->filter()
                ->toArray();

            // Delete check-in details not in the update
            $ics211Record->checkInDetails()
                ->whereNotIn('uuid', $existingUuids)
                ->delete();

            foreach ($validated['check_in_details'] as $checkInDetail) {
                if (isset($checkInDetail['uuid'])) {
                    // Update existing
                    CheckInDetails::where('uuid', $checkInDetail['uuid'])->update([
                        'order_request_number' => $checkInDetail['order_request_number'],
                        'personnel_id' => $checkInDetail['personnel_id'] ?? null,
                        'checkin_date' => $checkInDetail['checkin_date'],
                        'checkin_time' => $checkInDetail['checkin_time'],
                        'kind' => $checkInDetail['kind'],
                        'category' => $checkInDetail['category'],
                        'type' => $checkInDetail['type'],
                        'resource_identifier' => $checkInDetail['resource_identifier'],
                        'name_of_leader' => $checkInDetail['name_of_leader'],
                        'contact_information' => $checkInDetail['contact_information'],
                        'quantity' => $checkInDetail['quantity'],
                        'department' => $checkInDetail['department'],
                        'departure_point_of_origin' => $checkInDetail['departure_point_of_origin'],
                        'departure_date' => $checkInDetail['departure_date'],
                        'departure_time' => $checkInDetail['departure_time'],
                        'departure_method_of_travel' => $checkInDetail['departure_method_of_travel'],
                        'with_manifest' => $checkInDetail['with_manifest'] ?? false,
                        'incident_assignment' => $checkInDetail['incident_assignment'] ?? null,
                        'other_qualifications' => $checkInDetail['other_qualifications'] ?? null,
                        'sent_resl' => $checkInDetail['sent_resl'] ?? false,
                    ]);
                } else {
                    // Create new
                    CheckInDetails::create([
                        'uuid' => Str::uuid(),
                        'ics211_record_id' => $ics211Record->id,
                        'personnel_id' => $checkInDetail['personnel_id'] ?? null,
                        'order_request_number' => $checkInDetail['order_request_number'],
                        'checkin_date' => $checkInDetail['checkin_date'],
                        'checkin_time' => $checkInDetail['checkin_time'],
                        'kind' => $checkInDetail['kind'],
                        'category' => $checkInDetail['category'],
                        'type' => $checkInDetail['type'],
                        'resource_identifier' => $checkInDetail['resource_identifier'],
                        'name_of_leader' => $checkInDetail['name_of_leader'],
                        'contact_information' => $checkInDetail['contact_information'],
                        'quantity' => $checkInDetail['quantity'],
                        'department' => $checkInDetail['department'],
                        'departure_point_of_origin' => $checkInDetail['departure_point_of_origin'],
                        'departure_date' => $checkInDetail['departure_date'],
                        'departure_time' => $checkInDetail['departure_time'],
                        'departure_method_of_travel' => $checkInDetail['departure_method_of_travel'],
                        'with_manifest' => $checkInDetail['with_manifest'] ?? false,
                        'incident_assignment' => $checkInDetail['incident_assignment'] ?? null,
                        'other_qualifications' => $checkInDetail['other_qualifications'] ?? null,
                        'sent_resl' => $checkInDetail['sent_resl'] ?? false,
                    ]);
                }
            }
        }

        // Load relationships
        $ics211Record->load(['rul', 'checkInDetails.personnel']);

        return response()->json([
            'success' => true,
            'message' => 'ICS 211 record updated successfully',
            'data' => new Ics211RecordResource($ics211Record),
        ]);
    }

    public function updateStatus($ics211Record, $status){
        $ics211Record = Ics211Record::where('uuid', $ics211Record)->first();
        if (!$ics211Record) {
            return response()->json([
                'success' => false,
                'message' => 'ICS 211 record not found',
            ], 404);
        }

        $validStatuses = ['active', 'inactive', 'closed'];
        if (!in_array($status, $validStatuses)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status provided',
            ], 400);
        }

        $ics211Record->status = $status;
        $ics211Record->save();
        $ics211Record->load(['rul', 'checkInDetails.personnel']);

        return response()->json([
            'success' => true,
            'message' => 'ICS 211 record status updated successfully',
            'data' => new Ics211RecordResource($ics211Record),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($ics211Record)
    {
        $ics211Record = Ics211Record::where('uuid', $ics211Record)->first();
        if (!$ics211Record) {
            return response()->json([
                'success' => false,
                'message' => 'ICS 211 record not found',
            ], 404);
        }
        $ics211Record->delete();

        return response()->json([
            'success' => true,
            'message' => 'ICS 211 record deleted successfully',
        ]);
    }
}
