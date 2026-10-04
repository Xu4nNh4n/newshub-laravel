# 06. CƠ CHẾ BẢO MẬT & HỆ THỐNG KIỂM THỬ TỰ ĐỘNG

---

## 🔒 1. CÁC CƠ CHẾ BẢO MẬT CỐT LÕI (SECURITY ARCHITECTURE)

Hệ thống được thiết kế tuân thủ nghiêm ngặt các tiêu chuẩn an ninh ứng dụng web hiện đại (OWASP Top 10):

### 1. Phòng chống giả mạo yêu cầu (CSRF Protection)
- Mọi biểu mẫu (Form) gửi dữ liệu qua các phương thức `POST`, `PUT`, `PATCH`, `DELETE` đều bắt buộc phải đính kèm thẻ `@csrf` chứa mã token bảo mật ngẫu nhiên của phiên làm việc.
- Các yêu cầu AJAX tải ảnh lên đều phải truyền header `X-CSRF-TOKEN`. Mọi yêu cầu thiếu token đều bị máy chủ từ chối ngay lập tức (Lỗi 419 Page Expired).

### 2. Phòng chống tấn công chèn mã độc (XSS Prevention)
- Tận dụng cơ chế biên dịch tự động của Blade Engine: Toàn bộ dữ liệu hiển thị ra ngoài qua cú pháp `{{ $data }}` đều được đưa qua hàm `htmlspecialchars()` để chuyển đổi các ký tự nguy hiểm (`<`, `>`, `"`, `'`) thành HTML entities.
- Các trường nhập liệu phong phú (nội dung bài báo) được xử lý qua bộ lọc thẻ HTML nghiêm ngặt trước khi lưu trữ và render.

### 3. Phòng chống tấn công tiêm mã SQL (SQL Injection Prevention)
- 100% các thao tác truy vấn dữ liệu đều sử dụng **Laravel Eloquent ORM** và **PDO Parameterized Queries**.
- Dữ liệu đầu vào của người dùng tuyệt đối không bao giờ được nối trực tiếp vào câu lệnh SQL dạng chuỗi (`no raw string concatenation`), giúp miễn nhiễm hoàn toàn với các kỹ thuật SQL Injection.

### 4. Phân quyền chặt chẽ bằng Middleware & Policies
- **Phân quyền vai trò (Role-based):** Sử dụng middleware `role:admin` hoặc `role:author,admin` trên từng nhóm route. Người dùng thông thường cố tình truy cập vào `/admin` hoặc `/author` sẽ bị trả về mã lỗi 403 Forbidden.
- **Phân quyền cấp tài nguyên (Resource Policies):** Được bảo vệ bởi các lớp Policy:
  - Tác giả chỉ có thể chỉnh sửa bài viết do chính mình tạo ra (`PostPolicy`).
  - Tác giả không thể tự sửa hoặc xóa bài viết sau khi đã nộp duyệt hoặc đã xuất bản.
  - Quản trị viên không thể tự giáng chức, khóa tài khoản của chính mình hoặc tác động lên tài khoản của một Admin khác (`UserPolicy`).

### 5. Cơ chế đặt lại mật khẩu an toàn tuyệt đối (Zero-Knowledge Principle)
- Khi độc giả quên mật khẩu hoặc nhờ Admin hỗ trợ, hệ thống áp dụng cơ chế **Zero-Knowledge**:
  - Không cho phép nhập mật khẩu mới trực tiếp từ giao diện Admin.
  - Không sinh mật khẩu mặc định sơ sài (như `123456`) dễ bị lộ trên đường truyền.
  - Thay vào đó, hệ thống sinh một chuỗi mã bảo mật duy nhất (Token) có thời hạn 60 phút, mã hóa một chiều trong database và gửi đường link an toàn thẳng tới email của người dùng.
  - Người dùng tự tay mở email và đặt mật khẩu của riêng họ. Admin hoàn toàn không biết mật khẩu của độc giả.

### 6. Giới hạn tần suất gửi yêu cầu (Rate Limiting / Throttling)
- **Đăng nhập:** Giới hạn số lần đăng nhập sai liên tiếp để ngăn chặn tấn công dò mật khẩu tự động (Brute-force).
- **Tải ảnh lên (Media Upload):** Áp dụng middleware `throttle:30,1` (tối đa 30 tệp ảnh trong 1 phút) để ngăn chặn kẻ xấu cố tình spam làm cạn kiệt dung lượng ổ cứng máy chủ.

