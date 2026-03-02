<?php

namespace App\Http\Controllers;

use App\Models\CounselorDocument;
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

        $counselor->load(['division', 'counselorDocuments']);

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

        $user->load(['division', 'counselorDocuments']);

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
            'counselor_qualification' => 'nullable|in:' . implode(',', array_keys(User::COUNSELOR_QUALIFICATIONS)),
            'counselor_specialization' => 'nullable|in:' . implode(',', array_keys(User::COUNSELOR_SPECIALIZATIONS)),
            'counselor_years_experience' => 'nullable|integer|min:0|max:50',
            'counselor_training' => 'nullable|string|max:2000',
            'counselor_school_phone' => 'nullable|string|max:50',
        ]);

        $user->update($validated);

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
}
