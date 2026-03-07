<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WeeklyUpdateController;
use App\Http\Controllers\WeeklyPlanController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\LivePollController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DivisionController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\StaffApprovalController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\CasesReportController;
use App\Http\Controllers\TrackedActivityController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CounselorProfileController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\SirDashboardController;
use App\Http\Controllers\PublicIncidentController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| SIR Public Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/

Route::prefix('sir/public')->name('sir.public.')->middleware('throttle:10,1')->group(function () {
    Route::get('/report', [PublicIncidentController::class, 'create'])->name('report');
    Route::post('/report', [PublicIncidentController::class, 'store'])->name('store');
    Route::get('/confirm', [PublicIncidentController::class, 'confirm'])->name('confirm');
    Route::get('/track', [PublicIncidentController::class, 'trackForm'])->name('track.form');
    Route::post('/track', [PublicIncidentController::class, 'track'])->name('track');
    
    // OTP verification
    Route::post('/otp/send', [PublicIncidentController::class, 'sendOtp'])->name('otp.send');
    Route::post('/otp/verify', [PublicIncidentController::class, 'verifyOtp'])->name('otp.verify');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active'])->group(function () {

    // Live Polling
    Route::get('/live/poll', [LivePollController::class, 'poll'])->name('live.poll');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    Route::delete('/profile/photo', [ProfileController::class, 'removePhoto'])->name('profile.photo.remove');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Weekly Updates
    Route::get('weekly-updates/consolidated', [WeeklyUpdateController::class, 'consolidated'])->name('weekly-updates.consolidated');
    Route::get('weekly-updates/download-consolidated', [WeeklyUpdateController::class, 'downloadConsolidated'])->name('weekly-updates.download-consolidated');
    Route::resource('weekly-updates', WeeklyUpdateController::class);
    Route::post('weekly-updates/{weekly_update}/review', [WeeklyUpdateController::class, 'review'])->name('weekly-updates.review');
    Route::post('weekly-updates/activity/{activity}/comment', [WeeklyUpdateController::class, 'activityComment'])->name('weekly-updates.activity-comment');
    Route::get('weekly-updates/{weekly_update}/download', [WeeklyUpdateController::class, 'downloadSingle'])->name('weekly-updates.download');

    // Weekly Plans
    Route::resource('weekly-plans', WeeklyPlanController::class);
    Route::post('weekly-plans/{weekly_plan}/review', [WeeklyPlanController::class, 'review'])->name('weekly-plans.review');

    // Activities
    Route::resource('activities', ActivityController::class);
    Route::post('activities/{activity}/comment', [ActivityController::class, 'addComment'])->name('activities.comment');

    // Tracked Activities (from weekly submissions)
    Route::get('tracked-activities', [TrackedActivityController::class, 'index'])->name('tracked-activities.index');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

    // Messages
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/create', [MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{message}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{message}/reply', [MessageController::class, 'reply'])->name('messages.reply');
    Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');

    // Director Staff Management
    Route::middleware(['role:director'])->prefix('staff')->name('staff.')->group(function () {
        Route::get('/', [StaffController::class, 'index'])->name('index');
        Route::get('/counselors', [StaffController::class, 'counselors'])->name('counselors');
        Route::get('/create', [StaffController::class, 'create'])->name('create');
        Route::post('/', [StaffController::class, 'store'])->name('store');
        Route::get('/{staff_user}/edit', [StaffController::class, 'edit'])->name('edit');
        Route::put('/{staff_user}', [StaffController::class, 'update'])->name('update');
        Route::delete('/{staff_user}', [StaffController::class, 'destroy'])->name('destroy');
    });

    // Counselor Profile
    Route::get('/counselor-profile/edit/me', [CounselorProfileController::class, 'edit'])->name('counselor-profile.edit');
    Route::put('/counselor-profile', [CounselorProfileController::class, 'update'])->name('counselor-profile.update');
    Route::post('/counselor-profile/documents', [CounselorProfileController::class, 'uploadDocument'])->name('counselor-profile.documents.upload');
    Route::delete('/counselor-profile/documents/{document}', [CounselorProfileController::class, 'deleteDocument'])->name('counselor-profile.documents.delete');
    Route::post('/counselor-profile/qualifications', [CounselorProfileController::class, 'storeQualification'])->name('counselor-profile.qualifications.store');
    Route::delete('/counselor-profile/qualifications/{education}', [CounselorProfileController::class, 'deleteQualification'])->name('counselor-profile.qualifications.delete');
    Route::post('/counselor-profile/certificates', [CounselorProfileController::class, 'storeCertificate'])->name('counselor-profile.certificates.store');
    Route::delete('/counselor-profile/certificates/{certificate}', [CounselorProfileController::class, 'deleteCertificate'])->name('counselor-profile.certificates.delete');
    Route::get('/counselor-profile/{counselor}', [CounselorProfileController::class, 'show'])->name('counselor-profile.show');

    // Cases Report Landing (legacy — redirects to SIR)
    Route::get('/cases-report', [CasesReportController::class, 'index'])->name('cases-report');

    // SRGBV Legacy Redirects → SIR System
    Route::prefix('srgbv')->name('srgbv.')->group(function () {
        Route::get('/dashboard', fn() => redirect()->route('sir.srgbv.dashboard'))->name('dashboard');
        Route::get('/cases', fn() => redirect()->route('sir.srgbv.cases.index'))->name('cases.index');
        Route::get('/cases/create', fn() => redirect()->route('sir.srgbv.cases.create'))->name('cases.create');
        Route::get('/cases/{legacyId}', function ($legacyId) {
            $incident = \App\Models\Incident::where('legacy_srgbv_id', $legacyId)->first();
            if ($incident) return redirect()->route('sir.srgbv.cases.show', $incident);
            return redirect()->route('sir.srgbv.cases.index')->with('error', 'Case not found.');
        })->name('cases.show');
    });

    // ══════════════════════════════════════════════════════════════════════
    // SIR (School Incident Reporter) - Separated Modules
    // ══════════════════════════════════════════════════════════════════════
    Route::prefix('sir')->name('sir.')->group(function () {
        
        // Landing Page (shows both modules)
        Route::get('/', fn() => redirect()->route('sir.dashboard'));
        Route::get('/dashboard', [SirDashboardController::class, 'index'])->name('dashboard');

        // ── SRGBV Module ────────────────────────────────────────────────────
        Route::prefix('srgbv')->name('srgbv.')->group(function () {
            Route::get('/dashboard', [SirDashboardController::class, 'srgbvDashboard'])->name('dashboard');
            Route::get('/cases', [IncidentController::class, 'index'])->name('cases.index')->defaults('module', 'srgbv');
            Route::get('/cases/export', [IncidentController::class, 'exportList'])->name('cases.export')->defaults('module', 'srgbv');
            Route::get('/cases/create', [IncidentController::class, 'create'])->name('cases.create')->defaults('module', 'srgbv');
            Route::post('/cases', [IncidentController::class, 'store'])->name('cases.store')->defaults('module', 'srgbv');
            Route::get('/cases/{incident}', [IncidentController::class, 'show'])->name('cases.show')->defaults('module', 'srgbv');
            Route::get('/cases/{incident}/export', [IncidentController::class, 'exportCase'])->name('cases.export-single')->defaults('module', 'srgbv');
            Route::get('/cases/{incident}/edit', [IncidentController::class, 'edit'])->name('cases.edit')->defaults('module', 'srgbv');
            Route::put('/cases/{incident}', [IncidentController::class, 'update'])->name('cases.update')->defaults('module', 'srgbv');
            Route::post('/cases/{incident}/notes', [IncidentController::class, 'addNote'])->name('cases.notes')->defaults('module', 'srgbv');
            Route::post('/cases/{incident}/files', [IncidentController::class, 'uploadFiles'])->name('cases.files')->defaults('module', 'srgbv');
            Route::delete('/cases/{incident}/files/{file}', [IncidentController::class, 'deleteFile'])->name('cases.files.delete')->defaults('module', 'srgbv');
            Route::patch('/cases/{incident}/status', [IncidentController::class, 'updateStatus'])->name('cases.status')->defaults('module', 'srgbv');
            Route::delete('/cases/{incident}', [IncidentController::class, 'destroy'])->name('cases.destroy')->defaults('module', 'srgbv');
        });

        // ── Other Incidents Module ─────────────────────────────────────────
        Route::prefix('other')->name('other.')->group(function () {
            Route::get('/dashboard', [SirDashboardController::class, 'otherDashboard'])->name('dashboard');
            Route::get('/incidents', [IncidentController::class, 'index'])->name('incidents.index')->defaults('module', 'other');
            Route::get('/incidents/export', [IncidentController::class, 'exportList'])->name('incidents.export')->defaults('module', 'other');
            Route::get('/incidents/create', [IncidentController::class, 'create'])->name('incidents.create')->defaults('module', 'other');
            Route::post('/incidents', [IncidentController::class, 'store'])->name('incidents.store')->defaults('module', 'other');
            Route::get('/incidents/{incident}', [IncidentController::class, 'show'])->name('incidents.show')->defaults('module', 'other');
            Route::get('/incidents/{incident}/export', [IncidentController::class, 'exportCase'])->name('incidents.export-single')->defaults('module', 'other');
            Route::get('/incidents/{incident}/edit', [IncidentController::class, 'edit'])->name('incidents.edit')->defaults('module', 'other');
            Route::put('/incidents/{incident}', [IncidentController::class, 'update'])->name('incidents.update')->defaults('module', 'other');
            Route::post('/incidents/{incident}/notes', [IncidentController::class, 'addNote'])->name('incidents.notes')->defaults('module', 'other');
            Route::post('/incidents/{incident}/files', [IncidentController::class, 'uploadFiles'])->name('incidents.files')->defaults('module', 'other');
            Route::delete('/incidents/{incident}/files/{file}', [IncidentController::class, 'deleteFile'])->name('incidents.files.delete')->defaults('module', 'other');
            Route::patch('/incidents/{incident}/status', [IncidentController::class, 'updateStatus'])->name('incidents.status')->defaults('module', 'other');
            Route::delete('/incidents/{incident}', [IncidentController::class, 'destroy'])->name('incidents.destroy')->defaults('module', 'other');
        });

        // ── Legacy combined routes (redirect to appropriate module) ────────
        Route::get('/incidents', fn() => redirect()->route('sir.dashboard'))->name('incidents.index');
        Route::get('/incidents/create', fn() => redirect()->route('sir.dashboard'))->name('incidents.create');
        Route::get('/incidents/{incident}', function (\App\Models\Incident $incident) {
            if ($incident->type === 'srgbv') {
                return redirect()->route('sir.srgbv.cases.show', $incident);
            }
            return redirect()->route('sir.other.incidents.show', $incident);
        })->name('incidents.show');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware(['role:admin_assistant,tech_assistant,minister'])->prefix('admin')->name('admin.')->group(function () {
        // User Management
        Route::get('users/counselors', [UserController::class, 'counselors'])->name('users.counselors');
        Route::resource('users', UserController::class)->except(['show']);
        Route::patch('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');

        // Counselor Profile Management (admin)
        Route::get('counselor-profile/{counselor}/edit', [CounselorProfileController::class, 'adminEdit'])->name('counselor-profile.edit');
        Route::put('counselor-profile/{counselor}', [CounselorProfileController::class, 'adminUpdate'])->name('counselor-profile.update');
        Route::post('counselor-profile/{counselor}/documents', [CounselorProfileController::class, 'adminUploadDocument'])->name('counselor-profile.documents.upload');
        Route::post('counselor-profile/{counselor}/certificates', [CounselorProfileController::class, 'adminStoreCertificate'])->name('counselor-profile.certificates.store');
        Route::post('counselor-profile/{counselor}/qualifications', [CounselorProfileController::class, 'adminStoreQualification'])->name('counselor-profile.qualifications.store');
        Route::put('counselor-profile/{counselor}/education', [CounselorProfileController::class, 'adminUpdateEducation'])->name('counselor-profile.education.update');
        Route::post('counselor-profile/{counselor}/approve', [CounselorProfileController::class, 'adminApproveProfile'])->name('counselor-profile.approve');
        Route::post('counselor-profile/{counselor}/request-changes', [CounselorProfileController::class, 'adminRequestChanges'])->name('counselor-profile.request-changes');

        // Staff Approvals
        Route::get('staff-approvals', [StaffApprovalController::class, 'index'])->name('staff-approvals.index');
        Route::get('staff-approvals/{user}', [StaffApprovalController::class, 'show'])->name('staff-approvals.show');
        Route::post('staff-approvals/{user}/approve', [StaffApprovalController::class, 'approve'])->name('staff-approvals.approve');
        Route::post('staff-approvals/{user}/reject', [StaffApprovalController::class, 'reject'])->name('staff-approvals.reject');

        // Division Management
        Route::resource('divisions', DivisionController::class)->except(['show']);

        // System Settings
        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
    });
});
