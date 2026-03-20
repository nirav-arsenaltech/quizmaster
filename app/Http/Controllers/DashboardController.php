<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Services\QuizService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly QuizService $quizService) {}

    public function index(Request $request): View
    {
        $user  = $request->user();
        $quiz  = Quiz::published()->first();

        $stats = $quiz
            ? $this->quizService->userStats($user, $quiz)
            : null;

        $recentAttempts = $user->quizAttempts()
            ->with('quiz')
            ->whereIn('status', ['completed', 'timed_out'])
            ->latest('submitted_at')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact('quiz', 'stats', 'recentAttempts'));
    }
}
