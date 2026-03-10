<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Changes victim_age from integer to string to support age range keys.
     */
    public function up(): void
    {
        // Step 1: First change the column type to string (to allow string values)
        Schema::table('incidents', function (Blueprint $table) {
            $table->string('victim_age', 20)->nullable()->change();
        });

        // Step 2: Now convert any existing numeric ages to age range keys
        // Map existing numeric values to range keys using raw SQL for efficiency
        DB::statement("UPDATE incidents SET victim_age = 'under_6' WHERE victim_age REGEXP '^[0-5]$'");
        DB::statement("UPDATE incidents SET victim_age = '6_10' WHERE victim_age REGEXP '^([6-9]|10)$'");
        DB::statement("UPDATE incidents SET victim_age = '11_14' WHERE victim_age REGEXP '^(1[1-4])$'");
        DB::statement("UPDATE incidents SET victim_age = '15_17' WHERE victim_age REGEXP '^(1[5-7])$'");
        DB::statement("UPDATE incidents SET victim_age = '18_plus' WHERE victim_age REGEXP '^(1[8-9]|[2-9][0-9]|[0-9]{3,})$'");
        
        // Set any remaining numeric-only values to unknown
        DB::statement("UPDATE incidents SET victim_age = 'unknown' WHERE victim_age REGEXP '^[0-9]+$' AND victim_age NOT IN ('under_6', '6_10', '11_14', '15_17', '18_plus', 'unknown')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear the string values first (can't convert back to int)
        DB::statement("UPDATE incidents SET victim_age = NULL WHERE victim_age IN ('under_6', '6_10', '11_14', '15_17', '18_plus', 'unknown')");
        
        Schema::table('incidents', function (Blueprint $table) {
            $table->integer('victim_age')->nullable()->change();
        });
    }
};
