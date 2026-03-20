@extends('layouts.app')
@section('title', 'Questions — Admin')
@section('page-title', $quiz->title)
@section('page-subtitle', $questions->count() . ' questions · ' . ($quiz->is_published ? 'Published' : 'Draft'))
@section('header-actions')
    <div class="flex gap-3">
        <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="px-4 py-2 rounded-xl text-sm font-medium" style="background:var(--card); border:1px solid var(--border); color:var(--text);">Quiz Settings</a>
        <a href="{{ route('admin.quizzes.questions.create', $quiz) }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90" style="background:var(--brand);">+ Add Question</a>
    </div>
@endsection

@section('content')
@if($questions->isEmpty())
    <div class="flex flex-col items-center justify-center py-20 text-center">
        <p class="mb-4" style="color:var(--muted);">No questions yet.</p>
        <a href="{{ route('admin.quizzes.questions.create', $quiz) }}" class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white hover:opacity-90" style="background:var(--brand);">Add First Question →</a>
    </div>
@else
    <div class="space-y-3">
        @foreach($questions as $i => $question)
            <div class="rounded-2xl overflow-hidden" style="background:var(--card); border:1px solid var(--border);">
                <div class="flex items-start gap-4 px-5 py-4">
                    <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0"
                          style="background:rgba(124,92,252,.12); color:var(--brand);">{{ $i + 1 }}</span>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-white leading-snug mb-2">{{ $question->body }}</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($question->answers as $a)
                                <span class="text-xs px-2.5 py-1 rounded-lg {{ $a->is_correct ? 'text-emerald-300' : 'text-gray-400' }}"
                                      style="{{ $a->is_correct ? 'background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.25);' : 'background:var(--bg);border:1px solid var(--border);' }}">
                                    {{ $a->is_correct ? '✓ ' : '' }}{{ $a->body }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex gap-3 flex-shrink-0">
                        <a href="{{ route('admin.quizzes.questions.edit', [$quiz, $question]) }}" class="text-xs font-medium hover:text-white" style="color:var(--muted);">Edit</a>
                        <form method="POST" action="{{ route('admin.quizzes.questions.destroy', [$quiz, $question]) }}" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs font-medium text-red-400 hover:text-red-300">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
