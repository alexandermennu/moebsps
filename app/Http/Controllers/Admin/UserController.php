<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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

        if ($request->filled('profile_status')) {
            $query->where('counselor_profile_status', $request->profile_status);
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
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'counselor_school' => 'required_if:role,counselor|nullable|string|max:255',
            'counselor_county' => 'required_if:role,counselor|nullable|in:' . implode(',', User::COUNTIES),
            'counselor_status' => 'required_if:role,counselor|nullable|in:' . implode(',', array_keys(User::COUNSELOR_STATUSES)),
            'counselor_specialization' => 'nullable|in:' . implode(',', array_keys(User::COUNSELOR_SPECIALIZATIONS)),
            'counselor_years_experience' => 'nullable|integer|min:0|max:50',
            'counselor_training' => 'nullable|string|max:2000',
            'counselor_school_phone' => 'nullable|string|max:50',
            'counselor_appointed_at' => 'nullable|date',
            // Personal information
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:' . implode(',', array_keys(User::GENDERS)),
            'nationality' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:50',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            // Assignment details
            'counselor_assignment_date' => 'nullable|date',
            'counselor_school_district' => 'nullable|string|max:255',
            'counselor_school_address' => 'nullable|string|max:1000',
            'counselor_school_principal' => 'nullable|string|max:255',
            'counselor_school_level' => 'nullable|in:' . implode(',', array_keys(User::SCHOOL_LEVELS)),
            'counselor_school_type' => 'nullable|in:' . implode(',', array_keys(User::SCHOOL_TYPES)),
            'counselor_school_population' => 'nullable|integer|min:0|max:50000',
            'counselor_num_boys' => 'nullable|integer|min:0|max:50000',
            'counselor_num_girls' => 'nullable|integer|min:0|max:50000',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active') ? true : false;
        $validated['approval_status'] = User::APPROVAL_APPROVED;
        $validated['approved_at'] = now();
        $validated['approved_by'] = auth()->id();
        unset($validated['profile_photo']);

        // Counselor-only fields — remove entirely for non-counselor roles
        $counselorOnlyFields = [
            'counselor_school', 'counselor_county', 'counselor_status',
            'counselor_specialization', 'counselor_years_experience',
            'counselor_training', 'counselor_school_phone', 'counselor_appointed_at',
            'counselor_assignment_date', 'counselor_school_district',
            'counselor_school_address', 'counselor_school_principal',
            'counselor_school_level', 'counselor_school_type',
            'counselor_school_population', 'counselor_num_boys', 'counselor_num_girls',
            'date_of_birth', 'gender', 'nationality', 'address', 'city',
            'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relationship',
        ];

        if ($validated['role'] !== User::ROLE_COUNSELOR) {
            // Remove all counselor/personal fields — don't attempt to write them at all
            foreach ($counselorOnlyFields as $field) {
                unset($validated[$field]);
            }
        } else {
            // Counselors must always belong to CGPC division
            $cgpc = Division::where('code', 'CGPC')->first();
            if ($cgpc) {
                $validated['division_id'] = $cgpc->id;
            }
        }

        $user = User::create($validated);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store(
                'profile-photos/' . $user->id,
                config('filesystems.uploads', 'public')
            );
            $user->update(['profile_photo' => $path]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $divisions = Division::where('is_active', true)->get();
        $roles = User::ROLES;
        $user->load('counselorEducation');

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
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'remove_photo' => 'nullable|boolean',
            'counselor_school' => 'required_if:role,counselor|nullable|string|max:255',
            'counselor_county' => 'required_if:role,counselor|nullable|in:' . implode(',', User::COUNTIES),
            'counselor_status' => 'required_if:role,counselor|nullable|in:' . implode(',', array_keys(User::COUNSELOR_STATUSES)),
            'counselor_specialization' => 'nullable|in:' . implode(',', array_keys(User::COUNSELOR_SPECIALIZATIONS)),
            'counselor_years_experience' => 'nullable|integer|min:0|max:50',
            'counselor_training' => 'nullable|string|max:2000',
            'counselor_school_phone' => 'nullable|string|max:50',
            'counselor_appointed_at' => 'nullable|date',
            // Personal information
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:' . implode(',', array_keys(User::GENDERS)),
            'nationality' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:50',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            // Assignment details
            'counselor_assignment_date' => 'nullable|date',
            'counselor_school_district' => 'nullable|string|max:255',
            'counselor_school_address' => 'nullable|string|max:1000',
            'counselor_school_principal' => 'nullable|string|max:255',
            'counselor_school_level' => 'nullable|in:' . implode(',', array_keys(User::SCHOOL_LEVELS)),
            'counselor_school_type' => 'nullable|in:' . implode(',', array_keys(User::SCHOOL_TYPES)),
            'counselor_school_population' => 'nullable|integer|min:0|max:50000',
            'counselor_num_boys' => 'nullable|integer|min:0|max:50000',
            'counselor_num_girls' => 'nullable|integer|min:0|max:50000',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->boolean('is_active');
        unset($validated['profile_photo'], $validated['remove_photo']);

        // Counselor-only fields — remove entirely for non-counselor roles
        $counselorOnlyFields = [
            'counselor_school', 'counselor_county', 'counselor_status',
            'counselor_specialization', 'counselor_years_experience',
            'counselor_training', 'counselor_school_phone', 'counselor_appointed_at',
            'counselor_assignment_date', 'counselor_school_district',
            'counselor_school_address', 'counselor_school_principal',
            'counselor_school_level', 'counselor_school_type',
            'counselor_school_population', 'counselor_num_boys', 'counselor_num_girls',
            'date_of_birth', 'gender', 'nationality', 'address', 'city',
            'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relationship',
        ];

        if ($validated['role'] !== User::ROLE_COUNSELOR) {
            // Remove all counselor/personal fields — don't attempt to write them at all
            foreach ($counselorOnlyFields as $field) {
                unset($validated[$field]);
            }
        } else {
            // Counselors must always belong to CGPC division
            $cgpc = Division::where('code', 'CGPC')->first();
            if ($cgpc) {
                $validated['division_id'] = $cgpc->id;
            }
        }

        $user->update($validated);

        // Handle profile photo
        if ($request->boolean('remove_photo')) {
            $user->deleteProfilePhoto();
        } elseif ($request->hasFile('profile_photo')) {
            // Delete old photo
            if ($user->profile_photo) {
                Storage::disk(config('filesystems.uploads', 'public'))->delete($user->profile_photo);
            }
            $path = $request->file('profile_photo')->store(
                'profile-photos/' . $user->id,
                config('filesystems.uploads', 'public')
            );
            $user->update(['profile_photo' => $path]);
        }

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
