<x-app-layout>
    <x-slot name="breadcrumb">Projects</x-slot>
    <x-slot name="title">{{ $project->name }}</x-slot>

    <x-slot name="actions">
        {{-- Share toggle --}}
        <form method="POST" action="{{ route('projects.share', $project) }}">
            @csrf
            <button type="submit"
                class="text-xs px-3 py-1.5 rounded-lg border transition-colors
                       {{ $project->share_enabled
                          ? 'border-[#2a2840] text-[#a89df5] bg-[#16151f] hover:bg-[#1e1c2e]'
                          : 'border-[#1e1e22] text-[#555] hover:border-[#2a2a2e] hover:text-[#888]' }}">
                {{ $project->share_enabled ? 'Sharing on' : 'Share' }}
            </button>
        </form>

        <a href="{{ route('projects.edit', $project) }}"
           class="text-xs px-3 py-1.5 rounded-lg border border-[#1e1e22] text-[#555] hover:border-[#2a2a2e] hover:text-[#888] transition-colors">
            Edit
        </a>

        {{-- Add feature button triggers modal --}}
        <button @click="$dispatch('open-modal', 'add-feature')"
                class="flex items-center gap-1.5 bg-[#7c6af7] text-white text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-[#6a59e0] transition-colors">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Add feature
        </button>
    </x-slot>

    <div class="p-6" x-data>

        {{-- Project meta + stats --}}
        <div class="flex items-start gap-4 mb-6">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-1">
                    <span class="w-2.5 h-2.5 rounded-full" style="background:{{ $project->color }}"></span>
                    <h1 class="text-lg font-medium text-[#e8e6e0]">{{ $project->name }}</h1>
                    @if($project->client_name)
                        <span class="text-xs text-[#444]">· {{ $project->client_name }}</span>
                    @endif
                </div>
                @if($project->stack)
                    <p class="text-xs text-[#444] ml-5">{{ $project->stack }}</p>
                @endif
            </div>

            {{-- Share link copy --}}
            @if($project->share_enabled)
            <div class="flex items-center gap-2 bg-[#16151f] border border-[#2a2840] rounded-lg px-3 py-1.5">
                <span class="text-xs text-[#555] font-mono-dm truncate max-w-[180px]">
                    /share/{{ Str::limit($project->share_token, 12) }}…
                </span>
                <button onclick="navigator.clipboard.writeText('{{ route('client.show', $project->share_token) }}')"
                        class="text-xs text-[#7c6af7] hover:text-[#a89df5] flex-shrink-0">Copy</button>
            </div>
            @endif
        </div>

        {{-- Stats row --}}
        <div class="grid grid-cols-4 gap-3 mb-6">
            @php
                $total     = $features->count();
                $shipped   = $features->where('status','shipped')->count();
                $building  = $features->whereIn('status',['building','review'])->count();
                $approvals = $features->where('status','awaiting_approval')->count();
            @endphp
            <div class="bg-[#141416] border border-[#1e1e22] rounded-xl p-4">
                <p class="text-xl font-medium font-mono-dm">{{ $total }}</p>
                <p class="text-xs text-[#555] mt-0.5">features</p>
            </div>
            <div class="bg-[#141416] border border-[#1e1e22] rounded-xl p-4">
                <p class="text-xl font-medium font-mono-dm text-[#4CAF7D]">{{ $shipped }}</p>
                <p class="text-xs text-[#555] mt-0.5">shipped</p>
            </div>
            <div class="bg-[#141416] border border-[#1e1e22] rounded-xl p-4">
                <p class="text-xl font-medium font-mono-dm text-[#d4a017]">{{ $building }}</p>
                <p class="text-xs text-[#555] mt-0.5">in progress</p>
            </div>
            <div class="bg-[#141416] border border-[#1e1e22] rounded-xl p-4">
                <p class="text-xl font-medium font-mono-dm {{ $approvals > 0 ? 'text-[#d47db8]' : '' }}">{{ $approvals }}</p>
                <p class="text-xs text-[#555] mt-0.5">awaiting approval</p>
            </div>
        </div>

        {{-- Progress bar --}}
        @if($total > 0)
        <div class="mb-6">
            <div class="flex justify-between text-xs text-[#444] mb-1.5">
                <span>Progress</span>
                <span class="font-mono-dm text-[#7c6af7]">{{ $project->progress }}%</span>
            </div>
            <div class="h-1 bg-[#1e1e22] rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all duration-500" style="width:{{ $project->progress }}%; background:{{ $project->color }}"></div>
            </div>
        </div>
        @endif

        {{-- AI suggestions strip --}}
        @if($suggestions->isNotEmpty())
        <div class="mb-5 bg-[#16151f] border border-[#2a2840] rounded-xl p-4">
            <div class="flex items-center gap-2 mb-3">
                <span class="w-1.5 h-1.5 rounded-full bg-[#7c6af7] animate-pulse"></span>
                <p class="text-xs text-[#7c6af7] font-medium">AI suggestions</p>
                <span class="font-mono-dm text-[10px] text-[#555] ml-auto">{{ $suggestions->count() }} new</span>
                <form method="POST" action="{{ route('projects.suggestions.regenerate', $project) }}">
                    @csrf
                    <button class="text-[10px] text-[#444] hover:text-[#7c6af7] transition-colors">Refresh</button>
                </form>
            </div>
            <div class="space-y-2">
                @foreach($suggestions as $s)
                <div class="flex items-start gap-3 py-2 border-t border-[#1e1e22] first:border-0 first:pt-0">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-[#c8c6c0]">{{ $s->title }}</p>
                        <p class="text-[11px] text-[#555] mt-0.5">{{ $s->rationale }}</p>
                        @if($s->source_project)
                            <p class="text-[10px] text-[#7c6af7] mt-0.5">↗ from {{ $s->source_project }}</p>
                        @endif
                    </div>
                    <div class="flex gap-2 flex-shrink-0">
                        <form method="POST" action="{{ route('suggestions.accept', $project) }}">
                            @csrf
                            <input type="hidden" name="suggestion_id" value="{{ $s->id }}">
                            <button class="text-[11px] text-[#4CAF7D] hover:text-[#6ddf95] transition-colors">Add</button>
                        </form>
                        <form method="POST" action="{{ route('suggestions.dismiss', $project) }}">
                            @csrf
                            <input type="hidden" name="suggestion_id" value="{{ $s->id }}">
                            <button class="text-[11px] text-[#444] hover:text-[#888] transition-colors">Dismiss</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Feature list --}}
        @php
        $sections = [
            'awaiting_approval' => ['Awaiting approval', '#d47db8'],
            'building'          => ['Building',          '#d4a017'],
            'review'            => ['In review',         '#d47db8'],
            'approved'          => ['Approved — ready',  '#a89df5'],
            'backlog'           => ['Backlog',            '#555'],
            'shipped'           => ['Shipped',            '#4CAF7D'],
            'cancelled'         => ['Cancelled',          '#333'],
        ];
        @endphp

        @foreach($sections as $status => [$label, $color])
            @php $group = $features->where('status', $status); @endphp
            @if($group->isNotEmpty())
            <div class="mb-4">
                <p class="text-[10px] uppercase tracking-widest mb-2" style="color:{{ $color }}">{{ $label }}</p>
                <div class="space-y-1">
                    @foreach($group as $feature)
                        @include('projects._feature_row', ['feature' => $feature, 'color' => $color])
                    @endforeach
                </div>
            </div>
            @endif
        @endforeach

        @if($features->isEmpty())
        <div class="text-center py-12">
            <p class="text-sm text-[#444]">No features yet — add one to start tracking.</p>
        </div>
        @endif

    </div>

    {{-- ── Add Feature Modal ──────────────────────────────────── --}}
    <x-modal name="add-feature" focusable>
        <form method="POST" action="{{ route('features.store', $project) }}" class="p-6 bg-[#141416]">
            @csrf

            <h2 class="text-sm font-medium text-[#e8e6e0] mb-5">Add feature</h2>

            {{-- Title --}}
            <div class="mb-4">
                <label class="block text-xs text-[#555] mb-1.5">Feature title <span class="text-[#e06c6c]">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required autofocus
                       placeholder="e.g. Authentication, Settings page, Dark mode"
                       class="w-full bg-[#0e0e10] border border-[#2a2a2e] text-[#e8e6e0] text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-[#7c6af7] placeholder-[#333]">
            </div>

            {{-- Description --}}
            <div class="mb-4">
                <label class="block text-xs text-[#555] mb-1.5">Description <span class="text-[#333]">(shown to client on approval page)</span></label>
                <textarea name="description" rows="3"
                          placeholder="What this feature does and why it's needed…"
                          class="w-full bg-[#0e0e10] border border-[#2a2a2e] text-[#e8e6e0] text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-[#7c6af7] placeholder-[#333] resize-none">{{ old('description') }}</textarea>
            </div>

            {{-- Type + Priority --}}
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="block text-xs text-[#555] mb-1.5">Type</label>
                    <select name="type" class="w-full bg-[#0e0e10] border border-[#2a2a2e] text-[#888] text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-[#7c6af7]">
                        <option value="personal"      {{ old('type','personal') === 'personal'      ? 'selected' : '' }}>Personal</option>
                        <option value="client"        {{ old('type') === 'client'                   ? 'selected' : '' }}>Client</option>
                        <option value="collaborative" {{ old('type') === 'collaborative'            ? 'selected' : '' }}>Collaborative</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-[#555] mb-1.5">Priority</label>
                    <select name="priority" class="w-full bg-[#0e0e10] border border-[#2a2a2e] text-[#888] text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-[#7c6af7]">
                        <option value="low"      {{ old('priority') === 'low'                       ? 'selected' : '' }}>Low</option>
                        <option value="medium"   {{ old('priority','medium') === 'medium'           ? 'selected' : '' }}>Medium</option>
                        <option value="high"     {{ old('priority') === 'high'                      ? 'selected' : '' }}>High</option>
                        <option value="critical" {{ old('priority') === 'critical'                  ? 'selected' : '' }}>Critical</option>
                    </select>
                </div>
            </div>

            {{-- Needs approval toggle --}}
            <div class="flex items-center gap-3 mb-5 py-3 border-t border-[#1e1e22]">
                <input type="checkbox" name="needs_approval" id="needs_approval" value="1"
                       {{ old('needs_approval') ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-[#2a2a2e] bg-[#0e0e10] text-[#7c6af7] focus:ring-[#7c6af7] focus:ring-offset-0">
                <div>
                    <label for="needs_approval" class="text-xs text-[#888] cursor-pointer">Requires client approval</label>
                    <p class="text-[11px] text-[#444]">You'll be able to send an approval link before starting</p>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" x-on:click="$dispatch('close')"
                        class="flex-1 text-sm text-[#555] border border-[#1e1e22] rounded-lg py-2 hover:border-[#2a2a2e] hover:text-[#888] transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 text-sm bg-[#7c6af7] text-white rounded-lg py-2 hover:bg-[#6a59e0] transition-colors">
                    Add feature
                </button>
            </div>
        </form>
    </x-modal>

</x-app-layout>