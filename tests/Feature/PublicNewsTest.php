<?php

namespace Tests\Feature;

use App\Enums\CategoryStatus;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PublicNewsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_home_only_displays_currently_public_posts(): void
    {
        $published = Post::factory()->published()->create(['title' => 'Tin đã xuất bản']);
        Post::factory()->create(['title' => 'Bản nháp bí mật']);
        Post::factory()->scheduled()->create(['title' => 'Tin hẹn giờ']);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee($published->title);
        $response->assertDontSee('Bản nháp bí mật');
        $response->assertDontSee('Tin hẹn giờ');
    }

    public function test_home_groups_latest_public_articles_by_category(): void
    {
        $category = Category::factory()->create(['name' => 'Công nghệ kiểm thử']);
        Post::factory()->published()->create(['category_id' => $category, 'title' => 'Tin trong nhóm chuyên mục']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Công nghệ kiểm thử')
            ->assertSee('Tin trong nhóm chuyên mục')
            ->assertSee('Xem chuyên mục');
    }

    public function test_search_filters_public_posts_without_leaking_drafts(): void
    {
        Post::factory()->published()->create(['title' => 'Laravel dành cho sinh viên']);
        Post::factory()->published()->create(['title' => 'Bản tin thể thao']);
        Post::factory()->create(['title' => 'Laravel nội bộ']);

        $response = $this->get(route('news.index', ['q' => 'Laravel']));

        $response->assertOk();
        $response->assertSee('Laravel dành cho sinh viên');
        $response->assertDontSee('Bản tin thể thao');
        $response->assertDontSee('Laravel nội bộ');
    }

    public function test_parent_category_filter_includes_posts_from_direct_children(): void
    {
        $parent = Category::factory()->create(['name' => 'Công nghệ']);
        $child = Category::factory()->create(['parent_id' => $parent, 'name' => 'AI']);
        $other = Category::factory()->create();
        Post::factory()->published()->create(['category_id' => $parent, 'title' => 'Tin danh mục cha']);
        Post::factory()->published()->create(['category_id' => $child, 'title' => 'Tin danh mục con']);
        Post::factory()->published()->create(['category_id' => $other, 'title' => 'Tin ngoài danh mục']);

        $this->get(route('news.index', ['category' => $parent->slug]))
            ->assertOk()
            ->assertSee('Tin danh mục cha')
            ->assertSee('Tin danh mục con')
            ->assertDontSee('Tin ngoài danh mục')
            ->assertViewHas('selectedCategory', fn (Category $category): bool => $category->is($parent));
    }

    public function test_scheduled_post_returns_not_found_before_publication_time(): void
    {
        $post = Post::factory()->scheduled()->create();

        $this->get(route('news.show', $post->slug))->assertNotFound();
    }

    public function test_post_in_hidden_category_is_not_public(): void
    {
        $hiddenCategory = Category::factory()->create(['status' => CategoryStatus::Hidden]);
        $post = Post::factory()->published()->create(['category_id' => $hiddenCategory]);

        $this->get(route('news.show', $post->slug))->assertNotFound();
    }

    public function test_article_renders_allowed_html_and_removes_active_content(): void
    {
        $post = Post::factory()->published()->create([
            'content' => '<h2 onclick="alert(1)">Tiêu đề an toàn</h2>'
                .'<p>Nội dung <strong>quan trọng</strong>.</p>'
                .'<a href="javascript:alert(2)" onmouseover="alert(3)">Liên kết</a>'
                .'<iframe src="https://www.youtube.com/embed/video123" onload="alert(4)"></iframe>'
                .'<iframe src="https://example.com/embed/dangerous"></iframe>'
                .'<script>alert("xss")</script>',
        ]);

        $response = $this->get(route('news.show', $post->slug));

        $response->assertOk();
        $response->assertSee('<h2>Tiêu đề an toàn</h2>', false);
        $response->assertSee('<strong>quan trọng</strong>', false);
        $response->assertSee('https://www.youtube.com/embed/video123', false);
        $response->assertDontSee('javascript:alert', false);
        $response->assertDontSee('onmouseover=', false);
        $response->assertDontSee('https://example.com/embed/dangerous', false);
        $response->assertDontSee('alert("xss")', false);
    }

    public function test_article_can_hide_its_thumbnail_at_the_start_of_content(): void
    {
        $post = Post::factory()->published()->create([
            'thumbnail' => 'posts/thumbnails/hidden-cover.webp',
            'show_thumbnail_in_post' => false,
        ]);

        $this->get(route('news.show', $post->slug))
            ->assertOk()
            ->assertDontSee('Hình ảnh minh họa cho bài viết');
    }

    public function test_article_seo_description_fallback_is_limited_to_160_characters(): void
    {
        $post = Post::factory()->published()->create([
            'summary' => str_repeat('a', 200),
            'meta_description' => null,
        ]);

        $this->get(route('news.show', $post->slug))
            ->assertOk()
            ->assertSee('<meta name="description" content="'.str_repeat('a', 160).'">', false);
    }

    public function test_refreshing_article_in_same_session_counts_one_view(): void
    {
        $post = Post::factory()->published()->create();

        $this->get(route('news.show', $post->slug))->assertOk();
        $this->get(route('news.show', $post->slug))->assertOk();

        $this->assertSame(1, $post->refresh()->view_count);
        $this->assertDatabaseCount('post_views', 1);
    }
}
