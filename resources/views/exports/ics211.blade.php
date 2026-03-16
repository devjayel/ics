<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ICS 211 - {{ $record->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8px;
            color: #000;
        }

        .page {
            width: 100%;
            padding: 8px;
        }

        /* Header */
        .header-wrapper {
            display: flex;
            width: 100%;
            border: 1px solid #000;
            margin-bottom: 0;
        }

        .header-logo {
            width: 60px;
            padding: 4px;
            border-right: 1px solid #000;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .header-logo img {
            width: 50px;
            height: 50px;
        }

        .header-title {
            flex: 1;
            text-align: center;
            padding: 6px;
            border-right: 1px solid #000;
        }

        .header-title h1 {
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .header-title h2 {
            font-size: 12px;
            font-weight: bold;
        }

        /* Section 1-3 row */
        .info-row {
            display: flex;
            width: 100%;
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        .info-cell {
            padding: 4px 6px;
            border-right: 1px solid #000;
        }

        .info-cell:last-child {
            border-right: none;
        }

        .info-cell .label {
            font-size: 7px;
            font-weight: bold;
        }

        .info-cell .value {
            font-size: 9px;
            margin-top: 2px;
            min-height: 14px;
        }

        .cell-incident {
            width: 30%;
        }

        .cell-datetime {
            width: 20%;
        }

        .cell-location {
            flex: 1;
        }

        .checkin-location-options {
            display: flex;
            gap: 8px;
            margin-top: 3px;
            flex-wrap: wrap;
        }

        .checkin-location-options span {
            font-size: 7.5px;
        }

        .checkbox {
            display: inline-block;
            width: 8px;
            height: 8px;
            border: 1px solid #000;
            margin-right: 2px;
            vertical-align: middle;
            text-align: center;
            line-height: 8px;
            font-size: 7px;
        }

        .checkbox.checked::after {
            content: '✓';
        }

        /* Section 4 header */
        .section-header {
            background-color: #fff;
            text-align: center;
            font-weight: bold;
            font-size: 8px;
            padding: 3px;
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            border-left: 1px solid #000;
            border-right: 1px solid #000;
        }

        table th, table td {
            border: 1px solid #000;
            padding: 2px 3px;
            text-align: center;
            vertical-align: middle;
            font-size: 7px;
            word-break: break-word;
        }

        table th {
            font-weight: bold;
            background-color: #f2f2f2;
        }

        .th-group {
            font-weight: bold;
            font-size: 7px;
        }

        .col-order { width: 3.5%; }
        .col-checkin { width: 5%; }
        .col-kind { width: 3.5%; }
        .col-type { width: 4%; }
        .col-resource-group { width: 11%; }
        .col-single { width: 4%; }
        .col-st { width: 2.5%; }
        .col-tf { width: 2.5%; }
        .col-agency { width: 8%; }
        .col-leader { width: 7%; }
        .col-contact { width: 6.5%; }
        .col-total { width: 4%; }
        .col-departure-group { width: 16%; }
        .col-origin { width: 5.5%; }
        .col-dep-dt { width: 5.5%; }
        .col-method { width: 5%; }
        .col-manifest { width: 5%; }
        .col-yes { width: 2.5%; }
        .col-no { width: 2.5%; }
        .col-assignment { width: 7%; }
        .col-quals { width: 7%; }
        .col-resl { width: 5%; }

        .empty-rows td {
            height: 14px;
        }

        /* Footer */
        .footer {
            border: 1px solid #000;
            border-top: none;
            display: flex;
            width: 100%;
        }

        .footer-cell {
            padding: 4px 6px;
            border-right: 1px solid #000;
            font-size: 7.5px;
        }

        .footer-cell:last-child {
            border-right: none;
        }

        .footer-page {
            width: 80px;
        }

        .footer-prepared {
            width: 120px;
        }

        .footer-name {
            flex: 1;
        }

        .footer-date {
            width: 130px;
        }

        .footer-time {
            width: 100px;
        }

        .footer-note {
            text-align: right;
            font-size: 6.5px;
            padding: 2px 4px;
            border-top: 1px solid #000;
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .header-top {
            display: flex;
            border: 1px solid #000;
            border-bottom: none;
        }

        .header-logo-box {
            width: 70px;
            border-right: 1px solid #000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4px;
        }

        .header-center {
            flex: 1;
            text-align: center;
            padding: 6px 0;
            border-right: 1px solid #000;
        }
    </style>
</head>
<body>
<div class="page">

    {{-- Header Row --}}
    <table style="border-collapse: collapse; width: 100%; border: 1px solid #000;">
        <tr>
            <td style="text-align: center; padding: 6px; vertical-align: middle;">
                <div style="font-size: 14px; font-weight: bold; letter-spacing: 1px;">INCIDENT CHECK-IN LIST</div>
                <div style="font-size: 12px; font-weight: bold;">ICS 211</div>
            </td>
        </tr>
    </table>

    {{-- Sections 1–3 --}}
    <table style="border-collapse: collapse; width: 100%; border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000;">
        <tr>
            <td style="width: 30%; border-right: 1px solid #000; padding: 4px 6px; vertical-align: top;">
                <div style="font-size: 7px; font-weight: bold;">1. INCIDENT/EVENT NAME</div>
                <div style="font-size: 9px; margin-top: 3px; min-height: 28px;">{{ $record->name }}</div>
            </td>
            <td style="width: 20%; border-right: 1px solid #000; padding: 4px 6px; vertical-align: top;">
                <div style="font-size: 7px; font-weight: bold;">2. START DATE AND TIME</div>
                <div style="font-size: 8px; margin-top: 3px;">
                    Date: {{ $record->start_date ? $record->start_date->format('m/d/Y') : '___________' }}
                </div>
                <div style="font-size: 8px; margin-top: 2px;">
                    Time: {{ $record->start_time ? \Carbon\Carbon::parse($record->start_time)->format('H:i') : '___________' }}
                </div>
            </td>
            <td style="padding: 4px 6px; vertical-align: top;">
                <div style="font-size: 7px; font-weight: bold;">3. CHECK-IN LOCATION (Please check)</div>
                <div style="margin-top: 4px; font-size: 8px;">
                    @php
                        $location = strtolower($record->checkin_location ?? '');
                        $locations = ['base', 'camp', 'staging area', 'icp', 'others'];
                    @endphp
                    @foreach($locations as $loc)
                        <span style="margin-right: 8px;">
                            @if($location === strtolower($loc))
                                <span style="display:inline-block; width:8px; height:8px; border:1px solid #000; font-size:7px; text-align:center; vertical-align:middle;">&#10003;</span>
                            @else
                                <span style="display:inline-block; width:8px; height:8px; border:1px solid #000; vertical-align:middle;"></span>
                            @endif
                            {{ ucfirst($loc) }}
                        </span>
                    @endforeach
                </div>
            </td>
        </tr>
    </table>

    {{-- Section 4 Header --}}
    <div style="text-align: center; font-weight: bold; font-size: 8px; padding: 3px; border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000;">
        4. CHECK-IN INFORMATION
    </div>

    {{-- Check-in Details Table --}}
    <table style="border-collapse: collapse; width: 100%; height: 520px;">
        <thead>
            {{-- Top header row --}}
            <tr>
                <th rowspan="3" style="width:3.5%;">Order/<br>Request<br>No.</th>
                <th rowspan="3" style="width:5%;">Check-in<br>Date and<br>Time</th>
                <th rowspan="3" style="width:3.5%;">Kind</th>
                <th rowspan="3" style="width:4%;">Type</th>
                <th colspan="3" style="width:11%;">Resource Identifier</th>
                <th rowspan="3" style="width:8%;">Name of Agency /<br>Office / Home<br>Base</th>
                <th rowspan="3" style="width:7%;">Name of Leader</th>
                <th rowspan="3" style="width:6.5%;">Contact<br>Details</th>
                <th rowspan="3" style="width:4%;">Total No.<br>of Pers.</th>
                <th colspan="3" style="width:16%;">Departure Details</th>
                <th colspan="2" style="width:5%;">With<br>Manifest?</th>
                <th rowspan="3" style="width:7%;">Incident<br>Assignment</th>
                <th rowspan="3" style="width:7%;">Other<br>Qualifications</th>
                <th rowspan="3" style="width:5%;">Data Sent to<br>RESL</th>
            </tr>
            <tr>
                <th rowspan="2" style="width:4%;">Single<br>Resource</th>
                <th rowspan="2" style="width:2.5%;">ST</th>
                <th rowspan="2" style="width:2.5%;">TF</th>
                <th rowspan="2" style="width:5.5%;">Point of<br>Origin</th>
                <th rowspan="2" style="width:5.5%;">Date and<br>Time</th>
                <th rowspan="2" style="width:5%;">Method of<br>Travel</th>
                <th style="width:2.5%;">Yes</th>
                <th style="width:2.5%;">No</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($checkInDetails as $detail)
            <tr>
                <td>{{ $detail->order_request_number ?? '' }}</td>
                <td>
                    {{ $detail->checkin_date ? $detail->checkin_date->format('m/d/Y') : '' }}<br>
                    {{ $detail->checkin_time ? \Carbon\Carbon::parse($detail->checkin_time)->format('H:i') : '' }}
                </td>
                <td>{{ $detail->kind ?? '' }}</td>
                <td>{{ $detail->type ?? '' }}</td>
                {{-- Resource Identifier: Single Resource, ST, TF based on category --}}
                <td>{{ ($detail->category ?? '') === 'Single Resource' ? ($detail->resource_identifier ?? '') : '' }}</td>
                <td>{{ ($detail->category ?? '') === 'ST' ? ($detail->resource_identifier ?? '') : '' }}</td>
                <td>{{ ($detail->category ?? '') === 'TF' ? ($detail->resource_identifier ?? '') : '' }}</td>
                <td class="text-left">{{ $detail->department ?? '' }}</td>
                <td class="text-left">{{ $detail->name_of_leader ?? '' }}</td>
                <td>{{ $detail->contact_information ?? '' }}</td>
                <td>{{ $detail->quantity ?? '' }}</td>
                <td class="text-left">{{ $detail->departure_point_of_origin ?? '' }}</td>
                <td>
                    {{ $detail->departure_date ? $detail->departure_date->format('m/d/Y') : '' }}<br>
                    {{ $detail->departure_time ? \Carbon\Carbon::parse($detail->departure_time)->format('H:i') : '' }}
                </td>
                <td>{{ $detail->departure_method_of_travel ?? '' }}</td>
                <td>{{ $detail->with_manifest ? '✓' : '' }}</td>
                <td>{{ !$detail->with_manifest ? '✓' : '' }}</td>
                <td class="text-left">{{ $detail->incident_assignment ?? '' }}</td>
                <td class="text-left">{{ $detail->other_qualifications ?? '' }}</td>
                <td>{{ $detail->sent_resl ? '✓' : '' }}</td>
            </tr>
            @empty
            @endforelse

            {{-- Fill empty rows to maintain form appearance (minimum 8 rows) --}}
            @for($i = count($checkInDetails); $i < 8; $i++)
            <tr>
                <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                <td></td><td></td><td></td><td></td><td></td>
            </tr>
            @endfor
        </tbody>
    </table>

    {{-- Additional sheets note --}}
    <div style="text-align: right; font-size: 6.5px; padding: 2px 4px; border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; font-style: italic;">
        Use additional sheets as needed
    </div>

    {{-- Footer --}}
    <table style="border-collapse: collapse; width: 100%; border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000;">
        <tr>
            <td style="width: 80px; border-right: 1px solid #000; padding: 4px 6px; font-size: 7.5px; vertical-align: top;">
                Page {{ $page }} of {{ $totalPages }}
            </td>
            <td style="width: 130px; border-right: 1px solid #000; padding: 4px 6px; font-size: 7.5px; vertical-align: top;">
                <strong>5. Prepared by ({{ $preparedBy ?? '__________' }})</strong>
            </td>
            <td style="border-right: 1px solid #000; padding: 4px 6px; font-size: 7.5px; vertical-align: top;">
                Name and Signature: {{ $preparedByName ?? '' }}
                @if(!empty($signatureDataUri))
                    <br><img src="{{ $signatureDataUri }}" style="max-height: 30px; max-width: 120px; margin-top: 2px;" alt="Signature">
                @endif
            </td>
            <td style="width: 130px; border-right: 1px solid #000; padding: 4px 6px; font-size: 7.5px; vertical-align: top;">
                Date Prepared: {{ $datePrepared }}
            </td>
            <td style="width: 100px; padding: 4px 6px; font-size: 7.5px; vertical-align: top;">
                Time Prepared: {{ $timePrepared }}
            </td>
        </tr>
    </table>

</div>
</body>
</html>
