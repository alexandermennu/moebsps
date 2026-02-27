<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BureauTrackerSeeder extends Seeder
{
    public function run(): void
    {
        // Remove old placeholder divisions
        Division::whereIn('code', ['FIN', 'HR', 'IT', 'OPS', 'LEG', 'PND'])->delete();

        // Remove old placeholder director accounts
        User::whereIn('email', [
            'director.finance@moebsps.com',
            'director.hr@moebsps.com',
            'director.it@moebsps.com',
        ])->delete();

        // Create Divisions
        $divisions = [
            ['name' => 'Division of School Health', 'code' => 'DSH', 'description' => 'School health programs and student wellness initiatives'],
            ['name' => 'Division of Career Guidance & Psychosocial Counseling', 'code' => 'CGPC', 'description' => 'Career guidance services and psychosocial counseling support'],
            ['name' => 'Division of School Feeding', 'code' => 'DSF', 'description' => 'School feeding programs and nutrition services'],
            ['name' => 'Division of Community Engagement & Dropout Prevention', 'code' => 'CEDP', 'description' => 'Community engagement initiatives and dropout prevention programs'],
            ['name' => 'Division of National Service Program', 'code' => 'DNSP', 'description' => 'National service program coordination and management'],
        ];

        foreach ($divisions as $division) {
            Division::updateOrCreate(['code' => $division['code']], $division);
        }

        // Remove old role accounts
        User::whereIn('email', [
            'admin@moebsps.com',
            'bureauhead@moebsps.com',
        ])->delete();

        // Create Administrative Assistant
        User::updateOrCreate(
            ['email' => 'admin@moebsps.com'],
            [
                'name' => 'Administrative Assistant',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN_ASSISTANT,
                'position' => 'Administrative Assistant',
                'is_active' => true,
            ]
        );

        // Create Technical Assistant
        User::updateOrCreate(
            ['email' => 'tech@moebsps.com'],
            [
                'name' => 'Technical Assistant',
                'password' => Hash::make('password'),
                'role' => User::ROLE_TECH_ASSISTANT,
                'position' => 'Technical Assistant',
                'is_active' => true,
            ]
        );

        // Create Minister
        User::updateOrCreate(
            ['email' => 'minister@moebsps.com'],
            [
                'name' => 'Minister',
                'password' => Hash::make('password'),
                'role' => User::ROLE_MINISTER,
                'position' => 'Minister',
                'is_active' => true,
            ]
        );

        // Create Division Directors
        $dshDiv = Division::where('code', 'DSH')->first();
        $cgpcDiv = Division::where('code', 'CGPC')->first();
        $dsfDiv = Division::where('code', 'DSF')->first();
        $cedpDiv = Division::where('code', 'CEDP')->first();
        $dnspDiv = Division::where('code', 'DNSP')->first();

        User::updateOrCreate(
            ['email' => 'director.schoolhealth@moebsps.com'],
            [
                'name' => 'School Health Director',
                'password' => Hash::make('password'),
                'role' => User::ROLE_DIRECTOR,
                'division_id' => $dshDiv->id,
                'position' => 'Division Director',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'director.counseling@moebsps.com'],
            [
                'name' => 'Career Guidance & Counseling Director',
                'password' => Hash::make('password'),
                'role' => User::ROLE_DIRECTOR,
                'division_id' => $cgpcDiv->id,
                'position' => 'Division Director',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'director.schoolfeeding@moebsps.com'],
            [
                'name' => 'School Feeding Director',
                'password' => Hash::make('password'),
                'role' => User::ROLE_DIRECTOR,
                'division_id' => $dsfDiv->id,
                'position' => 'Division Director',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'director.community@moebsps.com'],
            [
                'name' => 'Community Engagement Director',
                'password' => Hash::make('password'),
                'role' => User::ROLE_DIRECTOR,
                'division_id' => $cedpDiv->id,
                'position' => 'Division Director',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'director.nationalservice@moebsps.com'],
            [
                'name' => 'National Service Program Director',
                'password' => Hash::make('password'),
                'role' => User::ROLE_DIRECTOR,
                'division_id' => $dnspDiv->id,
                'position' => 'Division Director',
                'is_active' => true,
            ]
        );

        // Create sample Supervisor (DSH division)
        User::updateOrCreate(
            ['email' => 'supervisor.schoolhealth@moebsps.com'],
            [
                'name' => 'School Health Supervisor',
                'password' => Hash::make('password'),
                'role' => User::ROLE_SUPERVISOR,
                'division_id' => $dshDiv->id,
                'position' => 'Supervisor',
                'is_active' => true,
            ]
        );

        // Create sample Coordinator (CGPC division)
        User::updateOrCreate(
            ['email' => 'coordinator.counseling@moebsps.com'],
            [
                'name' => 'Counseling Coordinator',
                'password' => Hash::make('password'),
                'role' => User::ROLE_COORDINATOR,
                'division_id' => $cgpcDiv->id,
                'position' => 'Coordinator',
                'is_active' => true,
            ]
        );

        // Create sample Counselor (CGPC division)
        User::updateOrCreate(
            ['email' => 'counselor@moebsps.com'],
            [
                'name' => 'Psychosocial Counselor',
                'password' => Hash::make('password'),
                'role' => User::ROLE_COUNSELOR,
                'division_id' => $cgpcDiv->id,
                'position' => 'Counselor',
                'is_active' => true,
            ]
        );

        // Create sample Record Clerk
        User::updateOrCreate(
            ['email' => 'clerk@moebsps.com'],
            [
                'name' => 'Record Clerk',
                'password' => Hash::make('password'),
                'role' => User::ROLE_RECORD_CLERK,
                'division_id' => $dsfDiv->id,
                'position' => 'Record Clerk',
                'is_active' => true,
            ]
        );

        // Create sample Secretary
        User::updateOrCreate(
            ['email' => 'secretary@moebsps.com'],
            [
                'name' => 'Bureau Secretary',
                'password' => Hash::make('password'),
                'role' => User::ROLE_SECRETARY,
                'position' => 'Secretary',
                'is_active' => true,
            ]
        );

        // Create System Settings
        $settings = [
            ['key' => 'overdue_check_days', 'value' => '1', 'type' => 'integer', 'group' => 'activities', 'description' => 'Days after due date to mark as overdue'],
            ['key' => 'escalation_days', 'value' => '3', 'type' => 'integer', 'group' => 'activities', 'description' => 'Days overdue before escalating to Bureau Head'],
            ['key' => 'minister_escalation_days', 'value' => '7', 'type' => 'integer', 'group' => 'activities', 'description' => 'Days overdue before escalating to Minister'],
            ['key' => 'weekly_update_day', 'value' => 'friday', 'type' => 'string', 'group' => 'schedule', 'description' => 'Day for weekly update submission'],
            ['key' => 'weekly_plan_day', 'value' => 'monday', 'type' => 'string', 'group' => 'schedule', 'description' => 'Day for weekly plan submission'],
            ['key' => 'enable_email_notifications', 'value' => '1', 'type' => 'boolean', 'group' => 'notifications', 'description' => 'Enable email notifications'],
            ['key' => 'enable_overdue_alerts', 'value' => '1', 'type' => 'boolean', 'group' => 'notifications', 'description' => 'Enable overdue activity alerts'],
            ['key' => 'system_name', 'value' => 'Bureau Activity Tracking System', 'type' => 'string', 'group' => 'general', 'description' => 'System display name'],
            ['key' => 'system_acronym', 'value' => 'MOEBSPS', 'type' => 'string', 'group' => 'general', 'description' => 'System acronym'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
