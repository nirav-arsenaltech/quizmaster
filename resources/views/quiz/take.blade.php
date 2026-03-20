@extends('layouts.app')
@section('title', 'Taking Quiz — QuizMaster')
@section('page-title', $quiz->title)
@section('page-subtitle', 'Attempt #' . $attempt->attempt_number . ' · ' . $questions->count() . ' questions')

@section('header-actions')
    @if($timeRemaining !== null)
        <div id="timer-wrap" class="flex items-center gap-2 px-4 py-2 rounded-xl font-display text-lg font-bold"
             style="background:var(--card); border:1px solid var(--border);"
             data-seconds="{{ $timeRemaining }}"
             data-attempt="{{ $attempt->id }}">
            <svg class="w-4 h-4 flex-shrink-0" style="color:var(--brand);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span id="timer-text" class="text-white tabular-nums">--:--</span>
        </div>
    @endif
@endsection

@section('content')

{{-- Progress --}}
<div class="mb-6">
    <div class="flex items-center justify-between text-xs mb-2" style="color:var(--muted);">
        <span id="q-label">Question 1 of {{ $questions->count() }}</span>
        <span id="ans-label">0 answered</span>
    </div>
    <div class="h-1.5 rounded-full overflow-hidden" style="background:var(--border);">
        <div id="progress-bar" class="h-full rounded-full transition-all duration-500" style="background:var(--brand);width:0%;"></div>
    </div>
</div>

<form id="quiz-form" method="POST" action="{{ route('quiz.submit', $attempt) }}">
    @csrf
    <input type="hidden" name="timed_out" id="timed_out" value="0"/>

    {{-- Questions (one visible at a time) --}}
    @foreach($questions as $i => $question)
        <div class="question-slide {{ $i > 0 ? 'hidden' : '' }}" data-index="{{ $i }}" data-qid="{{ $question->id }}">
            <div class="rounded-2xl p-6 mb-6" style="background:var(--card); border:1px solid var(--border);">
                <span class="inline-block text-xs font-bold px-2 py-1 rounded-lg mb-3"
                      style="background:rgba(124,92,252,.12); color:var(--brand);">Q{{ $i + 1 }}</span>
                <p class="font-display text-lg font-semibold text-white leading-snug mb-6">{{ $question->body }}</p>

                <div class="space-y-3">
                    @foreach($question->answers as $answer)
                        <label class="answer-opt flex items-center gap-4 p-4 rounded-xl cursor-pointer border transition-all"
                               style="border-color:var(--border); background:var(--bg);"
                               data-qid="{{ $question->id }}">
                            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $answer->id }}"
                                   class="sr-only answer-radio" />
                            <div class="rdot w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-all"
                                 style="border-color:var(--border);">
                                <div class="rdot-inner w-2.5 h-2.5 rounded-full hidden" style="background:var(--brand);"></div>
                            </div>
                            <span class="text-sm font-medium text-white">{{ $answer->body }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach

    {{-- Navigation --}}
    <div class="flex items-center justify-between">
        <button type="button" id="btn-prev" disabled
                class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium transition-all disabled:opacity-30"
                style="background:var(--card); border:1px solid var(--border); color:var(--text);">
            ← Previous
        </button>

        {{-- Dot navigator --}}
        <div class="flex items-center gap-1.5" id="dot-wrap">
            @foreach($questions as $i => $q)
                <button type="button" data-target="{{ $i }}"
                        class="dot w-2 h-2 rounded-full transition-all hover:scale-125"
                        style="background:var(--border);"></button>
            @endforeach
        </div>

        <div class="flex gap-2">
            <button type="button" id="btn-next"
                    class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-all"
                    style="background:var(--brand);">
                Next →
            </button>
            <button type="submit" id="btn-submit"
                    class="hidden px-5 py-2.5 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-all"
                    style="background:#22c55e;">
                Submit ✓
            </button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
(function(){
    const total    = {{ $questions->count() }};
    let cur        = 0;
    const answered = new Set();

    const slides   = [...document.querySelectorAll('.question-slide')];
    const dots     = [...document.querySelectorAll('.dot')];
    const btnPrev  = document.getElementById('btn-prev');
    const btnNext  = document.getElementById('btn-next');
    const btnSub   = document.getElementById('btn-submit');
    const pBar     = document.getElementById('progress-bar');
    const qLabel   = document.getElementById('q-label');
    const aLabel   = document.getElementById('ans-label');

    function goTo(idx) {
        slides[cur].classList.add('hidden');
        dots[cur].style.background = answered.has(cur) ? 'var(--brand)' : 'var(--border)';
        cur = Math.max(0, Math.min(total - 1, idx));
        slides[cur].classList.remove('hidden');
        dots[cur].style.background = '#fff';
        btnPrev.disabled = cur === 0;
        const last = cur === total - 1;
        btnNext.classList.toggle('hidden', last);
        btnSub.classList.toggle('hidden', !last);
        qLabel.textContent = `Question ${cur + 1} of ${total}`;
        pBar.style.width = Math.round((answered.size / total) * 100) + '%';
        aLabel.textContent = answered.size + ' answered';
    }

    btnNext.addEventListener('click', () => goTo(cur + 1));
    btnPrev.addEventListener('click', () => goTo(cur - 1));
    dots.forEach((d, i) => d.addEventListener('click', () => goTo(i)));

    document.querySelectorAll('.answer-opt').forEach(label => {
        label.addEventListener('click', () => {
            const qid   = label.dataset.qid;
            const radio = label.querySelector('.answer-radio');
            document.querySelectorAll(`.answer-opt[data-qid="${qid}"]`).forEach(l => {
                l.style.borderColor = 'var(--border)';
                l.style.background  = 'var(--bg)';
                l.querySelector('.rdot').style.borderColor = 'var(--border)';
                l.querySelector('.rdot-inner').classList.add('hidden');
            });
            radio.checked = true;
            label.style.borderColor = 'var(--brand)';
            label.style.background  = 'rgba(124,92,252,0.07)';
            label.querySelector('.rdot').style.borderColor = 'var(--brand)';
            label.querySelector('.rdot-inner').classList.remove('hidden');
            answered.add(cur);
            dots[cur].style.background = 'var(--brand)';
            pBar.style.width = Math.round((answered.size / total) * 100) + '%';
            aLabel.textContent = answered.size + ' answered';
        });
    });

    // Timer
    const tw = document.getElementById('timer-wrap');
    if (tw) {
        const key = 'qm_deadline_' + tw.dataset.attempt;
        let deadline = parseInt(sessionStorage.getItem(key) || '0');
        if (!deadline || deadline < Date.now()) {
            deadline = Date.now() + (parseInt(tw.dataset.seconds) * 1000);
            sessionStorage.setItem(key, deadline);
        }
        const tEl = document.getElementById('timer-text');
        function tick() {
            const rem = Math.max(0, Math.floor((deadline - Date.now()) / 1000));
            tEl.textContent = Math.floor(rem/60) + ':' + String(rem%60).padStart(2,'0');
            if (rem <= 60) { tw.style.borderColor='var(--brand2)'; tEl.style.color='var(--brand2)'; }
            if (rem <= 0) { sessionStorage.removeItem(key); document.getElementById('timed_out').value='1'; document.getElementById('quiz-form').submit(); return; }
            setTimeout(tick, 1000);
        }
        tick();
    }

    goTo(0);
    dots[0].style.background = '#fff';
})();
</script>
@endpush
