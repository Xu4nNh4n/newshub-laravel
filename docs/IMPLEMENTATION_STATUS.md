# Trạng thái triển khai NewsHub

> Cập nhật ngày 27/09/2026. Tài liệu này ghi nhận trạng thái thực tế của mã nguồn hiện tại, những việc còn thiếu và thứ tự nên thực hiện tiếp.

## 1. Kết luận nhanh

Phiên bản đồ án NewsHub đã hoàn thành các nhóm chức năng chính trong phạm vi phiên bản 1: xác thực, phân quyền, quản lý bài viết, duyệt và hẹn giờ xuất bản, trang tin công khai, bình luận, báo cáo, yêu thích, thống kê, SEO cơ bản, upload ảnh an toàn và kiểm thử tự động.

Phần còn lại không phải là thiếu nghiệp vụ chính. Chủ yếu là cấu hình dịch vụ thật, triển khai production, kiểm thử nghiệm thu thủ công và chuẩn bị tài liệu/demo bảo vệ.

Trạng thái kiểm tra gần nhất:

- Laravel Pint: đạt.
- Composer platform requirements: đạt.
- PHPUnit: **102 test, 401 assertion đều đạt**.
- PHP dùng để kiểm tra: **PHP 8.5.8 NTS**.
- GD và WebP: đã bật và kiểm tra hoạt động.
- Frontend Vite: đã có bản build trong `public/build`.

## 2. Những phần đã triển khai

### 2.1. Nền tảng và cơ sở dữ liệu

- Khởi tạo ứng dụng Laravel và cấu hình môi trường phát triển.
- Thiết kế các bảng nghiệp vụ:
  - `users`;
  - `categories`;
  - `tags`;
  - `posts`;
  - `post_tag`;
  - `comments`;
  - `comment_reports`;
  - `favorites`;
  - `post_views`;
  - `activity_logs`.
- Có khóa ngoại, unique constraint và index phục vụ truy vấn/báo cáo.
- Có soft delete cho dữ liệu cần khôi phục hoặc giữ lịch sử.
- Có factory và dữ liệu demo cho môi trường local/testing.
- Seeder chạy lặp không nhân đôi dữ liệu và không tạo tài khoản demo ở production.
- Đã có tài liệu ERD tại `docs/ERD.md` và sơ đồ luồng tại `docs/SYSTEM_DIAGRAMS.md`.

### 2.2. Xác thực, tài khoản và bảo mật

- Đăng ký, đăng nhập và đăng xuất.
- Xác minh email bằng liên kết có chữ ký và thời hạn.
- Gửi lại email xác minh có rate limit.
- Quên mật khẩu và đặt lại mật khẩu bằng token có thời hạn.
- Đổi mật khẩu yêu cầu nhập đúng mật khẩu hiện tại.
- Cập nhật tên và avatar.
- Phân quyền `user`, `author`, `admin` bằng middleware và policy.
- Chặn tài khoản bị khóa/vô hiệu hóa.
- Người chưa xác minh vẫn đăng nhập và đọc tin được nhưng bị chặn các thao tác tạo dữ liệu nhạy cảm.
- Rate limit cho đăng nhập, quên mật khẩu, xác minh email, bình luận và báo cáo bình luận.
- Validation được tách vào Form Request ở các luồng chính.
- Có trang lỗi 403, 404 và 500 riêng.

### 2.3. Chuyên mục và thẻ

- Admin có thể tạo, sửa, xem danh sách và xóa chuyên mục/thẻ.
- Không cho xóa chuyên mục đang có bài viết.
- Khi xóa thẻ, liên kết trong `post_tag` được xóa theo cascade nhưng bài viết không bị xóa.
- Có kiểm tra quyền và validation cho các thao tác quản trị.

### 2.4. Bài viết và quy trình duyệt

