<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Ics211RecordResource;
use App\Models\Ics211Record;
use App\Models\Personnel;
use App\Models\Rul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticController extends Controller
{
    public function index()
    {
        // Total counts
        $totalPersonnels = Personnel::count();
        $totalRuls = Rul::count();
        $totalRecords = Ics211Record::count();

        // ICS Records status statistics
        $completedCount = Ics211Record::where('status', 'completed')->count();
        $ongoingCount = Ics211Record::where('status', 'ongoing')->count();
        $pendingCount = Ics211Record::where('status', 'pending')->count();

        // Calculate percentages
        $completedPercent = $totalRecords > 0 ? round(($completedCount / $totalRecords) * 100, 2) : 0;
        $ongoingPercent = $totalRecords > 0 ? round(($ongoingCount / $totalRecords) * 100, 2) : 0;
        $pendingPercent = $totalRecords > 0 ? round(($pendingCount / $totalRecords) * 100, 2) : 0;

        // Monthly data for current year
        $currentYear = Carbon::now()->year;
        $monthlyData = [];
        $months = [
            'january', 'february', 'march', 'april', 'may', 'june',
            'july', 'august', 'september', 'october', 'november', 'december'
        ];

        foreach ($months as $index => $month) {
            $monthNumber = $index + 1;
            
            // Get records for this month
            $monthRecords = Ics211Record::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $monthNumber);
            
            $monthTotal = $monthRecords->count();
            $monthCompleted = (clone $monthRecords)->where('status', 'completed')->count();
            $monthOngoing = (clone $monthRecords)->where('status', 'ongoing')->count();
            $monthPending = (clone $monthRecords)->where('status', 'pending')->count();

            // Calculate old pending (records older than 7 days and still pending)
            $oldPendingCount = Ics211Record::where('status', 'pending')
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $monthNumber)
                ->where('created_at', '<=', Carbon::now()->subDays(7))
                ->count();

            $monthlyData[$month] = [
                'completed' => [
                    'percent' => $monthTotal > 0 ? round(($monthCompleted / $monthTotal) * 100, 2) : 0,
                    'whole' => $monthCompleted,
                ],
                'ongoing' => [
                    'percent' => $monthTotal > 0 ? round(($monthOngoing / $monthTotal) * 100, 2) : 0,
                    'whole' => $monthOngoing,
                ],
                'pending' => [
                    'percent' => $monthTotal > 0 ? round(($monthPending / $monthTotal) * 100, 2) : 0,
                    'whole' => $monthPending,
                    'old_pending' => $oldPendingCount, // Unprocessed for long time
                ],
                'total' => $monthTotal,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'counts' => [
                    'personnels' => $totalPersonnels,
                    'ruls' => $totalRuls,
                    'total_records' => $totalRecords,
                ],
                'overall_statistics' => [
                    'completed' => [
                        'count' => $completedCount,
                        'percent' => $completedPercent,
                    ],
                    'ongoing' => [
                        'count' => $ongoingCount,
                        'percent' => $ongoingPercent,
                    ],
                    'pending' => [
                        'count' => $pendingCount,
                        'percent' => $pendingPercent,
                    ],
                ],
                'monthly_data' => $monthlyData,
                'year' => $currentYear,
            ],
        ]);
    }

    public function map()
    {
        // Get all unique regions
        $regions = Ics211Record::select('region')
            ->distinct()
            ->whereNotNull('region')
            ->pluck('region');

        $regionalData = [];

        foreach ($regions as $region) {
            // Get total records for this region
            $regionRecords = Ics211Record::where('region', $region);
            $totalRecords = $regionRecords->count();

            // Get status counts
            $completedCount = (clone $regionRecords)->where('status', 'completed')->count();
            $ongoingCount = (clone $regionRecords)->where('status', 'ongoing')->count();
            $pendingCount = (clone $regionRecords)->where('status', 'pending')->count();

            // Calculate percentages
            $completedPercent = $totalRecords > 0 ? round(($completedCount / $totalRecords) * 100, 2) : 0;
            $ongoingPercent = $totalRecords > 0 ? round(($ongoingCount / $totalRecords) * 100, 2) : 0;
            $pendingPercent = $totalRecords > 0 ? round(($pendingCount / $totalRecords) * 100, 2) : 0;

            // Get recent activity (last 30 days)
            $recentActivity = Ics211Record::where('region', $region)
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->count();

            $regionalData[$region] = [
                'total_records' => $totalRecords,
                'statistics' => [
                    'completed' => [
                        'count' => $completedCount,
                        'percent' => $completedPercent,
                    ],
                    'ongoing' => [
                        'count' => $ongoingCount,
                        'percent' => $ongoingPercent,
                    ],
                    'pending' => [
                        'count' => $pendingCount,
                        'percent' => $pendingPercent,
                    ],
                ],
                'recent_activity_30days' => $recentActivity,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'regions' => $regionalData,
                'total_regions' => count($regions),
            ],
        ]);
    }

    public function regions()
    {
        $regions = Ics211Record::select('region')
            ->distinct()
            ->whereNotNull('region')
            ->where('region', '<>', '')
            ->orderBy('region')
            ->pluck('region');

        return response()->json([
            'success' => true,
            'data' => [
                'regions' => $regions,
                'total_regions' => $regions->count(),
            ],
        ]);
    }

    public function region(Request $request, $region)
    {
        // Get all ICS records for the specified region
        $records = Ics211Record::where('region', $region)
            ->with(['operators:id,name,contact_number', 'checkInDetails'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get statistics for this region
        $totalRecords = $records->count();
        $completedCount = $records->where('status', 'completed')->count();
        $ongoingCount = $records->where('status', 'ongoing')->count();
        $pendingCount = $records->where('status', 'pending')->count();

        // Calculate percentages
        $completedPercent = $totalRecords > 0 ? round(($completedCount / $totalRecords) * 100, 2) : 0;
        $ongoingPercent = $totalRecords > 0 ? round(($ongoingCount / $totalRecords) * 100, 2) : 0;
        $pendingPercent = $totalRecords > 0 ? round(($pendingCount / $totalRecords) * 100, 2) : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'region' => $region,
                'statistics' => [
                    'total_records' => $totalRecords,
                    'completed' => [
                        'count' => $completedCount,
                        'percent' => $completedPercent,
                    ],
                    'ongoing' => [
                        'count' => $ongoingCount,
                        'percent' => $ongoingPercent,
                    ],
                    'pending' => [
                        'count' => $pendingCount,
                        'percent' => $pendingPercent,
                    ],
                ],
                'records' => Ics211RecordResource::collection($records),
            ],
        ]);
    }

    public function show($uuid)
    {
        // Get specific ICS record by UUID
        $record = Ics211Record::where('uuid', $uuid)
            ->with([
                'operators:id,name,contact_number',
                'checkInDetails.personnel:id,name,contact_number',
            ])
            ->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'ICS record not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new Ics211RecordResource($record),
        ]);
    }
}
