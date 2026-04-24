<x-app-layout>
    <x-slot name="breadcrumb">Projects</x-slot>
    <x-slot name="title">New project</x-slot>

    <div class="p-6 max-w-xl">
        <form method="POST" action="{{ route('projects.store') }}" x-data="{ tags: {{ json_encode(old('tech_tags', [])) }} }">
            @csrf

            {{-- Name --}}
            <div class="mb-5">
                <label class="block text-xs text-[#555] mb-1.5">Project name <span class="text-[#e06c6c]">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                       placeholder="Pweza Delivery, Africa Auto Parts…"
                       class="w-full bg-[#141416] border border-[#2a2a2e] text-[#e8e6e0] text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:border-[#7c6af7] placeholder-[#333]">
            </div>

            {{-- Client --}}
            <div class="grid grid-cols-2 gap-3 mb-5">
                <div>
                    <label class="block text-xs text-[#555] mb-1.5">Client name</label>
                    <input type="text" name="client_name" value="{{ old('client_name') }}"
                           placeholder="Acme Ltd"
                           class="w-full bg-[#141416] border border-[#2a2a2e] text-[#e8e6e0] text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:border-[#7c6af7] placeholder-[#333]">
                </div>
                <div>
                    <label class="block text-xs text-[#555] mb-1.5">Client email <span class="text-[#333]">(for approval emails)</span></label>
                    <input type="email" name="client_email" value="{{ old('client_email') }}"
                           placeholder="client@company.com"
                           class="w-full bg-[#141416] border border-[#2a2a2e] text-[#e8e6e0] text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:border-[#7c6af7] placeholder-[#333]">
                </div>
            </div>

            {{-- Stack --}}
            <div class="mb-3">
                <label class="block text-xs text-[#555] mb-1.5">Tech stack</label>
                <input type="text" name="stack" value="{{ old('stack') }}"
                       placeholder="React, TypeScript, Laravel, MySQL"
                       class="w-full bg-[#141416] border border-[#2a2a2e] text-[#e8e6e0] text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:border-[#7c6af7] placeholder-[#333]">
            </div>

            {{-- Tech tags (for AI matching) --}}
            <div class="mb-5">
                <label class="block text-xs text-[#555] mb-1.5">
                    Tech tags
                    <span class="text-[#333]">— used by AI to match past projects</span>
                </label>
                <div class="flex flex-wrap gap-1.5 mb-2">
                    <template x-for="(tag, i) in tags" :key="i">
                        <span class="flex items-center gap-1 bg-[#16151f] border border-[#2a2840] text-[#a89df5] text-[11px] font-mono-dm px-2 py-0.5 rounded">
                            <span x-text="tag"></span>
                            <button type="button" @click="tags.splice(i,1)" class="text-[#7c6af7] hover:text-[#e06c6c] ml-0.5">×</button>
                            <input type="hidden" :name="'tech_tags[' + i + ']'" :value="tag">
                        </span>
                    </template>
                </div>
                <input type="text" placeholder="Type a tag and press Enter (e.g. react, laravel, expo)"
                       class="w-full bg-[#141416] border border-[#2a2a2e] text-[#e8e6e0] text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-[#7c6af7] placeholder-[#333]"
                       @keydown.enter.prevent="if($event.target.value.trim()) { tags.push($event.target.value.trim().toLowerCase()); $event.target.value = '' }"
                       @keydown.comma.prevent="if($event.target.value.trim()) { tags.push($event.target.value.replace(',','').trim().toLowerCase()); $event.target.value = '' }">
                <p class="text-[11px] text-[#333] mt-1">Press Enter or comma to add each tag</p>
            </div>

            {{-- Project brief (AI context) --}}
            <div class="mb-5">
                <label class="block text-xs text-[#555] mb-1.5">
                    Project brief
                    <span class="text-[#555]">— the AI reads this to suggest features</span>
                </label>
                <textarea name="brief" rows="4"
                          placeholder="What does this app do? Who uses it? Any specific goals or constraints?&#10;&#10;e.g. A delivery platform for small restaurants in Nairobi. Drivers use a mobile app (Expo), restaurant owners use an admin web panel. Key goal is real-time order tracking."
                          class="w-full bg-[#141416] border border-[#2a2a2e] text-[#e8e6e0] text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:border-[#7c6af7] placeholder-[#333] resize-none">{{ old('brief') }}</textarea>
                <p class="text-[11px] text-[#444] mt-1">The more context you give, the better the AI suggestions</p>
            </div>

            {{-- Color + Deadline --}}
            <div class="grid grid-cols-2 gap-3 mb-6">
                <div>
                    <label class="block text-xs text-[#555] mb-1.5">Project colour</label>
                    <div class="flex items-center gap-2">
                        <input type="color" name="color" value="{{ old('color', '#7c6af7') }}"
                               class="w-8 h-8 rounded-lg border border-[#2a2a2e] bg-[#141416] cursor-pointer">
                        <span class="text-xs text-[#444]">Sidebar dot colour</span>
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-[#555] mb-1.5">Deadline</label>
                    <input type="date" name="deadline" value="{{ old('deadline') }}"
                           class="w-full bg-[#141416] border border-[#2a2a2e] text-[#888] text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:border-[#7c6af7]">
                </div>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('dashboard') }}"
                   class="flex-1 text-center text-sm text-[#555] border border-[#1e1e22] rounded-lg py-2.5 hover:border-[#2a2a2e] hover:text-[#888] transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="flex-1 text-sm bg-[#7c6af7] text-white rounded-lg py-2.5 hover:bg-[#6a59e0] transition-colors">
                    Create project
                </button>
            </div>
        </form>
    </div>
</x-app-layout>