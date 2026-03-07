<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'incident_number', 'type', 'category', 'source', 'status', 'priority',
    'title', 'description',
    'incident_date', 'incident_location', 'incident_description', 'witnesses', 'is_recurring',
    'school_name', 'school_county', 'school_district', 'school_level',
    'victim_name', 'victim_age', 'victim_gender', 'victim_grade',
    'victim_contact', 'victim_parent_guardian', 'victim_parent_contact',
    'perpetrator_name', 'perpetrator_type', 'perpetrator_description',
    'public_reporter_name', 'public_reporter_phone', 'public_reporter_email', 'public_reporter_relationship',
    'public_reporter_verified',
    'tracking_code',
    'reported_by', 'assigned_to', 'division_id', 'is_confidential',
    'resolution', 'resolution_date', 'referral_agency', 'referral_details',
    'follow_up_required', 'follow_up_date',
    'risk_level', 'immediate_action_required', 'safety_plan',
    'legacy_srgbv_id',
])]
class Incident extends Model
{
    use HasFactory;

    protected $table = 'incidents';

    // ── Incident Types ──────────────────────────────────────
    const TYPE_SRGBV = 'srgbv';
    const TYPE_OTHER_INCIDENT = 'other_incident';

    // Legacy types (kept for backward compatibility)
    const TYPE_DISCIPLINARY = 'disciplinary';
    const TYPE_SAFETY = 'safety';
    const TYPE_INFRASTRUCTURE = 'infrastructure';
    const TYPE_ACADEMIC = 'academic';
    const TYPE_HEALTH = 'health';
    const TYPE_OTHER = 'other';

    // Main types shown on forms
    const TYPES = [
        self::TYPE_SRGBV => 'SRGBV (School-Related Gender-Based Violence)',
        self::TYPE_OTHER_INCIDENT => 'Other Incidents',
    ];

    // All types including legacy (for validation)
    const ALL_TYPES = [
        self::TYPE_SRGBV => 'SRGBV (School-Related Gender-Based Violence)',
        self::TYPE_OTHER_INCIDENT => 'Other Incidents',
        self::TYPE_DISCIPLINARY => 'Disciplinary',
        self::TYPE_SAFETY => 'Safety & Security',
        self::TYPE_INFRASTRUCTURE => 'Infrastructure',
        self::TYPE_ACADEMIC => 'Academic Misconduct',
        self::TYPE_HEALTH => 'Health & Welfare',
        self::TYPE_OTHER => 'Other',
    ];

