<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;

class PublicReporterController extends Controller
{
    /**
     * Display a listing of public reporters for SRGBV incidents.
     */
    public function srgbvIndex(Request $request)
    {
        $query = Incident::query()
            ->where('type', 'srgbv')
            ->whereNotNull('public_reporter_phone')
            ->where('public_reporter_phone', '!=', '')
            ->where('phone_verified', true)
            ->orderBy('created_at', 'desc');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('public_reporter_name', 'like', "%{$search}%")
                  ->orWhere('public_reporter_phone', 'like', "%{$search}%")
                  ->orWhere('public_reporter_email', 'like', "%{$search}%")
                  ->orWhere('incident_number', 'like', "%{$search}%");
            });
        }

        $reporters = $query->paginate(20)->withQueryString();

        return view('sir.public-reporters.index', [
            'reporters' => $reporters,
            'type' => 'srgbv',
            'title' => 'SRGBV Public Reporters',
            'backRoute' => route('sir.srgbv.dashboard'),
            'backLabel' => 'SRGBV Dashboard',
        ]);
    }

    /**
     * Display a listing of public reporters for Other Incidents.
     */
    public function otherIndex(Request $request)
    {
        $query = Incident::query()
            ->where('type', 'other_incident')
            ->whereNotNull('public_reporter_phone')
            ->where('public_reporter_phone', '!=', '')
            ->where('phone_verified', true)
            ->orderBy('created_at', 'desc');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('public_reporter_name', 'like', "%{$search}%")
                  ->orWhere('public_reporter_phone', 'like', "%{$search}%")
                  ->orWhere('public_reporter_email', 'like', "%{$search}%")
                  ->orWhere('incident_number', 'like', "%{$search}%");
            });
        }

        $reporters = $query->paginate(20)->withQueryString();

        return view('sir.public-reporters.index', [
            'reporters' => $reporters,
            'type' => 'other_incident',
            'title' => 'Other Incidents Public Reporters',
            'backRoute' => route('sir.other.dashboard'),
            'backLabel' => 'Other Incidents Dashboard',
        ]);
    }

    /**
     * Show reporter detail with their incidents.
     */
    public function show(Request $request, $phone)
    {
        $phone = urldecode($phone);
        
        $incidents = Incident::where('public_reporter_phone', $phone)
            ->where('phone_verified', true)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($incidents->isEmpty()) {
            abort(404, 'Reporter not found');
        }

        $reporter = (object) [
            'name' => $incidents->first()->public_reporter_name,
            'phone' => $phone,
            'email' => $incidents->first()->public_reporter_email,
            'relationship' => $incidents->first()->public_reporter_relationship,
            'total_reports' => $incidents->count(),
            'first_report' => $incidents->last()->created_at,
            'latest_report' => $incidents->first()->created_at,
        ];

        return view('sir.public-reporters.show', [
            'reporter' => $reporter,
            'incidents' => $incidents,
        ]);
    }
}
