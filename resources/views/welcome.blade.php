<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DevLog</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>body { font-family: 'DM Sans', system-ui, sans-serif; }</style>
</head>
<body class="bg-[#0e0e10] text-[#e8e6e0] min-h-screen flex items-center justify-center">
    <div class="text-center">
        <p class="text-2xl font-medium mb-1">dev<span class="text-[#7c6af7]">log</span></p>
        <p class="text-sm text-[#444] mb-8">Track what you build. Ship faster.</p>
        <div class="flex gap-3 justify-center">
            @auth
                <a href="{{ route('dashboard') }}" class="bg-[#7c6af7] text-white text-sm px-5 py-2 rounded-lg hover:bg-[#6a59e0] transition-colors">Go to dashboard</a>
            @else
                <a href="{{ route('login') }}" class="text-sm text-[#555] border border-[#1e1e22] px-5 py-2 rounded-lg hover:border-[#2a2a2e] hover:text-[#888] transition-colors">Sign in</a>
                <a href="{{ route('register') }}" class="bg-[#7c6af7] text-white text-sm px-5 py-2 rounded-lg hover:bg-[#6a59e0] transition-colors">Get started</a>
            @endauth
        </div>
    </div>
</body>
</html>