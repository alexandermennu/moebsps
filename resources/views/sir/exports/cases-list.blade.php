<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $moduleLabel }} Report - Ministry of Education</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            font-size: 11px; 
            line-height: 1.4; 
            color: #1f2937;
            background: white;
        }
        
        /* Print styles */
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            .page-break { page-break-after: always; }
            @page { margin: 0.5in; size: A4 landscape; }
        }
        
        /* Screen styles for preview */
        @media screen {
            body { padding: 20px; background: #f3f4f6; }
            .document { 
                max-width: 1100px; 
                margin: 0 auto; 
                background: white; 
                padding: 40px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            }
        }
        
        /* Header */
        .header {
            display: flex;
            align-items: center;
            gap: 20px;
            padding-bottom: 20px;
            border-bottom: 3px solid #991b1b;
            margin-bottom: 20px;
        }
        .logo-placeholder {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #991b1b 0%, #7f1d1d 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 10px;
            text-align: center;
        }
        .header-text h1 {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 2px;
        }
        .header-text h2 {
            font-size: 13px;
            font-weight: 600;
            color: #991b1b;
            margin-bottom: 4px;
        }
        .header-text p {
            font-size: 10px;
            color: #6b7280;
        }
        
        /* Meta info */
        .meta-info {
            display: flex;
            justify-content: space-between;
            background: #f9fafb;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 10px;
        }
        .meta-info span { color: #4b5563; }
        .meta-info strong { color: #111827; }
        
        /* Summary stats */
        .summary-stats {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
        }
        .stat-box {
            flex: 1;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px;
            text-align: center;
        }
        .stat-box .number {
            font-size: 22px;
            font-weight: 700;
            color: #111827;
        }
        .stat-box .label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6b7280;
        }
        .stat-box.critical .number { color: #dc2626; }
        .stat-box.open .number { color: #2563eb; }
        .stat-box.resolved .number { color: #059669; }
        
        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        thead {
            background: #1f2937;
            color: white;
        }
        th {
            padding: 10px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }
        tbody tr:nth-child(even) {
            background: #f9fafb;
        }
        tbody tr:hover {
            background: #f3f4f6;
        }
        
        /* Badges */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-critical { background: #fef2f2; color: #dc2626; }
        .badge-high { background: #fff7ed; color: #ea580c; }
        .badge-medium { background: #eff6ff; color: #2563eb; }
        .badge-low { background: #f0fdf4; color: #16a34a; }
        .badge-reported { background: #dbeafe; color: #1d4ed8; }
        .badge-under_review { background: #fef3c7; color: #d97706; }
        .badge-under_investigation { background: #ffedd5; color: #ea580c; }
        .badge-action_taken { background: #f3e8ff; color: #9333ea; }
        .badge-referred { background: #e0e7ff; color: #4f46e5; }
        .badge-resolved { background: #dcfce7; color: #16a34a; }
        .badge-closed { background: #f3f4f6; color: #6b7280; }
        .badge-public { background: #fef9c3; color: #ca8a04; }
        .badge-internal { background: #e0f2fe; color: #0284c7; }
        .badge-urgent { background: #dc2626; color: white; }
        
        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            font-size: 9px;
            color: #9ca3af;
            display: flex;
            justify-content: space-between;
        }
        
        /* Print actions */
        .print-actions {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 8px;
            z-index: 100;
        }
        .print-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .print-btn.pdf {
            background: #dc2626;
            color: white;
        }
        .print-btn.pdf:hover { background: #b91c1c; }
        .print-btn.word {
            background: #2563eb;
            color: white;
        }
        .print-btn.word:hover { background: #1d4ed8; }
        .print-btn.back {
            background: #6b7280;
            color: white;
        }
        .print-btn.back:hover { background: #4b5563; }

        .truncate {
            max-width: 180px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body>
    {{-- Print/Export Actions --}}
    <div class="print-actions no-print">
        <button onclick="history.back()" class="print-btn back">← Back</button>
        <button onclick="window.print()" class="print-btn pdf">📄 Print / Save PDF</button>
    </div>

    <div class="document">
        {{-- Header --}}
        <div class="header">
            <div class="logo-placeholder">MOE<br>LIBERIA</div>
            <div class="header-text">
                <h1>Ministry of Education</h1>
                <h2>{{ $moduleLabel }} Report</h2>
                <p>Bureau of Student Personnel Services · School Incident Reporting System</p>
            </div>
        </div>

        {{-- Meta Info --}}
        <div class="meta-info">
            <span>Generated: <strong>{{ $exportDate->format('F d, Y \a\t h:i A') }}</strong></span>
            <span>Generated by: <strong>{{ $user->name }}</strong></span>
            <span>Total Records: <strong>{{ $incidents->count() }}</strong></span>
        </div>

        {{-- Summary Stats --}}
        @php
            $totalCount = $incidents->count();
            $openCount = $incidents->whereNotIn('status', ['resolved', 'closed'])->count();
            $criticalCount = $incidents->where('priority', 'critical')->count();
            $resolvedCount = $incidents->whereIn('status', ['resolved', 'closed'])->count();
            $publicCount = $incidents->where('source', 'public')->count();
        @endphp
        <div class="summary-stats">
            <div class="stat-box">
                <div class="number">{{ $totalCount }}</div>
                <div class="label">Total Cases</div>
            </div>
            <div class="stat-box open">
                <div class="number">{{ $openCount }}</div>
                <div class="label">Open</div>
            </div>
            <div class="stat-box critical">
                <div class="number">{{ $criticalCount }}</div>
                <div class="label">Critical</div>
            </div>
            <div class="stat-box resolved">
                <div class="number">{{ $resolvedCount }}</div>
                <div class="label">Resolved</div>
            </div>
            <div class="stat-box">
                <div class="number">{{ $publicCount }}</div>
                <div class="label">Public Reports</div>
            </div>
        </div>

        {{-- Cases Table --}}
        <table>
            <thead>
                <tr>
                    <th style="width: 90px;">Case #</th>
                    <th style="width: 180px;">Title</th>
                    <th>Category</th>
                    <th>School/Location</th>
                    <th style="width: 80px;">Date</th>
                    <th style="width: 70px;">Priority</th>
                    <th style="width: 70px;">Source</th>
                    <th style="width: 90px;">Status</th>
                    <th>Reporter</th>
                </tr>
            </thead>
            <tbody>
                @forelse($incidents as $incident)
                <tr>
                    <td>
                        <strong>{{ $incident->incident_number }}</strong>
                        @if($incident->immediate_action_required)
                        <br><span class="badge badge-urgent">URGENT</span>
                        @endif
                    </td>
                    <td>
                        <div class="truncate" title="{{ $incident->title }}">{{ $incident->title }}</div>
                    </td>
                    <td>{{ $incident->category_label }}</td>
                    <td>
                        {{ $incident->school_name ?? '—' }}
                        @if($incident->school_county)
                        <br><small style="color:#6b7280;">{{ $incident->school_county }}</small>
                        @endif
                    </td>
                    <td>{{ $incident->incident_date?->format('M d, Y') ?? '—' }}</td>
                    <td>
                        <span class="badge badge-{{ $incident->priority }}">{{ $incident->priority_label }}</span>
                    </td>
                    <td>
                        <span class="badge badge-{{ $incident->source }}">{{ $incident->source_label }}</span>
                    </td>
                    <td>
                        <span class="badge badge-{{ $incident->status }}">{{ $incident->status_label }}</span>
                    </td>
                    <td style="font-size: 9px;">
                        @if($incident->reporter)
                            {{ $incident->reporter->name }}
                        @elseif($incident->public_reporter_name)
                            {{ $incident->public_reporter_name }}
                        @else
                            <em style="color:#9ca3af;">Anonymous</em>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 40px; color: #9ca3af;">
                        No cases found matching the criteria.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Footer --}}
        <div class="footer">
            <span>Ministry of Education · Republic of Liberia · Bureau of Student Personnel Services</span>
            <span>Confidential Document · Page 1</span>
        </div>
    </div>

    <script>
        // Auto-trigger print if format is pdf
        @if($format === 'pdf')
        // window.onload = () => window.print();
        @endif
    </script>
</body>
</html>
