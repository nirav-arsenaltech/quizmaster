<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', config('app.name', 'QuizMaster'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center" style="background:var(--bg);">

<div class="w-full max-w-md px-6">
    {{-- Logo --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center gap-2.5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-white text-lg"
                 style="background:var(--brand);">Q</div>
            <span class="font-display text-2xl font-bold text-white">QuizMaster</span>
        </div>
    </div>

    <div class="rounded-2xl p-8" style="background:var(--card); border:1px solid var(--border);">
        @yield('content')
    </div>
</div>

</body>
</html>
