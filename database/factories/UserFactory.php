<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory {

    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array {
        return [
            'deleted' => 0,
            'user_first_name' => fake()->firstName(),
            'user_last_name' => $this->faker->lastName,
            'user_email' => fake()->unique()->safeEmail(),
            'user_password_hash' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'user_active' => 1,
            'user_account_type' => 1,
            'group' => 1,
        ];
    }
}
