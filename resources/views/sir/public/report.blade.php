<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report a School Incident — Ministry of Education</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50 min-h-screen">
    {{-- Header --}}
    <div class="bg-red-800 text-white">
        <div class="max-w-3xl mx-auto px-4 py-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold">Report a School Incident</h1>
                    <p class="text-red-200 text-sm">Ministry of Education — Bureau of Student Personnel Services</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 py-8">
        {{-- Info Banner --}}
        <div class="bg-blue-50 border border-blue-200 p-4 rounded-md mb-6">
            <p class="text-sm text-blue-800"><strong>Your report matters.</strong> This form allows you to report any incident occurring in a school. All reports are reviewed by the Ministry of Education. You may remain anonymous, or provide your contact information to receive updates via a tracking code.</p>
        </div>

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 p-4 rounded-md mb-6">
            <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('sir.public.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            {{-- Honeypot --}}
            <div style="display:none;"><input type="text" name="website" tabindex="-1" autocomplete="off"></div>

            {{-- 1. Incident Type --}}
            <div class="bg-white border border-gray-200 rounded-md p-6">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">What happened?</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Type of Incident <span class="text-red-500">*</span></label>
                        <select name="type" id="incident_type" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="">Select...</option>
                            @foreach(\App\Models\Incident::TYPES as $key => $label)
                            <option value="{{ $key }}" {{ old('type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Category <span class="text-red-500">*</span></label>
                        <select name="category" id="incident_category" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="">Select type first...</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Brief Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="e.g., Student assaulted by teacher at XYZ School">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Describe what happened <span class="text-red-500">*</span></label>
                        <textarea name="description" rows="5" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Please provide as much detail as possible...">{{ old('description') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">When did it happen? <span class="text-red-500">*</span></label>
                        <input type="date" name="incident_date" value="{{ old('incident_date') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                </div>
            </div>

            {{-- 2. School --}}
            <div class="bg-white border border-gray-200 rounded-md p-6">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Where did it happen?</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">School Name <span class="text-red-500">*</span></label>
                        <input type="text" name="school_name" value="{{ old('school_name') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Name of the school">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">County <span class="text-red-500">*</span></label>
                        <select name="school_county" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="">Select county...</option>
                            @foreach(\App\Models\User::COUNTIES as $county)
                            <option value="{{ $county }}" {{ old('school_county') === $county ? 'selected' : '' }}>{{ $county }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">District (if known)</label>
                        <input type="text" name="school_district" value="{{ old('school_district') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Location in School</label>
                        <input type="text" name="incident_location" value="{{ old('incident_location') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="e.g., Classroom, playground, etc.">
                    </div>
                </div>
            </div>

            {{-- 3. People Involved --}}
            <div class="bg-white border border-gray-200 rounded-md p-6">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-1">Who was affected?</h3>
                <p class="text-xs text-gray-400 mb-4">This information helps us respond effectively. Leave blank if you don't know.</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Affected Person's Name</label>
                        <input type="text" name="victim_name" value="{{ old('victim_name') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Age</label>
                        <input type="number" name="victim_age" value="{{ old('victim_age') }}" min="1" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Gender</label>
                        <select name="victim_gender" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="">Select...</option>
                            <option value="male" {{ old('victim_gender') === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('victim_gender') === 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- 4. Your Info --}}
            <div class="bg-white border border-gray-200 rounded-md p-6">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-1">Your Information (Optional)</h3>
                <p class="text-xs text-gray-400 mb-4">Providing contact info allows us to reach you for follow-up. You will also receive a tracking code to check the status of your report. You may remain anonymous.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Your Name</label>
                        <input type="text" name="public_reporter_name" value="{{ old('public_reporter_name') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Phone Number</label>
                        <input type="text" name="public_reporter_phone" value="{{ old('public_reporter_phone') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="e.g., 0770-000-000">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Email</label>
                        <input type="email" name="public_reporter_email" value="{{ old('public_reporter_email') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Your Relationship</label>
                        <select name="public_reporter_relationship" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="">Select...</option>
                            @foreach(\App\Models\Incident::REPORTER_RELATIONSHIPS as $key => $label)
                            <option value="{{ $key }}" {{ old('public_reporter_relationship') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- 5. Evidence --}}
            <div class="bg-white border border-gray-200 rounded-md p-6">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-1">Supporting Evidence (Optional)</h3>
                <p class="text-xs text-gray-400 mb-4">Upload up to 3 files (photos, documents). Max 5MB each.</p>
                <input type="file" name="files[]" multiple accept="image/*,.pdf,.doc,.docx" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:bg-red-50 file:text-red-700 hover:file:bg-red-100 file:rounded-md">
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-between">
                <a href="{{ route('sir.public.track.form') }}" class="text-sm text-blue-700 hover:underline">Already reported? Track your report →</a>
                <button type="submit" class="px-8 py-3 bg-red-700 text-white text-sm font-semibold hover:bg-red-800 rounded-md shadow-sm">Submit Report</button>
            </div>
        </form>
    </div>

    {{-- Footer --}}
    <div class="border-t border-gray-200 bg-white mt-12">
        <div class="max-w-3xl mx-auto px-4 py-4 text-center">
            <p class="text-xs text-gray-400">Ministry of Education · Republic of Liberia · Bureau of Student Personnel Services</p>
        </div>
    </div>

    <script>
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
    </script>
</body>
</html>
