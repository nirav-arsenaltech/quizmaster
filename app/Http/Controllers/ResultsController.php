<?php

namespace App\Http\Controllers;

use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResultsController extends Controller
{
    /**
     * Show all completed attempts for the authenticated user.
     */
    public function index(Request $request): View
    {
        $attempts = $request->user()
            ->quizAttempts()
            ->with('quiz')
            ->whereIn('status', ['completed', 'timed_out'])
            ->latest('submitted_at')
            ->paginate(10);

        return view('results.index', compact('attempts'));
    }

    /**
     * Show the result + review for a single attempt.
     */
    public function show(Request $request, QuizAttempt $attempt): View
    {
        abort_unless($attempt->user_id === $request->user()->id, 403);

        $review            = $attempt->buildReview();
        $quiz              = $attempt->quiz;
        $attemptsUsed      = $request->user()->completedAttemptsForQuiz($attempt->quiz_id);
        $attemptsRemaining = max(0, $quiz->max_attempts - $attemptsUsed);

        return view('results.show', compact(
            'attempt', 'review', 'quiz', 'attemptsUsed', 'attemptsRemaining'
        ));
    }
}
