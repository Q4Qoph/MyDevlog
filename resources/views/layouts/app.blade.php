{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' — DevLog' : 'DevLog' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'DM Sans', system-ui, sans-serif; }
        .font-mono-dm { font-family: 'DM Mono', monospace; }
        /* Scrollbar */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: #0e0e10; }
        ::-webkit-scrollbar-thumb { background: #2a2a2e; border-radius: 2px; }
        /* Flash animation for new items */
        @keyframes flash-in { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: none; } }
        .flash-in { animation: flash-in .25s ease-out; }
    </style>
</head>
<body class="bg-[#0e0e10] text-[#e8e6e0] antialiased" x-data>

<div class="flex h-screen overflow-hidden">

    {{-- ── Sidebar ──────────────────────────────────────────────── --}}
    <aside class="w-52 flex-shrink-0 border-r border-[#1e1e22] flex flex-col">

        {{-- Logo --}}
        <div class="h-12 flex items-center px-5 border-b border-[#1e1e22]">
            <a href="{{ route('dashboard') }}" class="text-sm font-medium tracking-widest">
                dev<span class="text-[#7c6af7]">log</span>
            </a>
        </div>

        {{-- Projects --}}
        <nav class="flex-1 overflow-y-auto py-4 px-3">
            <p class="px-2 mb-2 text-[10px] uppercase tracking-widest text-[#3a3a3e]">Projects</p>

            @php $projects = auth()->user()->projects()->active()->orderBy('updated_at','desc')->get(); @endphp

            @foreach($projects as $p)
            <a href="{{ route('projects.show', $p) }}"
               class="flex items-center gap-2 px-2 py-1.5 rounded-md text-xs mb-0.5 group
                      {{ request()->route('project')?->id === $p->id
                         ? 'bg-[#16151f] text-[#a89df5]'
                         : 'text-[#666] hover:bg-[#1a1a1e] hover:text-[#e8e6e0]' }}">
                <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" style="background:{{ $p->color }}"></span>
                <span class="flex-1 truncate">{{ $p->name }}</span>
                @php $pending = $p->pending_approval_count; @endphp
                @if($pending > 0)
                    <span class="font-mono-dm text-[10px] text-[#d47db8] bg-[#1a1114] border border-[#2d1520] px-1 rounded">{{ $pending }}</span>
                @else
                    <span class="font-mono-dm text-[10px] text-[#3a3a3e] group-hover:text-[#555]">
                        {{ $p->features()->count() }}
                    </span>
                @endif
            </a>
            @endforeach

            <a href="{{ route('projects.create') }}"
               class="flex items-center gap-2 px-2 py-1.5 rounded-md text-xs text-[#444] hover:text-[#888] mt-1">
                <span class="w-1.5 h-1.5 rounded-full border border-[#333]"></span>
                New project
            </a>

            {{-- Archived --}}
            @php $archived = auth()->user()->projects()->archived()->count(); @endphp
            @if($archived > 0)
            <div class="mt-5">
                <p class="px-2 mb-2 text-[10px] uppercase tracking-widest text-[#3a3a3e]">Archive</p>
                <a href="{{ route('projects.index') }}?status=archived"
                   class="flex items-center gap-2 px-2 py-1.5 rounded-md text-xs text-[#444] hover:text-[#888]">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#333]"></span>
                    {{ $archived }} shipped
                </a>
            </div>
            @endif
        </nav>

        {{-- User footer --}}
        <div class="border-t border-[#1e1e22] p-3">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-[#7c6af7] flex items-center justify-center text-[11px] font-medium text-white flex-shrink-0">
                    {{ auth()->user()->initials() }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-[#888] truncate">{{ auth()->user()->name }}</p>
                </div>
                <a href="{{ route('profile.edit') }}" class="text-[#444] hover:text-[#888]">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/>
                        <path d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                    </svg>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-[#444] hover:text-[#888]">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ── Main content ─────────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Topbar --}}
        <header class="h-12 flex items-center justify-between px-6 border-b border-[#1e1e22] flex-shrink-0">
            <div class="flex items-center gap-2">
                @isset($breadcrumb)
                    <span class="text-xs text-[#444]">{{ $breadcrumb }}</span>
                @endisset
                @isset($title)
                    @isset($breadcrumb)<span class="text-[#2a2a2e]">/</span>@endisset
                    <span class="text-xs text-[#888]">{{ $title }}</span>
                @endisset
            </div>
            <div class="flex items-center gap-3">
                @isset($actions)
                    {{ $actions }}
                @endisset
            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('success'))
        <div class="flash-in mx-6 mt-4 flex items-center gap-2 bg-[#0d2218] border border-[#1a3d28] text-[#4CAF7D] text-xs px-4 py-2.5 rounded-lg" x-data x-init="setTimeout(() => $el.remove(), 4000)">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
            {{ session('success') }}
        </div>
        @endif

        @if(session('approval_url'))
        <div class="flash-in mx-6 mt-3 flex items-center justify-between bg-[#16151f] border border-[#2a2840] text-[#a89df5] text-xs px-4 py-2.5 rounded-lg">
            <span>Approval link ready — copy and send to your client:</span>
            <code class="font-mono-dm text-[#7c6af7] ml-3 truncate max-w-xs">{{ session('approval_url') }}</code>
            <button onclick="navigator.clipboard.writeText('{{ session('approval_url') }}')" class="ml-3 text-[#7c6af7] hover:text-[#a89df5] flex-shrink-0">Copy</button>
        </div>
        @endif

        @foreach($errors->all() as $error)
        <div class="flash-in mx-6 mt-3 bg-[#1f0e0e] border border-[#3d1a1a] text-[#e06c6c] text-xs px-4 py-2.5 rounded-lg">
            {{ $error }}
        </div>
        @endforeach

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto">
            {{ $slot }}
        </main>
    </div>
</div>

{{-- Alpine.js for dropdowns/modals --}}
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>