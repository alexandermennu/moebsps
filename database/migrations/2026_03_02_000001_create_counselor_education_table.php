<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Primary education record — one per counselor, linked to their highest qualification
        Schema::create('counselor_education', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('institution');                    // School / University name
            $table->string('program');                        // e.g. "Bachelor of Education"
            $table->string('degree_level');                   // mirrors User::COUNSELOR_QUALIFICATIONS keys
            $table->year('year_started')->nullable();
            $table->year('year_graduated')->nullable();
            $table->string('country')->nullable();            // Country of institution
            $table->text('notes')->nullable();                // e.g. honours, GPA, etc.
            $table->timestamps();

            $table->index('user_id');
        });

        // Additional certificates & achievements — many per counselor
        Schema::create('counselor_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('certificate_name');               // e.g. "Certified School Counselor"
            $table->string('institution');                     // Issuing school / organization
            $table->string('program')->nullable();            // Program / course name
            $table->year('year_obtained')->nullable();
            $table->string('certificate_number')->nullable(); // Cert / licence number
            $table->date('expiry_date')->nullable();          // If the cert expires
            $table->text('description')->nullable();          // Any extra details
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('counselor_certificates');
        Schema::dropIfExists('counselor_education');
    }
};
