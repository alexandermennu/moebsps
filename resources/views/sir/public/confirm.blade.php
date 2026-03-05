@php
    $incident = \App\Models\Incident::where('tracking_code', $trackingCode)->first();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Submitted — Ministry of Education</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50 min-h-screen">
    {{-- Header --}}
    <div class="bg-red-800 text-white">
        <div class="max-w-2xl mx-auto px-4 py-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold">Report Submitted</h1>
                    <p class="text-red-200 text-sm">Ministry of Education — Bureau of Student Personnel Services</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-2xl mx-auto px-4 py-12">
        <div class="bg-white border border-gray-200 rounded-md p-8 text-center space-y-6">
            {{-- Success icon --}}
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>

            <div>
                <h2 class="text-xl font-bold text-gray-900">Thank you for your report</h2>
                <p class="text-sm text-gray-500 mt-1">Your report has been received and will be reviewed by the Ministry of Education.</p>
            </div>

            @if($incident)
            <div class="text-sm text-gray-600">
                <p class="mb-1">Incident Number:</p>
                <p class="text-lg font-mono font-bold text-gray-900">{{ $incident->incident_number }}</p>
            </div>
            @endif

            @if($trackingCode && $trackingCode !== 'SUBMITTED')
            <div class="bg-amber-50 border border-amber-200 rounded-md p-6">
                <p class="text-sm text-amber-800 font-medium mb-2">Your Tracking Code</p>
                <p class="text-3xl font-mono font-bold text-amber-900 tracking-wider">{{ $trackingCode }}</p>
                <p class="text-xs text-amber-600 mt-3">Save this code! You can use it to check the status of your report at any time.</p>
            </div>
            @endif

            <div class="pt-4 space-y-3 text-sm text-gray-500">
                <p>What happens next:</p>
                <ol class="text-left max-w-sm mx-auto space-y-2">
                    <li class="flex items-start gap-2"><span class="w-5 h-5 bg-red-100 text-red-700 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">1</span> Your report is reviewed by a counselor</li>
                    <li class="flex items-start gap-2"><span class="w-5 h-5 bg-red-100 text-red-700 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">2</span> An investigation may be opened</li>
                    <li class="flex items-start gap-2"><span class="w-5 h-5 bg-red-100 text-red-700 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">3</span> Appropriate action is taken</li>
                    @if($trackingCode && $trackingCode !== 'SUBMITTED')
                    <li class="flex items-start gap-2"><span class="w-5 h-5 bg-red-100 text-red-700 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">4</span> You can track progress with your code</li>
                    @endif
                </ol>
            </div>

            <div class="flex items-center justify-center gap-4 pt-4">
                <a href="{{ route('sir.public.report') }}" class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 rounded-md">Submit Another Report</a>
                @if($trackingCode && $trackingCode !== 'SUBMITTED')
                <a href="{{ route('sir.public.track.form') }}" class="px-6 py-2.5 bg-red-700 text-white text-sm font-medium hover:bg-red-800 rounded-md">Track Your Report</a>
                @endif
            </div>
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
