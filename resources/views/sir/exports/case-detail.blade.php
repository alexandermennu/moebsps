<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Case {{ $incident->incident_number }} - Ministry of Education</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            font-size: 11px; 
            line-height: 1.5; 
            color: #1f2937;
            background: white;
        }
        
        /* Print styles */
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            .page-break { page-break-after: always; }
            @page { margin: 0.6in; size: A4; }
        }
        
        /* Screen styles for preview */
        @media screen {
            body { padding: 20px; background: #f3f4f6; }
            .document { 
                max-width: 800px; 
                margin: 0 auto; 
                background: white; 
                padding: 50px;
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
            margin-bottom: 25px;
        }
        .logo-placeholder {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #991b1b 0%, #7f1d1d 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 11px;
            text-align: center;
        }
        .header-text h1 {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 2px;
        }
        .header-text h2 {
            font-size: 14px;
            font-weight: 600;
            color: #991b1b;
            margin-bottom: 4px;
        }
        .header-text p {
            font-size: 11px;
            color: #6b7280;
        }
        
        /* Case header */
        .case-header {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .case-number {
            font-size: 24px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 8px;
        }
        .case-title {
            font-size: 16px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 12px;
        }
        .case-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        /* Badges */
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-critical { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .badge-high { background: #fff7ed; color: #ea580c; border: 1px solid #fed7aa; }
        .badge-medium { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
        .badge-low { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
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
        .badge-srgbv { background: #fef2f2; color: #dc2626; }
        
        /* Section */
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #991b1b;
            padding-bottom: 8px;
            border-bottom: 2px solid #fecaca;
            margin-bottom: 15px;
        }
        
        /* Info grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        .info-item {
            padding: 10px 12px;
            background: #f9fafb;
            border-radius: 6px;
        }
        .info-item.full-width {
            grid-column: span 2;
        }
        .info-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6b7280;
            margin-bottom: 3px;
        }
        .info-value {
            font-size: 12px;
            color: #111827;
            font-weight: 500;
        }
        .info-value.empty {
            color: #9ca3af;
            font-style: italic;
            font-weight: 400;
        }
        
        /* Description */
        .description {
            background: #f9fafb;
            border-radius: 6px;
            padding: 15px;
            font-size: 11px;
            line-height: 1.6;
            color: #374151;
            white-space: pre-wrap;
        }
        
        /* Timeline/Notes */
        .timeline {
            border-left: 2px solid #e5e7eb;
            padding-left: 20px;
            margin-left: 8px;
        }
        .timeline-item {
            position: relative;
            padding-bottom: 15px;
        }
        .timeline-item:last-child {
            padding-bottom: 0;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -25px;
            top: 4px;
            width: 10px;
            height: 10px;
            background: #991b1b;
            border-radius: 50%;
        }
        .timeline-date {
            font-size: 9px;
            color: #6b7280;
            margin-bottom: 4px;
        }
        .timeline-author {
            font-size: 10px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 4px;
        }
        .timeline-content {
            font-size: 11px;
            color: #4b5563;
            background: #f9fafb;
            padding: 10px;
            border-radius: 6px;
        }
        
        /* Files table */
        .files-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        .files-table th {
            background: #f3f4f6;
            padding: 8px;
            text-align: left;
            font-weight: 600;
            color: #374151;
        }
        .files-table td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        /* Footer */
        .footer {
            margin-top: 40px;
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
        .print-btn.back {
            background: #6b7280;
            color: white;
        }
        .print-btn.back:hover { background: #4b5563; }
        
        /* Confidential watermark */
        @if($incident->is_confidential)
        .document::before {
            content: 'CONFIDENTIAL';
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            font-weight: bold;
            color: rgba(220, 38, 38, 0.05);
            pointer-events: none;
            z-index: 0;
        }
        @endif
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
                <h2>Incident Case Report</h2>
                <p>Bureau of Student Personnel Services · School Incident Reporting System</p>
            </div>
        </div>

        {{-- Case Header --}}
        <div class="case-header">
            <div class="case-number">{{ $incident->incident_number }}</div>
            <div class="case-title">{{ $incident->title }}</div>
            <div class="case-meta">
                <span class="badge badge-{{ $incident->type }}">{{ $incident->type_label }}</span>
                <span class="badge badge-{{ $incident->priority }}">{{ $incident->priority_label }} Priority</span>
                <span class="badge badge-{{ $incident->status }}">{{ $incident->status_label }}</span>
                <span class="badge badge-{{ $incident->source }}">{{ $incident->source_label }}</span>
                @if($incident->is_confidential)
                <span class="badge" style="background:#fef2f2;color:#dc2626;">Confidential</span>
                @endif
                @if($incident->immediate_action_required)
                <span class="badge badge-urgent">URGENT</span>
                @endif
            </div>
        </div>

        {{-- Basic Information --}}
        <div class="section">
            <div class="section-title">Incident Information</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Category</div>
                    <div class="info-value">{{ $incident->category_label }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Incident Date</div>
                    <div class="info-value">{{ $incident->incident_date?->format('F d, Y') ?? 'Not specified' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Date Reported</div>
                    <div class="info-value">{{ $incident->created_at->format('F d, Y \a\t h:i A') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Risk Level</div>
                    <div class="info-value {{ !$incident->risk_level ? 'empty' : '' }}">{{ $incident->risk_level_label ?? 'Not assessed' }}</div>
                </div>
                <div class="info-item full-width">
                    <div class="info-label">Location</div>
                    <div class="info-value {{ !$incident->incident_location ? 'empty' : '' }}">{{ $incident->incident_location ?? 'Not specified' }}</div>
                </div>
            </div>
        </div>

        {{-- Description --}}
        <div class="section">
            <div class="section-title">Description</div>
            <div class="description">{{ $incident->description }}</div>
        </div>

        {{-- School Information --}}
        <div class="section">
            <div class="section-title">School Information</div>
            <div class="info-grid">
                <div class="info-item full-width">
                    <div class="info-label">School Name</div>
                    <div class="info-value {{ !$incident->school_name ? 'empty' : '' }}">{{ $incident->school_name ?? 'Not specified' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">County</div>
                    <div class="info-value {{ !$incident->school_county ? 'empty' : '' }}">{{ $incident->school_county ?? 'Not specified' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">District</div>
                    <div class="info-value {{ !$incident->school_district ? 'empty' : '' }}">{{ $incident->school_district ?? 'Not specified' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">School Level</div>
                    <div class="info-value {{ !$incident->school_level ? 'empty' : '' }}">{{ $incident->school_level_label ?? 'Not specified' }}</div>
                </div>
            </div>
        </div>

        {{-- Victim Information --}}
        @if($incident->victim_name || $incident->victim_age || $incident->victim_gender)
        <div class="section">
            <div class="section-title">Victim Information</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Name</div>
                    <div class="info-value {{ !$incident->victim_name ? 'empty' : '' }}">{{ $incident->victim_name ?? 'Not provided' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Age</div>
                    <div class="info-value {{ !$incident->victim_age ? 'empty' : '' }}">{{ $incident->victim_age ? $incident->victim_age . ' years' : 'Not provided' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Gender</div>
                    <div class="info-value {{ !$incident->victim_gender ? 'empty' : '' }}">{{ ucfirst($incident->victim_gender) ?? 'Not provided' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Grade</div>
                    <div class="info-value {{ !$incident->victim_grade ? 'empty' : '' }}">{{ $incident->victim_grade ?? 'Not provided' }}</div>
                </div>
            </div>
        </div>
        @endif

        {{-- Perpetrator Information --}}
        @if($incident->perpetrator_name || $incident->perpetrator_type || $incident->perpetrator_description)
        <div class="section">
            <div class="section-title">Alleged Perpetrator Information</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Name</div>
                    <div class="info-value {{ !$incident->perpetrator_name ? 'empty' : '' }}">{{ $incident->perpetrator_name ?? 'Not provided' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Type</div>
                    <div class="info-value {{ !$incident->perpetrator_type ? 'empty' : '' }}">{{ $incident->perpetrator_type_label ?? 'Not specified' }}</div>
                </div>
                @if($incident->perpetrator_description)
                <div class="info-item full-width">
                    <div class="info-label">Description</div>
                    <div class="info-value">{{ $incident->perpetrator_description }}</div>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Reporter Information --}}
        <div class="section">
            <div class="section-title">Reporter Information</div>
            <div class="info-grid">
                @if($incident->reporter)
                <div class="info-item">
                    <div class="info-label">Reported By</div>
                    <div class="info-value">{{ $incident->reporter->name }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Role</div>
                    <div class="info-value">{{ $incident->reporter->role_label }}</div>
                </div>
                @elseif($incident->isPublicReport())
                <div class="info-item">
                    <div class="info-label">Reporter Name</div>
                    <div class="info-value {{ !$incident->public_reporter_name ? 'empty' : '' }}">{{ $incident->public_reporter_name ?? 'Anonymous' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Phone</div>
                    <div class="info-value {{ !$incident->public_reporter_phone ? 'empty' : '' }}">{{ $incident->public_reporter_phone ?? 'Not provided' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value {{ !$incident->public_reporter_email ? 'empty' : '' }}">{{ $incident->public_reporter_email ?? 'Not provided' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Relationship to Victim</div>
                    <div class="info-value {{ !$incident->public_reporter_relationship ? 'empty' : '' }}">{{ $incident->reporter_relationship_label ?? 'Not specified' }}</div>
                </div>
                @else
                <div class="info-item full-width">
                    <div class="info-label">Source</div>
                    <div class="info-value empty">Anonymous submission</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Case Management --}}
        <div class="section">
            <div class="section-title">Case Management</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Assigned To</div>
                    <div class="info-value {{ !$incident->assignee ? 'empty' : '' }}">{{ $incident->assignee?->name ?? 'Unassigned' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Division</div>
                    <div class="info-value {{ !$incident->division ? 'empty' : '' }}">{{ $incident->division?->name ?? 'Not assigned' }}</div>
                </div>
                @if($incident->tracking_code)
                <div class="info-item">
                    <div class="info-label">Tracking Code</div>
                    <div class="info-value" style="font-family: monospace;">{{ $incident->tracking_code }}</div>
                </div>
                @endif
                @if($incident->follow_up_date)
                <div class="info-item">
                    <div class="info-label">Follow-up Date</div>
                    <div class="info-value">{{ $incident->follow_up_date->format('F d, Y') }}</div>
                </div>
                @endif
                @if($incident->resolution_date)
                <div class="info-item">
                    <div class="info-label">Resolution Date</div>
                    <div class="info-value">{{ $incident->resolution_date->format('F d, Y') }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Resolution --}}
        @if($incident->resolution)
        <div class="section">
            <div class="section-title">Resolution</div>
            <div class="description">{{ $incident->resolution }}</div>
        </div>
        @endif

        {{-- Case Notes --}}
        @if($incident->notes->count() > 0)
        <div class="section">
            <div class="section-title">Case Notes ({{ $incident->notes->count() }})</div>
            <div class="timeline">
                @foreach($incident->notes->sortByDesc('created_at') as $note)
                <div class="timeline-item">
                    <div class="timeline-date">{{ $note->created_at->format('M d, Y \a\t h:i A') }}</div>
                    <div class="timeline-author">{{ $note->user?->name ?? 'System' }} @if($note->note_type !== 'general')· {{ ucfirst(str_replace('_', ' ', $note->note_type)) }}@endif</div>
                    <div class="timeline-content">{{ $note->note }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Attached Files --}}
        @if($incident->files->count() > 0)
        <div class="section">
            <div class="section-title">Attached Files ({{ $incident->files->count() }})</div>
            <table class="files-table">
                <thead>
                    <tr>
                        <th>File Name</th>
                        <th>Category</th>
                        <th>Size</th>
                        <th>Uploaded</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($incident->files as $file)
                    <tr>
                        <td>{{ $file->file_name }}</td>
                        <td>{{ ucfirst($file->category) }}</td>
                        <td>{{ number_format($file->file_size / 1024, 1) }} KB</td>
                        <td>{{ $file->created_at->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- Footer --}}
        <div class="footer">
            <span>Ministry of Education · Republic of Liberia · Bureau of Student Personnel Services</span>
            <span>Generated {{ $exportDate->format('M d, Y h:i A') }} by {{ $user->name }}</span>
        </div>
    </div>

    <script>
        @if($format === 'pdf')
        // Optionally auto-print for PDF
        @endif
    </script>
</body>
</html>