    // ── Type-Specific Categories ────────────────────────────
    const CATEGORIES_BY_TYPE = [
        self::TYPE_SRGBV => [
            'sexual_harassment' => 'Sexual harassment',
            'sexual_assault' => 'Sexual assault or rape',
            'sexual_exploitation' => 'Sexual exploitation (sex for grades or favor)',
            'physical_violence' => 'Physical violence (Beating or corporal punishment causing harm)',
            'unwanted_touching' => 'Unwanted sexual touching',
            'sexual_intimidation' => 'Sexual intimidation or coercion',
            'exposure_sexual_content' => 'Exposure to sexual content or pornography',
            'verbal_abuse' => 'Verbal abuse',
            'threats_intimidation' => 'Threats or intimidation',
        ],
        self::TYPE_OTHER_INCIDENT => [
            'student_misconduct' => 'Student Misconduct',
            'teacher_misconduct' => 'Teacher/Staff Misconduct',
            'substance_abuse' => 'Substance Abuse',
            'fighting' => 'Fighting / Violence',
            'vandalism' => 'Vandalism / Property Damage',
            'theft' => 'Theft',
            'fire' => 'Fire Incident',
            'structural_hazard' => 'Structural Hazard',
            'sanitation' => 'Sanitation / Health Issue',
            'accident_injury' => 'Accident / Injury',
            'bullying' => 'Bullying / Harassment (Non-Sexual)',
            'truancy' => 'Truancy / Attendance Issues',
            'other' => 'Other',
        ],
        // Legacy categories kept for backward compatibility
        self::TYPE_DISCIPLINARY => [
            'student_misconduct' => 'Student Misconduct',
            'teacher_misconduct' => 'Teacher Misconduct',
            'staff_misconduct' => 'Staff Misconduct',
            'substance_abuse' => 'Substance Abuse',
            'vandalism' => 'Vandalism',
            'fighting' => 'Fighting',
            'other' => 'Other',
        ],
        self::TYPE_SAFETY => [
            'fire' => 'Fire',
            'flood' => 'Flood/Water Damage',
            'structural' => 'Structural Hazard',
            'trespassing' => 'Trespassing',
            'theft' => 'Theft',
            'weapon' => 'Weapon on Campus',
            'other' => 'Other',
        ],
        self::TYPE_INFRASTRUCTURE => [
            'building_damage' => 'Building Damage',
            'sanitation' => 'Sanitation Issue',
            'water_supply' => 'Water Supply',
            'electricity' => 'Electricity Issue',
            'furniture' => 'Furniture/Equipment',
            'road_access' => 'Road/Access Issue',
            'other' => 'Other',
        ],
        self::TYPE_ACADEMIC => [
            'cheating' => 'Cheating/Exam Fraud',
            'grade_manipulation' => 'Grade Manipulation',
            'attendance_fraud' => 'Attendance Fraud',
            'credential_fraud' => 'Credential Fraud',
            'other' => 'Other',
        ],
        self::TYPE_HEALTH => [
            'disease_outbreak' => 'Disease Outbreak',
            'food_safety' => 'Food Safety',
            'injury' => 'Injury',
            'mental_health' => 'Mental Health Crisis',
            'disability_access' => 'Disability Access Issue',
            'other' => 'Other',
        ],
        self::TYPE_OTHER => [
            'general' => 'General Complaint',
            'policy_violation' => 'Policy Violation',
            'community_concern' => 'Community Concern',
            'other' => 'Other',
        ],
    ];

    // ── All Categories (flat, for validation) ───────────────
    public static function allCategories(): array
    {
        $all = [];
        foreach (self::CATEGORIES_BY_TYPE as $categories) {
            $all = array_merge($all, $categories);
        }
        return array_unique($all);
    }

    // ── Source Constants ─────────────────────────────────────
    const SOURCE_INTERNAL = 'internal';
    const SOURCE_PUBLIC = 'public';

    const SOURCES = [
        self::SOURCE_INTERNAL => 'Internal (Ministry)',
        self::SOURCE_PUBLIC => 'Public Report',
    ];

    // ── Status Constants ────────────────────────────────────
    const STATUS_REPORTED = 'reported';
    const STATUS_UNDER_REVIEW = 'under_review';
    const STATUS_UNDER_INVESTIGATION = 'under_investigation';
    const STATUS_ACTION_TAKEN = 'action_taken';
    const STATUS_REFERRED = 'referred';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_CLOSED = 'closed';

    const STATUSES = [
        self::STATUS_REPORTED => 'Reported',
        self::STATUS_UNDER_REVIEW => 'Under Review',
        self::STATUS_UNDER_INVESTIGATION => 'Under Investigation',
        self::STATUS_ACTION_TAKEN => 'Action Taken',
        self::STATUS_REFERRED => 'Referred',
        self::STATUS_RESOLVED => 'Resolved',
        self::STATUS_CLOSED => 'Closed',
    ];

    // ── Priority Constants ──────────────────────────────────
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_CRITICAL = 'critical';

    const PRIORITIES = [
        self::PRIORITY_LOW => 'Low',
        self::PRIORITY_MEDIUM => 'Medium',
        self::PRIORITY_HIGH => 'High',
        self::PRIORITY_CRITICAL => 'Critical',
    ];

    // ── Perpetrator Types ───────────────────────────────────
    const PERPETRATOR_TYPES = [
        'student' => 'Student',
        'teacher' => 'Teacher',
        'staff' => 'School Staff',
        'parent' => 'Parent/Guardian',
        'community_member' => 'Community Member',
        'unknown' => 'Unknown',
        'other' => 'Other',
    ];

