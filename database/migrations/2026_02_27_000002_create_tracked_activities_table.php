<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\SystemSetting;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracked_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_id')->constrained()->cascadeOnDelete();
            $table->string('activity_hash', 32)->index();
            $table->text('activity_text');
            $table->string('current_status')->default('not_started'); // not_started, ongoing, completed, na
            $table->string('responsible_persons')->nullable();
            $table->text('status_comment')->nullable();
            $table->text('challenges')->nullable();
            $table->date('first_reported_at');
            $table->date('last_reported_at');
            $table->unsignedInteger('times_reported')->default(1);
            $table->unsignedInteger('weeks_unchanged')->default(1);
            $table->boolean('is_stale')->default(false);
            $table->boolean('is_repeated')->default(false);
            $table->string('source_type')->default('update'); // 'update' or 'plan'
            $table->foreignId('latest_update_activity_id')->nullable()->constrained('update_activities')->nullOnDelete();
            $table->foreignId('latest_weekly_update_id')->nullable()->constrained('weekly_updates')->nullOnDelete();
            $table->timestamps();

            $table->index(['division_id', 'activity_hash']);
            $table->index('is_stale');
            $table->index('is_repeated');
        });

        // Seed activity tracking settings
        SystemSetting::setValue('stale_activity_weeks', 3, 'integer', 'activity_tracking', 'Weeks an activity can stay in the same non-completed status before being flagged as stale');
        SystemSetting::setValue('repeat_threshold', 2, 'integer', 'activity_tracking', 'Minimum times an activity must appear in submissions to be flagged as repeated');
        SystemSetting::setValue('stale_detection_enabled', true, 'boolean', 'activity_tracking', 'Enable automatic detection of stale activities');
        SystemSetting::setValue('repeat_detection_enabled', true, 'boolean', 'activity_tracking', 'Enable automatic detection of repeated activities');
    }

    public function down(): void
    {
        Schema::dropIfExists('tracked_activities');

        SystemSetting::whereIn('key', [
            'stale_activity_weeks', 'repeat_threshold',
            'stale_detection_enabled', 'repeat_detection_enabled',
        ])->delete();
    }
};
