<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('srgbv_cases', function (Blueprint $table) {
            $table->id();
            $table->string('case_number')->unique();

            // ── Status & Priority ──
            $table->string('status')->default('reported');
            // reported, under_investigation, action_taken, referred, resolved, closed
            $table->string('priority')->default('medium');
            // low, medium, high, critical
            $table->string('category');
            // physical_violence, sexual_violence, psychological_violence, bullying, harassment, exploitation, neglect, other

            $table->string('title');
            $table->text('description');

            // ── Victim Information ──
            $table->string('victim_name');
            $table->integer('victim_age')->nullable();
            $table->string('victim_gender')->nullable();
            $table->string('victim_grade')->nullable();
            $table->string('victim_school')->nullable();
            $table->string('victim_contact')->nullable();
            $table->string('victim_parent_guardian')->nullable();
            $table->string('victim_parent_contact')->nullable();

            // ── Perpetrator Information ──
            $table->string('perpetrator_name')->nullable();
            $table->string('perpetrator_type')->nullable();
            // student, teacher, staff, community_member, unknown, other
            $table->text('perpetrator_description')->nullable();

            // ── Incident Details ──
            $table->date('incident_date');
            $table->string('incident_location')->nullable();
            $table->text('incident_description')->nullable();
            $table->text('witnesses')->nullable();
            $table->boolean('is_recurring')->default(false);

            // ── Assignment & Tracking ──
            $table->foreignId('reported_by')->constrained('users');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('division_id')->constrained('divisions');
            $table->boolean('is_confidential')->default(true);

            // ── Resolution ──
            $table->text('resolution')->nullable();
            $table->date('resolution_date')->nullable();
            $table->string('referral_agency')->nullable();
            $table->text('referral_details')->nullable();
            $table->boolean('follow_up_required')->default(false);
            $table->date('follow_up_date')->nullable();

            // ── Risk Assessment ──
            $table->string('risk_level')->nullable();
            // low, moderate, high, immediate_danger
            $table->boolean('immediate_action_required')->default(false);
            $table->text('safety_plan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('srgbv_cases');
    }
};