    // ── Risk Levels ─────────────────────────────────────────
    const RISK_LEVELS = [
        'low' => 'Low',
        'moderate' => 'Moderate',
        'high' => 'High',
        'immediate_danger' => 'Immediate Danger',
    ];

    // ── Public Reporter Relationships ───────────────────────
    const REPORTER_RELATIONSHIPS = [
        'parent' => 'Parent/Guardian',
        'student' => 'Student',
        'teacher' => 'Teacher',
        'community_member' => 'Community Member',
        'ngo_worker' => 'NGO Worker',
        'other' => 'Other',
    ];

    // ── School Levels ───────────────────────────────────────
    const SCHOOL_LEVELS = [
        'primary' => 'Primary',
        'junior_high' => 'Junior High',
        'senior_high' => 'Senior High',
        'vocational' => 'Vocational/Technical',
        'university' => 'University',
        'other' => 'Other',
    ];

    // ── File Categories ─────────────────────────────────────
    const FILE_CATEGORIES = [
        'evidence' => 'Evidence',
        'photo' => 'Photo',
        'document' => 'Document',
        'medical_report' => 'Medical Report',
        'police_report' => 'Police Report',
        'consent_form' => 'Consent Form',
        'incident_report' => 'Incident Report',
        'other' => 'Other',
    ];

    // ── Casts ───────────────────────────────────────────────
    protected function casts(): array
    {
        return [
            'incident_date' => 'date',
            'resolution_date' => 'date',
            'follow_up_date' => 'date',
            'is_confidential' => 'boolean',
            'is_recurring' => 'boolean',
            'follow_up_required' => 'boolean',
            'immediate_action_required' => 'boolean',
        ];
    }

