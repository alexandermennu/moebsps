<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Division;
use App\Models\SrgbvCase;
use App\Models\TrackedActivity;
use App\Models\User;
use App\Models\WeeklyPlan;
use App\Models\WeeklyUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->hasFullAccess()) {
            return $this->fullAccessDashboard($user);
        }

        if ($user->isDirector()) {
            return $this->directorDashboard($user);
        }

        if (in_array($user->role, ['supervisor', 'coordinator', 'counselor'])) {
            return $this->limitedDivisionDashboard($user);
        }

        // Record Clerk, Secretary - personal access
        return $this->personalDashboard($user);
    }

    private function directorDashboard(User $user)
    {
        $divisionId = $user->division_id;
        $totalActivities = Activity::byDivision($divisionId)->count();

        $stats = [
            'total_activities' => $totalActivities,
            'in_progress' => Activity::byDivision($divisionId)->where('status', 'in_progress')->count(),
            'completed' => Activity::byDivision($divisionId)->where('status', 'completed')->count(),
            'overdue' => Activity::byDivision($divisionId)->overdue()->count(),
            'not_started' => Activity::byDivision($divisionId)->where('status', 'not_started')->count(),
            'completion_rate' => $totalActivities > 0
                ? round((Activity::byDivision($divisionId)->where('status', 'completed')->count() / $totalActivities) * 100)
                : 0,
            'pending_updates' => WeeklyUpdate::where('division_id', $divisionId)->where('status', 'draft')->count(),
            'pending_plans' => WeeklyPlan::where('division_id', $divisionId)->where('status', 'draft')->count(),
            'total_updates' => WeeklyUpdate::where('division_id', $divisionId)->count(),
            'total_plans' => WeeklyPlan::where('division_id', $divisionId)->count(),
            'total_staff' => User::where('division_id', $divisionId)->where('is_active', true)->count(),
            'escalated' => Activity::byDivision($divisionId)->escalated()->count(),
        ];

        // SRGBV stats for CGPC directors
        if ($user->division && $user->division->code === 'CGPC') {
            $stats['srgbv_total'] = SrgbvCase::count();
            $stats['srgbv_open'] = SrgbvCase::open()->count();
            $stats['srgbv_critical'] = SrgbvCase::critical()->open()->count();
        }

        $recentActivities = Activity::byDivision($divisionId)
            ->with('assignee')
            ->latest()
            ->take(5)
            ->get();

        $overdueActivities = Activity::byDivision($divisionId)
            ->overdue()
            ->with('assignee')
            ->latest()
            ->take(5)
            ->get();

        // Division staff list
        $divisionStaff = User::where('division_id', $divisionId)
            ->where('is_active', true)
            ->withCount([
                'activities',
                'activities as completed_activities_count' => fn($q) => $q->where('status', 'completed'),
                'activities as overdue_activities_count' => fn($q) => $q->where('is_overdue', true),
            ])
            ->orderBy('name')
            ->get();

        // Submitted plans & updates for status tracking
        $submittedPlans = WeeklyPlan::where('division_id', $divisionId)
            ->with('reviewer')
            ->whereIn('status', ['submitted', 'approved', 'rejected'])
            ->latest()
            ->take(10)
            ->get();

        $submittedUpdates = WeeklyUpdate::where('division_id', $divisionId)
            ->with('reviewer')
            ->whereIn('status', ['submitted', 'approved', 'rejected'])
            ->latest()
            ->take(10)
            ->get();

        // Tracked activities from submissions
        $trackedStats = [
            'total' => TrackedActivity::byDivision($divisionId)->count(),
            'stale' => TrackedActivity::byDivision($divisionId)->stale()->count(),
            'repeated' => TrackedActivity::byDivision($divisionId)->repeated()->count(),
        ];
        $flaggedActivities = TrackedActivity::byDivision($divisionId)
            ->flagged()
            ->latest('last_reported_at')
            ->take(5)
            ->get();

        return view('dashboard.director', compact('stats', 'recentActivities', 'overdueActivities', 'divisionStaff', 'submittedPlans', 'submittedUpdates', 'trackedStats', 'flaggedActivities', 'user'));
    }

    private function fullAccessDashboard(User $user)
    {
        $divisions = Division::where('is_active', true)->withCount([
            'activities',
            'activities as overdue_count' => fn($q) => $q->where('is_overdue', true),
            'activities as completed_count' => fn($q) => $q->where('status', 'completed'),
            'activities as in_progress_count' => fn($q) => $q->where('status', 'in_progress'),
            'users as staff_count' => fn($q) => $q->where('is_active', true),
        ])->get();

        $totalActivities = Activity::count();
        $stats = [
            'total_divisions' => Division::where('is_active', true)->count(),
            'total_activities' => $totalActivities,
            'in_progress' => Activity::where('status', 'in_progress')->count(),
            'completed' => Activity::where('status', 'completed')->count(),
            'overdue_activities' => Activity::overdue()->count(),
            'escalated_activities' => Activity::escalated()->count(),
            'pending_updates' => WeeklyUpdate::where('status', 'submitted')->count(),
            'pending_plans' => WeeklyPlan::where('status', 'submitted')->count(),
            'total_updates' => WeeklyUpdate::count(),
            'total_plans' => WeeklyPlan::count(),
            'completion_rate' => $totalActivities > 0
                ? round((Activity::where('status', 'completed')->count() / $totalActivities) * 100)
                : 0,
            'total_users' => User::where('is_active', true)->count(),
            'pending_staff' => User::pendingApproval()->count(),
            'srgbv_total' => SrgbvCase::count(),
            'srgbv_open' => SrgbvCase::open()->count(),
            'srgbv_critical' => SrgbvCase::critical()->open()->count(),
        ];

        $escalatedActivities = Activity::escalated()
            ->with(['division', 'assignee'])
            ->latest('escalated_at')
            ->take(5)
            ->get();

        $pendingReviews = WeeklyUpdate::where('status', 'submitted')
            ->with(['division', 'submitter'])
            ->latest()
            ->take(5)
            ->get();

        $pendingPlanReviews = WeeklyPlan::where('status', 'submitted')
            ->with(['division', 'submitter'])
            ->latest()
            ->take(5)
            ->get();

        // Approved plans & updates for Minister's dashboard
        $approvedPlans = WeeklyPlan::where('status', 'approved')
            ->with(['division', 'submitter', 'reviewer'])
            ->latest('reviewed_at')
            ->take(10)
            ->get();

        $approvedUpdates = WeeklyUpdate::where('status', 'approved')
            ->with(['division', 'submitter', 'reviewer'])
            ->latest('reviewed_at')
            ->take(10)
            ->get();

        $recentActivities = Activity::with(['division', 'assignee'])
            ->latest()
            ->take(5)
            ->get();

        // Tracked activities from submissions
        $trackedStats = [
            'total' => TrackedActivity::count(),
            'stale' => TrackedActivity::stale()->count(),
            'repeated' => TrackedActivity::repeated()->count(),
            'active' => TrackedActivity::active()->count(),
        ];
        $flaggedActivities = TrackedActivity::with('division')
            ->flagged()
            ->latest('last_reported_at')
            ->take(8)
            ->get();

        // Staff by role breakdown
        $staffByRole = User::where('is_active', true)
            ->select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role')
            ->toArray();

        // Division-level weekly update summaries
        $divisionUpdateSummaries = Division::where('is_active', true)
            ->with(['weeklyUpdates' => function ($q) {
                $q->with('activities')
                    ->whereIn('status', ['submitted', 'approved'])
                    ->latest()
                    ->take(5);
            }])
            ->withCount([
                'weeklyUpdates as total_updates_count',
                'weeklyUpdates as approved_updates_count' => fn($q) => $q->where('status', 'approved'),
                'weeklyUpdates as submitted_updates_count' => fn($q) => $q->where('status', 'submitted'),
                'weeklyUpdates as rejected_updates_count' => fn($q) => $q->where('status', 'rejected'),
                'weeklyUpdates as draft_updates_count' => fn($q) => $q->where('status', 'draft'),
            ])
            ->get()
            ->map(function ($division) {
                // Calculate activity status breakdown from latest approved update
                $latestApproved = $division->weeklyUpdates->where('status', 'approved')->first();
                $division->latest_update = $latestApproved ?? $division->weeklyUpdates->first();
                $activityStats = ['completed' => 0, 'ongoing' => 0, 'not_started' => 0, 'na' => 0];
                if ($division->latest_update && $division->latest_update->activities->count()) {
                    foreach ($division->latest_update->activities as $act) {
                        $flag = $act->status_flag ?? 'na';
                        if (isset($activityStats[$flag])) {
                            $activityStats[$flag]++;
                        }
                    }
                }
                $division->activity_stats = $activityStats;
                $division->submission_rate = $division->total_updates_count > 0
                    ? round(($division->approved_updates_count / $division->total_updates_count) * 100)
                    : 0;
                return $division;
            });

        return view('dashboard.full-access', compact('stats', 'divisions', 'escalatedActivities', 'pendingReviews', 'pendingPlanReviews', 'approvedPlans', 'approvedUpdates', 'recentActivities', 'staffByRole', 'trackedStats', 'flaggedActivities', 'divisionUpdateSummaries', 'user'));
    }

    private function ministerDashboard(User $user)
    {
        $divisions = Division::where('is_active', true)->withCount([
            'activities',
            'activities as overdue_count' => fn($q) => $q->where('is_overdue', true),
            'activities as completed_count' => fn($q) => $q->where('status', 'completed'),
        ])->get();

        $stats = [
            'total_divisions' => Division::where('is_active', true)->count(),
            'total_activities' => Activity::count(),
            'completion_rate' => Activity::count() > 0
                ? round((Activity::where('status', 'completed')->count() / Activity::count()) * 100)
                : 0,
            'overdue_activities' => Activity::overdue()->count(),
            'escalated_to_minister' => Activity::where('escalated_to', 'minister')->count(),
        ];

        $criticalActivities = Activity::where('escalated_to', 'minister')
            ->with(['division', 'assignee'])
            ->latest('escalated_at')
            ->take(10)
            ->get();

        return view('dashboard.minister', compact('stats', 'divisions', 'criticalActivities', 'user'));
    }

    private function limitedDivisionDashboard(User $user)
    {
        $divisionId = $user->division_id;

        $stats = [
            'total_activities' => Activity::byDivision($divisionId)->count(),
            'in_progress' => Activity::byDivision($divisionId)->where('status', 'in_progress')->count(),
            'completed' => Activity::byDivision($divisionId)->where('status', 'completed')->count(),
            'overdue' => Activity::byDivision($divisionId)->overdue()->count(),
        ];

        $recentActivities = Activity::byDivision($divisionId)
            ->with('assignee')
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.limited-division', compact('stats', 'recentActivities', 'user'));
    }

    private function personalDashboard(User $user)
    {
        $myActivities = Activity::where('assigned_to', $user->id)
            ->with('division')
            ->latest()
            ->take(10)
            ->get();

        $stats = [
            'assigned_to_me' => Activity::where('assigned_to', $user->id)->count(),
            'in_progress' => Activity::where('assigned_to', $user->id)->where('status', 'in_progress')->count(),
            'completed' => Activity::where('assigned_to', $user->id)->where('status', 'completed')->count(),
            'overdue' => Activity::where('assigned_to', $user->id)->overdue()->count(),
        ];

        return view('dashboard.personal', compact('stats', 'myActivities', 'user'));
    }
}
