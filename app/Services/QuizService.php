<?php

namespace App\Services;

use App\Models\AttemptAnswer;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QuizService
{
    /**
     * Create a new in-progress attempt for the given user and quiz.
     *
     * @throws ValidationException when max attempts already reached
     */
    public function startAttempt(User $user, Quiz $quiz): QuizAttempt
    {
        if (! $user->canAttemptQuiz($quiz)) {
            throw ValidationException::withMessages([
                'quiz' => "You have used all {$quiz->max_attempts} attempts for this quiz.",
            ]);
        }

        // Abandon any stale in-progress attempt before starting a fresh one
        QuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->where('status', 'in_progress')
            ->update(['status' => 'completed', 'submitted_at' => now()]);

        $attemptNumber = $user->completedAttemptsForQuiz($quiz->id) + 1;
        $questions     = $quiz->pickQuestionsForAttempt();

        return QuizAttempt::create([
            'quiz_id'         => $quiz->id,
            'user_id'         => $user->id,
            'attempt_number'  => $attemptNumber,
            'total_questions' => $questions->count(),
            'question_order'  => $questions->pluck('id')->toArray(),
            'status'          => 'in_progress',
            'started_at'      => now(),
        ]);
    }

    /**
     * Grade and persist a submitted attempt.
     *
     * @param  array<int, int>  $submittedAnswers  [question_id => answer_id]
     */
    public function submitAttempt(
        QuizAttempt $attempt,
        array       $submittedAnswers,
        bool        $timedOut = false
    ): QuizAttempt {
        // Idempotent: already submitted
        if ($attempt->isCompleted()) {
            return $attempt;
        }

        return DB::transaction(function () use ($attempt, $submittedAnswers, $timedOut) {
            $quiz      = $attempt->quiz()->with('questions.answers')->first();
            $questions = $quiz->questions->keyBy('id');
            $score     = 0;

            foreach ($questions as $questionId => $question) {
                $answerId  = isset($submittedAnswers[$questionId])
                    ? (int) $submittedAnswers[$questionId]
                    : null;

                $isCorrect = false;

                if ($answerId !== null) {
                    // Reject answers that don't belong to this question (anti-spoofing)
                    if ($question->ownsAnswer($answerId)) {
                        $isCorrect = $question->isCorrectAnswer($answerId);
                    } else {
                        $answerId = null;
                    }
                }

                if ($isCorrect) {
                    $score++;
                }

                AttemptAnswer::updateOrCreate(
                    [
                        'quiz_attempt_id' => $attempt->id,
                        'question_id'     => $questionId,
                    ],
                    [
                        'answer_id'  => $answerId,
                        'is_correct' => $isCorrect,
                    ]
                );
            }

            $timeTaken = max(0, (int) now()->diffInSeconds($attempt->started_at));

            $attempt->update([
                'score'        => $score,
                'time_taken'   => $timeTaken,
                'status'       => $timedOut ? 'timed_out' : 'completed',
                'submitted_at' => now(),
            ]);

            return $attempt->fresh();
        });
    }

    /**
     * Return top-10 leaderboard for a quiz.
     * Ranked by best score DESC, then fastest time ASC.
     */
    public function leaderboard(Quiz $quiz): Collection
    {
        return QuizAttempt::query()
            ->with('user')
            ->where('quiz_id', $quiz->id)
            ->whereIn('status', ['completed', 'timed_out'])
            ->select(
                'user_id',
                DB::raw('MAX(score) as best_score'),
                DB::raw('MIN(CASE WHEN score = (
                    SELECT MAX(score) FROM quiz_attempts qa2
                    WHERE qa2.user_id = quiz_attempts.user_id
                      AND qa2.quiz_id = quiz_attempts.quiz_id
                ) THEN time_taken END) as best_time'),
                DB::raw('COUNT(*) as total_attempts')
            )
            ->groupBy('user_id')
            ->orderByDesc('best_score')
            ->orderBy('best_time')
            ->limit(10)
            ->get()
            ->map(fn ($row) => [
                'user'           => $row->user,
                'best_score'     => (int) $row->best_score,
                'best_time'      => (int) $row->best_time,
                'total_attempts' => (int) $row->total_attempts,
                'time_formatted' => $this->formatSeconds((int) $row->best_time),
            ]);
    }

    /**
     * Aggregate stats for the dashboard.
     */
    public function userStats(User $user, Quiz $quiz): array
    {
        $attempts = QuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->whereIn('status', ['completed', 'timed_out'])
            ->orderBy('attempt_number')
            ->get();

        return [
            'attempts_used'      => $attempts->count(),
            'attempts_remaining' => max(0, $quiz->max_attempts - $attempts->count()),
            'best_score'         => (int) ($attempts->max('score') ?? 0),
            'avg_score'          => round($attempts->avg('score') ?? 0, 1),
            'total_quizzes'      => $attempts->count(),
            'score_history'      => $attempts->map(fn ($a) => [
                'attempt' => $a->attempt_number,
                'score'   => $a->score,
                'time'    => $a->time_taken,
            ])->values()->toArray(),
        ];
    }

    private function formatSeconds(?int $seconds): string
    {
        if ($seconds === null) {
            return '—';
        }

        return sprintf('%d:%02d', intdiv($seconds, 60), $seconds % 60);
    }
}
