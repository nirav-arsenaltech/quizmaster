@extends('layouts.guest')
@section('title', 'Sign In — QuizMaster')

@section('content')
<h2 class="font-display text-xl font-bold text-white mb-1">Welcome back</h2>
<p class="text-sm mb-6" style="color:var(--muted);">Sign in to your account to continue.</p>

@if ($errors->any())
    <div class="mb-4 px-4 py-3 rounded-xl text-sm text-red-300" style="background:rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.25);">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('login') }}" class="space-y-4">
    @csrf

    <div>
        <label class="block text-sm font-medium text-white mb-1.5" for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
               class="w-full px-4 py-2.5 rounded-xl text-sm text-white outline-none focus:ring-2 focus:ring-purple-500 transition-all"
               style="background:var(--bg); border:1px solid var(--border);"
               placeholder="you@example.com" />
    </div>

    <div>
        <label class="block text-sm font-medium text-white mb-1.5" for="password">Password</label>
        <input id="password" type="password" name="password" required autocomplete="current-password"
               class="w-full px-4 py-2.5 rounded-xl text-sm text-white outline-none focus:ring-2 focus:ring-purple-500 transition-all"
               style="background:var(--bg); border:1px solid var(--border);"
               placeholder="••••••••" />
    </div>

    <div class="flex items-center justify-between">
        <label class="flex items-center gap-2 text-sm cursor-pointer" style="color:var(--muted);">
            <input type="checkbox" name="remember" class="rounded" style="accent-color:var(--brand);" />
            Remember me
        </label>
    </div>

    <button type="submit"
            class="w-full py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90"
            style="background:var(--brand);">
        Sign In
    </button>
</form>

<p class="text-center text-sm mt-6" style="color:var(--muted);">
    Don't have an account?
    <a href="{{ route('register') }}" class="font-medium hover:text-white transition-colors" style="color:var(--brand);">Register</a>
</p>

<div class="mt-6 pt-5 text-xs text-center rounded-xl p-3" style="border-top:1px solid var(--border); color:var(--muted);">
    <p class="font-semibold mb-1" style="color:var(--text);">Demo Accounts</p>
    <p>Admin: admin@quizmaster.test / password</p>
    <p>User: user@quizmaster.test / password</p>
</div>
@endsection
