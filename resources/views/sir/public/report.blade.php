<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#991B1B">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Report Incident — Ministry of Education</title>
    @vite(['resources/css/app.css'])
    <style>
        html, body { overscroll-behavior: none; }
        .step { display: none; animation: slideIn 0.3s ease-out; }
        .step.active { display: block; }
        @keyframes slideIn { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: translateX(0); } }
        @keyframes slideOut { from { opacity: 1; transform: translateX(0); } to { opacity: 0; transform: translateX(-20px); } }
        .step.exiting { animation: slideOut 0.2s ease-in forwards; }
        input, select, textarea { font-size: 16px !important; } /* Prevents iOS zoom */
        .safe-bottom { padding-bottom: env(safe-area-inset-bottom, 20px); }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <form method="POST" action="{{ route('sir.public.store') }}" enctype="multipart/form-data" id="reportForm" class="flex-1 flex flex-col">
        @csrf
        {{-- Honeypot --}}
        <div style="display:none;"><input type="text" name="website_url" tabindex="-1" autocomplete="off"></div>
        {{-- Reporter type (anonymous or verified) --}}
        <input type="hidden" name="reporter_type" id="reporterType" value="anonymous">
        <input type="hidden" name="verified_phone" id="verifiedPhone" value="">

        {{-- App Header --}}
        <div class="bg-red-800 text-white sticky top-0 z-50 safe-top">
            <div class="px-4 py-3 flex items-center justify-between">
                <button type="button" id="backBtn" class="w-10 h-10 -ml-2 flex items-center justify-center rounded-full hover:bg-white/10 transition hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <div class="flex-1 text-center">
                    <h1 class="text-base font-semibold" id="stepTitle">Report Incident</h1>
                </div>
                <a href="{{ route('sir.public.track.form') }}" class="w-10 h-10 -mr-2 flex items-center justify-center rounded-full hover:bg-white/10 transition" title="Track Report">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </a>
            </div>
            {{-- Progress Dots (hidden on landing/choice) --}}
            <div id="progressDots" class="px-4 pb-3 flex items-center justify-center gap-2 hidden">
                <span class="progress-dot w-2.5 h-2.5 rounded-full bg-white transition-all" data-step="2"></span>
                <span class="progress-dot w-2 h-2 rounded-full bg-white/40 transition-all" data-step="3"></span>
                <span class="progress-dot w-2 h-2 rounded-full bg-white/40 transition-all" data-step="4"></span>
                <span class="progress-dot w-2 h-2 rounded-full bg-white/40 transition-all" data-step="5"></span>
                <span class="progress-dot w-2 h-2 rounded-full bg-white/40 transition-all" data-step="6"></span>
            </div>
        </div>

        {{-- Validation Errors --}}
        @if($errors->any())
        <div id="validationErrors" class="mx-4 mt-4 bg-red-50 border border-red-200 rounded-xl p-4">
            <p class="text-sm font-medium text-red-800 mb-1">Please fix these issues:</p>
            <ul class="text-sm text-red-600 space-y-0.5">
                @foreach($errors->all() as $error)<li>• {{ $error }}</li>@endforeach
            </ul>
        </div>
        @endif

        {{-- Steps Container --}}
        <div class="flex-1 overflow-y-auto px-4 py-6">
            
            {{-- Landing Page --}}
            <div class="step active" data-step="0">
                <div class="text-center pt-8">
                    {{-- Logo/Icon --}}
                    <div class="w-24 h-24 bg-gradient-to-br from-red-600 to-red-800 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Report a School Incident</h2>
                    <p class="text-gray-500 mb-8">Ministry of Education · Republic of Liberia</p>

                    {{-- Features --}}
                    <div class="space-y-4 text-left max-w-sm mx-auto mb-8">
                        <div class="flex items-start gap-4 bg-white rounded-xl p-4 shadow-sm">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Safe & Confidential</h4>
                                <p class="text-sm text-gray-500">Your report is protected. You may remain anonymous.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 bg-white rounded-xl p-4 shadow-sm">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Track Your Report</h4>
                                <p class="text-sm text-gray-500">Get a tracking code to follow up on your case.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 bg-white rounded-xl p-4 shadow-sm">
                            <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Quick & Easy</h4>
                                <p class="text-sm text-gray-500">Takes only 2-3 minutes to complete.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 1: Anonymous or Verified Choice --}}
            <div class="step" data-step="1">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">How would you like to report?</h2>
                    <p class="text-sm text-gray-500 mt-1">Choose how to identify yourself</p>
                </div>

                <div class="space-y-4">
                    {{-- Anonymous Option --}}
                    <button type="button" id="chooseAnonymous" class="w-full text-left bg-white border-2 border-gray-200 rounded-xl p-5 hover:border-gray-300 hover:bg-gray-50 transition group">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0 group-hover:bg-gray-200 transition">
                                <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 text-lg">Stay Anonymous</h3>
                                <p class="text-sm text-gray-500 mt-1">Report without providing your identity. You'll still get a tracking code.</p>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </button>

                    {{-- Verified Option --}}
                    <button type="button" id="chooseVerified" class="w-full text-left bg-white border-2 border-green-200 rounded-xl p-5 hover:border-green-300 hover:bg-green-50 transition group">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 group-hover:bg-green-200 transition">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 text-lg">Verify with Phone</h3>
                                <p class="text-sm text-gray-500 mt-1">Quick SMS verification. Helps us follow up and prioritize your report.</p>
                                <span class="inline-flex items-center gap-1 mt-2 text-xs font-medium text-green-700 bg-green-100 px-2 py-1 rounded-full">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    Recommended
                                </span>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </button>
                </div>

                <p class="text-xs text-gray-400 text-center mt-6">Your choice does not affect how we handle your report. All reports are taken seriously.</p>
            </div>

            {{-- Step 1.5: Phone Input (only for verified) --}}
            <div class="step" data-step="1.5">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Enter your phone number</h2>
                    <p class="text-sm text-gray-500 mt-1">We'll send you a verification code</p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                        <div class="flex gap-2">
                            <div class="w-20 px-4 py-3.5 bg-gray-100 border border-gray-200 rounded-xl text-base text-center text-gray-600 font-medium">+231</div>
                            <input type="tel" id="phoneInput" placeholder="770 000 000" class="flex-1 px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" autocomplete="tel">
                        </div>
                        <p class="text-xs text-gray-400 mt-2">Enter your Liberian mobile number</p>
                    </div>

                    <div id="phoneError" class="hidden bg-red-50 border border-red-200 rounded-xl p-3">
                        <p class="text-sm text-red-600" id="phoneErrorText">Please enter a valid phone number</p>
                    </div>
                </div>
            </div>

            {{-- Step 1.6: OTP Verification --}}
            <div class="step" data-step="1.6">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Enter verification code</h2>
                    <p class="text-sm text-gray-500 mt-1">Sent to <span id="displayPhone" class="font-medium text-gray-700">+231 770 000 000</span></p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">6-Digit Code *</label>
                        <div class="flex justify-center gap-2">
                            <input type="text" maxlength="1" class="otp-input w-12 h-14 text-center text-2xl font-bold bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" data-index="0">
                            <input type="text" maxlength="1" class="otp-input w-12 h-14 text-center text-2xl font-bold bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" data-index="1">
                            <input type="text" maxlength="1" class="otp-input w-12 h-14 text-center text-2xl font-bold bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" data-index="2">
                            <span class="flex items-center text-gray-300">-</span>
                            <input type="text" maxlength="1" class="otp-input w-12 h-14 text-center text-2xl font-bold bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" data-index="3">
                            <input type="text" maxlength="1" class="otp-input w-12 h-14 text-center text-2xl font-bold bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" data-index="4">
                            <input type="text" maxlength="1" class="otp-input w-12 h-14 text-center text-2xl font-bold bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" data-index="5">
                        </div>
                    </div>

                    <div id="otpError" class="hidden bg-red-50 border border-red-200 rounded-xl p-3">
                        <p class="text-sm text-red-600" id="otpErrorText">Invalid code. Please try again.</p>
                    </div>

                    <div class="text-center pt-4">
                        <p class="text-sm text-gray-500">Didn't receive the code?</p>
                        <button type="button" id="resendOtp" class="text-sm font-medium text-green-600 hover:text-green-700 disabled:text-gray-400 disabled:cursor-not-allowed mt-1" disabled>
                            Resend code <span id="resendTimer">(60s)</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Step 1.7: Personal Details (only for verified reporters) --}}
            <div class="step" data-step="1.7">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Phone Verified!</h2>
                    <p class="text-sm text-gray-500 mt-1">Now tell us a bit about yourself</p>
                </div>

                <div class="space-y-4">
                    <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-2">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-green-800">Phone number verified</p>
                                <p class="text-xs text-green-600" id="verifiedPhoneStep17">+231 770 000 000</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Your Full Name *</label>
                        <input type="text" id="verifiedReporterName" name="public_reporter_name" value="{{ old('public_reporter_name') }}" placeholder="Enter your full name" class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" id="verifiedReporterEmail" name="public_reporter_email" value="{{ old('public_reporter_email') }}" placeholder="Optional - for updates on your report" class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Your Relationship to Incident *</label>
                        <select id="verifiedReporterRelationship" name="public_reporter_relationship" class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <option value="">Select your relationship...</option>
                            @foreach(\App\Models\Incident::REPORTER_RELATIONSHIPS as $key => $label)
                            <option value="{{ $key }}" {{ old('public_reporter_relationship') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <p class="text-xs text-gray-400 text-center mt-4">Your information helps us prioritize and follow up on reports effectively.</p>
                </div>
            </div>

            {{-- Step 2: What Happened (was step 1) --}}
            <div class="step" data-step="2">
                {{-- Verified badge (shown if phone verified) --}}
                <div id="verifiedBadge" class="hidden mb-4 bg-green-50 border border-green-200 rounded-xl p-3 flex items-center gap-3">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-green-800">Phone verified</p>
                        <p class="text-xs text-green-600" id="verifiedPhoneDisplay">+231 770 000 000</p>
                    </div>
                </div>

                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">What happened?</h2>
                    <p class="text-sm text-gray-500 mt-1">Tell us about the incident</p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type of Incident *</label>
                        <select name="type" id="incident_type" required class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">Select incident type...</option>
                            <option value="srgbv" {{ old('type') === 'srgbv' ? 'selected' : '' }}>SRGBV (School-Related Gender-Based Violence)</option>
                            <option value="other_incident" {{ old('type') === 'other_incident' ? 'selected' : '' }}>Other Incidents</option>
                        </select>
                        <p class="text-xs text-gray-400 mt-1.5">SRGBV cases are handled by the Counseling Division</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                        <select name="category" id="incident_category" required class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">Select type first...</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">When did it happen? *</label>
                        <input type="date" name="incident_date" value="{{ old('incident_date') }}" max="{{ date('Y-m-d') }}" required class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <p class="text-xs text-gray-400 mt-1">Date cannot be in the future</p>
                    </div>
                </div>
            </div>

            {{-- Step 3: Where --}}
            <div class="step" data-step="3">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Where did it happen?</h2>
                    <p class="text-sm text-gray-500 mt-1">School information</p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">School Name *</label>
                        <input type="text" name="school_name" value="{{ old('school_name') }}" required placeholder="Enter school name" class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">County *</label>
                        <select name="school_county" required class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">Select county...</option>
                            @foreach(\App\Models\User::COUNTIES as $county)
                            <option value="{{ $county }}" {{ old('school_county') === $county ? 'selected' : '' }}>{{ $county }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">District <span class="text-gray-400 font-normal">(optional)</span></label>
                        <input type="text" name="school_district" value="{{ old('school_district') }}" placeholder="Enter district if known" class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Location in school <span class="text-gray-400 font-normal">(optional)</span></label>
                        <input type="text" name="incident_location" value="{{ old('incident_location') }}" placeholder="e.g., Classroom, playground" class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            {{-- Step 4: Details --}}
            <div class="step" data-step="4">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Tell us more</h2>
                    <p class="text-sm text-gray-500 mt-1">Describe what happened</p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Brief Title *</label>
                        <input type="text" name="title" value="{{ old('title') }}" required placeholder="e.g., Student assaulted at school" class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">What happened? *</label>
                        <textarea name="description" rows="6" required placeholder="Describe the incident in detail. Include names, times, and any other relevant information..." class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Step 5: People Involved (shown conditionally based on incident type) --}}
            <div class="step" data-step="5">
                {{-- SRGBV or person-related incidents --}}
                <div id="peopleInvolvedSection">
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900" id="step5Title">Who was affected?</h2>
                        <p class="text-sm text-gray-500 mt-1" id="step5Subtitle">Optional — helps us respond better</p>
                    </div>

                    <div class="space-y-4" id="victimFields">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Affected Person's Name</label>
                            <input type="text" name="victim_name" value="{{ old('victim_name') }}" placeholder="Leave blank if unknown" class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Age Range</label>
                                <select name="victim_age" class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                    <option value="">Select...</option>
                                    @foreach(\App\Models\Incident::VICTIM_AGE_RANGES as $key => $label)
                                    <option value="{{ $key }}" {{ old('victim_age') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                                <select name="victim_gender" class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                    <option value="">Select...</option>
                                    <option value="male" {{ old('victim_gender') === 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('victim_gender') === 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-100">
                            <p class="text-xs text-gray-500 text-center">You can skip this step if you don't have this information</p>
                        </div>
                    </div>
                </div>

                {{-- Property/Non-person incidents (theft, fire, vandalism, etc.) --}}
                <div id="propertyIncidentSection" class="hidden">
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900">Additional Details</h2>
                        <p class="text-sm text-gray-500 mt-1">Help us understand the impact</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Estimated Damage/Loss</label>
                            <select name="damage_estimate" class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                <option value="">Select if applicable...</option>
                                <option value="minor">Minor (less than $100)</option>
                                <option value="moderate">Moderate ($100 - $500)</option>
                                <option value="significant">Significant ($500 - $2,000)</option>
                                <option value="major">Major (over $2,000)</option>
                                <option value="unknown">Unknown</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Was anyone injured?</label>
                            <select name="any_injuries" id="anyInjuries" class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                <option value="no">No injuries</option>
                                <option value="yes">Yes, there were injuries</option>
                                <option value="unknown">Unknown</option>
                            </select>
                        </div>

                        <div id="injuryDetails" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Describe the injuries</label>
                            <textarea name="injury_description" rows="2" placeholder="Brief description of injuries..." class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Has this been reported elsewhere?</label>
                            <select name="reported_elsewhere" class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                <option value="">Select...</option>
                                <option value="no">No, this is the first report</option>
                                <option value="school">Yes, to school administration</option>
                                <option value="police">Yes, to police</option>
                                <option value="both">Yes, to both school and police</option>
                            </select>
                        </div>

                        <div class="pt-4 border-t border-gray-100">
                            <p class="text-xs text-gray-500 text-center">All fields are optional</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 6: Your Info & Submit (only for anonymous reporters) --}}
            <div class="step" data-step="6">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900" id="step6Title">Your Information</h2>
                    <p class="text-sm text-gray-500 mt-1" id="step6Subtitle">Optional — you may remain anonymous</p>
                </div>

                <div class="space-y-4">
                    {{-- Anonymous user info section --}}
                    <div id="anonymousInfoSection">
                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-4">
                            <p class="text-sm text-blue-800">💡 Providing contact info lets us update you and gives you a tracking code to check your report's status.</p>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Your Name</label>
                                <input type="text" id="anonReporterName" name="anon_reporter_name" placeholder="Optional" class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                <input type="tel" id="anonReporterPhone" name="anon_reporter_phone" value="{{ old('public_reporter_phone') }}" placeholder="e.g., 0770-000-000" class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" id="anonReporterEmail" name="anon_reporter_email" placeholder="Optional" class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Your Relationship to Incident</label>
                                <select id="anonReporterRelationship" name="anon_reporter_relationship" class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                    <option value="">Select...</option>
                                    @foreach(\App\Models\Incident::REPORTER_RELATIONSHIPS as $key => $label)
                                    <option value="{{ $key }}" {{ old('public_reporter_relationship') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Verified user summary (shown instead of form) --}}
                    <div id="verifiedInfoSummary" class="hidden">
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-4">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-green-800">Verified Reporter</p>
                                </div>
                            </div>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-green-700">Name:</span>
                                    <span class="font-medium text-green-900" id="summaryName">—</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-green-700">Phone:</span>
                                    <span class="font-medium text-green-900" id="summaryPhone">—</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-green-700">Email:</span>
                                    <span class="font-medium text-green-900" id="summaryEmail">—</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Attach Evidence <span class="text-gray-400 font-normal">(optional)</span></label>
                        <div id="fileUploadArea" class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center bg-white relative">
                            <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="text-sm text-gray-500 mb-1">Tap to upload photos or files</p>
                            <p class="text-xs text-gray-400">You can select multiple files</p>
                            <input type="file" name="files[]" id="fileInput" multiple accept="image/*,.pdf,.doc,.docx" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        </div>
                        <div id="filePreview" class="mt-3 space-y-2 hidden"></div>
                        <p class="text-xs text-gray-400 mt-2 text-center">Max 3 files, 5MB each • Images, PDFs, or documents</p>
                    </div>
                </div>
            </div>

        </div>

        {{-- Bottom Navigation --}}
        <div class="sticky bottom-0 bg-white border-t border-gray-200 px-4 py-4 safe-bottom">
            <div class="flex items-center gap-3">
                <button type="button" id="prevBtn" class="flex-1 py-3.5 bg-gray-100 text-gray-700 font-semibold rounded-xl transition hover:bg-gray-200 hidden">
                    Back
                </button>
                <button type="button" id="startBtn" class="flex-1 py-3.5 bg-red-700 text-white font-semibold rounded-xl transition hover:bg-red-800">
                    Get Started
                </button>
                <button type="button" id="sendOtpBtn" class="flex-1 py-3.5 bg-green-600 text-white font-semibold rounded-xl transition hover:bg-green-700 hidden">
                    <span id="sendOtpText">Send Code</span>
                    <span id="sendOtpLoading" class="hidden">Sending...</span>
                </button>
                <button type="button" id="verifyOtpBtn" class="flex-1 py-3.5 bg-green-600 text-white font-semibold rounded-xl transition hover:bg-green-700 hidden">
                    <span id="verifyOtpText">Verify</span>
                    <span id="verifyOtpLoading" class="hidden">Verifying...</span>
                </button>
                <button type="button" id="continueAfterVerifyBtn" class="flex-1 py-3.5 bg-green-600 text-white font-semibold rounded-xl transition hover:bg-green-700 hidden">
                    Continue
                </button>
                <button type="button" id="nextBtn" class="flex-1 py-3.5 bg-red-700 text-white font-semibold rounded-xl transition hover:bg-red-800 hidden">
                    Next
                </button>
                <button type="submit" id="submitBtn" class="flex-1 py-3.5 bg-green-600 text-white font-semibold rounded-xl transition hover:bg-green-700 hidden">
                    Submit Report
                </button>
            </div>
            {{-- Track link on landing only --}}
            <div id="trackLink" class="mt-3">
                <a href="{{ route('sir.public.track.form') }}" class="block text-center text-sm text-blue-600 hover:underline">Already submitted a report? Track it here →</a>
            </div>
            <p id="footerText" class="text-xs text-gray-400 text-center mt-3 hidden">Ministry of Education · Republic of Liberia</p>
        </div>
    </form>

    <script>
    // Category switching
    const categoriesByType = @json(\App\Models\Incident::CATEGORIES_BY_TYPE);
    const typeSelect = document.getElementById('incident_type');
    const categorySelect = document.getElementById('incident_category');
    const savedCategory = @json(old('category', ''));

    function updateCategories() {
        const type = typeSelect.value;
        const categories = categoriesByType[type] || {};
        categorySelect.innerHTML = '<option value="">Select category...</option>';
        Object.entries(categories).forEach(([key, label]) => {
            const opt = document.createElement('option');
            opt.value = key;
            opt.textContent = label;
            if (key === savedCategory) opt.selected = true;
            categorySelect.appendChild(opt);
        });
        
        // Update step 5 based on incident type
        updateStep5ForType();
    }
    typeSelect.addEventListener('change', updateCategories);
    if (typeSelect.value) updateCategories();

    // Categories that are about property/non-person incidents
    const propertyCategories = ['theft', 'vandalism', 'fire', 'structural_hazard', 'sanitation', 'accident_injury'];
    
    function updateStep5ForType() {
        const type = typeSelect.value;
        const category = categorySelect.value;
        const peopleSection = document.getElementById('peopleInvolvedSection');
        const propertySection = document.getElementById('propertyIncidentSection');
        
        // SRGBV always shows people section
        if (type === 'srgbv') {
            peopleSection.classList.remove('hidden');
            propertySection.classList.add('hidden');
        } else if (type === 'other_incident') {
            // For other incidents, show property section by default
            // (can still ask about injuries)
            peopleSection.classList.add('hidden');
            propertySection.classList.remove('hidden');
        } else {
            // Default to people section
            peopleSection.classList.remove('hidden');
            propertySection.classList.add('hidden');
        }
    }
    
    // Also update when category changes
    categorySelect.addEventListener('change', updateStep5ForType);

    // File upload preview
    const fileInput = document.getElementById('fileInput');
    const filePreview = document.getElementById('filePreview');
    const fileUploadArea = document.getElementById('fileUploadArea');
    let selectedFiles = [];

    fileInput.addEventListener('change', function(e) {
        const files = Array.from(e.target.files).slice(0, 3); // Max 3 files
        selectedFiles = files;
        
        if (files.length > 0) {
            filePreview.classList.remove('hidden');
            filePreview.innerHTML = '';
            
            files.forEach((file, index) => {
                const isImage = file.type.startsWith('image/');
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                
                const fileItem = document.createElement('div');
                fileItem.className = 'flex items-center gap-3 bg-gray-50 rounded-lg p-3';
                fileItem.innerHTML = `
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 ${isImage ? 'bg-blue-100' : 'bg-gray-200'}">
                        ${isImage 
                            ? '<svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>'
                            : '<svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>'
                        }
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">${file.name}</p>
                        <p class="text-xs text-gray-400">${fileSize} MB</p>
                    </div>
                    <button type="button" class="remove-file p-1 text-gray-400 hover:text-red-500" data-index="${index}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                `;
                filePreview.appendChild(fileItem);
            });
            
            // Add remove file handlers
            document.querySelectorAll('.remove-file').forEach(btn => {
                btn.addEventListener('click', function() {
                    const index = parseInt(this.dataset.index);
                    selectedFiles.splice(index, 1);
                    updateFileInput();
                });
            });
            
            // Update upload area text
            fileUploadArea.querySelector('p').textContent = `${files.length} file${files.length > 1 ? 's' : ''} selected`;
        } else {
            filePreview.classList.add('hidden');
            fileUploadArea.querySelector('p').textContent = 'Tap to upload photos or files';
        }
    });
    
    function updateFileInput() {
        // Create a new DataTransfer to update the file input
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
        
        // Trigger change event to refresh preview
        fileInput.dispatchEvent(new Event('change'));
    }

    // Injury details toggle
    const anyInjuries = document.getElementById('anyInjuries');
    const injuryDetails = document.getElementById('injuryDetails');
    if (anyInjuries) {
        anyInjuries.addEventListener('change', function() {
            injuryDetails.classList.toggle('hidden', this.value !== 'yes');
        });
    }

    // Step Navigation
    let currentStep = 0;
    let reporterType = 'anonymous'; // 'anonymous' or 'verified'
    let verifiedPhone = '';
    const totalSteps = 6;
    const steps = document.querySelectorAll('.step');
    const dots = document.querySelectorAll('.progress-dot');
    const progressDots = document.getElementById('progressDots');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const startBtn = document.getElementById('startBtn');
    const sendOtpBtn = document.getElementById('sendOtpBtn');
    const verifyOtpBtn = document.getElementById('verifyOtpBtn');
    const submitBtn = document.getElementById('submitBtn');
    const backBtn = document.getElementById('backBtn');
    const stepTitle = document.getElementById('stepTitle');
    const trackLink = document.getElementById('trackLink');
    const footerText = document.getElementById('footerText');
    const continueAfterVerifyBtn = document.getElementById('continueAfterVerifyBtn');

    const stepTitles = {
        0: 'Report Incident',
        1: 'Identify Yourself',
        1.5: 'Phone Number',
        1.6: 'Verification',
        1.7: 'Your Details',
        2: 'What Happened?',
        3: 'Location',
        4: 'Details',
        5: 'People Involved',
        6: 'Review & Submit'
    };

    function updateUI() {
        // Update steps
        steps.forEach(step => {
            const stepNum = parseFloat(step.dataset.step);
            step.classList.remove('active', 'exiting');
            if (stepNum === currentStep) {
                step.classList.add('active');
            }
        });

        // Show/hide progress dots (hidden on landing, choice, phone, otp, personal details)
        const isFormStep = currentStep >= 2;
        progressDots.classList.toggle('hidden', !isFormStep);

        // Update dots (only for steps 2-6)
        dots.forEach(dot => {
            const dotStep = parseInt(dot.dataset.step);
            if (dotStep === currentStep) {
                dot.classList.remove('bg-white/40', 'w-2', 'h-2');
                dot.classList.add('bg-white', 'w-2.5', 'h-2.5');
            } else if (dotStep < currentStep) {
                dot.classList.remove('bg-white/40', 'w-2.5', 'h-2.5');
                dot.classList.add('bg-white', 'w-2', 'h-2');
            } else {
                dot.classList.remove('bg-white', 'w-2.5', 'h-2.5');
                dot.classList.add('bg-white/40', 'w-2', 'h-2');
            }
        });

        // Update buttons based on current step
        const isLanding = currentStep === 0;
        const isChoice = currentStep === 1;
        const isPhoneInput = currentStep === 1.5;
        const isOtpVerify = currentStep === 1.6;
        const isPersonalDetails = currentStep === 1.7;
        const isLast = currentStep === totalSteps;
        
        startBtn.classList.toggle('hidden', !isLanding);
        sendOtpBtn.classList.toggle('hidden', !isPhoneInput);
        verifyOtpBtn.classList.toggle('hidden', !isOtpVerify);
        continueAfterVerifyBtn.classList.toggle('hidden', !isPersonalDetails);
        prevBtn.classList.toggle('hidden', currentStep < 2 || currentStep === 2);
        backBtn.classList.toggle('hidden', currentStep <= 0);
        nextBtn.classList.toggle('hidden', isLanding || isChoice || isPhoneInput || isOtpVerify || isPersonalDetails || isLast);
        submitBtn.classList.toggle('hidden', !isLast);
        
        // Track link only on landing
        trackLink.classList.toggle('hidden', !isLanding);
        footerText.classList.toggle('hidden', isLanding || isChoice);

        // Update title
        stepTitle.textContent = stepTitles[currentStep] || 'Report Incident';

        // Show verified badge on step 2 if verified
        const verifiedBadge = document.getElementById('verifiedBadge');
        if (verifiedBadge) {
            verifiedBadge.classList.toggle('hidden', reporterType !== 'verified' || currentStep !== 2);
        }

        // Update step 6 based on reporter type
        if (currentStep === 6) {
            updateStep6ForReporterType();
        }
    }

    // Update step 6 UI based on reporter type
    function updateStep6ForReporterType() {
        const anonymousSection = document.getElementById('anonymousInfoSection');
        const verifiedSummary = document.getElementById('verifiedInfoSummary');
        const step6Title = document.getElementById('step6Title');
        const step6Subtitle = document.getElementById('step6Subtitle');
        
        if (reporterType === 'verified') {
            // Show summary, hide form
            anonymousSection.classList.add('hidden');
            verifiedSummary.classList.remove('hidden');
            step6Title.textContent = 'Review & Submit';
            step6Subtitle.textContent = 'Verify your details and submit';
            
            // Populate summary
            const name = document.getElementById('verifiedReporterName')?.value || '—';
            const email = document.getElementById('verifiedReporterEmail')?.value || 'Not provided';
            document.getElementById('summaryName').textContent = name;
            document.getElementById('summaryPhone').textContent = verifiedPhone;
            document.getElementById('summaryEmail').textContent = email || 'Not provided';
        } else {
            // Show form for anonymous
            anonymousSection.classList.remove('hidden');
            verifiedSummary.classList.add('hidden');
            step6Title.textContent = 'Your Information';
            step6Subtitle.textContent = 'Optional — you may remain anonymous';
        }
    }

    function validateStep(step) {
        console.log('validateStep called with step:', step);
        if (step === 0 || step === 1) return true;
        
        // Validate step 1.7 (personal details for verified users)
        if (step === 1.7) {
            const name = document.getElementById('verifiedReporterName');
            const relationship = document.getElementById('verifiedReporterRelationship');
            console.log('Step 1.7 validation - name element:', name, 'relationship element:', relationship);
            let valid = true;
            
            if (!name.value.trim()) {
                console.log('Name validation failed');
                valid = false;
                name.classList.add('border-red-500', 'ring-2', 'ring-red-200');
                setTimeout(() => name.classList.remove('border-red-500', 'ring-2', 'ring-red-200'), 2000);
            }
            if (!relationship.value) {
                console.log('Relationship validation failed');
                valid = false;
                relationship.classList.add('border-red-500', 'ring-2', 'ring-red-200');
                setTimeout(() => relationship.classList.remove('border-red-500', 'ring-2', 'ring-red-200'), 2000);
            }
            console.log('Step 1.7 validation result:', valid);
            return valid;
        }
        
        const currentStepEl = document.querySelector(`.step[data-step="${step}"]`);
        if (!currentStepEl) return true;
        const requiredFields = currentStepEl.querySelectorAll('[required]');
        let valid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                valid = false;
                field.classList.add('border-red-500', 'ring-2', 'ring-red-200');
                setTimeout(() => {
                    field.classList.remove('border-red-500', 'ring-2', 'ring-red-200');
                }, 2000);
            }
        });
        
        return valid;
    }

    // Next button
    nextBtn.addEventListener('click', () => {
        if (!validateStep(currentStep)) return;
        if (currentStep < totalSteps) {
            currentStep++;
            updateUI();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    // Start button (landing → choice)
    startBtn.addEventListener('click', () => {
        currentStep = 1;
        updateUI();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Anonymous choice
    document.getElementById('chooseAnonymous').addEventListener('click', () => {
        reporterType = 'anonymous';
        document.getElementById('reporterType').value = 'anonymous';
        currentStep = 2;
        updateUI();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Verified choice
    document.getElementById('chooseVerified').addEventListener('click', () => {
        reporterType = 'verified';
        document.getElementById('reporterType').value = 'verified';
        currentStep = 1.5;
        updateUI();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Back button (prev for form steps)
    prevBtn.addEventListener('click', () => {
        if (currentStep > 2) {
            currentStep--;
            updateUI();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    // Header back button
    backBtn.addEventListener('click', () => {
        if (currentStep === 1.5) {
            currentStep = 1;
        } else if (currentStep === 1.6) {
            currentStep = 1.5;
        } else if (currentStep === 1.7) {
            currentStep = 1.6;
        } else if (currentStep === 2 && reporterType === 'verified') {
            currentStep = 1.7;
        } else if (currentStep === 2) {
            currentStep = 1;
        } else if (currentStep > 0) {
            currentStep--;
        }
        updateUI();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Continue after verification (personal details) button
    continueAfterVerifyBtn.addEventListener('click', () => {
        console.log('Continue button clicked, currentStep:', currentStep);
        const name = document.getElementById('verifiedReporterName');
        const relationship = document.getElementById('verifiedReporterRelationship');
        console.log('Name value:', name?.value, 'Relationship value:', relationship?.value);
        
        if (!validateStep(1.7)) {
            console.log('Validation failed for step 1.7');
            return;
        }
        console.log('Validation passed, moving to step 2');
        currentStep = 2;
        updateUI();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Phone input & OTP
    const phoneInput = document.getElementById('phoneInput');
    const phoneError = document.getElementById('phoneError');
    const phoneErrorText = document.getElementById('phoneErrorText');
    const otpInputs = document.querySelectorAll('.otp-input');
    const otpError = document.getElementById('otpError');
    const otpErrorText = document.getElementById('otpErrorText');
    const displayPhone = document.getElementById('displayPhone');
    const resendOtp = document.getElementById('resendOtp');
    const resendTimer = document.getElementById('resendTimer');
    let resendCountdown = 60;
    let resendInterval;

    // Send OTP
    sendOtpBtn.addEventListener('click', async () => {
        const phone = phoneInput.value.replace(/\D/g, '');
        if (phone.length < 7) {
            phoneError.classList.remove('hidden');
            phoneErrorText.textContent = 'Please enter a valid phone number';
            return;
        }
        phoneError.classList.add('hidden');
        
        // Show loading
        document.getElementById('sendOtpText').classList.add('hidden');
        document.getElementById('sendOtpLoading').classList.remove('hidden');
        sendOtpBtn.disabled = true;

        try {
            const response = await fetch('{{ route("sir.public.otp.send") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ phone: '+231' + phone })
            });
            const data = await response.json();
            
            if (data.success) {
                verifiedPhone = '+231' + phone;
                displayPhone.textContent = verifiedPhone;
                currentStep = 1.6;
                updateUI();
                startResendTimer();
                otpInputs[0].focus();
            } else {
                phoneError.classList.remove('hidden');
                phoneErrorText.textContent = data.message || 'Failed to send code. Try again.';
            }
        } catch (e) {
            phoneError.classList.remove('hidden');
            phoneErrorText.textContent = 'Network error. Please try again.';
        }

        // Reset button
        document.getElementById('sendOtpText').classList.remove('hidden');
        document.getElementById('sendOtpLoading').classList.add('hidden');
        sendOtpBtn.disabled = false;
    });

    // OTP input handling
    otpInputs.forEach((input, index) => {
        input.addEventListener('input', (e) => {
            const value = e.target.value;
            if (value && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }
            // Auto-verify when all filled
            const code = Array.from(otpInputs).map(i => i.value).join('');
            if (code.length === 6) {
                verifyOtpBtn.click();
            }
        });
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !input.value && index > 0) {
                otpInputs[index - 1].focus();
            }
        });
        // Allow paste
        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            const digits = paste.replace(/\D/g, '').slice(0, 6);
            digits.split('').forEach((d, i) => {
                if (otpInputs[i]) otpInputs[i].value = d;
            });
            if (digits.length === 6) verifyOtpBtn.click();
        });
    });

    // Verify OTP
    verifyOtpBtn.addEventListener('click', async () => {
        const code = Array.from(otpInputs).map(i => i.value).join('');
        if (code.length !== 6) {
            otpError.classList.remove('hidden');
            otpErrorText.textContent = 'Please enter the complete 6-digit code';
            return;
        }
        otpError.classList.add('hidden');

        document.getElementById('verifyOtpText').classList.add('hidden');
        document.getElementById('verifyOtpLoading').classList.remove('hidden');
        verifyOtpBtn.disabled = true;

        try {
            const response = await fetch('{{ route("sir.public.otp.verify") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ phone: verifiedPhone, code: code })
            });
            const data = await response.json();

            if (data.success) {
                console.log('OTP verification successful!');
                // Success! Store verified phone and proceed to personal details
                document.getElementById('verifiedPhone').value = verifiedPhone;
                document.getElementById('verifiedPhoneDisplay').textContent = verifiedPhone;
                
                // Update phone display in step 1.7
                const phoneStep17 = document.getElementById('verifiedPhoneStep17');
                if (phoneStep17) {
                    phoneStep17.textContent = verifiedPhone;
                }
                
                // Pre-fill phone in step 6 (for form submission)
                const phoneField = document.querySelector('input[name="public_reporter_phone"]');
                if (phoneField) {
                    phoneField.value = verifiedPhone;
                    phoneField.readOnly = true;
                    phoneField.classList.add('bg-gray-100');
                }
                
                // Go to personal details step (1.7) for verified users
                console.log('Setting currentStep to 1.7');
                currentStep = 1.7;
                console.log('currentStep is now:', currentStep);
                updateUI();
                console.log('updateUI called, checking step 1.7 visibility...');
                const step17El = document.querySelector('.step[data-step="1.7"]');
                console.log('Step 1.7 element:', step17El);
                console.log('Step 1.7 has active class:', step17El?.classList.contains('active'));
                
                clearInterval(resendInterval);
                
                // Focus on name field
                setTimeout(() => {
                    document.getElementById('verifiedReporterName')?.focus();
                }, 100);
            } else {
                otpError.classList.remove('hidden');
                otpErrorText.textContent = data.message || 'Invalid code. Please try again.';
                otpInputs.forEach(i => i.value = '');
                otpInputs[0].focus();
            }
        } catch (e) {
            otpError.classList.remove('hidden');
            otpErrorText.textContent = 'Network error. Please try again.';
        }

        document.getElementById('verifyOtpText').classList.remove('hidden');
        document.getElementById('verifyOtpLoading').classList.add('hidden');
        verifyOtpBtn.disabled = false;
    });

    // Resend timer
    function startResendTimer() {
        resendCountdown = 60;
        resendOtp.disabled = true;
        resendTimer.textContent = `(${resendCountdown}s)`;
        
        resendInterval = setInterval(() => {
            resendCountdown--;
            if (resendCountdown <= 0) {
                clearInterval(resendInterval);
                resendOtp.disabled = false;
                resendTimer.textContent = '';
            } else {
                resendTimer.textContent = `(${resendCountdown}s)`;
            }
        }, 1000);
    }

    // Resend OTP
    resendOtp.addEventListener('click', () => {
        if (!resendOtp.disabled) {
            sendOtpBtn.click();
        }
    });

    // Prevent double form submission
    let isSubmitting = false;
    document.getElementById('reportForm').addEventListener('submit', function(e) {
        if (isSubmitting) {
            e.preventDefault();
            return false;
        }
        isSubmitting = true;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
    });

    // Initialize
    updateUI();

    // Handle validation errors - navigate to correct step
    @if($errors->any())
    (function() {
        const errorFields = @json($errors->keys());
        const fieldToStep = {
            'type': 2, 'category': 2, 'incident_date': 2, 'title': 2,
            'school_name': 3, 'school_county': 3, 'school_district': 3, 'school_level': 3, 'incident_location': 3,
            'victim_name': 4, 'victim_age': 4, 'victim_gender': 4, 'victim_grade': 4,
            'perpetrator_name': 4, 'perpetrator_type': 4, 'perpetrator_description': 4,
            'description': 5,
            'public_reporter_name': 6, 'public_reporter_phone': 6, 'public_reporter_email': 6, 
            'public_reporter_relationship': 6, 'is_confidential': 6, 'immediate_action_required': 6
        };
        
        let targetStep = 2;
        for (const field of errorFields) {
            if (fieldToStep[field]) {
                targetStep = fieldToStep[field];
                break;
            }
        }
        
        currentStep = targetStep;
        updateUI();
        
        setTimeout(() => {
            const errorBox = document.getElementById('validationErrors');
            if (errorBox) {
                errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }, 150);
    })();
    @endif
    </script>
</body>
</html>
