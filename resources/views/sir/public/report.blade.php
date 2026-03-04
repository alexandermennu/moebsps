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
        <div style="display:none;"><input type="text" name="website" tabindex="-1" autocomplete="off"></div>

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
            {{-- Progress Dots --}}
            <div class="px-4 pb-3 flex items-center justify-center gap-2">
                <span class="progress-dot w-2.5 h-2.5 rounded-full bg-white transition-all" data-step="1"></span>
                <span class="progress-dot w-2 h-2 rounded-full bg-white/40 transition-all" data-step="2"></span>
                <span class="progress-dot w-2 h-2 rounded-full bg-white/40 transition-all" data-step="3"></span>
                <span class="progress-dot w-2 h-2 rounded-full bg-white/40 transition-all" data-step="4"></span>
                <span class="progress-dot w-2 h-2 rounded-full bg-white/40 transition-all" data-step="5"></span>
            </div>
        </div>

        {{-- Validation Errors --}}
        @if($errors->any())
        <div class="mx-4 mt-4 bg-red-50 border border-red-200 rounded-xl p-4">
            <p class="text-sm font-medium text-red-800 mb-1">Please fix these issues:</p>
            <ul class="text-sm text-red-600 space-y-0.5">
                @foreach($errors->all() as $error)<li>• {{ $error }}</li>@endforeach
            </ul>
        </div>
        @endif

        {{-- Steps Container --}}
        <div class="flex-1 overflow-y-auto px-4 py-6">
            
            {{-- Step 1: What Happened --}}
            <div class="step active" data-step="1">
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
                            @foreach(\App\Models\Incident::TYPES as $key => $label)
                            <option value="{{ $key }}" {{ old('type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                        <select name="category" id="incident_category" required class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">Select type first...</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">When did it happen? *</label>
                        <input type="date" name="incident_date" value="{{ old('incident_date') }}" required class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            {{-- Step 2: Where --}}
            <div class="step" data-step="2">
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

            {{-- Step 3: Details --}}
            <div class="step" data-step="3">
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

            {{-- Step 4: People Involved --}}
            <div class="step" data-step="4">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Who was affected?</h2>
                    <p class="text-sm text-gray-500 mt-1">Optional — helps us respond better</p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Affected Person's Name</label>
                        <input type="text" name="victim_name" value="{{ old('victim_name') }}" placeholder="Leave blank if unknown" class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Age</label>
                            <input type="number" name="victim_age" value="{{ old('victim_age') }}" min="1" max="100" placeholder="Age" class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
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

            {{-- Step 5: Your Info & Submit --}}
            <div class="step" data-step="5">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Your Information</h2>
                    <p class="text-sm text-gray-500 mt-1">Optional — you may remain anonymous</p>
                </div>

                <div class="space-y-4">
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-2">
                        <p class="text-sm text-blue-800">💡 Providing contact info lets us update you and gives you a tracking code to check your report's status.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Your Name</label>
                        <input type="text" name="public_reporter_name" value="{{ old('public_reporter_name') }}" placeholder="Optional" class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" name="public_reporter_phone" value="{{ old('public_reporter_phone') }}" placeholder="e.g., 0770-000-000" class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="public_reporter_email" value="{{ old('public_reporter_email') }}" placeholder="Optional" class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Your Relationship to Incident</label>
                        <select name="public_reporter_relationship" class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">Select...</option>
                            @foreach(\App\Models\Incident::REPORTER_RELATIONSHIPS as $key => $label)
                            <option value="{{ $key }}" {{ old('public_reporter_relationship') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pt-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Attach Evidence <span class="text-gray-400 font-normal">(optional)</span></label>
                        <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center bg-white relative">
                            <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="text-sm text-gray-500 mb-2">Tap to upload photos or files</p>
                            <input type="file" name="files[]" multiple accept="image/*,.pdf,.doc,.docx" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        </div>
                        <p class="text-xs text-gray-400 mt-2 text-center">Max 3 files, 5MB each</p>
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
                <button type="button" id="nextBtn" class="flex-1 py-3.5 bg-red-700 text-white font-semibold rounded-xl transition hover:bg-red-800">
                    Next
                </button>
                <button type="submit" id="submitBtn" class="flex-1 py-3.5 bg-green-600 text-white font-semibold rounded-xl transition hover:bg-green-700 hidden">
                    Submit Report
                </button>
            </div>
            <p class="text-xs text-gray-400 text-center mt-3">Ministry of Education · Republic of Liberia</p>
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
    }
    typeSelect.addEventListener('change', updateCategories);
    if (typeSelect.value) updateCategories();

    // Step Navigation
    let currentStep = 1;
    const totalSteps = 5;
    const steps = document.querySelectorAll('.step');
    const dots = document.querySelectorAll('.progress-dot');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    const backBtn = document.getElementById('backBtn');
    const stepTitle = document.getElementById('stepTitle');

    const stepTitles = {
        1: 'What Happened?',
        2: 'Location',
        3: 'Details',
        4: 'People Involved',
        5: 'Your Info'
    };

    function updateUI() {
        // Update steps
        steps.forEach(step => {
            const stepNum = parseInt(step.dataset.step);
            step.classList.remove('active', 'exiting');
            if (stepNum === currentStep) {
                step.classList.add('active');
            }
        });

        // Update dots
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

        // Update buttons
        prevBtn.classList.toggle('hidden', currentStep === 1);
        backBtn.classList.toggle('hidden', currentStep === 1);
        nextBtn.classList.toggle('hidden', currentStep === totalSteps);
        submitBtn.classList.toggle('hidden', currentStep !== totalSteps);

        // Update title
        stepTitle.textContent = stepTitles[currentStep] || 'Report Incident';
    }

    function validateStep(step) {
        const currentStepEl = document.querySelector(`.step[data-step="${step}"]`);
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

    nextBtn.addEventListener('click', () => {
        if (!validateStep(currentStep)) {
            return;
        }
        if (currentStep < totalSteps) {
            currentStep++;
            updateUI();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    prevBtn.addEventListener('click', () => {
        if (currentStep > 1) {
            currentStep--;
            updateUI();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    backBtn.addEventListener('click', () => {
        if (currentStep > 1) {
            currentStep--;
            updateUI();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    // Initialize
    updateUI();
    </script>
</body>
</html>
