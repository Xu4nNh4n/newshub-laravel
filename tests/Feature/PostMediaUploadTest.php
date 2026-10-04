<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostMediaUploadTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_author_can_upload_an_optimized_inline_image(): void
    {
        Storage::fake('public');
        $author = User::factory()->create(['role' => UserRole::Author]);

        $response = $this->actingAs($author)->postJson(route('author.media.upload'), [
            'image' => UploadedFile::fake()->image('news-photo.png', 2400, 1600),
        ]);

        $response
            ->assertCreated()
            ->assertJson(['success' => true])
            ->assertJsonPath('url', fn (string $url): bool => str_starts_with($url, '/storage/posts/media/'));

        $files = Storage::disk('public')->allFiles('posts/media');
        $this->assertCount(1, $files);
        $this->assertStringEndsWith('.webp', $files[0]);

        $dimensions = getimagesizefromstring(Storage::disk('public')->get($files[0]));
        $this->assertIsArray($dimensions);
        $this->assertLessThanOrEqual(1920, $dimensions[0]);
        $this->assertLessThanOrEqual(1920, $dimensions[1]);
    }

    public function test_guest_is_redirected_to_login_when_uploading_inline_media(): void
    {
        Storage::fake('public');

        $this->post(route('author.media.upload'), [
            'image' => UploadedFile::fake()->image('news-photo.png'),
        ])->assertRedirect(route('login'));

        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_reader_is_forbidden_from_uploading_inline_media(): void
    {
        Storage::fake('public');
        $reader = User::factory()->create(['role' => UserRole::User]);

        $this->actingAs($reader)->postJson(route('author.media.upload'), [
            'image' => UploadedFile::fake()->image('news-photo.png'),
        ])->assertForbidden();

        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_unverified_author_is_redirected_to_verification_before_upload(): void
    {
        Storage::fake('public');
        $author = User::factory()->unverified()->create(['role' => UserRole::Author]);

        $this->actingAs($author)->post(route('author.media.upload'), [
            'image' => UploadedFile::fake()->image('news-photo.png'),
        ])->assertRedirect(route('verification.notice'));

        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_non_image_file_is_rejected_without_being_stored(): void
    {
        Storage::fake('public');
        $author = User::factory()->create(['role' => UserRole::Author]);

        $this->actingAs($author)->postJson(route('author.media.upload'), [
            'image' => UploadedFile::fake()->create('payload.php', 10, 'application/x-php'),
        ])->assertUnprocessable()->assertJsonValidationErrors('image');

        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_image_larger_than_five_megabytes_is_rejected(): void
    {
        Storage::fake('public');
        $author = User::factory()->create(['role' => UserRole::Author]);

        $this->actingAs($author)->postJson(route('author.media.upload'), [
            'image' => UploadedFile::fake()->image('oversized.jpg')->size(5121),
        ])->assertUnprocessable()->assertJsonValidationErrors('image');

        $this->assertSame([], Storage::disk('public')->allFiles());
    }
}
