<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('approval_status')->default('approved')->after('is_active');
            // approved = can log in (default for admin-created users)
            // pending  = waiting for full-access user to approve (director-created staff)
            // rejected = rejected by full-access user
            $table->unsignedBigInteger('created_by_user_id')->nullable()->after('approval_status');
            $table->timestamp('approved_at')->nullable()->after('created_by_user_id');
            $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');
            $table->text('rejection_reason')->nullable()->after('approved_by');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['approval_status', 'created_by_user_id', 'approved_at', 'approved_by', 'rejection_reason']);
        });
    }
};
