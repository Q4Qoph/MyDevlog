<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $project->name }} — Progress</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#0e0e10] text-[#e8e6e0] min-h-screen p-6 md:p-10">

    <div class="max-w-2xl mx-auto">

        {{-- Header --}}
        <div class="mb-8">
            <p class="text-xs font-mono text-[#555] uppercase tracking-widest mb-2">dev<span class="text-[#7c6af7]">log</span> · project update</p>
            <h1 class="text-3xl font-medium">{{ $project->name }}</h1>
            @if($project->stack)
                <p class="text-sm text-[#555] mt-1">{{ $project->stack }}</p>
            @endif
        </div>

        {{-- Progress bar --}}
        @php $progress = $project->progress; @endphp
        <div class="bg-[#141416] border border-[#1e1e22] rounded-xl p-5 mb-6">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-[#888]">Overall progress</span>
                <span class="text-sm font-mono text-[#7c6af7]">{{ $progress }}%</span>
            </div>
            <div class="h-1.5 bg-[#1e1e22] rounded-full overflow-hidden">
                <div class="h-full bg-[#7c6af7] rounded-full transition-all" style="width: {{ $progress }}%"></div>
            </div>
            <div class="flex gap-6 mt-4 text-xs text-[#555]">
                <span><span class="text-[#4CAF7D]">{{ $grouped->get('shipped', collect())->count() }}</span> shipped</span>
                <span><span class="text-[#d4a017]">{{ ($grouped->get('building', collect())->count() + $grouped->get('review', collect())->count()) }}</span> in progress</span>
                <span><span class="text-[#888]">{{ $grouped->get('backlog', collect())->count() + $grouped->get('approved', collect())->count() }}</span> planned</span>
            </div>
        </div>

        {{-- Feature groups --}}
        @php
        $sections = [
            'shipped'  => ['label' => 'Shipped',      'color' => '#4CAF7D', 'bg' => '#0d2218', 'border' => '#1a3d28'],
            'review'   => ['label' => 'In review',    'color' => '#d47db8', 'bg' => '#1a1114', 'border' => '#2d1520'],
            'building' => ['label' => 'Building',     'color' => '#d4a017', 'bg' => '#1c1608', 'border' => '#332d10'],
            'approved' => ['label' => 'Approved',     'color' => '#a89df5', 'bg' => '#16151f', 'border' => '#2a2840'],
            'backlog'  => ['label' => 'Planned',      'color' => '#555',    'bg' => '#141416', 'border' => '#1e1e22'],
        ];
        @endphp

        @foreach($sections as $status => $cfg)
            @if($grouped->has($status) && $grouped->get($status)->isNotEmpty())
            <div class="mb-4">
                <p class="text-xs uppercase tracking-widest mb-2" style="color: {{ $cfg['color'] }}">{{ $cfg['label'] }}</p>
                <div class="space-y-2">
                    @foreach($grouped->get($status) as $feature)
                    <div class="flex items-center gap-3 rounded-lg px-4 py-3 border"
                         style="background: {{ $cfg['bg'] }}; border-color: {{ $cfg['border'] }}">
                        <div class="w-1.5 h-1.5 rounded-full flex-shrink-0" style="background: {{ $cfg['color'] }}"></div>
                        <span class="text-sm text-[#c8c6c0] flex-1">{{ $feature->title }}</span>
                        @if($feature->type === 'client')
                            <span class="text-[10px] text-[#444] font-mono">client</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        @endforeach

        <p class="text-xs text-[#333] text-center mt-10">Last updated {{ $project->updated_at->diffForHumans() }}</p>
    </div>

</body>
</html>
