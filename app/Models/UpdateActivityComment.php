<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['update_activity_id', 'user_id', 'body'])]
class UpdateActivityComment extends Model
{
    protected $table = 'update_activity_comments';

    public function activity(): BelongsTo
    {
        return $this->belongsTo(UpdateActivity::class, 'update_activity_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
