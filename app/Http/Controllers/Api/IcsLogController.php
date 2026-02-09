<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IcsLog;
use App\Models\Ics211Record;
use Illuminate\Http\Request;

class IcsLogController extends Controller
{
    /**
     * Display logs for a specific ICS record
     */
    public function icsRecordLogs($icsUuid)
    {
        $ics211Record = Ics211Record::where('uuid', $icsUuid)->first();
        if (!$ics211Record) {
            return response()->json([
                'success' => false,
                'message' => 'ICS 211 record not found',
            ], 404);
        }

        $logs = IcsLog::where('ics211_record_id', $ics211Record->id)
            ->with(['rul:id,name,contact_number', 'ics211Record:id,uuid,name'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }

    /**
     * Display all logs for RUL (operator)
     */
    public function myLogs(Request $request)
    {
        $rulId = $request->user()->id;

        $logs = IcsLog::where('rul_id', $rulId)
            ->with(['rul:id,name,contact_number', 'ics211Record:id,uuid,name'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $logs,
            'total' => $logs->total(),
        ]);
    }

    /**
     * Display logs filtered by action type for RUL
     */
    public function myLogsByAction(Request $request, $action)
    {
        $rulId = $request->user()->id;
        
        $validActions = ['created', 'updated', 'personnel_added', 'personnel_removed', 'status_changed'];
        if (!in_array($action, $validActions)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid action filter',
            ], 400);
        }

        $logs = IcsLog::where('rul_id', $rulId)
            ->where('action', $action)
            ->with(['rul:id,name,contact_number', 'ics211Record:id,uuid,name'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $logs,
            'total' => $logs->total(),
            'action' => $action,
        ]);
    }

    /**
     * Display activity summary for RUL
     */
    public function myActivitySummary(Request $request)
    {
        $rulId = $request->user()->id;

        $summary = IcsLog::where('rul_id', $rulId)
            ->selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->get()
            ->pluck('count', 'action');

        $totalActions = IcsLog::where('rul_id', $rulId)->count();

        $recentLogs = IcsLog::where('rul_id', $rulId)
            ->with(['rul:id,name,contact_number', 'ics211Record:id,uuid,name'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total_actions' => $totalActions,
                'actions_breakdown' => $summary,
                'recent_activities' => $recentLogs,
            ],
        ]);
    }

    /**
     * Display logs by date range for RUL
     */
    public function myLogsByDateRange(Request $request)
    {
        $validated = $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        $rulId = $request->user()->id;

        $logs = IcsLog::where('rul_id', $rulId)
            ->whereBetween('created_at', [$validated['from_date'], $validated['to_date']])
            ->with(['rul:id,name,contact_number', 'ics211Record:id,uuid,name'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $logs,
            'total' => $logs->total(),
            'date_range' => [
                'from' => $validated['from_date'],
                'to' => $validated['to_date'],
            ],
        ]);
    }
}
