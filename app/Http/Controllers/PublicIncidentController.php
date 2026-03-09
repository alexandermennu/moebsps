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
            'type' => ['required', Rule::in(array_keys(Incident::ALL_TYPES))],
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
            'victim_age' => ['nullable', Rule::in(array_keys(Incident::VICTIM_AGE_RANGES))],
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
            'reporter_type' => 'nullable|string|in:anonymous,verified',
            'verified_phone' => 'nullable|string|max:50',
        ]);

        $trackingCode = Incident::generateTrackingCode($validated['type']);

        // Determine priority: verified reporters may get slightly higher default
        $priority = 'medium';
        $reporterType = $request->input('reporter_type', 'anonymous');
        $verifiedPhone = $request->input('verified_phone');
        
        // If phone was verified through OTP, use that phone
        $reporterPhone = $verifiedPhone ?: ($validated['public_reporter_phone'] ?? null);

        $incident = Incident::create([
            'incident_number' => Incident::generateIncidentNumber($validated['type'], 'public'),
            'type' => $validated['type'],
            'category' => $validated['category'],
            'source' => $reporterType === 'verified' ? Incident::SOURCE_PUBLIC : Incident::SOURCE_PUBLIC,
            'status' => Incident::STATUS_REPORTED,
            'priority' => $priority,
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
            'public_reporter_phone' => $reporterPhone,
            'public_reporter_email' => $validated['public_reporter_email'] ?? null,
            'public_reporter_relationship' => $validated['public_reporter_relationship'] ?? null,
            'phone_verified' => $reporterType === 'verified' && !empty($verifiedPhone),
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
            'tracking_code' => ['required', 'string', 'min:15', 'max:20', 'regex:/^SIR-(SRGBV|OI)-[A-Z]{3}-\d{4}$/i'],
        ], [
            'tracking_code.regex' => 'Invalid tracking code format. Expected format: SIR-SRGBV-ABC-1234 or SIR-OI-ABC-1234',
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

    /**
     * Send OTP to phone number for verification.
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
        ]);

        $phone = $request->phone;
        $phoneDigits = preg_replace('/\D/', '', $phone);
        
        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store OTP in session (expires in 10 minutes)
        session([
            'otp_code' => $otp,
            'otp_phone' => $phoneDigits,
            'otp_expires' => now()->addMinutes(10),
        ]);

        // Log OTP for debugging
        \Log::info("OTP for {$phone}: {$otp}");

        // Send SMS via Twilio if configured
        if (config('services.twilio.sid') && config('services.twilio.token')) {
            try {
                $twilio = new \Twilio\Rest\Client(
                    config('services.twilio.sid'),
                    config('services.twilio.token')
                );
                
                $twilio->messages->create($phone, [
                    'from' => config('services.twilio.from'),
                    'body' => "Your MOE incident report verification code is: {$otp}. Valid for 10 minutes."
                ]);
                
                \Log::info("SMS sent successfully to {$phone}");
            } catch (\Exception $e) {
                \Log::error("Failed to send SMS: " . $e->getMessage());
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send verification code. Please try again.',
                ], 500);
            }
        } else {
            // Twilio not configured - log warning
            \Log::warning("Twilio not configured. OTP not sent via SMS.");
        }

        return response()->json([
            'success' => true,
            'message' => 'Verification code sent',
            // In development, return the OTP for testing
            'debug_otp' => app()->environment('local') ? $otp : null,
        ]);
    }

    /**
     * Verify OTP code.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
            'code' => 'required|string|size:6',
        ]);

        $phone = preg_replace('/\D/', '', $request->phone);
        $storedOtp = session('otp_code');
        $storedPhone = session('otp_phone');
        $expires = session('otp_expires');

        // Validate OTP
        if (!$storedOtp || !$storedPhone || !$expires) {
            return response()->json([
                'success' => false,
                'message' => 'No verification code found. Please request a new one.',
            ]);
        }

        if (now()->isAfter($expires)) {
            session()->forget(['otp_code', 'otp_phone', 'otp_expires']);
            return response()->json([
                'success' => false,
                'message' => 'Code expired. Please request a new one.',
            ]);
        }

        if ($storedPhone !== $phone) {
            return response()->json([
                'success' => false,
                'message' => 'Phone number mismatch.',
            ]);
        }

        if ($storedOtp !== $request->code) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid code. Please try again.',
            ]);
        }

        // OTP verified! Clear session and mark as verified
        session()->forget(['otp_code', 'otp_phone', 'otp_expires']);
        session(['verified_phone' => $request->phone]);

        return response()->json([
            'success' => true,
            'message' => 'Phone verified successfully',
        ]);
    }
}
