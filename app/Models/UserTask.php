<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'due_date',
        'priority',
        'status',
        'related_to',
        'related_id',
        'completed_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    /**
     * Get available "related to" options
     */
    public static function getRelatedToOptions(): array
    {
        return [
            'personal' => 'Personal',
            'weekly_updates' => 'Weekly Updates',
            'weekly_plans' => 'Weekly Plans',
            'srgbv' => 'SRGBV Cases',
            'counselor' => 'Counselor Management',
            'incidents' => 'Incident Reports',
            'activities' => 'Activities',
            'other' => 'Other',
        ];
    }

    /**
     * Get priority options
     */
    public static function getPriorityOptions(): array
    {
        return [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
        ];
    }

    /**
     * Get status options
     */
    public static function getStatusOptions(): array
    {
        return [
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
        ];
    }

    /**
     * Relationship to user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related to label
     */
    public function getRelatedToLabelAttribute(): string
    {
        return self::getRelatedToOptions()[$this->related_to] ?? ucfirst($this->related_to);
    }

    /**
     * Get priority badge color
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'high' => 'red',
            'medium' => 'yellow',
            'low' => 'green',
            default => 'gray',
        };
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'completed' => 'green',
            'in_progress' => 'blue',
            'pending' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Check if task is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        if (!$this->due_date || $this->status === 'completed') {
            return false;
        }
        return $this->due_date->isPast();
    }

    /**
     * Check if task is due today
     */
    public function getIsDueTodayAttribute(): bool
    {
        if (!$this->due_date) {
            return false;
        }
        return $this->due_date->isToday();
    }

    /**
     * Scope for pending tasks
     */
    public function scopePending($query)
    {
        return $query->where('status', '!=', 'completed');
    }

    /**
     * Scope for completed tasks
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for overdue tasks
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'completed')
                     ->whereNotNull('due_date')
                     ->where('due_date', '<', now()->startOfDay());
    }

    /**
     * Scope for tasks due today
     */
    public function scopeDueToday($query)
    {
        return $query->whereDate('due_date', now()->toDateString());
    }

    /**
     * Mark task as completed
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Mark task as pending
     */
    public function markAsPending(): void
    {
        $this->update([
            'status' => 'pending',
            'completed_at' => null,
        ]);
    }
}
