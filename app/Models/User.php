<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

#[Fillable(['name', 'email', 'password', 'role', 'division_id', 'position', 'phone', 'profile_photo', 'is_active', 'approval_status', 'created_by_user_id', 'approved_at', 'approved_by', 'rejection_reason', 'address', 'city', 'date_of_birth', 'gender', 'nationality', 'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relationship', 'counselor_school', 'counselor_county', 'counselor_status', 'counselor_qualification', 'counselor_specialization', 'counselor_years_experience', 'counselor_training', 'counselor_school_phone', 'counselor_appointed_at', 'counselor_assignment_date', 'counselor_school_district', 'counselor_school_level', 'counselor_school_type', 'counselor_school_population', 'counselor_num_boys', 'counselor_num_girls', 'counselor_school_address', 'counselor_school_principal'])]
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
            'date_of_birth' => 'date',
            'counselor_appointed_at' => 'date',
            'counselor_assignment_date' => 'date',
        ];
    }

    // ── Approval Constants ──────────────────────────────────
    const APPROVAL_APPROVED = 'approved';
    const APPROVAL_PENDING  = 'pending';
    const APPROVAL_REJECTED = 'rejected';

    // ── Counselor Status Constants ──────────────────────────
    const COUNSELOR_STATUSES = [
        'active' => 'Active',
        'abandoned_resigned' => 'Abandoned/Resigned',
        'transferred' => 'Transferred',
        'on_study_leave' => 'On Study Leave',
        'on_sick_leave' => 'On Sick Leave',
        'returned_from_study' => 'Returned from Study',
    ];

    // ── County Constants ────────────────────────────────────
    const COUNTIES = [
        'Bomi County',
        'Bong County',
        'Gbarpolu County',
        'Grand Bassa County',
        'Grand Cape Mount County',
        'Grand Gedeh County',
        'Grand Kru County',
        'Lofa County',
        'Margibi County',
        'Maryland County',
        'Montserrado County',
        'Nimba County',
        'River Cess County',
        'River Gee County',
        'Sinoe County',
    ];

    // ── Counselor Qualification Constants ───────────────────
    const COUNSELOR_QUALIFICATIONS = [
        'certificate'   => 'Certificate',
        'diploma'       => 'Diploma',
        'associate'     => 'Associate Degree',
        'bachelors'     => 'Bachelor\'s Degree',
        'masters'       => 'Master\'s Degree',
        'doctorate'     => 'Doctorate',
        'other'         => 'Other',
    ];

    // ── Counselor Specialization Constants ──────────────────
    const COUNSELOR_SPECIALIZATIONS = [
        'school_counseling'      => 'School Counseling',
        'mental_health'          => 'Mental Health',
        'trauma_support'         => 'Trauma Support',
        'child_protection'       => 'Child Protection',
        'gender_based_violence'  => 'Gender-Based Violence',
        'substance_abuse'        => 'Substance Abuse',
        'career_guidance'        => 'Career Guidance',
        'psychosocial_support'   => 'Psychosocial Support',
        'special_needs'          => 'Special Needs Education',
        'other'                  => 'Other',
    ];

    // ── Gender Constants ────────────────────────────────────
    const GENDERS = [
        'male'   => 'Male',
        'female' => 'Female',
    ];

    // ── School Level Constants ──────────────────────────────
    const SCHOOL_LEVELS = [
        'early_childhood' => 'Early Childhood (ECE)',
        'primary'         => 'Primary',
        'junior_high'     => 'Junior High',
        'senior_high'     => 'Senior High',
        'combined'        => 'Combined (Multi-Level)',
        'technical'       => 'Technical / Vocational',
        'other'           => 'Other',
    ];

    // ── School Type Constants ───────────────────────────────
    const SCHOOL_TYPES = [
        'public'           => 'Public',
        'private'          => 'Private',
        'community'        => 'Community',
        'mission'          => 'Mission / Faith-Based',
        'government_aided' => 'Government-Aided',
        'other'            => 'Other',
    ];

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

    public function counselorDocuments(): HasMany
    {
        return $this->hasMany(CounselorDocument::class);
    }

    public function counselorEducation(): HasMany
    {
        return $this->hasMany(CounselorEducation::class);
    }

    public function counselorCertificates(): HasMany
    {
        return $this->hasMany(CounselorCertificate::class);
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

    // ── Profile Photo Helpers ──────────────────────────────

    /**
     * Get the URL for the user's profile photo.
     */
    public function getProfilePhotoUrlAttribute(): ?string
    {
        if (!$this->profile_photo) {
            return null;
        }

        $disk = config('filesystems.uploads', 'public');
        $storage = Storage::disk($disk);

        // Use signed temporary URLs for S3 (private buckets)
        if (config("filesystems.disks.{$disk}.driver") === 's3') {
            return $storage->temporaryUrl($this->profile_photo, now()->addHour());
        }

        return $storage->url($this->profile_photo);
    }

    /**
     * Get the user's initials (first letter of first and last name).
     */
    public function getInitialsAttribute(): string
    {
        $parts = explode(' ', trim($this->name));
        if (count($parts) >= 2) {
            return strtoupper(substr($parts[0], 0, 1) . substr(end($parts), 0, 1));
        }
        return strtoupper(substr($this->name, 0, 1));
    }

    /**
     * Check if user has a profile photo.
     */
    public function hasProfilePhoto(): bool
    {
        return !empty($this->profile_photo);
    }

    /**
     * Delete the user's profile photo from storage.
     */
    public function deleteProfilePhoto(): void
    {
        if ($this->profile_photo) {
            Storage::disk(config('filesystems.uploads', 'public'))->delete($this->profile_photo);
            $this->update(['profile_photo' => null]);
        }
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
     * Can review/approve weekly updates and plans (legacy - includes minister)
     */
    public function canReview(): bool
    {
        return $this->hasFullAccess();
    }

    /**
     * Can review/approve submitted plans & updates (Admin Asst & Tech Asst only).
     * Minister only sees approved submissions on their dashboard.
     */
    public function canReviewSubmissions(): bool
    {
        return in_array($this->role, [
            self::ROLE_ADMIN_ASSISTANT,
            self::ROLE_TECH_ASSISTANT,
        ]);
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
     * CGPC (Counseling) division can assign Supervisors, Coordinators, and Counselors.
     * Other divisions can only create Supervisors and Coordinators.
     * Record Clerk and Secretary are only assignable by full-access users (admin forms).
     */
    public static function directorAssignableRoles(?int $divisionId = null): array
    {
        $roles = [
            self::ROLE_SUPERVISOR   => 'Supervisor',
            self::ROLE_COORDINATOR  => 'Coordinator',
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

    public function getCounselorStatusLabelAttribute(): string
    {
        return self::COUNSELOR_STATUSES[$this->counselor_status] ?? ucfirst($this->counselor_status ?? 'Active');
    }

    public function getCounselorQualificationLabelAttribute(): string
    {
        return self::COUNSELOR_QUALIFICATIONS[$this->counselor_qualification] ?? ucfirst($this->counselor_qualification ?? '—');
    }

    public function getCounselorSpecializationLabelAttribute(): string
    {
        return self::COUNSELOR_SPECIALIZATIONS[$this->counselor_specialization] ?? ucfirst($this->counselor_specialization ?? '—');
    }

    public function unreadNotificationCount(): int
    {
        return $this->bureauNotifications()->unread()->where('type', '!=', 'message')->count();
    }
}
