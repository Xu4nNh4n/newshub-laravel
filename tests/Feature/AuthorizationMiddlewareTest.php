<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AuthorizationMiddlewareTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guest_is_redirected_from_dashboard_to_login(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_verified_standard_user_cannot_access_writing_or_admin_areas(): void
    {
        $user = User::factory()->create(['role' => UserRole::User]);

        $this->actingAs($user)->get(route('author.dashboard'))->assertOk();
        $this->actingAs($user)->get(route('author.posts.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_author_can_access_author_area_but_not_admin_area(): void
    {
        $author = User::factory()->create(['role' => UserRole::Author]);

        $this->actingAs($author)->get(route('author.dashboard'))->assertOk();
        $this->actingAs($author)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_admin_can_access_author_and_admin_areas(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $this->actingAs($admin)->get(route('author.dashboard'))->assertOk();
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
    }

    public function test_unverified_user_is_redirected_from_writing_area_to_verification_notice(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get(route('author.posts.index'));

        $response->assertRedirect(route('verification.notice'));
    }

    public function test_blocked_authenticated_user_is_logged_out(): void
    {
        $blockedUser = User::factory()->create(['status' => UserStatus::Blocked]);

        $response = $this->actingAs($blockedUser)->get(route('dashboard'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email', 'Tài khoản đã bị khóa.');
        $this->assertGuest();
    }
}
