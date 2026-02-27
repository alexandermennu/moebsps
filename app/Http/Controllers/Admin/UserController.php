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
        $query = User::with('division');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Get non-counselor users for the main table
        $usersQuery = clone $query;
        $users = $usersQuery->where('role', '!=', User::ROLE_COUNSELOR)->latest()->paginate(15);

        // Get counselors separately (only when not filtering by a non-counselor role)
        $counselors = collect();
        if (!$request->filled('role') || $request->role === User::ROLE_COUNSELOR) {
            $counselorQuery = User::with('division');
            if ($request->filled('search')) {
                $search = $request->search;
                $counselorQuery->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
            $counselors = $counselorQuery->where('role', User::ROLE_COUNSELOR)->latest()->get();
        }

        $divisions = Division::where('is_active', true)->get();

        return view('admin.users.index', compact('users', 'counselors', 'divisions'));
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
