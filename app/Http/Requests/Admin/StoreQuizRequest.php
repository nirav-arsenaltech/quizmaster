<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuizRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'title'                 => ['required', 'string', 'max:255'],
            'description'           => ['nullable', 'string'],
            'time_limit'            => ['required', 'integer', 'min:0', 'max:7200'],
            'questions_per_attempt' => ['required', 'integer', 'min:1', 'max:100'],
            'max_attempts'          => ['required', 'integer', 'min:1', 'max:10'],
            'randomize_questions'   => ['boolean'],
            'randomize_answers'     => ['boolean'],
            'is_published'          => ['boolean'],
        ];
    }
}
