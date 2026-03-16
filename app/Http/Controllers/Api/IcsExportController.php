<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ics211Record;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class IcsExportController extends Controller
{
    /**
     * Export an ICS 211 record with all check-in details as a PDF.
     */
    public function export(string $uuid)
    {
        $record = Ics211Record::where('uuid', $uuid)
            ->with(['checkInDetails.personnel', 'operators'])
            ->first();

        if (! $record) {
            return response()->json([
                'success' => false,
                'message' => 'ICS 211 record not found.',
            ], 404);
        }

        $checkInDetails = $record->checkInDetails;
        $firstOperator  = $record->operators->first();

        $signatureDataUri = null;
        if ($firstOperator && $firstOperator->signature) {
            $path = storage_path('app/public/' . $firstOperator->signature);
            if (file_exists($path)) {
                $mime = mime_content_type($path);
                $signatureDataUri = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
            }
        }

        $now = Carbon::now();

        $data = [
            'record'           => $record,
            'checkInDetails'   => $checkInDetails,
            'preparedBy'       => $firstOperator->name ?? null,
            'preparedByName'   => $firstOperator->name ?? null,
            'signatureDataUri' => $signatureDataUri,
            'datePrepared'     => $now->format('m/d/Y'),
            'timePrepared'     => $now->format('H:i'),
            'page'             => 1,
            'totalPages'       => 1,
        ];

        $pdf = Pdf::loadView('exports.ics211', $data)
            ->setPaper('legal', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
                'defaultFont'          => 'Arial',
            ]);

        $filename = 'ICS211_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $record->name) . '_' . $now->format('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }
}
