<?php

namespace Database\Factories;

use App\Http\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Http\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->firstName,
            'surname' => $this->faker->lastName,
            // `username` is unique in the schema; faker's userName collides on
            // larger batches (seeder makes 30 + tests add more), so force it
            // unique to keep the suite deterministic.
            'username' => $this->faker->unique()->userName,
            'password' => bcrypt('test123'),
            'role_id' => 2,
            'city' => '-',
            'country' => $this->faker->country,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }
}