    // ── Relationships ───────────────────────────────────────

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(IncidentNote::class)->latest();
    }

    public function files(): HasMany
    {
        return $this->hasMany(IncidentFile::class)->latest();
    }

    // ── Number Generators ───────────────────────────────────

    public static function generateIncidentNumber(string $type, string $source = 'internal'): string
    {
        $year = now()->format('Y');
        
        // Type prefix: SIR-SRGBV or SIR-OI (Other Incidents)
        $typePrefix = $type === self::TYPE_SRGBV ? 'SIR-SRGBV' : 'SIR-OI';
        
        // Source suffix for public reports
        $sourceSuffix = $source === 'public' ? '-PUB' : '';
        
        $prefix = "{$typePrefix}-{$year}{$sourceSuffix}";

        $lastIncident = static::where('incident_number', 'like', "{$prefix}-%")
            ->orderByDesc('id')
            ->first();

        if ($lastIncident && preg_match('/-(\d+)$/', $lastIncident->incident_number, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public static function generateTrackingCode(string $type): string
    {
        // Type prefix: SIR-SRGBV or SIR-OI
        $typePrefix = $type === self::TYPE_SRGBV ? 'SIR-SRGBV' : 'SIR-OI';
        
        do {
            $code = $typePrefix . '-' . strtoupper(Str::random(3)) . '-' . rand(1000, 9999);
        } while (static::where('tracking_code', $code)->exists());

        return $code;
    }

    // ── Scopes ──────────────────────────────────────────────

    public function scopeByDivision($query, $divisionId)
    {
        return $query->where('division_id', $divisionId);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeBySource($query, string $source)
    {
        return $query->where('source', $source);
    }

    public function scopeInternal($query)
    {
        return $query->where('source', self::SOURCE_INTERNAL);
    }

    public function scopePublicReports($query)
    {
        return $query->where('source', self::SOURCE_PUBLIC);
    }

    public function scopeOpen($query)
    {
        return $query->whereNotIn('status', [self::STATUS_RESOLVED, self::STATUS_CLOSED]);
    }

    public function scopeClosed($query)
    {
        return $query->whereIn('status', [self::STATUS_RESOLVED, self::STATUS_CLOSED]);
    }

    public function scopeCritical($query)
    {
        return $query->where('priority', self::PRIORITY_CRITICAL);
    }

    public function scopeSrgbv($query)
    {
        return $query->where('type', self::TYPE_SRGBV);
    }

    public function scopeRequiringFollowUp($query)
    {
        return $query->where('follow_up_required', true)
            ->where(function ($q) {
                $q->whereNull('follow_up_date')
                  ->orWhere('follow_up_date', '<=', now());
            })
            ->whereNotIn('status', [self::STATUS_RESOLVED, self::STATUS_CLOSED]);
    }

    public function scopeRequiringImmediateAction($query)
    {
        return $query->where('immediate_action_required', true)
            ->open();
    }

    // ── Helpers ─────────────────────────────────────────────

    public function isOpen(): bool
    {
        return !in_array($this->status, [self::STATUS_RESOLVED, self::STATUS_CLOSED]);
    }

    public function isCritical(): bool
    {
        return $this->priority === self::PRIORITY_CRITICAL;
    }

    public function isSrgbv(): bool
    {
        return $this->type === self::TYPE_SRGBV;
    }

    public function isPublicReport(): bool
    {
        return $this->source === self::SOURCE_PUBLIC;
    }

    public function isInternalReport(): bool
    {
        return $this->source === self::SOURCE_INTERNAL;
    }

    // ── Accessors ───────────────────────────────────────────

    public function getTypeLabelAttribute(): string
    {
        return self::ALL_TYPES[$this->type] ?? self::TYPES[$this->type] ?? ucfirst($this->type);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function getPriorityLabelAttribute(): string
    {
        return self::PRIORITIES[$this->priority] ?? ucfirst($this->priority);
    }

    public function getCategoryLabelAttribute(): string
    {
        $categories = self::CATEGORIES_BY_TYPE[$this->type] ?? [];
        return $categories[$this->category] ?? ucfirst(str_replace('_', ' ', $this->category));
    }

    public function getSourceLabelAttribute(): string
    {
        return self::SOURCES[$this->source] ?? ucfirst($this->source);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_REPORTED => 'red',
            self::STATUS_UNDER_REVIEW => 'amber',
            self::STATUS_UNDER_INVESTIGATION => 'orange',
            self::STATUS_ACTION_TAKEN => 'blue',
            self::STATUS_REFERRED => 'purple',
            self::STATUS_RESOLVED => 'green',
            self::STATUS_CLOSED => 'gray',
            default => 'gray',
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            self::PRIORITY_LOW => 'gray',
            self::PRIORITY_MEDIUM => 'blue',
            self::PRIORITY_HIGH => 'amber',
            self::PRIORITY_CRITICAL => 'red',
            default => 'gray',
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            self::TYPE_SRGBV => 'red',
            self::TYPE_DISCIPLINARY => 'amber',
            self::TYPE_SAFETY => 'orange',
            self::TYPE_INFRASTRUCTURE => 'blue',
            self::TYPE_ACADEMIC => 'purple',
            self::TYPE_HEALTH => 'teal',
            self::TYPE_OTHER => 'gray',
            default => 'gray',
        };
    }

    public function getSourceColorAttribute(): string
    {
        return match($this->source) {
            self::SOURCE_INTERNAL => 'blue',
            self::SOURCE_PUBLIC => 'green',
            default => 'gray',
        };
    }

    public function getDaysSinceReportedAttribute(): int
    {
        return $this->created_at ? $this->created_at->diffInDays(now()) : 0;
    }

    public function getRiskLevelLabelAttribute(): string
    {
        return self::RISK_LEVELS[$this->risk_level] ?? ucfirst($this->risk_level ?? 'N/A');
    }

    public function getRiskLevelColorAttribute(): string
    {
        return match($this->risk_level) {
            'low' => 'green',
            'moderate' => 'amber',
            'high' => 'orange',
            'immediate_danger' => 'red',
            default => 'gray',
        };
    }
}
