<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Safety migration: ensures all counselor/personal detail columns exist on the users table.
 * Uses hasColumn() checks so it's safe to run even if prior migrations partially applied.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Personal details (from 2026_03_02_000002)
            if (!Schema::hasColumn('users', 'address')) {
                $table->string('address')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'gender')) {
                $table->string('gender')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'nationality')) {
                $table->string('nationality')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'emergency_contact_name')) {
                $table->string('emergency_contact_name')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'emergency_contact_phone')) {
                $table->string('emergency_contact_phone')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'emergency_contact_relationship')) {
                $table->string('emergency_contact_relationship')->nullable()->after('phone');
            }

            // School / Assignment details (from 2026_03_02_000002)
            if (!Schema::hasColumn('users', 'counselor_assignment_date')) {
                $table->date('counselor_assignment_date')->nullable();
            }
            if (!Schema::hasColumn('users', 'counselor_school_district')) {
                $table->string('counselor_school_district')->nullable();
            }
            if (!Schema::hasColumn('users', 'counselor_school_level')) {
                $table->string('counselor_school_level')->nullable();
            }
            if (!Schema::hasColumn('users', 'counselor_school_type')) {
                $table->string('counselor_school_type')->nullable();
            }
            if (!Schema::hasColumn('users', 'counselor_school_population')) {
                $table->unsignedInteger('counselor_school_population')->nullable();
            }
            if (!Schema::hasColumn('users', 'counselor_num_boys')) {
                $table->unsignedInteger('counselor_num_boys')->nullable();
            }
            if (!Schema::hasColumn('users', 'counselor_num_girls')) {
                $table->unsignedInteger('counselor_num_girls')->nullable();
            }
            if (!Schema::hasColumn('users', 'counselor_school_address')) {
                $table->text('counselor_school_address')->nullable();
            }
            if (!Schema::hasColumn('users', 'counselor_school_principal')) {
                $table->string('counselor_school_principal')->nullable();
            }

            // Counselor profile fields (from 2026_02_28_000003)
            if (!Schema::hasColumn('users', 'counselor_qualification')) {
                $table->string('counselor_qualification')->nullable();
            }
            if (!Schema::hasColumn('users', 'counselor_specialization')) {
                $table->string('counselor_specialization')->nullable();
            }
            if (!Schema::hasColumn('users', 'counselor_years_experience')) {
                $table->unsignedSmallInteger('counselor_years_experience')->nullable();
            }
            if (!Schema::hasColumn('users', 'counselor_training')) {
                $table->text('counselor_training')->nullable();
            }
            if (!Schema::hasColumn('users', 'counselor_school_phone')) {
                $table->string('counselor_school_phone')->nullable();
            }
            if (!Schema::hasColumn('users', 'counselor_appointed_at')) {
                $table->date('counselor_appointed_at')->nullable();
            }

            // Profile approval fields (from 2026_03_03_000001)
            if (!Schema::hasColumn('users', 'counselor_profile_status')) {
                $table->string('counselor_profile_status')->default('draft');
            }
            if (!Schema::hasColumn('users', 'counselor_profile_reviewed_at')) {
                $table->timestamp('counselor_profile_reviewed_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'counselor_profile_reviewed_by')) {
                $table->foreignId('counselor_profile_reviewed_by')->nullable()
                      ->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'counselor_profile_review_notes')) {
                $table->text('counselor_profile_review_notes')->nullable();
            }
        });
    }

    public function down(): void
    {
        // No-op: this is a safety migration, original migrations handle rollback
    }
};
