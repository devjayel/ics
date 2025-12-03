<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StorePersonnelRequest;
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
            'data' => Personnel::all(),
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
        ]);

        $personnel = Personnel::create([
            'uuid' => Str::uuid(),
            'name' => $validated['name'],
            'contact_number' => $validated['contact_number'],
            'serial_number' => $validated['serial_number'],
            'department' => $validated['department'],
        ]);

        return response()->json([
            'success' => true,
            'data' => $personnel,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $personnel = Personnel::where('uuid', $id)->first();

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
            'serial_number' => 'sometimes|required|string|max:100|unique:personnels,serial_number,'.$personnel->id,
            'department' => 'sometimes|required|string|max:100',
        ]);

        $personnel->update($validated);

        return response()->json([
            'success' => true,
            'data' => $personnel,
            'message' => 'Personnel updated successfully',
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
