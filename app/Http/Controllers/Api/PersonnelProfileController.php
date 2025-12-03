<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Personnel;
use Illuminate\Http\Request;

class PersonnelProfileController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show()
    {
        $personnel = request()->user();
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
        $personnel = request()->user();

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'contact_number' => 'sometimes|required|string|max:255',
            'department' => 'sometimes|required|string|max:255',
        ]);

        $personnel->update(array_filter([
            'name' => $validated['name'] ?? null,
            'contact_number' => $validated['contact_number'] ?? null,
            'department' => $validated['department'] ?? null,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $personnel,
        ]);
    }
}
