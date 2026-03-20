<?php

namespace Database\Factories;

use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuizFactory extends Factory
{
    protected $model = Quiz::class;

    public function definition(): array
    {
        return [
            'title'                 => fake()->sentence(4),
            'description'           => fake()->paragraph(),
            'time_limit'            => 300,
            'questions_per_attempt' => 10,
            'max_attempts'          => 3,
            'randomize_questions'   => false,
            'randomize_answers'     => false,
            'is_published'          => true,
        ];
    }

    public function published(): static
    {
        return $this->state(['is_published' => true]);
    }

    public function draft(): static
    {
        return $this->state(['is_published' => false]);
    }
}
