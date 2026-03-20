@extends('layouts.app')
@section('title', 'Dashboard — QuizMaster')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Welcome back, ' . auth()->user()->name . '!')

@section('content')

@if (!$quiz || !$stats)
    <div class="flex flex-col items-center justify-center py-24 text-center">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-4"
             style="background:rgba(124,92,252,.12);">
            <svg class="w-8 h-8" style="color:var(--brand);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <p class="text-lg font-semibold text-white mb-1">No quiz published yet</p>
        @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.quizzes.create') }}" class="mt-4 px-5 py-2.5 rounded-xl text-sm font-semibold text-white hover:opacity-90" style="background:var(--brand);">Create Quiz →</a>
        @endif
    </div>
@else

{{-- Stat cards --}}
<div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
    @php
        $cards = [
            ['label'=>'Quizzes Taken',   'value'=> $stats['total_quizzes'],       'color'=>'#7c5cfc', 'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2'],
            ['label'=>'Best Score',      'value'=> $stats['best_score'].'/10',     'color'=>'#f59e0b', 'icon'=>'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'],
            ['label'=>'Average Score',   'value'=> $stats['avg_score'].'/10',      'color'=>'#22c55e', 'icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
            ['label'=>'Attempts Left',   'value'=> $stats['attempts_remaining'],   'color'=>'#fc5c7d', 'icon'=>'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
        ];
    @endphp
    @foreach($cards as $card)
        <div class="rounded-2xl p-5" style="background:var(--card); border:1px solid var(--border);">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-4"
                 style="background:{{ $card['color'] }}22;">
                <svg style="width:18px;height:18px;color:{{ $card['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $card['icon'] }}"/>
                </svg>
            </div>
            <p class="font-display text-2xl font-bold text-white">{{ $card['value'] }}</p>
            <p class="text-xs mt-0.5" style="color:var(--muted);">{{ $card['label'] }}</p>
        </div>
    @endforeach
</div>

{{-- Chart + Attempts ring --}}
<div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-8">
    <div class="lg:col-span-3 rounded-2xl p-6" style="background:var(--card); border:1px solid var(--border);">
        <h3 class="font-display font-bold text-white mb-4">Score History</h3>
        @if(count($stats['score_history']) > 0)
            <div style="height:200px;"><canvas id="scoreChart"></canvas></div>
        @else
            <div class="flex items-center justify-center h-44 rounded-xl" style="background:var(--bg);">
                <p class="text-sm" style="color:var(--muted);">No attempts yet — take the quiz!</p>
            </div>
        @endif
    </div>

    <div class="lg:col-span-2 rounded-2xl p-6 flex flex-col items-center justify-center text-center"
         style="background:var(--card); border:1px solid var(--border);">
        <h3 class="font-display font-bold text-white mb-4">Attempts</h3>
        @php
            $used      = $stats['attempts_used'];
            $total     = $quiz->max_attempts;
            $remaining = $stats['attempts_remaining'];
            $pct       = $total > 0 ? round(($remaining / $total) * 100) : 0;
            $dashArray = round($pct, 1) . ' ' . (100 - round($pct, 1));
        @endphp
        <div class="relative w-28 h-28 mb-4">
            <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                <circle cx="18" cy="18" r="15.9" fill="none" stroke="var(--border)" stroke-width="3"/>
                <circle cx="18" cy="18" r="15.9" fill="none" stroke="var(--brand)" stroke-width="3"
                        stroke-dasharray="{{ $dashArray }}" stroke-dashoffset="0" stroke-linecap="round"/>
            </svg>
            <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span class="font-display text-2xl font-bold text-white">{{ $remaining }}</span>
                <span class="text-xs" style="color:var(--muted);">left</span>
            </div>
        </div>
        <p class="text-sm mb-4" style="color:var(--muted);">{{ $used }} of {{ $total }} used</p>
        @if($remaining > 0)
            <form method="POST" action="{{ route('quiz.start', $quiz) }}">
                @csrf
                <button type="submit" class="px-5 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90" style="background:var(--brand);">
                    Take Quiz →
                </button>
            </form>
        @else
            <p class="text-xs font-medium" style="color:var(--brand2);">No attempts remaining</p>
        @endif
    </div>
</div>

{{-- Recent attempts --}}
@if($recentAttempts->isNotEmpty())
    <div class="rounded-2xl overflow-hidden" style="background:var(--card); border:1px solid var(--border);">
        <div class="flex items-center justify-between px-6 py-4" style="border-bottom:1px solid var(--border);">
            <h3 class="font-display font-bold text-white">Recent Attempts</h3>
            <a href="{{ route('results.index') }}" class="text-xs font-medium" style="color:var(--brand);">View all →</a>
        </div>
        @foreach($recentAttempts as $attempt)
            <a href="{{ route('results.show', $attempt) }}"
               class="flex items-center justify-between px-6 py-3.5 hover:bg-white/3 transition-colors"
               style="border-bottom:1px solid var(--border);">
                <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded-full text-xs font-bold flex items-center justify-center"
                          style="background:rgba(124,92,252,.15); color:var(--brand);">#{{ $attempt->attempt_number }}</span>
                    <div>
                        <p class="text-sm font-medium text-white">{{ $attempt->quiz->title }}</p>
                        <p class="text-xs" style="color:var(--muted);">{{ $attempt->submitted_at?->diffForHumans() }}</p>
                    </div>
                </div>
                <span class="font-display font-bold text-lg {{ $attempt->score >= 8 ? 'text-emerald-400' : ($attempt->score >= 5 ? 'text-amber-400' : 'text-red-400') }}">
                    {{ $attempt->score }}/{{ $attempt->total_questions }}
                </span>
            </a>
        @endforeach
    </div>
@endif

@endif
@endsection

@push('scripts')
@if(isset($stats) && count($stats['score_history'] ?? []) > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('scoreChart'), {
    type: 'line',
    data: {
        labels: @json(array_map(fn($h) => 'Attempt '.$h['attempt'], $stats['score_history'])),
        datasets: [{
            data: @json(array_map(fn($h) => $h['score'], $stats['score_history'])),
            fill: true,
            borderColor: '#7c5cfc',
            backgroundColor: 'rgba(124,92,252,0.1)',
            borderWidth: 2.5,
            pointBackgroundColor: '#7c5cfc',
            pointRadius: 5,
            tension: 0.4,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { min:0, max:10, ticks:{ color:'#55556e', stepSize:2 }, grid:{ color:'rgba(255,255,255,0.04)' } },
            x: { ticks:{ color:'#55556e' }, grid:{ display:false } }
        }
    }
});
</script>
@endif
@endpush
