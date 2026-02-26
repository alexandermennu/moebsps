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
        // Create Divisions
        $divisions = [
            ['name' => 'Finance & Budget', 'code' => 'FIN', 'description' => 'Financial planning, budgeting, and accounting division'],
            ['name' => 'Human Resources', 'code' => 'HR', 'description' => 'Personnel management and employee services'],
            ['name' => 'Information Technology', 'code' => 'IT', 'description' => 'Technology infrastructure and digital services'],
            ['name' => 'Operations & Logistics', 'code' => 'OPS', 'description' => 'Operational management and logistics coordination'],
            ['name' => 'Legal & Compliance', 'code' => 'LEG', 'description' => 'Legal affairs and regulatory compliance'],
            ['name' => 'Planning & Development', 'code' => 'PND', 'description' => 'Strategic planning and organizational development'],
        ];

        foreach ($divisions as $division) {
            Division::updateOrCreate(['code' => $division['code']], $division);
        }

        // Create Admin User
        User::updateOrCreate(
            ['email' => 'admin@moebsps.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'position' => 'System Administrator',
                'is_active' => true,
            ]
        );

        // Create Bureau Head
        User::updateOrCreate(
            ['email' => 'bureauhead@moebsps.com'],
            [
                'name' => 'Bureau Head',
                'password' => Hash::make('password'),
                'role' => User::ROLE_BUREAU_HEAD,
                'position' => 'Head of Bureau',
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
        $finDiv = Division::where('code', 'FIN')->first();
        $hrDiv = Division::where('code', 'HR')->first();
        $itDiv = Division::where('code', 'IT')->first();

        User::updateOrCreate(
            ['email' => 'director.finance@moebsps.com'],
            [
                'name' => 'Finance Director',
                'password' => Hash::make('password'),
                'role' => User::ROLE_DIRECTOR,
                'division_id' => $finDiv->id,
                'position' => 'Division Director',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'director.hr@moebsps.com'],
            [
                'name' => 'HR Director',
                'password' => Hash::make('password'),
                'role' => User::ROLE_DIRECTOR,
                'division_id' => $hrDiv->id,
                'position' => 'Division Director',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'director.it@moebsps.com'],
            [
                'name' => 'IT Director',
                'password' => Hash::make('password'),
                'role' => User::ROLE_DIRECTOR,
                'division_id' => $itDiv->id,
                'position' => 'Division Director',
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
