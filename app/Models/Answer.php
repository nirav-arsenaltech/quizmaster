<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = ['question_id', 'body', 'is_correct', 'order'];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
        ];
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function attemptAnswers(): HasMany
    {
        return $this->hasMany(AttemptAnswer::class);
    }
}
