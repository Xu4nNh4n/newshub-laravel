<?php

namespace Tests\Feature;

use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PostWorkflowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_verified_author_can_create_draft_with_tags(): void
    {
        $user = User::factory()->create(['role' => UserRole::Author]);
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();

        $response = $this->actingAs($user)->post(route('author.posts.store'), [
            'title' => 'Bài viết thử nghiệm',
            'slug' => '',
            'summary' => 'Tóm tắt',
            'content' => 'Nội dung đầy đủ',
            'category_id' => $category->id,
            'tag_ids' => [$tag->id],
        ]);

        $post = Post::query()->firstOrFail();
        $response->assertRedirect(route('author.posts.edit', $post));
        $this->assertSame($user->id, $post->author_id);
        $this->assertSame(PostStatus::Draft, $post->status);
        $this->assertSame('bai-viet-thu-nghiem', $post->slug);
        $this->assertDatabaseHas('post_tag', ['post_id' => $post->id, 'tag_id' => $tag->id]);
    }

    public function test_author_can_create_post_with_normalized_dynamic_tags(): void
    {
        $author = User::factory()->create(['role' => UserRole::Author]);
        $category = Category::factory()->create();
        $existingTag = Tag::factory()->create();

        $response = $this->actingAs($author)->post(route('author.posts.store'), [
            'title' => 'Bài viết có thẻ động',
            'slug' => '',
            'content' => '<p>Nội dung bài viết.</p>',
            'category_id' => $category->id,
            'tag_ids' => [$existingTag->id],
            'tag_names' => [' OpenAI o3 ', '<b>Triển lãm CES 2026</b>', 'OpenAI o3'],
        ]);

        $post = Post::query()->where('title', 'Bài viết có thẻ động')->firstOrFail();
        $response->assertRedirect(route('author.posts.edit', $post));
        $this->assertDatabaseHas('tags', ['name' => 'OpenAI o3', 'slug' => 'openai-o3']);
        $this->assertDatabaseHas('tags', ['name' => 'Triển lãm CES 2026', 'slug' => 'trien-lam-ces-2026']);
        $this->assertSame(
            [$existingTag->id, ...Tag::query()->whereIn('slug', ['openai-o3', 'trien-lam-ces-2026'])->pluck('id')->all()],
            $post->tags()->orderBy('tags.id')->pluck('tags.id')->all(),
        );
        $this->assertSame(3, $post->tags()->count());
    }

    public function test_author_can_add_a_dynamic_tag_when_updating_a_post(): void
    {
        $author = User::factory()->create(['role' => UserRole::Author]);
        $post = Post::factory()->create([
            'author_id' => $author,
            'show_thumbnail_in_post' => false,
        ]);

        $response = $this->actingAs($author)->put(route('author.posts.update', $post), [
            'title' => $post->title,
            'slug' => $post->slug,
            'content' => '<p>Nội dung cập nhật.</p>',
            'category_id' => $post->category_id,
            'tag_names' => ['Tin nóng 2026'],
        ]);

        $tag = Tag::query()->where('slug', 'tin-nong-2026')->firstOrFail();
        $response->assertRedirect();
        $this->assertDatabaseHas('post_tag', ['post_id' => $post->id, 'tag_id' => $tag->id]);
        $this->assertFalse($post->refresh()->show_thumbnail_in_post);
    }

    public function test_post_persists_thumbnail_visibility_choice_and_sanitized_content(): void
    {
        $author = User::factory()->create(['role' => UserRole::Author]);
        $category = Category::factory()->create();

        $this->actingAs($author)->post(route('author.posts.store'), [
            'title' => 'Bài viết HTML an toàn',
            'slug' => '',
            'content' => '<h2 onclick="alert(1)">Tiêu đề</h2><script>alert(2)</script>',
            'category_id' => $category->id,
            'show_thumbnail_in_post' => '0',
        ])->assertRedirect();

        $post = Post::query()->where('title', 'Bài viết HTML an toàn')->firstOrFail();
        $this->assertFalse($post->show_thumbnail_in_post);
        $this->assertSame('<h2>Tiêu đề</h2>', $post->content);
    }

    public function test_author_cannot_update_another_authors_post(): void
    {
        $author = User::factory()->create(['role' => UserRole::Author]);
        $otherPost = Post::factory()->create();

        $this->actingAs($author)->get(route('author.posts.edit', $otherPost))->assertForbidden();
    }

    public function test_author_can_submit_own_draft_for_review(): void
    {
        $user = User::factory()->create(['role' => UserRole::Author]);
        $post = Post::factory()->create(['author_id' => $user]);

        $response = $this->actingAs($user)->post(route('author.posts.submit', $post));

        $response->assertRedirect(route('author.posts.index'));
        $this->assertSame(PostStatus::PendingReview, $post->refresh()->status);
        $this->assertDatabaseHas('activity_logs', ['action' => 'post.submitted', 'subject_id' => $post->id]);
    }

    public function test_author_can_filter_own_posts_by_workflow_status(): void
    {
        $author = User::factory()->create(['role' => UserRole::Author]);
        Post::factory()->create(['author_id' => $author, 'title' => 'Bản nháp cần thấy']);
        Post::factory()->pendingReview()->create(['author_id' => $author, 'title' => 'Bài chờ duyệt cần ẩn']);

        $this->actingAs($author)->get(route('author.posts.index', ['status' => PostStatus::Draft->value]))
            ->assertOk()
            ->assertSee('Bản nháp cần thấy')
            ->assertDontSee('Bài chờ duyệt cần ẩn');
    }

    public function test_author_cannot_edit_or_resubmit_pending_post(): void
    {
        $author = User::factory()->create(['role' => UserRole::Author]);
        $post = Post::factory()->pendingReview()->create(['author_id' => $author]);

        $this->actingAs($author)->get(route('author.posts.edit', $post))->assertForbidden();
        $this->actingAs($author)->post(route('author.posts.submit', $post))->assertForbidden();
    }

    public function test_admin_can_reject_pending_post_with_reason(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $post = Post::factory()->pendingReview()->create();

        $response = $this->actingAs($admin)->post(route('admin.post-reviews.reject', $post), [
            'reason' => 'Cần bổ sung nguồn tin.',
        ]);

        $response->assertRedirect();
        $this->assertSame(PostStatus::Rejected, $post->refresh()->status);
        $this->assertSame('Cần bổ sung nguồn tin.', $post->rejection_reason);
        $this->assertDatabaseHas('activity_logs', ['action' => 'post.rejected', 'subject_id' => $post->id]);
    }

    public function test_admin_can_schedule_approved_post_without_publishing_it_early(): void
    {
        $this->travelTo('2026-09-27 10:00:00');
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $post = Post::factory()->pendingReview()->create();

        $response = $this->actingAs($admin)->post(route('admin.post-reviews.approve', $post), [
            'published_at' => '2026-09-28 10:00:00',
        ]);

        $response->assertRedirect();
        $this->assertSame(PostStatus::Published, $post->refresh()->status);
        $this->assertFalse(Post::publiclyVisible()->whereKey($post)->exists());
        $this->assertDatabaseHas('activity_logs', ['action' => 'post.approved', 'subject_id' => $post->id]);
    }

    public function test_admin_can_publish_a_new_post_directly(): void
    {
        $this->travelTo('2026-10-04 09:00:00');
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->post(route('author.posts.store'), [
            'title' => 'Tin xuất bản trực tiếp',
            'slug' => '',
            'content' => 'Nội dung đầy đủ',
            'category_id' => $category->id,
            'action' => 'publish',
        ]);

        $post = Post::query()->where('title', 'Tin xuất bản trực tiếp')->firstOrFail();
        $response->assertRedirect(route('author.posts.index'))
            ->assertSessionHas('status', 'Đã xuất bản bài viết thành công.');
        $this->assertSame(PostStatus::Published, $post->status);
        $this->assertSame('2026-10-04 09:00:00', $post->published_at?->format('Y-m-d H:i:s'));
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'post.published',
            'subject_id' => $post->id,
        ]);
    }

    public function test_author_publish_action_is_saved_as_a_draft(): void
    {
        $author = User::factory()->create(['role' => UserRole::Author]);
        $category = Category::factory()->create();

        $response = $this->actingAs($author)->post(route('author.posts.store'), [
            'title' => 'Bài vẫn cần duyệt',
            'slug' => '',
            'content' => 'Nội dung đầy đủ',
            'category_id' => $category->id,
            'action' => 'publish',
        ]);

        $post = Post::query()->where('title', 'Bài vẫn cần duyệt')->firstOrFail();
        $response->assertRedirect(route('author.posts.edit', $post));
        $this->assertSame(PostStatus::Draft, $post->status);
        $this->assertNull($post->published_at);
        $this->assertDatabaseMissing('activity_logs', [
            'action' => 'post.published',
            'subject_id' => $post->id,
        ]);
    }

    public function test_admin_can_quick_publish_only_their_own_draft(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $ownPost = Post::factory()->create(['author_id' => $admin]);
        $otherPost = Post::factory()->create();

        $this->actingAs($admin)->post(route('author.posts.publish', $ownPost))
            ->assertRedirect(route('author.posts.index'));
        $this->assertSame(PostStatus::Published, $ownPost->refresh()->status);

        $this->actingAs($admin)->post(route('author.posts.publish', $otherPost))->assertForbidden();
        $this->assertSame(PostStatus::Draft, $otherPost->refresh()->status);
    }
}
