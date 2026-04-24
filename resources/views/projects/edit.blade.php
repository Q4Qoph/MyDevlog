<x-app-layout>
    <x-slot name="breadcrumb">{{ $project->name }}</x-slot>
    <x-slot name="title">Edit project</x-slot>

    <div class="p-6 max-w-xl">
        <form method="POST" action="{{ route('projects.update', $project) }}"
              x-data="{ tags: {{ json_encode(old('tech_tags', $project->tech_tags ?? [])) }} }">
            @csrf @method('PATCH')

            {{-- Name --}}
            <div class="mb-5">
                <label class="block text-xs text-[#555] mb-1.5">Project name</label>
                <input type="text" name="name" value="{{ old('name', $project->name) }}" required
                       class="w-full bg-[#141416] border border-[#2a2a2e] text-[#e8e6e0] text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:border-[#7c6af7]">
            </div>

            {{-- Client --}}
            <div class="grid grid-cols-2 gap-3 mb-5">
                <div>
                    <label class="block text-xs text-[#555] mb-1.5">Client name</label>
                    <input type="text" name="client_name" value="{{ old('client_name', $project->client_name) }}"
                           class="w-full bg-[#141416] border border-[#2a2a2e] text-[#e8e6e0] text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:border-[#7c6af7] placeholder-[#333]">
                </div>
                <div>
                    <label class="block text-xs text-[#555] mb-1.5">Client email</label>
                    <input type="email" name="client_email" value="{{ old('client_email', $project->client_email) }}"
                           class="w-full bg-[#141416] border border-[#2a2a2e] text-[#e8e6e0] text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:border-[#7c6af7] placeholder-[#333]">
                </div>
            </div>

            {{-- Stack --}}
            <div class="mb-3">
                <label class="block text-xs text-[#555] mb-1.5">Tech stack</label>
                <input type="text" name="stack" value="{{ old('stack', $project->stack) }}"
                       class="w-full bg-[#141416] border border-[#2a2a2e] text-[#e8e6e0] text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:border-[#7c6af7]">
            </div>

            {{-- Tech tags --}}
            <div class="mb-5">
                <label class="block text-xs text-[#555] mb-1.5">Tech tags</label>
                <div class="flex flex-wrap gap-1.5 mb-2">
                    <template x-for="(tag, i) in tags" :key="i">
                        <span class="flex items-center gap-1 bg-[#16151f] border border-[#2a2840] text-[#a89df5] text-[11px] font-mono-dm px-2 py-0.5 rounded">
                            <span x-text="tag"></span>
                            <button type="button" @click="tags.splice(i,1)" class="text-[#7c6af7] hover:text-[#e06c6c] ml-0.5">×</button>
                            <input type="hidden" :name="'tech_tags[' + i + ']'" :value="tag">
                        </span>
                    </template>
                </div>
                <input type="text" placeholder="Add a tag and press Enter"
                       class="w-full bg-[#141416] border border-[#2a2a2e] text-[#e8e6e0] text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-[#7c6af7] placeholder-[#333]"
                       @keydown.enter.prevent="if($event.target.value.trim()) { tags.push($event.target.value.trim().toLowerCase()); $event.target.value = '' }"
                       @keydown.comma.prevent="if($event.target.value.trim()) { tags.push($event.target.value.replace(',','').trim().toLowerCase()); $event.target.value = '' }">
            </div>

            {{-- Brief --}}
            <div class="mb-5">
                <label class="block text-xs text-[#555] mb-1.5">Project brief</label>
                <textarea name="brief" rows="4"
                          class="w-full bg-[#141416] border border-[#2a2a2e] text-[#e8e6e0] text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:border-[#7c6af7] resize-none">{{ old('brief', $project->brief) }}</textarea>
            </div>

            {{-- Status + Color + Deadline --}}
            <div class="grid grid-cols-3 gap-3 mb-6">
                <div>
                    <label class="block text-xs text-[#555] mb-1.5">Status</label>
                    <select name="status" class="w-full bg-[#141416] border border-[#2a2a2e] text-[#888] text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:border-[#7c6af7]">
                        @foreach(['planning','active','review','shipped','archived'] as $s)
                            <option value="{{ $s }}" {{ $project->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-[#555] mb-1.5">Colour</label>
                    <input type="color" name="color" value="{{ old('color', $project->color) }}"
                           class="w-full h-10 rounded-lg border border-[#2a2a2e] bg-[#141416] cursor-pointer">
                </div>
                <div>
                    <label class="block text-xs text-[#555] mb-1.5">Deadline</label>
                    <input type="date" name="deadline" value="{{ old('deadline', $project->deadline?->format('Y-m-d')) }}"
                           class="w-full bg-[#141416] border border-[#2a2a2e] text-[#888] text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:border-[#7c6af7]">
                </div>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('projects.show', $project) }}"
                   class="flex-1 text-center text-sm text-[#555] border border-[#1e1e22] rounded-lg py-2.5 hover:border-[#2a2a2e] hover:text-[#888] transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="flex-1 text-sm bg-[#7c6af7] text-white rounded-lg py-2.5 hover:bg-[#6a59e0] transition-colors">
                    Save changes
                </button>
            </div>

            {{-- Danger zone --}}
            <div class="mt-8 pt-6 border-t border-[#1e1e22]">
                <p class="text-xs text-[#333] mb-3">Danger zone</p>
                <form method="POST" action="{{ route('projects.destroy', $project) }}"
                      onsubmit="return confirm('Delete {{ $project->name }}? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs text-[#e06c6c] hover:text-[#ff8888] transition-colors">
                        Delete project
                    </button>
                </form>
            </div>
        </form>
    </div>
</x-app-layout>