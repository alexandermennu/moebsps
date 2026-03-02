<?php

namespace App\Http\Controllers;

use App\Models\CounselorCertificate;
use App\Models\CounselorDocument;
use App\Models\CounselorEducation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CounselorProfileController extends Controller
{
    /**
     * Show a counselor's full profile.
     * Accessible by the counselor themselves and admin/full-access users.
     */
    public function show(User $counselor)
    {
        $user = auth()->user();

        // Ensure the target is actually a counselor
        if (!$counselor->isCounselor()) {
            abort(404);
        }

        // Access: own profile, full-access, or same-division director
        if ($counselor->id !== $user->id && !$user->hasFullAccess() && !($user->isDirector() && $user->division_id === $counselor->division_id)) {
            abort(403);
        }

        $counselor->load(['division', 'counselorDocuments', 'counselorEducation', 'counselorCertificates']);

        return view('counselor-profile.show', compact('counselor'));
    }

    /**
     * Show the edit form for counselor profile fields.
     * Only the counselor themselves can edit their extended profile.
     */
    public function edit()
    {
        $user = auth()->user();

        if (!$user->isCounselor()) {
            abort(403, 'Only counselors can access this page.');
        }

        $user->load(['division', 'counselorDocuments', 'counselorEducation', 'counselorCertificates']);

        return view('counselor-profile.edit', ['counselor' => $user]);
    }

    /**
     * Update the counselor's extended profile fields.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        if (!$user->isCounselor()) {
            abort(403);
        }

        $validated = $request->validate([
            // Section 1: Personal Information
            'date_of_birth'                  => 'nullable|date|before:today',
            'gender'                         => 'nullable|in:' . implode(',', array_keys(User::GENDERS)),
            'nationality'                    => 'nullable|string|max:100',
            'address'                        => 'nullable|string|max:500',
            'city'                           => 'nullable|string|max:100',
            'counselor_school_phone'         => 'nullable|string|max:50',
            'emergency_contact_name'         => 'nullable|string|max:255',
            'emergency_contact_phone'        => 'nullable|string|max:50',
            'emergency_contact_relationship' => 'nullable|string|max:100',

            // Section 2: Assignment Details (counselor-editable)
            'counselor_assignment_date'      => 'nullable|date',
            'counselor_school_district'      => 'nullable|string|max:255',
            'counselor_school_address'       => 'nullable|string|max:1000',
            'counselor_school_principal'     => 'nullable|string|max:255',
            'counselor_school_level'         => 'nullable|in:' . implode(',', array_keys(User::SCHOOL_LEVELS)),
            'counselor_school_type'          => 'nullable|in:' . implode(',', array_keys(User::SCHOOL_TYPES)),
            'counselor_school_population'    => 'nullable|integer|min:0|max:50000',
            'counselor_num_boys'             => 'nullable|integer|min:0|max:50000',
            'counselor_num_girls'            => 'nullable|integer|min:0|max:50000',

            // Section 3: Experience & Specialization
            'counselor_specialization'       => 'nullable|in:' . implode(',', array_keys(User::COUNSELOR_SPECIALIZATIONS)),
            'counselor_years_experience'     => 'nullable|integer|min:0|max:50',
            'counselor_training'             => 'nullable|string|max:2000',
        ]);

        $user->update(collect($validated)->only([
            // Personal
            'date_of_birth', 'gender', 'nationality', 'address', 'city',
            'counselor_school_phone', 'emergency_contact_name',
            'emergency_contact_phone', 'emergency_contact_relationship',
            // Assignment
            'counselor_assignment_date', 'counselor_school_district',
            'counselor_school_address', 'counselor_school_principal',
            'counselor_school_level', 'counselor_school_type',
            'counselor_school_population', 'counselor_num_boys', 'counselor_num_girls',
            // Experience & Specialization
            'counselor_specialization', 'counselor_years_experience', 'counselor_training',
        ])->toArray());

        // Set profile status to pending review for admin approval
        $user->update(['counselor_profile_status' => User::PROFILE_PENDING_REVIEW]);

        return redirect()->route('counselor-profile.show', $user)
            ->with('success', 'Your profile has been updated and submitted for review. An administrator will verify your records shortly.');
    }

    /**
     * Upload a document for the counselor.
     */
    public function uploadDocument(Request $request)
    {
        $user = auth()->user();

        if (!$user->isCounselor()) {
            abort(403);
        }

        $request->validate([
            'document_type' => 'required|in:' . implode(',', array_keys(CounselorDocument::DOCUMENT_TYPES)),
            'title' => 'required|string|max:255',
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png,webp,doc,docx|max:5120',
        ]);

        $file = $request->file('document');
        $path = $file->store(
            'counselor-documents/' . $user->id,
            config('filesystems.uploads', 'public')
        );

        CounselorDocument::create([
            'user_id' => $user->id,
            'document_type' => $request->document_type,
            'title' => $request->title,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return redirect()->route('counselor-profile.show', $user)
            ->with('success', 'Document uploaded successfully.');
    }

    /**
     * Delete a counselor document.
     */
    public function deleteDocument(CounselorDocument $document)
    {
        $user = auth()->user();

        // Counselor can delete own documents, or full-access users can delete any
        if ($document->user_id !== $user->id && !$user->hasFullAccess()) {
            abort(403);
        }

        $document->deleteFile();
        $document->delete();

        return back()->with('success', 'Document removed successfully.');
    }

    /**
     * Admin: show the edit form for a counselor's full profile.
     */
    public function adminEdit(User $counselor)
    {
        $user = auth()->user();

        if (!$user->hasFullAccess()) {
            abort(403);
        }

        if (!$counselor->isCounselor()) {
            abort(404);
        }

        $counselor->load(['division', 'counselorDocuments', 'counselorEducation', 'counselorCertificates']);

        return view('counselor-profile.admin-edit', compact('counselor'));
    }

    /**
     * Admin: update counselor's extended profile fields.
     */
    public function adminUpdate(Request $request, User $counselor)
    {
        $user = auth()->user();

        if (!$user->hasFullAccess()) {
            abort(403);
        }

        if (!$counselor->isCounselor()) {
            abort(404);
        }

        $validated = $request->validate([
            // Personal Information
            'date_of_birth'                  => 'nullable|date|before:today',
            'gender'                         => 'nullable|in:' . implode(',', array_keys(User::GENDERS)),
            'nationality'                    => 'nullable|string|max:100',
            'address'                        => 'nullable|string|max:500',
            'city'                           => 'nullable|string|max:100',
            'emergency_contact_name'         => 'nullable|string|max:255',
            'emergency_contact_phone'        => 'nullable|string|max:50',
            'emergency_contact_relationship' => 'nullable|string|max:100',

            // Admin-Managed Assignment Fields
            'counselor_school'               => 'nullable|string|max:255',
            'counselor_county'               => 'nullable|string|max:100',
            'counselor_status'               => 'nullable|in:' . implode(',', array_keys(User::COUNSELOR_STATUSES)),
            'counselor_appointed_at'         => 'nullable|date',

            // Assignment Details
            'counselor_school_phone'         => 'nullable|string|max:50',
            'counselor_assignment_date'      => 'nullable|date',
            'counselor_school_district'      => 'nullable|string|max:255',
            'counselor_school_address'       => 'nullable|string|max:1000',
            'counselor_school_principal'     => 'nullable|string|max:255',
            'counselor_school_level'         => 'nullable|in:' . implode(',', array_keys(User::SCHOOL_LEVELS)),
            'counselor_school_type'          => 'nullable|in:' . implode(',', array_keys(User::SCHOOL_TYPES)),
            'counselor_school_population'    => 'nullable|integer|min:0|max:50000',
            'counselor_num_boys'             => 'nullable|integer|min:0|max:50000',
            'counselor_num_girls'            => 'nullable|integer|min:0|max:50000',

            // Education & Qualifications
            'counselor_specialization'       => 'nullable|in:' . implode(',', array_keys(User::COUNSELOR_SPECIALIZATIONS)),
            'counselor_years_experience'     => 'nullable|integer|min:0|max:50',
            'counselor_training'             => 'nullable|string|max:2000',
        ]);

        // counselor_qualification is auto-synced from education records
        $counselor->update(collect($validated)->except('counselor_qualification')->toArray());

        return redirect()->route('counselor-profile.show', $counselor)
            ->with('success', 'Counselor profile updated successfully.');
    }

    /**
     * Admin: approve a counselor's profile after review.
     */
    public function adminApproveProfile(Request $request, User $counselor)
    {
        $user = auth()->user();

        if (!$user->hasFullAccess()) {
            abort(403);
        }

        if (!$counselor->isCounselor()) {
            abort(404);
        }

        $counselor->update([
            'counselor_profile_status'      => User::PROFILE_APPROVED,
            'counselor_profile_reviewed_at' => now(),
            'counselor_profile_reviewed_by' => $user->id,
            'counselor_profile_review_notes' => $request->input('review_notes'),
        ]);

        return redirect()->route('counselor-profile.show', $counselor)
            ->with('success', 'Counselor profile has been approved.');
    }

    /**
     * Admin: request changes on a counselor's profile.
     */
    public function adminRequestChanges(Request $request, User $counselor)
    {
        $user = auth()->user();

        if (!$user->hasFullAccess()) {
            abort(403);
        }

        if (!$counselor->isCounselor()) {
            abort(404);
        }

        $request->validate([
            'review_notes' => 'required|string|max:2000',
        ]);

        $counselor->update([
            'counselor_profile_status'      => User::PROFILE_CHANGES_REQUESTED,
            'counselor_profile_reviewed_at' => now(),
            'counselor_profile_reviewed_by' => $user->id,
            'counselor_profile_review_notes' => $request->input('review_notes'),
        ]);

        return redirect()->route('counselor-profile.show', $counselor)
            ->with('success', 'Changes have been requested. The counselor will be notified.');
    }

    /**
     * Admin: upload a document for a counselor.
     */
    public function adminUploadDocument(Request $request, User $counselor)
    {
        $user = auth()->user();

        if (!$user->hasFullAccess()) {
            abort(403);
        }

        if (!$counselor->isCounselor()) {
            abort(404);
        }

        $request->validate([
            'document_type' => 'required|in:' . implode(',', array_keys(CounselorDocument::DOCUMENT_TYPES)),
            'title' => 'required|string|max:255',
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png,webp,doc,docx|max:5120',
        ]);

        $file = $request->file('document');
        $path = $file->store(
            'counselor-documents/' . $counselor->id,
            config('filesystems.uploads', 'public')
        );

        CounselorDocument::create([
            'user_id' => $counselor->id,
            'document_type' => $request->document_type,
            'title' => $request->title,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return redirect()->route('counselor-profile.show', $counselor)
            ->with('success', 'Document uploaded successfully.');
    }

    // ─────────────────────────────────────────────────────────
    // Qualification / Education CRUD  (self-service)
    // ─────────────────────────────────────────────────────────

    /**
     * Store a new qualification (education record) for the authenticated counselor.
     */
    public function storeQualification(Request $request)
    {
        $user = auth()->user();

        if (!$user->isCounselor()) {
            abort(403);
        }

        $validated = $request->validate([
            'degree_level'              => 'required|in:' . implode(',', array_keys(User::COUNSELOR_QUALIFICATIONS)),
            'institution'               => 'required|string|max:255',
            'program'                   => 'nullable|string|max:255',
            'year_obtained'             => 'required|digits:4|integer|min:1950|max:' . (date('Y') + 5),
            'country'                   => 'nullable|string|max:100',
            'notes'                     => 'nullable|string|max:1000',
            'qualification_document'    => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,doc,docx|max:5120',
        ]);

        // Handle optional document upload
        $documentData = [];
        if ($request->hasFile('qualification_document')) {
            $file = $request->file('qualification_document');
            $path = $file->store(
                'counselor-qualifications/' . $user->id,
                config('filesystems.uploads', 'public')
            );
            $documentData = [
                'document_path' => $path,
                'document_name' => $file->getClientOriginalName(),
                'document_type' => $file->getMimeType(),
                'document_size' => $file->getSize(),
            ];
        }

        $user->counselorEducation()->create(array_merge(
            collect($validated)->except('qualification_document')->toArray(),
            $documentData
        ));

        // Sync highest qualification to user record
        $this->syncHighestQualification($user);

        // Trigger pending review
        $user->update(['counselor_profile_status' => User::PROFILE_PENDING_REVIEW]);

        return redirect()->route('counselor-profile.edit')
            ->with('success', 'Qualification added and submitted for review.');
    }

    /**
     * Delete a qualification (education record).
     */
    public function deleteQualification(CounselorEducation $education)
    {
        $user = auth()->user();

        if ($education->user_id !== $user->id && !$user->hasFullAccess()) {
            abort(403);
        }

        $owner = User::find($education->user_id);

        // Delete associated document file from storage if present
        if ($education->hasDocument()) {
            Storage::disk(config('filesystems.uploads', 'public'))->delete($education->document_path);
        }

        $education->delete();

        // Re-sync highest qualification
        if ($owner) {
            $this->syncHighestQualification($owner);
        }

        return back()->with('success', 'Qualification removed successfully.');
    }

    /**
     * Sync the user's counselor_qualification field to the highest qualification
     * from their education records.
     */
    protected function syncHighestQualification(User $user): void
    {
        // Ordered from highest to lowest
        $hierarchy = ['doctorate', 'masters', 'bachelors', 'associate', 'diploma', 'certificate', 'other'];

        $educationLevels = $user->counselorEducation()->pluck('degree_level')->toArray();

        $highest = null;
        foreach ($hierarchy as $level) {
            if (in_array($level, $educationLevels)) {
                $highest = $level;
                break;
            }
        }

        $user->update(['counselor_qualification' => $highest]);
    }

    // ─────────────────────────────────────────────────────────
    // Certificate CRUD  (self-service)
    // ─────────────────────────────────────────────────────────

    /**
     * Store a new certificate for the authenticated counselor.
     */
    public function storeCertificate(Request $request)
    {
        $user = auth()->user();

        if (!$user->isCounselor()) {
            abort(403);
        }

        $validated = $request->validate([
            'certificate_name'   => 'required|string|max:255',
            'institution'        => 'required|string|max:255',
            'program'            => 'nullable|string|max:255',
            'year_obtained'      => 'nullable|digits:4|integer|min:1950|max:' . (date('Y') + 5),
            'certificate_number' => 'nullable|string|max:100',
            'expiry_date'        => 'nullable|date',
            'description'        => 'nullable|string|max:1000',
            'certificate_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,doc,docx|max:5120',
        ]);

        // Handle optional document upload
        $documentData = [];
        if ($request->hasFile('certificate_document')) {
            $file = $request->file('certificate_document');
            $path = $file->store(
                'counselor-certificates/' . $user->id,
                config('filesystems.uploads', 'public')
            );
            $documentData = [
                'document_path' => $path,
                'document_name' => $file->getClientOriginalName(),
                'document_type' => $file->getMimeType(),
                'document_size' => $file->getSize(),
            ];
        }

        $user->counselorCertificates()->create(array_merge(
            collect($validated)->except('certificate_document')->toArray(),
            $documentData
        ));

        // Trigger pending review when certificates are added
        $user->update(['counselor_profile_status' => User::PROFILE_PENDING_REVIEW]);

        return redirect()->route('counselor-profile.show', $user)
            ->with('success', 'Certificate added and submitted for review.');
    }

    /**
     * Delete a certificate.
     */
    public function deleteCertificate(CounselorCertificate $certificate)
    {
        $user = auth()->user();

        if ($certificate->user_id !== $user->id && !$user->hasFullAccess()) {
            abort(403);
        }

        // Delete associated document file from storage if present
        if ($certificate->hasDocument()) {
            Storage::disk(config('filesystems.uploads', 'public'))->delete($certificate->document_path);
        }

        $certificate->delete();

        return back()->with('success', 'Certificate removed successfully.');
    }

    // ─────────────────────────────────────────────────────────
    // Admin: Certificate CRUD
    // ─────────────────────────────────────────────────────────

    /**
     * Admin: store a certificate for a counselor.
     */
    public function adminStoreCertificate(Request $request, User $counselor)
    {
        $user = auth()->user();

        if (!$user->hasFullAccess()) {
            abort(403);
        }

        if (!$counselor->isCounselor()) {
            abort(404);
        }

        $validated = $request->validate([
            'certificate_name'   => 'required|string|max:255',
            'institution'        => 'required|string|max:255',
            'program'            => 'nullable|string|max:255',
            'year_obtained'      => 'nullable|digits:4|integer|min:1950|max:' . (date('Y') + 5),
            'certificate_number' => 'nullable|string|max:100',
            'expiry_date'        => 'nullable|date',
            'description'        => 'nullable|string|max:1000',
            'certificate_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,doc,docx|max:5120',
        ]);

        $documentData = [];
        if ($request->hasFile('certificate_document')) {
            $file = $request->file('certificate_document');
            $path = $file->store(
                'counselor-certificates/' . $counselor->id,
                config('filesystems.uploads', 'public')
            );
            $documentData = [
                'document_path' => $path,
                'document_name' => $file->getClientOriginalName(),
                'document_type' => $file->getMimeType(),
                'document_size' => $file->getSize(),
            ];
        }

        $counselor->counselorCertificates()->create(array_merge(
            collect($validated)->except('certificate_document')->toArray(),
            $documentData
        ));

        return redirect()->route('counselor-profile.show', $counselor)
            ->with('success', 'Certificate added successfully.');
    }

    /**
     * Admin: store a qualification for a counselor.
     */
    public function adminStoreQualification(Request $request, User $counselor)
    {
        $user = auth()->user();

        if (!$user->hasFullAccess()) {
            abort(403);
        }

        if (!$counselor->isCounselor()) {
            abort(404);
        }

        $validated = $request->validate([
            'degree_level'              => 'required|in:' . implode(',', array_keys(User::COUNSELOR_QUALIFICATIONS)),
            'institution'               => 'required|string|max:255',
            'program'                   => 'nullable|string|max:255',
            'year_obtained'             => 'required|digits:4|integer|min:1950|max:' . (date('Y') + 5),
            'country'                   => 'nullable|string|max:100',
            'notes'                     => 'nullable|string|max:1000',
            'qualification_document'    => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,doc,docx|max:5120',
        ]);

        $documentData = [];
        if ($request->hasFile('qualification_document')) {
            $file = $request->file('qualification_document');
            $path = $file->store(
                'counselor-qualifications/' . $counselor->id,
                config('filesystems.uploads', 'public')
            );
            $documentData = [
                'document_path' => $path,
                'document_name' => $file->getClientOriginalName(),
                'document_type' => $file->getMimeType(),
                'document_size' => $file->getSize(),
            ];
        }

        $counselor->counselorEducation()->create(array_merge(
            collect($validated)->except('qualification_document')->toArray(),
            $documentData
        ));

        // Sync highest qualification
        $this->syncHighestQualification($counselor);

        return redirect()->route('counselor-profile.show', $counselor)
            ->with('success', 'Qualification added successfully.');
    }

    /**
     * Admin: update counselor education record.
     */
    public function adminUpdateEducation(Request $request, User $counselor)
    {
        $user = auth()->user();

        if (!$user->hasFullAccess()) {
            abort(403);
        }

        if (!$counselor->isCounselor()) {
            abort(404);
        }

        $validated = $request->validate([
            'edu_institution'    => 'required|string|max:255',
            'edu_program'        => 'required|string|max:255',
            'edu_degree_level'   => 'required|in:' . implode(',', array_keys(User::COUNSELOR_QUALIFICATIONS)),
            'edu_year_started'   => 'nullable|digits:4|integer|min:1950|max:' . (date('Y') + 5),
            'edu_year_graduated' => 'nullable|digits:4|integer|min:1950|max:' . (date('Y') + 5),
            'edu_country'        => 'nullable|string|max:100',
            'edu_notes'          => 'nullable|string|max:1000',
        ]);

        $counselor->counselorEducation()->updateOrCreate(
            ['degree_level' => $validated['edu_degree_level']],
            [
                'institution'    => $validated['edu_institution'],
                'program'        => $validated['edu_program'],
                'year_started'   => $validated['edu_year_started'] ?? null,
                'year_graduated' => $validated['edu_year_graduated'] ?? null,
                'country'        => $validated['edu_country'] ?? null,
                'notes'          => $validated['edu_notes'] ?? null,
            ]
        );

        // Also update the qualification on the user record
        $counselor->update(['counselor_qualification' => $validated['edu_degree_level']]);

        return redirect()->route('counselor-profile.show', $counselor)
            ->with('success', 'Education details updated successfully.');
    }
}
