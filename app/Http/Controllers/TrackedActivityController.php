<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\SystemSetting;
use App\Models\TrackedActivity;
use Illuminate\Http\Request;

class TrackedActivityController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->hasPersonalAccessOnly()) {
            abort(403, 'You do not have access to tracked activities.');
        }

        $query = TrackedActivity::with(['division', 'latestWeeklyUpdate.submitter']);

        // Scope by division for division-scoped users
        if ($user->isDivisionScoped()) {
            $query->where('division_id', $user->division_id);
        }

        // Filters
        if ($request->filled('division_id') && !$user->isDivisionScoped()) {
            $query->where('division_id', $request->division_id);
        }

        if ($request->filled('status')) {
            $query->where('current_status', $request->status);
        }

        if ($request->filled('flag')) {
            if ($request->flag === 'stale') {
                $query->stale();
            } elseif ($request->flag === 'repeated') {
                $query->repeated();
            } elseif ($request->flag === 'flagged') {
                $query->flagged();
            }
        }

        if ($request->filled('search')) {
            $query->where('activity_text', 'like', '%' . $request->search . '%');
        }

        // Stats
        $statsQuery = TrackedActivity::query();
        if ($user->isDivisionScoped()) {
            $statsQuery->where('division_id', $user->division_id);
        }
        if ($request->filled('division_id') && !$user->isDivisionScoped()) {
            $statsQuery->where('division_id', $request->division_id);
        }

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'active' => (clone $statsQuery)->active()->count(),
            'stale' => (clone $statsQuery)->stale()->count(),
            'repeated' => (clone $statsQuery)->repeated()->count(),
            'completed' => (clone $statsQuery)->where('current_status', 'completed')->count(),
        ];

        $settings = [
            'stale_weeks' => SystemSetting::getValue('stale_activity_weeks', 3),
            'repeat_threshold' => SystemSetting::getValue('repeat_threshold', 2),
        ];

        $activities = $query->latest('last_reported_at')->paginate(20)->withQueryString();
        $divisions = $user->isDivisionScoped() ? collect() : Division::where('is_active', true)->orderBy('name')->get();

        return view('tracked-activities.index', compact('activities', 'stats', 'settings', 'divisions', 'user'));
    }
}
