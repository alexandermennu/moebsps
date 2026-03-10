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
            'newToday' => Incident::srgbv()->whereDate('created_at', today())->count(),
        ] : null;

        $otherStats = $canAccessOther ? [
            'total' => Incident::where('type', '!=', Incident::TYPE_SRGBV)->count(),
            'open' => Incident::where('type', '!=', Incident::TYPE_SRGBV)->open()->count(),
            'critical' => Incident::where('type', '!=', Incident::TYPE_SRGBV)->critical()->open()->count(),
            'newToday' => Incident::where('type', '!=', Incident::TYPE_SRGBV)->whereDate('created_at', today())->count(),
        ] : null;

        // Combined stats
        $combinedStats = [
            'total' => ($srgbvStats['total'] ?? 0) + ($otherStats['total'] ?? 0),
            'open' => ($srgbvStats['open'] ?? 0) + ($otherStats['open'] ?? 0),
            'critical' => ($srgbvStats['critical'] ?? 0) + ($otherStats['critical'] ?? 0),
            'newToday' => ($srgbvStats['newToday'] ?? 0) + ($otherStats['newToday'] ?? 0),
        ];

        // Recent incidents across both modules
        $recentIncidents = Incident::with(['reporter', 'assignee'])
            ->when(!$canAccessSrgbv, fn($q) => $q->where('type', '!=', Incident::TYPE_SRGBV))
            ->when(!$canAccessOther, fn($q) => $q->where('type', Incident::TYPE_SRGBV))
            ->latest()
            ->take(5)
            ->get();

        // Urgent cases requiring attention
        $urgentCount = Incident::requiringImmediateAction()
            ->when(!$canAccessSrgbv, fn($q) => $q->where('type', '!=', Incident::TYPE_SRGBV))
            ->when(!$canAccessOther, fn($q) => $q->where('type', Incident::TYPE_SRGBV))
            ->count();

        return view('sir.landing', [
            'user' => $user,
            'canAccessSrgbv' => $canAccessSrgbv,
            'canAccessOther' => $canAccessOther,
            'srgbvStats' => $srgbvStats,
            'otherStats' => $otherStats,
            'combinedStats' => $combinedStats,
            'recentIncidents' => $recentIncidents,
            'urgentCount' => $urgentCount,
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

        // New stats for dashboard
        $newToday = Incident::srgbv()->whereDate('created_at', today())->count();
        $resolvedThisMonth = Incident::srgbv()->closed()->whereMonth('updated_at', now()->month)->whereYear('updated_at', now()->year)->count();

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

        $rawMonthlyTrend = Incident::srgbv()
            ->select(DB::raw("$monthExpr as month"), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Fill in missing months with zeros for a complete 12-month chart
        $monthlyTrend = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $monthlyTrend[$month] = $rawMonthlyTrend[$month] ?? 0;
        }

        // Follow-up due
        $followUpIncidents = Incident::srgbv()
            ->requiringFollowUp()
            ->with(['assignee'])
            ->orderBy('follow_up_date')
            ->take(5)
            ->get();

        // Top counties (for map - get all counties)
        $countyData = Incident::srgbv()
            ->select('school_county', DB::raw('count(*) as total'))
            ->whereNotNull('school_county')
            ->groupBy('school_county')
            ->pluck('total', 'school_county')
            ->toArray();

        // Top counties for display
        $topCounties = collect($countyData)->sortDesc()->take(10)->toArray();

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

        // Victim age range distribution
        // Get raw age data first (handles both numeric and string keys)
        $rawAgeData = Incident::srgbv()
            ->select('victim_age', DB::raw('count(*) as total'))
            ->whereNotNull('victim_age')
            ->where('victim_age', '!=', '')
            ->groupBy('victim_age')
            ->pluck('total', 'victim_age')
            ->toArray();

        // Normalize to age range keys (convert numeric ages to range keys)
        $byAgeRange = [];
        $validKeys = array_keys(Incident::VICTIM_AGE_RANGES);
        
        foreach ($rawAgeData as $ageValue => $count) {
            // If already a valid key
            if (in_array($ageValue, $validKeys)) {
                $byAgeRange[$ageValue] = ($byAgeRange[$ageValue] ?? 0) + $count;
            } 
            // If numeric, convert to range key
            elseif (is_numeric($ageValue)) {
                $age = (int) $ageValue;
                $key = match(true) {
                    $age < 6 => 'under_6',
                    $age <= 10 => '6_10',
                    $age <= 14 => '11_14',
                    $age <= 17 => '15_17',
                    default => '18_plus',
                };
                $byAgeRange[$key] = ($byAgeRange[$key] ?? 0) + $count;
            }
            // Otherwise treat as unknown
            else {
                $byAgeRange['unknown'] = ($byAgeRange['unknown'] ?? 0) + $count;
            }
        }

        // Get only top 3 recent cases for dashboard preview
        $recentIncidents = Incident::srgbv()
            ->with(['reporter', 'assignee'])
            ->latest()
            ->take(3)
            ->get();

        return view('sir.srgbv-dashboard', [
            'user' => $user,
            'canManage' => $canManage,
            'totalIncidents' => $totalIncidents,
            'openIncidents' => $openIncidents,
            'closedIncidents' => $closedIncidents,
            'criticalIncidents' => $criticalIncidents,
            'followUpDue' => $followUpDue,
            'immediateAction' => $immediateAction,
            'newToday' => $newToday,
            'resolvedThisMonth' => $resolvedThisMonth,
            'internalCount' => $internalCount,
            'publicCount' => $publicCount,
            'byCategory' => $byCategory,
            'byStatus' => $byStatus,
            'byPriority' => $byPriority,
            'monthlyTrend' => $monthlyTrend,
            'recentIncidents' => $recentIncidents,
            'followUpIncidents' => $followUpIncidents,
            'countyData' => $countyData,
            'topCounties' => $topCounties,
            'resolutionRate' => $resolutionRate,
            'avgResolutionDays' => $avgResolutionDays ? round($avgResolutionDays) : null,
            'byGender' => $byGender,
            'byAgeRange' => $byAgeRange,
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

        // New stats for enhanced dashboard
        $newToday = Incident::where('type', '!=', Incident::TYPE_SRGBV)->whereDate('created_at', today())->count();
        $resolvedThisMonth = Incident::where('type', '!=', Incident::TYPE_SRGBV)
            ->closed()
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();

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

        $rawMonthlyTrend = Incident::where('type', '!=', Incident::TYPE_SRGBV)
            ->select(DB::raw("$monthExpr as month"), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Fill in missing months with zeros for a complete 12-month chart
        $monthlyTrend = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $monthlyTrend[$month] = $rawMonthlyTrend[$month] ?? 0;
        }

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

        // County data for map
        $countyData = Incident::where('type', '!=', Incident::TYPE_SRGBV)
            ->select('school_county', DB::raw('count(*) as total'))
            ->whereNotNull('school_county')
            ->groupBy('school_county')
            ->pluck('total', 'school_county')
            ->toArray();

        // Top counties (derived from county data)
        arsort($countyData);
        $topCounties = array_slice($countyData, 0, 10, true);

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

        // Recent incidents (top 3 for dashboard preview)
        $recentIncidents = Incident::where('type', '!=', Incident::TYPE_SRGBV)
            ->with(['reporter', 'assignee'])
            ->latest()
            ->take(3)
            ->get();

        return view('sir.other-dashboard', [
            'user' => $user,
            'canManage' => $canManage,
            'totalIncidents' => $totalIncidents,
            'openIncidents' => $openIncidents,
            'closedIncidents' => $closedIncidents,
            'criticalIncidents' => $criticalIncidents,
            'followUpDue' => $followUpDue,
            'immediateAction' => $immediateAction,
            'newToday' => $newToday,
            'resolvedThisMonth' => $resolvedThisMonth,
            'internalCount' => $internalCount,
            'publicCount' => $publicCount,
            'byType' => $byType,
            'byStatus' => $byStatus,
            'byPriority' => $byPriority,
            'monthlyTrend' => $monthlyTrend,
            'recentIncidents' => $recentIncidents,
            'followUpIncidents' => $followUpIncidents,
            'countyData' => $countyData,
            'topCounties' => $topCounties,
            'resolutionRate' => $resolutionRate,
            'avgResolutionDays' => $avgResolutionDays ? round($avgResolutionDays) : null,
        ]);
    }
}
