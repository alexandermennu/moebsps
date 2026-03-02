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

            // Section 3: Education, Experience & Qualifications
            'counselor_qualification'        => 'nullable|in:' . implode(',', array_keys(User::COUNSELOR_QUALIFICATIONS)),
            'counselor_specialization'       => 'nullable|in:' . implode(',', array_keys(User::COUNSELOR_SPECIALIZATIONS)),
            'counselor_years_experience'     => 'nullable|integer|min:0|max:50',
            'counselor_training'             => 'nullable|string|max:2000',

            // Primary education details
            'edu_institution'   => 'nullable|string|max:255',
            'edu_program'       => 'nullable|string|max:255',
            'edu_year_started'  => 'nullable|digits:4|integer|min:1950|max:' . (date('Y') + 5),
            'edu_year_graduated'=> 'nullable|digits:4|integer|min:1950|max:' . (date('Y') + 5),
            'edu_country'       => 'nullable|string|max:100',
            'edu_notes'         => 'nullable|string|max:1000',
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
            // Qualifications
            'counselor_qualification', 'counselor_specialization',
            'counselor_years_experience', 'counselor_training',
        ])->toArray());

        // Save / update primary education record
        if ($validated['counselor_qualification'] && $validated['edu_institution']) {
            $user->counselorEducation()->updateOrCreate(
                ['degree_level' => $validated['counselor_qualification']],
                [
                    'institution'    => $validated['edu_institution'],
                    'program'        => $validated['edu_program'] ?? '',
                    'year_started'   => $validated['edu_year_started'] ?? null,
                    'year_graduated' => $validated['edu_year_graduated'] ?? null,
                    'country'        => $validated['edu_country'] ?? null,
                    'notes'          => $validated['edu_notes'] ?? null,
                ]
            );
        }

        return redirect()->route('counselor-profile.show', $user)
            ->with('success', 'Counselor profile updated successfully.');
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
            'counselor_qualification' => 'nullable|in:' . implode(',', array_keys(User::COUNSELOR_QUALIFICATIONS)),
            'counselor_specialization' => 'nullable|in:' . implode(',', array_keys(User::COUNSELOR_SPECIALIZATIONS)),
            'counselor_years_experience' => 'nullable|integer|min:0|max:50',
            'counselor_training' => 'nullable|string|max:2000',
            'counselor_school_phone' => 'nullable|string|max:50',
            'counselor_appointed_at' => 'nullable|date',
        ]);

        $counselor->update($validated);

        return redirect()->route('counselor-profile.show', $counselor)
            ->with('success', 'Counselor profile updated successfully.');
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
        ]);

        $user->counselorCertificates()->create($validated);

        return redirect()->route('counselor-profile.show', $user)
            ->with('success', 'Certificate added successfully.');
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
        ]);

        $counselor->counselorCertificates()->create($validated);

        return redirect()->route('counselor-profile.show', $counselor)
            ->with('success', 'Certificate added successfully.');
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
