<div class="flex items-center gap-3 px-4 py-2.5 bg-[#141416] border border-[#1e1e22] rounded-lg group hover:border-[#2a2a2e] transition-colors"
     x-data="{ open: false }">

    {{-- Status dot --}}
    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" style="background:{{ $color }}"></span>

    {{-- Title --}}
    <span class="text-sm text-[#c8c6c0] flex-1 truncate">{{ $feature->title }}</span>

    {{-- Type badge --}}
    @if($feature->type !== 'personal')
        <span class="text-[10px] text-[#444] font-mono-dm hidden group-hover:inline">{{ $feature->type }}</span>
    @endif

    {{-- Priority badge (only high/critical) --}}
    @if(in_array($feature->priority, ['high','critical']))
        <span class="text-[10px] {{ $feature->priority === 'critical' ? 'text-[#e06c6c]' : 'text-[#d4a017]' }} font-mono-dm">
            {{ $feature->priority }}
        </span>
    @endif

    {{-- AI badge --}}
    @if($feature->ai_suggested)
        <span class="text-[10px] text-[#7c6af7] font-mono-dm" title="AI suggested">ai</span>
    @endif

    {{-- Actions (visible on hover) --}}
    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">

        {{-- Send for approval (client features in backlog/approved) --}}
        @if($feature->needs_approval && in_array($feature->status, ['backlog','approved']))
        <form method="POST" action="{{ route('features.approval.send', $feature) }}">
            @csrf
            <button class="text-[11px] text-[#d47db8] hover:text-[#e898c8] px-2 py-1 rounded hover:bg-[#1a1114] transition-colors">
                Send approval
            </button>
        </form>
        @endif

        {{-- Status advance --}}
        @if(!in_array($feature->status, ['shipped','cancelled','awaiting_approval']))
        <form method="POST" action="{{ route('features.status', $feature) }}">
            @csrf @method('PATCH')
            @php
                $nextStatus = [
                    'backlog'   => 'building',
                    'approved'  => 'building',
                    'building'  => 'review',
                    'review'    => 'shipped',
                ][$feature->status] ?? null;
            @endphp
            @if($nextStatus)
                <input type="hidden" name="status" value="{{ $nextStatus }}">
                <button class="text-[11px] text-[#4CAF7D] hover:text-[#6ddf95] px-2 py-1 rounded hover:bg-[#0d2218] transition-colors">
                    → {{ $nextStatus }}
                </button>
            @endif
        </form>
        @endif

        {{-- Delete --}}
        <form method="POST" action="{{ route('features.destroy', $feature) }}"
              onsubmit="return confirm('Remove this feature?')">
            @csrf @method('DELETE')
            <button class="text-[11px] text-[#333] hover:text-[#e06c6c] px-2 py-1 rounded hover:bg-[#1f0e0e] transition-colors">
                ✕
            </button>
        </form>
    </div>
</div>