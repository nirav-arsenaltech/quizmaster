<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition(): array
    {
        return [
            'quiz_id'     => Quiz::factory(),
            'body'        => fake()->sentence() . '?',
            'order'       => fake()->numberBetween(1, 100),
            'explanation' => fake()->optional()->sentence(),
        ];
    }
}
