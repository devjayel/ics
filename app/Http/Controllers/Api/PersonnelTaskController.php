<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PersonnelTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $personnel = auth()->user();

        $tasks = $personnel->tasks()->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $tasks,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function updateStatus($uuid, $status)
    {
        //
    }
}
