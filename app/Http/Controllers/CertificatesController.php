<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Rul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CertificatesController extends Controller
{
    public function index()
    {
        $certificates = Certificate::with('rul')
            ->latest()
            ->paginate(10);
        
        return Inertia::render('certificates/index', [
            'certificates' => $certificates
        ]);
    }

    public function create()
    {
        $ruls = Rul::select('id', 'uuid', 'name', 'contact_number')->get();
        
        return Inertia::render('certificates/create', [
            'ruls' => $ruls
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rul_id' => 'required|exists:resident_unit_leaders,id',
            'certificate_name' => 'required|string|max:255',
            'certificate_file' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240',
        ]);

        $validated['uuid'] = \Illuminate\Support\Str::uuid();

        if ($request->hasFile('certificate_file')) {
            $file = $request->file('certificate_file');
            $path = $file->store('certificates', 'public');
            $validated['file_path'] = $path;
        }

        unset($validated['certificate_file']);
        
        Certificate::create($validated);

        return redirect()->route('certificates.index')
            ->with('success', 'Certificate created successfully.');
    }

    public function show(Certificate $certificate)
    {
        $certificate->load('rul');
        
        return Inertia::render('certificates/show', [
            'certificate' => $certificate
        ]);
    }

    public function edit(Certificate $certificate)
    {
        $certificate->load('rul');
        $ruls = Rul::select('id', 'uuid', 'name', 'contact_number')->get();
        
        return Inertia::render('certificates/edit', [
            'certificate' => $certificate,
            'ruls' => $ruls
        ]);
    }

    public function update(Request $request, Certificate $certificate)
    {
        $validated = $request->validate([
            'rul_id' => 'required|exists:resident_unit_leaders,id',
            'certificate_name' => 'required|string|max:255',
            'certificate_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240',
        ]);

        if ($request->hasFile('certificate_file')) {
            // Delete old file
            if ($certificate->file_path) {
                Storage::disk('public')->delete($certificate->file_path);
            }
            
            $file = $request->file('certificate_file');
            $path = $file->store('certificates', 'public');
            $validated['file_path'] = $path;
        }

        unset($validated['certificate_file']);
        
        $certificate->update($validated);

        return redirect()->route('certificates.index')
            ->with('success', 'Certificate updated successfully.');
    }

    public function destroy(Certificate $certificate)
    {
        // Delete file from storage
        if ($certificate->file_path) {
            Storage::disk('public')->delete($certificate->file_path);
        }
        
        $certificate->delete();

        return redirect()->route('certificates.index')
            ->with('success', 'Certificate deleted successfully.');
    }
}
