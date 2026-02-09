<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Ics211RecordResource;
use App\Models\Ics211Record;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Get all ICS form records with relations
        $icsRecords = Ics211Record::with([
            'operators.certificates',
            'checkInDetails.personnel',
        ])
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json([
            'success' => true,
            'data' => Ics211RecordResource::collection($icsRecords),
            'total' => $icsRecords->count(),
        ]);
    }
}
