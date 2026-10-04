<?php

namespace Tests\Feature;

use App\Enums\CommentReportStatus;
use App\Enums\UserRole;
use App\Models\AuthorApplication;
use App\Models\Category;
use App\Models\CommentReport;
use App\Models\Post;
use App\Models\PostRequest;
use App\Models\PostView;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class DashboardStatisticsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_dashboard_shows_aggregated_system_statistics(): void
    {
        $this->travelTo('2026-09-27 10:00:00');
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        User::factory()->count(2)->create();
        User::factory()->create(['role' => UserRole::Author]);
        User::factory()->create(['status' => 'blocked']);
        $popularPost = Post::factory()->published()->create(['view_count' => 42]);
        PostView::query()->create(['post_id' => $popularPost->id, 'session_id' => 'dashboard-test', 'viewed_at' => now()]);
        Post::factory()->pendingReview()->create();
        Post::factory()->create();
        PostRequest::factory()->pending()->create();
        CommentReport::factory()->create(['status' => CommentReportStatus::Pending]);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response
            ->assertViewHas('adminStats', fn (array $stats): bool => $stats['pending_post_requests'] === 1)
            ->assertSee('Tổng quan quản trị')
            ->assertSee('Bài đang được xem nhiều')
            ->assertSee('42 lượt xem')
            ->assertSee('Đang hiển thị')
            ->assertSee('Bài tạo theo tháng')
            ->assertSee('Lượt xem 7 ngày')
            ->assertSee('Chuyên mục nổi bật')
            ->assertSee('09/2026')
            ->assertSee('27/09');
    }

    public function test_author_dashboard_only_shows_the_authors_own_statistics(): void
    {
        $author = User::factory()->create(['role' => UserRole::Author]);
        Post::factory()->published()->create(['author_id' => $author, 'view_count' => 7]);
        Post::factory()->pendingReview()->create(['author_id' => $author]);
        Post::factory()->published()->create(['view_count' => 99]);

        $response = $this->actingAs($author)->get(route('dashboard'));

        $response
            ->assertSee('Thống kê bài viết của bạn')
            ->assertSee('Tổng lượt xem')
            ->assertSee('Bài viết có lượt xem cao nhất')
            ->assertSee('7')
            ->assertDontSee('Tổng quan quản trị')
            ->assertDontSee('99 lượt xem');
    }

    public function test_standard_user_does_not_receive_writing_statistics(): void
    {
        $user = User::factory()->create();
        Post::factory()->create(['author_id' => $user, 'title' => 'Bản nháp của User']);
        Post::factory()->published()->create(['title' => 'Bài của người khác']);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response
            ->assertDontSee('Tổng quan quản trị')
            ->assertDontSee('Thống kê bài viết của bạn')
            ->assertDontSee('Bản nháp của User')
            ->assertDontSee('Bài của người khác');
    }

    public function test_standard_user_dashboard_receives_latest_application_and_active_categories(): void
    {
        $user = User::factory()->create();
        $activeCategory = Category::factory()->create(['name' => 'Công nghệ']);
        Category::factory()->create(['status' => 'hidden']);
        $application = AuthorApplication::factory()->rejected()->create([
            'user_id' => $user,
            'category_id' => $activeCategory,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertViewHas('authorApplication', fn (AuthorApplication $value): bool => $value->is($application)
            && $value->relationLoaded('category'));
        $response->assertViewHas('authorApplicationCategories', fn ($categories): bool => $categories->pluck('id')->all() === [$activeCategory->id]);
    }
}
