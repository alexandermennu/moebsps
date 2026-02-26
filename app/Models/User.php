<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'division_id', 'position', 'phone', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    const ROLE_DIRECTOR = 'director';
    const ROLE_BUREAU_HEAD = 'bureau_head';
    const ROLE_MINISTER = 'minister';
    const ROLE_ADMIN = 'admin';

    const ROLES = [
        self::ROLE_DIRECTOR => 'Division Director',
        self::ROLE_BUREAU_HEAD => 'Bureau Head',
        self::ROLE_MINISTER => 'Minister',
        self::ROLE_ADMIN => 'Admin',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

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

    // ── Role Helpers ───────────────────────────────────────

    public function isDirector(): bool
    {
        return $this->role === self::ROLE_DIRECTOR;
    }

    public function isBureauHead(): bool
    {
        return $this->role === self::ROLE_BUREAU_HEAD;
    }

    public function isMinister(): bool
    {
        return $this->role === self::ROLE_MINISTER;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
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
        return $this->bureauNotifications()->unread()->count();
    }
}
