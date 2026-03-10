<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Report — Ministry of Education</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50 min-h-screen">
    {{-- Header --}}
    <div class="bg-red-800 text-white">
        <div class="max-w-2xl mx-auto px-4 py-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold">Track Your Report</h1>
                    <p class="text-red-200 text-sm">Ministry of Education — Bureau of Student Personnel Services</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-2xl mx-auto px-4 py-12">
        {{-- Search Form --}}
        <div class="bg-white border border-gray-200 rounded-md p-8">
            <div class="text-center mb-6">
                <h2 class="text-lg font-bold text-gray-900">Enter Your Tracking Code</h2>
                <p class="text-sm text-gray-500 mt-1">The tracking code was provided when you submitted your report.</p>
            </div>

            <form method="POST" action="{{ route('sir.public.track') }}" class="flex items-end gap-3 max-w-lg mx-auto">
                @csrf
                <div class="flex-1">
                    <input type="text" 
                           name="tracking_code" 
                           value="{{ old('tracking_code', $trackingCode ?? '') }}" 
                           required 
                           placeholder="e.g., SIR-SRGBV-ABC-1234" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-md text-center text-lg font-mono tracking-wider focus:outline-none focus:ring-2 focus:ring-red-500 uppercase" 
                           minlength="15"
                           maxlength="20"
                           pattern="SIR-(SRGBV|OI)-[A-Z0-9]{3}-\d{4}"
                           title="Format: SIR-SRGBV-XXX-1234 or SIR-OI-XXX-1234">
                </div>
                <button type="submit" class="px-6 py-3 bg-red-700 text-white text-sm font-semibold hover:bg-red-800 rounded-md">Track</button>
            </form>

            @if(session('error'))
            <div class="mt-4 bg-red-50 border border-red-200 p-3 rounded-md text-center">
                <p class="text-sm text-red-700">{{ session('error') }}</p>
            </div>
            @endif
        </div>

        {{-- Result --}}
        @if(isset($incident))
        <div class="mt-8 bg-white border border-gray-200 rounded-md p-8 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-400">Incident Number</p>
                    <p class="text-lg font-mono font-bold text-gray-900">{{ $incident->incident_number }}</p>
                </div>
                <span class="px-3 py-1 text-sm font-medium bg-{{ $incident->status_color }}-100 text-{{ $incident->status_color }}-700 rounded-full">{{ $incident->status_label }}</span>
            </div>

            <div class="border-t border-gray-100 pt-4 space-y-3">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Type</p>
                        <p class="font-medium text-gray-800">{{ $incident->type_label }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Category</p>
                        <p class="font-medium text-gray-800">{{ $incident->category_label }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Submitted</p>
                        <p class="font-medium text-gray-800">{{ $incident->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Incident Date</p>
                        <p class="font-medium text-gray-800">{{ $incident->incident_date->format('M d, Y') }}</p>
                    </div>
                    @if($incident->school_name)
                    <div>
                        <p class="text-gray-500">School</p>
                        <p class="font-medium text-gray-800">{{ $incident->school_name }}</p>
                    </div>
                    @endif
                    @if($incident->school_county)
                    <div>
                        <p class="text-gray-500">County</p>
                        <p class="font-medium text-gray-800">{{ $incident->school_county }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Status Progress --}}
            <div class="border-t border-gray-100 pt-4">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Progress</p>
                @php
                    $statusOrder = ['reported', 'under_review', 'under_investigation', 'action_taken', 'referred', 'resolved', 'closed'];
                    $currentIndex = array_search($incident->status, $statusOrder);
                    $statusLabels = \App\Models\Incident::STATUSES;
                @endphp
                <div class="flex items-center gap-1">
                    @foreach($statusOrder as $index => $status)
                    <div class="flex-1">
                        <div class="h-2 rounded-full {{ $index <= $currentIndex ? 'bg-green-500' : 'bg-gray-200' }}"></div>
                        <p class="text-[10px] mt-1 {{ $index <= $currentIndex ? 'text-green-700 font-medium' : 'text-gray-400' }}">{{ $statusLabels[$status] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Resolution --}}
            @if($incident->resolution)
            <div class="border-t border-gray-100 pt-4">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Resolution</p>
                <p class="text-sm text-gray-700">{{ $incident->resolution }}</p>
                @if($incident->resolution_date)
                <p class="text-xs text-gray-400 mt-1">Resolved on {{ $incident->resolution_date->format('M d, Y') }}</p>
                @endif
            </div>
            @endif

            {{-- Referral info --}}
            @if($incident->referral_agency)
            <div class="border-t border-gray-100 pt-4">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Referral</p>
                <p class="text-sm text-gray-700">This case has been referred to: <strong>{{ $incident->referral_agency }}</strong></p>
            </div>
            @endif

            <div class="text-xs text-gray-400 text-center pt-4 border-t border-gray-100">
                Last updated: {{ $incident->updated_at->format('M d, Y g:i A') }} ({{ $incident->updated_at->diffForHumans() }})
            </div>
        </div>
        @endif

        {{-- Links --}}
        <div class="mt-8 text-center">
            <a href="{{ route('sir.public.report') }}" class="text-sm text-blue-700 hover:underline">← Report a new incident</a>
        </div>
    </div>

    {{-- Footer --}}
    <div class="border-t border-gray-200 bg-white mt-12">
        <div class="max-w-2xl mx-auto px-4 py-4 text-center">
            <p class="text-xs text-gray-400">Ministry of Education · Republic of Liberia · Bureau of Student Personnel Services</p>
        </div>
    </div>
</body>
</html>
