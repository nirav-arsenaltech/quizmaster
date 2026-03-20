<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQuestionRequest;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QuestionController extends Controller
{
    public function index(Quiz $quiz): View
    {
        $questions = $quiz->questions()
            ->withCount('answers')
            ->with('answers')
            ->orderBy('order')
            ->get();

        return view('admin.questions.index', compact('quiz', 'questions'));
    }

    public function create(Quiz $quiz): View
    {
        return view('admin.questions.form', [
            'quiz'     => $quiz,
            'question' => new Question(),
        ]);
    }

    public function store(StoreQuestionRequest $request, Quiz $quiz): RedirectResponse
    {
        DB::transaction(function () use ($request, $quiz) {
            $question = $quiz->questions()->create([
                'body'        => $request->body,
                'explanation' => $request->explanation,
                'order'       => $request->order ?? ($quiz->questions()->max('order') + 1),
            ]);

            foreach ($request->answers as $index => $answerData) {
                $question->answers()->create([
                    'body'       => $answerData['body'],
                    'is_correct' => ! empty($answerData['is_correct']),
                    'order'      => $index + 1,
                ]);
            }
        });

        return redirect()
            ->route('admin.quizzes.questions.index', $quiz)
            ->with('success', 'Question added.');
    }

    public function edit(Quiz $quiz, Question $question): View
    {
        $question->load('answers');
        return view('admin.questions.form', compact('quiz', 'question'));
    }

    public function update(StoreQuestionRequest $request, Quiz $quiz, Question $question): RedirectResponse
    {
        DB::transaction(function () use ($request, $question) {
            $question->update([
                'body'        => $request->body,
                'explanation' => $request->explanation,
                'order'       => $request->order ?? $question->order,
            ]);

            $incomingIds = collect($request->answers)->pluck('id')->filter()->toArray();
            $question->answers()->whereNotIn('id', $incomingIds)->delete();

            foreach ($request->answers as $index => $answerData) {
                $question->answers()->updateOrCreate(
                    ['id' => $answerData['id'] ?? null],
                    [
                        'body'       => $answerData['body'],
                        'is_correct' => ! empty($answerData['is_correct']),
                        'order'      => $index + 1,
                    ]
                );
            }
        });

        return redirect()
            ->route('admin.quizzes.questions.index', $quiz)
            ->with('success', 'Question updated.');
    }

    public function destroy(Quiz $quiz, Question $question): RedirectResponse
    {
        $question->delete();
        return back()->with('success', 'Question deleted.');
    }
}
