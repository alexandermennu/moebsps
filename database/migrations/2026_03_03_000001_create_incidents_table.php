<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->string('incident_number')->unique();
            // e.g. SIR-2026-0001 (internal) or SIR-2026-PUB-0001 (public)

            // ── Type & Classification ──
            $table->string('type'); // srgbv, disciplinary, safety, infrastructure, academic, health, other
            $table->string('category'); // subtype within the type
            $table->string('source')->default('internal'); // internal, public
            $table->string('status')->default('reported');
            // reported, under_review, under_investigation, action_taken, referred, resolved, closed
            $table->string('priority')->default('medium'); // low, medium, high, critical

            // ── Core Details ──
            $table->string('title');
            $table->text('description');
            $table->date('incident_date');
            $table->string('incident_location')->nullable();
            $table->text('incident_description')->nullable();
            $table->text('witnesses')->nullable();
            $table->boolean('is_recurring')->default(false);

            // ── School Information ──
            $table->string('school_name')->nullable();
            $table->string('school_county')->nullable();
            $table->string('school_district')->nullable();
            $table->string('school_level')->nullable(); // primary, junior_high, senior_high

            // ── Affected Person / Victim ──
            $table->string('victim_name')->nullable();
            $table->integer('victim_age')->nullable();
            $table->string('victim_gender')->nullable();
            $table->string('victim_grade')->nullable();
            $table->string('victim_contact')->nullable();
            $table->string('victim_parent_guardian')->nullable();
            $table->string('victim_parent_contact')->nullable();

            // ── Perpetrator (if applicable) ──
            $table->string('perpetrator_name')->nullable();
            $table->string('perpetrator_type')->nullable();
            // student, teacher, staff, community_member, parent, unknown, other
            $table->text('perpetrator_description')->nullable();

            // ── Public Reporter (for public submissions) ──
            $table->string('public_reporter_name')->nullable();
            $table->string('public_reporter_phone')->nullable();
            $table->string('public_reporter_email')->nullable();
            $table->string('public_reporter_relationship')->nullable();
            // parent, student, teacher, community_member, other
            $table->string('tracking_code')->nullable()->unique();
            // For public reporters to check status without an account

            // ── Internal Assignment & Tracking ──
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            // nullable because public reports have no user account
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('division_id')->nullable()->constrained('divisions')->nullOnDelete();
            $table->boolean('is_confidential')->default(true);

            // ── Resolution ──
            $table->text('resolution')->nullable();
            $table->date('resolution_date')->nullable();
            $table->string('referral_agency')->nullable();
            $table->text('referral_details')->nullable();
            $table->boolean('follow_up_required')->default(false);
            $table->date('follow_up_date')->nullable();

            // ── Risk Assessment ──
            $table->string('risk_level')->nullable(); // low, moderate, high, immediate_danger
            $table->boolean('immediate_action_required')->default(false);
            $table->text('safety_plan')->nullable();

            // ── Legacy Reference ──
            $table->unsignedBigInteger('legacy_srgbv_id')->nullable();
            // References the original srgbv_cases.id for migrated records

            $table->timestamps();

            // ── Indexes ──
            $table->index('type');
            $table->index('source');
            $table->index('status');
            $table->index('priority');
            $table->index('tracking_code');
            $table->index(['type', 'status']);
            $table->index(['source', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
