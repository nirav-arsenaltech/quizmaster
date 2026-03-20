<?php

namespace App\Http\Requests;

use App\Models\QuizAttempt;
use Illuminate\Foundation\Http\FormRequest;

class SubmitQuizRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var QuizAttempt $attempt */
        $attempt = $this->route('attempt');

        return $attempt
            && $attempt->user_id === $this->user()->id
            && ! $attempt->isCompleted();
    }

    public function rules(): array
    {
        /** @var QuizAttempt $attempt */
        $attempt     = $this->route('attempt');
        $questionIds = $attempt->question_order ?? [];

        $rules = [
            'timed_out' => ['boolean'],
        ];

        foreach ($questionIds as $questionId) {
            $rules["answers.{$questionId}"] = [
                'nullable',
                'integer',
                'exists:answers,id',
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'answers.*.exists'  => 'One or more selected answers are invalid.',
            'answers.*.integer' => 'Answer values must be integers.',
        ];
    }
}
