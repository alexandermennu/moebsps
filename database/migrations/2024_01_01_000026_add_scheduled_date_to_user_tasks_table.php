<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_tasks', function (Blueprint $table) {
            $table->date('scheduled_date')->nullable()->after('due_date');
            $table->boolean('is_weekly_target')->default(false)->after('scheduled_date');
            
            $table->index(['user_id', 'scheduled_date']);
            $table->index(['user_id', 'is_weekly_target']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_tasks', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'scheduled_date']);
            $table->dropIndex(['user_id', 'is_weekly_target']);
            $table->dropColumn(['scheduled_date', 'is_weekly_target']);
        });
    }
};
