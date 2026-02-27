<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'division_id', 'position', 'phone', 'is_active', 'approval_status', 'created_by_user_id', 'approved_at', 'approved_by', 'rejection_reason'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // ── Full Access Roles ───────────────────────────────────
    const ROLE_MINISTER = 'minister';
    const ROLE_ADMIN_ASSISTANT = 'admin_assistant';
    const ROLE_TECH_ASSISTANT = 'tech_assistant';

    // ── Division Director ──────────────────────────────────
    const ROLE_DIRECTOR = 'director';

    // ── Limited Division Access ────────────────────────────
    const ROLE_SUPERVISOR = 'supervisor';
    const ROLE_COORDINATOR = 'coordinator';
    const ROLE_COUNSELOR = 'counselor';

    // ── Personal Access Only ───────────────────────────────
    const ROLE_RECORD_CLERK = 'record_clerk';
    const ROLE_SECRETARY = 'secretary';

    const ROLES = [
        self::ROLE_MINISTER => 'Minister',
        self::ROLE_ADMIN_ASSISTANT => 'Administrative Assistant',
        self::ROLE_TECH_ASSISTANT => 'Technical Assistant',
        self::ROLE_DIRECTOR => 'Director',
        self::ROLE_SUPERVISOR => 'Supervisor',
        self::ROLE_COORDINATOR => 'Coordinator',
        self::ROLE_COUNSELOR => 'Counselor',
        self::ROLE_RECORD_CLERK => 'Record Clerk',
        self::ROLE_SECRETARY => 'Secretary',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'approved_at' => 'datetime',
        ];
    }

    // ── Approval Constants ──────────────────────────────────
    const APPROVAL_APPROVED = 'approved';
    const APPROVAL_PENDING  = 'pending';
    const APPROVAL_REJECTED = 'rejected';

    // ── Relationships ──────────────────────────────────────

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, 'assigned_to');
    }

    public function createdActivities(): HasMany
    {
        return $this->hasMany(Activity::class, 'created_by');
    }

    public function weeklyUpdates(): HasMany
    {
        return $this->hasMany(WeeklyUpdate::class, 'submitted_by');
    }

    public function weeklyPlans(): HasMany
    {
        return $this->hasMany(WeeklyPlan::class, 'submitted_by');
    }

    public function bureauNotifications(): HasMany
    {
        return $this->hasMany(BureauNotification::class);
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ── Approval Helpers ───────────────────────────────────

    public function isPending(): bool
    {
        return $this->approval_status === self::APPROVAL_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->approval_status === self::APPROVAL_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->approval_status === self::APPROVAL_REJECTED;
    }

    public function scopePendingApproval($query)
    {
        return $query->where('approval_status', self::APPROVAL_PENDING);
    }

    // ── Role Helpers ───────────────────────────────────────

    public function isMinister(): bool
    {
        return $this->role === self::ROLE_MINISTER;
    }

    public function isAdminAssistant(): bool
    {
        return $this->role === self::ROLE_ADMIN_ASSISTANT;
    }

    public function isTechAssistant(): bool
    {
        return $this->role === self::ROLE_TECH_ASSISTANT;
    }

    public function isDirector(): bool
    {
        return $this->role === self::ROLE_DIRECTOR;
    }

    public function isSupervisor(): bool
    {
        return $this->role === self::ROLE_SUPERVISOR;
    }

    public function isCoordinator(): bool
    {
        return $this->role === self::ROLE_COORDINATOR;
    }

    public function isCounselor(): bool
    {
        return $this->role === self::ROLE_COUNSELOR;
    }

    public function isRecordClerk(): bool
    {
        return $this->role === self::ROLE_RECORD_CLERK;
    }

    public function isSecretary(): bool
    {
        return $this->role === self::ROLE_SECRETARY;
    }

    /**
     * Full access: Minister, Admin Assistant, Technical Assistant
     */
    public function hasFullAccess(): bool
    {
        return in_array($this->role, [
            self::ROLE_MINISTER,
            self::ROLE_ADMIN_ASSISTANT,
            self::ROLE_TECH_ASSISTANT,
        ]);
    }

    /**
     * Admin panel access: Admin Assistant, Technical Assistant
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, [
            self::ROLE_ADMIN_ASSISTANT,
            self::ROLE_TECH_ASSISTANT,
        ]);
    }

    /**
     * Can manage division content (create/edit activities, submit updates/plans)
     */
    public function canManageDivision(): bool
    {
        return in_array($this->role, [
            self::ROLE_MINISTER,
            self::ROLE_ADMIN_ASSISTANT,
            self::ROLE_TECH_ASSISTANT,
            self::ROLE_DIRECTOR,
        ]);
    }

    /**
     * Can view division activities (read-only for limited roles)
     */
    public function hasDivisionAccess(): bool
    {
        return in_array($this->role, [
            self::ROLE_MINISTER,
            self::ROLE_ADMIN_ASSISTANT,
            self::ROLE_TECH_ASSISTANT,
            self::ROLE_DIRECTOR,
            self::ROLE_SUPERVISOR,
            self::ROLE_COORDINATOR,
            self::ROLE_COUNSELOR,
        ]);
    }

    /**
     * Personal access only (Record Clerk, Secretary)
     */
    public function hasPersonalAccessOnly(): bool
    {
        return in_array($this->role, [
            self::ROLE_RECORD_CLERK,
            self::ROLE_SECRETARY,
        ]);
    }

    /**
     * Can review/approve weekly updates and plans
     */
    public function canReview(): bool
    {
        return $this->hasFullAccess();
    }

    /**
     * Is scoped to a specific division
     */
    public function isDivisionScoped(): bool
    {
        return in_array($this->role, [
            self::ROLE_DIRECTOR,
            self::ROLE_SUPERVISOR,
            self::ROLE_COORDINATOR,
            self::ROLE_COUNSELOR,
            self::ROLE_RECORD_CLERK,
            self::ROLE_SECRETARY,
        ]);
    }

    /**
     * Can create staff in their division (Directors only)
     */
    public function canCreateStaff(): bool
    {
        return $this->role === self::ROLE_DIRECTOR;
    }

    /**
     * Roles a director is allowed to assign to staff they create.
     * Counselor role is only available to the CGPC (Counseling) division.
     */
    public static function directorAssignableRoles(?int $divisionId = null): array
    {
        $roles = [
            self::ROLE_SUPERVISOR   => 'Supervisor',
            self::ROLE_COORDINATOR  => 'Coordinator',
            self::ROLE_RECORD_CLERK => 'Record Clerk',
            self::ROLE_SECRETARY    => 'Secretary',
        ];

        // Only the Counseling division (CGPC) can assign Counselor role
        if ($divisionId !== null) {
            $division = \App\Models\Division::find($divisionId);
            if ($division && $division->code === 'CGPC') {
                $roles[self::ROLE_COUNSELOR] = 'Counselor';
            }
        }

        return $roles;
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_string($roles)) {
            return $this->role === $roles;
        }
        return in_array($this->role, $roles);
    }

    public function getRoleLabelAttribute(): string
    {
        return self::ROLES[$this->role] ?? ucfirst($this->role);
    }

    public function unreadNotificationCount(): int
    {
        return $this->bureauNotifications()->unread()->where('type', '!=', 'message')->count();
    }
}
