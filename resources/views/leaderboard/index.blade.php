@extends('layouts.app')
@section('title', 'Leaderboard — QuizMaster')
@section('page-title', 'Leaderboard')
@section('page-subtitle', 'Top performers ranked by score and speed')

@section('content')
@if($quizzes->count() > 1)
    <div class="flex gap-2 mb-6 flex-wrap">
        @foreach($quizzes as $q)
            <a href="{{ route('leaderboard', ['quiz' => $q->id]) }}"
               class="px-4 py-2 rounded-xl text-sm font-medium transition-all"
               style="{{ $q->id === $quiz?->id ? 'background:var(--brand);color:#fff;' : 'background:var(--card);border:1px solid var(--border);color:var(--muted);' }}">
                {{ $q->title }}
            </a>
        @endforeach
    </div>
@endif

@if($leaderboard->isEmpty())
    <div class="flex flex-col items-center justify-center py-24 text-center">
        <p style="color:var(--muted);">No completed attempts yet. Be the first!</p>
    </div>
@else
    @if($userRank)
        <div class="mb-6 flex items-center gap-3 px-5 py-4 rounded-2xl" style="background:rgba(124,92,252,.1); border:1px solid rgba(124,92,252,.3);">
            <span class="font-display text-2xl font-bold" style="color:var(--brand);">#{{ $userRank }}</span>
            <p class="text-sm text-white">Your current rank on this quiz.</p>
        </div>
    @endif

    <div class="rounded-2xl overflow-hidden" style="background:var(--card); border:1px solid var(--border);">
        {{-- Podium --}}
        @if($leaderboard->count() >= 2)
            <div class="flex items-end justify-center gap-4 px-8 pt-8 pb-6" style="border-bottom:1px solid var(--border);">
                @php $top = $leaderboard->take(3); @endphp
                @if($top->count() >= 2)
                    <div class="text-center">
                        <div class="w-11 h-11 rounded-full mx-auto mb-2 flex items-center justify-center font-display font-bold text-sm" style="background:rgba(156,163,175,.12);border:2px solid #9ca3af;color:#9ca3af;">{{ strtoupper(substr($top[1]['user']->name??'?',0,1)) }}</div>
                        <p class="text-xs font-medium text-white w-20 truncate">{{ $top[1]['user']->name??'Unknown' }}</p>
                        <p class="font-display font-bold text-lg text-white">{{ $top[1]['best_score'] }}/10</p>
                        <div class="h-10 mt-2 rounded-t-lg" style="background:rgba(156,163,175,.15);"></div>
                        <p class="text-xs font-bold" style="color:#9ca3af;">2nd</p>
                    </div>
                @endif
                <div class="text-center">
                    <p class="text-2xl mb-1">🏆</p>
                    <div class="w-13 h-13 rounded-full mx-auto mb-2 flex items-center justify-center font-display font-bold" style="width:52px;height:52px;background:rgba(245,158,11,.12);border:2px solid #f59e0b;color:#f59e0b;font-size:1.1rem;">{{ strtoupper(substr($top[0]['user']->name??'?',0,1)) }}</div>
                    <p class="text-xs font-medium text-white w-20 truncate mx-auto">{{ $top[0]['user']->name??'Unknown' }}</p>
                    <p class="font-display font-bold text-2xl text-white">{{ $top[0]['best_score'] }}/10</p>
                    <div class="h-16 mt-2 rounded-t-lg" style="background:rgba(245,158,11,.15);"></div>
                    <p class="text-xs font-bold text-amber-400">1st</p>
                </div>
                @if($top->count() >= 3)
                    <div class="text-center">
                        <div class="w-11 h-11 rounded-full mx-auto mb-2 flex items-center justify-center font-display font-bold text-sm" style="background:rgba(205,127,50,.12);border:2px solid #cd7f32;color:#cd7f32;">{{ strtoupper(substr($top[2]['user']->name??'?',0,1)) }}</div>
                        <p class="text-xs font-medium text-white w-20 truncate">{{ $top[2]['user']->name??'Unknown' }}</p>
                        <p class="font-display font-bold text-lg text-white">{{ $top[2]['best_score'] }}/10</p>
                        <div class="h-6 mt-2 rounded-t-lg" style="background:rgba(205,127,50,.15);"></div>
                        <p class="text-xs font-bold" style="color:#cd7f32;">3rd</p>
                    </div>
                @endif
            </div>
        @endif

        <table class="w-full text-sm">
            <thead>
                <tr style="border-bottom:1px solid var(--border);">
                    <th class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--muted);">Rank</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--muted);">Player</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--muted);">Best Score</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--muted);">Best Time</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color:var(--muted);">Attempts</th>
                </tr>
            </thead>
            <tbody>
                @foreach($leaderboard as $i => $row)
                    @php $isMe = $row['user']?->id === auth()->id(); @endphp
                    <tr class="transition-colors {{ $isMe ? '' : 'hover:bg-white/2' }}"
                        style="{{ $isMe ? 'background:rgba(124,92,252,.07);' : '' }} border-bottom:1px solid var(--border);">
                        <td class="px-6 py-4 text-lg">
                            @if($i===0) 🥇 @elseif($i===1) 🥈 @elseif($i===2) 🥉
                            @else <span class="font-bold" style="color:var(--muted);">#{{ $i+1 }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold"
                                     style="background:{{ $isMe ? 'var(--brand)' : 'var(--border)' }}; color:{{ $isMe ? '#fff' : 'var(--muted)' }};">
                                    {{ strtoupper(substr($row['user']->name??'?',0,1)) }}
                                </div>
                                <span class="{{ $isMe ? 'text-white font-semibold' : 'text-white' }}">
                                    {{ $row['user']->name??'Unknown' }}
                                    @if($isMe) <span class="text-xs ml-1" style="color:var(--brand);">(you)</span> @endif
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <span class="font-display font-bold {{ $row['best_score']>=8?'text-emerald-400':($row['best_score']>=5?'text-amber-400':'text-red-400') }}">
                                {{ $row['best_score'] }}/10
                            </span>
                        </td>
                        <td class="px-4 py-4 tabular-nums" style="color:var(--muted);">{{ $row['time_formatted'] }}</td>
                        <td class="px-4 py-4" style="color:var(--muted);">{{ $row['total_attempts'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
