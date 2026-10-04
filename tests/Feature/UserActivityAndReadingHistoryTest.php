<?php

namespace Tests\Feature;

use App\Enums\CategoryStatus;
use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Post;
use App\Models\PostView;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class UserActivityAndReadingHistoryTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guest_cannot_access_reading_history(): void
    {
        $this->get(route('reading-history.index'))
            ->assertRedirect(route('login'));
    }

    public function test_user_can_view_their_reading_history(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $category = Category::factory()->create(['status' => CategoryStatus::Active]);
        $post1 = Post::factory()->create([
            'category_id' => $category->id,
            'status' => PostStatus::Published,
            'published_at' => now()->subDay(),
            'title' => 'Bài viết công nghệ AI số 1',
        ]);
        $post2 = Post::factory()->create([
            'category_id' => $category->id,
            'status' => PostStatus::Published,
            'published_at' => now()->subHours(5),
            'title' => 'Bài viết kinh tế tài chính số 2',
        ]);
        $otherPost = Post::factory()->create([
            'category_id' => $category->id,
            'status' => PostStatus::Published,
            'published_at' => now()->subDays(2),
            'title' => 'Bài viết người khác đọc',
        ]);

        PostView::create([
            'user_id' => $user->id,
            'post_id' => $post1->id,
            'viewed_at' => now()->subHours(2),
        ]);
        PostView::create([
            'user_id' => $user->id,
            'post_id' => $post2->id,
            'viewed_at' => now()->subHour(),
        ]);
        PostView::create([
            'user_id' => $otherUser->id,
            'post_id' => $otherPost->id,
            'viewed_at' => now()->subHour(),
        ]);

        $response = $this->actingAs($user)->get(route('reading-history.index'));

        $response->assertOk()
            ->assertSee('Bài viết đã đọc')
            ->assertSee('Bài viết công nghệ AI số 1')
            ->assertSee('Bài viết kinh tế tài chính số 2')
            ->assertDontSee('Bài viết người khác đọc');
    }

    public function test_user_can_delete_a_single_post_from_reading_history(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['status' => CategoryStatus::Active]);
        $post = Post::factory()->create([
            'category_id' => $category->id,
            'status' => PostStatus::Published,
            'published_at' => now()->subDay(),
        ]);

        PostView::create([
            'user_id' => $user->id,
            'post_id' => $post->id,
            'viewed_at' => now()->subMinutes(10),
        ]);

        $this->actingAs($user)
            ->delete(route('reading-history.destroy', $post))
            ->assertRedirect()
            ->assertSessionHas('status', 'Đã xóa bài viết khỏi lịch sử đọc.');

        $this->assertDatabaseMissing('post_views', [
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
    }

    public function test_user_can_clear_all_reading_history(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['status' => CategoryStatus::Active]);
        $post1 = Post::factory()->create(['category_id' => $category->id, 'status' => PostStatus::Published, 'published_at' => now()->subDay()]);
        $post2 = Post::factory()->create(['category_id' => $category->id, 'status' => PostStatus::Published, 'published_at' => now()->subDay()]);

        PostView::create(['user_id' => $user->id, 'post_id' => $post1->id, 'viewed_at' => now()->subMinutes(10)]);
        PostView::create(['user_id' => $user->id, 'post_id' => $post2->id, 'viewed_at' => now()->subMinutes(5)]);

        $this->actingAs($user)
            ->delete(route('reading-history.clear'))
            ->assertRedirect()
            ->assertSessionHas('status', 'Đã xóa toàn bộ lịch sử đọc bài viết.');

        $this->assertDatabaseMissing('post_views', [
            'user_id' => $user->id,
        ]);
    }

    public function test_guest_cannot_access_activity_history(): void
    {
        $this->get(route('profile.activity'))
            ->assertRedirect(route('login'));
    }

    public function test_user_can_view_their_activity_history_timeline(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'profile.updated',
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'description' => 'Cập nhật thông tin hồ sơ cá nhân.',
            'created_at' => now()->subHours(2),
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'password.updated',
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'description' => 'Đổi mật khẩu tài khoản thành công.',
            'created_at' => now()->subHour(),
        ]);

        ActivityLog::create([
            'user_id' => $otherUser->id,
            'action' => 'profile.updated',
            'subject_type' => User::class,
            'subject_id' => $otherUser->id,
            'description' => 'Hoạt động của người dùng khác bí mật.',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('profile.activity'));

        $response->assertOk()
            ->assertSee('Lịch sử hoạt động')
            ->assertSee('Cập nhật hồ sơ')
            ->assertSee('Đổi mật khẩu tài khoản')
            ->assertSee('Cập nhật thông tin hồ sơ cá nhân.')
            ->assertDontSee('Hoạt động của người dùng khác bí mật.');
    }

    public function test_author_sees_post_activities_and_can_filter_by_type(): void
    {
        $author = User::factory()->create(['role' => UserRole::Author]);
        $category = Category::factory()->create(['status' => CategoryStatus::Active]);
        $post = Post::factory()->create([
            'author_id' => $author->id,
            'category_id' => $category->id,
            'title' => 'Bài viết chuyên sâu của tác giả',
        ]);

        ActivityLog::create([
            'user_id' => $author->id,
            'action' => 'post.created',
            'subject_type' => Post::class,
            'subject_id' => $post->id,
            'description' => 'Tạo bài viết mới: '.$post->title,
            'created_at' => now()->subDay(),
        ]);

        ActivityLog::create([
            'user_id' => $author->id,
            'action' => 'profile.updated',
            'subject_type' => User::class,
            'subject_id' => $author->id,
            'description' => 'Cập nhật hồ sơ tác giả.',
            'created_at' => now()->subHours(3),
        ]);

        $response = $this->actingAs($author)->get(route('profile.activity', ['type' => 'posts']));

        $response->assertOk()
            ->assertSee('Tạo bài viết mới')
            ->assertSee($post->title)
            ->assertDontSee('Cập nhật hồ sơ tác giả.');
    }

    public function test_profile_update_and_password_update_generate_activity_logs(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => 'Tên Mới Sau Khi Cập Nhật',
        ])->assertRedirect(route('profile.edit'));

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'action' => 'profile.updated',
        ]);

        $this->actingAs($user)->put(route('profile.password.update'), [
            'current_password' => 'password',
            'password' => 'NewSecurePassword123!',
            'password_confirmation' => 'NewSecurePassword123!',
        ])->assertRedirect();

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'action' => 'password.updated',
        ]);
    }
}
