<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class StaticPagesTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_privacy_page_is_accessible_and_renders_correctly(): void
    {
        $response = $this->get(route('pages.privacy'));

        $response->assertOk()
            ->assertSee('Chính Sách Bảo Mật Thông Tin & Dữ Liệu Độc Giả', false)
            ->assertSee('Nghị định số 13/2023/NĐ-CP')
            ->assertSee('Điều 1: Căn Cứ Pháp Lý & Phạm Vi Áp Dụng', false)
            ->assertSee('privacy@newshub.vn');
    }

    public function test_terms_page_is_accessible_and_renders_correctly(): void
    {
        $response = $this->get(route('pages.terms'));

        $response->assertOk()
            ->assertSee('Điều Khoản Dịch Vụ Độc Giả & Tiêu Chuẩn Sử Dụng', false)
            ->assertSee('Điều 2: Quyền Sở Hữu Trí Tuệ & Bản Quyền Báo Chí', false)
            ->assertSee('Điều 3: Quy Chuẩn Bình Luận & Chuẩn Mực Cộng Đồng', false)
            ->assertSee('newshub.vn');
    }

    public function test_moderation_policy_page_is_accessible_and_renders_correctly(): void
    {
        $response = $this->get(route('pages.moderation-policy'));

        $response->assertOk()
            ->assertSee('Quy Chế Kiểm Duyệt Nội Dung & Tiêu Chuẩn Xuất Bản', false)
            ->assertSee('Vòng 1: Tác giả / Phóng viên biên soạn bản thảo (Draft)')
            ->assertSee('Vòng 2: Biên tập viên Ban chuyên môn rà soát nghiệp vụ (In Review)')
            ->assertSee('Vòng 3: Ban Thư ký Tòa soạn & Tổng biên tập phê chuẩn (Publish)', false)
            ->assertSee('Chính Sách Đính Chính, Sửa Bài & Gỡ Bài', false)
            ->assertSee('128/GP-BTTTT');
    }

    public function test_contact_page_is_accessible_and_renders_correctly(): void
    {
        $response = $this->get(route('pages.contact'));

        $response->assertOk()
            ->assertSee('Liên Hệ Ban Biên Tập & Hợp Tác Quảng Cáo', false)
            ->assertSee('1900 8888')
            ->assertSee('quangcao@newshub.vn')
            ->assertSee('Trụ sở chính (Hà Nội)')
            ->assertSee('Gửi Thư Trực Tuyến Tới Tòa Soạn');
    }

    public function test_contact_form_validates_required_fields(): void
    {
        $response = $this->from(route('pages.contact'))
            ->post(route('pages.contact.submit'), []);

        $response->assertRedirect(route('pages.contact'))
            ->assertSessionHasErrors(['name', 'email', 'topic', 'subject', 'message']);
    }

    public function test_contact_form_submits_successfully_and_flashes_status(): void
    {
        Log::shouldReceive('info')
            ->once()
            ->withArgs(function (string $message, array $context): bool {
                return $message === 'Contact message received via NewsHub portal'
                    && $context['name'] === 'Độc Giả Nguyễn Văn A'
                    && $context['email'] === 'reader@example.com'
                    && $context['topic'] === 'hotline';
            });

        $response = $this->from(route('pages.contact'))
            ->post(route('pages.contact.submit'), [
                'name' => 'Độc Giả Nguyễn Văn A',
                'email' => 'reader@example.com',
                'phone' => '0987654321',
                'topic' => 'hotline',
                'subject' => 'Tin nóng về công nghệ AI tại Việt Nam',
                'message' => 'Tôi muốn cung cấp tài liệu về nghiên cứu AI mới vừa công bố.',
            ]);

        $response->assertRedirect(route('pages.contact'))
            ->assertSessionHas('status');
    }

    public function test_footer_contains_links_to_all_four_pages(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertSee(route('pages.privacy'))
            ->assertSee(route('pages.terms'))
            ->assertSee(route('pages.moderation-policy'))
            ->assertSee(route('pages.contact'));
    }
}
