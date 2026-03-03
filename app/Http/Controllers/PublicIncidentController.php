<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\IncidentFile;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PublicIncidentController extends Controller
{
    /**
     * Show the public report form.
     */
    public function create()
    {
        return view('sir.public.report');
    }

    /**
     * Store a public incident report.
     */
    public function store(Request $request)
    {
        // Honeypot check — if this hidden field is filled, it's a bot
        if ($request->filled('website_url')) {
            // Silently redirect as if successful (don't reveal to bots)
            return redirect()->route('sir.public.confirm', ['code' => 'SUBMITTED']);
        }

        $validated = $request->validate([
            'type' => ['required', Rule::in(array_keys(Incident::TYPES))],
            'category' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'incident_date' => 'required|date|before_or_equal:today',
            'incident_location' => 'nullable|string|max:255',
            'school_name' => 'nullable|string|max:255',
            'school_county' => 'nullable|string|max:255',
            'school_district' => 'nullable|string|max:255',
            'school_level' => ['nullable', Rule::in(array_keys(Incident::SCHOOL_LEVELS))],
            'victim_name' => 'nullable|string|max:255',
            'victim_age' => 'nullable|integer|min:1|max:100',
            'victim_gender' => 'nullable|string|max:50',
            'victim_grade' => 'nullable|string|max:50',
            'perpetrator_name' => 'nullable|string|max:255',
            'perpetrator_type' => ['nullable', Rule::in(array_keys(Incident::PERPETRATOR_TYPES))],
            'perpetrator_description' => 'nullable|string|max:2000',
            'public_reporter_name' => 'nullable|string|max:255',
            'public_reporter_phone' => 'nullable|string|max:50',
            'public_reporter_email' => 'nullable|email|max:255',
            'public_reporter_relationship' => ['nullable', Rule::in(array_keys(Incident::REPORTER_RELATIONSHIPS))],
            'is_confidential' => 'boolean',
            'risk_level' => ['nullable', Rule::in(array_keys(Incident::RISK_LEVELS))],
            'immediate_action_required' => 'boolean',
            'files.*' => 'nullable|file|max:5120', // 5MB limit for public
        ]);

        $trackingCode = Incident::generateTrackingCode();

        $incident = Incident::create([
            'incident_number' => Incident::generateIncidentNumber('public'),
            'type' => $validated['type'],
            'category' => $validated['category'],
            'source' => Incident::SOURCE_PUBLIC,
            'status' => Incident::STATUS_REPORTED,
            'priority' => 'medium', // Default for public reports — staff can escalate
            'title' => $validated['title'],
            'description' => $validated['description'],
            'incident_date' => $validated['incident_date'],
            'incident_location' => $validated['incident_location'] ?? null,
            'school_name' => $validated['school_name'] ?? null,
            'school_county' => $validated['school_county'] ?? null,
            'school_district' => $validated['school_district'] ?? null,
            'school_level' => $validated['school_level'] ?? null,
            'victim_name' => $validated['victim_name'] ?? null,
            'victim_age' => $validated['victim_age'] ?? null,
            'victim_gender' => $validated['victim_gender'] ?? null,
            'victim_grade' => $validated['victim_grade'] ?? null,
            'perpetrator_name' => $validated['perpetrator_name'] ?? null,
            'perpetrator_type' => $validated['perpetrator_type'] ?? null,
            'perpetrator_description' => $validated['perpetrator_description'] ?? null,
            'public_reporter_name' => $validated['public_reporter_name'] ?? null,
            'public_reporter_phone' => $validated['public_reporter_phone'] ?? null,
            'public_reporter_email' => $validated['public_reporter_email'] ?? null,
            'public_reporter_relationship' => $validated['public_reporter_relationship'] ?? null,
            'tracking_code' => $trackingCode,
            'is_confidential' => $request->boolean('is_confidential', true),
            'risk_level' => $validated['risk_level'] ?? null,
            'immediate_action_required' => $request->boolean('immediate_action_required'),
        ]);

        // Handle file uploads (limited for public)
        if ($request->hasFile('files')) {
            $files = array_slice($request->file('files'), 0, 3); // Max 3 files from public
            foreach ($files as $file) {
                $path = $file->store('sir-public/' . $incident->id, config('filesystems.uploads', 'public'));
                IncidentFile::create([
                    'incident_id' => $incident->id,
                    'uploaded_by' => null,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'category' => 'evidence',
                ]);
            }
        }

        return redirect()->route('sir.public.confirm', ['code' => $trackingCode]);
    }

    /**
     * Show the confirmation page with tracking code.
     */
    public function confirm(Request $request)
    {
        $code = $request->query('code', '');
        return view('sir.public.confirm', ['trackingCode' => $code]);
    }

    /**
     * Show the tracking form.
     */
    public function trackForm()
    {
        return view('sir.public.track');
    }

    /**
     * Look up an incident by tracking code.
     */
    public function track(Request $request)
    {
        $request->validate([
            'tracking_code' => 'required|string|max:20',
        ]);

        $incident = Incident::where('tracking_code', strtoupper($request->tracking_code))->first();

        if (!$incident) {
            return back()->withErrors(['tracking_code' => 'No incident found with that tracking code. Please check and try again.'])->withInput();
        }

        return view('sir.public.track', [
            'incident' => $incident,
            'trackingCode' => $request->tracking_code,
        ]);
    }
}
