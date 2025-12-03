<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
}
