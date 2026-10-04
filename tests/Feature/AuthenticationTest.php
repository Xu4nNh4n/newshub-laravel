<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guest_can_register_as_an_active_standard_user(): void
    {
        Notification::fake();

        $response = $this->post(route('register.store'), [
            'name' => 'Nguyen Van A',
            'email' => 'reader@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('verification.notice'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'reader@example.com',
            'role' => UserRole::User->value,
            'status' => UserStatus::Active->value,
        ]);
        Notification::assertSentTo(User::whereEmail('reader@example.com')->firstOrFail(), VerifyEmailNotification::class);
    }

    public function test_registration_rejects_duplicate_email_and_unconfirmed_password(): void
    {
        User::factory()->create(['email' => 'reader@example.com']);

        $response = $this->from(route('register'))->post(route('register.store'), [
            'name' => 'Nguyen Van A',
            'email' => 'reader@example.com',
            'password' => 'password',
            'password_confirmation' => 'different',
        ]);

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors(['email', 'password']);
        $this->assertGuest();
    }

    public function test_unverified_active_user_can_log_in(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_blocked_user_cannot_log_in(): void
    {
        $user = User::factory()->create(['status' => UserStatus::Blocked]);

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email', 'Tài khoản đã bị khóa.');
        $this->assertGuest();
    }

    public function test_authenticated_user_can_log_out(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_user_can_verify_email_with_a_valid_signed_url(): void
    {
        $user = User::factory()->unverified()->create();
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->getKey(), 'hash' => sha1($user->getEmailForVerification())],
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect(route('dashboard'));
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_password_reset_request_uses_generic_response_and_sends_notification(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $response = $this->post(route('password.email'), ['email' => $user->email]);

        $response->assertSessionHas('status', 'Nếu email tồn tại, hệ thống đã gửi liên kết đặt lại mật khẩu.');
        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_authentication_emails_use_localized_content_and_secure_links(): void
    {
        $user = User::factory()->unverified()->create(['name' => 'Nguyễn <script>alert(1)</script>']);
        $verification = new VerifyEmailNotification;
        $reset = new ResetPasswordNotification('secret-reset-token');

        $verificationMail = $verification->toMail($user);
        $resetMail = $reset->toMail($user);
        $verificationHtml = view($verificationMail->view['html'], $verificationMail->viewData)->render();
        $verificationText = view($verificationMail->view['text'], $verificationMail->viewData)->render();
        $resetHtml = view($resetMail->view['html'], $resetMail->viewData)->render();
        $resetText = view($resetMail->view['text'], $resetMail->viewData)->render();

        $this->assertSame('Xác minh địa chỉ email NewsHub', $verificationMail->subject);
        $this->assertSame(['html' => 'mail.auth-action', 'text' => 'mail.auth-action-text'], $verificationMail->view);
        $this->assertStringContainsString('signature=', $verificationMail->viewData['actionUrl']);
        $this->assertStringContainsString('Xác minh email', $verificationHtml);
        $this->assertStringContainsString('Xác minh email', $verificationText);
        $this->assertStringContainsString('&lt;script&gt;', $verificationHtml);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $verificationHtml);
        $this->assertSame('Đặt lại mật khẩu NewsHub', $resetMail->subject);
        $this->assertStringContainsString('secret-reset-token', $resetMail->viewData['actionUrl']);
        $this->assertStringContainsString(urlencode($user->email), $resetMail->viewData['actionUrl']);
        $this->assertStringContainsString('Đặt lại mật khẩu', $resetHtml);
        $this->assertStringContainsString('Đặt lại mật khẩu', $resetText);
    }

    public function test_user_can_reset_password_with_a_valid_token(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->post(route('password.store'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertTrue(auth()->validate([
            'email' => $user->email,
            'password' => 'new-password',
        ]));
    }
}
