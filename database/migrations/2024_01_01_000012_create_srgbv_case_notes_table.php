<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('srgbv_case_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('srgbv_case_id')->constrained('srgbv_cases')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->text('note');
            $table->string('note_type')->default('progress_update');
            // progress_update, follow_up, referral, action_taken, assessment, counseling_session, other
            $table->boolean('is_private')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('srgbv_case_notes');
    }
};
