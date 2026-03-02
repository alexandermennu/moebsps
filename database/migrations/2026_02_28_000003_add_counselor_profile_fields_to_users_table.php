<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('counselor_qualification')->nullable()->after('counselor_status');
            $table->string('counselor_specialization')->nullable()->after('counselor_qualification');
            $table->unsignedSmallInteger('counselor_years_experience')->nullable()->after('counselor_specialization');
            $table->text('counselor_training')->nullable()->after('counselor_years_experience');
            $table->string('counselor_school_phone')->nullable()->after('counselor_training');
            $table->date('counselor_appointed_at')->nullable()->after('counselor_school_phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'counselor_qualification',
                'counselor_specialization',
                'counselor_years_experience',
                'counselor_training',
                'counselor_school_phone',
                'counselor_appointed_at',
            ]);
        });
    }
};
