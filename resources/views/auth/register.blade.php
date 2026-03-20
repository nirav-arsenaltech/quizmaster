@extends('layouts.guest')
@section('title', 'Register — QuizMaster')

@section('content')
<h2 class="font-display text-xl font-bold text-white mb-1">Create an account</h2>
<p class="text-sm mb-6" style="color:var(--muted);">Join and start testing your knowledge.</p>

@if ($errors->any())
    <div class="mb-4 px-4 py-3 rounded-xl text-sm text-red-300" style="background:rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.25);">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('register') }}" class="space-y-4">
    @csrf

    <div>
        <label class="block text-sm font-medium text-white mb-1.5" for="name">Name</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
               class="w-full px-4 py-2.5 rounded-xl text-sm text-white outline-none focus:ring-2 focus:ring-purple-500 transition-all"
               style="background:var(--bg); border:1px solid var(--border);"
               placeholder="John Doe" />
    </div>

    <div>
        <label class="block text-sm font-medium text-white mb-1.5" for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
               class="w-full px-4 py-2.5 rounded-xl text-sm text-white outline-none focus:ring-2 focus:ring-purple-500 transition-all"
               style="background:var(--bg); border:1px solid var(--border);"
               placeholder="you@example.com" />
    </div>

    <div>
        <label class="block text-sm font-medium text-white mb-1.5" for="password">Password</label>
        <input id="password" type="password" name="password" required autocomplete="new-password"
               class="w-full px-4 py-2.5 rounded-xl text-sm text-white outline-none focus:ring-2 focus:ring-purple-500 transition-all"
               style="background:var(--bg); border:1px solid var(--border);"
               placeholder="Min. 8 characters" />
    </div>

    <div>
        <label class="block text-sm font-medium text-white mb-1.5" for="password_confirmation">Confirm Password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
               class="w-full px-4 py-2.5 rounded-xl text-sm text-white outline-none focus:ring-2 focus:ring-purple-500 transition-all"
               style="background:var(--bg); border:1px solid var(--border);"
               placeholder="••••••••" />
    </div>

    <button type="submit"
            class="w-full py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90"
            style="background:var(--brand);">
        Create Account
    </button>
</form>

<p class="text-center text-sm mt-6" style="color:var(--muted);">
    Already have an account?
    <a href="{{ route('login') }}" class="font-medium hover:text-white transition-colors" style="color:var(--brand);">Sign in</a>
</p>
@endsection
