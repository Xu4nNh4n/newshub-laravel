# TÀI LIỆU CHI TIẾT DỰ ÁN TÒA SOẠN TIN TỨC SỐ (NEWSHUB)
> **Dành cho:** Thành viên nhóm phát triển, Bạn cùng làm đồ án, Giảng viên chấm đề tài.  
> **Cập nhật lần cuối:** 2026-10-04  
> **Phiên bản:** 1.0 - Production Ready

---

## 🎯 MỤC ĐÍCH CỦA BỘ TÀI LIỆU NÀY
Bộ tài liệu này được biên soạn nhằm giúp bạn nắm bắt nhanh chóng và hiểu sâu toàn bộ cấu trúc dự án **NewsHub** từ tổng quan nghiệp vụ, kiến trúc phần mềm, cấu trúc thư mục source code, thiết kế cơ sở dữ liệu, ngôn ngữ thiết kế giao diện cho đến quy trình cài đặt và chạy thử nghiệm.

---

## 📚 MỤC LỤC CHI TIẾT CÁC TÀI LIỆU

Vui lòng đọc theo thứ tự khuyến nghị dưới đây để có cái nhìn liền mạch nhất:

1. 📄 **[01. Tổng quan dự án & Công nghệ sử dụng](./01_TONG_QUAN_DU_AN.md)**
   - Bối cảnh đề tài & Bài toán giải quyết.
   - Các nhóm người dùng trong hệ thống (RBAC).
   - Công nghệ cốt lõi: PHP 8.4+, Laravel 11/12, Tailwind CSS v4, Blade, SQLite/MySQL.

2. 📄 **[02. Kiến trúc phần mềm & Cấu trúc thư mục](./02_KIEN_TRUC_VA_CAU_TRUC_THU_MUC.md)**
   - Mô hình MVC mở rộng (Service Layer, Form Requests, Policies, View Composers).
   - Bản đồ chi tiết toàn bộ các thư mục trong dự án và ý nghĩa từng thư mục.
   - Vòng đời xử lý một yêu cầu (Request Lifecycle).

3. 📄 **[03. Cơ sở dữ liệu & Mô hình quan hệ (Models & ERD)](./03_DATABASE_VA_MODELS.md)**
   - Danh sách 14 bảng dữ liệu và ý nghĩa nghiệp vụ.
   - Mối quan hệ giữa các Models trong Eloquent (1-nhiều, nhiều-nhiều, lồng nhau, morph).
   - Đánh chỉ mục hiệu năng (Indexing) & Toàn vẹn dữ liệu (Foreign keys, On Delete Cascade/Set Null).

4. 📄 **[04. Chi tiết các tính năng theo phân quyền người dùng](./04_CAC_TINH_NANG_THEO_PHAN_QUYEN.md)**
   - **Public:** Đọc báo, tìm kiếm, lọc danh mục đa cấp, nghe đọc bài TTS, công cụ đọc báo, chia sẻ MXH, RSS Feed.
   - **Độc giả (User):** Lưu bài yêu thích, lịch sử đọc, bình luận lồng nhau, ứng tuyển tác giả, nhận thông báo quả chuông.
   - **Tác giả (Author):** Quản lý bài viết, soạn thảo bài, nộp bài kiểm duyệt, gửi yêu cầu gỡ/sửa bài.
   - **Quản trị viên (Admin):** Duyệt bài, duyệt đơn CTV, xử lý khiếu nại/yêu cầu, quản lý chuyên mục/thẻ, quản lý tài khoản, gửi link reset mật khẩu Zero-Knowledge, xuất báo cáo CSV, nhật ký kiểm toán.

5. 📄 **[05. Ngôn ngữ thiết kế Editorial & Giao diện Frontend](./05_DESIGN_SYSTEM_VA_FRONTEND.md)**
   - Định hướng thẩm mỹ OpenJev / Neo-Brutalist thanh lịch (Tông giấy ấm, viền đen sắc nét, đổ bóng thô, màu nhấn Lime).
   - Hệ thống Design Tokens (Màu sắc, Typography, Spacing, Buttons, Forms, Modals).
   - Cấu trúc Blade Templates và tính nhất quán trải nghiệm (100% views đồng bộ).

6. 📄 **[06. Cơ chế bảo mật & Hệ thống kiểm thử tự động](./06_BAO_MAT_VA_CHAT_LUONG_CODE.md)**
   - Bảo mật: CSRF, XSS, SQL Injection, Zero-Knowledge Reset Password, Rate Limiting, Policies.
   - Kiểm toán hành động tòa soạn (`activity_logs`).
   - Bộ 180 Feature & Unit Tests tự động đạt tỉ lệ hoàn hảo 100%.

7. 📄 **[07. Hướng dẫn cài đặt & Khởi chạy dự án](./07_HUONG_DAN_CAI_DAT_VA_CHAY_PROJECT.md)**
   - Yêu cầu môi trường máy tính.
   - Các bước cài đặt chi tiết từng câu lệnh từ khi clone về.
   - Hướng dẫn chạy nhanh với SQLite (Zero-config) hoặc chuyển sang MySQL (XAMPP/Laragon).
   - Danh sách tài khoản đăng nhập mẫu sẵn có.

---

> 💡 **Mẹo:** Nếu cần chạy demo đồ án ngay lập tức trong 2 phút, bạn hãy mở thẳng file **[07_HUONG_DAN_CAI_DAT_VA_CHAY_PROJECT.md](./07_HUONG_DAN_CAI_DAT_VA_CHAY_PROJECT.md)** để xem lệnh khởi chạy!
