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
            'counselor_qualification' => 'nullable|in:' . implode(',', array_keys(User::COUNSELOR_QUALIFICATIONS)),
            'counselor_specialization' => 'nullable|in:' . implode(',', array_keys(User::COUNSELOR_SPECIALIZATIONS)),
            'counselor_years_experience' => 'nullable|integer|min:0|max:50',
            'counselor_school_phone' => 'nullable|string|max:50',
            'edu_institution' => 'nullable|string|max:255',
            'edu_program' => 'nullable|string|max:255',
            'edu_year_started' => 'nullable|integer|min:1950|max:' . (date('Y') + 5),
            'edu_year_graduated' => 'nullable|integer|min:1950|max:' . (date('Y') + 5),
            'edu_country' => 'nullable|string|max:100',
            'edu_notes' => 'nullable|string|max:1000',
        ]);

        $eduFields = collect($validated)->filter(fn($v, $k) => str_starts_with($k, 'edu_'))->toArray();

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
            'counselor_school' => $validated['role'] === User::ROLE_COUNSELOR ? ($validated['counselor_school'] ?? null) : null,
            'counselor_county' => $validated['role'] === User::ROLE_COUNSELOR ? ($validated['counselor_county'] ?? null) : null,
            'counselor_status' => $validated['role'] === User::ROLE_COUNSELOR ? ($validated['counselor_status'] ?? 'active') : null,
            'counselor_qualification' => $validated['role'] === User::ROLE_COUNSELOR ? ($validated['counselor_qualification'] ?? null) : null,
            'counselor_specialization' => $validated['role'] === User::ROLE_COUNSELOR ? ($validated['counselor_specialization'] ?? null) : null,
            'counselor_years_experience' => $validated['role'] === User::ROLE_COUNSELOR ? ($validated['counselor_years_experience'] ?? null) : null,
            'counselor_school_phone' => $validated['role'] === User::ROLE_COUNSELOR ? ($validated['counselor_school_phone'] ?? null) : null,
        ]);

        // Save education record for counselors
        if ($validated['role'] === User::ROLE_COUNSELOR && !empty($eduFields['edu_institution']) && !empty($validated['counselor_qualification'])) {
            $newStaff->counselorEducation()->create([
                'institution' => $eduFields['edu_institution'],
                'program' => $eduFields['edu_program'] ?? null,
                'degree_level' => $validated['counselor_qualification'],
                'year_started' => $eduFields['edu_year_started'] ?? null,
                'year_graduated' => $eduFields['edu_year_graduated'] ?? null,
                'country' => $eduFields['edu_country'] ?? null,
                'notes' => $eduFields['edu_notes'] ?? null,
            ]);
        }

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
            'counselor_qualification' => 'nullable|in:' . implode(',', array_keys(User::COUNSELOR_QUALIFICATIONS)),
            'counselor_specialization' => 'nullable|in:' . implode(',', array_keys(User::COUNSELOR_SPECIALIZATIONS)),
            'counselor_years_experience' => 'nullable|integer|min:0|max:50',
            'counselor_school_phone' => 'nullable|string|max:50',
            'edu_institution' => 'nullable|string|max:255',
            'edu_program' => 'nullable|string|max:255',
            'edu_year_started' => 'nullable|integer|min:1950|max:' . (date('Y') + 5),
            'edu_year_graduated' => 'nullable|integer|min:1950|max:' . (date('Y') + 5),
            'edu_country' => 'nullable|string|max:100',
            'edu_notes' => 'nullable|string|max:1000',
        ]);

        $eduFields = collect($validated)->filter(fn($v, $k) => str_starts_with($k, 'edu_'))->toArray();

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'position' => $validated['position'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'counselor_school' => $validated['role'] === User::ROLE_COUNSELOR ? ($validated['counselor_school'] ?? null) : null,
            'counselor_county' => $validated['role'] === User::ROLE_COUNSELOR ? ($validated['counselor_county'] ?? null) : null,
            'counselor_status' => $validated['role'] === User::ROLE_COUNSELOR ? ($validated['counselor_status'] ?? 'active') : null,
            'counselor_qualification' => $validated['role'] === User::ROLE_COUNSELOR ? ($validated['counselor_qualification'] ?? null) : null,
            'counselor_specialization' => $validated['role'] === User::ROLE_COUNSELOR ? ($validated['counselor_specialization'] ?? null) : null,
            'counselor_years_experience' => $validated['role'] === User::ROLE_COUNSELOR ? ($validated['counselor_years_experience'] ?? null) : null,
            'counselor_school_phone' => $validated['role'] === User::ROLE_COUNSELOR ? ($validated['counselor_school_phone'] ?? null) : null,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $staff_user->update($data);

        // Save/update education record for counselors
        if ($validated['role'] === User::ROLE_COUNSELOR && !empty($eduFields['edu_institution']) && !empty($validated['counselor_qualification'])) {
            $staff_user->counselorEducation()->updateOrCreate(
                ['degree_level' => $validated['counselor_qualification']],
                [
                    'institution' => $eduFields['edu_institution'],
                    'program' => $eduFields['edu_program'] ?? null,
                    'year_started' => $eduFields['edu_year_started'] ?? null,
                    'year_graduated' => $eduFields['edu_year_graduated'] ?? null,
                    'country' => $eduFields['edu_country'] ?? null,
                    'notes' => $eduFields['edu_notes'] ?? null,
                ]
            );
        }

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
