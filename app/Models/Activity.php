<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'title', 'description', 'division_id', 'assigned_to', 'created_by',
    'status', 'priority', 'start_date', 'due_date', 'completed_date',
    'progress_percentage', 'is_overdue', 'is_escalated', 'escalated_to',
    'escalated_at', 'is_repeated', 'remarks'
])]
class Activity extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'due_date' => 'date',
            'completed_date' => 'date',
            'escalated_at' => 'datetime',
            'is_overdue' => 'boolean',
            'is_escalated' => 'boolean',
            'is_repeated' => 'boolean',
        ];
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ActivityComment::class);
    }

    public function isOverdue(): bool
    {
        return $this->status !== 'completed' && $this->due_date->isPast();
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'completed')
                     ->where('due_date', '<', now())
                     ->where('is_overdue', true);
    }

    public function scopeByDivision($query, $divisionId)
    {
        return $query->where('division_id', $divisionId);
    }

    public function scopeEscalated($query)
    {
        return $query->where('is_escalated', true);
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'not_started' => 'gray',
            'in_progress' => 'blue',
            'completed' => 'green',
            'overdue' => 'red',
            default => 'gray',
        };
    }

    public function getPriorityBadgeColorAttribute(): string
    {
        return match($this->priority) {
            'low' => 'gray',
            'medium' => 'yellow',
            'high' => 'orange',
            'critical' => 'red',
            default => 'gray',
        };
    }
}
