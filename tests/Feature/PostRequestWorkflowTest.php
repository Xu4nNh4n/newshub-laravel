<?php

namespace Tests\Feature;

use App\Enums\PostRequestStatus;
use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Models\Post;
use App\Models\PostRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PostRequestWorkflowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_author_can_request_a_correction_for_own_published_post(): void
    {
        $author = User::factory()->create(['role' => UserRole::Author]);
        $post = Post::factory()->published()->create(['author_id' => $author]);

        $response = $this->actingAs($author)->post(route('author.posts.requests.store', $post), [
            'type' => 'correction',
            'priority' => 'urgent',
            'reason' => 'Số liệu trong bài chưa chính xác.',
            'notes' => 'Cần thay số liệu trong đoạn thứ hai.',
        ]);

        $postRequest = PostRequest::query()->firstOrFail();
        $response->assertRedirect()->assertSessionHas('status', 'Đã gửi yêu cầu tới Ban biên tập.');
        $this->assertSame($post->id, $postRequest->post_id);
        $this->assertSame($post->id, $postRequest->pending_post_id);
        $this->assertSame($author->id, $postRequest->author_id);
        $this->assertSame(PostRequestStatus::Pending, $postRequest->status);
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $author->id,
            'action' => 'post_request.created',
            'subject_id' => $postRequest->id,
        ]);
    }

    public function test_author_cannot_request_changes_for_another_authors_or_unpublished_post(): void
    {
        $author = User::factory()->create(['role' => UserRole::Author]);
        $otherPublishedPost = Post::factory()->published()->create();
        $ownDraft = Post::factory()->create(['author_id' => $author]);
        $payload = [
            'type' => 'removal',
            'priority' => 'normal',
            'reason' => 'Nội dung không còn phù hợp.',
        ];

        $this->actingAs($author)
            ->post(route('author.posts.requests.store', $otherPublishedPost), $payload)
            ->assertForbidden();
        $this->actingAs($author)
            ->post(route('author.posts.requests.store', $ownDraft), $payload)
            ->assertForbidden();

        $this->assertDatabaseCount('post_requests', 0);
    }

    public function test_author_cannot_create_a_second_pending_request_for_the_same_post(): void
    {
        $author = User::factory()->create(['role' => UserRole::Author]);
        $post = Post::factory()->published()->create(['author_id' => $author]);
        PostRequest::factory()->pending()->create([
            'post_id' => $post->id,
            'pending_post_id' => $post->id,
            'author_id' => $author->id,
        ]);

        $this->actingAs($author)
            ->from(route('author.posts.index'))
            ->post(route('author.posts.requests.store', $post), [
                'type' => 'removal',
                'priority' => 'normal',
                'reason' => 'Yêu cầu trùng.',
            ])
            ->assertRedirect(route('author.posts.index'))
            ->assertSessionHasErrors('post');

        $this->assertSame(1, $post->requests()->count());
    }

    public function test_author_can_withdraw_own_pending_post_to_draft(): void
    {
        $author = User::factory()->create(['role' => UserRole::Author]);
        $post = Post::factory()->pendingReview()->create(['author_id' => $author]);

        $response = $this->actingAs($author)->post(route('author.posts.withdraw', $post));

        $response->assertRedirect(route('author.posts.edit', $post))
            ->assertSessionHas('status', 'Đã rút bài về bản nháp.');
        $this->assertSame(PostStatus::Draft, $post->refresh()->status);
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $author->id,
            'action' => 'post.withdrawn',
            'subject_id' => $post->id,
        ]);
    }

    public function test_author_cannot_withdraw_another_authors_pending_post(): void
    {
        $author = User::factory()->create(['role' => UserRole::Author]);
        $post = Post::factory()->pendingReview()->create();

        $this->actingAs($author)->post(route('author.posts.withdraw', $post))->assertForbidden();

        $this->assertSame(PostStatus::PendingReview, $post->refresh()->status);
        $this->assertDatabaseMissing('activity_logs', [
            'action' => 'post.withdrawn',
            'subject_id' => $post->id,
        ]);
    }

    public function test_admin_approval_of_removal_request_hides_the_post(): void
    {
        $this->travelTo('2026-10-04 10:00:00');
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $author = User::factory()->create(['role' => UserRole::Author]);
        $post = Post::factory()->published()->create([
            'author_id' => $author,
            'is_featured' => true,
        ]);
        $postRequest = PostRequest::factory()->pending()->removal()->create([
            'post_id' => $post->id,
            'pending_post_id' => $post->id,
            'author_id' => $author->id,
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.post-requests.update', $postRequest), [
            'status' => 'approved',
            'admin_notes' => 'Đã xác minh yêu cầu gỡ bài.',
        ]);

        $response->assertRedirect()->assertSessionHas('status', 'Đã xử lý yêu cầu bài viết.');
        $this->assertSame(PostStatus::Hidden, $post->refresh()->status);
        $this->assertFalse($post->is_featured);
        $this->assertSame(PostRequestStatus::Approved, $postRequest->refresh()->status);
        $this->assertNull($postRequest->pending_post_id);
        $this->assertSame($admin->id, $postRequest->handled_by);
        $this->assertSame('2026-10-04 10:00:00', $postRequest->handled_at?->format('Y-m-d H:i:s'));
        $this->assertFalse(Post::query()->publiclyVisible()->whereKey($post)->exists());
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'post_request.approved',
            'subject_id' => $postRequest->id,
        ]);
    }

    public function test_admin_approval_of_correction_request_returns_post_to_draft(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $author = User::factory()->create(['role' => UserRole::Author]);
        $post = Post::factory()->published()->create(['author_id' => $author]);
        $postRequest = PostRequest::factory()->pending()->create([
            'post_id' => $post->id,
            'pending_post_id' => $post->id,
            'author_id' => $author->id,
        ]);

        $this->actingAs($admin)->patch(route('admin.post-requests.update', $postRequest), [
            'status' => 'approved',
        ])->assertRedirect();

        $this->assertSame(PostStatus::Draft, $post->refresh()->status);
        $this->assertNull($post->published_at);
        $this->assertSame(PostRequestStatus::Approved, $postRequest->refresh()->status);
    }

    public function test_admin_rejection_keeps_post_published_and_requires_notes(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $author = User::factory()->create(['role' => UserRole::Author]);
        $post = Post::factory()->published()->create(['author_id' => $author]);
        $postRequest = PostRequest::factory()->pending()->create([
            'post_id' => $post->id,
            'pending_post_id' => $post->id,
            'author_id' => $author->id,
        ]);

        $this->actingAs($admin)
            ->from(route('admin.posts.index'))
            ->patch(route('admin.post-requests.update', $postRequest), ['status' => 'rejected'])
            ->assertRedirect(route('admin.posts.index'))
            ->assertSessionHasErrors('admin_notes');

        $this->actingAs($admin)->patch(route('admin.post-requests.update', $postRequest), [
            'status' => 'rejected',
            'admin_notes' => 'Chưa đủ căn cứ để xử lý.',
        ])->assertRedirect();

        $this->assertSame(PostStatus::Published, $post->refresh()->status);
        $this->assertSame(PostRequestStatus::Rejected, $postRequest->refresh()->status);
        $this->assertSame('Chưa đủ căn cứ để xử lý.', $postRequest->admin_notes);
    }

    public function test_non_admin_cannot_handle_a_post_request(): void
    {
        $author = User::factory()->create(['role' => UserRole::Author]);
        $post = Post::factory()->published()->create(['author_id' => $author]);
        $postRequest = PostRequest::factory()->pending()->create([
            'post_id' => $post->id,
            'pending_post_id' => $post->id,
            'author_id' => $author->id,
        ]);

        $this->actingAs($author)->patch(route('admin.post-requests.update', $postRequest), [
            'status' => 'approved',
        ])->assertForbidden();

        $this->assertSame(PostRequestStatus::Pending, $postRequest->refresh()->status);
        $this->assertSame(PostStatus::Published, $post->refresh()->status);
    }

    public function test_author_can_cancel_own_pending_request(): void
    {
        $author = User::factory()->create(['role' => UserRole::Author]);
        $post = Post::factory()->published()->create(['author_id' => $author]);
        $postRequest = PostRequest::factory()->pending()->create([
            'post_id' => $post->id,
            'pending_post_id' => $post->id,
            'author_id' => $author->id,
        ]);

        $response = $this->actingAs($author)->delete(route('author.posts.requests.destroy', [$post, $postRequest]));

        $response->assertRedirect()->assertSessionHas('status', 'Đã hủy yêu cầu bài viết thành công.');
        $this->assertSame(PostRequestStatus::Cancelled, $postRequest->refresh()->status);
        $this->assertNull($postRequest->pending_post_id);
        $this->assertNotNull($postRequest->handled_at);
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $author->id,
            'action' => 'post_request.cancelled',
            'subject_id' => $postRequest->id,
        ]);
    }

    public function test_author_cannot_cancel_another_authors_request(): void
    {
        $author1 = User::factory()->create(['role' => UserRole::Author]);
        $author2 = User::factory()->create(['role' => UserRole::Author]);
        $post = Post::factory()->published()->create(['author_id' => $author1]);
        $postRequest = PostRequest::factory()->pending()->create([
            'post_id' => $post->id,
            'pending_post_id' => $post->id,
            'author_id' => $author1->id,
        ]);

        $this->actingAs($author2)
            ->delete(route('author.posts.requests.destroy', [$post, $postRequest]))
            ->assertForbidden();

        $this->assertSame(PostRequestStatus::Pending, $postRequest->refresh()->status);
        $this->assertSame($post->id, $postRequest->refresh()->pending_post_id);
    }

    public function test_author_cannot_cancel_already_handled_request(): void
    {
        $author = User::factory()->create(['role' => UserRole::Author]);
        $post = Post::factory()->published()->create(['author_id' => $author]);
        $postRequest = PostRequest::factory()->create([
            'post_id' => $post->id,
            'pending_post_id' => null,
            'author_id' => $author->id,
            'status' => PostRequestStatus::Approved,
        ]);

        $this->actingAs($author)
            ->delete(route('author.posts.requests.destroy', [$post, $postRequest]))
            ->assertForbidden();

        $this->assertSame(PostRequestStatus::Approved, $postRequest->refresh()->status);
    }
}
