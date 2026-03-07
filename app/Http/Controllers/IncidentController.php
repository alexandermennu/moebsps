<?php

namespace App\Http\Controllers;

use App\Models\BureauNotification;
use App\Models\Division;
use App\Models\Incident;
use App\Models\IncidentFile;
use App\Models\IncidentNote;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class IncidentController extends Controller
{
    /**
     * Can the user access incidents of this type?
     */
    private function canAccessIncidentType(User $user, ?string $type = null): bool
    {
        if (!$user->canAccessSir()) return false;

        // If no specific type requested, they have access to at least one module
        if (!$type) return true;

        // SRGBV type requires SRGBV access
        if ($type === Incident::TYPE_SRGBV) {
            return $user->canAccessSrgbv();
        }

        // All other types require Other Incidents access
        return $user->canAccessOtherIncidents();
    }

    /**
     * Can the user manage incidents (edit, assign, resolve, delete)?
     */
    private function canManageIncidents(User $user): bool
    {
        return $user->canManageIncidents();
    }

    /**
     * Scope query based on user's SIR access (SRGBV only, Other only, or both).
     */
    private function scopeByAccess($query, User $user, ?string $module = null)
    {
        $canSrgbv = $user->canAccessSrgbv();
        $canOther = $user->canAccessOtherIncidents();

        // Module-specific filtering
        if ($module === 'srgbv') {
            $query->where('type', Incident::TYPE_SRGBV);
        } elseif ($module === 'other') {
            $query->where('type', '!=', Incident::TYPE_SRGBV);
        } elseif (!$canSrgbv && $canOther) {
            // User only has Other Incidents access
            $query->where('type', '!=', Incident::TYPE_SRGBV);
        } elseif ($canSrgbv && !$canOther) {
            // User only has SRGBV access
            $query->where('type', Incident::TYPE_SRGBV);
        }
        // If both, no type filter needed

        return $query;
    }

    /**
     * List incidents with filtering.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $module = $request->route('module') ?? $request->query('module'); // Get from route defaults or query
        
        // Check module-specific access
        if ($module === 'srgbv' && !$user->canAccessSrgbv()) abort(403);
        if ($module === 'other' && !$user->canAccessOtherIncidents()) abort(403);
        if (!$module && !$user->canAccessSir()) abort(403);

        $query = Incident::with(['reporter', 'assignee', 'division']);

        // Scope by module
        if ($module === 'srgbv') {
            $query->where('type', Incident::TYPE_SRGBV);
        } elseif ($module === 'other') {
            $query->where('type', '!=', Incident::TYPE_SRGBV);
        } else {
            $this->scopeByAccess($query, $user, null);
        }

        // Scope for non-manager users
        if (!$user->hasFullAccess() && !($user->isDirector() && $user->division && in_array($user->division->code, ['CGPC', 'CEDP']))) {
            $query->where(function ($q) use ($user) {
                $q->where('division_id', $user->division_id)
                  ->orWhere('reported_by', $user->id)
                  ->orWhere('assigned_to', $user->id);
            });
        }

        // Filters
        if ($request->filled('type') && $module !== 'srgbv') {
            $query->where('type', $request->type);
        }
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }
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
                $q->where('incident_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('victim_name', 'like', "%{$search}%")
                  ->orWhere('school_name', 'like', "%{$search}%")
                  ->orWhere('tracking_code', 'like', "%{$search}%");
            });
        }
        if ($request->filled('date_from')) {
            $query->where('incident_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('incident_date', '<=', $request->date_to);
        }

        $incidents = $query->latest()->paginate(15)->withQueryString();

        // Calculate stats for the current module
        $statsQuery = Incident::query();
        if ($module === 'srgbv') {
            $statsQuery->where('type', Incident::TYPE_SRGBV);
        } elseif ($module === 'other') {
            $statsQuery->where('type', '!=', Incident::TYPE_SRGBV);
        } else {
            $this->scopeByAccess($statsQuery, $user, null);
        }

        $openCount = (clone $statsQuery)->open()->count();
        $criticalCount = (clone $statsQuery)->critical()->open()->count();
        $closedCount = (clone $statsQuery)->closed()->count();
        $publicCount = (clone $statsQuery)->publicReports()->count();

        return view('sir.incidents.index', [
            'incidents' => $incidents,
            'user' => $user,
            'module' => $module ?? 'other', // Default to 'other' for legacy routes
            'canManage' => $this->canManageIncidents($user),
            'openCount' => $openCount,
            'criticalCount' => $criticalCount,
            'closedCount' => $closedCount,
            'publicCount' => $publicCount,
        ]);
    }

    /**
     * Show the create incident form.
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        $module = $request->route('module') ?? $request->query('module');
        
        // Check module-specific access
        if ($module === 'srgbv' && !$user->canAccessSrgbv()) abort(403);
        if ($module === 'other' && !$user->canAccessOtherIncidents()) abort(403);
        if (!$module && !$user->canAccessSir()) abort(403);

        $counselors = User::where('role', User::ROLE_COUNSELOR)
            ->where('is_active', true)
            ->where('approval_status', 'approved')
            ->get();

        $cgpcDivision = Division::where('code', 'CGPC')->first();

        // Pre-select type based on module
        $selectedType = $module === 'srgbv' ? Incident::TYPE_SRGBV : $request->query('type', null);

        return view('sir.incidents.create', [
            'user' => $user,
            'counselors' => $counselors,
            'cgpcDivision' => $cgpcDivision,
            'selectedType' => $selectedType,
            'module' => $module,
        ]);
    }

    /**
     * Store a new incident.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user->canAccessSir()) abort(403);

        $validated = $request->validate([
            'type' => ['required', Rule::in(array_keys(Incident::ALL_TYPES))],
            'category' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => ['required', Rule::in(array_keys(Incident::PRIORITIES))],
            'incident_date' => 'required|date|before_or_equal:today',
            'incident_location' => 'nullable|string|max:255',
            'incident_description' => 'nullable|string',
            'witnesses' => 'nullable|string',
            'is_recurring' => 'boolean',
            'school_name' => 'nullable|string|max:255',
            'school_county' => 'nullable|string|max:255',
            'school_district' => 'nullable|string|max:255',
            'school_level' => ['nullable', Rule::in(array_keys(Incident::SCHOOL_LEVELS))],
            'victim_name' => 'nullable|string|max:255',
            'victim_age' => ['nullable', Rule::in(array_keys(Incident::VICTIM_AGE_RANGES))],
            'victim_gender' => 'nullable|string|max:50',
            'victim_grade' => 'nullable|string|max:50',
            'victim_contact' => 'nullable|string|max:100',
            'victim_parent_guardian' => 'nullable|string|max:255',
            'victim_parent_contact' => 'nullable|string|max:100',
            'perpetrator_name' => 'nullable|string|max:255',
            'perpetrator_type' => ['nullable', Rule::in(array_keys(Incident::PERPETRATOR_TYPES))],
            'perpetrator_description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'is_confidential' => 'boolean',
            'risk_level' => ['nullable', Rule::in(array_keys(Incident::RISK_LEVELS))],
            'immediate_action_required' => 'boolean',
            'safety_plan' => 'nullable|string',
            'files.*' => 'nullable|file|max:10240',
            'file_categories.*' => 'nullable|string',
            'file_descriptions.*' => 'nullable|string|max:255',
        ]);

        // SRGBV incidents require victim name
        if ($validated['type'] === Incident::TYPE_SRGBV && empty($validated['victim_name'])) {
            return back()->withErrors(['victim_name' => 'Victim name is required for SRGBV incidents.'])->withInput();
        }

        $cgpcDivision = Division::where('code', 'CGPC')->first();

        $incident = Incident::create([
            'incident_number' => Incident::generateIncidentNumber($validated['type'], 'internal'),
            'type' => $validated['type'],
            'category' => $validated['category'],
            'source' => Incident::SOURCE_INTERNAL,
            'status' => Incident::STATUS_REPORTED,
            'priority' => $validated['priority'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'incident_date' => $validated['incident_date'],
            'incident_location' => $validated['incident_location'] ?? null,
            'incident_description' => $validated['incident_description'] ?? null,
            'witnesses' => $validated['witnesses'] ?? null,
            'is_recurring' => $request->boolean('is_recurring'),
            'school_name' => $validated['school_name'] ?? null,
            'school_county' => $validated['school_county'] ?? null,
            'school_district' => $validated['school_district'] ?? null,
            'school_level' => $validated['school_level'] ?? null,
            'victim_name' => $validated['victim_name'] ?? null,
            'victim_age' => $validated['victim_age'] ?? null,
            'victim_gender' => $validated['victim_gender'] ?? null,
            'victim_grade' => $validated['victim_grade'] ?? null,
            'victim_contact' => $validated['victim_contact'] ?? null,
            'victim_parent_guardian' => $validated['victim_parent_guardian'] ?? null,
            'victim_parent_contact' => $validated['victim_parent_contact'] ?? null,
            'perpetrator_name' => $validated['perpetrator_name'] ?? null,
            'perpetrator_type' => $validated['perpetrator_type'] ?? null,
            'perpetrator_description' => $validated['perpetrator_description'] ?? null,
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
                $path = $file->store('sir-incidents/' . $incident->id, config('filesystems.uploads', 'public'));
                IncidentFile::create([
                    'incident_id' => $incident->id,
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

        // Determine module and route for notifications
        $module = $incident->type === Incident::TYPE_SRGBV ? 'srgbv' : 'other';
        $showRoute = $module === 'srgbv' ? 'sir.srgbv.cases.show' : 'sir.other.incidents.show';
        $incidentLink = route($showRoute, $incident);

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

        $typeLabel = Incident::TYPES[$incident->type] ?? $incident->type;
        foreach ($notifyUsers as $notifyUser) {
            BureauNotification::create([
                'user_id' => $notifyUser->id,
                'type' => 'incident',
                'title' => 'New Incident Reported',
                'message' => "{$user->name} reported a {$incident->priority} priority {$typeLabel} incident: {$incident->title} ({$incident->incident_number})",
                'link' => $incidentLink,
            ]);
        }

        // Notify assigned person
        if ($incident->assigned_to && $incident->assigned_to !== $user->id) {
            BureauNotification::create([
                'user_id' => $incident->assigned_to,
                'type' => 'incident',
                'title' => 'Incident Assigned to You',
                'message' => "You have been assigned to incident {$incident->incident_number}: {$incident->title}",
                'link' => $incidentLink,
            ]);
        }

        return redirect()->route($showRoute, $incident)
            ->with('success', "Incident {$incident->incident_number} has been reported successfully.");
    }

    /**
     * Get the appropriate route name for an incident based on its type.
     */
    private function getIncidentRoute(Incident $incident, string $action = 'show'): string
    {
        $module = $incident->type === Incident::TYPE_SRGBV ? 'srgbv' : 'other';
        $entity = $module === 'srgbv' ? 'cases' : 'incidents';
        return "sir.{$module}.{$entity}.{$action}";
    }

    /**
     * Show incident details.
     */
    public function show(Request $request, Incident $incident)
    {
        $user = auth()->user();
        $module = $request->route('module') ?? ($incident->type === Incident::TYPE_SRGBV ? 'srgbv' : 'other');
        
        if (!$this->canAccessIncidentType($user, $incident->type)) abort(403);

        $incident->load([
            'reporter', 'assignee', 'division',
            'notes' => fn($q) => $q->with('user'),
            'files' => fn($q) => $q->with('uploader'),
        ]);

        // Non-managers see only non-private notes
        $notes = $incident->notes ?? collect();
        if (!$this->canManageIncidents($user)) {
            $notes = $notes->where('is_private', false);
        }

        $counselors = User::where('role', User::ROLE_COUNSELOR)
            ->where('is_active', true)
            ->where('approval_status', 'approved')
            ->get();

        return view('sir.incidents.show', [
            'incident' => $incident,
            'notes' => $notes,
            'user' => $user,
            'module' => $module,
            'canManage' => $this->canManageIncidents($user),
            'counselors' => $counselors,
        ]);
    }

    /**
     * Show the edit form (managers only).
     */
    public function edit(Request $request, Incident $incident)
    {
        $user = auth()->user();
        $module = $request->route('module') ?? ($incident->type === Incident::TYPE_SRGBV ? 'srgbv' : 'other');
        
        if (!$this->canManageIncidents($user)) abort(403);
        if (!$this->canAccessIncidentType($user, $incident->type)) abort(403);

        $counselors = User::where('role', User::ROLE_COUNSELOR)
            ->where('is_active', true)
            ->where('approval_status', 'approved')
            ->get();

        $cgpcDivision = Division::where('code', 'CGPC')->first();

        return view('sir.incidents.edit', [
            'incident' => $incident,
            'user' => $user,
            'counselors' => $counselors,
            'cgpcDivision' => $cgpcDivision,
        ]);
    }

    /**
     * Update an incident.
     */
    public function update(Request $request, Incident $incident)
    {
        $user = $request->user();
        if (!$this->canManageIncidents($user)) abort(403);

        $validated = $request->validate([
            'type' => ['required', Rule::in(array_keys(Incident::ALL_TYPES))],
            'category' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => ['required', Rule::in(array_keys(Incident::PRIORITIES))],
            'status' => ['required', Rule::in(array_keys(Incident::STATUSES))],
            'incident_date' => 'required|date',
            'incident_location' => 'nullable|string|max:255',
            'incident_description' => 'nullable|string',
            'witnesses' => 'nullable|string',
            'is_recurring' => 'boolean',
            'school_name' => 'nullable|string|max:255',
            'school_county' => 'nullable|string|max:255',
            'school_district' => 'nullable|string|max:255',
            'school_level' => ['nullable', Rule::in(array_keys(Incident::SCHOOL_LEVELS))],
            'victim_name' => 'nullable|string|max:255',
            'victim_age' => ['nullable', Rule::in(array_keys(Incident::VICTIM_AGE_RANGES))],
            'victim_gender' => 'nullable|string|max:50',
            'victim_grade' => 'nullable|string|max:50',
            'victim_contact' => 'nullable|string|max:100',
            'victim_parent_guardian' => 'nullable|string|max:255',
            'victim_parent_contact' => 'nullable|string|max:100',
            'perpetrator_name' => 'nullable|string|max:255',
            'perpetrator_type' => ['nullable', Rule::in(array_keys(Incident::PERPETRATOR_TYPES))],
            'perpetrator_description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'is_confidential' => 'boolean',
            'resolution' => 'nullable|string',
            'resolution_date' => 'nullable|date',
            'referral_agency' => 'nullable|string|max:255',
            'referral_details' => 'nullable|string',
            'follow_up_required' => 'boolean',
            'follow_up_date' => 'nullable|date',
            'risk_level' => ['nullable', Rule::in(array_keys(Incident::RISK_LEVELS))],
            'immediate_action_required' => 'boolean',
            'safety_plan' => 'nullable|string',
        ]);

        $oldStatus = $incident->status;
        $oldAssignee = $incident->assigned_to;

        $validated['is_recurring'] = $request->boolean('is_recurring');
        $validated['is_confidential'] = $request->boolean('is_confidential', true);
        $validated['follow_up_required'] = $request->boolean('follow_up_required');
        $validated['immediate_action_required'] = $request->boolean('immediate_action_required');

        $incident->update($validated);

        // Auto-create note on status change
        if ($oldStatus !== $incident->status) {
            IncidentNote::create([
                'incident_id' => $incident->id,
                'user_id' => $user->id,
                'note' => "Status changed from " . (Incident::STATUSES[$oldStatus] ?? $oldStatus) . " to " . $incident->status_label . ".",
                'note_type' => 'status_change',
            ]);

            // Notify reporter
            if ($incident->reported_by && $incident->reported_by !== $user->id) {
                BureauNotification::create([
                    'user_id' => $incident->reported_by,
                    'type' => 'incident',
                    'title' => 'Incident Status Updated',
                    'message' => "Incident {$incident->incident_number} status changed to {$incident->status_label}.",
                    'link' => route('sir.incidents.show', $incident),
                ]);
            }
        }

        // Notify newly assigned person
        if ($oldAssignee !== $incident->assigned_to && $incident->assigned_to && $incident->assigned_to !== $user->id) {
            BureauNotification::create([
                'user_id' => $incident->assigned_to,
                'type' => 'incident',
                'title' => 'Incident Assigned to You',
                'message' => "You have been assigned to incident {$incident->incident_number}: {$incident->title}",
                'link' => route('sir.incidents.show', $incident),
            ]);
        }

        return redirect()->route('sir.incidents.show', $incident)
            ->with('success', 'Incident updated successfully.');
    }

    /**
     * Add a note to an incident.
     */
    public function addNote(Request $request, Incident $incident)
    {
        $user = $request->user();
        if (!$this->canAccessIncidentType($user, $incident->type)) abort(403);

        $validated = $request->validate([
            'note' => 'required|string',
            'note_type' => ['required', Rule::in(array_keys(IncidentNote::NOTE_TYPES))],
            'is_private' => 'boolean',
        ]);

        IncidentNote::create([
            'incident_id' => $incident->id,
            'user_id' => $user->id,
            'note' => $validated['note'],
            'note_type' => $validated['note_type'],
            'is_private' => $request->boolean('is_private'),
        ]);

        return redirect()->route('sir.incidents.show', $incident)
            ->with('success', 'Note added successfully.');
    }

    /**
     * Upload files to an incident.
     */
    public function uploadFiles(Request $request, Incident $incident)
    {
        $user = $request->user();
        if (!$this->canAccessIncidentType($user, $incident->type)) abort(403);

        $request->validate([
            'files' => 'required',
            'files.*' => 'file|max:10240',
            'file_category' => 'nullable|string',
            'file_description' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('sir-incidents/' . $incident->id, config('filesystems.uploads', 'public'));
                IncidentFile::create([
                    'incident_id' => $incident->id,
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

        return redirect()->route('sir.incidents.show', $incident)
            ->with('success', 'Files uploaded successfully.');
    }

    /**
     * Delete a file from an incident.
     */
    public function deleteFile(Incident $incident, IncidentFile $file)
    {
        $user = auth()->user();
        if (!$this->canManageIncidents($user) && $file->uploaded_by !== $user->id) abort(403);

        Storage::disk(config('filesystems.uploads', 'public'))->delete($file->file_path);
        $file->delete();

        return redirect()->route('sir.incidents.show', $incident)
            ->with('success', 'File deleted.');
    }

    /**
     * Quick status update from the show page.
     */
    public function updateStatus(Request $request, Incident $incident)
    {
        $user = $request->user();
        if (!$this->canManageIncidents($user)) abort(403);

        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(Incident::STATUSES))],
        ]);

        $oldStatus = $incident->status;
        $incident->update(['status' => $validated['status']]);

        IncidentNote::create([
            'incident_id' => $incident->id,
            'user_id' => $user->id,
            'note' => "Status changed from " . (Incident::STATUSES[$oldStatus] ?? $oldStatus) . " to " . $incident->status_label . ".",
            'note_type' => 'status_change',
        ]);

        // Notify reporter (if internal)
        if ($incident->reported_by && $incident->reported_by !== $user->id) {
            BureauNotification::create([
                'user_id' => $incident->reported_by,
                'type' => 'incident',
                'title' => 'Incident Status Updated',
                'message' => "Incident {$incident->incident_number} status changed to " . Incident::STATUSES[$validated['status']] . ".",
                'link' => route('sir.incidents.show', $incident),
            ]);
        }

        return redirect()->route('sir.incidents.show', $incident)
            ->with('success', 'Incident status updated.');
    }

    /**
     * Export cases list as PDF.
     */
    public function exportList(Request $request)
    {
        $user = $request->user();
        $module = $request->route('module') ?? $request->query('module');
        
        if ($module === 'srgbv' && !$user->canAccessSrgbv()) abort(403);
        if ($module === 'other' && !$user->canAccessOtherIncidents()) abort(403);

        $query = Incident::with(['reporter', 'assignee']);

        if ($module === 'srgbv') {
            $query->where('type', Incident::TYPE_SRGBV);
        } elseif ($module === 'other') {
            $query->where('type', '!=', Incident::TYPE_SRGBV);
        }

        // Apply same filters as index
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('priority')) $query->where('priority', $request->priority);
        if ($request->filled('source')) $query->where('source', $request->source);
        if ($request->filled('date_from')) $query->where('incident_date', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->where('incident_date', '<=', $request->date_to);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('incident_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('school_name', 'like', "%{$search}%");
            });
        }

        $incidents = $query->latest()->get();
        $format = $request->query('format', 'pdf');
        $moduleLabel = $module === 'srgbv' ? 'SRGBV Cases' : 'Other Incidents';

        return view('sir.exports.cases-list', [
            'incidents' => $incidents,
            'module' => $module,
            'moduleLabel' => $moduleLabel,
            'format' => $format,
            'exportDate' => now(),
            'user' => $user,
        ]);
    }

    /**
     * Export single case details as PDF/Word.
     */
    public function exportCase(Request $request, Incident $incident)
    {
        $user = $request->user();
        
        if ($incident->type === Incident::TYPE_SRGBV && !$user->canAccessSrgbv()) abort(403);
        if ($incident->type !== Incident::TYPE_SRGBV && !$user->canAccessOtherIncidents()) abort(403);

        $incident->load(['reporter', 'assignee', 'division', 'notes.user', 'files']);
        $format = $request->query('format', 'pdf');

        return view('sir.exports.case-detail', [
            'incident' => $incident,
            'format' => $format,
            'exportDate' => now(),
            'user' => $user,
        ]);
    }

    /**
     * Delete an incident.
     */
    public function destroy(Incident $incident, Request $request)
    {
        $user = auth()->user();
        if (!$this->canManageIncidents($user)) abort(403);

        $incidentNumber = $incident->incident_number;
        $module = $request->route()->defaults['module'] ?? ($incident->type === 'srgbv' ? 'srgbv' : 'other');

        // Delete files from storage
        foreach ($incident->files as $file) {
            Storage::disk(config('filesystems.uploads', 'public'))->delete($file->file_path);
        }

        $incident->delete();

        // Redirect to appropriate module's cases list
        $redirectRoute = $module === 'srgbv' ? 'sir.srgbv.cases.index' : 'sir.other.incidents.index';

        return redirect()->route($redirectRoute)
            ->with('success', "Incident {$incidentNumber} has been permanently deleted.");
    }
}
