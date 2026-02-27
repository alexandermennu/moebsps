<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BureauNotification;
use App\Models\User;
use Illuminate\Http\Request;

class StaffApprovalController extends Controller
{
    /**
     * List all staff pending approval.
     */
    public function index(Request $request)
    {
        $query = User::pendingApproval()
            ->with(['division', 'createdByUser'])
            ->latest();

        if ($request->filled('division_id')) {
            $query->where('division_id', $request->division_id);
        }

        $pendingStaff = $query->paginate(15);
        $divisions = \App\Models\Division::where('is_active', true)->get();

        return view('admin.staff-approvals.index', compact('pendingStaff', 'divisions'));
    }

    /**
     * Show details of a pending staff member.
     */
    public function show(User $user)
    {
        if (!$user->isPending()) {
            return redirect()->route('admin.staff-approvals.index')
                ->with('info', 'This user has already been processed.');
        }

        $user->load(['division', 'createdByUser']);

        return view('admin.staff-approvals.show', compact('user'));
    }

    /**
     * Approve a pending staff member.
     */
    public function approve(User $user)
    {
        if (!$user->isPending()) {
            return redirect()->route('admin.staff-approvals.index')
                ->with('info', 'This user has already been processed.');
        }

        $user->update([
            'approval_status' => User::APPROVAL_APPROVED,
            'is_active' => true,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        // Notify the director who created this staff
        if ($user->created_by_user_id) {
            BureauNotification::send(
                $user->created_by_user_id,
                'approval',
                'Staff Approved',
                "Your staff member \"{$user->name}\" ({$user->role_label}) has been approved and can now log in.",
                route('staff.index')
            );
        }

        return redirect()->route('admin.staff-approvals.index')
            ->with('success', "\"{$user->name}\" has been approved and activated.");
    }

    /**
     * Reject a pending staff member.
     */
    public function reject(Request $request, User $user)
    {
        if (!$user->isPending()) {
            return redirect()->route('admin.staff-approvals.index')
                ->with('info', 'This user has already been processed.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $user->update([
            'approval_status' => User::APPROVAL_REJECTED,
            'is_active' => false,
            'rejection_reason' => $validated['rejection_reason'],
            'approved_by' => auth()->id(),
        ]);

        // Notify the director who created this staff
        if ($user->created_by_user_id) {
            BureauNotification::send(
                $user->created_by_user_id,
                'approval',
                'Staff Rejected',
                "Your staff member \"{$user->name}\" ({$user->role_label}) has been rejected. Reason: {$validated['rejection_reason']}",
                route('staff.index')
            );
        }

        return redirect()->route('admin.staff-approvals.index')
            ->with('success', "\"{$user->name}\" has been rejected.");
    }
}
