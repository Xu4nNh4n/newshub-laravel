<?php

namespace Database\Factories;

use App\Enums\PostRequestPriority;
use App\Enums\PostRequestStatus;
use App\Enums\PostRequestType;
use App\Models\Post;
use App\Models\PostRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PostRequest>
 */
class PostRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'post_id' => Post::factory()->published(),
            'author_id' => User::factory(),
            'type' => PostRequestType::Correction,
            'priority' => PostRequestPriority::Normal,
            'reason' => fake()->sentence(),
            'notes' => fake()->paragraph(),
            'status' => PostRequestStatus::Pending,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes): array => [
            'pending_post_id' => $attributes['post_id'] ?? Post::factory()->published(),
            'status' => PostRequestStatus::Pending,
            'admin_notes' => null,
            'handled_by' => null,
            'handled_at' => null,
        ]);
    }

    public function removal(): static
    {
        return $this->state(fn (): array => ['type' => PostRequestType::Removal]);
    }
}
