# NewsHub

Website tin tức Laravel cho độc giả, tác giả và quản trị viên, gồm quy trình duyệt/hẹn giờ bài, bình luận hai cấp, báo cáo, yêu thích, thống kê và activity log.

## Cài đặt dự án

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

macOS/Linux dùng `cp .env.example .env`. SQLite là mặc định; nếu dùng MySQL hãy cập nhật `DB_*` trước khi migrate. Seeder chỉ chạy ở `local`/`testing`, chạy lặp không nhân dữ liệu. Ba tài khoản demo `admin@newshub.test`, `author@newshub.test`, `reader@newshub.test` cùng mật khẩu `password`.

Kiểm tra dự án bằng `php artisan test --compact`; phát triển đồng thời bằng `composer run dev`. Tài liệu gồm [trạng thái triển khai](docs/IMPLEMENTATION_STATUS.md), [ERD](docs/ERD.md), [sơ đồ hệ thống](docs/SYSTEM_DIAGRAMS.md), [cấu hình Gmail](docs/EMAIL_SETUP.md) và [kế hoạch đồ án](KE_HOACH_DO_AN_WEBSITE_TIN_TUC.md).

Khi production, làm theo [hướng dẫn triển khai và sao lưu](docs/DEPLOYMENT.md). Không dùng tài khoản demo trên môi trường thật.
