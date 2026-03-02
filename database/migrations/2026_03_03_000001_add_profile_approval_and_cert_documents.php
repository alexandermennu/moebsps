<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Profile approval workflow fields on users ──────────────────
        Schema::table('users', function (Blueprint $table) {
            $table->string('counselor_profile_status')->default('draft')->after('counselor_school_principal');
            $table->timestamp('counselor_profile_reviewed_at')->nullable()->after('counselor_profile_status');
            $table->foreignId('counselor_profile_reviewed_by')->nullable()->after('counselor_profile_reviewed_at')
                  ->constrained('users')->nullOnDelete();
            $table->text('counselor_profile_review_notes')->nullable()->after('counselor_profile_reviewed_by');
        });

        // ── Certificate / Achievement supporting document fields ──────
        if (Schema::hasTable('counselor_certificates')) {
            Schema::table('counselor_certificates', function (Blueprint $table) {
                $table->string('document_path')->nullable()->after('description');
                $table->string('document_name')->nullable()->after('document_path');
                $table->string('document_type')->nullable()->after('document_name');
                $table->unsignedInteger('document_size')->nullable()->after('document_type');
            });
        } else {
            Schema::create('counselor_certificates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('certificate_name');
                $table->string('institution');
                $table->string('program')->nullable();
                $table->unsignedSmallInteger('year_obtained')->nullable();
                $table->string('certificate_number')->nullable();
                $table->date('expiry_date')->nullable();
                $table->text('description')->nullable();
                $table->string('document_path')->nullable();
                $table->string('document_name')->nullable();
                $table->string('document_type')->nullable();
                $table->unsignedInteger('document_size')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['counselor_profile_reviewed_by']);
            $table->dropColumn([
                'counselor_profile_status',
                'counselor_profile_reviewed_at',
                'counselor_profile_reviewed_by',
                'counselor_profile_review_notes',
            ]);
        });

        if (Schema::hasColumn('counselor_certificates', 'document_path')) {
            Schema::table('counselor_certificates', function (Blueprint $table) {
                $table->dropColumn(['document_path', 'document_name', 'document_type', 'document_size']);
            });
        }
    }
};
