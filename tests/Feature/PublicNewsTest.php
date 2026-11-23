<?php

namespace Tests\Feature;

use App\Enums\CategoryStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
        Post::factory()->published()->create([
            'category_id' => $category,
            'title' => 'Tin trong nhóm chuyên mục',
            'published_at' => now()->subWeek(),
        ]);
        Post::factory()->published()->count(9)->create(['published_at' => now()->subDay()]);

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

    public function test_article_always_shows_its_thumbnail(): void
    {
        $post = Post::factory()->published()->create([
            'thumbnail' => 'posts/thumbnails/hidden-cover.webp',
        ]);

        $this->get(route('news.show', $post->slug))
            ->assertOk()
            ->assertSee('hidden-cover.webp');
    }

    public function test_article_seo_description_fallback_is_limited_to_160_characters(): void
    {
        $post = Post::factory()->published()->create([
            'summary' => str_repeat('a', 200),
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
    }

    public function test_article_structured_data_escapes_script_tags_from_author_category_and_title(): void
    {
        $payload = '</script><script>alert(1)</script>';
        $post = Post::factory()->published()->create([
            'title' => $payload,
            'author_id' => User::factory()->create(['name' => $payload]),
            'category_id' => Category::factory()->create(['name' => $payload]),
        ]);

        $this->get(route('news.show', $post->slug))
            ->assertOk()
            ->assertDontSee($payload, false)
            ->assertSee('"@type": "NewsArticle"', false);
    }

    public function test_article_keeps_strikethrough_and_text_from_unsupported_formatting(): void
    {
        $post = Post::factory()->published()->create([
            'content' => '<p><s>gạch ngang</s> <span style="color:red" onclick="alert(1)">chữ màu</span></p>'
                .'<section><script>alert("nested")</script><p onclick="alert(2)">chữ trong khối</p></section>',
        ]);

        $this->get(route('news.show', $post->slug))
            ->assertOk()
            ->assertSee('<p><s>gạch ngang</s> chữ màu</p><p>chữ trong khối</p>', false)
            ->assertDontSee('alert("nested")', false);
    }

    public function test_article_converts_mixed_editor_list_into_semantic_lists(): void
    {
        $post = Post::factory()->published()->create([
            'content' => '<ol><li data-list="bullet"><span class="ql-ui" contenteditable="false"></span>chấm</li>'
                .'<li data-list="ordered">số một</li><li data-list="ordered">số hai</li></ol>',
        ]);

        $this->get(route('news.show', $post->slug))
            ->assertOk()
            ->assertSee('<ul><li>chấm</li></ul><ol><li>số một</li><li>số hai</li></ol>', false);
    }

    public function test_hidden_category_listing_returns_not_found(): void
    {
        $hidden = Category::factory()->create(['status' => CategoryStatus::Hidden]);

        $this->get(route('news.index', ['category' => $hidden->slug]))->assertNotFound();
    }

    public function test_hiding_parent_category_hides_child_category_and_its_posts(): void
    {
        $parent = Category::factory()->create(['status' => CategoryStatus::Hidden]);
        $child = Category::factory()->create(['parent_id' => $parent, 'name' => 'Chuyên mục con bị ẩn']);
        $post = Post::factory()->published()->create(['category_id' => $child, 'title' => 'Tin thuộc cha bị ẩn']);

        $this->get(route('news.show', $post->slug))->assertNotFound();
        $this->get(route('news.index', ['category' => $child->slug]))->assertNotFound();
        $this->get(route('feed.category', $child))->assertNotFound();
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('Tin thuộc cha bị ẩn')
            ->assertDontSee('Chuyên mục con bị ẩn');
        $this->get(route('sitemap'))
            ->assertOk()
            ->assertDontSee($child->slug);
    }

    public function test_public_layout_selects_only_existing_category_columns(): void
    {
        $columns = Schema::getColumnListing('categories');
        $selected = [];
        DB::listen(function (QueryExecuted $query) use (&$selected): void {
            if (preg_match('/^select (.+?) from "categories"/', $query->sql, $matches) === 1) {
                preg_match_all('/"(\w+)"(?!\.)/', $matches[1], $names);
                $selected = [...$selected, ...$names[1]];
            }
        });

        $this->get(route('pages.contact'))->assertOk();

        $this->assertNotEmpty($selected);
        $this->assertSame([], array_values(array_diff($selected, $columns)));
    }

    public function test_article_author_box_shows_the_authors_actual_role(): void
    {
        $author = User::factory()->create(['role' => UserRole::Author]);
        $post = Post::factory()->published()->create(['author_id' => $author]);

        $this->get(route('news.show', $post->slug))
            ->assertOk()
            ->assertSeeInOrder(['id="author-box"', 'Tác giả', 'Cây bút chuyên môn'], false);
    }

    public function test_public_dates_are_shown_in_vietnamese(): void
    {
        $this->travelTo('2026-11-23 10:00:00');
        Post::factory()->published()->create(['published_at' => now()->subDays(3)]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Thứ hai, 23/11/2026')
            ->assertSee('3 ngày trước');
    }

    public function test_home_lists_each_post_in_only_one_content_block(): void
    {
        $category = Category::factory()->create();
        foreach (range(1, 12) as $age) {
            Post::factory()->published()->create(['category_id' => $category, 'published_at' => now()->subDays($age)]);
        }

        $response = $this->get(route('home'))->assertOk()->assertSee('<h1', false);

        $ids = [
            ...$response->viewData('featuredPosts')->modelKeys(),
            ...$response->viewData('latestPosts')->modelKeys(),
            ...$response->viewData('categorySections')->flatMap->posts->pluck('id')->all(),
        ];
        $this->assertCount(12, $ids);
        $this->assertSame($ids, array_values(array_unique($ids)));
        $response->assertViewHas('hasMorePosts', true);
    }

    public function test_home_renders_without_any_post(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertViewHas('hasMorePosts', false);
    }
}