- Author/Admin có thể tạo, sửa, xóa và xem danh sách bài viết.
- Có trạng thái bản nháp, chờ duyệt, từ chối, đã duyệt/xuất bản, ẩn và lưu trữ.
- Author có thể xem trước bài chưa công khai.
- Author gửi bài để Admin duyệt.
- Admin có thể duyệt, từ chối kèm lý do, ẩn, lưu trữ hoặc khôi phục bài.
- Các thay đổi trạng thái quan trọng được xử lý trong service và transaction.
- Có activity log cho thao tác quản trị quan trọng.
- Hẹn giờ xuất bản dùng cơ chế query-time:
  - bài đã duyệt có thể có `published_at` trong tương lai;
  - trang công khai chỉ lấy bài có `published_at <= now()`;
  - không phụ thuộc Scheduler để đổi trạng thái.

### 2.5. Trang tin công khai

- Trang chủ và danh sách tin.
- Trang chi tiết bài viết theo slug.
- Tìm kiếm theo từ khóa.
- Lọc theo chuyên mục và thẻ.
- Sắp xếp mới nhất hoặc phổ biến.
- Phân trang.
- Trang hồ sơ tác giả.
- Danh sách bài liên quan.
- Chỉ hiển thị bài và chuyên mục đủ điều kiện công khai.
- Query đã sử dụng eager loading ở các màn hình cần quan hệ để hạn chế N+1.

### 2.6. Bình luận và báo cáo

- Bình luận hai cấp: bình luận gốc và một cấp trả lời.
- Tạo, sửa và xóa bình luận theo quyền sở hữu.
- Admin có thể ẩn, hiện hoặc xóa bình luận.
- Người dùng có trang xem các bình luận của mình.
- Nội dung hiển thị được escape để hạn chế XSS.
- Người dùng có thể báo cáo bình luận theo lý do định sẵn.
- Một người chỉ được báo cáo một bình luận một lần trong vòng đời bình luận.
- Database có unique constraint chống báo cáo trùng.
- Admin có thể bỏ qua, ẩn hoặc xóa khi xử lý báo cáo.
- Xử lý báo cáo sử dụng transaction và ghi activity log.

### 2.7. Yêu thích và lượt xem

- Người dùng đã xác minh có thể thêm hoặc bỏ bài yêu thích.
- Có trang danh sách bài yêu thích.
- Lượt xem được chống tăng vô hạn khi tải lại trang:
  - user đăng nhập dùng `user_id`;
  - guest ưu tiên `session_id`;
  - IP hash là phương án dự phòng;
  - cùng một danh tính chỉ được tính một lượt/bài trong 24 giờ.
- Khi ghi lượt xem, hệ thống đồng thời tạo `post_views` và tăng `posts.view_count` trong transaction.

### 2.8. Dashboard và quản trị

- Dashboard theo vai trò.
- Thống kê người dùng, bài viết, bình luận, lượt xem và yêu thích.
- Tách số bài đã duyệt khỏi số bài thực sự đang hiển thị công khai.
- Admin có màn hình quản lý user, bài viết, chuyên mục, thẻ, bình luận, báo cáo và activity log.
- Các danh sách quản trị có tìm kiếm/lọc và phân trang phù hợp.

### 2.9. SEO cơ bản

- Bài viết có `meta_title` và `meta_description`.
- Có giá trị fallback khi tác giả không nhập metadata riêng.
- Trang chi tiết có Open Graph metadata và ảnh đại diện.
- Có `sitemap.xml` chỉ đưa dữ liệu đủ điều kiện công khai vào sitemap.

### 2.10. Email giao dịch

- Đã có email xác minh tài khoản.
- Đã có email đặt lại mật khẩu.
- Email có cả phiên bản HTML và plain text.
- Giao diện email đã được thiết kế riêng cho NewsHub, responsive và có cảnh báo bảo mật/thời hạn liên kết.
- Có hướng dẫn cấu hình Gmail tại `docs/EMAIL_SETUP.md`.
- Khi chưa có Gmail thật, ứng dụng dùng `MAIL_MAILER=log` để không gửi nhầm ra ngoài.

### 2.11. Upload, resize và tối ưu ảnh

