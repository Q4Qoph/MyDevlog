<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review: {{ $feature->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'DM Sans', system-ui, sans-serif; }
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500&display=swap');
    </style>
</head>
<body class="bg-[#0e0e10] text-[#e8e6e0] min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-lg">

        {{-- Header --}}
        <div class="mb-8">
            <p class="text-xs font-mono text-[#555] uppercase tracking-widest mb-1">dev<span class="text-[#7c6af7]">log</span> · approval request</p>
            <h1 class="text-2xl font-medium text-[#e8e6e0]">{{ $feature->title }}</h1>
            <p class="text-sm text-[#555] mt-1">{{ $feature->project->name }} · requested {{ $feature->approval_requested_at?->diffForHumans() }}</p>
        </div>

        {{-- Feature description --}}
        @if($feature->description)
        <div class="bg-[#141416] border border-[#1e1e22] rounded-xl p-5 mb-6">
            <p class="text-xs text-[#555] uppercase tracking-widest mb-2">What this feature does</p>
            <p class="text-sm text-[#c8c6c0] leading-relaxed">{{ $feature->description }}</p>
        </div>
        @endif

        {{-- Decision form --}}
        <form action="{{ route('approval.decide', $feature->approval_token) }}" method="POST">
            @csrf

            {{-- Client name + email --}}
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="block text-xs text-[#555] mb-1">Your name <span class="text-[#333]">(optional)</span></label>
                    <input type="text" name="client_name" value="{{ old('client_name') }}"
                        class="w-full bg-[#141416] border border-[#1e1e22] rounded-lg px-3 py-2 text-sm text-[#e8e6e0] focus:outline-none focus:border-[#7c6af7]"
                        placeholder="Jane Smith">
                </div>
                <div>
                    <label class="block text-xs text-[#555] mb-1">Your email <span class="text-[#333]">(optional)</span></label>
                    <input type="email" name="client_email" value="{{ old('client_email') }}"
                        class="w-full bg-[#141416] border border-[#1e1e22] rounded-lg px-3 py-2 text-sm text-[#e8e6e0] focus:outline-none focus:border-[#7c6af7]"
                        placeholder="jane@company.com">
                </div>
            </div>

            {{-- Note --}}
            <div class="mb-6">
                <label class="block text-xs text-[#555] mb-1">Notes or feedback <span class="text-[#333]">(optional)</span></label>
                <textarea name="client_note" rows="3"
                    class="w-full bg-[#141416] border border-[#1e1e22] rounded-lg px-3 py-2 text-sm text-[#e8e6e0] focus:outline-none focus:border-[#7c6af7] resize-none"
                    placeholder="Any requirements, questions, or changes you'd like…">{{ old('client_note') }}</textarea>
            </div>

            {{-- Decision buttons --}}
            <div class="flex gap-3">
                <button type="submit" name="decision" value="approved"
                    class="flex-1 bg-[#0d2218] border border-[#1a3d28] text-[#4CAF7D] font-medium py-3 rounded-xl text-sm hover:bg-[#112a1f] transition-colors">
                    Approve — go ahead
                </button>
                <button type="submit" name="decision" value="changes_requested"
                    class="flex-1 bg-[#141416] border border-[#1e1e22] text-[#888] font-medium py-3 rounded-xl text-sm hover:border-[#2a2a2e] hover:text-[#c8c6c0] transition-colors">
                    Request changes
                </button>
            </div>
        </form>

        <p class="text-center text-xs text-[#333] mt-6">This link is private. No account needed.</p>
    </div>

</body>
</html>
