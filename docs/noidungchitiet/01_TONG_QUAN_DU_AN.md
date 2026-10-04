# 01. TỔNG QUAN DỰ ÁN & CÔNG NGHỆ SỬ DỤNG (NEWSHUB)

---

## 📰 1. GIỚI THIỆU DỰ ÁN
**NewsHub** là một nền tảng tòa soạn báo điện tử và mạng xuất bản nội dung số hiện đại, được thiết kế theo quy trình biên tập khép kín của một cơ quan báo chí thực thụ. Dự án kết hợp giữa **trải nghiệm đọc tin tức tinh tế (Editorial Reading Experience)** dành cho công chúng và **hệ thống quản trị nội dung tòa soạn mạnh mẽ (Editorial Management & Workflow)** dành cho phóng viên và ban biên tập.

### Điểm nhấn khác biệt của hệ thống:
1. **Quy trình xuất bản chuẩn tòa soạn:** Tác giả không thể tự ý đăng bài ra trang chủ mà phải trải qua quy trình nộp duyệt, ban biên tập kiểm tra nội dung, duyệt hoặc từ chối kèm lý do phản hồi rõ ràng.
2. **Quy trình thu hồi & sửa đổi bài viết:** Khi bài đã xuất bản, tác giả muốn gỡ hoặc cập nhật phải gửi yêu cầu chính thức kèm mức độ ưu tiên và lý do để ban biên tập phê duyệt.
3. **Cơ chế tuyển dụng tác giả (Author Application):** Độc giả thông thường có thể nộp đơn xin gia nhập đội ngũ tác giả kèm bài viết mẫu để ban biên tập xét duyệt trực tiếp trên hệ thống.
4. **Bộ công cụ đọc báo hiện đại:** Hỗ trợ tính năng đọc bài bằng giọng nói tự nhiên (Text-to-Speech), tăng/giảm cỡ chữ trực tiếp, chế độ in ấn sạch (Print CSS), chia sẻ mạng xã hội nhanh và cấu trúc dữ liệu chuẩn SEO Google Tin tức (JSON-LD `NewsArticle`).
5. **Ngôn ngữ thiết kế Editorial / Neo-Brutalist cao cấp:** Được lấy cảm hứng từ các tạp chí báo chí hiện đại (nền giấy ấm, viền mực đậm nét, tương phản cao, đổ bóng thô cá tính).

---

## 👥 2. CÁC NHÓM NGƯỜI DÙNG & PHÂN QUYỀN (RBAC)

Hệ thống phân chia chặt chẽ thành 4 vai trò (Roles) với ranh giới trách nhiệm rõ ràng:

```
[Khách vãng lai] ──(Đăng ký/Xác thực Email)──> [Độc giả (User)]
                                                     │
                                             (Ứng tuyển Tác giả)
                                                     │
                                                     ▼
[Ban biên tập (Admin)] ◄──(Xét duyệt)─── [Tác giả (Author)]
```

| Vai trò | Mô tả | Quyền hạn chính |
| :--- | :--- | :--- |
| **Khách vãng lai (Guest)** | Người dùng chưa đăng nhập | • Xem trang chủ, duyệt bài theo chuyên mục / thẻ.<br>• Tìm kiếm tin tức toàn văn.<br>• Nghe đọc bài bằng giọng nói, tăng/giảm cỡ chữ, in bài.<br>• Xem nguồn cấp dữ liệu RSS và sitemap.<br>• Đăng ký tài khoản và nhận email kích hoạt. |
| **Độc giả (Reader/User)** | Đã đăng nhập & xác thực email | • Bao gồm toàn bộ quyền của Khách.<br>• Bình luận bài viết và trả lời bình luận (lồng nhau đa cấp).<br>• Báo cáo bình luận vi phạm tiêu chuẩn cộng đồng.<br>• Lưu bài viết vào danh sách Yêu thích cá nhân.<br>• Xem lịch sử đọc bài gần đây.<br>• Nộp đơn ứng tuyển trở thành Tác giả của tòa soạn.<br>• Nhận thông báo thời gian thực (khi có người trả lời bình luận, kết quả duyệt đơn). |
| **Tác giả / Phóng viên (Author)** | Thành viên ban nội dung | • Bao gồm toàn bộ quyền của Độc giả.<br>• Dashboard cá nhân: thống kê số bài viết, lượt xem, trạng thái.<br>• Soạn thảo bài viết mới (kèm ảnh đại diện, SEO meta, định dạng nội dung phong phú).<br>• Nộp bài lên tòa soạn chờ duyệt hoặc rút bài về sửa lại.<br>• Gửi yêu cầu gỡ bài hoặc cập nhật bài viết đã xuất bản tới Admin. |
| **Ban biên tập (Admin)** | Quản trị viên cấp cao tòa soạn | • Kiểm soát toàn bộ hệ thống qua Dashboard phân tích.<br>• Duyệt / Từ chối bài viết của các tác giả.<br>• Duyệt / Từ chối đơn ứng tuyển tác giả mới.<br>• Xử lý các yêu cầu gỡ/sửa bài viết.<br>• Quản lý tài khoản: phân quyền, khóa tài khoản, gửi link đặt lại mật khẩu bảo mật.<br>• Xử lý báo cáo bình luận xấu.<br>• Quản trị danh mục đa cấp (cha - con) và Thẻ tag.<br>• Xuất báo cáo dữ liệu dạng file CSV.<br>• Kiểm tra nhật ký kiểm toán hành động toàn hệ thống (Audit Logs). |

---

## 🛠️ 3. TECH STACK CHI TIẾT

Hệ thống được xây dựng trên nền tảng công nghệ PHP hiện đại nhất, tuân thủ nghiêm ngặt chuẩn thiết kế của Laravel Framework:

### Backend:
- **Ngôn ngữ:** `PHP 8.4+ / 8.5` (áp dụng Constructor Promotion, Typed Properties, First-class Callables, Attributes).
- **Framework:** `Laravel 11 / 12` (sử dụng cấu trúc ứng dụng hiện đại: `bootstrap/app.php`, không còn `app/Http/Kernel.php` cồng kềnh).
- **Cơ sở dữ liệu:**
  - Môi trường phát triển mặc định: `SQLite` (nhúng thẳng file trong code, zero-configuration).
  - Môi trường mở rộng / báo cáo: `MySQL 8.x` / `MariaDB` (đã có sẵn file dump DDL + Seed tại `database/sql/apptintuc_mysql.sql`).
- **Xác thực & Bảo mật:** Laravel Fortify/Breeze authentication, Policy-based authorization, Hashed Password (Bcrypt cost 12), Signed Email Verification URL, CSRF Tokens, Throttle Middleware.

### Frontend:
- **Template Engine:** `Blade Template Engine` (kết hợp View Composers, Blade Components tái sử dụng cao).
- **CSS Framework:** `Tailwind CSS v4` (tận dụng CSS Variables, Design Tokens, cấu hình theme linh hoạt, không phụ thuộc file `tailwind.config.js` truyền thống).
- **JavaScript:** `Alpine.js` cho các tương tác micro-UI (dropdowns, mobile navigation drawer, tab switching, confirmation dialogs, toast notifications, Web Speech API).
- **Bundler:** `Vite` siêu tốc cho quá trình build asset (`npm run dev`, `npm run build`).

### Kiểm thử & Đảm bảo chất lượng (QA):
- **Test Runner:** `PHPUnit 11` (Bộ kiểm thử 180 Feature & Unit tests đạt tỉ lệ thành công 100%).
- **Code Formatter:** `Laravel Pint` (đảm bảo chuẩn PSR-12 và Laravel code style).
