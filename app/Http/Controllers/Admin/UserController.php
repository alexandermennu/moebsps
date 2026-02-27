<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $divisions = Division::where('is_active', true)->get();

        // "Office of the Minister" roles
        $officeRoles = [
            User::ROLE_MINISTER,
            User::ROLE_ADMIN_ASSISTANT,
            User::ROLE_TECH_ASSISTANT,
            User::ROLE_RECORD_CLERK,
            User::ROLE_SECRETARY,
        ];

        // Office of the Minister users (may or may not have a division)
        $officeQuery = User::with('division')->whereIn('role', $officeRoles);
        if ($request->filled('search')) {
            $search = $request->search;
            $officeQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        $officeUsers = $officeQuery->latest()->get();

        // Division staff (non-office, non-counselor) grouped by division
        $divisionStaff = [];
        $counselorCounts = [];
        foreach ($divisions as $division) {
            $query = User::with('division')
                ->where('division_id', $division->id)
                ->whereNotIn('role', array_merge($officeRoles, [User::ROLE_COUNSELOR]));

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            $divisionStaff[$division->id] = $query->latest()->get();

            // Count counselors for CGPC
            if ($division->code === 'CGPC') {
                $counselorCounts[$division->id] = User::where('division_id', $division->id)
                    ->where('role', User::ROLE_COUNSELOR)
                    ->count();
            }
        }

        // Users with no division that aren't office roles or counselors
        $noDivisionQuery = User::with('division')
            ->whereNull('division_id')
            ->whereNotIn('role', array_merge($officeRoles, [User::ROLE_COUNSELOR]));
        if ($request->filled('search')) {
            $search = $request->search;
            $noDivisionQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        $noDivisionUsers = $noDivisionQuery->latest()->get();

        return view('admin.users.index', compact('officeUsers', 'divisions', 'divisionStaff', 'counselorCounts', 'noDivisionUsers'));
    }

    public function counselors(Request $request)
    {
        $query = User::with('division')
            ->where('role', User::ROLE_COUNSELOR);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('counselor_school', 'like', "%{$search}%")
                  ->orWhere('counselor_county', 'like', "%{$search}%");
            });
        }

        if ($request->filled('county')) {
            $query->where('counselor_county', $request->county);
        }

        if ($request->filled('status')) {
            $query->where('counselor_status', $request->status);
        }

        $counselors = $query->latest()->paginate(20);

        return view('admin.users.counselors', compact('counselors'));
    }

    public function create()
    {
        $divisions = Division::where('is_active', true)->get();
        $roles = User::ROLES;

        return view('admin.users.create', compact('divisions', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:' . implode(',', array_keys(User::ROLES)),
            'division_id' => 'nullable|exists:divisions,id',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'counselor_school' => 'required_if:role,counselor|nullable|string|max:255',
            'counselor_county' => 'required_if:role,counselor|nullable|in:' . implode(',', User::COUNTIES),
            'counselor_status' => 'required_if:role,counselor|nullable|in:' . implode(',', array_keys(User::COUNSELOR_STATUSES)),
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);

        // Clear counselor fields if role is not counselor
        if ($validated['role'] !== User::ROLE_COUNSELOR) {
            $validated['counselor_school'] = null;
            $validated['counselor_county'] = null;
            $validated['counselor_status'] = null;
        }

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $divisions = Division::where('is_active', true)->get();
        $roles = User::ROLES;

        return view('admin.users.edit', compact('user', 'divisions', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:' . implode(',', array_keys(User::ROLES)),
            'division_id' => 'nullable|exists:divisions,id',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'counselor_school' => 'required_if:role,counselor|nullable|string|max:255',
            'counselor_county' => 'required_if:role,counselor|nullable|in:' . implode(',', User::COUNTIES),
            'counselor_status' => 'required_if:role,counselor|nullable|in:' . implode(',', array_keys(User::COUNSELOR_STATUSES)),
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->boolean('is_active');

        // Clear counselor fields if role is not counselor
        if ($validated['role'] !== User::ROLE_COUNSELOR) {
            $validated['counselor_school'] = null;
            $validated['counselor_county'] = null;
            $validated['counselor_status'] = null;
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function toggleActive(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User ' . ($user->is_active ? 'activated' : 'deactivated') . ' successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "User \"{$name}\" has been deleted.");
    }
}
