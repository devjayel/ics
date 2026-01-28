<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PersonnelResource;
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
            'data' => new PersonnelResource($personnel),
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
            'status' => 'sometimes|required',
        ], [
            'name.required' => 'Name is required.',
            'name.max' => 'Name must not exceed 255 characters.',
            'contact_number.required' => 'Contact number is required.',
            'contact_number.max' => 'Contact number must not exceed 255 characters.',
            'department.required' => 'Department is required.',
            'department.max' => 'Department must not exceed 255 characters.',
            'status.required' => 'Status is required.',
        ]);

        $personnel->update(array_filter([
            'name' => $validated['name'] ?? null,
            'contact_number' => $validated['contact_number'] ?? null,
            'department' => $validated['department'] ?? null,
            'status' => $validated['status'] ?? null,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => new PersonnelResource($personnel),
        ]);
    }

    public function updateAvatar(Request $request)
    {
        $personnel = request()->user();

        $validated = $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ], [
            'avatar.required' => 'Avatar image is required.',
            'avatar.image' => 'The file must be an image.',
            'avatar.mimes' => 'Avatar must be a file of type: jpeg, png, jpg, gif.',
            'avatar.max' => 'Avatar must not exceed 2MB in size.',
        ]);

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');

            // Delete old avatar if exists
            if ($personnel->avatar) {
                \Storage::disk('public')->delete($personnel->avatar);
            }

            // Update personnel avatar path
            $personnel->avatar = $avatarPath;
            $personnel->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Avatar updated successfully',
            'data' => new PersonnelResource($personnel),
        ]);
    }
}
