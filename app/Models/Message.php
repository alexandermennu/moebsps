<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    #[\Illuminate\Database\Eloquent\Attributes\Fillable([
        'sender_id',
        'receiver_id',
        'subject',
        'body',
        'parent_id',
        'is_read',
        'read_at',
        'sender_deleted',
        'receiver_deleted',
    ])]
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'subject',
        'body',
        'parent_id',
        'is_read',
        'read_at',
        'sender_deleted',
        'receiver_deleted',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'read_at' => 'datetime',
            'sender_deleted' => 'boolean',
            'receiver_deleted' => 'boolean',
        ];
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Message::class, 'parent_id')->orderBy('created_at', 'asc');
    }

    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    public function scopeInbox($query, int $userId)
    {
        return $query->where('receiver_id', $userId)
                     ->where('receiver_deleted', false)
                     ->whereNull('parent_id');
    }

    public function scopeSent($query, int $userId)
    {
        return $query->where('sender_id', $userId)
                     ->where('sender_deleted', false)
                     ->whereNull('parent_id');
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}
