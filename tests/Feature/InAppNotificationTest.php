<?php

namespace Tests\Feature;

use App\Models\AuthorApplication;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Notifications\AuthorApplicationStatusNotification;
use App\Notifications\CommentRepliedNotification;
use App\Notifications\PostStatusUpdatedNotification;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class InAppNotificationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_user_can_view_notifications_page_and_empty_state(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('notifications.index'));

        $response->assertOk();
        $response->assertSee('Chưa có thông báo nào');
    }

    public function test_user_can_fetch_notifications_via_json_for_dropdown(): void
    {
        $user = User::factory()->create();
        $author = User::factory()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->create(['author_id' => $author->id, 'category_id' => $category->id]);
        $comment = Comment::factory()->create(['post_id' => $post->id, 'user_id' => $user->id]);

        $user->notify(new CommentRepliedNotification($comment, $post, $author));

        $response = $this->actingAs($user)->getJson(route('notifications.index'));

        $response->assertOk();
        $response->assertJsonStructure([
            'unread_count',
            'notifications' => [
                '*' => ['id', 'read', 'created_at', 'data'],
            ],
        ]);
        $this->assertSame(1, $response->json('unread_count'));
    }

    public function test_user_can_click_notification_to_mark_as_read_and_redirect(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->create(['author_id' => $user->id, 'category_id' => $category->id]);

        $user->notify(new PostStatusUpdatedNotification($post, 'approved'));
        $notification = $user->notifications()->first();

        $this->assertNull($notification->read_at);

        $response = $this->actingAs($user)->get(route('notifications.read', $notification->id));

        $response->assertRedirect(route('news.show', $post->slug));
        $this->assertNotNull($notification->refresh()->read_at);
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->create(['author_id' => $user->id, 'category_id' => $category->id]);

        $user->notify(new PostStatusUpdatedNotification($post, 'approved'));
        $user->notify(new PostStatusUpdatedNotification($post, 'rejected', 'Nội dung chưa đủ tiêu chuẩn'));

        $this->assertSame(2, $user->unreadNotifications()->count());

        $response = $this->actingAs($user)->post(route('notifications.mark-all-read'));

        $response->assertRedirect();
        $this->assertSame(0, $user->fresh()->unreadNotifications()->count());
    }

    public function test_author_application_notification_contains_correct_action_and_data(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $application = AuthorApplication::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'status' => 'approved',
        ]);

        $user->notify(new AuthorApplicationStatusNotification($application, 'approved'));
        $notification = $user->notifications()->first();

        $this->assertSame('author_application', $notification->data['type']);
        $this->assertSame('approved', $notification->data['status']);
        $this->assertSame(route('author.posts.create'), $notification->data['url']);
    }
}
