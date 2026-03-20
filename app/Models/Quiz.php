<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Quiz extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'time_limit',
        'questions_per_attempt',
        'max_attempts',
        'randomize_questions',
        'randomize_answers',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'randomize_questions' => 'boolean',
            'randomize_answers'   => 'boolean',
            'is_published'        => 'boolean',
        ];
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Pick the questions for one attempt.
     * Randomises order and answer order if configured.
     * Limits to questions_per_attempt.
     */
    public function pickQuestionsForAttempt(): Collection
    {
        $questions = $this->questions()->with('answers')->get();

        if ($this->randomize_questions) {
            $questions = $questions->shuffle();
        }

        $questions = $questions->take($this->questions_per_attempt)->values();

        if ($this->randomize_answers) {
            $questions = $questions->map(function (Question $q) {
                $q->setRelation('answers', $q->answers->shuffle()->values());
                return $q;
            });
        }

        return $questions;
    }

    public function timeLimitFormatted(): string
    {
        if ($this->time_limit === 0) {
            return 'No limit';
        }
        $mins = intdiv($this->time_limit, 60);
        $secs = $this->time_limit % 60;
        return $secs > 0 ? "{$mins}m {$secs}s" : "{$mins} min";
    }

    public function hasTimeLimit(): bool
    {
        return $this->time_limit > 0;
    }
}
