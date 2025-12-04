<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIcs211RecordRequest;
use App\Http\Requests\UpdateIcs211RecordRequest;
use App\Http\Resources\Ics211RecordResource;
use App\Http\Resources\PersonnelIcsResource;
use App\Models\Ics211Record;
use App\Models\CheckInDetails;
use App\Models\Personnel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PersonnelIcsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $personnel = request()->user();
        $personnelId = $personnel->id;
        $records = CheckInDetails::with(['ics211Record.rul.certificates', 'personnel'])
            ->where('personnel_id', $personnelId)
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => PersonnelIcsResource::collection($records),
        ]);
    }

    public function latest()
    {
        $personnel = request()->user();
        $personnelId = $personnel->id;
        $record = CheckInDetails::with(['ics211Record.rul.certificates', 'personnel'])
            ->where('personnel_id', $personnelId)
            ->where('status', 'pending')
            ->latest()
            ->first();
        
        if (!$record) {{
            return response()->json([
                'success' => false,
                'message' => 'No pending ICS 211 record found',
                'data' => [],
            ], 404);
        }}
        
        return response()->json([
            'success' => true,
            'data' => new PersonnelIcsResource($record),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
         $records = CheckInDetails::with(['ics211Record.rul.certificates', 'personnel'])
            ->where('personnel_id', request()->user()->id)
            ->where('uuid', $uuid)
            ->first();
        if (!$records) {
            return response()->json([
                'success' => false,
                'message' => 'ICS 211 record not found',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => new PersonnelIcsResource($records),
        ]);
    }



    public function updateCheckinDetailStatus($uuid){        
        $checkInDetail = CheckInDetails::where('uuid', $uuid)->first();
        if (!$checkInDetail) {
            return response()->json([
                'success' => false,
                'message' => 'Check-in detail not found',
            ], 404);
        }

        $validated = request()->validate([
            'status' => 'required|string|in:available,staging,assigned,active,demobilized,out_of_service,standby',
        ]);

        $checkInDetail->status = $validated['status'];
        $checkInDetail->save();

        if ($checkInDetail->personnel_id) {
            // Update personnel status to match check-in detail status
            Personnel::where('id', $checkInDetail->personnel_id)->update(['status' => $validated['status']]);
        }

        $checkInDetail->load(['personnel']);

        return response()->json([
            'success' => true,
            'message' => 'Check-in detail status updated successfully',
            'data' => $checkInDetail,
        ]);
    }

}
