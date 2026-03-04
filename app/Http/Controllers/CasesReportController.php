<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\User;
use Illuminate\Http\Request;

class CasesReportController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // SRGBV access check
        $canAccessSrgbv = $user->hasFullAccess()
            || ($user->isDirector() && $user->division && $user->division->code === 'CGPC')
            || $user->isCounselor()
            || (in_array($user->role, [User::ROLE_SUPERVISOR, User::ROLE_COORDINATOR]) && $user->division && $user->division->code === 'CGPC');

        // Quick counts using Incident model (migrated from SrgbvCase)
        $srgbvOpenCount = $canAccessSrgbv ? Incident::srgbv()->open()->count() : 0;
        $srgbvTotalCount = $canAccessSrgbv ? Incident::srgbv()->count() : 0;
        $srgbvCriticalCount = $canAccessSrgbv ? Incident::srgbv()->critical()->open()->count() : 0;

        return view('cases.report', [
            'canAccessSrgbv' => $canAccessSrgbv,
            'srgbvOpenCount' => $srgbvOpenCount,
            'srgbvTotalCount' => $srgbvTotalCount,
            'srgbvCriticalCount' => $srgbvCriticalCount,
        ]);
    }
}
