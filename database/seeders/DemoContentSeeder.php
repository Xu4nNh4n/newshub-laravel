<?php

namespace Database\Seeders;

use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = collect([
            ['name' => 'Quản trị viên Demo', 'email' => 'admin@newshub.test', 'role' => UserRole::Admin],
            ['name' => 'Tác giả Demo', 'email' => 'author@newshub.test', 'role' => UserRole::Author],
            ['name' => 'Độc giả Demo', 'email' => 'reader@newshub.test', 'role' => UserRole::User],
        ])->mapWithKeys(function (array $account): array {
            $user = User::query()->updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'role' => $account['role'],
                    'status' => UserStatus::Active,
                ],
            );

            return [$account['role']->value => $user];
        });

        $categories = collect([
            ['name' => 'Công nghệ', 'slug' => 'cong-nghe'],
            ['name' => 'Trí tuệ nhân tạo', 'slug' => 'tri-tue-nhan-tao'],
            ['name' => 'An ninh mạng', 'slug' => 'an-ninh-mang'],
            ['name' => 'Phần mềm', 'slug' => 'phan-mem'],
            ['name' => 'Phần cứng', 'slug' => 'phan-cung'],
            ['name' => 'Điện toán đám mây', 'slug' => 'dien-toan-dam-may'],
            ['name' => 'Dữ liệu', 'slug' => 'du-lieu'],
            ['name' => 'Khởi nghiệp công nghệ', 'slug' => 'khoi-nghiep-cong-nghe'],
            ['name' => 'Chuyển đổi số', 'slug' => 'chuyen-doi-so'],
            ['name' => 'Kinh doanh', 'slug' => 'kinh-doanh'],
            ['name' => 'Đời sống', 'slug' => 'doi-song'],
        ])->mapWithKeys(fn (array $category): array => [
            $category['slug'] => Category::query()->updateOrCreate(
                ['slug' => $category['slug']],
                [
                    'parent_id' => null,
                    'name' => $category['name'],
                    'description' => "Tin tức {$category['name']}",
                    'status' => 'active',
                ],
            ),
        ]);

        foreach ([
            'tri-tue-nhan-tao',
            'an-ninh-mang',
            'phan-mem',
            'phan-cung',
            'dien-toan-dam-may',
            'du-lieu',
            'chuyen-doi-so',
        ] as $technologyChildSlug) {
            $categories[$technologyChildSlug]->update(['parent_id' => $categories['cong-nghe']->id]);
        }

        $categories['khoi-nghiep-cong-nghe']->update(['parent_id' => $categories['kinh-doanh']->id]);

        $tags = collect([
            'Laravel', 'PHP', 'AI', 'Startup', 'Cybersecurity', 'Cloud', 'DevOps',
            'Open Source', 'Dữ liệu lớn', 'Điện thoại thông minh', 'IoT', 'Web',
        ])->mapWithKeys(function (string $name): array {
            $slug = str($name)->slug()->toString();

            return [$slug => Tag::query()->updateOrCreate(['slug' => $slug], ['name' => $name])];
        });

        $articles = [
            [
                'Laravel hiện đại cho ứng dụng tin tức',
                'laravel-hien-dai-cho-ung-dung-tin-tuc',
                'phan-mem',
                ['laravel', 'php', 'web'],
                'Laravel hiện đại: nền tảng vững chắc cho ứng dụng tin tức',
                'Khám phá cách tổ chức ứng dụng tin tức với Laravel, Eloquent và các quy tắc bảo mật phổ biến.',
            ],
            [
                'Mô hình AI nhỏ giúp tối ưu chi phí cho doanh nghiệp',
                'mo-hinh-ai-nho-toi-uu-chi-phi',
                'tri-tue-nhan-tao',
                ['ai', 'du-lieu-lon'],
                'Mô hình AI nhỏ giúp doanh nghiệp tối ưu chi phí',
                'Các mô hình gọn nhẹ đang mở ra cách triển khai AI thực tế, tiết kiệm hạ tầng và dễ kiểm soát dữ liệu.',
            ],
            [
                'Năm nguyên tắc bảo vệ tài khoản trước tấn công lừa đảo',
                'nam-nguyen-tac-bao-ve-tai-khoan',
                'an-ninh-mang',
                ['cybersecurity'],
                'Năm nguyên tắc bảo vệ tài khoản trước lừa đảo',
                'Mật khẩu riêng biệt, xác thực đa yếu tố và thói quen kiểm tra liên kết giúp giảm đáng kể rủi ro bị chiếm tài khoản.',
            ],
            [
                'Điện toán đám mây thay đổi cách đội ngũ phát hành phần mềm',
                'dien-toan-dam-may-thay-doi-phat-hanh-phan-mem',
                'dien-toan-dam-may',
                ['cloud', 'devops'],
                'Điện toán đám mây thay đổi quy trình phát hành phần mềm',
                'Tự động hóa hạ tầng, quan sát hệ thống và triển khai liên tục giúp đội ngũ phần mềm phát hành ổn định hơn.',
            ],
            [
                'Từ dữ liệu thô đến quyết định kinh doanh có giá trị',
                'tu-du-lieu-tho-den-quyet-dinh-kinh-doanh',
                'du-lieu',
                ['du-lieu-lon', 'ai'],
                'Từ dữ liệu thô đến quyết định kinh doanh có giá trị',
                'Một quy trình dữ liệu tốt cần bắt đầu từ chất lượng dữ liệu, cách đo lường và câu hỏi kinh doanh rõ ràng.',
            ],
            [
                'Thiết kế API dễ bảo trì cho sản phẩm phát triển nhanh',
                'thiet-ke-api-de-bao-tri',
                'phan-mem',
                ['php', 'web', 'open-source'],
                'Thiết kế API dễ bảo trì cho sản phẩm phát triển nhanh',
                'Phiên bản API, validation, phân quyền và tài liệu nhất quán là nền tảng để sản phẩm mở rộng mà không tạo nợ kỹ thuật.',
            ],
            [
                'Những điểm cần kiểm tra khi chọn laptop cho lập trình viên',
                'chon-laptop-cho-lap-trinh-vien',
                'phan-cung',
                ['devops'],
                'Những điểm cần kiểm tra khi chọn laptop cho lập trình viên',
                'CPU, bộ nhớ, khả năng nâng cấp và thời lượng pin là các tiêu chí quan trọng hơn thông số quảng cáo đơn lẻ.',
            ],
            [
                'Thiết bị IoT và bài toán bảo mật ngay từ thiết kế',
                'thiet-bi-iot-va-bao-mat-ngay-tu-thiet-ke',
                'an-ninh-mang',
                ['iot', 'cybersecurity'],
                'Thiết bị IoT cần được bảo mật ngay từ thiết kế',
                'Quản lý danh tính thiết bị, cập nhật firmware và giới hạn quyền truy cập là ba lớp bảo vệ cần có trong hệ thống IoT.',
            ],
            [
                'Startup công nghệ nên đo lường điều gì trước khi mở rộng',
                'startup-cong-nghe-do-luong-truoc-khi-mo-rong',
                'khoi-nghiep-cong-nghe',
                ['startup', 'du-lieu-lon'],
                'Startup công nghệ nên đo lường điều gì trước khi mở rộng',
                'Tăng trưởng bền vững cần gắn với tỷ lệ giữ chân, chi phí thu hút khách hàng và giá trị sản phẩm mang lại.',
            ],
            [
                'Mã nguồn mở giúp đội ngũ nhỏ tăng tốc như thế nào',
                'ma-nguon-mo-giup-doi-ngu-nho-tang-toc',
                'phan-mem',
                ['open-source', 'php'],
                'Mã nguồn mở giúp đội ngũ nhỏ tăng tốc như thế nào',
                'Sử dụng thư viện mở đúng cách giúp tiết kiệm thời gian, nhưng vẫn cần kiểm tra giấy phép, bảo mật và khả năng bảo trì.',
            ],
            [
                'Chuyển đổi số bắt đầu từ quy trình chứ không chỉ từ phần mềm',
                'chuyen-doi-so-bat-dau-tu-quy-trinh',
                'chuyen-doi-so',
                ['cloud', 'startup'],
                'Chuyển đổi số bắt đầu từ quy trình chứ không chỉ từ phần mềm',
                'Công nghệ chỉ tạo ra giá trị khi giải quyết đúng điểm nghẽn và được người dùng nội bộ chấp nhận.',
            ],
            [
                'Ứng dụng AI có trách nhiệm trong tòa soạn số',
                'ung-dung-ai-co-trach-nhiem-trong-toa-soan',
                'tri-tue-nhan-tao',
                ['ai', 'cybersecurity'],
                'Ứng dụng AI có trách nhiệm trong tòa soạn số',
                'AI có thể hỗ trợ phân loại và gợi ý nội dung, nhưng biên tập viên vẫn cần kiểm chứng nguồn và chịu trách nhiệm cuối cùng.',
            ],
            [
                'DevOps và văn hóa cải tiến liên tục trong nhóm kỹ thuật',
                'devops-va-van-hoa-cai-tien-lien-tuc',
                'dien-toan-dam-may',
                ['devops', 'cloud'],
                'DevOps và văn hóa cải tiến liên tục trong nhóm kỹ thuật',
                'DevOps không chỉ là công cụ triển khai mà còn là cách phối hợp để phát hiện lỗi sớm và cải thiện liên tục.',
            ],
            [
                'Tự động hóa báo cáo giúp doanh nghiệp tiết kiệm thời gian',
                'tu-dong-hoa-bao-cao-doanh-nghiep',
                'chuyen-doi-so',
                ['du-lieu-lon', 'ai'],
                'Tự động hóa báo cáo giúp doanh nghiệp tiết kiệm thời gian',
                'Chuẩn hóa dữ liệu đầu vào và thiết lập chỉ số rõ ràng giúp báo cáo tự động đáng tin cậy hơn.',
            ],
            [
                'Xu hướng nghề nghiệp lập trình PHP trong sản phẩm web',
                'xu-huong-nghe-nghiep-lap-trinh-php',
                'cong-nghe',
                ['php', 'laravel', 'web'],
                'Xu hướng nghề nghiệp lập trình PHP trong sản phẩm web',
                'Nền tảng PHP hiện đại yêu cầu lập trình viên hiểu cả kiến trúc, kiểm thử, bảo mật và vận hành sản phẩm.',
            ],
        ];

        foreach ($articles as $index => [$title, $slug, $categorySlug, $tagSlugs, $metaTitle, $metaDescription]) {
            $post = Post::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'author_id' => $accounts[UserRole::Author->value]->id,
                    'category_id' => $categories[$categorySlug]->id,
                    'title' => $title,
                    'meta_title' => $metaTitle,
                    'meta_description' => $metaDescription,
                    'summary' => $metaDescription,
                    'content' => "{$metaDescription} Bài viết cung cấp góc nhìn tổng quan và các gợi ý thực hành phù hợp cho độc giả NewsHub.",
                    'status' => PostStatus::Published,
                    'is_featured' => $index < 2,
                    'published_at' => now()->subDays($index + 1),
                ],
            );
            $post->tags()->sync($tags->only($tagSlugs)->pluck('id')->all());
        }
    }
}
