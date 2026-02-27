<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11pt;
            color: #1f2937;
            line-height: 1.5;
            padding: 40px;
            background: white;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #1e293b;
        }
        .header h1 {
            font-size: 18pt;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }
        .header .subtitle {
            font-size: 11pt;
            color: #6b7280;
        }
        .header .org {
            font-size: 9pt;
            color: #9ca3af;
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .division-header {
            background: #f1f5f9;
            padding: 10px 16px;
            margin: 24px 0 12px;
            border-left: 4px solid #1e293b;
            font-size: 13pt;
            font-weight: 700;
            color: #1e293b;
        }
        .update-header {
            padding: 10px 0;
            margin-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        .update-header .week {
            font-size: 11pt;
            font-weight: 600;
            color: #374151;
        }
        .update-header .meta {
            font-size: 9pt;
            color: #9ca3af;
            margin-top: 2px;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 8pt;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-approved { background: #dcfce7; color: #15803d; }
        .status-submitted { background: #dbeafe; color: #1d4ed8; }
        .status-rejected { background: #fee2e2; color: #b91c1c; }
        .status-draft { background: #f3f4f6; color: #4b5563; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 10pt;
        }
        table th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            text-align: left;
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        table td {
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }
        table td.center { text-align: center; }
        table tr:nth-child(even) { background: #fafafa; }
        .activity-status {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 8pt;
            font-weight: 600;
            white-space: nowrap;
        }
        .st-completed { background: #dcfce7; color: #15803d; }
        .st-ongoing { background: #fef9c3; color: #a16207; }
        .st-not_started { background: #fee2e2; color: #b91c1c; }
        .st-na { background: #f3f4f6; color: #6b7280; }
        .notes-section {
            margin: 12px 0;
            padding: 10px 14px;
            background: #f9fafb;
            border-left: 3px solid #d1d5db;
            font-size: 10pt;
        }
        .notes-section .label {
            font-weight: 600;
            color: #374151;
            font-size: 9pt;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .notes-section .value {
            color: #4b5563;
            white-space: pre-line;
        }
        .review-box {
            margin: 12px 0 24px;
            padding: 10px 14px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 6px;
            font-size: 9pt;
        }
        .review-box .reviewer { font-weight: 600; color: #92400e; }
        .page-break { page-break-before: always; }
        .footer {
            margin-top: 40px;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 8pt;
            color: #9ca3af;
        }
        .print-controls {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            gap: 8px;
        }
        .print-controls button {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 10pt;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-print {
            background: #dc2626;
            color: white;
        }
        .btn-close {
            background: #6b7280;
            color: white;
        }
        @media print {
            .print-controls { display: none !important; }
            body { padding: 20px; }
            .division-header { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            table th { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .activity-status { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .status-badge { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>

@if($format === 'pdf')
<div class="print-controls">
    <button class="btn-print" onclick="window.print()">🖨 Print / Save as PDF</button>
    <button class="btn-close" onclick="window.close()">✕ Close</button>
</div>
@endif

{{-- Report Header --}}
<div class="header">
    <div class="org">Ministry of Education – Bureau of Secondary &amp; Post-Secondary</div>
    <h1>{{ $title }}</h1>
    <div class="subtitle">{{ $subtitle }}</div>
    <div class="subtitle" style="margin-top: 4px; font-size: 9pt;">Generated on {{ now()->format('F d, Y \a\t h:i A') }}</div>
</div>

@if($isConsolidated)
    {{-- Consolidated Report: Grouped by Division --}}
    @foreach($grouped as $divisionName => $divUpdates)
        @if(!$loop->first)
            <div class="page-break"></div>
        @endif

        <div class="division-header">{{ $divisionName }}</div>

        @foreach($divUpdates as $update)
            <div class="update-header">
                <div class="week">
                    Week of {{ $update->week_start->format('M d') }} – {{ $update->week_end->format('M d, Y') }}
                    <span class="status-badge status-{{ $update->status }}">{{ ucfirst($update->status) }}</span>
                </div>
                <div class="meta">
                    Submitted by {{ $update->submitter->name }}
                    @if($update->reviewer) · Reviewed by {{ $update->reviewer->name }} on {{ $update->reviewed_at->format('M d, Y') }}@endif
                </div>
            </div>

            @if($update->activities->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th style="width: 35px;">No.</th>
                            <th>Activities / Task</th>
                            <th style="width: 120px;">Responsible</th>
                            <th style="width: 95px;">Status</th>
                            <th>Status Comment</th>
                            <th>Challenges</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($update->activities as $index => $activity)
                            <tr>
                                <td class="center">{{ $index + 1 }}</td>
                                <td>{{ $activity->activity }}</td>
                                <td>{{ $activity->responsible_persons ?? '—' }}</td>
                                <td>
                                    @php
                                        $labels = ['not_started' => 'Not Started', 'ongoing' => 'Ongoing', 'completed' => 'Completed', 'na' => 'N/A'];
                                    @endphp
                                    <span class="activity-status st-{{ $activity->status_flag }}">{{ $labels[$activity->status_flag] ?? 'N/A' }}</span>
                                </td>
                                <td>{{ $activity->status_comment ?? '—' }}</td>
                                <td>{{ $activity->challenges ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @if($update->support_needed || $update->key_metrics)
                <div class="notes-section">
                    @if($update->support_needed)
                        <div class="label">Support Needed</div>
                        <div class="value">{{ $update->support_needed }}</div>
                    @endif
                    @if($update->key_metrics)
                        <div class="label" style="margin-top: 8px;">Key Metrics</div>
                        <div class="value">{{ $update->key_metrics }}</div>
                    @endif
                </div>
            @endif

            @if($update->review_comments)
                <div class="review-box">
                    <span class="reviewer">Review Comments:</span> {{ $update->review_comments }}
                </div>
            @endif
        @endforeach
    @endforeach
@else
    {{-- Single Update Report --}}
    @foreach($updates as $update)
        <div class="update-header">
            <div class="week">
                {{ $update->division->name }}
                <span class="status-badge status-{{ $update->status }}">{{ ucfirst($update->status) }}</span>
            </div>
            <div class="meta">
                Submitted by {{ $update->submitter->name }}
                @if($update->reviewer) · Reviewed by {{ $update->reviewer->name }} on {{ $update->reviewed_at->format('M d, Y') }}@endif
            </div>
        </div>

        @if($update->activities->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 35px;">No.</th>
                        <th>Activities / Task</th>
                        <th style="width: 120px;">Responsible</th>
                        <th style="width: 95px;">Status</th>
                        <th>Status Comment</th>
                        <th>Challenges</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($update->activities as $index => $activity)
                        <tr>
                            <td class="center">{{ $index + 1 }}</td>
                            <td>{{ $activity->activity }}</td>
                            <td>{{ $activity->responsible_persons ?? '—' }}</td>
                            <td>
                                @php
                                    $labels = ['not_started' => 'Not Started', 'ongoing' => 'Ongoing', 'completed' => 'Completed', 'na' => 'N/A'];
                                @endphp
                                <span class="activity-status st-{{ $activity->status_flag }}">{{ $labels[$activity->status_flag] ?? 'N/A' }}</span>
                            </td>
                            <td>{{ $activity->status_comment ?? '—' }}</td>
                            <td>{{ $activity->challenges ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if($update->accomplishments)
            <div class="notes-section">
                <div class="label">Accomplishments</div>
                <div class="value">{{ $update->accomplishments }}</div>
            </div>
        @endif

        @if($update->support_needed || $update->key_metrics)
            <div class="notes-section">
                @if($update->support_needed)
                    <div class="label">Support Needed</div>
                    <div class="value">{{ $update->support_needed }}</div>
                @endif
                @if($update->key_metrics)
                    <div class="label" style="margin-top: 8px;">Key Metrics</div>
                    <div class="value">{{ $update->key_metrics }}</div>
                @endif
            </div>
        @endif

        @if($update->review_comments)
            <div class="review-box">
                <span class="reviewer">Review Comments:</span> {{ $update->review_comments }}
            </div>
        @endif
    @endforeach
@endif

<div class="footer">
    MOEBSPS – Bureau Activity Tracking System · Generated {{ now()->format('F d, Y') }} · Confidential
</div>

</body>
</html>
