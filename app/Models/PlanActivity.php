<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'weekly_plan_id', 'sort_order', 'activity',
    'responsible_persons', 'status_comment', 'track_this'
])]
class PlanActivity extends Model
{
    protected function casts(): array
    {
        return [
            'track_this' => 'boolean',
        ];
    }

    public function weeklyPlan(): BelongsTo
    {
        return $this->belongsTo(WeeklyPlan::class);
    }
}
