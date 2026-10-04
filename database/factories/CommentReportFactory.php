<?php

namespace Database\Factories;

use App\Enums\CommentReportReason;
use App\Enums\CommentReportStatus;
use App\Models\Comment;
use App\Models\CommentReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CommentReport>
 */
class CommentReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'comment_id' => Comment::factory(),
            'reporter_id' => User::factory(),
            'reason' => CommentReportReason::Spam,
            'description' => null,
            'status' => CommentReportStatus::Pending,
            'handled_by' => null,
            'handled_at' => null,
        ];
    }
}
