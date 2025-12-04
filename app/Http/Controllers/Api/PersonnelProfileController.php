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
    public function update(Request $request)
    {
        $personnel = request()->user();

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'contact_number' => 'sometimes|required|string|max:255',
            'department' => 'sometimes|required|string|max:255',
        ], [
            'name.required' => 'Name is required.',
            'name.max' => 'Name must not exceed 255 characters.',
            'contact_number.required' => 'Contact number is required.',
            'contact_number.max' => 'Contact number must not exceed 255 characters.',
            'department.required' => 'Department is required.',
            'department.max' => 'Department must not exceed 255 characters.',
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
