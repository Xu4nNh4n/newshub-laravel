<?php

namespace Database\Factories;

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'author_id' => User::factory(),
            'category_id' => Category::factory(),
            'title' => fake()->sentence(8),
            'slug' => fake()->unique()->slug(),
            'summary' => fake()->paragraph(),
            'content' => fake()->paragraphs(3, true),
            'show_thumbnail_in_post' => true,
            'status' => PostStatus::Draft,
            'is_featured' => false,
            'published_at' => null,
        ];
    }

    public function pendingReview(): static
    {
        return $this->state(fn (): array => ['status' => PostStatus::PendingReview]);
    }

    public function rejected(): static
    {
        return $this->state(fn (): array => [
            'status' => PostStatus::Rejected,
            'rejection_reason' => 'Nội dung cần chỉnh sửa.',
        ]);
    }

    public function published(): static
    {
        return $this->state(fn (): array => [
            'status' => PostStatus::Published,
            'published_at' => now()->subMinute(),
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn (): array => [
            'status' => PostStatus::Published,
            'published_at' => now()->addDay(),
        ]);
    }
}
