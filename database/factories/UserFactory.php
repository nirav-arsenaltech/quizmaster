<?php
// database/factories/UserFactory.php
namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name'              => fake()->name(),
            'email'             => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => bcrypt('password'),
            'remember_token'    => Str::random(10),
            'is_admin'          => false,
        ];
    }

    public function admin(): static
    {
        return $this->state(['is_admin' => true]);
    }

    public function unverified(): static
    {
        return $this->state(['email_verified_at' => null]);
    }
}
