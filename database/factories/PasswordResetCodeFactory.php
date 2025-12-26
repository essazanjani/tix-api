<?php

namespace Database\Factories;

use App\Models\PasswordResetCode;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PasswordResetCode>
 */
class PasswordResetCodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'code' => rand(10000, 99999),
            'expires_at' => now()->addMinutes(5)
        ];
    }
}
