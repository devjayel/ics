<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CheckInDetailHistories;
use Illuminate\Http\Request;

class CheckInDetailHistoriesController extends Controller
{
    public function show($uuid)
    {
        $CheckInDetailHistories = CheckInDetailHistories::where('uuid', $uuid)->first();
        if (!$CheckInDetailHistories) {
            return response()->json([
                'success' => false,
                'message' => 'Check-In Detail History not found',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $CheckInDetailHistories,
        ]);
    }

    public function updateStatus($uuid, $status)
    {
        $CheckInDetailHistories = CheckInDetailHistories::where('uuid', $uuid)->first();
        if (!$CheckInDetailHistories) {
            return response()->json([
                'success' => false,
                'message' => 'Check-In Detail History not found',
            ], 404);
        }
        $CheckInDetailHistories->status = $status;
        $CheckInDetailHistories->save();

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
        ]);
    }
}
