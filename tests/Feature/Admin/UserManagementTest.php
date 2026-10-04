<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_non_admin_cannot_manage_users(): void
    {
        $target = User::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($user)->patch(route('admin.users.update', $target), [
            'role' => UserRole::Author->value,
            'status' => UserStatus::Blocked->value,
        ])->assertForbidden();
    }

    public function test_admin_list_excludes_admin_accounts_and_supports_filters(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin, 'email' => 'admin@example.com']);
        $author = User::factory()->create(['role' => UserRole::Author, 'email' => 'author@example.com']);
        User::factory()->create(['email' => 'reader@example.com']);

        $response = $this->actingAs($admin)->get(route('admin.users.index', [
            'q' => 'author@',
            'role' => UserRole::Author->value,
            'status' => UserStatus::Active->value,
        ]));

        $response
            ->assertSee($author->email)
            ->assertDontSee('admin@example.com')
            ->assertDontSee('reader@example.com');
    }

    public function test_admin_can_promote_and_block_standard_user_with_activity_log(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $target = User::factory()->create();

        $response = $this->actingAs($admin)->patch(route('admin.users.update', $target), [
            'role' => UserRole::Author->value,
            'status' => UserStatus::Blocked->value,
        ]);

        $response->assertRedirect()->assertSessionHas('status', 'Đã cập nhật quyền tài khoản.');
        $this->assertSame(UserRole::Author, $target->refresh()->role);
        $this->assertSame(UserStatus::Blocked, $target->status);
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'user.access-updated',
            'subject_type' => User::class,
            'subject_id' => $target->id,
        ]);
    }

    public function test_admin_cannot_change_own_or_another_admin_access(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $otherAdmin = User::factory()->create(['role' => UserRole::Admin]);
        $payload = ['role' => UserRole::User->value, 'status' => UserStatus::Blocked->value];

        $this->actingAs($admin)->patch(route('admin.users.update', $admin), $payload)->assertForbidden();
        $this->actingAs($admin)->patch(route('admin.users.update', $otherAdmin), $payload)->assertForbidden();

        $this->assertSame(UserRole::Admin, $admin->refresh()->role);
        $this->assertSame(UserStatus::Active, $otherAdmin->refresh()->status);
        $this->assertDatabaseCount('activity_logs', 0);
    }

    public function test_admin_role_cannot_be_assigned_through_user_management(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $target = User::factory()->create();

        $response = $this->actingAs($admin)->patch(route('admin.users.update', $target), [
            'role' => UserRole::Admin->value,
            'status' => UserStatus::Active->value,
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertSame(UserRole::User, $target->refresh()->role);
        $this->assertDatabaseCount('activity_logs', 0);
    }

    public function test_admin_can_send_password_reset_link_to_user_with_activity_log(): void
    {
        Notification::fake();
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $target = User::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.users.send-reset-link', $target));

        $response->assertRedirect()->assertSessionHas('status', "Đã gửi liên kết đặt lại mật khẩu đến {$target->email}.");
        Notification::assertSentTo($target, ResetPasswordNotification::class);
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'user.password-reset-sent',
            'subject_type' => User::class,
            'subject_id' => $target->id,
            'description' => "Quản trị viên đã gửi liên kết đặt lại mật khẩu đến {$target->email}.",
        ]);
    }

    public function test_admin_cannot_send_password_reset_link_to_self_or_other_admin(): void
    {
        Notification::fake();
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $otherAdmin = User::factory()->create(['role' => UserRole::Admin]);

        $this->actingAs($admin)->post(route('admin.users.send-reset-link', $admin))->assertForbidden();
        $this->actingAs($admin)->post(route('admin.users.send-reset-link', $otherAdmin))->assertForbidden();

        Notification::assertNothingSent();
        $this->assertDatabaseCount('activity_logs', 0);
    }

    public function test_non_admin_cannot_send_password_reset_link(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $target = User::factory()->create();

        $this->actingAs($user)->post(route('admin.users.send-reset-link', $target))->assertForbidden();

        Notification::assertNothingSent();
        $this->assertDatabaseCount('activity_logs', 0);
    }
}
