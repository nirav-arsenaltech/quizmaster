<?php

use App\Models\Answer;
use App\Models\AttemptAnswer;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Services\QuizService;

// ── Helpers ────────────────────────────────────────────────────────────────────

function makeUser(bool $admin = false): User
{
    return User::factory()->create(['is_admin' => $admin]);
}

function makeQuiz(array $overrides = []): Quiz
{
    $quiz = Quiz::factory()->create(array_merge([
        'is_published'          => true,
        'max_attempts'          => 3,
        'questions_per_attempt' => 10,
        'time_limit'            => 300,
        'randomize_questions'   => false,
        'randomize_answers'     => false,
    ], $overrides));

    for ($i = 1; $i <= 10; $i++) {
        $q = Question::factory()->create(['quiz_id' => $quiz->id, 'order' => $i]);
        Answer::factory()->correct()->create(['question_id' => $q->id, 'order' => 1]);
        Answer::factory()->create(['question_id' => $q->id, 'order' => 2]);
        Answer::factory()->create(['question_id' => $q->id, 'order' => 3]);
        Answer::factory()->create(['question_id' => $q->id, 'order' => 4]);
    }

    return $quiz->fresh(['questions.answers']);
}

function allCorrect(Quiz $quiz, QuizAttempt $attempt): array
{
    $answers = [];
    foreach ($quiz->questions as $q) {
        $answers[$q->id] = $q->answers->firstWhere('is_correct', true)->id;
    }
    return $answers;
}

// ── Auth ───────────────────────────────────────────────────────────────────────

