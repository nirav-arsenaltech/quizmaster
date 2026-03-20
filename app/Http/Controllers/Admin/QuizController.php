<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQuizRequest;
use App\Models\Quiz;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function index(): View
    {
        $quizzes = Quiz::withCount('questions')
            ->withTrashed()
            ->latest()
            ->paginate(15);

        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function create(): View
    {
        return view('admin.quizzes.form', ['quiz' => new Quiz()]);
    }

    public function store(StoreQuizRequest $request): RedirectResponse
    {
        $quiz = Quiz::create($this->prepare($request->validated()));

        return redirect()
            ->route('admin.quizzes.questions.index', $quiz)
            ->with('success', 'Quiz created! Now add some questions.');
    }

    public function edit(Quiz $quiz): View
    {
        return view('admin.quizzes.form', compact('quiz'));
    }

    public function update(StoreQuizRequest $request, Quiz $quiz): RedirectResponse
    {
        $quiz->update($this->prepare($request->validated()));

        return redirect()
            ->route('admin.quizzes.index')
            ->with('success', 'Quiz updated.');
    }

    public function destroy(Quiz $quiz): RedirectResponse
    {
        $quiz->delete();
        return back()->with('success', 'Quiz archived.');
    }

    public function restore(int $id): RedirectResponse
    {
        Quiz::withTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Quiz restored.');
    }

    private function prepare(array $data): array
    {
        $data['randomize_questions'] = $data['randomize_questions'] ?? false;
        $data['randomize_answers']   = $data['randomize_answers'] ?? false;
        $data['is_published']        = $data['is_published'] ?? false;
        return $data;
    }
}
