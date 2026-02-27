<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'counselor_school')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('counselor_school')->nullable();
            });
        }

        if (!Schema::hasColumn('users', 'counselor_county')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('counselor_county')->nullable();
            });
        }

        if (!Schema::hasColumn('users', 'counselor_status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('counselor_status')->nullable()->default('active');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'counselor_school')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('counselor_school');
            });
        }

        if (Schema::hasColumn('users', 'counselor_county')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('counselor_county');
            });
        }

        if (Schema::hasColumn('users', 'counselor_status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('counselor_status');
            });
        }
    }
};