- Kiểm tra MIME type, phần mở rộng và dung lượng file.
- Chặn file thực thi giả dạng ảnh.
- Chặn ảnh đầu vào lớn hơn 4096 × 4096 pixel để giảm nguy cơ ảnh nén gây hết RAM.
- Tên file đầu ra dùng UUID ngẫu nhiên.
- Dùng Intervention Image 4 và PHP GD.
- Ảnh bài viết:
  - giữ đúng tỉ lệ;
  - giới hạn tối đa 1600 × 1200;
  - không phóng lớn ảnh nhỏ.
- Avatar:
  - crop vuông từ tâm;
  - giới hạn tối đa 512 × 512;
  - không phóng lớn ảnh nhỏ.
- Tất cả ảnh được re-encode thành WebP chất lượng 82 và loại metadata.
- Mỗi upload chỉ lưu **một file WebP**; không giữ ảnh gốc và không sinh nhiều thumbnail không sử dụng.
- Khi thay ảnh, file cũ được xóa.
- Nếu cập nhật database thất bại, file mới được dọn để không tạo file rác.
- Ảnh trong danh sách sử dụng lazy loading.
- `ext-gd` đã được khai báo trong Composer để môi trường thiếu GD báo lỗi sớm.

### 2.12. Logging, triển khai và tài liệu

- Sử dụng Laravel log theo môi trường.
- Có tài liệu cài đặt trong `README.md`.
- Có hướng dẫn Gmail tại `docs/EMAIL_SETUP.md`.
- Có hướng dẫn triển khai, backup và phục hồi tại `docs/DEPLOYMENT.md`.
- Có ERD và các sơ đồ use case/activity/sequence.
- Có kế hoạch đồ án đầy đủ tại `KE_HOACH_DO_AN_WEBSITE_TIN_TUC.md`.

### 2.13. Kiểm thử tự động

Các nhóm đã có Feature Test gồm:

- xác thực và phân quyền;
- tài khoản bị khóa và email chưa xác minh;
- quản lý user, chuyên mục và thẻ;
- workflow bài viết và duyệt bài;
- hẹn giờ/điều kiện hiển thị công khai;
- tìm kiếm, lọc và trang tin;
- bình luận hai cấp và kiểm duyệt;
- báo cáo bình luận;
- yêu thích;
- chống trùng lượt xem;
- dashboard thống kê;
- upload, resize, crop và WebP;
- SEO/sitemap;
- seeder demo;
- trang lỗi.

## 3. Những phần chưa hoàn tất

### 3.1. Chưa cấu hình Gmail thật

Trạng thái hiện tại:

- `MAIL_MAILER=log`;
- email được render đầy đủ nhưng chỉ ghi vào `storage/logs/laravel.log`;
- chưa thực hiện gửi/nhận qua Gmail thật.

Lý do chưa thể hoàn tất:

- cần một Gmail dành cho NewsHub;
- Gmail phải bật xác minh hai bước;
- cần App Password 16 ký tự;
- thông tin này là bí mật và phải do chủ tài khoản cung cấp trực tiếp trong `.env`, không được ghi vào Git hoặc tài liệu.

### 3.2. Chưa triển khai lên production

Mã nguồn đã có hướng dẫn triển khai nhưng chưa có môi trường thật. Còn thiếu:

- nhà cung cấp hosting/VPS/Laravel Cloud;
- domain và HTTPS;
- database production;
- biến môi trường production;
- quyền ghi cho `storage` và `bootstrap/cache`;
- cấu hình mail production;
- chạy migration, build frontend, storage link và smoke test trên server.

Không thể tự hoàn tất phần này khi chưa có tài khoản hạ tầng, domain và lựa chọn nơi triển khai.

### 3.3. Chưa tự động hóa backup production

Đã có quy trình backup/restore trong tài liệu, nhưng chưa thể tạo lịch backup thật vì chưa có server và nơi lưu bản sao. Sau khi có production cần:

- backup database định kỳ;
- backup `storage/app/public` cùng thời điểm;
- lưu bản backup ngoài máy chủ ứng dụng;
- đặt retention policy;
- thử phục hồi định kỳ thay vì chỉ kiểm tra file tồn tại.

