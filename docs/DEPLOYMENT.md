# Triển khai, sao lưu và phục hồi

## Điều kiện trước khi triển khai

- Máy chủ có PHP 8.5, Composer, Node.js và web server trỏ document root vào thư mục `public`.
- Có domain, HTTPS, database và dịch vụ gửi mail thực tế.
- Tạo `.env` production riêng; không đưa file này hoặc khóa bí mật vào Git.
- Đặt `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` đúng domain và cấu hình `DB_*`, `MAIL_*`.

## Quy trình phát hành

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan storage:link
php artisan migrate --force
php artisan optimize
```

Web server cần quyền ghi vào `storage` và `bootstrap/cache`. Kiểm tra `/up`, đăng nhập, upload ảnh và gửi email sau mỗi lần phát hành. Không chạy `db:seed` ở production vì seeder demo chỉ phục vụ local/testing.

## Sao lưu

Phải sao lưu đồng thời database và thư mục `storage/app/public`; thiếu một trong hai sẽ làm mất dữ liệu hoặc ảnh upload.

SQLite, sau khi đưa ứng dụng vào maintenance mode:

```bash
php artisan down
copy database\database.sqlite D:\backups\newshub-database.sqlite
php artisan up
```

MySQL:

```bash
mysqldump --single-transaction --routines --triggers -u DB_USER -p DB_NAME > newshub.sql
```

Thư mục upload nên được snapshot/copy bởi công cụ backup của máy chủ. Mỗi bản backup cần có thời gian tạo, được lưu ngoài máy chủ ứng dụng và kiểm tra khả năng đọc định kỳ. Không lưu mật khẩu database trong script backup.

## Phục hồi

1. Bật maintenance mode bằng `php artisan down`.
2. Sao lưu trạng thái lỗi hiện tại trước khi ghi đè.
3. Khôi phục database đúng phiên bản và khôi phục `storage/app/public`.
4. Chạy `php artisan optimize:clear`, sau đó `php artisan optimize`.
5. Kiểm tra migration bằng `php artisan migrate:status`; chỉ chạy migration còn thiếu sau khi đã xác nhận backup.
6. Chạy smoke test, mở lại bằng `php artisan up`.

Lệnh và đường dẫn backup phải được điều chỉnh theo hệ điều hành, nhà cung cấp database và chính sách lưu trữ của môi trường thực tế.
