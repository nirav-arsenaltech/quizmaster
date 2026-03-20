<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Admin\QuizController as AdminQuizController;
use App\Http\Controllers\Admin\QuestionController as AdminQuestionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ResultsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ── Root redirect ─────────────────────────────────────────────────────────────
Route::get('/', fn () => redirect()->route('dashboard'));

// ── Guest only ────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])
         ->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
         ->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

// ── Authenticated ─────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
         ->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
         ->name('dashboard');

    // Quizzes
    Route::get('/quizzes', [QuizController::class, 'index'])
         ->name('quiz.index');

    Route::post('/quizzes/{quiz}/start', [QuizController::class, 'start'])
         ->name('quiz.start');

    Route::get('/attempts/{attempt}', [QuizController::class, 'take'])
         ->name('quiz.take');

    Route::post('/attempts/{attempt}/submit', [QuizController::class, 'submit'])
         ->name('quiz.submit');

    // Results
    Route::get('/results', [ResultsController::class, 'index'])
         ->name('results.index');

    Route::get('/results/{attempt}', [ResultsController::class, 'show'])
         ->name('results.show');

    // Leaderboard
    Route::get('/leaderboard', [LeaderboardController::class, 'index'])
         ->name('leaderboard');
});

// ── Admin (auth + admin middleware) ───────────────────────────────────────────
Route::middleware(['auth', 'admin'])
     ->prefix('admin')
     ->name('admin.')
     ->group(function () {

    // Quizzes CRUD
    Route::get('/quizzes',                  [AdminQuizController::class, 'index'])  ->name('quizzes.index');
    Route::get('/quizzes/create',           [AdminQuizController::class, 'create']) ->name('quizzes.create');
    Route::post('/quizzes',                 [AdminQuizController::class, 'store'])  ->name('quizzes.store');
    Route::get('/quizzes/{quiz}/edit',      [AdminQuizController::class, 'edit'])   ->name('quizzes.edit');
    Route::put('/quizzes/{quiz}',           [AdminQuizController::class, 'update']) ->name('quizzes.update');
    Route::delete('/quizzes/{quiz}',        [AdminQuizController::class, 'destroy'])->name('quizzes.destroy');
    Route::post('/quizzes/{id}/restore',    [AdminQuizController::class, 'restore'])->name('quizzes.restore');

    // Nested questions
    Route::get('/quizzes/{quiz}/questions',                    [AdminQuestionController::class, 'index'])  ->name('quizzes.questions.index');
    Route::get('/quizzes/{quiz}/questions/create',             [AdminQuestionController::class, 'create']) ->name('quizzes.questions.create');
    Route::post('/quizzes/{quiz}/questions',                   [AdminQuestionController::class, 'store'])  ->name('quizzes.questions.store');
    Route::get('/quizzes/{quiz}/questions/{question}/edit',    [AdminQuestionController::class, 'edit'])   ->name('quizzes.questions.edit');
    Route::put('/quizzes/{quiz}/questions/{question}',         [AdminQuestionController::class, 'update']) ->name('quizzes.questions.update');
    Route::delete('/quizzes/{quiz}/questions/{question}',      [AdminQuestionController::class, 'destroy'])->name('quizzes.questions.destroy');
});
