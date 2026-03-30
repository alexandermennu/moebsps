<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds granular module access controls to users table.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Module access columns - null means use role defaults
            $table->boolean('access_assignments')->nullable()->after('sir_access');
            $table->boolean('access_weekly_updates')->nullable()->after('access_assignments');
            $table->boolean('access_weekly_plans')->nullable()->after('access_weekly_updates');
            $table->boolean('access_activity_tracker')->nullable()->after('access_weekly_plans');
            $table->boolean('access_messages')->nullable()->after('access_activity_tracker');
            $table->boolean('access_my_staff')->nullable()->after('access_messages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'access_assignments',
                'access_weekly_updates',
                'access_weekly_plans',
                'access_activity_tracker',
                'access_messages',
                'access_my_staff',
            ]);
        });
    }
};
