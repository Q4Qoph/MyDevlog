<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <x-slot name="actions">
        <a href="{{ route('projects.create') }}"
           class="flex items-center gap-1.5 bg-[#7c6af7] text-white text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-[#6a59e0] transition-colors">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            New project
        </a>
    </x-slot>

    <div class="p-6">

        {{-- Top stats --}}
        <div class="grid grid-cols-4 gap-3 mb-8">
            @php
                $allProjects  = $projects;
                $activeCount  = $allProjects->whereIn('status', ['planning','active','review'])->count();
                $totalFeatures = $allProjects->sum('features_count');
                $shippedFeatures = $allProjects->sum('shipped_count');
                $pendingApprovals = $allProjects->sum(fn($p) => $p->pending_approval_count);
            @endphp

            <div class="bg-[#141416] border border-[#1e1e22] rounded-xl p-4">
                <p class="text-2xl font-medium font-mono-dm text-[#e8e6e0]">{{ $activeCount }}</p>
                <p class="text-xs text-[#555] mt-1">active projects</p>
            </div>
            <div class="bg-[#141416] border border-[#1e1e22] rounded-xl p-4">
                <p class="text-2xl font-medium font-mono-dm text-[#e8e6e0]">{{ $totalFeatures }}</p>
                <p class="text-xs text-[#555] mt-1">total features</p>
            </div>
            <div class="bg-[#141416] border border-[#1e1e22] rounded-xl p-4">
                <p class="text-2xl font-medium font-mono-dm text-[#4CAF7D]">{{ $shippedFeatures }}</p>
                <p class="text-xs text-[#555] mt-1">features shipped</p>
            </div>
            <div class="bg-[#141416] border border-[#1e1e22] rounded-xl p-4">
                <p class="text-2xl font-medium font-mono-dm {{ $pendingApprovals > 0 ? 'text-[#d47db8]' : 'text-[#e8e6e0]' }}">{{ $pendingApprovals }}</p>
                <p class="text-xs text-[#555] mt-1">awaiting approval</p>
            </div>
        </div>

        {{-- Active projects --}}
        @php $active = $projects->whereIn('status', ['planning','active','review']); @endphp
        @if($active->isNotEmpty())
        <div class="mb-8">
            <p class="text-xs uppercase tracking-widest text-[#3a3a3e] mb-3">Active</p>
            <div class="grid grid-cols-2 gap-3">
                @foreach($active as $project)
                    @include('dashboard._project_card', ['project' => $project])
                @endforeach
            </div>
        </div>
        @endif

        {{-- Archived projects --}}
        @php $archived = $projects->whereIn('status', ['shipped','archived']); @endphp
        @if($archived->isNotEmpty())
        <div>
            <p class="text-xs uppercase tracking-widest text-[#3a3a3e] mb-3">Shipped</p>
            <div class="grid grid-cols-2 gap-3 opacity-60">
                @foreach($archived as $project)
                    @include('dashboard._project_card', ['project' => $project])
                @endforeach
            </div>
        </div>
        @endif

        {{-- Empty state --}}
        @if($projects->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-12 h-12 rounded-xl bg-[#16151f] border border-[#2a2840] flex items-center justify-center mb-4">
                <svg width="20" height="20" fill="none" stroke="#7c6af7" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z"/>
                </svg>
            </div>
            <p class="text-sm text-[#888] mb-1">No projects yet</p>
            <p class="text-xs text-[#444] mb-5">Create your first project and start tracking features</p>
            <a href="{{ route('projects.create') }}"
               class="bg-[#7c6af7] text-white text-xs font-medium px-4 py-2 rounded-lg hover:bg-[#6a59e0] transition-colors">
                Create project
            </a>
        </div>
        @endif

    </div>
</x-app-layout>