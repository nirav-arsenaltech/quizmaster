<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'body'                 => ['required', 'string'],
            'explanation'          => ['nullable', 'string'],
            'order'                => ['nullable', 'integer', 'min:0'],
            'answers'              => ['required', 'array', 'min:2', 'max:6'],
            'answers.*.body'       => ['required', 'string'],
            'answers.*.is_correct' => ['boolean'],
            'answers.*.id'         => ['nullable', 'integer'],
        ];
    }

    /**
     * Ensure exactly one answer is marked as correct.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $answers    = $this->input('answers', []);
            $numCorrect = collect($answers)
                ->filter(fn ($a) => ! empty($a['is_correct']))
                ->count();

            if ($numCorrect !== 1) {
                $v->errors()->add('answers', 'Exactly one answer must be marked as correct.');
            }
        });
    }
}
