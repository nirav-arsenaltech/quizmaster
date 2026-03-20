@extends('layouts.app')
@section('title', 'Manage Quizzes — Admin')
@section('page-title', 'Manage Quizzes')
@section('page-subtitle', 'Create, edit and publish quizzes')
@section('header-actions')
    <a href="{{ route('admin.quizzes.create') }}" class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90" style="background:var(--brand);">
        + New Quiz
    </a>
@endsection

@section('content')
<div class="rounded-2xl overflow-hidden" style="background:var(--card); border:1px solid var(--border);">
    @if($quizzes->isEmpty())
        <div class="py-16 text-center" style="color:var(--muted);">No quizzes yet.</div>
    @else
        <table class="w-full text-sm">
            <thead>
                <tr style="border-bottom:1px solid var(--border);">
                    <th class="text-left px-6 py-3.5 text-xs font-semibold uppercase tracking-wider" style="color:var(--muted);">Title</th>
                    <th class="text-left px-4 py-3.5 text-xs font-semibold uppercase tracking-wider" style="color:var(--muted);">Questions</th>
                    <th class="text-left px-4 py-3.5 text-xs font-semibold uppercase tracking-wider" style="color:var(--muted);">Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($quizzes as $quiz)
                    <tr class="{{ $quiz->trashed() ? 'opacity-50' : '' }} hover:bg-white/2 transition-colors" style="border-bottom:1px solid var(--border);">
                        <td class="px-6 py-4">
                            <p class="font-medium text-white">{{ $quiz->title }}</p>
                            <p class="text-xs truncate max-w-xs" style="color:var(--muted);">{{ $quiz->timeLimitFormatted() }} · max {{ $quiz->max_attempts }} attempts</p>
                        </td>
                        <td class="px-4 py-4" style="color:var(--muted);">{{ $quiz->questions_count }}</td>
                        <td class="px-4 py-4">
                            @if($quiz->trashed())
                                <span class="text-xs px-2 py-1 rounded-lg" style="background:rgba(239,68,68,.1);color:#ef4444;">Archived</span>
                            @elseif($quiz->is_published)
                                <span class="text-xs px-2 py-1 rounded-lg" style="background:rgba(34,197,94,.1);color:#22c55e;">Published</span>
                            @else
                                <span class="text-xs px-2 py-1 rounded-lg" style="background:rgba(85,85,110,.2);color:var(--muted);">Draft</span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3 justify-end">
                                @if($quiz->trashed())
                                    <form method="POST" action="{{ route('admin.quizzes.restore', $quiz->id) }}">
                                        @csrf
                                        <button type="submit" class="text-xs font-medium hover:text-white transition-colors" style="color:var(--muted);">Restore</button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.quizzes.questions.index', $quiz) }}" class="text-xs font-medium transition-colors" style="color:var(--brand);">Questions</a>
                                    <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="text-xs font-medium hover:text-white transition-colors" style="color:var(--muted);">Edit</a>
                                    <form method="POST" action="{{ route('admin.quizzes.destroy', $quiz) }}" onsubmit="return confirm('Archive this quiz?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs font-medium text-red-400 hover:text-red-300 transition-colors">Archive</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if($quizzes->hasPages())
            <div class="px-6 py-4" style="border-top:1px solid var(--border);">{{ $quizzes->links() }}</div>
        @endif
    @endif
</div>
@endsection
