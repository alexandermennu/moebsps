<?php

namespace App\Http\Controllers;

use App\Models\BureauNotification;
use App\Models\CounselorEducation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'counselor_school' => 'required_if:role,counselor|nullable|string|max:255',
            'counselor_county' => 'required_if:role,counselor|nullable|in:' . implode(',', User::COUNTIES),
            'counselor_status' => 'required_if:role,counselor|nullable|in:' . implode(',', array_keys(User::COUNSELOR_STATUSES)),
            'counselor_specialization' => 'nullable|in:' . implode(',', array_keys(User::COUNSELOR_SPECIALIZATIONS)),
            'counselor_years_experience' => 'nullable|integer|min:0|max:50',
            'counselor_school_phone' => 'nullable|string|max:50',
            // Personal information
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:' . implode(',', array_keys(User::GENDERS)),
            'nationality' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:50',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            // School & assignment details
            'counselor_assignment_date' => 'nullable|date',
            'counselor_school_district' => 'nullable|string|max:255',
            'counselor_school_address' => 'nullable|string|max:500',
            'counselor_school_principal' => 'nullable|string|max:255',
            'counselor_school_level' => 'nullable|in:' . implode(',', array_keys(User::SCHOOL_LEVELS)),
            'counselor_school_type' => 'nullable|in:' . implode(',', array_keys(User::SCHOOL_TYPES)),
            'counselor_school_population' => 'nullable|integer|min:0|max:50000',
            'counselor_num_boys' => 'nullable|integer|min:0|max:50000',
            'counselor_num_girls' => 'nullable|integer|min:0|max:50000',
        ]);

        $isCounselor = $validated['role'] === User::ROLE_COUNSELOR;

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
            'counselor_school' => $isCounselor ? ($validated['counselor_school'] ?? null) : null,
            'counselor_county' => $isCounselor ? ($validated['counselor_county'] ?? null) : null,
            'counselor_status' => $isCounselor ? ($validated['counselor_status'] ?? 'active') : null,
            'counselor_specialization' => $isCounselor ? ($validated['counselor_specialization'] ?? null) : null,
            'counselor_years_experience' => $isCounselor ? ($validated['counselor_years_experience'] ?? null) : null,
            'counselor_school_phone' => $isCounselor ? ($validated['counselor_school_phone'] ?? null) : null,
            // Personal information
            'date_of_birth' => $isCounselor ? ($validated['date_of_birth'] ?? null) : null,
            'gender' => $isCounselor ? ($validated['gender'] ?? null) : null,
            'nationality' => $isCounselor ? ($validated['nationality'] ?? null) : null,
            'address' => $isCounselor ? ($validated['address'] ?? null) : null,
            'city' => $isCounselor ? ($validated['city'] ?? null) : null,
            'emergency_contact_name' => $isCounselor ? ($validated['emergency_contact_name'] ?? null) : null,
            'emergency_contact_phone' => $isCounselor ? ($validated['emergency_contact_phone'] ?? null) : null,
            'emergency_contact_relationship' => $isCounselor ? ($validated['emergency_contact_relationship'] ?? null) : null,
            // School & assignment details
            'counselor_assignment_date' => $isCounselor ? ($validated['counselor_assignment_date'] ?? null) : null,
            'counselor_school_district' => $isCounselor ? ($validated['counselor_school_district'] ?? null) : null,
            'counselor_school_address' => $isCounselor ? ($validated['counselor_school_address'] ?? null) : null,
            'counselor_school_principal' => $isCounselor ? ($validated['counselor_school_principal'] ?? null) : null,
            'counselor_school_level' => $isCounselor ? ($validated['counselor_school_level'] ?? null) : null,
            'counselor_school_type' => $isCounselor ? ($validated['counselor_school_type'] ?? null) : null,
            'counselor_school_population' => $isCounselor ? ($validated['counselor_school_population'] ?? null) : null,
            'counselor_num_boys' => $isCounselor ? ($validated['counselor_num_boys'] ?? null) : null,
            'counselor_num_girls' => $isCounselor ? ($validated['counselor_num_girls'] ?? null) : null,
        ]);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store(
                'profile-photos/' . $newStaff->id,
                config('filesystems.uploads', 'public')
            );
            $newStaff->update(['profile_photo' => $path]);
        }

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
        $staff_user->load('counselorEducation');

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
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'remove_photo' => 'nullable|boolean',
            'counselor_school' => 'required_if:role,counselor|nullable|string|max:255',
            'counselor_county' => 'required_if:role,counselor|nullable|in:' . implode(',', User::COUNTIES),
            'counselor_status' => 'required_if:role,counselor|nullable|in:' . implode(',', array_keys(User::COUNSELOR_STATUSES)),
            'counselor_specialization' => 'nullable|in:' . implode(',', array_keys(User::COUNSELOR_SPECIALIZATIONS)),
            'counselor_years_experience' => 'nullable|integer|min:0|max:50',
            'counselor_school_phone' => 'nullable|string|max:50',
            // Personal information
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:' . implode(',', array_keys(User::GENDERS)),
            'nationality' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:50',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            // School & assignment details
            'counselor_assignment_date' => 'nullable|date',
            'counselor_school_district' => 'nullable|string|max:255',
            'counselor_school_address' => 'nullable|string|max:500',
            'counselor_school_principal' => 'nullable|string|max:255',
            'counselor_school_level' => 'nullable|in:' . implode(',', array_keys(User::SCHOOL_LEVELS)),
            'counselor_school_type' => 'nullable|in:' . implode(',', array_keys(User::SCHOOL_TYPES)),
            'counselor_school_population' => 'nullable|integer|min:0|max:50000',
            'counselor_num_boys' => 'nullable|integer|min:0|max:50000',
            'counselor_num_girls' => 'nullable|integer|min:0|max:50000',
        ]);

        $isCounselor = $validated['role'] === User::ROLE_COUNSELOR;

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'position' => $validated['position'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'counselor_school' => $isCounselor ? ($validated['counselor_school'] ?? null) : null,
            'counselor_county' => $isCounselor ? ($validated['counselor_county'] ?? null) : null,
            'counselor_status' => $isCounselor ? ($validated['counselor_status'] ?? 'active') : null,
            'counselor_specialization' => $isCounselor ? ($validated['counselor_specialization'] ?? null) : null,
            'counselor_years_experience' => $isCounselor ? ($validated['counselor_years_experience'] ?? null) : null,
            'counselor_school_phone' => $isCounselor ? ($validated['counselor_school_phone'] ?? null) : null,
            // Personal information
            'date_of_birth' => $isCounselor ? ($validated['date_of_birth'] ?? null) : null,
            'gender' => $isCounselor ? ($validated['gender'] ?? null) : null,
            'nationality' => $isCounselor ? ($validated['nationality'] ?? null) : null,
            'address' => $isCounselor ? ($validated['address'] ?? null) : null,
            'city' => $isCounselor ? ($validated['city'] ?? null) : null,
            'emergency_contact_name' => $isCounselor ? ($validated['emergency_contact_name'] ?? null) : null,
            'emergency_contact_phone' => $isCounselor ? ($validated['emergency_contact_phone'] ?? null) : null,
            'emergency_contact_relationship' => $isCounselor ? ($validated['emergency_contact_relationship'] ?? null) : null,
            // School & assignment details
            'counselor_assignment_date' => $isCounselor ? ($validated['counselor_assignment_date'] ?? null) : null,
            'counselor_school_district' => $isCounselor ? ($validated['counselor_school_district'] ?? null) : null,
            'counselor_school_address' => $isCounselor ? ($validated['counselor_school_address'] ?? null) : null,
            'counselor_school_principal' => $isCounselor ? ($validated['counselor_school_principal'] ?? null) : null,
            'counselor_school_level' => $isCounselor ? ($validated['counselor_school_level'] ?? null) : null,
            'counselor_school_type' => $isCounselor ? ($validated['counselor_school_type'] ?? null) : null,
            'counselor_school_population' => $isCounselor ? ($validated['counselor_school_population'] ?? null) : null,
            'counselor_num_boys' => $isCounselor ? ($validated['counselor_num_boys'] ?? null) : null,
            'counselor_num_girls' => $isCounselor ? ($validated['counselor_num_girls'] ?? null) : null,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $staff_user->update($data);

        // Handle profile photo
        if ($request->boolean('remove_photo')) {
            $staff_user->deleteProfilePhoto();
        } elseif ($request->hasFile('profile_photo')) {
            if ($staff_user->profile_photo) {
                Storage::disk(config('filesystems.uploads', 'public'))->delete($staff_user->profile_photo);
            }
            $path = $request->file('profile_photo')->store(
                'profile-photos/' . $staff_user->id,
                config('filesystems.uploads', 'public')
            );
            $staff_user->update(['profile_photo' => $path]);
        }

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
