<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AuthorProfileAndRelatedPostsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_public_author_profile_shows_only_currently_visible_articles(): void
    {
        $author = User::factory()->create(['role' => UserRole::Author, 'name' => 'Tác giả thử nghiệm']);
        $visible = Post::factory()->published()->create(['author_id' => $author, 'title' => 'Bài đang công khai']);
        Post::factory()->create(['author_id' => $author, 'title' => 'Bản nháp bí mật']);
        Post::factory()->scheduled()->create(['author_id' => $author, 'title' => 'Bài chưa đến giờ']);

        $this->get(route('authors.show', $author))
            ->assertOk()
            ->assertSee($author->name)
            ->assertSee($visible->title)
            ->assertDontSee('Bản nháp bí mật')
            ->assertDontSee('Bài chưa đến giờ');
    }

    public function test_standard_user_does_not_have_a_public_author_profile(): void
    {
        $reader = User::factory()->create();

        $this->get(route('authors.show', $reader))->assertNotFound();
    }

    public function test_standard_user_with_a_published_article_has_a_public_author_profile(): void
    {
        $writer = User::factory()->create(['name' => 'Người viết cộng đồng']);
        $post = Post::factory()->published()->create([
            'author_id' => $writer,
            'title' => 'Bài viết cộng đồng đã duyệt',
        ]);

        $this->get(route('authors.show', $writer))
            ->assertOk()
            ->assertSee($writer->name)
            ->assertSee($post->title);
    }

    public function test_related_articles_prioritize_shared_tags_before_same_category(): void
    {
        $category = Category::factory()->create();
        $otherCategory = Category::factory()->create();
        $tag = Tag::factory()->create();
        $post = Post::factory()->published()->create(['category_id' => $category]);
        $post->tags()->attach($tag);
        $sharedTag = Post::factory()->published()->create([
            'category_id' => $otherCategory,
            'title' => 'Ưu tiên chung tag',
        ]);
        $sharedTag->tags()->attach($tag);
        Post::factory()->published()->create([
            'category_id' => $category,
            'title' => 'Sau đó cùng chuyên mục',
        ]);

        $this->get(route('news.show', $post->slug))
            ->assertOk()
            ->assertSeeInOrder(['Ưu tiên chung tag', 'Sau đó cùng chuyên mục']);
    }
}
