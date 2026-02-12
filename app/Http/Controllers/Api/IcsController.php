<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIcs211RecordRequest;
use App\Http\Requests\UpdateIcs211RecordRequest;
use App\Http\Resources\Ics211RecordResource;
use App\Models\CheckInDetails;
use App\Models\Ics211Record;
use App\Models\IcsLog;
use App\Models\Personnel;
use App\Services\PusherChannelServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class IcsController extends Controller
{
    protected $pusherService;

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

    public function __construct(PusherChannelServices $pusherService)
    {
        $this->pusherService = $pusherService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $records = Ics211Record::with(['operators.certificates', 'checkInDetails.personnel'])->get();

        return response()->json([
            'success' => true,
            'data' => Ics211RecordResource::collection($records),
        ]);
    }

    /**
     * Search ICS records with filters
     */
    public function search(Request $request)
    {
        $query = Ics211Record::with(['operators.certificates', 'checkInDetails.personnel']);

        // Filter by status
        if ($request->has('status') && ! empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Filter by name (partial match)
        if ($request->has('name') && ! empty($request->name)) {
            $query->where('name', 'like', '%'.$request->name.'%');
        }

        // Filter by start_date
        if ($request->has('start_date') && ! empty($request->start_date)) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }

        // Filter by end_date
        if ($request->has('end_date') && ! empty($request->end_date)) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }

        // Filter by start_location (partial match)
        if ($request->has('start_location') && ! empty($request->start_location)) {
            $query->where('start_location', 'like', '%'.$request->start_location.'%');
        }

        // Filter by end_location (partial match)
        if ($request->has('end_location') && ! empty($request->end_location)) {
            $query->where('end_location', 'like', '%'.$request->end_location.'%');
        }

        $records = $query->get();

        return response()->json([
            'success' => true,
            'data' => Ics211RecordResource::collection($records),
            'count' => $records->count(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIcs211RecordRequest $request)
    {
        $validated = $request->validated();
        $rulId = $request->user()->id;

        \Log::info('ICS 211 Store Payload:', $validated);

        // Handle image upload
        $remarksImagePath = null;
        if ($request->hasFile('remarks_image_attachment')) {
            $remarksImagePath = $request->file('remarks_image_attachment')->store('ics/remarks', 'public');
        }

        // Create ICS 211 Record
        $ics211Record = Ics211Record::create([
            'uuid' => Str::uuid(),
            'token' => Str::random(8),
            'name' => $validated['name'],
            'type' => $validated['type'],
            'start_date' => $validated['start_date'],
            'start_time' => $validated['start_time'],
            'end_date' => $validated['end_date'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'checkin_location' => $validated['checkin_location'],
            'start_coordinates' => $validated['start_coordinates'] ?? null,
            'end_coordinates' => $validated['end_coordinates'] ?? null,
            'start_location' => $validated['start_location'] ?? null,
            'end_location' => $validated['end_location'] ?? null,
            'region' => $validated['region'] ?? null,
            'remarks' => $validated['remarks'] ?? null,
            'remarks_image_attachment' => $remarksImagePath,
            'status' => $validated['status'] ?? 'pending',
        ]);

        // Attach current user as operator (creator is automatically an owner)
        $operatorIds = [$request->user()->id];

        $ics211Record->operators()->attach($operatorIds);

        // Log ICS creation
        $this->logAction(
            $ics211Record,
            'created',
            'ICS 211 record created by '.$request->user()->name,
            null,
            $ics211Record->toArray(),
            $rulId
        );

        // Create Check-in Details if provided
        if (! empty($validated['check_in_details'])) {
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


                // Update personnel status to assigned if personnel_id exists
                if (! empty($checkInDetail['personnel_id'])) {
                    Personnel::where('id', $checkInDetail['personnel_id'])->update(['status' => 'standby']);

                    // Log personnel addition
                    $personnel = Personnel::find($checkInDetail['personnel_id']);
                    $this->logAction(
                        $ics211Record,
                        'personnel_added',
                        'Personnel '.$personnel->name.' ('.$personnel->serial_number.') added to ICS',
                        null,
                        ['personnel_id' => $checkInDetail['personnel_id'], 'personnel_name' => $personnel->name],
                        $rulId
                    );

                    // Notify personnel via Pusher
                    //$this->pusherService->push("ics-{$personnel->uuid}", 'ics_task_updated', []);
                }
            }
        }

        // Load relationships
        $ics211Record->load(['operators.certificates', 'checkInDetails.personnel']);

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
        if (! $ics211Record) {
            return response()->json([
                'success' => false,
                'message' => 'ICS 211 record not found',
            ], 404);
        }
        $ics211Record->load(['operators.certificates', 'checkInDetails.personnel']);

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
        
        if (! $ics211Record) {
            return response()->json([
                'success' => false,
                'message' => 'ICS 211 record not found',
            ], 404);
        }
        
        $validated = $request->validated();
        $rulId = $request->user()->id;

        // Handle image upload
        $remarksImagePath = $ics211Record->remarks_image_attachment;
        if ($request->hasFile('remarks_image_attachment')) {
            // Delete old image if it exists
            if ($remarksImagePath && \Storage::disk('public')->exists($remarksImagePath)) {
                \Storage::disk('public')->delete($remarksImagePath);
            }
            $remarksImagePath = $request->file('remarks_image_attachment')->store('ics/remarks', 'public');
        }

        // Update ICS 211 Record
        $oldValues = $ics211Record->toArray();

        $ics211Record->update(array_filter([
            'name' => $validated['name'] ?? $ics211Record->name,
            'type' => $validated['type'] ?? $ics211Record->type,
            'start_date' => $validated['start_date'] ?? $ics211Record->start_date,
            'start_time' => $validated['start_time'] ?? $ics211Record->start_time,
            'end_date' => $validated['end_date'] ?? $ics211Record->end_date,
            'end_time' => $validated['end_time'] ?? $ics211Record->end_time,
            'checkin_location' => $validated['checkin_location'] ?? $ics211Record->checkin_location,
            'start_coordinates' => $validated['start_coordinates'] ?? $ics211Record->start_coordinates,
            'end_coordinates' => $validated['end_coordinates'] ?? $ics211Record->end_coordinates,
            'start_location' => $validated['start_location'] ?? $ics211Record->start_location,
            'end_location' => $validated['end_location'] ?? $ics211Record->end_location,
            'region' => $validated['region'] ?? $ics211Record->region,
            'remarks' => $validated['remarks'] ?? $ics211Record->remarks,
            'remarks_image_attachment' => $remarksImagePath,
            'status' => $validated['status'] ?? $ics211Record->status,
        ]));

        // Log ICS update
        $newValues = $ics211Record->fresh()->toArray();
        $changes = array_diff_assoc($newValues, $oldValues);
        if (! empty($changes)) {
            $this->logAction(
                $ics211Record,
                'updated',
                'ICS 211 record updated',
                $oldValues,
                $newValues,
                $rulId
            );
        }

        // Update Check-in Details if provided
        if (isset($validated['check_in_details'])) {
            // Get existing check-in detail UUIDs
            $existingUuids = collect($validated['check_in_details'])
                ->pluck('uuid')
                ->filter()
                ->toArray();

            // Get personnel IDs from check-in details that will be deleted
            $deletedCheckInDetails = $ics211Record->checkInDetails()
                ->whereNotIn('uuid', $existingUuids)
                ->get();

            foreach ($deletedCheckInDetails as $deletedDetail) {
                if ($deletedDetail->personnel_id) {
                    // Reset personnel status to available when removed from ICS
                    Personnel::where('id', $deletedDetail->personnel_id)->update(['status' => 'available']);
                    
                    // Log personnel removal
                    $personnel = Personnel::find($deletedDetail->personnel_id);
                    if ($personnel) {
                        $this->logAction(
                            $ics211Record,
                            'personnel_removed',
                            'Personnel ' . $personnel->name . ' (' . $personnel->serial_number . ') removed from ICS',
                            ['personnel_id' => $deletedDetail->personnel_id, 'personnel_name' => $personnel->name],
                            null,
                            $rulId
                        );
                    }
                }
            }

            // Delete check-in details not in the update
            $ics211Record->checkInDetails()
                ->whereNotIn('uuid', $existingUuids)
                ->delete();

            foreach ($validated['check_in_details'] as $checkInDetail) {
                if (isset($checkInDetail['uuid'])) {
                    // Get the existing check-in detail to compare personnel_id
                    $existingDetail = CheckInDetails::where('uuid', $checkInDetail['uuid'])->first();
                    $oldPersonnelId = $existingDetail ? $existingDetail->personnel_id : null;
                    $newPersonnelId = $checkInDetail['personnel_id'] ?? null;

                    // Update existing
                    CheckInDetails::where('uuid', $checkInDetail['uuid'])->update([
                        'order_request_number' => $checkInDetail['order_request_number'],
                        'personnel_id' => $newPersonnelId,
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

                    // Handle personnel status changes
                    if ($oldPersonnelId != $newPersonnelId) {
                        // Reset old personnel status to available if it was changed
                        if ($oldPersonnelId) {
                            Personnel::where('id', $oldPersonnelId)->update(['status' => 'available']);
                            
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

                                // Notify personnel via Pusher
                                $this->pusherService->push("ics-{$newPersonnel->uuid}", 'ics_task_updated', []);
                            }
                        }
                    }
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

                    // Update personnel status to standby if personnel_id exists (same as store)
                    if (! empty($checkInDetail['personnel_id'])) {
                        Personnel::where('id', $checkInDetail['personnel_id'])->update(['status' => 'standby']);

                        // Log personnel addition
                        $personnel = Personnel::find($checkInDetail['personnel_id']);
                        $this->logAction(
                            $ics211Record,
                            'personnel_added',
                            'Personnel '.$personnel->name.' ('.$personnel->serial_number.') added to ICS',
                            null,
                            ['personnel_id' => $checkInDetail['personnel_id'], 'personnel_name' => $personnel->name],
                            $rulId
                        );

                        // Notify personnel via Pusher
                        //$this->pusherService->push("ics-{$personnel->uuid}", 'ics_task_updated', []);
                    }
                }
            }
        }

        // Load relationships
        $ics211Record->load(['operators.certificates', 'checkInDetails.personnel']);

        return response()->json([
            'success' => true,
            'message' => 'ICS 211 record updated successfully',
            'data' => new Ics211RecordResource($ics211Record),
        ]);
    }

    public function updateStatus(Request $request,$ics211Record, $status)
    {
        $ics211Record = Ics211Record::where('uuid', $ics211Record)->first();

        if (! $ics211Record) {
            return response()->json([
                'success' => false,
                'message' => 'ICS 211 record not found',
            ], 404);
        }

        $rulId = $request->user()->id;


        $validStatuses = ['pending', 'ongoing', 'completed'];
        if (! in_array($status, haystack: $validStatuses)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status provided',
            ], 400);
        }

        $validated = request()->validate([
            'remarks' => 'nullable|string',
        ]);

        $ics211Record->remarks = $validated['remarks'] ?? $ics211Record->remarks;
        $ics211Record->status = $status;
        $ics211Record->save();

        // Log status update
        $this->logAction(
            $ics211Record,
            'status_changed',
            'ICS 211 record status changed to '.$status,
            ['status' => 'previous_status'],
            ['status' => $status],
            $rulId
        );

        // If status is completed, update all associated personnel status to available
        if ($status === 'completed') {
            $personnelIds = $ics211Record->checkInDetails()->whereNotNull('personnel_id')->pluck('personnel_id')->toArray();
            if (! empty($personnelIds)) {
                Personnel::whereIn('id', $personnelIds)->update(['status' => 'available']);
            }
        }

        $ics211Record->load(['operators.certificates', 'checkInDetails.personnel']);

        return response()->json([
            'success' => true,
            'message' => 'ICS 211 record status updated successfully',
            'data' => new Ics211RecordResource($ics211Record),
        ]);
    }

    public function updateCheckinDetailStatus($uuid)
    {
        $checkInDetail = CheckInDetails::where('uuid', $uuid)->first();
        if (! $checkInDetail) {
            return response()->json([
                'success' => false,
                'message' => 'Check-in detail not found',
            ], 404);
        }

        $validated = request()->validate([
            'status' => 'required|string|in:available,staging,assigned,active,demobalized,out_of_service,standby',
        ]);

        $checkInDetail->status = $validated['status'];
        $checkInDetail->save();

        if ($checkInDetail->personnel_id) {
            // Update personnel status to match check-in detail status
            Personnel::where('id', $checkInDetail->personnel_id)->update(['status' => $validated['status']]);
            // Notify personnel via Pusher
            $personnel = Personnel::find($checkInDetail->personnel_id);
            $this->pusherService->push("ics-{$personnel->uuid}", 'ics_task_updated', []);
        }

        $checkInDetail->load(['personnel']);

        return response()->json([
            'success' => true,
            'message' => 'Check-in detail status updated successfully',
            'data' => $checkInDetail,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($ics211Record)
    {
        $ics211Record = Ics211Record::where('uuid', $ics211Record)->first();
        if (! $ics211Record) {
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

    /**
     * Add current logged-in RUL as operator to ICS record via token
     */
    public function joinIcs(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
        ]);

        // Find ICS record by token
        $ics211Record = Ics211Record::where('token', $validated['token'])->first();

        if (! $ics211Record) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid invitation code. ICS 211 record not found.',
            ], 404);
        }

        $currentUser = $request->user();

        // Check if user is already an operator
        if ($ics211Record->operators()->where('resident_unit_leaders.id', $currentUser->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You are already an operator of this ICS record',
            ], 400);
        }

        // Attach current user as operator
        $ics211Record->operators()->attach($currentUser->id);

        // Log operator addition
        $this->logAction(
            $ics211Record,
            'operator_added',
            "RUL {$currentUser->name} joined {$ics211Record->name} operators",
            null,
            ['operator_name' => $currentUser->name],
            $currentUser->id
        );

        // Load relationships
        $ics211Record->load(['operators.certificates', 'checkInDetails.personnel']);

        return response()->json([
            'success' => true,
            'message' => 'Successfully joined ICS 211 record as operator',
            'data' => new Ics211RecordResource($ics211Record),
        ], 200);
    }
}
