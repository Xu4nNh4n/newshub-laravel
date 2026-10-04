<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\ActivityLog;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ActivityLogViewerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_non_admin_cannot_view_activity_logs(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.activity-logs.index'))->assertForbidden();
    }

    public function test_admin_can_filter_activity_logs_by_action_and_actor(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $otherAdmin = User::factory()->create(['role' => UserRole::Admin]);
        $post = Post::factory()->create();
        ActivityLog::query()->create([
            'user_id' => $admin->id,
            'action' => 'post.hidden',
            'subject_type' => Post::class,
            'subject_id' => $post->id,
            'description' => 'Nhật ký cần hiển thị.',
        ]);
        ActivityLog::query()->create([
            'user_id' => $otherAdmin->id,
            'action' => 'post.archived',
            'subject_type' => Post::class,
            'subject_id' => $post->id,
            'description' => 'Nhật ký không phù hợp.',
        ]);

        $this->actingAs($admin)->get(route('admin.activity-logs.index', [
            'action' => 'post.hidden',
            'user_id' => $admin->id,
        ]))
            ->assertOk()
            ->assertSee('Nhật ký cần hiển thị.')
            ->assertDontSee('Nhật ký không phù hợp.');
    }
}
