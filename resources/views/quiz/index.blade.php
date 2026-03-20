@extends('layouts.app')
@section('title', 'Quizzes — QuizMaster')
@section('page-title', 'Available Quizzes')
@section('page-subtitle', 'Choose a quiz to test your knowledge')

@section('content')
@if($quizzes->isEmpty())
    <div class="flex flex-col items-center justify-center py-24 text-center">
        <p style="color:var(--muted);">No quizzes are published yet.</p>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @foreach($quizzes as $quiz)
            @php
                $user      = auth()->user();
                $used      = $user->completedAttemptsForQuiz($quiz->id);
                $remaining = max(0, $quiz->max_attempts - $used);
                $best      = $user->bestScoreForQuiz($quiz->id);
            @endphp
            <div class="rounded-2xl overflow-hidden" style="background:var(--card); border:1px solid var(--border);">
                <div class="h-1" style="background:linear-gradient(90deg,var(--brand),var(--brand2));"></div>
                <div class="p-6">
                    <div class="flex items-start justify-between mb-2">
                        <h2 class="font-display text-lg font-bold text-white">{{ $quiz->title }}</h2>
                        @if($quiz->hasTimeLimit())
                            <span class="text-xs px-2 py-1 rounded-lg ml-3 flex-shrink-0" style="background:rgba(124,92,252,.12); color:var(--brand);">
                                ⏱ {{ $quiz->timeLimitFormatted() }}
                            </span>
                        @endif
                    </div>
                    @if($quiz->description)
                        <p class="text-sm mb-4 leading-relaxed" style="color:var(--muted);">{{ $quiz->description }}</p>
                    @endif
                    <div class="flex flex-wrap gap-3 text-xs mb-5" style="color:var(--muted);">
                        <span>📋 {{ $quiz->questions_per_attempt }} questions</span>
                        <span>🔄 {{ $remaining }}/{{ $quiz->max_attempts }} attempts left</span>
                        @if($best)
                            <span>🏆 Best: <strong class="text-white">{{ $best->score }}/{{ $best->total_questions }}</strong></span>
                        @endif
                    </div>
                    @if($remaining > 0)
                        <form method="POST" action="{{ route('quiz.start', $quiz) }}">
                            @csrf
                            <button type="submit" class="w-full py-2.5 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-all" style="background:var(--brand);">
                                {{ $used > 0 ? 'Try Again →' : 'Start Quiz →' }}
                            </button>
                        </form>
                    @else
                        <div class="w-full py-2.5 rounded-xl text-sm font-semibold text-center" style="background:var(--border); color:var(--muted);">All attempts used</div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
