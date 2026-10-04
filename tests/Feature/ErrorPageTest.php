<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Route;
use RuntimeException;
use Tests\TestCase;

class ErrorPageTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_missing_public_article_uses_custom_404_page(): void
    {
        config()->set('app.debug', false);

        $this->get(route('news.show', 'khong-ton-tai'))
            ->assertNotFound()
            ->assertSee('Không tìm thấy nội dung');
    }

    public function test_forbidden_admin_area_uses_custom_403_page(): void
    {
        config()->set('app.debug', false);

        $this->actingAs(User::factory()->create())
            ->get(route('admin.posts.index'))
            ->assertForbidden()
            ->assertSee('Bạn không có quyền truy cập');
    }

    public function test_custom_500_page_can_render_without_exposing_exception_details(): void
    {
        config()->set('app.debug', false);
        Route::get('/_test/error-500', function (): never {
            throw new RuntimeException('Sensitive detail');
        });

        $this->get('/_test/error-500')
            ->assertInternalServerError()
            ->assertSee('Hệ thống đang gặp sự cố')
            ->assertDontSee('Sensitive detail');
    }
}
