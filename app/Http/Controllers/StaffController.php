<?php

namespace App\Http\Controllers;

use App\Models\BureauNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    /**
     * List staff members in the director's division.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user->canCreateStaff()) {
            abort(403);
        }

        $query = User::where('division_id', $user->division_id)
            ->where('id', '!=', $user->id)
            ->whereIn('role', array_keys(User::directorAssignableRoles($user->division_id)));

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('approval_status', $request->status);
        }

        $staff = $query->latest()->paginate(15);
        $roles = User::directorAssignableRoles($user->division_id);

        return view('staff.index', compact('staff', 'roles', 'user'));
    }

    /**
     * Show form to create a new staff member.
     */
    public function create()
    {
        $user = auth()->user();

        if (!$user->canCreateStaff()) {
            abort(403);
        }

        $roles = User::directorAssignableRoles($user->division_id);

        return view('staff.create', compact('user', 'roles'));
    }

    /**
     * Store a new staff member under the director's division.
     * Staff is created with pending approval status — must be approved by full-access user.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user->canCreateStaff()) {
            abort(403);
        }

        $allowedRoles = array_keys(User::directorAssignableRoles($user->division_id));

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in($allowedRoles)],
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $newStaff = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'division_id' => $user->division_id,
            'position' => $validated['position'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'is_active' => false,
            'approval_status' => User::APPROVAL_PENDING,
            'created_by_user_id' => $user->id,
        ]);

        // Notify full-access users about the pending approval
        $reviewers = User::whereIn('role', [
            User::ROLE_MINISTER,
            User::ROLE_ADMIN_ASSISTANT,
            User::ROLE_TECH_ASSISTANT,
        ])->where('is_active', true)->get();

        foreach ($reviewers as $reviewer) {
            BureauNotification::send(
                $reviewer->id,
                'approval',
                'Staff Approval Required',
                "{$user->name} ({$user->division?->name}) has created a new staff member \"{$newStaff->name}\" ({$newStaff->role_label}) that requires your approval.",
                route('admin.staff-approvals.index')
            );
        }

        return redirect()->route('staff.index')
            ->with('success', 'Staff member created and submitted for approval. They will be able to log in once approved by an administrator.');
    }

    /**
     * Show form to edit an existing staff member.
     */
    public function edit(User $staff_user)
    {
        $user = auth()->user();

        if (!$user->canCreateStaff()) {
            abort(403);
        }

        if ($staff_user->division_id !== $user->division_id ||
            !in_array($staff_user->role, array_keys(User::directorAssignableRoles($user->division_id)))) {
            abort(403, 'You can only manage staff in your own division.');
        }

        $roles = User::directorAssignableRoles($user->division_id);

        return view('staff.edit', ['staff' => $staff_user, 'roles' => $roles, 'user' => $user]);
    }

    /**
     * Update an existing staff member.
     */
    public function update(Request $request, User $staff_user)
    {
        $user = $request->user();

        if (!$user->canCreateStaff()) {
            abort(403);
        }

        if ($staff_user->division_id !== $user->division_id ||
            !in_array($staff_user->role, array_keys(User::directorAssignableRoles($user->division_id)))) {
            abort(403);
        }

        $allowedRoles = array_keys(User::directorAssignableRoles($user->division_id));

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($staff_user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => ['required', Rule::in($allowedRoles)],
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'position' => $validated['position'] ?? null,
            'phone' => $validated['phone'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $staff_user->update($data);

        return redirect()->route('staff.index')
            ->with('success', 'Staff member updated successfully.');
    }

    /**
     * Delete a staff member.
     */
    public function destroy(User $staff_user)
    {
        $user = auth()->user();

        if (!$user->canCreateStaff()) {
            abort(403);
        }

        if ($staff_user->division_id !== $user->division_id ||
            !in_array($staff_user->role, array_keys(User::directorAssignableRoles($user->division_id)))) {
            abort(403, 'You can only delete staff in your own division.');
        }

        $name = $staff_user->name;
        $staff_user->delete();

        return redirect()->route('staff.index')
            ->with('success', "Staff member \"{$name}\" has been deleted.");
    }
}