test('guests are redirected to login', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

test('authenticated user can view dashboard', function () {
    $this->actingAs(makeUser())->get(route('dashboard'))->assertOk();
});

test('user can register', function () {
    $this->post(route('register'), [
        'name'                  => 'Test User',
        'email'                 => 'test@example.com',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirect(route('dashboard'));

    expect(User::where('email', 'test@example.com')->exists())->toBeTrue();
});

test('user can login', function () {
    $user = makeUser();
    $this->post(route('login'), ['email' => $user->email, 'password' => 'password'])
         ->assertRedirect(route('dashboard'));
});

// ── Quiz listing ───────────────────────────────────────────────────────────────

test('quiz index shows published quizzes', function () {
    $user = makeUser();
    $quiz = makeQuiz();

    $this->actingAs($user)
         ->get(route('quiz.index'))
         ->assertOk()
         ->assertSeeText($quiz->title);
});

// ── Start attempt ─────────────────────────────────────────────────────────────

test('user can start an attempt', function () {
    $user = makeUser();
    $quiz = makeQuiz();

    $this->actingAs($user)
         ->post(route('quiz.start', $quiz))
         ->assertRedirect();

    expect(QuizAttempt::where('user_id', $user->id)->count())->toBe(1);
});

test('started attempt has correct question order', function () {
    $user = makeUser();
    $quiz = makeQuiz();

    $this->actingAs($user)->post(route('quiz.start', $quiz));

    $attempt = QuizAttempt::first();
    expect($attempt->question_order)->toHaveCount(10);
    expect($attempt->status)->toBe('in_progress');
});

// ── Taking quiz ────────────────────────────────────────────────────────────────

test('user can view quiz take page', function () {
    $user    = makeUser();
    $quiz    = makeQuiz();
    $service = app(QuizService::class);
    $attempt = $service->startAttempt($user, $quiz);

    $this->actingAs($user)
         ->get(route('quiz.take', $attempt))
         ->assertOk()
         ->assertSeeText($quiz->title);
});

test('another user cannot view someone elses attempt', function () {
    $user1   = makeUser();
    $user2   = makeUser();
    $quiz    = makeQuiz();
    $service = app(QuizService::class);
    $attempt = $service->startAttempt($user1, $quiz);

    $this->actingAs($user2)
         ->get(route('quiz.take', $attempt))
         ->assertForbidden();
});

// ── Scoring ────────────────────────────────────────────────────────────────────

test('all correct answers scores 10', function () {
    $user    = makeUser();
    $quiz    = makeQuiz();
    $service = app(QuizService::class);
    $attempt = $service->startAttempt($user, $quiz);
    $answers = allCorrect($quiz, $attempt);

    $result = $service->submitAttempt($attempt, $answers);

    expect($result->score)->toBe(10)
         ->and($result->status)->toBe('completed');
});

test('zero correct answers scores 0', function () {
    $user    = makeUser();
    $quiz    = makeQuiz();
    $service = app(QuizService::class);
    $attempt = $service->startAttempt($user, $quiz);

    $answers = [];
    foreach ($quiz->questions as $q) {
        $answers[$q->id] = $q->answers->firstWhere('is_correct', false)->id;
    }

    expect($service->submitAttempt($attempt, $answers)->score)->toBe(0);
});

test('partial correct answers score correctly', function () {
    $user    = makeUser();
    $quiz    = makeQuiz();
    $service = app(QuizService::class);
    $attempt = $service->startAttempt($user, $quiz);

    $answers = [];
    $i = 0;
    foreach ($quiz->questions as $q) {
        $answers[$q->id] = $i++ < 7
            ? $q->answers->firstWhere('is_correct', true)->id
            : $q->answers->firstWhere('is_correct', false)->id;
    }

    expect($service->submitAttempt($attempt, $answers)->score)->toBe(7);
});

test('attempt_answers are stored for every question', function () {
    $user    = makeUser();
    $quiz    = makeQuiz();
    $service = app(QuizService::class);
    $attempt = $service->startAttempt($user, $quiz);

    $service->submitAttempt($attempt, allCorrect($quiz, $attempt));

    expect(AttemptAnswer::where('quiz_attempt_id', $attempt->id)->count())->toBe(10);
});

// ── Attempt limits ─────────────────────────────────────────────────────────────

test('user cannot exceed max 3 attempts', function () {
    $user    = makeUser();
    $quiz    = makeQuiz(['max_attempts' => 3]);
    $service = app(QuizService::class);

    for ($i = 0; $i < 3; $i++) {
        $service->submitAttempt($service->startAttempt($user, $quiz), []);
    }

    $this->expectException(\Illuminate\Validation\ValidationException::class);
    $service->startAttempt($user, $quiz);
});

test('attempt numbers increment 1 2 3', function () {
    $user    = makeUser();
    $quiz    = makeQuiz();
    $service = app(QuizService::class);

    for ($n = 1; $n <= 3; $n++) {
        $a = $service->startAttempt($user, $quiz);
        $service->submitAttempt($a, []);
        expect($a->fresh()->attempt_number)->toBe($n);
    }
});

test('different users have independent attempt counts', function () {
    $quiz    = makeQuiz(['max_attempts' => 3]);
    $service = app(QuizService::class);

    $u1 = makeUser();
    for ($i = 0; $i < 3; $i++) {
        $service->submitAttempt($service->startAttempt($u1, $quiz), []);
    }

    // u2 should still have 3 attempts available
    $u2 = makeUser();
    expect($u2->canAttemptQuiz($quiz))->toBeTrue();
});

// ── Timer ──────────────────────────────────────────────────────────────────────

test('timed_out attempt gets timed_out status', function () {
    $user    = makeUser();
    $quiz    = makeQuiz(['time_limit' => 60]);
    $service = app(QuizService::class);
    $attempt = $service->startAttempt($user, $quiz);

    expect($service->submitAttempt($attempt, [], timedOut: true)->status)->toBe('timed_out');
});

test('timed_out attempt still scores correctly', function () {
    $user    = makeUser();
    $quiz    = makeQuiz();
    $service = app(QuizService::class);
    $attempt = $service->startAttempt($user, $quiz);

    $result = $service->submitAttempt($attempt, allCorrect($quiz, $attempt), timedOut: true);
    expect($result->score)->toBe(10)
         ->and($result->status)->toBe('timed_out');
});

// ── Results ────────────────────────────────────────────────────────────────────

test('owner can view results page', function () {
    $user    = makeUser();
    $quiz    = makeQuiz();
    $service = app(QuizService::class);
    $attempt = $service->startAttempt($user, $quiz);
    $service->submitAttempt($attempt, []);

    $this->actingAs($user)
         ->get(route('results.show', $attempt))
         ->assertOk();
});

test('non-owner cannot view results', function () {
    $u1      = makeUser();
    $u2      = makeUser();
    $quiz    = makeQuiz();
    $service = app(QuizService::class);
    $attempt = $service->startAttempt($u1, $quiz);
    $service->submitAttempt($attempt, []);

    $this->actingAs($u2)->get(route('results.show', $attempt))->assertForbidden();
});

test('buildReview returns 10 items with correct keys', function () {
    $user    = makeUser();
    $quiz    = makeQuiz();
    $service = app(QuizService::class);
    $attempt = $service->startAttempt($user, $quiz);
    $service->submitAttempt($attempt, allCorrect($quiz, $attempt));

    $review = $attempt->fresh()->buildReview();
    expect($review)->toHaveCount(10);
    expect($review->first())->toHaveKeys(['question', 'chosen_answer', 'correct_answer', 'is_correct']);
    expect($review->every(fn ($r) => $r['is_correct']))->toBeTrue();
});

// ── Anti-spoofing ──────────────────────────────────────────────────────────────

test('cross-question answer IDs are rejected and score 0', function () {
    $user    = makeUser();
    $quiz    = makeQuiz();
    $service = app(QuizService::class);
    $attempt = $service->startAttempt($user, $quiz);

    $questions = $quiz->questions;
    $answers   = [];
    foreach ($questions as $i => $q) {
        $other = $questions[($i + 1) % $questions->count()];
        $answers[$q->id] = $other->answers->first()->id;
    }

    expect($service->submitAttempt($attempt, $answers)->score)->toBe(0);
});

// ── Randomisation ─────────────────────────────────────────────────────────────

test('quiz picks correct number of questions', function () {
    $quiz    = makeQuiz(['randomize_questions' => true, 'questions_per_attempt' => 10]);
    $picked  = $quiz->pickQuestionsForAttempt();
    expect($picked)->toHaveCount(10);
});

// ── Leaderboard ────────────────────────────────────────────────────────────────

test('leaderboard is sorted by best score', function () {
    $quiz    = makeQuiz();
    $service = app(QuizService::class);

    $loser  = makeUser();
    $winner = makeUser();

    $service->submitAttempt($service->startAttempt($loser, $quiz), []);
    $service->submitAttempt($service->startAttempt($winner, $quiz), allCorrect($quiz, $service->startAttempt($winner, $quiz)));

    // Reload to get fresh attempt
    $quiz->refresh();
    $winnerAttempt = $service->startAttempt($winner, $quiz);
    $service->submitAttempt($winnerAttempt, allCorrect($quiz, $winnerAttempt));

    $board = $service->leaderboard($quiz);
    expect($board->first()['best_score'])->toBeGreaterThanOrEqual($board->last()['best_score']);
});

// ── Admin ──────────────────────────────────────────────────────────────────────

test('admin can access admin panel', function () {
    $admin = makeUser(admin: true);
    $this->actingAs($admin)->get(route('admin.quizzes.index'))->assertOk();
});

test('non-admin gets 403 on admin panel', function () {
    $user = makeUser();
    $this->actingAs($user)->get(route('admin.quizzes.index'))->assertForbidden();
});

test('admin can create a quiz', function () {
    $admin = makeUser(admin: true);
    $this->actingAs($admin)->post(route('admin.quizzes.store'), [
        'title'                 => 'My New Quiz',
        'description'           => 'Test',
        'time_limit'            => 300,
        'questions_per_attempt' => 10,
        'max_attempts'          => 3,
        'randomize_questions'   => true,
        'randomize_answers'     => true,
        'is_published'          => true,
    ])->assertRedirect();

    expect(Quiz::where('title', 'My New Quiz')->exists())->toBeTrue();
});

test('admin can soft-delete a quiz', function () {
    $admin = makeUser(admin: true);
    $quiz  = makeQuiz();

    $this->actingAs($admin)
         ->delete(route('admin.quizzes.destroy', $quiz))
         ->assertRedirect();

    expect(Quiz::withTrashed()->find($quiz->id)->trashed())->toBeTrue();
});