### 7. Ngăn chặn tài khoản bị khóa tức thì (`EnsureUserIsActive`)
- Mọi tài khoản sau khi bị Admin chuyển sang trạng thái `blocked` sẽ bị chặn ở cấp Middleware trong mọi thao tác kế tiếp, tự động đăng xuất và vô hiệu hóa phiên làm việc hiện tại.

---

## 📋 2. NHẬT KÝ KIỂM TOÁN TÒA SOẠN (AUDIT LOGGING)

Mọi hành động nhạy cảm trong hệ thống đều được lưu lại vĩnh viễn trong bảng `activity_logs`:
- Ai là người thực hiện? (`user_id`)
- Hành động là gì? (`action`: duyệt bài, từ chối bài, đổi quyền tài khoản, gửi link reset mật khẩu, ẩn bình luận, etc.)
- Đối tượng bị tác động? (`subject_type`, `subject_id`)
- Diễn giải bằng văn bản tiếng Việt chi tiết? (`description`)
- Mốc thời gian chính xác? (`created_at`)

Dữ liệu này có thể được Quản trị viên lọc tìm kiếm và xuất ra file CSV bất kỳ lúc nào để phục vụ công tác thanh tra tòa soạn.

---

## 🧪 3. HỆ THỐNG KIỂM THỬ TỰ ĐỘNG (AUTOMATED TEST SUITE)

Dự án sở hữu một bộ kiểm thử tự động toàn diện được viết bằng **PHPUnit**:

### Các số liệu ấn tượng:
- **Tổng số ca kiểm thử:** **180 tests** bao phủ 100% các kịch bản người dùng.
- **Tổng số khẳng định kiểm tra:** **761 assertions**.
- **Tỉ lệ thành công:** **100% Passed**.
- **Thời gian chạy:** Chỉ xấp xỉ **5 - 6 giây** nhờ cơ chế SQLite In-Memory Database (`:memory:`).

### Danh sách các bộ test chính (`tests/Feature`):
1. `AuthenticationTest`: Kiểm tra đăng ký, đăng nhập, xác thực email, quên mật khẩu, phân quyền.
2. `UserManagementTest`: Kiểm tra Admin quản lý tài khoản, thăng hạng quyền, khóa tài khoản, gửi link reset mật khẩu.
3. `PostWorkflowTest`: Kiểm tra quy trình sáng tạo bài viết, lưu nháp, nộp duyệt, rút bài, duyệt bài, từ chối bài.
4. `PostRequestWorkflowTest`: Kiểm tra quy trình tác giả gửi yêu cầu gỡ/sửa bài viết đã duyệt tới Admin.
5. `AuthorApplicationWorkflowTest`: Kiểm tra quy trình độc giả nộp đơn ứng tuyển làm tác giả và Admin xét duyệt.
6. `CommentThreadTest`: Kiểm tra bình luận lồng nhau đa cấp, báo cáo bình luận xấu.
7. `FavoriteManagementTest`: Kiểm tra chức năng lưu và quản lý bài viết yêu thích.
8. `UserActivityAndReadingHistoryTest`: Kiểm tra ghi nhận lịch sử đọc bài và nhật ký kiểm toán.
9. `PublicNewsTest`: Kiểm tra hiển thị trang chủ, đọc chi tiết, công cụ đọc báo, sitemap, RSS feed.

### Cách chạy kiểm thử:
- **Chạy toàn bộ bộ test:**
  ```bash
  php artisan test --compact
  ```
- **Chạy riêng một nhóm test cụ thể:**
  ```bash
  php artisan test tests/Feature/Admin/UserManagementTest.php --compact
  ```

---

## 🎨 4. QUY CHUẨN ĐỊNH DẠNG MÃ NGUỒN (CODE FORMATTER)

Dự án sử dụng công cụ **Laravel Pint** để tự động kiểm tra và chuẩn hóa cú pháp theo tiêu chuẩn PSR-12 của cộng đồng PHP quốc tế.  
Để định dạng lại toàn bộ code:
```bash
vendor/bin/pint --format agent
```
*(Hiện tại 100% file PHP trong dự án đều đạt trạng thái chuẩn chỉ không có lỗi formatting).*
