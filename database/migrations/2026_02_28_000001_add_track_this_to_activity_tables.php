<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('update_activities', function (Blueprint $table) {
            $table->boolean('track_this')->default(false)->after('challenges');
        });

        Schema::table('plan_activities', function (Blueprint $table) {
            $table->boolean('track_this')->default(false)->after('status_comment');
        });

        Schema::table('tracked_activities', function (Blueprint $table) {
            $table->foreignId('latest_plan_activity_id')->nullable()->after('latest_weekly_update_id')
                ->constrained('plan_activities')->nullOnDelete();
            $table->foreignId('latest_weekly_plan_id')->nullable()->after('latest_plan_activity_id')
                ->constrained('weekly_plans')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('update_activities', function (Blueprint $table) {
            $table->dropColumn('track_this');
        });

        Schema::table('plan_activities', function (Blueprint $table) {
            $table->dropColumn('track_this');
        });

        Schema::table('tracked_activities', function (Blueprint $table) {
            $table->dropForeign(['latest_plan_activity_id']);
            $table->dropForeign(['latest_weekly_plan_id']);
            $table->dropColumn(['latest_plan_activity_id', 'latest_weekly_plan_id']);
        });
    }
};
