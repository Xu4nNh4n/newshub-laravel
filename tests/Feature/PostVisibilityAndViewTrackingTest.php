<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use App\Services\PostViewRecorder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PostVisibilityAndViewTrackingTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_public_scope_excludes_unpublished_and_future_posts(): void
    {
        $visiblePost = Post::factory()->create([
            'status' => 'published',
            'published_at' => now()->subMinute(),
        ]);
        Post::factory()->create(['status' => 'draft']);
        Post::factory()->create([
            'status' => 'published',
            'published_at' => now()->addMinute(),
        ]);

        $this->assertSame([$visiblePost->id], Post::publiclyVisible()->pluck('id')->all());
    }

    public function test_view_is_recorded_once_per_user_during_the_ttl(): void
    {
        $post = Post::factory()->create();
        $viewer = User::factory()->create();
        $recorder = new PostViewRecorder(Cache::store('array'));

        $firstView = $recorder->record($post, $viewer, null, null);
        $duplicateView = $recorder->record($post, $viewer, null, null);

        $this->assertTrue($firstView);
        $this->assertFalse($duplicateView);
        $this->assertDatabaseCount('post_views', 1);
        $this->assertSame(1, $post->refresh()->view_count);
    }

    public function test_guest_views_are_deduplicated_by_session(): void
    {
        $post = Post::factory()->create();
        $recorder = new PostViewRecorder(Cache::store('array'));

        $recorder->record($post, null, 'session-a', null);
        $duplicateView = $recorder->record($post, null, 'session-a', null);
        $newSessionView = $recorder->record($post, null, 'session-b', null);

        $this->assertFalse($duplicateView);
        $this->assertTrue($newSessionView);
        $this->assertDatabaseCount('post_views', 2);
        $this->assertSame(2, $post->refresh()->view_count);
    }

    public function test_guest_view_uses_ip_hash_when_session_is_missing(): void
    {
        $post = Post::factory()->create();
        $recorder = new PostViewRecorder(Cache::store('array'));

        $firstView = $recorder->record($post, null, null, 'hashed-ip');
        $duplicateView = $recorder->record($post, null, null, 'hashed-ip');

        $this->assertTrue($firstView);
        $this->assertFalse($duplicateView);
        $this->assertDatabaseCount('post_views', 1);
    }

    public function test_view_without_an_identity_is_ignored(): void
    {
        $post = Post::factory()->create();
        $recorder = new PostViewRecorder(Cache::store('array'));

        $recorded = $recorder->record($post, null, null, null);

        $this->assertFalse($recorded);
        $this->assertDatabaseCount('post_views', 0);
    }
}
