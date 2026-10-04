<?php

namespace Tests\Feature;

use App\Enums\CategoryStatus;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class RssFeedTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_feed_returns_valid_rss_with_only_public_posts(): void
    {
        $publicPost = Post::factory()->published()->create([
            'title' => 'Tin công khai & an toàn',
            'summary' => '<script>alert("rss")</script>',
        ]);
        Post::factory()->create(['title' => 'Bản nháp nội bộ']);
        Post::factory()->scheduled()->create(['title' => 'Tin chưa đến giờ']);

        $response = $this->get(route('feed.index'));

        $response
            ->assertOk()
            ->assertHeader('Content-Type', 'application/rss+xml; charset=UTF-8')
            ->assertSee('<?xml version="1.0" encoding="UTF-8"?>', false)
            ->assertSee('<rss version="2.0"', false)
            ->assertSee('Tin công khai &amp; an toàn', false)
            ->assertSee('&lt;script&gt;alert(&quot;rss&quot;)&lt;/script&gt;', false)
            ->assertDontSee('<script>', false)
            ->assertDontSee('Bản nháp nội bộ')
            ->assertDontSee('Tin chưa đến giờ')
            ->assertSee(route('news.show', $publicPost->slug), false);

        $this->assertNotFalse(simplexml_load_string($response->getContent()));
    }

    public function test_category_feed_includes_parent_and_direct_child_posts(): void
    {
        $parent = Category::factory()->create(['name' => 'Công nghệ']);
        $child = Category::factory()->create(['parent_id' => $parent]);
        $other = Category::factory()->create();
        Post::factory()->published()->create(['category_id' => $parent, 'title' => 'RSS danh mục cha']);
        Post::factory()->published()->create(['category_id' => $child, 'title' => 'RSS danh mục con']);
        Post::factory()->published()->create(['category_id' => $other, 'title' => 'RSS danh mục khác']);

        $this->get(route('feed.category', $parent))
            ->assertOk()
            ->assertSee('RSS danh mục cha')
            ->assertSee('RSS danh mục con')
            ->assertDontSee('RSS danh mục khác');
    }

    public function test_hidden_or_deleted_category_feed_returns_not_found(): void
    {
        $hidden = Category::factory()->create(['status' => CategoryStatus::Hidden]);
        $deleted = Category::factory()->create();
        $deleted->delete();

        $this->get(route('feed.category', $hidden))->assertNotFound();
        $this->get('/feed/'.$deleted->slug)->assertNotFound();
    }
}
