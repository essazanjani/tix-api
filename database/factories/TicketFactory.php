<?php

namespace Database\Factories;

use App\Enums\TicketStatusEnum;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
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
            'admin_id' => null,
            'parent_id' => null,
            'subject' => fake()->sentence(),
            'message' => fake()->paragraph(),
            'status' => TicketStatusEnum::OPEN->value,
            'seen_at' => null,
        ];
    }


    public function reply(Ticket $parent): self
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent->id,
            'user_id' => $parent->user_id,
            'admin_id' => null,
            'subject' => $parent->subject,
            'status' => TicketStatusEnum::PENDING->value
        ]);
    }
}
