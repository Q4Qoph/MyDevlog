<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Done — DevLog</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#0e0e10] text-[#e8e6e0] min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-sm text-center">

        @php $approved = $feature->latestApproval?->decision === 'approved'; @endphp

        <div class="w-14 h-14 rounded-full mx-auto mb-5 flex items-center justify-center text-xl
            {{ $approved ? 'bg-[#0d2218] text-[#4CAF7D]' : 'bg-[#1c1608] text-[#d4a017]' }}">
            {{ $approved ? '✓' : '↩' }}
        </div>

        <h1 class="text-xl font-medium mb-2">
            {{ $approved ? 'Approved!' : 'Feedback sent' }}
        </h1>

        @if(session('message'))
            <p class="text-sm text-[#888] mb-6">{{ session('message') }}</p>
        @endif

        <div class="bg-[#141416] border border-[#1e1e22] rounded-xl p-4 text-left text-sm">
            <p class="text-[#555] text-xs mb-1">Feature</p>
            <p class="text-[#c8c6c0]">{{ $feature->title }}</p>
            <p class="text-[#555] text-xs mt-3 mb-1">Project</p>
            <p class="text-[#c8c6c0]">{{ $feature->project->name }}</p>

            @if($feature->latestApproval?->client_note)
            <p class="text-[#555] text-xs mt-3 mb-1">Your note</p>
            <p class="text-[#c8c6c0]">{{ $feature->latestApproval->client_note }}</p>
            @endif
        </div>

        <p class="text-xs text-[#333] mt-6">You can close this page.</p>
    </div>
</body>
</html>
