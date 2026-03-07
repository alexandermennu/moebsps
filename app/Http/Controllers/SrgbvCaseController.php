<?php

namespace App\Http\Controllers;

use App\Models\BureauNotification;
use App\Models\Division;
use App\Models\Incident;
use App\Models\SrgbvCase;
use App\Models\SrgbvCaseFile;
use App\Models\SrgbvCaseNote;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SrgbvCaseController extends Controller
{
    /**
     * Determine if the user can access SRGBV cases.
     */
    private function canAccessSrgbv(User $user): bool
    {
        // Full-access users
        if ($user->hasFullAccess()) return true;

        // CGPC division director
        if ($user->isDirector() && $user->division && $user->division->code === 'CGPC') return true;

        // Counselors and CGPC division staff
        if ($user->isCounselor()) return true;
        if (in_array($user->role, [User::ROLE_SUPERVISOR, User::ROLE_COORDINATOR]) && $user->division && $user->division->code === 'CGPC') return true;

        return false;
    }

    /**
     * Can the user manage cases (edit status, assign, resolve)?
     */
    private function canManageCases(User $user): bool
    {
        if ($user->hasFullAccess()) return true;
        if ($user->isDirector() && $user->division && $user->division->code === 'CGPC') return true;
        return false;
    }

    /**
     * List all SRGBV cases.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$this->canAccessSrgbv($user)) abort(403);

        $query = SrgbvCase::with(['reporter', 'assignee', 'division']);

        // Non-full-access users: scope to their division
        if (!$user->hasFullAccess() && !($user->isDirector() && $user->division && $user->division->code === 'CGPC')) {
            $query->where(function ($q) use ($user) {
                $q->where('division_id', $user->division_id)
                  ->orWhere('reported_by', $user->id)
                  ->orWhere('assigned_to', $user->id);
            });
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('case_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('victim_name', 'like', "%{$search}%");
            });
        }
        if ($request->filled('date_from')) {
            $query->where('incident_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('incident_date', '<=', $request->date_to);
        }

        $cases = $query->latest()->paginate(15)->withQueryString();

        return view('srgbv.cases.index', [
            'cases' => $cases,
            'user' => $user,
            'canManage' => $this->canManageCases($user),
        ]);
    }

    /**
     * Show case report form.
     */
    public function create()
    {
        $user = auth()->user();
        if (!$this->canAccessSrgbv($user)) abort(403);

        $counselors = User::where('role', User::ROLE_COUNSELOR)
            ->where('is_active', true)
            ->where('approval_status', 'approved')
            ->get();

        // Get CGPC division
        $cgpcDivision = Division::where('code', 'CGPC')->first();

        return view('srgbv.cases.create', [
            'user' => $user,
            'counselors' => $counselors,
            'cgpcDivision' => $cgpcDivision,
        ]);
    }

    /**
     * Store a new SRGBV case.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        if (!$this->canAccessSrgbv($user)) abort(403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => ['required', Rule::in(array_keys(SrgbvCase::CATEGORIES))],
            'priority' => ['required', Rule::in(array_keys(SrgbvCase::PRIORITIES))],
            'victim_name' => 'required|string|max:255',
            'victim_age' => ['nullable', Rule::in(array_keys(Incident::VICTIM_AGE_RANGES))],
            'victim_gender' => 'nullable|string|max:50',
            'victim_grade' => 'nullable|string|max:50',
            'victim_school' => 'nullable|string|max:255',
            'victim_contact' => 'nullable|string|max:100',
            'victim_parent_guardian' => 'nullable|string|max:255',
            'victim_parent_contact' => 'nullable|string|max:100',
            'perpetrator_name' => 'nullable|string|max:255',
            'perpetrator_type' => ['nullable', Rule::in(array_keys(SrgbvCase::PERPETRATOR_TYPES))],
            'perpetrator_description' => 'nullable|string',
            'incident_date' => 'required|date|before_or_equal:today',
            'incident_location' => 'nullable|string|max:255',
            'incident_description' => 'nullable|string',
            'witnesses' => 'nullable|string',
            'is_recurring' => 'boolean',
            'assigned_to' => 'nullable|exists:users,id',
            'is_confidential' => 'boolean',
            'risk_level' => ['nullable', Rule::in(array_keys(SrgbvCase::RISK_LEVELS))],
            'immediate_action_required' => 'boolean',
            'safety_plan' => 'nullable|string',
            'files.*' => 'nullable|file|max:10240',
            'file_categories.*' => 'nullable|string',
            'file_descriptions.*' => 'nullable|string|max:255',
        ]);

        // Get CGPC division
        $cgpcDivision = Division::where('code', 'CGPC')->first();

        $case = SrgbvCase::create([
            'case_number' => SrgbvCase::generateCaseNumber(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'status' => SrgbvCase::STATUS_REPORTED,
            'victim_name' => $validated['victim_name'],
            'victim_age' => $validated['victim_age'] ?? null,
            'victim_gender' => $validated['victim_gender'] ?? null,
            'victim_grade' => $validated['victim_grade'] ?? null,
            'victim_school' => $validated['victim_school'] ?? null,
            'victim_contact' => $validated['victim_contact'] ?? null,
            'victim_parent_guardian' => $validated['victim_parent_guardian'] ?? null,
            'victim_parent_contact' => $validated['victim_parent_contact'] ?? null,
            'perpetrator_name' => $validated['perpetrator_name'] ?? null,
            'perpetrator_type' => $validated['perpetrator_type'] ?? null,
            'perpetrator_description' => $validated['perpetrator_description'] ?? null,
            'incident_date' => $validated['incident_date'],
            'incident_location' => $validated['incident_location'] ?? null,
            'incident_description' => $validated['incident_description'] ?? null,
            'witnesses' => $validated['witnesses'] ?? null,
            'is_recurring' => $request->boolean('is_recurring'),
            'reported_by' => $user->id,
            'assigned_to' => $validated['assigned_to'] ?? null,
            'division_id' => $cgpcDivision ? $cgpcDivision->id : $user->division_id,
            'is_confidential' => $request->boolean('is_confidential', true),
            'risk_level' => $validated['risk_level'] ?? null,
            'immediate_action_required' => $request->boolean('immediate_action_required'),
            'safety_plan' => $validated['safety_plan'] ?? null,
        ]);

        // Handle file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $index => $file) {
                $path = $file->store('srgbv-cases/' . $case->id, config('filesystems.uploads', 'public'));
                SrgbvCaseFile::create([
                    'srgbv_case_id' => $case->id,
                    'uploaded_by' => $user->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'category' => $request->input("file_categories.{$index}", 'evidence'),
                    'description' => $request->input("file_descriptions.{$index}"),
                ]);
            }
        }

        // Notify CGPC director and full-access users
        $notifyUsers = User::where('is_active', true)
            ->where('approval_status', 'approved')
            ->where(function ($q) use ($cgpcDivision) {
                $q->whereIn('role', [User::ROLE_MINISTER, User::ROLE_ADMIN_ASSISTANT, User::ROLE_TECH_ASSISTANT])
                  ->orWhere(function ($q2) use ($cgpcDivision) {
                      if ($cgpcDivision) {
                          $q2->where('role', User::ROLE_DIRECTOR)
                             ->where('division_id', $cgpcDivision->id);
                      }
                  });
            })
            ->where('id', '!=', $user->id)
            ->get();

        foreach ($notifyUsers as $notifyUser) {
            BureauNotification::create([
                'user_id' => $notifyUser->id,
                'type' => 'srgbv_case',
                'title' => 'New SRGBV Case Reported',
                'message' => "{$user->name} reported a new {$case->priority} priority SRGBV case: {$case->title} ({$case->case_number})",
                'link' => route('srgbv.cases.show', $case),
            ]);
        }

        // Notify assigned counselor
        if ($case->assigned_to && $case->assigned_to !== $user->id) {
            BureauNotification::create([
                'user_id' => $case->assigned_to,
                'type' => 'srgbv_case',
                'title' => 'SRGBV Case Assigned to You',
                'message' => "You have been assigned to case {$case->case_number}: {$case->title}",
                'link' => route('srgbv.cases.show', $case),
            ]);
        }

        return redirect()->route('srgbv.cases.show', $case)
            ->with('success', "Case {$case->case_number} has been reported successfully.");
    }

    /**
     * Show case details.
     */
    public function show(SrgbvCase $srgbvCase)
    {
        $user = auth()->user();
        if (!$this->canAccessSrgbv($user)) abort(403);

        $srgbvCase->load([
            'reporter', 'assignee', 'division',
            'notes' => fn($q) => $q->with('user'),
            'files' => fn($q) => $q->with('uploader'),
        ]);

        // Non-managers see only non-private notes
        $notes = $srgbvCase->notes;
        if (!$this->canManageCases($user)) {
            $notes = $notes->where('is_private', false);
        }

        $counselors = User::where('role', User::ROLE_COUNSELOR)
            ->where('is_active', true)
            ->where('approval_status', 'approved')
            ->get();

        return view('srgbv.cases.show', [
            'case' => $srgbvCase,
            'notes' => $notes,
            'user' => $user,
            'canManage' => $this->canManageCases($user),
            'counselors' => $counselors,
        ]);
    }

    /**
     * Show edit form (managers only).
     */
    public function edit(SrgbvCase $srgbvCase)
    {
        $user = auth()->user();
        if (!$this->canManageCases($user)) abort(403);

        $counselors = User::where('role', User::ROLE_COUNSELOR)
            ->where('is_active', true)
            ->where('approval_status', 'approved')
            ->get();

        $cgpcDivision = Division::where('code', 'CGPC')->first();

        return view('srgbv.cases.edit', [
            'case' => $srgbvCase,
            'user' => $user,
            'counselors' => $counselors,
            'cgpcDivision' => $cgpcDivision,
        ]);
    }

    /**
     * Update a case.
     */
    public function update(Request $request, SrgbvCase $srgbvCase)
    {
        $user = $request->user();
        if (!$this->canManageCases($user)) abort(403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => ['required', Rule::in(array_keys(SrgbvCase::CATEGORIES))],
            'priority' => ['required', Rule::in(array_keys(SrgbvCase::PRIORITIES))],
            'status' => ['required', Rule::in(array_keys(SrgbvCase::STATUSES))],
            'victim_name' => 'required|string|max:255',
            'victim_age' => ['nullable', Rule::in(array_keys(Incident::VICTIM_AGE_RANGES))],
            'victim_gender' => 'nullable|string|max:50',
            'victim_grade' => 'nullable|string|max:50',
            'victim_school' => 'nullable|string|max:255',
            'victim_contact' => 'nullable|string|max:100',
            'victim_parent_guardian' => 'nullable|string|max:255',
            'victim_parent_contact' => 'nullable|string|max:100',
            'perpetrator_name' => 'nullable|string|max:255',
            'perpetrator_type' => ['nullable', Rule::in(array_keys(SrgbvCase::PERPETRATOR_TYPES))],
            'perpetrator_description' => 'nullable|string',
            'incident_date' => 'required|date',
            'incident_location' => 'nullable|string|max:255',
            'incident_description' => 'nullable|string',
            'witnesses' => 'nullable|string',
            'is_recurring' => 'boolean',
            'assigned_to' => 'nullable|exists:users,id',
            'is_confidential' => 'boolean',
            'resolution' => 'nullable|string',
            'resolution_date' => 'nullable|date',
            'referral_agency' => 'nullable|string|max:255',
            'referral_details' => 'nullable|string',
            'follow_up_required' => 'boolean',
            'follow_up_date' => 'nullable|date',
            'risk_level' => ['nullable', Rule::in(array_keys(SrgbvCase::RISK_LEVELS))],
            'immediate_action_required' => 'boolean',
            'safety_plan' => 'nullable|string',
        ]);

        $oldStatus = $srgbvCase->status;
        $oldAssignee = $srgbvCase->assigned_to;

        $validated['is_recurring'] = $request->boolean('is_recurring');
        $validated['is_confidential'] = $request->boolean('is_confidential', true);
        $validated['follow_up_required'] = $request->boolean('follow_up_required');
        $validated['immediate_action_required'] = $request->boolean('immediate_action_required');

        $srgbvCase->update($validated);

        // Auto-create a note if status changed
        if ($oldStatus !== $srgbvCase->status) {
            SrgbvCaseNote::create([
                'srgbv_case_id' => $srgbvCase->id,
                'user_id' => $user->id,
                'note' => "Case status changed from " . (SrgbvCase::STATUSES[$oldStatus] ?? $oldStatus) . " to " . $srgbvCase->status_label . ".",
                'note_type' => 'progress_update',
            ]);

            // Notify reporter of status change
            if ($srgbvCase->reported_by !== $user->id) {
                BureauNotification::create([
                    'user_id' => $srgbvCase->reported_by,
                    'type' => 'srgbv_case',
                    'title' => 'SRGBV Case Status Updated',
                    'message' => "Case {$srgbvCase->case_number} status changed to {$srgbvCase->status_label}.",
                    'link' => route('srgbv.cases.show', $srgbvCase),
                ]);
            }
        }

        // Notify newly assigned counselor
        if ($oldAssignee !== $srgbvCase->assigned_to && $srgbvCase->assigned_to && $srgbvCase->assigned_to !== $user->id) {
            BureauNotification::create([
                'user_id' => $srgbvCase->assigned_to,
                'type' => 'srgbv_case',
                'title' => 'SRGBV Case Assigned to You',
                'message' => "You have been assigned to case {$srgbvCase->case_number}: {$srgbvCase->title}",
                'link' => route('srgbv.cases.show', $srgbvCase),
            ]);
        }

        return redirect()->route('srgbv.cases.show', $srgbvCase)
            ->with('success', 'Case updated successfully.');
    }

    /**
     * Add a note/progress update to a case.
     */
    public function addNote(Request $request, SrgbvCase $srgbvCase)
    {
        $user = $request->user();
        if (!$this->canAccessSrgbv($user)) abort(403);

        $validated = $request->validate([
            'note' => 'required|string',
            'note_type' => ['required', Rule::in(array_keys(SrgbvCaseNote::NOTE_TYPES))],
            'is_private' => 'boolean',
        ]);

        SrgbvCaseNote::create([
            'srgbv_case_id' => $srgbvCase->id,
            'user_id' => $user->id,
            'note' => $validated['note'],
            'note_type' => $validated['note_type'],
            'is_private' => $request->boolean('is_private'),
        ]);

        return redirect()->route('srgbv.cases.show', $srgbvCase)
            ->with('success', 'Note added successfully.');
    }

    /**
     * Upload files to a case.
     */
    public function uploadFiles(Request $request, SrgbvCase $srgbvCase)
    {
        $user = $request->user();
        if (!$this->canAccessSrgbv($user)) abort(403);

        $request->validate([
            'files' => 'required',
            'files.*' => 'file|max:10240',
            'file_category' => 'nullable|string',
            'file_description' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('srgbv-cases/' . $srgbvCase->id, config('filesystems.uploads', 'public'));
                SrgbvCaseFile::create([
                    'srgbv_case_id' => $srgbvCase->id,
                    'uploaded_by' => $user->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'category' => $request->input('file_category', 'evidence'),
                    'description' => $request->input('file_description'),
                ]);
            }
        }

        return redirect()->route('srgbv.cases.show', $srgbvCase)
            ->with('success', 'Files uploaded successfully.');
    }

    /**
     * Delete a file from a case.
     */
    public function deleteFile(SrgbvCase $srgbvCase, SrgbvCaseFile $file)
    {
        $user = auth()->user();
        if (!$this->canManageCases($user) && $file->uploaded_by !== $user->id) abort(403);

        Storage::disk(config('filesystems.uploads', 'public'))->delete($file->file_path);
        $file->delete();

        return redirect()->route('srgbv.cases.show', $srgbvCase)
            ->with('success', 'File deleted.');
    }

    /**
     * Quick status update (for the show page).
     */
    public function updateStatus(Request $request, SrgbvCase $srgbvCase)
    {
        $user = $request->user();
        if (!$this->canManageCases($user)) abort(403);

        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(SrgbvCase::STATUSES))],
        ]);

        $oldStatus = $srgbvCase->status;
        $srgbvCase->update(['status' => $validated['status']]);

        // Add status change note
        SrgbvCaseNote::create([
            'srgbv_case_id' => $srgbvCase->id,
            'user_id' => $user->id,
            'note' => "Status changed from " . (SrgbvCase::STATUSES[$oldStatus] ?? $oldStatus) . " to " . $srgbvCase->status_label . ".",
            'note_type' => 'progress_update',
        ]);

        // Notify reporter
        if ($srgbvCase->reported_by !== $user->id) {
            BureauNotification::create([
                'user_id' => $srgbvCase->reported_by,
                'type' => 'srgbv_case',
                'title' => 'Case Status Updated',
                'message' => "Case {$srgbvCase->case_number} status changed to " . SrgbvCase::STATUSES[$validated['status']] . ".",
                'link' => route('srgbv.cases.show', $srgbvCase),
            ]);
        }

        return redirect()->route('srgbv.cases.show', $srgbvCase)
            ->with('success', 'Case status updated.');
    }

    /**
     * Delete a case and all associated files, notes.
     */
    public function destroy(SrgbvCase $srgbvCase)
    {
        $user = auth()->user();

        if (!$this->canManageCases($user)) {
            abort(403);
        }

        $caseNumber = $srgbvCase->case_number;

        // Delete all associated files from storage
        foreach ($srgbvCase->files as $file) {
            Storage::disk(config('filesystems.uploads', 'public'))->delete($file->file_path);
        }

        // Delete case (notes and files cascade via foreign key)
        $srgbvCase->delete();

        return redirect()->route('srgbv.cases.index')
            ->with('success', "Case {$caseNumber} has been permanently deleted.");
    }
}
