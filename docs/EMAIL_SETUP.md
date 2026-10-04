# Cấu hình Gmail SMTP

Ứng dụng đã có email xác minh tài khoản và đặt lại mật khẩu bằng liên kết bảo mật. Khi chưa có credential, giữ `MAIL_MAILER=log`; nội dung email sẽ nằm trong `storage/logs/laravel.log`.

## Thông tin cần chuẩn bị

1. Một Gmail dành riêng cho NewsHub.
2. Bật xác minh 2 bước trên tài khoản Google.
3. Tạo App Password 16 ký tự cho ứng dụng. Không sử dụng mật khẩu đăng nhập Gmail chính.

App Password có thể không xuất hiện với tài khoản tổ chức, tài khoản chỉ dùng security key hoặc tài khoản bật Advanced Protection. Xem hướng dẫn chính thức của Google: <https://support.google.com/accounts/answer/185833>.

## Cấu hình `.env`

```env
MAIL_MAILER=smtp
MAIL_SCHEME=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME="your-newshub-account@gmail.com"
MAIL_PASSWORD="your-16-character-app-password"
MAIL_FROM_ADDRESS="your-newshub-account@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

`MAIL_FROM_ADDRESS` nên trùng với tài khoản Gmail xác thực. Không ghi credential thật vào `.env.example`, README, ảnh chụp hoặc Git.

Sau khi đổi `.env`, chạy:

```bash
php artisan optimize:clear
```

Sau đó đăng ký bằng một email nhận thử hoặc bấm “gửi lại email xác minh”. Tiếp tục kiểm tra luồng “quên mật khẩu”. Nếu thư không tới, kiểm tra spam và `storage/logs/laravel.log`.

Gmail phù hợp cho đồ án và lưu lượng nhỏ. Khi triển khai production có nhiều người dùng, nên chuyển sang nhà cung cấp email giao dịch có domain đã xác minh.
