<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StorePersonnelRequest;
use App\Models\CheckInDetails;
use App\Models\Personnel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PersonnelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Personnel::with("rul")->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'serial_number' => 'required|string|max:100|unique:personnels,serial_number',
            'department' => 'required|string|max:100',
        ], [
            'name.required' => 'Name is required.',
            'name.max' => 'Name must not exceed 255 characters.',
            'contact_number.required' => 'Contact number is required.',
            'contact_number.max' => 'Contact number must not exceed 20 characters.',
            'serial_number.required' => 'Serial number is required.',
            'serial_number.max' => 'Serial number must not exceed 100 characters.',
            'serial_number.unique' => 'This serial number is already registered.',
            'department.required' => 'Department is required.',
            'department.max' => 'Department must not exceed 100 characters.',
        ]);

        $validated['rul_id'] = $request->user()->id;

        $personnel = Personnel::create([
            'uuid' => Str::uuid(),
            'rul_id' => $validated['rul_id'],
            'name' => $validated['name'],
            'contact_number' => $validated['contact_number'],
            'serial_number' => $validated['serial_number'],
            'department' => $validated['department'],
        ]);

        return response()->json([
            'success' => true,
            'data' => $personnel->with("rul")->first(),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $personnel = Personnel::where('uuid', $id)->with('rul')->first();

        if (!$personnel) {
            return response()->json([
                'success' => false,
                'message' => 'Personnel not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $personnel,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $personnel = Personnel::where('uuid', $id)->first();

        if (!$personnel) {
            return response()->json([
                'success' => false,
                'message' => 'Personnel not found',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'contact_number' => 'sometimes|required|string|max:20',
            'serial_number' => 'sometimes|required|string|max:100|unique:personnels,serial_number,' . $personnel->id,
            'department' => 'sometimes|required|string|max:100',
        ], [
            'name.required' => 'Name is required.',
            'name.max' => 'Name must not exceed 255 characters.',
            'contact_number.required' => 'Contact number is required.',
            'contact_number.max' => 'Contact number must not exceed 20 characters.',
            'serial_number.required' => 'Serial number is required.',
            'serial_number.max' => 'Serial number must not exceed 100 characters.',
            'serial_number.unique' => 'This serial number is already registered.',
            'department.required' => 'Department is required.',
            'department.max' => 'Department must not exceed 100 characters.',
        ]);

        $personnel->update($validated);

        return response()->json([
            'success' => true,
            'data' => $personnel->with("rul")->first(),
            'message' => 'Personnel updated successfully',
        ]);
    }

    public function updateStatus($uuid, Request $request)
    {
        $personnel = Personnel::where('uuid', $uuid)->first();

        if (!$personnel) {
            return response()->json([
                'success' => false,
                'message' => 'Personnel not found',
            ], 404);
        }

        $validated = $request->validate([
            'checkin_details_id' => 'required|exists:check_in_details,id',
            'status' => 'required|string|in:available,staging,assigned,active,demobilized,out_of_service,standby',
        ]);
        
        $personnel->status = $validated['status'];
        $personnel->save();

        return response()->json([
            'success' => true,
            'data' => $personnel->with("rul")->first(),
            'message' => 'Personnel status updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $personnel = Personnel::where('uuid', $id)->first();

        if (!$personnel) {
            return response()->json([
                'success' => false,
                'message' => 'Personnel not found',
            ], 404);
        }

        $personnel->delete();

        return response()->json([
            'success' => true,
            'message' => 'Personnel deleted successfully',
        ]);
    }
}
