@extends('layouts.app')
@section('title', ($quiz->exists ? 'Edit' : 'Create') . ' Quiz — Admin')
@section('page-title', $quiz->exists ? 'Edit Quiz' : 'Create Quiz')

@section('content')
@if($errors->any())
    <div class="mb-5 px-4 py-3 rounded-xl text-sm" style="background:rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.25);">
        <p class="font-semibold text-red-400 mb-1">Please fix:</p>
        @foreach($errors->all() as $e)
            <p class="text-red-300">{{ $e }}</p>
        @endforeach
    </div>
@endif

<form method="POST"
      action="{{ $quiz->exists ? route('admin.quizzes.update', $quiz) : route('admin.quizzes.store') }}"
      class="max-w-2xl">
    @csrf
    @if($quiz->exists) @method('PUT') @endif

    <div class="rounded-2xl p-6 space-y-5 mb-5" style="background:var(--card); border:1px solid var(--border);">
        <div>
            <label class="block text-sm font-medium text-white mb-1.5">Title <span class="text-red-400">*</span></label>
            <input type="text" name="title" value="{{ old('title', $quiz->title) }}" required
                   class="w-full px-4 py-2.5 rounded-xl text-sm text-white outline-none focus:ring-2 focus:ring-purple-500"
                   style="background:var(--bg); border:1px solid var(--border);"
                   placeholder="e.g. General Knowledge" />
        </div>
        <div>
            <label class="block text-sm font-medium text-white mb-1.5">Description</label>
            <textarea name="description" rows="2"
                      class="w-full px-4 py-2.5 rounded-xl text-sm text-white outline-none resize-none"
                      style="background:var(--bg); border:1px solid var(--border);">{{ old('description', $quiz->description) }}</textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-white mb-1.5">Time Limit (seconds)</label>
                <input type="number" name="time_limit" min="0" max="7200" value="{{ old('time_limit', $quiz->time_limit ?? 300) }}"
                       class="w-full px-4 py-2.5 rounded-xl text-sm text-white outline-none"
                       style="background:var(--bg); border:1px solid var(--border);" />
                <p class="text-xs mt-1" style="color:var(--muted);">0 = no limit</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-white mb-1.5">Questions per Attempt</label>
                <input type="number" name="questions_per_attempt" min="1" max="100" value="{{ old('questions_per_attempt', $quiz->questions_per_attempt ?? 10) }}"
                       class="w-full px-4 py-2.5 rounded-xl text-sm text-white outline-none"
                       style="background:var(--bg); border:1px solid var(--border);" />
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-white mb-1.5">Max Attempts per User</label>
            <input type="number" name="max_attempts" min="1" max="10" value="{{ old('max_attempts', $quiz->max_attempts ?? 3) }}"
                   class="w-full px-4 py-2.5 rounded-xl text-sm text-white outline-none max-w-xs"
                   style="background:var(--bg); border:1px solid var(--border);" />
        </div>
        <div class="space-y-3 pt-2">
            @foreach([
                ['randomize_questions', 'Randomise question order'],
                ['randomize_answers',   'Randomise answer order'],
                ['is_published',        'Publish (visible to users)'],
            ] as [$f, $l])
                <label class="flex items-center gap-3 cursor-pointer">
                    <div class="relative">
                        <input type="checkbox" name="{{ $f }}" value="1" class="sr-only peer"
                               {{ old($f, $quiz->$f ?? false) ? 'checked' : '' }} />
                        <div class="w-10 h-5 rounded-full transition-colors peer-checked:bg-purple-600" style="background:var(--border);"></div>
                        <div class="absolute top-0.5 left-0.5 w-4 h-4 rounded-full bg-white transition-transform peer-checked:translate-x-5"
                             style="box-shadow:0 1px 3px rgba(0,0,0,.4);"></div>
                    </div>
                    <span class="text-sm text-white">{{ $l }}</span>
                </label>
            @endforeach
        </div>
    </div>
    <div class="flex gap-3">
        <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white hover:opacity-90" style="background:var(--brand);">
            {{ $quiz->exists ? 'Update Quiz' : 'Create Quiz' }}
        </button>
        <a href="{{ route('admin.quizzes.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-medium" style="color:var(--muted);">Cancel</a>
    </div>
</form>
@endsection
