<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PersonnelController extends Controller
{
    public function index()
    {
        $personnels = Personnel::latest()->paginate(10);
        
        return Inertia::render('personnels/index', [
            'personnels' => $personnels
        ]);
    }

    public function create()
    {
        return Inertia::render('personnels/create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255|unique:personnels',
            'department' => 'required|string|max:255',
        ]);

        $validated['uuid'] = \Illuminate\Support\Str::uuid();

        Personnel::create($validated);

        return redirect()->route('personnels.index')
            ->with('success', 'Personnel created successfully.');
    }

    public function show(Personnel $personnel)
    {
        return Inertia::render('personnels/show', [
            'personnel' => $personnel
        ]);
    }

    public function edit(Personnel $personnel)
    {
        return Inertia::render('personnels/edit', [
            'personnel' => $personnel
        ]);
    }

    public function update(Request $request, Personnel $personnel)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255|unique:personnels,serial_number,' . $personnel->id,
            'department' => 'required|string|max:255',
        ]);

        $personnel->update($validated);

        return redirect()->route('personnels.index')
            ->with('success', 'Personnel updated successfully.');
    }

    public function destroy(Personnel $personnel)
    {
        $personnel->delete();

        return redirect()->route('personnels.index')
            ->with('success', 'Personnel deleted successfully.');
    }
}
