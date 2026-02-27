<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'case_number', 'status', 'priority', 'category',
    'title', 'description',
    'victim_name', 'victim_age', 'victim_gender', 'victim_grade',
    'victim_school', 'victim_contact', 'victim_parent_guardian', 'victim_parent_contact',
    'perpetrator_name', 'perpetrator_type', 'perpetrator_description',
    'incident_date', 'incident_location', 'incident_description', 'witnesses', 'is_recurring',
    'reported_by', 'assigned_to', 'division_id', 'is_confidential',
    'resolution', 'resolution_date', 'referral_agency', 'referral_details',
    'follow_up_required', 'follow_up_date',
    'risk_level', 'immediate_action_required', 'safety_plan',
])]
class SrgbvCase extends Model
{
    use HasFactory;

    protected $table = 'srgbv_cases';

    // ── Status Constants ────────────────────────────────────
    const STATUS_REPORTED = 'reported';
    const STATUS_UNDER_INVESTIGATION = 'under_investigation';
    const STATUS_ACTION_TAKEN = 'action_taken';
    const STATUS_REFERRED = 'referred';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_CLOSED = 'closed';

    const STATUSES = [
        self::STATUS_REPORTED => 'Reported',
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

    // ── Category Constants ──────────────────────────────────
    const CATEGORIES = [
        'physical_violence' => 'Physical Violence',
        'sexual_violence' => 'Sexual Violence',
        'psychological_violence' => 'Psychological Violence',
        'bullying' => 'Bullying',
        'harassment' => 'Harassment',
        'exploitation' => 'Exploitation',
        'neglect' => 'Neglect',
        'other' => 'Other',
    ];

    // ── Perpetrator Types ───────────────────────────────────
    const PERPETRATOR_TYPES = [
        'student' => 'Student',
        'teacher' => 'Teacher',
        'staff' => 'School Staff',
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

    // ── File Categories ─────────────────────────────────────
    const FILE_CATEGORIES = [
        'evidence' => 'Evidence',
        'photo' => 'Photo',
        'document' => 'Document',
        'medical_report' => 'Medical Report',
        'police_report' => 'Police Report',
        'consent_form' => 'Consent Form',
        'other' => 'Other',
    ];

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
        return $this->hasMany(SrgbvCaseNote::class)->latest();
    }

    public function files(): HasMany
    {
        return $this->hasMany(SrgbvCaseFile::class)->latest();
    }

    // ── Auto-Generate Case Number ───────────────────────────

    public static function generateCaseNumber(): string
    {
        $year = now()->format('Y');
        $lastCase = static::whereYear('created_at', $year)
            ->orderByDesc('id')
            ->first();

        if ($lastCase && preg_match('/SRGBV-' . $year . '-(\d+)/', $lastCase->case_number, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        return 'SRGBV-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    // ── Scopes ──────────────────────────────────────────────

    public function scopeByDivision($query, $divisionId)
    {
        return $query->where('division_id', $divisionId);
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

    public function scopeRequiringFollowUp($query)
    {
        return $query->where('follow_up_required', true)
            ->where(function ($q) {
                $q->whereNull('follow_up_date')
                  ->orWhere('follow_up_date', '<=', now());
            })
            ->whereNotIn('status', [self::STATUS_RESOLVED, self::STATUS_CLOSED]);
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
        return self::CATEGORIES[$this->category] ?? ucfirst($this->category);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_REPORTED => 'red',
            self::STATUS_UNDER_INVESTIGATION => 'amber',
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

    public function getDaysSinceReportedAttribute(): int
    {
        return $this->created_at->diffInDays(now());
    }
}
