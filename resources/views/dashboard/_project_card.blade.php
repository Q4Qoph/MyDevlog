<a href="{{ route('projects.show', $project) }}"
   class="block bg-[#141416] border border-[#1e1e22] rounded-xl p-4 hover:border-[#2a2a2e] transition-colors group">

    <div class="flex items-start justify-between mb-3">
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-0.5">
                <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $project->color }}"></span>
                <p class="text-sm font-medium text-[#e8e6e0] truncate">{{ $project->name }}</p>
            </div>
            @if($project->client_name)
                <p class="text-xs text-[#444] ml-4">{{ $project->client_name }}</p>
            @endif
        </div>

        @php
            $badgeMap = [
                'planning' => ['bg-[#16151f] border-[#2a2840] text-[#a89df5]', 'planning'],
                'active'   => ['bg-[#0d2218] border-[#1a3d28] text-[#4CAF7D]', 'active'],
                'review'   => ['bg-[#1a1114] border-[#2d1520] text-[#d47db8]', 'review'],
                'shipped'  => ['bg-[#141416] border-[#1e1e22] text-[#555]',    'shipped'],
                'archived' => ['bg-[#141416] border-[#1e1e22] text-[#555]',    'archived'],
            ];
            [$badgeCls, $badgeLabel] = $badgeMap[$project->status] ?? ['bg-[#141416] border-[#1e1e22] text-[#555]', $project->status];
        @endphp
        <span class="font-mono-dm text-[10px] border px-2 py-0.5 rounded {{ $badgeCls }}">{{ $badgeLabel }}</span>
    </div>

    {{-- Progress bar --}}
    @php $progress = $project->progress; @endphp
    <div class="mb-3">
        <div class="h-1 bg-[#1e1e22] rounded-full overflow-hidden">
            <div class="h-full rounded-full transition-all" style="width:{{ $progress }}%; background:{{ $project->color }}"></div>
        </div>
    </div>

    {{-- Stats row --}}
    <div class="flex items-center gap-4 text-xs text-[#444]">
        <span><span class="text-[#888]">{{ $project->features_count }}</span> features</span>
        <span><span class="text-[#4CAF7D]">{{ $project->shipped_count }}</span> shipped</span>
        @if($project->pending_approval_count > 0)
            <span class="ml-auto text-[#d47db8]">{{ $project->pending_approval_count }} awaiting approval</span>
        @elseif($project->stack)
            <span class="ml-auto truncate max-w-[120px]">{{ $project->stack }}</span>
        @endif
    </div>
</a>