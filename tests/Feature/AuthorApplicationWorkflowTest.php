<?php

namespace Tests\Feature;

use App\Enums\AuthorApplicationStatus;
use App\Enums\UserRole;
use App\Models\AuthorApplication;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AuthorApplicationWorkflowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_verified_user_can_submit_one_pending_application(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->post(route('author-applications.store'), [
            'category_id' => $category->id,
            'bio' => 'Tôi có kinh nghiệm viết bài công nghệ.',
            'sample_title' => 'Xu hướng AI trong năm nay',
            'sample_content' => 'Đây là nội dung bài viết mẫu của tôi.',
        ]);

        $response->assertRedirect(route('dashboard'))
            ->assertSessionHas('status', 'Đã gửi đơn ứng tuyển tác giả.');
        $this->assertDatabaseHas('author_applications', [
            'user_id' => $user->id,
            'pending_user_id' => $user->id,
            'category_id' => $category->id,
            'status' => AuthorApplicationStatus::Pending->value,
        ]);
    }

    public function test_user_cannot_submit_a_second_pending_application(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        AuthorApplication::factory()->create([
            'user_id' => $user,
            'pending_user_id' => $user,
            'category_id' => $category,
        ]);

        $this->actingAs($user)->post(route('author-applications.store'), [
            'category_id' => $category->id,
            'bio' => 'Thông tin mới.',
            'sample_title' => 'Bài viết mới',
            'sample_content' => 'Nội dung bài viết mới.',
        ])->assertSessionHasErrors('application', 'Bạn đang có một đơn ứng tuyển chờ duyệt.');

        $this->assertSame(1, AuthorApplication::query()->where('user_id', $user->id)->count());
    }

    public function test_author_cannot_submit_an_application(): void
    {
        $author = User::factory()->create(['role' => UserRole::Author]);
        $category = Category::factory()->create();

        $this->actingAs($author)->post(route('author-applications.store'), [
            'category_id' => $category->id,
            'bio' => 'Thông tin.',
            'sample_title' => 'Bài viết',
            'sample_content' => 'Nội dung.',
        ])->assertForbidden();

        $this->assertSame(0, AuthorApplication::query()->count());
    }

    public function test_unverified_user_is_redirected_before_submitting_an_application(): void
    {
        $user = User::factory()->unverified()->create();
        $category = Category::factory()->create();

        $this->actingAs($user)->post(route('author-applications.store'), [
            'category_id' => $category->id,
            'bio' => 'Thông tin.',
            'sample_title' => 'Bài viết',
            'sample_content' => 'Nội dung.',
        ])->assertRedirect(route('verification.notice'));

        $this->assertSame(0, AuthorApplication::query()->count());
    }

    public function test_admin_approval_promotes_user_and_writes_an_activity_log(): void
    {
        $this->travelTo('2026-10-04 10:30:00');
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $user = User::factory()->create();
        $application = AuthorApplication::factory()->create([
            'user_id' => $user,
            'pending_user_id' => $user,
        ]);

        $response = $this->actingAs($admin)
            ->patch(route('admin.author-applications.approve', $application));

        $response->assertRedirect()
            ->assertSessionHas('status', 'Đã phê duyệt đơn và cấp quyền tác giả.');
        $application->refresh();
        $this->assertSame(AuthorApplicationStatus::Approved, $application->status);
        $this->assertNull($application->pending_user_id);
        $this->assertSame($admin->id, $application->reviewed_by);
        $this->assertSame('2026-10-04 10:30:00', $application->reviewed_at?->format('Y-m-d H:i:s'));
        $this->assertSame(UserRole::Author, $user->refresh()->role);
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'author_application.approved',
            'subject_id' => $application->id,
        ]);
    }

    public function test_admin_must_give_a_reason_when_rejecting_an_application(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $application = AuthorApplication::factory()->create();

        $this->actingAs($admin)
            ->patch(route('admin.author-applications.reject', $application), ['reason' => ''])
            ->assertSessionHasErrors('reason');

        $this->assertSame(AuthorApplicationStatus::Pending, $application->refresh()->status);
    }

    public function test_rejected_user_can_submit_a_new_application(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $application = AuthorApplication::factory()->create([
            'user_id' => $user,
            'pending_user_id' => $user,
            'category_id' => $category,
        ]);

        $this->actingAs($admin)->patch(route('admin.author-applications.reject', $application), [
            'reason' => 'Cần bổ sung ví dụ bài viết chi tiết hơn.',
        ])->assertRedirect();

        $application->refresh();
        $this->assertSame(AuthorApplicationStatus::Rejected, $application->status);
        $this->assertSame('Cần bổ sung ví dụ bài viết chi tiết hơn.', $application->rejection_reason);
        $this->assertNull($application->pending_user_id);
        $this->assertSame(UserRole::User, $user->refresh()->role);

        $this->actingAs($user)->post(route('author-applications.store'), [
            'category_id' => $category->id,
            'bio' => 'Thông tin đã bổ sung.',
            'sample_title' => 'Bài mẫu chi tiết',
            'sample_content' => 'Nội dung mới đầy đủ và chi tiết hơn.',
        ])->assertRedirect(route('dashboard'));

        $this->assertSame(1, AuthorApplication::query()
            ->where('user_id', $user->id)
            ->where('status', AuthorApplicationStatus::Pending)
            ->count());
    }
}
