<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rul;
use Illuminate\Http\Request;

class RulProfileController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(string $uuid)
    {
        $profile = Rul::where('uuid', $uuid)->first();
        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'RUL profile not found',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $profile,
        ]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $profile = Rul::where('uuid', $id)->first();
        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'RUL profile not found',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'contact_number' => 'sometimes|required|string|max:255',
            'department' => 'sometimes|required|string|max:255',
            'certificates' => 'nullable|array',
            'certificates.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
            'signature' => 'nullable|string',
            'remove_certificates' => 'nullable|array',
            'remove_certificates.*' => 'exists:certificates,uuid',
            'remove_signature' => 'nullable|boolean',
        ]);

        // Update basic profile fields (excluding serial_number)
        $profile->update(array_filter([
            'name' => $validated['name'] ?? null,
            'contact_number' => $validated['contact_number'] ?? null,
            'department' => $validated['department'] ?? null,
        ]));

        // Handle signature update
        if ($request->boolean('remove_signature')) {
            if ($profile->signature) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($profile->signature);
                $profile->update(['signature' => null]);
            }
        } elseif ($request->filled('signature')) {
            // Delete old signature if exists
            if ($profile->signature) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($profile->signature);
            }
            
            $signatureData = $request->input('signature');
            $signatureData = preg_replace('/^data:image\/\w+;base64,/', '', $signatureData);
            $signatureData = base64_decode($signatureData);
            
            $signaturePath = 'signatures/' . $profile->uuid . '.png';
            \Illuminate\Support\Facades\Storage::disk('public')->put($signaturePath, $signatureData);
            
            $profile->update(['signature' => $signaturePath]);
        }

        // Handle certificate removals
        if ($request->has('remove_certificates')) {
            foreach ($request->input('remove_certificates') as $certUuid) {
                $certificate = \App\Models\Certificate::where('uuid', $certUuid)->first();
                if ($certificate && $certificate->rul_id === $profile->id) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($certificate->file_path);
                    $certificate->delete();
                }
            }
        }

        // Handle new certificate uploads
        if ($request->hasFile('certificates')) {
            foreach ($request->file('certificates') as $certificate) {
                $fileName = time() . '_' . \Illuminate\Support\Str::uuid() . '.' . $certificate->getClientOriginalExtension();
                $filePath = $certificate->storeAs('certificates', $fileName, 'public');
                
                \App\Models\Certificate::create([
                    'uuid' => \Illuminate\Support\Str::uuid(),
                    'rul_id' => $profile->id,
                    'certificate_name' => $certificate->getClientOriginalName(),
                    'file_path' => $filePath,
                ]);
            }
        }

        // Reload relationships
        $profile->load('certificates');

        return response()->json([
            'success' => true,
            'message' => 'RUL profile updated successfully',
            'data' => $profile,
        ]);
    }
}
