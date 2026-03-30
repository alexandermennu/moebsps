<?php

use App\Models\Division;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create the Office of the Minister division
        $division = Division::updateOrCreate(
            ['code' => 'OOM'],
            [
                'name' => 'Office of the Minister',
                'description' => 'Office of the Minister - Administrative leadership and coordination',
                'is_active' => true,
            ]
        );

        // Assign all Minister's Office staff to this division
        User::whereIn('role', [
            User::ROLE_MINISTER,
            User::ROLE_ADMIN_ASSISTANT,
            User::ROLE_TECH_ASSISTANT,
        ])->update(['division_id' => $division->id]);
    }

    public function down(): void
    {
        // Remove division assignment from Minister's Office staff
        $division = Division::where('code', 'OOM')->first();
        
        if ($division) {
            User::where('division_id', $division->id)
                ->whereIn('role', [
                    User::ROLE_MINISTER,
                    User::ROLE_ADMIN_ASSISTANT,
                    User::ROLE_TECH_ASSISTANT,
                ])
                ->update(['division_id' => null]);
            
            $division->delete();
        }
    }
};
