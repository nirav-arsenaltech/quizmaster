<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitQuizRequest;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Services\QuizService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function __construct(private readonly QuizService $quizService) {}

    /**
     * List all published quizzes.
     */
    public function index(): View
    {
        $quizzes = Quiz::published()->get();
        return view('quiz.index', compact('quizzes'));
    }

    /**
     * Start a new attempt for the given quiz.
     */
    public function start(Request $request, Quiz $quiz): RedirectResponse
    {
        $attempt = $this->quizService->startAttempt($request->user(), $quiz);
        return redirect()->route('quiz.take', $attempt);
    }

    /**
     * Show the quiz-taking page for an in-progress attempt.
     */
    public function take(Request $request, QuizAttempt $attempt): View|RedirectResponse
    {
        $this->authorizeAttempt($request, $attempt);

        if ($attempt->isCompleted()) {
            return redirect()->route('results.show', $attempt);
        }

        $quiz = $attempt->quiz()->with('questions.answers')->first();

        // Load questions in the order stored on the attempt
        $questions = $quiz->questions()
            ->with('answers')
            ->whereIn('id', $attempt->question_order ?? [])
            ->get()
            ->sortBy(fn ($q) => array_search($q->id, $attempt->question_order))
            ->values();

        // Randomise answer order per question if configured
        if ($quiz->randomize_answers) {
            $questions = $questions->map(function ($q) {
                $q->setRelation('answers', $q->answers->shuffle()->values());
                return $q;
            });
        }

        $timeRemaining = $quiz->hasTimeLimit()
            ? max(0, $quiz->time_limit - (int) now()->diffInSeconds($attempt->started_at))
            : null;

        return view('quiz.take', compact('quiz', 'attempt', 'questions', 'timeRemaining'));
    }

    /**
     * Submit a quiz attempt.
     */
    public function submit(SubmitQuizRequest $request, QuizAttempt $attempt): RedirectResponse
    {
        $data      = $request->validated();
        $timedOut  = (bool) ($data['timed_out'] ?? false);
        $answers   = array_map('intval',
            array_filter($data['answers'] ?? [], fn ($v) => $v !== null && $v !== '')
        );

        $attempt = $this->quizService->submitAttempt($attempt, $answers, $timedOut);

        return redirect()->route('results.show', $attempt);
    }

    private function authorizeAttempt(Request $request, QuizAttempt $attempt): void
    {
        abort_unless(
            $attempt->user_id === $request->user()->id,
            403,
            'You are not authorised to access this attempt.'
        );
    }
}
