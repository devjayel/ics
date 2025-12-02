<?php

namespace App\Http\Controllers;

use App\Models\Rul;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ResourceUnitLeadersController extends Controller
{
    public function index()
    {
        $ruls = Rul::with('certificates')->latest()->paginate(10);
        
        return Inertia::render('ruls/index', [
            'ruls' => $ruls
        ]);
    }

    public function create()
    {
        return Inertia::render('ruls/create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255|unique:resident_unit_leaders,serial_number',
            'department' => 'required|string|max:255',
            'certificates' => 'nullable|array',
            'certificates.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
            'signature' => 'nullable|string',
        ]);

        $rul = Rul::create([
            'uuid' => Str::uuid(),
            'name' => $validated['name'],
            'contact_number' => $validated['contact_number'],
            'serial_number' => $validated['serial_number'],
            'department' => $validated['department'],
            'token' => Str::random(60),
        ]);

        // Handle signature upload
        if ($request->filled('signature')) {
            $signatureData = $request->input('signature');
            // Remove data:image/png;base64, prefix if present
            $signatureData = preg_replace('/^data:image\/\w+;base64,/', '', $signatureData);
            $signatureData = base64_decode($signatureData);
            
            $signaturePath = 'signatures/' . $rul->uuid . '.png';
            Storage::disk('public')->put($signaturePath, $signatureData);
            
            $rul->update(['signature' => $signaturePath]);
        }

        // Handle certificate uploads
        if ($request->hasFile('certificates')) {
            foreach ($request->file('certificates') as $certificate) {
                $fileName = time() . '_' . Str::uuid() . '.' . $certificate->getClientOriginalExtension();
                $filePath = $certificate->storeAs('certificates', $fileName, 'public');
                
                Certificate::create([
                    'uuid' => Str::uuid(),
                    'rul_id' => $rul->id,
                    'certificate_name' => $certificate->getClientOriginalName(),
                    'file_path' => $filePath,
                ]);
            }
        }

        return redirect()->route('ruls.index')->with('success', 'Resource Unit Leader created successfully.');
    }

    public function show(Rul $rul)
    {
        $rul->load('certificates');
        
        return Inertia::render('ruls/show', [
            'rul' => $rul
        ]);
    }

    public function edit(Rul $rul)
    {
        $rul->load('certificates');
        
        return Inertia::render('ruls/edit', [
            'rul' => $rul
        ]);
    }

    public function update(Request $request, Rul $rul)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255|unique:resident_unit_leaders,serial_number,' . $rul->id,
            'department' => 'required|string|max:255',
            'certificates' => 'nullable|array',
            'certificates.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
            'signature' => 'nullable|string',
            'remove_certificates' => 'nullable|array',
            'remove_certificates.*' => 'exists:certificates,uuid',
            'remove_signature' => 'nullable|boolean',
        ]);

        $rul->update([
            'name' => $validated['name'],
            'contact_number' => $validated['contact_number'],
            'serial_number' => $validated['serial_number'],
            'department' => $validated['department'],
        ]);

        // Handle signature update
        if ($request->boolean('remove_signature')) {
            if ($rul->signature) {
                Storage::disk('public')->delete($rul->signature);
                $rul->update(['signature' => null]);
            }
        } elseif ($request->filled('signature')) {
            // Delete old signature if exists
            if ($rul->signature) {
                Storage::disk('public')->delete($rul->signature);
            }
            
            $signatureData = $request->input('signature');
            $signatureData = preg_replace('/^data:image\/\w+;base64,/', '', $signatureData);
            $signatureData = base64_decode($signatureData);
            
            $signaturePath = 'signatures/' . $rul->uuid . '.png';
            Storage::disk('public')->put($signaturePath, $signatureData);
            
            $rul->update(['signature' => $signaturePath]);
        }

        // Handle certificate removals
        if ($request->has('remove_certificates')) {
            foreach ($request->input('remove_certificates') as $certUuid) {
                $certificate = Certificate::where('uuid', $certUuid)->first();
                if ($certificate && $certificate->rul_id === $rul->id) {
                    Storage::disk('public')->delete($certificate->file_path);
                    $certificate->delete();
                }
            }
        }

        // Handle new certificate uploads
        if ($request->hasFile('certificates')) {
            foreach ($request->file('certificates') as $certificate) {
                $fileName = time() . '_' . Str::uuid() . '.' . $certificate->getClientOriginalExtension();
                $filePath = $certificate->storeAs('certificates', $fileName, 'public');
                
                Certificate::create([
                    'uuid' => Str::uuid(),
                    'rul_id' => $rul->id,
                    'certificate_name' => $certificate->getClientOriginalName(),
                    'file_path' => $filePath,
                ]);
            }
        }

        return redirect()->route('ruls.index')->with('success', 'Resource Unit Leader updated successfully.');
    }

    public function destroy(Rul $rul)
    {
        // Delete signature if exists
        if ($rul->signature) {
            Storage::disk('public')->delete($rul->signature);
        }

        // Delete all certificates
        foreach ($rul->certificates as $certificate) {
            Storage::disk('public')->delete($certificate->file_path);
            $certificate->delete();
        }

        $rul->delete();

        return redirect()->route('ruls.index')->with('success', 'Resource Unit Leader deleted successfully.');
    }
}
