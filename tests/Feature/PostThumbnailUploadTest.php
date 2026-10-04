<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostThumbnailUploadTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_author_can_upload_thumbnail_for_draft(): void
    {
        Storage::fake('public');
        $author = User::factory()->create(['role' => UserRole::Author]);
        $category = Category::factory()->create();

        $response = $this->actingAs($author)->post(route('author.posts.store'), [
            ...$this->postData($category),
            'thumbnail' => UploadedFile::fake()->image('cover.png', 2400, 1600),
        ]);

        $post = Post::query()->firstOrFail();
        $response->assertRedirect(route('author.posts.edit', $post));
        Storage::disk('public')->assertExists($post->thumbnail);
        $this->assertStringEndsWith('.webp', $post->thumbnail);
        $this->assertSame('image/webp', Storage::disk('public')->mimeType($post->thumbnail));

        $dimensions = getimagesizefromstring(Storage::disk('public')->get($post->thumbnail));
        $this->assertIsArray($dimensions);
        $this->assertLessThanOrEqual(1600, $dimensions[0]);
        $this->assertLessThanOrEqual(1200, $dimensions[1]);
        $this->assertCount(1, Storage::disk('public')->allFiles('posts/thumbnails'));
    }

    public function test_replacing_thumbnail_removes_old_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('posts/thumbnails/old.jpg', 'old');
        $author = User::factory()->create(['role' => UserRole::Author]);
        $post = Post::factory()->create(['author_id' => $author, 'thumbnail' => 'posts/thumbnails/old.jpg']);

        $response = $this->actingAs($author)->put(route('author.posts.update', $post), [
            ...$this->postData($post->category),
            'thumbnail' => UploadedFile::fake()->image('new.png', 600, 400),
        ]);

        $response->assertRedirect();
        Storage::disk('public')->assertMissing('posts/thumbnails/old.jpg');
        Storage::disk('public')->assertExists($post->refresh()->thumbnail);
    }

    public function test_executable_file_is_rejected_as_thumbnail(): void
    {
        Storage::fake('public');
        $author = User::factory()->create(['role' => UserRole::Author]);
        $category = Category::factory()->create();

        $response = $this->actingAs($author)->post(route('author.posts.store'), [
            ...$this->postData($category),
            'thumbnail' => UploadedFile::fake()->create('shell.php', 10, 'application/x-php'),
        ]);

        $response->assertSessionHasErrors('thumbnail');
        $this->assertDatabaseCount('posts', 0);
    }

    public function test_image_with_excessive_pixel_dimensions_is_rejected_before_storage(): void
    {
        Storage::fake('public');
        $author = User::factory()->create(['role' => UserRole::Author]);
        $category = Category::factory()->create();

        $response = $this->actingAs($author)->post(route('author.posts.store'), [
            ...$this->postData($category),
            'thumbnail' => UploadedFile::fake()->image('too-wide.png', 4097, 1),
        ]);

        $response->assertSessionHasErrors('thumbnail');
        $this->assertDatabaseCount('posts', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    /** @return array<string, mixed> */
    private function postData(Category $category): array
    {
        return [
            'title' => 'Bài viết có ảnh',
            'slug' => 'bai-viet-co-anh',
            'summary' => 'Tóm tắt bài viết.',
            'content' => 'Nội dung bài viết.',
            'category_id' => $category->id,
            'tag_ids' => [],
        ];
    }
}