### 3.4. Chưa kiểm thử email end-to-end với nhà cung cấp thật

Test hiện tại xác nhận application tạo đúng notification, URL và giao diện email. Vẫn cần kiểm tra thực tế:

- email có đến inbox hay vào spam;
- nút xác minh/đặt lại mật khẩu hoạt động đúng domain production;
- `APP_URL` tạo URL HTTPS đúng;
- tên người gửi và địa chỉ người gửi hiển thị đúng;
- giao diện trên Gmail desktop và mobile.

### 3.5. Chưa nghiệm thu giao diện thủ công đầy đủ

Automated test đã đạt, nhưng trước khi bảo vệ vẫn nên chạy UAT bằng trình duyệt với ba vai trò:

- Admin;
- Author;
- User/Guest.

Cần kiểm tra thêm kích thước mobile/tablet/desktop, nội dung tiếng Việt dài, ảnh dọc/ngang, trạng thái rỗng và thông báo lỗi thực tế.

### 3.6. Chưa kiểm thử production bằng MySQL

Môi trường kiểm thử hiện ưu tiên SQLite. Migration và query được viết theo Laravel/Eloquent, nhưng nếu production dùng MySQL vẫn phải chạy:

- migrate trên database MySQL thử nghiệm;
- toàn bộ test quan trọng hoặc smoke test;
- kiểm tra charset/collation tiếng Việt;
- kiểm tra index và hiệu năng với dữ liệu lớn hơn dữ liệu demo.

### 3.7. Vấn đề Git của máy hiện tại chưa xử lý

Git đang nhận repository cha tại `D:/Code/code`, có cảnh báo `dubious ownership`, và thư mục `PhpProject` xuất hiện như một thư mục chưa được track trong repo cha.

Ảnh hưởng hiện tại:

- không ảnh hưởng việc chạy Laravel hoặc PHPUnit;
- làm các công cụ dựa vào Git như Pint `--dirty` không hoạt động đúng;
- có nguy cơ commit lẫn các project không liên quan nếu thao tác tại repo cha.

Chưa tự sửa vì thay đổi ownership, global Git config hoặc tách repository là quyết định ảnh hưởng ngoài phạm vi riêng của NewsHub. Phương án nên chọn là tạo repository Git riêng cho `PhpProject`, sau đó chỉ commit các file thuộc dự án này.

### 3.8. Chưa có giám sát lỗi bên ngoài

- Laravel log đã hoạt động.
- Chưa tích hợp Sentry hoặc dịch vụ cảnh báo lỗi tương tự.
- Đây không phải yêu cầu bắt buộc của phiên bản đồ án; chỉ nên thêm sau khi có production nếu thật sự cần.

## 4. Những phần cố ý không triển khai trong phiên bản 1

Các mục dưới đây là ngoài phạm vi, không được xem là lỗi hoặc thiếu chức năng bắt buộc:

- đăng nhập Google/Facebook;
- ứng dụng mobile;
- thông báo real-time;
- trình soạn thảo WYSIWYG nâng cao;
- đa ngôn ngữ;
- gợi ý bài viết bằng AI;
- Elasticsearch/Meilisearch;
- S3/CDN hoặc dịch vụ xử lý ảnh cloud;
- sinh nhiều kích thước thumbnail khi giao diện hiện tại không sử dụng;
- Scheduler đổi trạng thái bài `pending → published`, vì hệ thống đã chọn query-time filter làm nguồn sự thật.

Chỉ bổ sung các mục này khi yêu cầu đồ án thay đổi hoặc sau khi phiên bản 1 đã triển khai ổn định.

## 5. Việc cần làm tiếp theo

### Ưu tiên 1 — Hoàn tất email thật

1. Chuẩn bị Gmail riêng cho NewsHub.
2. Bật xác minh hai bước và tạo App Password.
3. Điền `MAIL_*` trong `.env` theo `docs/EMAIL_SETUP.md`.
4. Chạy `php artisan optimize:clear`.
5. Gửi thử email xác minh và quên mật khẩu.
6. Xác nhận inbox, spam, liên kết và thời hạn token.

