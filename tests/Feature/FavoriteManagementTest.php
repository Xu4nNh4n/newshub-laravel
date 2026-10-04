<?php

namespace Tests\Feature;

use App\Enums\PostStatus;
use App\Models\Favorite;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class FavoriteManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guest_and_unverified_user_cannot_manage_favorites(): void
    {
        $post = Post::factory()->published()->create();

        $this->post(route('favorites.store', $post))->assertRedirect(route('login'));
        $this->actingAs(User::factory()->unverified()->create())
            ->post(route('favorites.store', $post))
            ->assertRedirect(route('verification.notice'));

        $this->assertDatabaseCount('favorites', 0);
    }

    public function test_verified_user_can_save_public_post_only_once(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->published()->create();

        $firstResponse = $this->actingAs($user)->post(route('favorites.store', $post));
        $secondResponse = $this->post(route('favorites.store', $post));

        $firstResponse->assertRedirect()->assertSessionHas('status', 'Đã lưu bài viết.');
        $secondResponse->assertRedirect();
        $this->assertDatabaseCount('favorites', 1);
        $this->assertDatabaseHas('favorites', ['user_id' => $user->id, 'post_id' => $post->id]);
    }

    public function test_user_cannot_save_non_public_post(): void
    {
        $draft = Post::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('favorites.store', $draft))
            ->assertNotFound();

        $this->assertDatabaseCount('favorites', 0);
    }

    public function test_user_can_remove_only_their_own_saved_post(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::factory()->published()->create();
        Favorite::query()->create(['user_id' => $user->id, 'post_id' => $post->id]);
        Favorite::query()->create(['user_id' => $otherUser->id, 'post_id' => $post->id]);

        $response = $this->actingAs($user)->delete(route('favorites.destroy', $post));

        $response->assertRedirect()->assertSessionHas('status', 'Đã bỏ lưu bài viết.');
        $this->assertDatabaseMissing('favorites', ['user_id' => $user->id, 'post_id' => $post->id]);
        $this->assertDatabaseHas('favorites', ['user_id' => $otherUser->id, 'post_id' => $post->id]);
    }

    public function test_favorites_page_only_shows_current_users_public_posts(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $publicPost = Post::factory()->published()->create(['title' => '<script>alert("saved")</script>']);
        $hiddenPost = Post::factory()->published()->create(['title' => 'Bài đã ẩn', 'status' => PostStatus::Hidden]);
        $otherPost = Post::factory()->published()->create(['title' => 'Bài của người khác']);
        Favorite::query()->create(['user_id' => $user->id, 'post_id' => $publicPost->id]);
        Favorite::query()->create(['user_id' => $user->id, 'post_id' => $hiddenPost->id]);
        Favorite::query()->create(['user_id' => $otherUser->id, 'post_id' => $otherPost->id]);

        $response = $this->actingAs($user)->get(route('favorites.index'));

        $response
            ->assertSee('&lt;script&gt;alert(&quot;saved&quot;)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert("saved")</script>', false)
            ->assertDontSee('Bài đã ẩn')
            ->assertDontSee('Bài của người khác');
    }

    public function test_news_detail_displays_current_favorite_state(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->published()->create();
        Favorite::query()->create(['user_id' => $user->id, 'post_id' => $post->id]);

        $response = $this->actingAs($user)->get(route('news.show', $post->slug));

        $response->assertSee('Bỏ lưu bài viết');
    }
}
