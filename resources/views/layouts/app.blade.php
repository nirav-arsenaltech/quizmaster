<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', config('app.name', 'QuizMaster'))</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="h-full flex overflow-hidden" style="background:var(--bg);">

{{-- ═══════════════ SIDEBAR ═══════════════ --}}
<aside class="w-60 flex-shrink-0 flex flex-col h-full overflow-y-auto"
       style="background:var(--panel); border-right:1px solid var(--border);">

    {{-- Logo --}}
    <div class="px-5 py-5" style="border-bottom:1px solid var(--border);">
        <a href="{{ route('dashboard') }}"
           class="font-display flex items-center gap-2.5 text-gray font-bold text-lg">
            <div class="w-8 h-8 rounded-xl flex items-center justify-center text-sm font-black"
                 style="background:var(--brand);">Q</div>
            QuizMaster
        </a>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-3 py-4 space-y-0.5" role="navigation" aria-label="Main navigation">
        @php
            $nav = [
                ['route' => 'dashboard',     'label' => 'Dashboard',   'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                ['route' => 'quiz.index',    'label' => 'Take Quiz',   'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 8h4m-2-2v4'],
                ['route' => 'results.index', 'label' => 'My Results',  'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                ['route' => 'leaderboard',   'label' => 'Leaderboard', 'icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'],
            ];
        @endphp

        @foreach ($nav as $item)
            @php $active = request()->routeIs($item['route']); @endphp
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ $active ? 'nav-link-active' : 'hover:bg-white/5' }}"
               style="color: {{ $active ? '#fff' : 'var(--muted)' }};">
                <svg style="width:17px;height:17px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $item['icon'] }}" />
                </svg>
                {{ $item['label'] }}
            </a>
        @endforeach

        @if(auth()->user()?->isAdmin())
            <div class="pt-5 pb-1.5 px-3">
                <p class="text-xs uppercase tracking-widest font-semibold" style="color:var(--muted);">Admin</p>
            </div>
            <a href="{{ route('admin.quizzes.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->is('admin*') ? 'nav-link-active' : 'hover:bg-white/5' }}"
               style="color: {{ request()->is('admin*') ? '#fff' : 'var(--muted)' }};">
                <svg style="width:17px;height:17px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Manage Quizzes
            </a>
        @endif
    </nav>

    {{-- User footer --}}
    <div class="px-4 py-4" style="border-top:1px solid var(--border);">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                 style="background:var(--brand);">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs truncate" style="color:var(--muted);">{{ auth()->user()->email }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full text-left text-xs px-2 py-1.5 rounded-lg transition-colors hover:bg-white/5"
                    style="color:var(--muted);">
                Sign out →
            </button>
        </form>
    </div>
</aside>

{{-- ═══════════════ MAIN ═══════════════ --}}
<main class="flex-1 flex flex-col min-h-screen overflow-y-auto" style="background:var(--bg);">

    {{-- Top bar --}}
    <header class="flex-shrink-0 flex items-center justify-between px-8 py-5"
            style="border-bottom:1px solid var(--border);">
        <div>
            <h1 class="font-display text-xl font-bold text-white">
                @yield('page-title', 'Dashboard')
            </h1>
            @hasSection('page-subtitle')
                <p class="text-sm mt-0.5" style="color:var(--muted);">@yield('page-subtitle')</p>
            @endif
        </div>
        @yield('header-actions')
    </header>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mx-8 mt-5 flex items-center gap-2 px-4 py-3 rounded-xl text-sm font-medium text-emerald-300"
             style="background:rgba(34,197,94,.1); border:1px solid rgba(34,197,94,.25);">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mx-8 mt-5 flex items-center gap-2 px-4 py-3 rounded-xl text-sm font-medium text-red-300"
             style="background:rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.25);">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="flex-1 px-8 py-6">
        @yield('content')
    </div>
</main>

@stack('scripts')
</body>
</html>