### Ưu tiên 2 — Nghiệm thu local

1. Mở terminal mới để PATH nhận PHP 8.5.8 có GD.
2. Chạy `php -v` và `php -m` để xác nhận `gd`.
3. Chạy `composer install`.
4. Chạy `php artisan migrate --seed`; chỉ dùng `migrate:fresh` với database demo đã xác nhận có thể xóa.
5. Chạy `npm run build`.
6. Chạy `php artisan test --compact`.
7. Kiểm thử thủ công toàn bộ kịch bản demo bằng ba tài khoản mẫu.
8. Upload thử JPEG, PNG và WebP có tỉ lệ khác nhau.

### Ưu tiên 3 — Chuẩn bị bảo vệ đồ án

1. Chốt kịch bản demo theo mục 16 của kế hoạch.
2. Chụp ảnh các màn hình chính.
3. Xuất ERD và sơ đồ hệ thống thành hình nếu báo cáo Word/PDF không render Mermaid.
4. Chuẩn bị dữ liệu demo ổn định.
5. Chuẩn bị giải thích các quyết định quan trọng:
   - query-time scheduling;
   - chống trùng view 24 giờ;
   - chống báo cáo trùng;
   - quyền của email chưa xác minh;
   - resize/re-encode WebP và không giữ ảnh dư;
   - transaction, policy và rate limit.

### Ưu tiên 4 — Triển khai production

1. Chọn nơi triển khai và database.
2. Cấu hình domain/HTTPS.
3. Đảm bảo PHP production có `ext-gd` và WebP.
4. Cấu hình `.env` production, tuyệt đối không dùng `.env` local.
5. Chạy quy trình trong `docs/DEPLOYMENT.md`.
6. Kiểm tra login, email, upload ảnh, sitemap và các quyền quan trọng.
7. Thiết lập backup database và upload.
8. Chạy thử quy trình phục hồi ít nhất một lần.

### Ưu tiên 5 — Nâng cấp sau phiên bản 1

Chỉ cân nhắc sau khi các mục trên hoàn tất:

- Sentry/giám sát lỗi;
- object storage/S3;
- queue email nếu lượng gửi tăng;
- CDN ảnh;
- full-text search chuyên dụng;
- CI/CD và chạy test tự động khi push code.

## 6. Các lệnh kiểm tra trước khi bàn giao

```bash
php -v
php -m
composer validate --strict --no-check-publish
composer check-platform-reqs
php artisan migrate:status
php artisan route:list --except-vendor
vendor/bin/pint --format agent
php artisan test --compact
npm run build
```

Kết quả chỉ được xem là hoàn tất khi các lệnh phù hợp đều thành công và các luồng email/upload/quyền truy cập đã được kiểm tra thủ công trên môi trường sẽ dùng để demo.

## 7. Nguyên tắc tránh dữ liệu và dependency dư

- Không lưu ảnh gốc sau khi đã chuyển WebP.
- Không sinh thumbnail mà giao diện không sử dụng.
- Khi thay/xóa ảnh phải dọn file cũ.
- Không lưu credential thật trong repository.
- Không chạy seeder demo ở production.
- Không thêm package nếu Laravel hoặc code hiện tại đã đáp ứng yêu cầu.
- `intervention/gif` là dependency bắt buộc đi kèm Intervention Image, không phải package được thêm riêng và không nên xóa thủ công.
- File cache/build/log không được coi là dữ liệu nghiệp vụ; cần quản lý bằng `.gitignore` và quy trình deploy phù hợp.

## 8. Tài liệu liên quan

- Kế hoạch tổng thể: `KE_HOACH_DO_AN_WEBSITE_TIN_TUC.md`.
- ERD: `docs/ERD.md`.
- Sơ đồ hệ thống: `docs/SYSTEM_DIAGRAMS.md`.
- Cấu hình Gmail: `docs/EMAIL_SETUP.md`.
- Triển khai và backup: `docs/DEPLOYMENT.md`.
- Cài đặt nhanh: `README.md`.
