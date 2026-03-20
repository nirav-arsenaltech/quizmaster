<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Services\QuizService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    public function __construct(private readonly QuizService $quizService) {}

    public function index(Request $request): View
    {
        $quizzes    = Quiz::published()->get();
        $selectedId = (int) $request->query('quiz', $quizzes->first()?->id);
        $quiz       = $quizzes->firstWhere('id', $selectedId) ?? $quizzes->first();

        $leaderboard = $quiz
            ? $this->quizService->leaderboard($quiz)
            : collect();

        $userRank = null;
        if ($quiz && $leaderboard->isNotEmpty()) {
            $userId   = $request->user()->id;
            $position = $leaderboard->search(fn ($row) => $row['user']?->id === $userId);
            $userRank = $position !== false ? $position + 1 : null;
        }

        return view('leaderboard.index', compact('quizzes', 'quiz', 'leaderboard', 'userRank'));
    }
}
