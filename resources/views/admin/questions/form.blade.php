@extends('layouts.app')
@section('title', ($question->exists ? 'Edit' : 'Add') . ' Question — Admin')
@section('page-title', $question->exists ? 'Edit Question' : 'Add Question')
@section('page-subtitle', $quiz->title)

@section('content')
@if($errors->any())
    <div class="mb-5 px-4 py-3 rounded-xl text-sm" style="background:rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.25);">
        <p class="font-semibold text-red-400 mb-1">Please fix:</p>
        @foreach($errors->all() as $e) <p class="text-red-300">{{ $e }}</p> @endforeach
    </div>
@endif

<form method="POST"
      action="{{ $question->exists ? route('admin.quizzes.questions.update', [$quiz, $question]) : route('admin.quizzes.questions.store', $quiz) }}"
      class="max-w-2xl" id="qform">
    @csrf
    @if($question->exists) @method('PUT') @endif

    <div class="rounded-2xl p-6 space-y-5 mb-5" style="background:var(--card); border:1px solid var(--border);">
        <div>
            <label class="block text-sm font-medium text-white mb-1.5">Question <span class="text-red-400">*</span></label>
            <textarea name="body" rows="3" required
                      class="w-full px-4 py-2.5 rounded-xl text-sm text-white outline-none resize-none"
                      style="background:var(--bg); border:1px solid var(--border);"
                      placeholder="Enter your question...">{{ old('body', $question->body) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-white mb-1.5">Explanation <span class="text-xs font-normal" style="color:var(--muted);">(optional, shown after quiz)</span></label>
            <textarea name="explanation" rows="2"
                      class="w-full px-4 py-2.5 rounded-xl text-sm text-white outline-none resize-none"
                      style="background:var(--bg); border:1px solid var(--border);"
                      placeholder="Why is this answer correct?">{{ old('explanation', $question->explanation) }}</textarea>
        </div>
        <div class="w-32">
            <label class="block text-sm font-medium text-white mb-1.5">Order</label>
            <input type="number" name="order" min="0" value="{{ old('order', $question->order ?? 0) }}"
                   class="w-full px-4 py-2.5 rounded-xl text-sm text-white outline-none"
                   style="background:var(--bg); border:1px solid var(--border);" />
        </div>
    </div>

    <div class="rounded-2xl p-6" style="background:var(--card); border:1px solid var(--border);">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-display font-bold text-white">Answer Options</h3>
            <p class="text-xs" style="color:var(--muted);">Mark exactly one correct</p>
        </div>

        @php
            $existing = old('answers', $question->answers->map(fn($a) => [
                'id'         => $a->id,
                'body'       => $a->body,
                'is_correct' => $a->is_correct,
            ])->toArray());
            if (!$question->exists) {
                while (count($existing) < 4) $existing[] = ['id'=>null,'body'=>'','is_correct'=>false];
            }
        @endphp

        <div id="ans-list" class="space-y-3">
            @foreach($existing as $i => $ans)
                <div class="ans-row flex items-center gap-3 p-3 rounded-xl" style="background:var(--bg); border:1px solid var(--border);" data-idx="{{ $i }}">
                    @if(!empty($ans['id'])) <input type="hidden" name="answers[{{ $i }}][id]" value="{{ $ans['id'] }}" /> @endif
                    <label class="flex-shrink-0 flex items-center gap-1.5 cursor-pointer">
                        <input type="radio" name="correct_idx" value="{{ $i }}" class="sr-only corr-radio" onchange="syncCorrect()" {{ !empty($ans['is_correct']) ? 'checked' : '' }} />
                        <div class="corr-dot w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all" style="border-color:{{ !empty($ans['is_correct']) ? 'var(--brand)' : 'var(--border)' }};">
                            <div class="corr-inner w-2.5 h-2.5 rounded-full" style="background:{{ !empty($ans['is_correct']) ? 'var(--brand)' : 'transparent' }};"></div>
                        </div>
                        <span class="text-xs" style="color:var(--muted);">Correct</span>
                    </label>
                    <input type="text" name="answers[{{ $i }}][body]" value="{{ $ans['body'] }}" placeholder="Answer {{ $i + 1 }}"
                           class="flex-1 px-3 py-2 rounded-lg text-sm text-white outline-none"
                           style="background:var(--card); border:1px solid var(--border);" />
                    <input type="hidden" name="answers[{{ $i }}][is_correct]" value="{{ !empty($ans['is_correct']) ? '1' : '0' }}" class="corr-flag" />
                    <button type="button" onclick="rmRow(this)" class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-red-500/20 transition-colors" style="color:var(--muted);">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endforeach
        </div>

        <button type="button" onclick="addRow()" class="mt-3 flex items-center gap-2 text-sm font-medium" style="color:var(--brand);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add option
        </button>
    </div>

    <div class="flex gap-3 mt-5">
        <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white hover:opacity-90" style="background:var(--brand);">
            {{ $question->exists ? 'Update' : 'Save Question' }}
        </button>
        <a href="{{ route('admin.quizzes.questions.index', $quiz) }}" class="px-5 py-2.5 rounded-xl text-sm" style="color:var(--muted);">Cancel</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
let cnt = {{ count($existing) }};

function syncCorrect() {
    document.querySelectorAll('.ans-row').forEach(row => {
        const radio = row.querySelector('.corr-radio');
        const dot   = row.querySelector('.corr-dot');
        const inner = row.querySelector('.corr-inner');
        const flag  = row.querySelector('.corr-flag');
        const ok    = radio.checked;
        dot.style.borderColor  = ok ? 'var(--brand)' : 'var(--border)';
        inner.style.background = ok ? 'var(--brand)' : 'transparent';
        flag.value = ok ? '1' : '0';
    });
}

function addRow() {
    const i = cnt++;
    const d = document.createElement('div');
    d.className = 'ans-row flex items-center gap-3 p-3 rounded-xl';
    d.style.cssText = 'background:var(--bg);border:1px solid var(--border);';
    d.dataset.idx = i;
    d.innerHTML = `
        <label class="flex-shrink-0 flex items-center gap-1.5 cursor-pointer">
            <input type="radio" name="correct_idx" value="${i}" class="sr-only corr-radio" onchange="syncCorrect()" />
            <div class="corr-dot w-5 h-5 rounded-full border-2 flex items-center justify-center" style="border-color:var(--border);">
                <div class="corr-inner w-2.5 h-2.5 rounded-full" style="background:transparent;"></div>
            </div>
            <span class="text-xs" style="color:var(--muted);">Correct</span>
        </label>
        <input type="text" name="answers[${i}][body]" placeholder="Answer ${i+1}" class="flex-1 px-3 py-2 rounded-lg text-sm text-white outline-none" style="background:var(--card);border:1px solid var(--border);" />
        <input type="hidden" name="answers[${i}][is_correct]" value="0" class="corr-flag" />
        <button type="button" onclick="rmRow(this)" class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-red-500/20" style="color:var(--muted);">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>`;
    document.getElementById('ans-list').appendChild(d);
}

function rmRow(btn) {
    if (document.querySelectorAll('.ans-row').length <= 2) { alert('Minimum 2 answers required.'); return; }
    btn.closest('.ans-row').remove();
    reindex();
}

function reindex() {
    document.querySelectorAll('.ans-row').forEach((row, i) => {
        row.dataset.idx = i;
        const r = row.querySelector('.corr-radio');
        const t = row.querySelector('input[type=text]');
        const h = row.querySelector('.corr-flag');
        if(r) r.value = i;
        if(t) t.name  = `answers[${i}][body]`;
        if(h) h.name  = `answers[${i}][is_correct]`;
    });
    cnt = document.querySelectorAll('.ans-row').length;
}
</script>
@endpush
