<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'user_id',
        'attempt_number',
        'score',
        'total_questions',
        'time_taken',
        'question_order',
        'status',
        'started_at',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'question_order' => 'array',
            'started_at'     => 'datetime',
            'submitted_at'   => 'datetime',
        ];
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attemptAnswers(): HasMany
    {
        return $this->hasMany(AttemptAnswer::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['completed', 'timed_out']);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function scorePercent(): float
    {
        if ($this->total_questions === 0) {
            return 0.0;
        }

        return round(($this->score / $this->total_questions) * 100, 1);
    }

    public function timeTakenFormatted(): string
    {
        if ($this->time_taken === null) {
            return '—';
        }

        return sprintf('%d:%02d', intdiv($this->time_taken, 60), $this->time_taken % 60);
    }

    public function isCompleted(): bool
    {
        return in_array($this->status, ['completed', 'timed_out'], true);
    }

    public function isTimedOut(): bool
    {
        return $this->status === 'timed_out';
    }

    /**
     * Build a rich review collection for the results page.
     *
     * @return \Illuminate\Support\Collection<array{
     *     question: Question,
     *     chosen_answer: Answer|null,
     *     correct_answer: Answer|null,
     *     is_correct: bool
     * }>
     */
    public function buildReview(): \Illuminate\Support\Collection
    {
        $this->load(['attemptAnswers.question.answers', 'attemptAnswers.answer']);

        return $this->attemptAnswers->map(function (AttemptAnswer $aa) {
            $question      = $aa->question;
            $correctAnswer = $question->answers->firstWhere('is_correct', true);

            return [
                'question'       => $question,
                'chosen_answer'  => $aa->answer,
                'correct_answer' => $correctAnswer,
                'is_correct'     => $aa->is_correct,
            ];
        });
    }
}
