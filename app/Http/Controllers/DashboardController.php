<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Division;
use App\Models\User;
use App\Models\WeeklyPlan;
use App\Models\WeeklyUpdate;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return match($user->role) {
            'director' => $this->directorDashboard($user),
            'bureau_head' => $this->bureauHeadDashboard($user),
            'minister' => $this->ministerDashboard($user),
            'admin' => $this->adminDashboard($user),
            default => abort(403),
        };
    }

    private function directorDashboard(User $user)
    {
        $divisionId = $user->division_id;

        $stats = [
            'total_activities' => Activity::byDivision($divisionId)->count(),
            'in_progress' => Activity::byDivision($divisionId)->where('status', 'in_progress')->count(),
            'completed' => Activity::byDivision($divisionId)->where('status', 'completed')->count(),
            'overdue' => Activity::byDivision($divisionId)->overdue()->count(),
            'pending_updates' => WeeklyUpdate::where('division_id', $divisionId)->where('status', 'draft')->count(),
            'pending_plans' => WeeklyPlan::where('division_id', $divisionId)->where('status', 'draft')->count(),
        ];

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

        return view('dashboard.director', compact('stats', 'recentActivities', 'overdueActivities', 'user'));
    }

    private function bureauHeadDashboard(User $user)
    {
        $divisions = Division::where('is_active', true)->withCount([
            'activities',
            'activities as overdue_count' => fn($q) => $q->where('is_overdue', true),
            'activities as completed_count' => fn($q) => $q->where('status', 'completed'),
        ])->get();

        $stats = [
            'total_divisions' => Division::where('is_active', true)->count(),
            'total_activities' => Activity::count(),
            'overdue_activities' => Activity::overdue()->count(),
            'escalated_activities' => Activity::escalated()->count(),
            'pending_updates' => WeeklyUpdate::where('status', 'submitted')->count(),
            'pending_plans' => WeeklyPlan::where('status', 'submitted')->count(),
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

        return view('dashboard.bureau-head', compact('stats', 'divisions', 'escalatedActivities', 'pendingReviews', 'user'));
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

    private function adminDashboard(User $user)
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'total_divisions' => Division::count(),
            'total_activities' => Activity::count(),
            'overdue_activities' => Activity::overdue()->count(),
        ];

        $recentUsers = User::latest()->take(5)->get();

        return view('dashboard.admin', compact('stats', 'recentUsers', 'user'));
    }
}
