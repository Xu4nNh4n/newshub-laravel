<?php

namespace Tests\Feature\Admin;

use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PostModerationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_non_admin_cannot_manage_posts(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->published()->create();

        $this->actingAs($user)->get(route('admin.posts.index'))->assertForbidden();
        $this->actingAs($user)->patch(route('admin.posts.update', $post), [
            'action' => 'hide',
        ])->assertForbidden();
    }

    public function test_admin_can_filter_posts_and_hide_a_published_post(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $target = Post::factory()->published()->create([
            'title' => 'Bài cần kiểm soát',
            'is_featured' => true,
        ]);
        Post::factory()->pendingReview()->create(['title' => 'Bài không phù hợp']);

        $this->actingAs($admin)->get(route('admin.posts.index', [
            'q' => 'kiểm soát',
            'status' => PostStatus::Published->value,
        ]))->assertOk()->assertSee($target->title)->assertDontSee('Bài không phù hợp');

        $response = $this->actingAs($admin)->patch(route('admin.posts.update', $target), [
            'action' => 'hide',
        ]);

        $response->assertRedirect()->assertSessionHas('status');
        $this->assertSame(PostStatus::Hidden, $target->refresh()->status);
        $this->assertFalse($target->is_featured);
        $this->assertFalse(Post::publiclyVisible()->whereKey($target)->exists());
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'post.hidden',
            'subject_id' => $target->id,
        ]);
    }

    public function test_admin_can_filter_posts_by_category_and_author(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $author = User::factory()->create(['role' => UserRole::Author]);
        $otherAuthor = User::factory()->create(['role' => UserRole::Author]);
        $category = Category::factory()->create();
        $otherCategory = Category::factory()->create();
        $target = Post::factory()->published()->create([
            'author_id' => $author,
            'category_id' => $category,
            'title' => 'Đúng tác giả và chuyên mục',
        ]);
        Post::factory()->published()->create([
            'author_id' => $otherAuthor,
            'category_id' => $category,
            'title' => 'Sai tác giả',
        ]);
        Post::factory()->published()->create([
            'author_id' => $author,
            'category_id' => $otherCategory,
            'title' => 'Sai chuyên mục',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.posts.index', [
                'category_id' => $category->id,
                'author_id' => $author->id,
            ]))
            ->assertOk()
            ->assertSee($target->title)
            ->assertDontSee('Sai tác giả')
            ->assertDontSee('Sai chuyên mục')
            ->assertViewHas('categories', fn ($categories): bool => $categories->contains($category))
            ->assertViewHas('authors', fn ($authors): bool => $authors->contains($author));
    }

    public function test_admin_can_archive_and_restore_an_article(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $post = Post::factory()->published()->create();

        $this->actingAs($admin)->patch(route('admin.posts.update', $post), [
            'action' => 'archive',
        ])->assertRedirect();
        $this->assertSame(PostStatus::Archived, $post->refresh()->status);

        $this->actingAs($admin)->patch(route('admin.posts.update', $post), [
            'action' => 'restore',
        ])->assertRedirect();

        $this->assertSame(PostStatus::Published, $post->refresh()->status);
        $this->assertTrue(Post::publiclyVisible()->whereKey($post)->exists());
        $this->assertDatabaseHas('activity_logs', ['action' => 'post.archived', 'subject_id' => $post->id]);
        $this->assertDatabaseHas('activity_logs', ['action' => 'post.restored', 'subject_id' => $post->id]);
    }

    public function test_admin_can_feature_and_unfeature_only_published_articles(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $published = Post::factory()->published()->create();
        $draft = Post::factory()->create();

        $this->actingAs($admin)->patch(route('admin.posts.update', $published), [
            'action' => 'feature',
        ])->assertRedirect();
        $this->assertTrue($published->refresh()->is_featured);

        $this->actingAs($admin)->patch(route('admin.posts.update', $published), [
            'action' => 'unfeature',
        ])->assertRedirect();
        $this->assertFalse($published->refresh()->is_featured);

        $this->actingAs($admin)
            ->from(route('admin.posts.index'))
            ->patch(route('admin.posts.update', $draft), ['action' => 'feature'])
            ->assertRedirect(route('admin.posts.index'))
            ->assertSessionHasErrors('action');

        $this->assertFalse($draft->refresh()->is_featured);
        $this->assertDatabaseMissing('activity_logs', [
            'action' => 'post.featured',
            'subject_id' => $draft->id,
        ]);
    }

    public function test_admin_can_quickly_toggle_featured_state_and_it_is_audited(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $published = Post::factory()->published()->create(['title' => 'Bài ghim nhanh']);

        $this->actingAs($admin)
            ->patch(route('admin.posts.toggle-featured', $published))
            ->assertRedirect()
            ->assertSessionHas('status', 'Đã ghim bài viết làm Tiêu điểm trang chủ.');

        $this->assertTrue($published->refresh()->is_featured);
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'post.featured',
            'subject_id' => $published->id,
            'description' => 'Ghim tiêu điểm bài viết: Bài ghim nhanh',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.posts.toggle-featured', $published))
            ->assertRedirect()
            ->assertSessionHas('status', 'Đã bỏ ghim tiêu điểm bài viết.');

        $this->assertFalse($published->refresh()->is_featured);
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'post.unfeatured',
            'subject_id' => $published->id,
        ]);
    }

    public function test_quick_feature_toggle_rejects_non_admin_and_unpublished_posts(): void
    {
        $user = User::factory()->create();
        $published = Post::factory()->published()->create();
        $draft = Post::factory()->create();

        $this->actingAs($user)
            ->patch(route('admin.posts.toggle-featured', $published))
            ->assertForbidden();

        $this->actingAs(User::factory()->create(['role' => UserRole::Admin]))
            ->from(route('admin.posts.index'))
            ->patch(route('admin.posts.toggle-featured', $draft))
            ->assertRedirect(route('admin.posts.index'))
            ->assertSessionHasErrors('action');

        $this->assertFalse($draft->refresh()->is_featured);
    }

    public function test_author_can_preview_own_unpublished_post_but_not_another_authors_post(): void
    {
        $author = User::factory()->create(['role' => UserRole::Author]);
        $ownPost = Post::factory()->create([
            'author_id' => $author,
            'title' => '<script>alert(1)</script>',
            'content' => '<img src=x onerror=alert(1)>',
        ]);
        $otherPost = Post::factory()->create();

        $this->actingAs($author)
            ->get(route('posts.preview', $ownPost))
            ->assertOk()
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertDontSee('<img src=x onerror=alert(1)>', false);

        $this->actingAs($author)->get(route('posts.preview', $otherPost))->assertForbidden();
    }

    public function test_admin_can_preview_any_post_and_guest_must_login(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $post = Post::factory()->pendingReview()->create();

        $this->actingAs($admin)->get(route('posts.preview', $post))->assertOk();

        auth()->logout();
        $this->get(route('posts.preview', $post))->assertRedirect(route('login'));
    }
}
