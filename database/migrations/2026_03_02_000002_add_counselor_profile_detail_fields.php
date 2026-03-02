<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Personal details
            $table->string('address')->nullable()->after('phone');
            $table->string('city')->nullable()->after('address');
            $table->date('date_of_birth')->nullable()->after('city');
            $table->string('gender')->nullable()->after('date_of_birth');
            $table->string('nationality')->nullable()->after('gender');
            $table->string('emergency_contact_name')->nullable()->after('nationality');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_relationship')->nullable()->after('emergency_contact_phone');

            // School / Assignment details
            $table->date('counselor_assignment_date')->nullable()->after('counselor_appointed_at');
            $table->string('counselor_school_district')->nullable()->after('counselor_assignment_date');
            $table->string('counselor_school_level')->nullable()->after('counselor_school_district');
            $table->string('counselor_school_type')->nullable()->after('counselor_school_level');
            $table->unsignedInteger('counselor_school_population')->nullable()->after('counselor_school_type');
            $table->unsignedInteger('counselor_num_boys')->nullable()->after('counselor_school_population');
            $table->unsignedInteger('counselor_num_girls')->nullable()->after('counselor_num_boys');
            $table->text('counselor_school_address')->nullable()->after('counselor_num_girls');
            $table->string('counselor_school_principal')->nullable()->after('counselor_school_address');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'address',
                'city',
                'date_of_birth',
                'gender',
                'nationality',
                'emergency_contact_name',
                'emergency_contact_phone',
                'emergency_contact_relationship',
                'counselor_assignment_date',
                'counselor_school_district',
                'counselor_school_level',
                'counselor_school_type',
                'counselor_school_population',
                'counselor_num_boys',
                'counselor_num_girls',
                'counselor_school_address',
                'counselor_school_principal',
            ]);
        });
    }
};
