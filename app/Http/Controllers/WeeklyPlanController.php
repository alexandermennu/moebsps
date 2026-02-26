<?php

namespace App\Http\Controllers;

use App\Models\BureauNotification;
use App\Models\WeeklyPlan;
use Illuminate\Http\Request;

class WeeklyPlanController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = WeeklyPlan::with(['division', 'submitter', 'reviewer']);

        if ($user->isDirector()) {
            $query->where('division_id', $user->division_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('division_id') && !$user->isDirector()) {
            $query->where('division_id', $request->division_id);
        }

        $plans = $query->latest()->paginate(15);

        return view('weekly-plans.index', compact('plans', 'user'));
    }

    public function create()
    {
        $user = auth()->user();

        if (!$user->isDirector()) {
            abort(403, 'Only Division Directors can create weekly plans.');
        }

        return view('weekly-plans.create', compact('user'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user->isDirector()) {
            abort(403);
        }

        $validated = $request->validate([
            'week_start' => 'required|date',
            'week_end' => 'required|date|after:week_start',
            'planned_activities' => 'required|string',
            'objectives' => 'nullable|string',
            'expected_outcomes' => 'nullable|string',
            'resources_needed' => 'nullable|string',
            'status' => 'in:draft,submitted',
        ]);

        $plan = WeeklyPlan::create([
            ...$validated,
            'division_id' => $user->division_id,
            'submitted_by' => $user->id,
            'status' => $request->input('status', 'draft'),
        ]);

        if ($plan->status === 'submitted') {
            $this->notifyBureauHead($plan);
        }

        return redirect()->route('weekly-plans.index')
            ->with('success', 'Weekly plan ' . ($plan->status === 'submitted' ? 'submitted' : 'saved as draft') . ' successfully.');
    }

    public function show(WeeklyPlan $weeklyPlan)
    {
        $user = auth()->user();

        if ($user->isDirector() && $weeklyPlan->division_id !== $user->division_id) {
            abort(403);
        }

        $weeklyPlan->load(['division', 'submitter', 'reviewer']);

        return view('weekly-plans.show', compact('weeklyPlan', 'user'));
    }

    public function edit(WeeklyPlan $weeklyPlan)
    {
        $user = auth()->user();

        if (!$user->isDirector() || $weeklyPlan->submitted_by !== $user->id) {
            abort(403);
        }

        if (!in_array($weeklyPlan->status, ['draft', 'rejected'])) {
            return redirect()->route('weekly-plans.show', $weeklyPlan)
                ->with('error', 'Only draft or rejected plans can be edited.');
        }

        return view('weekly-plans.edit', compact('weeklyPlan', 'user'));
    }

    public function update(Request $request, WeeklyPlan $weeklyPlan)
    {
        $user = $request->user();

        if (!$user->isDirector() || $weeklyPlan->submitted_by !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'week_start' => 'required|date',
            'week_end' => 'required|date|after:week_start',
            'planned_activities' => 'required|string',
            'objectives' => 'nullable|string',
            'expected_outcomes' => 'nullable|string',
            'resources_needed' => 'nullable|string',
            'status' => 'in:draft,submitted',
        ]);

        $weeklyPlan->update($validated);

        if ($weeklyPlan->status === 'submitted') {
            $this->notifyBureauHead($weeklyPlan);
        }

        return redirect()->route('weekly-plans.show', $weeklyPlan)
            ->with('success', 'Weekly plan updated successfully.');
    }

    public function review(Request $request, WeeklyPlan $weeklyPlan)
    {
        $user = $request->user();

        if (!$user->isBureauHead() && !$user->isMinister()) {
            abort(403);
        }

        $validated = $request->validate([
            'action' => 'required|in:approved,rejected',
            'review_comments' => 'nullable|string',
        ]);

        $weeklyPlan->update([
            'status' => $validated['action'],
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
            'review_comments' => $validated['review_comments'],
        ]);

        BureauNotification::send(
            $weeklyPlan->submitted_by,
            $validated['action'] === 'approved' ? 'approval' : 'rejection',
            'Weekly Plan ' . ucfirst($validated['action']),
            "Your weekly plan for {$weeklyPlan->week_start->format('M d')} - {$weeklyPlan->week_end->format('M d, Y')} has been {$validated['action']}.",
            route('weekly-plans.show', $weeklyPlan)
        );

        return redirect()->route('weekly-plans.show', $weeklyPlan)
            ->with('success', 'Weekly plan ' . $validated['action'] . ' successfully.');
    }

    private function notifyBureauHead(WeeklyPlan $plan): void
    {
        $bureauHeads = \App\Models\User::where('role', 'bureau_head')->where('is_active', true)->get();

        foreach ($bureauHeads as $head) {
            BureauNotification::send(
                $head->id,
                'reminder',
                'New Weekly Plan Submitted',
                "A weekly plan has been submitted by {$plan->submitter->name} from {$plan->division->name}.",
                route('weekly-plans.show', $plan)
            );
        }
    }
}
