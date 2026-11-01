<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ProfileManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guest_cannot_access_profile_pages(): void
    {
        $this->get(route('profile.edit'))->assertRedirect(route('login'));
        $this->get(route('profile.comments'))->assertRedirect(route('login'));
    }

    public function test_user_can_update_name_without_mass_assigning_role(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'name' => 'Tên mới',
            'role' => UserRole::Admin->value,
        ]);

        $response->assertRedirect(route('profile.edit'))->assertSessionHas('status', 'Đã cập nhật hồ sơ.');
        $this->assertSame('Tên mới', $user->refresh()->name);
        $this->assertSame(UserRole::User, $user->role);
    }

    public function test_profile_page_receives_activity_statistics(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['author_id' => $user]);
        Comment::factory()->count(2)->for($user)->create();

        $response = $this->actingAs($user)->get(route('profile.edit'));

        $response->assertViewHas('stats', [
            'comments_count' => 2,
            'posts_count' => 1,
        ]);
    }

    public function test_user_can_change_email_and_receives_a_new_verification_notification(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'old@example.com']);

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => 'new@example.com',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $user->refresh();
        $this->assertSame('new@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
        Notification::assertSentTo($user, VerifyEmailNotification::class);
    }

    public function test_profile_email_must_be_unique_except_for_the_current_user(): void
    {
        $user = User::factory()->create(['email' => 'current@example.com']);
        User::factory()->create(['email' => 'taken@example.com']);

        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => 'taken@example.com',
        ])->assertSessionHasErrors('email');

        $this->assertSame('current@example.com', $user->refresh()->email);
    }

    public function test_user_must_supply_current_password_before_changing_password(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('profile.password.update'), [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertSessionHasErrors('current_password');
        $this->assertTrue(auth()->validate(['email' => $user->email, 'password' => 'password']));
    }

    public function test_user_can_change_password_with_current_password(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('profile.password.update'), [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect()->assertSessionHas('status', 'Đã đổi mật khẩu.');
        $this->assertTrue(auth()->validate(['email' => $user->email, 'password' => 'new-password']));
    }

    public function test_my_comments_page_only_shows_escaped_comments_owned_by_user(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->published()->create();
        Comment::factory()->for($post)->for($user)->create([
            'content' => '<script>alert("mine")</script>',
        ]);
        Comment::factory()->for($post)->create(['content' => 'Bình luận của người khác']);

        $response = $this->actingAs($user)->get(route('profile.comments'));

        $response
            ->assertSee('&lt;script&gt;alert(&quot;mine&quot;)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert("mine")</script>', false)
            ->assertDontSee('Bình luận của người khác');
    }
}
