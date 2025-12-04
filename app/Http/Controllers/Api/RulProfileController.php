<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RulResource;
use App\Models\Rul;
use Illuminate\Http\Request;

class RulProfileController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $profile = $request->user();

        return response()->json([
            'success' => true,
            'data' => new RulResource($profile->load('certificates')),
        ]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $profile = $request->user();

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
        ], [
            'name.required' => 'Name is required.',
            'name.max' => 'Name must not exceed 255 characters.',
            'contact_number.required' => 'Contact number is required.',
            'contact_number.max' => 'Contact number must not exceed 255 characters.',
            'department.required' => 'Department is required.',
            'department.max' => 'Department must not exceed 255 characters.',
            'certificates.*.file' => 'Each certificate must be a valid file.',
            'certificates.*.mimes' => 'Certificates must be PDF, JPG, JPEG, or PNG files.',
            'certificates.*.max' => 'Each certificate must not exceed 10MB.',
            'remove_certificates.*.exists' => 'Certificate not found.',
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
