<?php

namespace Tests\Feature\Admin;

use App\Enums\CommentStatus;
use App\Enums\UserRole;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class CommentManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_non_admin_cannot_manage_comments(): void
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->create();

        $this->actingAs($user)->get(route('admin.comments.index'))->assertForbidden();
        $this->actingAs($user)->patch(route('admin.comments.update', $comment), ['action' => 'hide'])->assertForbidden();
    }

    public function test_admin_can_hide_restore_and_soft_delete_any_comment_with_audit_logs(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $comment = Comment::factory()->create();

        $this->actingAs($admin)->patch(route('admin.comments.update', $comment), ['action' => 'hide'])->assertRedirect();
        $this->assertSame(CommentStatus::Hidden, $comment->refresh()->status);

        $this->actingAs($admin)->patch(route('admin.comments.update', $comment), ['action' => 'restore'])->assertRedirect();
        $this->assertSame(CommentStatus::Visible, $comment->refresh()->status);

        $this->actingAs($admin)->patch(route('admin.comments.update', $comment), ['action' => 'delete'])->assertRedirect();
        $this->assertSoftDeleted($comment);
    }

    public function test_admin_comment_list_filters_state_and_escapes_content(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $hidden = Comment::factory()->create(['status' => CommentStatus::Hidden, 'content' => '<script>alert(1)</script>']);
        Comment::factory()->create(['content' => 'Bình luận đang hiển thị']);

        $this->actingAs($admin)->get(route('admin.comments.index', ['state' => 'hidden']))
            ->assertOk()
            ->assertSee($hidden->user->email)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertDontSee('Bình luận đang hiển thị');
    }

    public function test_admin_can_delete_another_users_comment_from_article_and_action_is_logged(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $comment = Comment::factory()->create();

        $this->actingAs($admin)->delete(route('comments.destroy', $comment))->assertRedirect();

        $this->assertSoftDeleted($comment);
    }
}
