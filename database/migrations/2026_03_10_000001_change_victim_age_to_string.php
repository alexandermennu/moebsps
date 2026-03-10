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
        // First, convert any existing numeric ages to age range keys
        // Map existing numeric values to range keys
        DB::table('incidents')
            ->whereNotNull('victim_age')
            ->orderBy('id')
            ->chunk(100, function ($incidents) {
                foreach ($incidents as $incident) {
                    $age = $incident->victim_age;
                    $rangeKey = $this->numericToRangeKey($age);
                    
                    if ($rangeKey !== $age) {
                        DB::table('incidents')
                            ->where('id', $incident->id)
                            ->update(['victim_age' => $rangeKey]);
                    }
                }
            });

        // Now change the column type to string
        Schema::table('incidents', function (Blueprint $table) {
            $table->string('victim_age', 20)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->integer('victim_age')->nullable()->change();
        });
    }

    /**
     * Convert numeric age to range key.
     */
    private function numericToRangeKey($value): string
    {
        // If already a valid key, return as-is
        $validKeys = ['under_6', '6_10', '11_14', '15_17', '18_plus', 'unknown'];
        if (in_array($value, $validKeys)) {
            return $value;
        }

        // If numeric, convert to range
        if (is_numeric($value)) {
            $age = (int) $value;
            if ($age < 6) return 'under_6';
            if ($age <= 10) return '6_10';
            if ($age <= 14) return '11_14';
            if ($age <= 17) return '15_17';
            return '18_plus';
        }

        // Unknown or invalid
        return 'unknown';
    }
};
