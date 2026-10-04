<?php

namespace Tests\Feature;

use App\Enums\CategoryStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_sitemap_contains_only_public_posts_and_used_taxonomies(): void
    {
        $visibleCategory = Category::factory()->create(['slug' => 'cong-nghe']);
        $visibleTag = Tag::factory()->create(['slug' => 'laravel']);
        $visiblePost = Post::factory()->published()->create([
            'category_id' => $visibleCategory,
            'slug' => 'bai-cong-khai',
        ]);
        $visiblePost->tags()->attach($visibleTag);

        $draftCategory = Category::factory()->create(['slug' => 'ban-nhap']);
        $unusedTag = Tag::factory()->create(['slug' => 'khong-cong-khai']);
        $draftPost = Post::factory()->create([
            'category_id' => $draftCategory,
            'slug' => 'bai-ban-nhap',
        ]);
        $draftPost->tags()->attach($unusedTag);

        $hiddenCategory = Category::factory()->create([
            'slug' => 'chuyen-muc-an',
            'status' => CategoryStatus::Hidden,
        ]);
        Post::factory()->published()->create([
            'category_id' => $hiddenCategory,
            'slug' => 'bai-thuoc-chuyen-muc-an',
        ]);

        $response = $this->get(route('sitemap'));

        $response
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertHeader('Cache-Control', 'max-age=300, public')
            ->assertSee(route('home'), false)
            ->assertSee(route('news.show', $visiblePost->slug), false)
            ->assertSee('category=cong-nghe', false)
            ->assertSee('tag=laravel', false)
            ->assertDontSee('bai-ban-nhap')
            ->assertDontSee('ban-nhap')
            ->assertDontSee('khong-cong-khai')
            ->assertDontSee('bai-thuoc-chuyen-muc-an')
            ->assertDontSee('chuyen-muc-an');
    }
}
