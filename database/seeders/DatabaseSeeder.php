<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin user ─────────────────────────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'admin@quizmaster.test'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ]
        );

        // ── Demo user ──────────────────────────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'user@quizmaster.test'],
            [
                'name'     => 'Demo User',
                'password' => Hash::make('password'),
                'is_admin' => false,
            ]
        );

        // ── Quiz (idempotent) ──────────────────────────────────────────────────
        if (Quiz::count() > 0) {
            return;
        }

        $quiz = Quiz::create([
            'title'                 => 'General Knowledge Challenge',
            'description'           => 'Test your knowledge across science, history, geography and more. 10 questions, 5 minutes.',
            'time_limit'            => 300,
            'questions_per_attempt' => 10,
            'max_attempts'          => 3,
            'randomize_questions'   => true,
            'randomize_answers'     => true,
            'is_published'          => true,
        ]);

        $questions = [
            [
                'body'        => 'What is the chemical symbol for gold?',
                'explanation' => 'Au comes from the Latin word "aurum", meaning gold.',
                'answers'     => [
                    ['body' => 'Go',  'is_correct' => false],
                    ['body' => 'Au',  'is_correct' => true],
                    ['body' => 'Ag',  'is_correct' => false],
                    ['body' => 'Gd',  'is_correct' => false],
                ],
            ],
            [
                'body'        => 'Which planet is known as the Red Planet?',
                'explanation' => 'Mars appears red due to iron oxide (rust) on its surface.',
                'answers'     => [
                    ['body' => 'Venus',   'is_correct' => false],
                    ['body' => 'Jupiter', 'is_correct' => false],
                    ['body' => 'Mars',    'is_correct' => true],
                    ['body' => 'Saturn',  'is_correct' => false],
                ],
            ],
            [
                'body'        => 'How many sides does a hexagon have?',
                'explanation' => 'Hex- is Greek for 6.',
                'answers'     => [
                    ['body' => '5', 'is_correct' => false],
                    ['body' => '6', 'is_correct' => true],
                    ['body' => '7', 'is_correct' => false],
                    ['body' => '8', 'is_correct' => false],
                ],
            ],
            [
                'body'        => 'Who wrote Romeo and Juliet?',
                'explanation' => 'Shakespeare wrote the play around 1594–1596.',
                'answers'     => [
                    ['body' => 'Charles Dickens',     'is_correct' => false],
                    ['body' => 'William Shakespeare', 'is_correct' => true],
                    ['body' => 'Jane Austen',         'is_correct' => false],
                    ['body' => 'Mark Twain',          'is_correct' => false],
                ],
            ],
            [
                'body'        => 'What is the largest ocean on Earth?',
                'explanation' => 'The Pacific Ocean covers more than 30% of Earth\'s surface.',
                'answers'     => [
                    ['body' => 'Atlantic Ocean', 'is_correct' => false],
                    ['body' => 'Indian Ocean',   'is_correct' => false],
                    ['body' => 'Arctic Ocean',   'is_correct' => false],
                    ['body' => 'Pacific Ocean',  'is_correct' => true],
                ],
            ],
            [
                'body'        => 'In which year did World War I begin?',
                'explanation' => 'WWI began in 1914, triggered by the assassination of Archduke Franz Ferdinand.',
                'answers'     => [
                    ['body' => '1912', 'is_correct' => false],
                    ['body' => '1914', 'is_correct' => true],
                    ['body' => '1916', 'is_correct' => false],
                    ['body' => '1918', 'is_correct' => false],
                ],
            ],
            [
                'body'        => 'What is the approximate speed of light in a vacuum?',
                'explanation' => 'The speed of light is approximately 299,792 km/s, commonly rounded to 300,000 km/s.',
                'answers'     => [
                    ['body' => '300,000 km/s', 'is_correct' => true],
                    ['body' => '150,000 km/s', 'is_correct' => false],
                    ['body' => '450,000 km/s', 'is_correct' => false],
                    ['body' => '30,000 km/s',  'is_correct' => false],
                ],
            ],
            [
                'body'        => 'Which country is home to the kangaroo?',
                'explanation' => 'Kangaroos are native to Australia.',
                'answers'     => [
                    ['body' => 'New Zealand',  'is_correct' => false],
                    ['body' => 'South Africa', 'is_correct' => false],
                    ['body' => 'Brazil',       'is_correct' => false],
                    ['body' => 'Australia',    'is_correct' => true],
                ],
            ],
            [
                'body'        => 'What is the smallest prime number?',
                'explanation' => '2 is the smallest and the only even prime number.',
                'answers'     => [
                    ['body' => '0', 'is_correct' => false],
                    ['body' => '1', 'is_correct' => false],
                    ['body' => '2', 'is_correct' => true],
                    ['body' => '3', 'is_correct' => false],
                ],
            ],
            [
                'body'        => 'What gas do plants primarily absorb during photosynthesis?',
                'explanation' => 'Plants absorb carbon dioxide (CO₂) and release oxygen during photosynthesis.',
                'answers'     => [
                    ['body' => 'Oxygen',         'is_correct' => false],
                    ['body' => 'Nitrogen',        'is_correct' => false],
                    ['body' => 'Carbon Dioxide',  'is_correct' => true],
                    ['body' => 'Hydrogen',        'is_correct' => false],
                ],
            ],
        ];

        foreach ($questions as $order => $qData) {
            $answers = $qData['answers'];
            unset($qData['answers']);

            $question = $quiz->questions()->create(
                array_merge($qData, ['order' => $order + 1])
            );

            foreach ($answers as $ansOrder => $ans) {
                $question->answers()->create(
                    array_merge($ans, ['order' => $ansOrder + 1])
                );
            }
        }
    }
}
