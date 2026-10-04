<?php

namespace Tests\Feature;

use App\Enums\CommentReportReason;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class CommentThreadTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guest_and_unverified_user_cannot_create_comment(): void
    {
        $post = Post::factory()->published()->create();

        $this->post(route('comments.store', $post), ['content' => 'Ý kiến'])
            ->assertRedirect(route('login'));

        $this->actingAs(User::factory()->unverified()->create())
            ->post(route('comments.store', $post), ['content' => 'Ý kiến'])
            ->assertRedirect(route('verification.notice'));

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_reply_to_a_reply_is_flattened_under_the_root_comment(): void
    {
        $post = Post::factory()->published()->create();
        $user = User::factory()->create();
        $root = Comment::factory()->for($post)->create();
        $firstReply = Comment::factory()->for($post)->create([
            'parent_id' => $root->id,
            'reply_to_id' => $root->id,
        ]);

        $response = $this->actingAs($user)->post(route('comments.store', $post), [
            'content' => 'Trả lời cấp tiếp theo',
            'reply_to_id' => $firstReply->id,
        ]);

        $response->assertRedirect()->assertSessionHas('status', 'Đã đăng bình luận.');
        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'parent_id' => $root->id,
            'reply_to_id' => $firstReply->id,
            'content' => 'Trả lời cấp tiếp theo',
        ]);
    }

    public function test_user_cannot_reply_to_a_comment_from_another_post(): void
    {
        $post = Post::factory()->published()->create();
        $otherComment = Comment::factory()->create();

        $response = $this->actingAs(User::factory()->create())->post(route('comments.store', $post), [
            'content' => 'Trả lời sai bài',
            'reply_to_id' => $otherComment->id,
        ]);

        $response->assertRedirect()->assertSessionHasErrors([
            'reply_to_id' => 'Bình luận được trả lời không thuộc bài viết này.',
        ]);
        $this->assertDatabaseMissing('comments', ['content' => 'Trả lời sai bài']);
    }

    public function test_user_cannot_edit_or_delete_another_users_comment(): void
    {
        $comment = Comment::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($otherUser)
            ->put(route('comments.update', $comment), ['content' => 'Nội dung bị sửa'])
            ->assertForbidden();
        $this->actingAs($otherUser)
            ->delete(route('comments.destroy', $comment))
            ->assertForbidden();

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => $comment->content,
            'deleted_at' => null,
        ]);
    }

    public function test_owner_can_update_and_soft_delete_comment(): void
    {
        $comment = Comment::factory()->create();

        $this->actingAs($comment->user)
            ->put(route('comments.update', $comment), ['content' => 'Nội dung đã cập nhật'])
            ->assertRedirect()
            ->assertSessionHas('status', 'Đã cập nhật bình luận.');
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'Nội dung đã cập nhật',
        ]);

        $this->actingAs($comment->user)
            ->delete(route('comments.destroy', $comment))
            ->assertRedirect()
            ->assertSessionHas('status', 'Đã xóa bình luận.');
        $this->assertSoftDeleted($comment);
    }

    public function test_comment_cannot_be_created_on_a_non_public_post(): void
    {
        $draft = Post::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('comments.store', $draft), ['content' => 'Không được lưu'])
            ->assertNotFound();

        $this->assertDatabaseMissing('comments', ['content' => 'Không được lưu']);
    }

    public function test_same_user_cannot_repeat_identical_comment_within_five_minutes(): void
    {
        $post = Post::factory()->published()->create();
        $user = User::factory()->create();
        Comment::factory()->for($post)->for($user)->create([
            'content' => 'Nội dung bị lặp',
            'created_at' => now()->subMinute(),
        ]);

        $this->actingAs($user)->post(route('comments.store', $post), [
            'content' => 'Nội dung bị lặp',
        ])->assertSessionHasErrors([
            'content' => 'Bạn vừa gửi nội dung bình luận này. Vui lòng chờ trước khi gửi lại.',
        ]);

        $this->assertSame(1, Comment::query()->where('content', 'Nội dung bị lặp')->count());
    }

    public function test_deleted_root_keeps_replies_visible_and_comment_content_is_escaped(): void
    {
        $post = Post::factory()->published()->create();
        $root = Comment::factory()->for($post)->create(['content' => 'Nội dung gốc']);
        Comment::factory()->for($post)->create([
            'parent_id' => $root->id,
            'reply_to_id' => $root->id,
            'content' => '<script>alert("xss")</script>',
        ]);
        $root->delete();

        $response = $this->get(route('news.show', $post->slug));

        $response
            ->assertSee('Bình luận này đã bị xóa hoặc ẩn.')
            ->assertSee('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert("xss")</script>', false);
    }

    public function test_reporter_can_only_report_a_comment_once(): void
    {
        $comment = Comment::factory()->create();
        $reporter = User::factory()->create();

        $this->actingAs($reporter)->post(route('comment-reports.store', $comment), [
            'reason' => CommentReportReason::Spam->value,
        ])->assertRedirect()->assertSessionHas('status');

        $this->actingAs($reporter)->post(route('comment-reports.store', $comment), [
            'reason' => CommentReportReason::Irrelevant->value,
        ])->assertRedirect()->assertSessionHasErrors([
            'reason' => 'Bạn đã báo cáo bình luận này trước đó.',
        ]);

        $this->assertDatabaseCount('comment_reports', 1);
    }

    public function test_user_cannot_report_own_comment_and_other_reason_requires_description(): void
    {
        $comment = Comment::factory()->create();

        $this->actingAs($comment->user)
            ->post(route('comment-reports.store', $comment), [
                'reason' => CommentReportReason::Spam->value,
            ])
            ->assertForbidden();

        $this->actingAs(User::factory()->create())
            ->post(route('comment-reports.store', $comment), [
                'reason' => CommentReportReason::Other->value,
            ])
            ->assertSessionHasErrors('description');

        $this->assertDatabaseCount('comment_reports', 0);
    }
}
