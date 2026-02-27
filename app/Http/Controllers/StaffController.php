<?php

namespace App\Http\Controllers;

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
            ->whereIn('role', array_keys(User::directorAssignableRoles()));

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

        $staff = $query->latest()->paginate(15);
        $roles = User::directorAssignableRoles();

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

        $roles = User::directorAssignableRoles();

        return view('staff.create', compact('user', 'roles'));
    }

    /**
     * Store a new staff member under the director's division.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user->canCreateStaff()) {
            abort(403);
        }

        $allowedRoles = array_keys(User::directorAssignableRoles());

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in($allowedRoles)],
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'division_id' => $user->division_id,
            'position' => $validated['position'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('staff.index')
            ->with('success', 'Staff member created successfully.');
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

        // Must belong to director's division and be an assignable role
        if ($staff_user->division_id !== $user->division_id ||
            !in_array($staff_user->role, array_keys(User::directorAssignableRoles()))) {
            abort(403, 'You can only manage staff in your own division.');
        }

        $roles = User::directorAssignableRoles();

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
            !in_array($staff_user->role, array_keys(User::directorAssignableRoles()))) {
            abort(403);
        }

        $allowedRoles = array_keys(User::directorAssignableRoles());

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($staff_user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => ['required', Rule::in($allowedRoles)],
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'position' => $validated['position'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'is_active' => $request->boolean('is_active', true),
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
            !in_array($staff_user->role, array_keys(User::directorAssignableRoles()))) {
            abort(403, 'You can only delete staff in your own division.');
        }

        $name = $staff_user->name;
        $staff_user->delete();

        return redirect()->route('staff.index')
            ->with('success', "Staff member \"{$name}\" has been deleted.");
    }
}
