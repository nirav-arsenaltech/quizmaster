@extends('layouts.app')
@section('title', 'Results — QuizMaster')
@section('page-title', 'Your Results')
@section('page-subtitle', $quiz->title . ' · Attempt #' . $attempt->attempt_number)

@section('header-actions')
    <div class="flex gap-3">
        @if($attemptsRemaining > 0)
            <form method="POST" action="{{ route('quiz.start', $quiz) }}">
                @csrf
                <button class="px-4 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90" style="background:var(--brand);">Try Again</button>
            </form>
        @endif
        <a href="{{ route('results.index') }}" class="px-4 py-2 rounded-xl text-sm font-medium transition-colors" style="background:var(--card); border:1px solid var(--border); color:var(--text);">All Results</a>
    </div>
@endsection

@section('content')
@php
    $pct   = $attempt->scorePercent();
    $color = $attempt->score >= 8 ? '#22c55e' : ($attempt->score >= 5 ? '#f59e0b' : '#ef4444');
    $msg   = $attempt->score >= 8 ? 'Excellent!' : ($attempt->score >= 5 ? 'Good effort!' : 'Keep practising!');
@endphp

{{-- Score card --}}
<div class="rounded-2xl p-8 mb-8 text-center relative overflow-hidden" style="background:var(--card); border:1px solid var(--border);">
    <div class="absolute inset-0 pointer-events-none" style="background:radial-gradient(circle at 50% 0%, {{ $color }}18, transparent 65%);"></div>
    <p class="text-sm mb-3" style="color:var(--muted);">
        {{ $attempt->isTimedOut() ? '⏱ Timed out' : 'Completed' }}
        · {{ $attempt->submitted_at?->format('M j, Y g:ia') }}
    </p>
    <div class="font-display font-extrabold mb-2 tabular-nums" style="font-size:5rem; line-height:1; color:{{ $color }};">
        {{ $attempt->score }}<span style="font-size:2rem; color:var(--muted);">/{{ $attempt->total_questions }}</span>
    </div>
    <p class="font-display text-xl font-semibold text-white mb-6">{{ $msg }}</p>
    <div class="flex justify-center gap-8 text-sm">
        <div><div class="font-bold text-xl text-emerald-400">{{ $attempt->score }}</div><div style="color:var(--muted);">Correct</div></div>
        <div><div class="font-bold text-xl text-red-400">{{ $attempt->total_questions - $attempt->score }}</div><div style="color:var(--muted);">Wrong</div></div>
        <div><div class="font-bold text-xl text-white">{{ $attempt->timeTakenFormatted() }}</div><div style="color:var(--muted);">Time</div></div>
        <div><div class="font-bold text-xl" style="color:var(--brand);">{{ $pct }}%</div><div style="color:var(--muted);">Score</div></div>
    </div>
</div>

{{-- Answer review --}}
<h2 class="font-display font-bold text-white mb-4">Answer Review</h2>
<div class="space-y-3">
    @foreach($review as $i => $item)
        @php
            $bc = $item['is_correct'] ? 'rgba(34,197,94,.35)' : 'rgba(239,68,68,.35)';
            $bg = $item['is_correct'] ? 'rgba(34,197,94,.04)' : 'rgba(239,68,68,.04)';
            $ic = $item['is_correct'] ? '#22c55e' : '#ef4444';
        @endphp
        <div class="rounded-2xl overflow-hidden" style="background:{{ $bg }}; border:1px solid {{ $bc }};">
            <div class="flex items-start gap-3 px-5 py-4">
                <div class="w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5"
                     style="background:{{ $ic }}22;">
                    @if($item['is_correct'])
                        <svg class="w-3.5 h-3.5" style="color:{{ $ic }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    @else
                        <svg class="w-3.5 h-3.5" style="color:{{ $ic }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                    @endif
                </div>
                <div class="flex-1">
                    <p class="text-xs font-bold mb-0.5" style="color:{{ $ic }};">Q{{ $i + 1 }}</p>
                    <p class="text-sm font-medium text-white leading-snug">{{ $item['question']->body }}</p>
                </div>
            </div>
            <div class="px-5 pb-4 grid grid-cols-1 sm:grid-cols-2 gap-3" style="padding-left:2.75rem;">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide mb-1" style="color:var(--muted);">Your answer</p>
                    <p class="text-sm {{ $item['is_correct'] ? 'text-emerald-400' : 'text-red-400' }} font-medium">
                        {{ $item['chosen_answer']?->body ?? '— Skipped —' }}
                    </p>
                </div>
                @if(!$item['is_correct'])
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide mb-1" style="color:var(--muted);">Correct answer</p>
                        <p class="text-sm text-emerald-400 font-medium">{{ $item['correct_answer']?->body }}</p>
                    </div>
                @endif
                @if($item['question']->explanation)
                    <div class="sm:col-span-2 px-3 py-2 rounded-lg text-sm" style="background:rgba(255,255,255,.04);">
                        <span class="font-semibold text-white">Explanation: </span>
                        <span style="color:var(--muted);">{{ $item['question']->explanation }}</span>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>

@if($attemptsRemaining > 0)
    <div class="text-center mt-8">
        <p class="text-sm mb-4" style="color:var(--muted);">{{ $attemptsRemaining }} attempt{{ $attemptsRemaining !== 1 ? 's' : '' }} remaining</p>
        <form method="POST" action="{{ route('quiz.start', $quiz) }}" class="inline">
            @csrf
            <button type="submit" class="px-8 py-3 rounded-xl font-display font-bold text-white text-sm hover:opacity-90 transition-all" style="background:var(--brand);">Try Again →</button>
        </form>
    </div>
@endif
@endsection
