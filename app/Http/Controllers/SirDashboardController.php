<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SirDashboardController extends Controller
{
    /**
     * SIR Landing Page — two module plates (SRGBV + Other Incidents).
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user->canAccessSir()) abort(403);

        $canAccessSrgbv = $user->canAccessSrgbv();
        $canAccessOther = $user->canAccessOtherIncidents();

        // Quick stats for each module
        $srgbvStats = $canAccessSrgbv ? [
            'total' => Incident::srgbv()->count(),
            'open' => Incident::srgbv()->open()->count(),
            'critical' => Incident::srgbv()->critical()->open()->count(),
        ] : null;

        $otherStats = $canAccessOther ? [
            'total' => Incident::where('type', '!=', Incident::TYPE_SRGBV)->count(),
            'open' => Incident::where('type', '!=', Incident::TYPE_SRGBV)->open()->count(),
            'critical' => Incident::where('type', '!=', Incident::TYPE_SRGBV)->critical()->open()->count(),
        ] : null;

        return view('sir.landing', [
            'user' => $user,
            'canAccessSrgbv' => $canAccessSrgbv,
            'canAccessOther' => $canAccessOther,
            'srgbvStats' => $srgbvStats,
            'otherStats' => $otherStats,
        ]);
    }

    /**
     * SRGBV Sub-Dashboard — detailed stats for SRGBV incidents only.
     */
    public function srgbvDashboard(Request $request)
    {
        $user = $request->user();
        if (!$user->canAccessSrgbv()) abort(403);

        $canManage = $user->canManageIncidents();

        $totalIncidents = Incident::srgbv()->count();
        $openIncidents = Incident::srgbv()->open()->count();
        $closedIncidents = Incident::srgbv()->closed()->count();
        $criticalIncidents = Incident::srgbv()->critical()->open()->count();
        $followUpDue = Incident::srgbv()->requiringFollowUp()->count();
        $immediateAction = Incident::srgbv()->requiringImmediateAction()->count();

        $internalCount = Incident::srgbv()->internal()->count();
        $publicCount = Incident::srgbv()->publicReports()->count();

        // By category (SRGBV-specific)
        $byCategory = Incident::srgbv()
            ->select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        // By status
        $byStatus = Incident::srgbv()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // By priority
        $byPriority = Incident::srgbv()
            ->select('priority', DB::raw('count(*) as total'))
            ->groupBy('priority')
            ->pluck('total', 'priority')
            ->toArray();

        // Monthly trend
        $driver = DB::connection()->getDriverName();
        $monthExpr = match($driver) {
            'mysql', 'mariadb' => "DATE_FORMAT(created_at, '%Y-%m')",
            'pgsql' => "TO_CHAR(created_at, 'YYYY-MM')",
            default => "strftime('%Y-%m', created_at)",
        };

        $monthlyTrend = Incident::srgbv()
            ->select(DB::raw("$monthExpr as month"), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Recent SRGBV incidents
        $recentIncidents = Incident::srgbv()
            ->with(['reporter', 'assignee'])
            ->latest()
            ->take(8)
            ->get();

        // Follow-up due
        $followUpIncidents = Incident::srgbv()
            ->requiringFollowUp()
            ->with(['assignee'])
            ->orderBy('follow_up_date')
            ->take(5)
            ->get();

        // Top counties
        $topCounties = Incident::srgbv()
            ->select('school_county', DB::raw('count(*) as total'))
            ->whereNotNull('school_county')
            ->groupBy('school_county')
            ->orderByDesc('total')
            ->take(10)
            ->pluck('total', 'school_county')
            ->toArray();

        // Resolution stats
        $resolutionRate = $totalIncidents > 0
            ? round(($closedIncidents / $totalIncidents) * 100)
            : 0;

        $avgDaysExpr = match($driver) {
            'mysql', 'mariadb' => 'AVG(DATEDIFF(resolution_date, created_at))',
            'pgsql' => 'AVG(EXTRACT(EPOCH FROM (resolution_date::timestamp - created_at::timestamp)) / 86400)',
            default => 'AVG(CAST(julianday(resolution_date) - julianday(created_at) AS INTEGER))',
        };
        $avgResolutionDays = Incident::srgbv()
            ->closed()
            ->whereNotNull('resolution_date')
            ->selectRaw("$avgDaysExpr as avg_days")
            ->value('avg_days');

        // Victim demographics
        $byGender = Incident::srgbv()
            ->select('victim_gender', DB::raw('count(*) as total'))
            ->whereNotNull('victim_gender')
            ->groupBy('victim_gender')
            ->pluck('total', 'victim_gender')
            ->toArray();

        return view('sir.srgbv-dashboard', [
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
            'byCategory' => $byCategory,
            'byStatus' => $byStatus,
            'byPriority' => $byPriority,
            'monthlyTrend' => $monthlyTrend,
            'recentIncidents' => $recentIncidents,
            'followUpIncidents' => $followUpIncidents,
            'topCounties' => $topCounties,
            'resolutionRate' => $resolutionRate,
            'avgResolutionDays' => $avgResolutionDays ? round($avgResolutionDays) : null,
            'byGender' => $byGender,
        ]);
    }

    /**
     * Other Incidents Sub-Dashboard — everything except SRGBV.
     */
    public function otherDashboard(Request $request)
    {
        $user = $request->user();
        if (!$user->canAccessOtherIncidents()) abort(403);

        $canManage = $user->canManageIncidents();

        $totalIncidents = Incident::where('type', '!=', Incident::TYPE_SRGBV)->count();
        $openIncidents = Incident::where('type', '!=', Incident::TYPE_SRGBV)->open()->count();
        $closedIncidents = Incident::where('type', '!=', Incident::TYPE_SRGBV)->closed()->count();
        $criticalIncidents = Incident::where('type', '!=', Incident::TYPE_SRGBV)->critical()->open()->count();
        $followUpDue = Incident::where('type', '!=', Incident::TYPE_SRGBV)->requiringFollowUp()->count();
        $immediateAction = Incident::where('type', '!=', Incident::TYPE_SRGBV)->requiringImmediateAction()->count();

        $internalCount = Incident::where('type', '!=', Incident::TYPE_SRGBV)->internal()->count();
        $publicCount = Incident::where('type', '!=', Incident::TYPE_SRGBV)->publicReports()->count();

        // By type (excluding SRGBV)
        $byType = Incident::where('type', '!=', Incident::TYPE_SRGBV)
            ->select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->pluck('total', 'type')
            ->toArray();

        // By status
        $byStatus = Incident::where('type', '!=', Incident::TYPE_SRGBV)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // By priority
        $byPriority = Incident::where('type', '!=', Incident::TYPE_SRGBV)
            ->select('priority', DB::raw('count(*) as total'))
            ->groupBy('priority')
            ->pluck('total', 'priority')
            ->toArray();

        // Monthly trend
        $driver = DB::connection()->getDriverName();
        $monthExpr = match($driver) {
            'mysql', 'mariadb' => "DATE_FORMAT(created_at, '%Y-%m')",
            'pgsql' => "TO_CHAR(created_at, 'YYYY-MM')",
            default => "strftime('%Y-%m', created_at)",
        };

        $monthlyTrend = Incident::where('type', '!=', Incident::TYPE_SRGBV)
            ->select(DB::raw("$monthExpr as month"), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Recent
        $recentIncidents = Incident::where('type', '!=', Incident::TYPE_SRGBV)
            ->with(['reporter', 'assignee'])
            ->latest()
            ->take(8)
            ->get();

        // Follow-up due
        $followUpIncidents = Incident::where('type', '!=', Incident::TYPE_SRGBV)
            ->where('follow_up_required', true)
            ->where(function ($q) {
                $q->whereNull('follow_up_date')
                  ->orWhere('follow_up_date', '<=', now()->addDays(3));
            })
            ->open()
            ->with(['assignee'])
            ->orderBy('follow_up_date')
            ->take(5)
            ->get();

        // Top counties
        $topCounties = Incident::where('type', '!=', Incident::TYPE_SRGBV)
            ->select('school_county', DB::raw('count(*) as total'))
            ->whereNotNull('school_county')
            ->groupBy('school_county')
            ->orderByDesc('total')
            ->take(10)
            ->pluck('total', 'school_county')
            ->toArray();

        // Resolution
        $resolutionRate = $totalIncidents > 0
            ? round(($closedIncidents / $totalIncidents) * 100)
            : 0;

        $avgDaysExpr = match($driver) {
            'mysql', 'mariadb' => 'AVG(DATEDIFF(resolution_date, created_at))',
            'pgsql' => 'AVG(EXTRACT(EPOCH FROM (resolution_date::timestamp - created_at::timestamp)) / 86400)',
            default => 'AVG(CAST(julianday(resolution_date) - julianday(created_at) AS INTEGER))',
        };
        $avgResolutionDays = Incident::where('type', '!=', Incident::TYPE_SRGBV)
            ->closed()
            ->whereNotNull('resolution_date')
            ->selectRaw("$avgDaysExpr as avg_days")
            ->value('avg_days');

        return view('sir.other-dashboard', [
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
            'monthlyTrend' => $monthlyTrend,
            'recentIncidents' => $recentIncidents,
            'followUpIncidents' => $followUpIncidents,
            'topCounties' => $topCounties,
            'resolutionRate' => $resolutionRate,
            'avgResolutionDays' => $avgResolutionDays ? round($avgResolutionDays) : null,
        ]);
    }
}
