<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['quiz_id', 'body', 'order', 'explanation'];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class)->orderBy('order');
    }

    public function attemptAnswers(): HasMany
    {
        return $this->hasMany(AttemptAnswer::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function correctAnswer(): ?Answer
    {
        return $this->answers->firstWhere('is_correct', true);
    }

    /**
     * Check if the given answer ID is the correct answer AND belongs to this question.
     */
    public function isCorrectAnswer(int $answerId): bool
    {
        return $this->answers()
                    ->where('id', $answerId)
                    ->where('is_correct', true)
                    ->exists();
    }

    /**
     * Anti-spoofing: verify an answer ID belongs to this question.
     */
    public function ownsAnswer(int $answerId): bool
    {
        return $this->answers()->where('id', $answerId)->exists();
    }
}
