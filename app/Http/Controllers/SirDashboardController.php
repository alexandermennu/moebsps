<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SirDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Access check
        $canAccess = $user->hasFullAccess()
            || ($user->isDirector() && $user->division && $user->division->code === 'CGPC')
            || $user->isCounselor()
            || (in_array($user->role, [User::ROLE_SUPERVISOR, User::ROLE_COORDINATOR]) && $user->division && $user->division->code === 'CGPC');

        if (!$canAccess) abort(403);

        $canManage = $user->hasFullAccess() || ($user->isDirector() && $user->division && $user->division->code === 'CGPC');

        // ── Overall Stats ──
        $totalIncidents = Incident::count();
        $openIncidents = Incident::open()->count();
        $closedIncidents = Incident::closed()->count();
        $criticalIncidents = Incident::critical()->open()->count();
        $followUpDue = Incident::requiringFollowUp()->count();
        $immediateAction = Incident::requiringImmediateAction()->count();

        // ── Source Breakdown ──
        $internalCount = Incident::internal()->count();
        $publicCount = Incident::publicReports()->count();

        // ── By Type ──
        $byType = Incident::select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->pluck('total', 'type')
            ->toArray();

        // ── By Status ──
        $byStatus = Incident::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // ── By Priority ──
        $byPriority = Incident::select('priority', DB::raw('count(*) as total'))
            ->groupBy('priority')
            ->pluck('total', 'priority')
            ->toArray();

        // ── By Source ──
        $bySource = Incident::select('source', DB::raw('count(*) as total'))
            ->groupBy('source')
            ->pluck('total', 'source')
            ->toArray();

        // ── SRGBV-specific stats ──
        $srgbvTotal = Incident::srgbv()->count();
        $srgbvOpen = Incident::srgbv()->open()->count();
        $srgbvCritical = Incident::srgbv()->critical()->open()->count();

        // ── Monthly Trend (last 12 months) ──
        $driver = DB::connection()->getDriverName();
        $monthExpr = match($driver) {
            'mysql', 'mariadb' => "DATE_FORMAT(created_at, '%Y-%m')",
            'pgsql' => "TO_CHAR(created_at, 'YYYY-MM')",
            default => "strftime('%Y-%m', created_at)",
        };
        $monthlyTrend = Incident::select(
                DB::raw("$monthExpr as month"),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // ── Monthly by Type (for stacked chart) ──
        $monthlyByType = Incident::select(
                DB::raw("$monthExpr as month"),
                'type',
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month', 'type')
            ->orderBy('month')
            ->get()
            ->groupBy('month')
            ->map(fn($group) => $group->pluck('total', 'type')->toArray())
            ->toArray();

        // ── Recent Incidents ──
        $recentIncidents = Incident::with(['reporter', 'assignee'])
            ->latest()
            ->take(8)
            ->get();

        // ── Incidents Requiring Follow-Up ──
        $followUpIncidents = Incident::requiringFollowUp()
            ->with(['assignee'])
            ->orderBy('follow_up_date')
            ->take(5)
            ->get();

        // ── Requiring Immediate Action ──
        $urgentIncidents = Incident::requiringImmediateAction()
            ->with(['reporter', 'assignee'])
            ->latest()
            ->take(5)
            ->get();

        // ── Resolution Stats ──
        $resolutionRate = $totalIncidents > 0
            ? round(($closedIncidents / $totalIncidents) * 100)
            : 0;

        $avgDaysExpr = match($driver) {
            'mysql', 'mariadb' => 'AVG(DATEDIFF(resolution_date, created_at))',
            'pgsql' => 'AVG(EXTRACT(EPOCH FROM (resolution_date::timestamp - created_at::timestamp)) / 86400)',
            default => 'AVG(CAST(julianday(resolution_date) - julianday(created_at) AS INTEGER))',
        };
        $avgResolutionDays = Incident::closed()
            ->whereNotNull('resolution_date')
            ->selectRaw("$avgDaysExpr as avg_days")
            ->value('avg_days');

        // ── Top Counties (where incidents happen) ──
        $topCounties = Incident::select('school_county', DB::raw('count(*) as total'))
            ->whereNotNull('school_county')
            ->groupBy('school_county')
            ->orderByDesc('total')
            ->take(10)
            ->pluck('total', 'school_county')
            ->toArray();

        return view('sir.dashboard', [
            'user' => $user,
            'canManage' => $canManage,
            'totalIncidents' => $totalIncidents,
            'openIncidents' => $openIncidents,
            'closedIncidents' => $closedIncidents,
            'criticalIncidents' => $criticalIncidents,
            'followUpDue' => $followUpDue,
            'immediateAction' => $immediateAction,
            'internalCount' => $internalCount,
            'publicCount' => $publicCount,
            'byType' => $byType,
            'byStatus' => $byStatus,
            'byPriority' => $byPriority,
            'bySource' => $bySource,
            'srgbvTotal' => $srgbvTotal,
            'srgbvOpen' => $srgbvOpen,
            'srgbvCritical' => $srgbvCritical,
            'monthlyTrend' => $monthlyTrend,
            'monthlyByType' => $monthlyByType,
            'recentIncidents' => $recentIncidents,
            'followUpIncidents' => $followUpIncidents,
            'urgentIncidents' => $urgentIncidents,
            'resolutionRate' => $resolutionRate,
            'avgResolutionDays' => $avgResolutionDays ? round($avgResolutionDays) : null,
            'topCounties' => $topCounties,
        ]);
    }
}
