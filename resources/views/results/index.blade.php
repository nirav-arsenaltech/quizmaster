@extends('layouts.app')
@section('title', 'My Results — QuizMaster')
@section('page-title', 'My Results')
@section('page-subtitle', 'All your quiz attempts')

@section('content')
@if($attempts->isEmpty())
    <div class="flex flex-col items-center justify-center py-24 text-center">
        <p class="mb-4" style="color:var(--muted);">No completed attempts yet.</p>
        <a href="{{ route('quiz.index') }}" class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white hover:opacity-90" style="background:var(--brand);">Take a Quiz →</a>
    </div>
@else
    <div class="rounded-2xl overflow-hidden" style="background:var(--card); border:1px solid var(--border);">
        <table class="w-full text-sm">
            <thead>
                <tr style="border-bottom:1px solid var(--border);">
                    <th class="text-left px-6 py-3.5 text-xs font-semibold uppercase tracking-wider" style="color:var(--muted);">Quiz</th>
                    <th class="text-left px-4 py-3.5 text-xs font-semibold uppercase tracking-wider" style="color:var(--muted);">#</th>
                    <th class="text-left px-4 py-3.5 text-xs font-semibold uppercase tracking-wider" style="color:var(--muted);">Score</th>
                    <th class="text-left px-4 py-3.5 text-xs font-semibold uppercase tracking-wider" style="color:var(--muted);">Time</th>
                    <th class="text-left px-4 py-3.5 text-xs font-semibold uppercase tracking-wider" style="color:var(--muted);">Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($attempts as $a)
                    <tr class="hover:bg-white/2 transition-colors" style="border-bottom:1px solid var(--border);">
                        <td class="px-6 py-4 font-medium text-white">{{ $a->quiz->title }}</td>
                        <td class="px-4 py-4" style="color:var(--muted);">#{{ $a->attempt_number }}</td>
                        <td class="px-4 py-4">
                            <span class="font-display font-bold text-base {{ $a->score >= 8 ? 'text-emerald-400' : ($a->score >= 5 ? 'text-amber-400' : 'text-red-400') }}">
                                {{ $a->score }}/{{ $a->total_questions }}
                            </span>
                        </td>
                        <td class="px-4 py-4 tabular-nums" style="color:var(--muted);">{{ $a->timeTakenFormatted() }}</td>
                        <td class="px-4 py-4 text-xs" style="color:var(--muted);">{{ $a->submitted_at?->format('M j, Y') }}</td>
                        <td class="px-4 py-4">
                            <a href="{{ route('results.show', $a) }}" class="text-xs font-medium hover:text-white transition-colors" style="color:var(--brand);">Review →</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if($attempts->hasPages())
            <div class="px-6 py-4" style="border-top:1px solid var(--border);">{{ $attempts->links() }}</div>
        @endif
    </div>
@endif
@endsection
