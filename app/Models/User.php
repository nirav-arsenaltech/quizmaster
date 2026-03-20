<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_admin'          => 'boolean',
        ];
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    // ── Business logic helpers ────────────────────────────────────────────────

    /**
     * Count completed / timed-out attempts for a quiz.
     */
    public function completedAttemptsForQuiz(int $quizId): int
    {
        return $this->quizAttempts()
                    ->where('quiz_id', $quizId)
                    ->whereIn('status', ['completed', 'timed_out'])
                    ->count();
    }

    /**
     * Whether the user may start another attempt on a quiz.
     */
    public function canAttemptQuiz(Quiz $quiz): bool
    {
        return $this->completedAttemptsForQuiz($quiz->id) < $quiz->max_attempts;
    }

    /**
     * Best completed attempt for a quiz (highest score, then fastest time).
     */
    public function bestScoreForQuiz(int $quizId): ?QuizAttempt
    {
        return $this->quizAttempts()
                    ->where('quiz_id', $quizId)
                    ->whereIn('status', ['completed', 'timed_out'])
                    ->orderByDesc('score')
                    ->orderBy('time_taken')
                    ->first();
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }
}
