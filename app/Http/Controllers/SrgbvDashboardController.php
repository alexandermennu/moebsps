<?php

namespace App\Http\Controllers;

use App\Models\SrgbvCase;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SrgbvDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Access check: full-access, CGPC director, counselors, CGPC staff
        $canAccess = $user->hasFullAccess()
            || ($user->isDirector() && $user->division && $user->division->code === 'CGPC')
            || $user->isCounselor()
            || (in_array($user->role, [User::ROLE_SUPERVISOR, User::ROLE_COORDINATOR]) && $user->division && $user->division->code === 'CGPC');

        if (!$canAccess) abort(403);

        $canManage = $user->hasFullAccess() || ($user->isDirector() && $user->division && $user->division->code === 'CGPC');

        // Stats
        $totalCases = SrgbvCase::count();
        $openCases = SrgbvCase::open()->count();
        $closedCases = SrgbvCase::closed()->count();
        $criticalCases = SrgbvCase::critical()->open()->count();
        $followUpDue = SrgbvCase::requiringFollowUp()->count();

        // Cases by Status
        $casesByStatus = SrgbvCase::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Cases by Category
        $casesByCategory = SrgbvCase::select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        // Cases by Priority
        $casesByPriority = SrgbvCase::select('priority', DB::raw('count(*) as total'))
            ->groupBy('priority')
            ->pluck('total', 'priority')
            ->toArray();

        // Monthly trend (last 12 months)
        $monthlyTrend = SrgbvCase::select(
                DB::raw("strftime('%Y-%m', created_at) as month"),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Recent cases
        $recentCases = SrgbvCase::with(['reporter', 'assignee'])
            ->latest()
            ->take(5)
            ->get();

        // Cases requiring follow-up
        $followUpCases = SrgbvCase::requiringFollowUp()
            ->with(['assignee'])
            ->orderBy('follow_up_date')
            ->take(5)
            ->get();

        // Resolution rate
        $resolutionRate = $totalCases > 0
            ? round(($closedCases / $totalCases) * 100)
            : 0;

        // Average days to resolve
        $avgResolutionDays = SrgbvCase::closed()
            ->whereNotNull('resolution_date')
            ->selectRaw('AVG(CAST(julianday(resolution_date) - julianday(created_at) AS INTEGER)) as avg_days')
            ->value('avg_days');

        return view('srgbv.dashboard', [
            'user' => $user,
            'canManage' => $canManage,
            'totalCases' => $totalCases,
            'openCases' => $openCases,
            'closedCases' => $closedCases,
            'criticalCases' => $criticalCases,
            'followUpDue' => $followUpDue,
            'casesByStatus' => $casesByStatus,
            'casesByCategory' => $casesByCategory,
            'casesByPriority' => $casesByPriority,
            'monthlyTrend' => $monthlyTrend,
            'recentCases' => $recentCases,
            'followUpCases' => $followUpCases,
            'resolutionRate' => $resolutionRate,
            'avgResolutionDays' => $avgResolutionDays ? round($avgResolutionDays) : null,
        ]);
    }
}
