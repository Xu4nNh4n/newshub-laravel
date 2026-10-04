<?php

namespace Database\Factories;

use App\Enums\AuthorApplicationStatus;
use App\Enums\UserRole;
use App\Models\AuthorApplication;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AuthorApplication>
 */
class AuthorApplicationFactory extends Factory
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
            'pending_user_id' => fn (array $attributes): int => $attributes['user_id'],
            'category_id' => Category::factory(),
            'bio' => fake()->paragraph(),
            'sample_title' => fake()->sentence(8),
            'sample_content' => fake()->paragraphs(4, true),
            'status' => AuthorApplicationStatus::Pending,
            'rejection_reason' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (): array => [
            'pending_user_id' => null,
            'status' => AuthorApplicationStatus::Approved,
            'reviewed_by' => User::factory()->state(['role' => UserRole::Admin]),
            'reviewed_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (): array => [
            'pending_user_id' => null,
            'status' => AuthorApplicationStatus::Rejected,
            'rejection_reason' => 'Hồ sơ cần bổ sung thêm kinh nghiệm.',
            'reviewed_by' => User::factory()->state(['role' => UserRole::Admin]),
            'reviewed_at' => now(),
        ]);
    }
}
