<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only migrate if both tables exist
        if (!Schema::hasTable('srgbv_cases') || !Schema::hasTable('incidents')) {
            return;
        }

        // Migrate SRGBV cases → incidents
        $cases = DB::table('srgbv_cases')->get();

        foreach ($cases as $case) {
            $incidentId = DB::table('incidents')->insertGetId([
                'incident_number' => str_replace('SRGBV-', 'SIR-', $case->case_number),
                'type' => 'srgbv',
                'category' => $case->category,
                'source' => 'internal',
                'status' => $case->status,
                'priority' => $case->priority,
                'title' => $case->title,
                'description' => $case->description,
                'incident_date' => $case->incident_date,
                'incident_location' => $case->incident_location,
                'incident_description' => $case->incident_description,
                'witnesses' => $case->witnesses,
                'is_recurring' => $case->is_recurring,
                'school_name' => $case->victim_school,
                'victim_name' => $case->victim_name,
                'victim_age' => $case->victim_age,
                'victim_gender' => $case->victim_gender,
                'victim_grade' => $case->victim_grade,
                'victim_contact' => $case->victim_contact,
                'victim_parent_guardian' => $case->victim_parent_guardian,
                'victim_parent_contact' => $case->victim_parent_contact,
                'perpetrator_name' => $case->perpetrator_name,
                'perpetrator_type' => $case->perpetrator_type,
                'perpetrator_description' => $case->perpetrator_description,
                'reported_by' => $case->reported_by,
                'assigned_to' => $case->assigned_to,
                'division_id' => $case->division_id,
                'is_confidential' => $case->is_confidential,
                'resolution' => $case->resolution,
                'resolution_date' => $case->resolution_date,
                'referral_agency' => $case->referral_agency,
                'referral_details' => $case->referral_details,
                'follow_up_required' => $case->follow_up_required,
                'follow_up_date' => $case->follow_up_date,
                'risk_level' => $case->risk_level,
                'immediate_action_required' => $case->immediate_action_required,
                'safety_plan' => $case->safety_plan,
                'legacy_srgbv_id' => $case->id,
                'created_at' => $case->created_at,
                'updated_at' => $case->updated_at,
            ]);

            // Migrate case notes
            if (Schema::hasTable('srgbv_case_notes')) {
                $notes = DB::table('srgbv_case_notes')
                    ->where('srgbv_case_id', $case->id)
                    ->get();

                foreach ($notes as $note) {
                    DB::table('incident_notes')->insert([
                        'incident_id' => $incidentId,
                        'user_id' => $note->user_id,
                        'note' => $note->note,
                        'note_type' => $note->note_type,
                        'is_private' => $note->is_private,
                        'created_at' => $note->created_at,
                        'updated_at' => $note->updated_at,
                    ]);
                }
            }

            // Migrate case files
            if (Schema::hasTable('srgbv_case_files')) {
                $files = DB::table('srgbv_case_files')
                    ->where('srgbv_case_id', $case->id)
                    ->get();

                foreach ($files as $file) {
                    DB::table('incident_files')->insert([
                        'incident_id' => $incidentId,
                        'uploaded_by' => $file->uploaded_by,
                        'file_name' => $file->file_name,
                        'file_path' => $file->file_path,
                        'file_type' => $file->file_type,
                        'file_size' => $file->file_size,
                        'category' => $file->category,
                        'description' => $file->description,
                        'created_at' => $file->created_at,
                        'updated_at' => $file->updated_at,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        // Remove all migrated incidents (those with a legacy reference)
        DB::table('incidents')->whereNotNull('legacy_srgbv_id')->delete();
    }
};
