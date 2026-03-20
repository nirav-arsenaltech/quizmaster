<?php

namespace Database\Factories;

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnswerFactory extends Factory
{
    protected $model = Answer::class;

    public function definition(): array
    {
        return [
            'question_id' => Question::factory(),
            'body'        => fake()->sentence(3),
            'is_correct'  => false,
            'order'       => fake()->numberBetween(1, 6),
        ];
    }

    public function correct(): static
    {
        return $this->state(['is_correct' => true]);
    }
}
