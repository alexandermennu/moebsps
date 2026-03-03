<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // SIR module access: null = no special SIR access (use role defaults)
            // 'srgbv' = can access SRGBV tracker only
            // 'other_incidents' = can access Other Incidents only
            // 'both' = can access both SIR modules
            $table->string('sir_access', 20)->nullable()->after('role');
        });

        // Auto-grant 'srgbv' access to all existing counselors
        DB::table('users')
            ->where('role', 'counselor')
            ->whereNull('sir_access')
            ->update(['sir_access' => 'srgbv']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('sir_access');
        });
    }
};
