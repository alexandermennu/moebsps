<?php

namespace App\Http\Controllers;

use App\Models\BureauNotification;
use App\Models\WeeklyUpdate;
use Illuminate\Http\Request;

class WeeklyUpdateController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = WeeklyUpdate::with(['division', 'submitter', 'reviewer']);

        if ($user->isDirector()) {
            $query->where('division_id', $user->division_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('division_id') && !$user->isDirector()) {
            $query->where('division_id', $request->division_id);
        }

        $updates = $query->latest()->paginate(15);

        return view('weekly-updates.index', compact('updates', 'user'));
    }

    public function create()
    {
        $user = auth()->user();

        if (!$user->isDirector()) {
            abort(403, 'Only Division Directors can create weekly updates.');
        }

        return view('weekly-updates.create', compact('user'));
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
            'accomplishments' => 'required|string',
            'challenges' => 'nullable|string',
            'support_needed' => 'nullable|string',
            'key_metrics' => 'nullable|string',
            'status' => 'in:draft,submitted',
        ]);

        $update = WeeklyUpdate::create([
            ...$validated,
            'division_id' => $user->division_id,
            'submitted_by' => $user->id,
            'status' => $request->input('status', 'draft'),
        ]);

        if ($update->status === 'submitted') {
            $this->notifyBureauHead($update);
        }

        return redirect()->route('weekly-updates.index')
            ->with('success', 'Weekly update ' . ($update->status === 'submitted' ? 'submitted' : 'saved as draft') . ' successfully.');
    }

    public function show(WeeklyUpdate $weeklyUpdate)
    {
        $user = auth()->user();

        if ($user->isDirector() && $weeklyUpdate->division_id !== $user->division_id) {
            abort(403);
        }

        $weeklyUpdate->load(['division', 'submitter', 'reviewer']);

        return view('weekly-updates.show', compact('weeklyUpdate', 'user'));
    }

    public function edit(WeeklyUpdate $weeklyUpdate)
    {
        $user = auth()->user();

        if (!$user->isDirector() || $weeklyUpdate->submitted_by !== $user->id) {
            abort(403);
        }

        if (!in_array($weeklyUpdate->status, ['draft', 'rejected'])) {
            return redirect()->route('weekly-updates.show', $weeklyUpdate)
                ->with('error', 'Only draft or rejected updates can be edited.');
        }

        return view('weekly-updates.edit', compact('weeklyUpdate', 'user'));
    }

    public function update(Request $request, WeeklyUpdate $weeklyUpdate)
    {
        $user = $request->user();

        if (!$user->isDirector() || $weeklyUpdate->submitted_by !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'week_start' => 'required|date',
            'week_end' => 'required|date|after:week_start',
            'accomplishments' => 'required|string',
            'challenges' => 'nullable|string',
            'support_needed' => 'nullable|string',
            'key_metrics' => 'nullable|string',
            'status' => 'in:draft,submitted',
        ]);

        $weeklyUpdate->update($validated);

        if ($weeklyUpdate->status === 'submitted') {
            $this->notifyBureauHead($weeklyUpdate);
        }

        return redirect()->route('weekly-updates.show', $weeklyUpdate)
            ->with('success', 'Weekly update updated successfully.');
    }

    public function review(Request $request, WeeklyUpdate $weeklyUpdate)
    {
        $user = $request->user();

        if (!$user->isBureauHead() && !$user->isMinister()) {
            abort(403);
        }

        $validated = $request->validate([
            'action' => 'required|in:approved,rejected',
            'review_comments' => 'nullable|string',
        ]);

        $weeklyUpdate->update([
            'status' => $validated['action'],
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
            'review_comments' => $validated['review_comments'],
        ]);

        // Notify the submitter
        BureauNotification::send(
            $weeklyUpdate->submitted_by,
            $validated['action'] === 'approved' ? 'approval' : 'rejection',
            'Weekly Update ' . ucfirst($validated['action']),
            "Your weekly update for {$weeklyUpdate->week_start->format('M d')} - {$weeklyUpdate->week_end->format('M d, Y')} has been {$validated['action']}.",
            route('weekly-updates.show', $weeklyUpdate)
        );

        return redirect()->route('weekly-updates.show', $weeklyUpdate)
            ->with('success', 'Weekly update ' . $validated['action'] . ' successfully.');
    }

    private function notifyBureauHead(WeeklyUpdate $update): void
    {
        $bureauHeads = \App\Models\User::where('role', 'bureau_head')->where('is_active', true)->get();

        foreach ($bureauHeads as $head) {
            BureauNotification::send(
                $head->id,
                'reminder',
                'New Weekly Update Submitted',
                "A weekly update has been submitted by {$update->submitter->name} from {$update->division->name}.",
                route('weekly-updates.show', $update)
            );
        }
    }
}
