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
use App\Http\Controllers\SrgbvCaseController;
use App\Http\Controllers\SrgbvDashboardController;
use App\Http\Controllers\CasesReportController;
use App\Http\Controllers\TrackedActivityController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Temporary diagnostic route — remove after fixing dashboard
Route::get('/debug-dashboard', function () {
    try {
        $checks = [];

        // 1. Database connection
        $checks['db_connection'] = \Illuminate\Support\Facades\DB::select('SELECT 1 as ok')[0]->ok === 1 ? '✅' : '❌';

        // 2. Division count
        $checks['divisions'] = \App\Models\Division::count() . ' divisions';

        // 3. WeeklyUpdate count
        $checks['weekly_updates'] = \App\Models\WeeklyUpdate::count() . ' updates';

        // 4. TrackedActivity count
        $checks['tracked_activities'] = \App\Models\TrackedActivity::count() . ' tracked';

        // 5. Test divisionUpdateSummaries query (the likely culprit)
        try {
            $summaries = \App\Models\Division::where('is_active', true)
                ->with(['weeklyUpdates' => function ($q) {
                    $q->with('activities')
                        ->whereIn('status', ['submitted', 'approved'])
                        ->latest()
                        ->take(5);
                }])
                ->withCount([
                    'weeklyUpdates as total_updates_count',
                    'weeklyUpdates as approved_updates_count' => fn($q) => $q->where('status', 'approved'),
                ])
                ->get();
            $checks['division_summaries_query'] = '✅ ' . $summaries->count() . ' results';
        } catch (\Throwable $e) {
            $checks['division_summaries_query'] = '❌ ' . $e->getMessage();
        }

        // 6. Test view compilation
        try {
            $user = \App\Models\User::first();
            $checks['first_user'] = $user ? '✅ ' . $user->name . ' (role: ' . $user->role . ')' : '❌ No users';
        } catch (\Throwable $e) {
            $checks['first_user'] = '❌ ' . $e->getMessage();
        }

        // 7. Test fullAccessDashboard (simulated)
        try {
            $user = \App\Models\User::whereIn('role', ['minister', 'admin_assistant', 'tech_assistant'])->first();
            if ($user) {
                $controller = new \App\Http\Controllers\DashboardController();
                $request = \Illuminate\Http\Request::create('/dashboard');
                $request->setUserResolver(fn() => $user);
                $response = $controller->index($request);
                $statusCode = method_exists($response, 'getStatusCode') ? $response->getStatusCode() : 'view';
                $content = method_exists($response, 'getContent') ? $response->getContent() : '';
                if ($statusCode === 500) {
                    $decoded = json_decode($content, true);
                    $checks['full_access_dashboard'] = '❌ ' . ($decoded['error'] ?? $content);
                    $checks['full_access_file'] = ($decoded['file'] ?? '') . ':' . ($decoded['line'] ?? '');
                    $checks['full_access_trace'] = $decoded['trace'] ?? [];
                } else {
                    $checks['full_access_dashboard'] = '✅ Status: ' . $statusCode;
                }
            } else {
                $checks['full_access_dashboard'] = '⚠️ No full-access user found';
            }
        } catch (\Throwable $e) {
            $checks['full_access_dashboard'] = '❌ ' . $e->getMessage() . ' at ' . basename($e->getFile()) . ':' . $e->getLine();
        }

        // 8. Test directorDashboard
        try {
            $user = \App\Models\User::where('role', 'director')->first();
            if ($user) {
                $controller = new \App\Http\Controllers\DashboardController();
                $request = \Illuminate\Http\Request::create('/dashboard');
                $request->setUserResolver(fn() => $user);
                $response = $controller->index($request);
                $checks['director_dashboard'] = '✅ Status: ' . $response->getStatusCode();
            } else {
                $checks['director_dashboard'] = '⚠️ No director user found';
            }
        } catch (\Throwable $e) {
            $checks['director_dashboard'] = '❌ ' . $e->getMessage() . ' at ' . basename($e->getFile()) . ':' . $e->getLine();
        }

        return response()->json($checks, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } catch (\Throwable $e) {
        return response()->json([
            'fatal' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], 500);
    }
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active'])->group(function () {

    // Live Polling
    Route::get('/live/poll', [LivePollController::class, 'poll'])->name('live.poll');

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
        Route::get('/create', [StaffController::class, 'create'])->name('create');
        Route::post('/', [StaffController::class, 'store'])->name('store');
        Route::get('/{staff_user}/edit', [StaffController::class, 'edit'])->name('edit');
        Route::put('/{staff_user}', [StaffController::class, 'update'])->name('update');
        Route::delete('/{staff_user}', [StaffController::class, 'destroy'])->name('destroy');
    });

    // Cases Report Landing
    Route::get('/cases-report', [CasesReportController::class, 'index'])->name('cases-report');

    // SRGBV Case Management
    Route::prefix('srgbv')->name('srgbv.')->group(function () {
        Route::get('/dashboard', [SrgbvDashboardController::class, 'index'])->name('dashboard');
        Route::get('/cases', [SrgbvCaseController::class, 'index'])->name('cases.index');
        Route::get('/cases/create', [SrgbvCaseController::class, 'create'])->name('cases.create');
        Route::post('/cases', [SrgbvCaseController::class, 'store'])->name('cases.store');
        Route::get('/cases/{srgbvCase}', [SrgbvCaseController::class, 'show'])->name('cases.show');
        Route::get('/cases/{srgbvCase}/edit', [SrgbvCaseController::class, 'edit'])->name('cases.edit');
        Route::put('/cases/{srgbvCase}', [SrgbvCaseController::class, 'update'])->name('cases.update');
        Route::post('/cases/{srgbvCase}/notes', [SrgbvCaseController::class, 'addNote'])->name('cases.notes');
        Route::post('/cases/{srgbvCase}/files', [SrgbvCaseController::class, 'uploadFiles'])->name('cases.files');
        Route::delete('/cases/{srgbvCase}/files/{file}', [SrgbvCaseController::class, 'deleteFile'])->name('cases.files.delete');
        Route::patch('/cases/{srgbvCase}/status', [SrgbvCaseController::class, 'updateStatus'])->name('cases.status');
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
