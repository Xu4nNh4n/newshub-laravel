# 02. KIẾN TRÚC PHẦN MỀM & CẤU TRÚC THƯ MỤC

---

## 🏛️ 1. MÔ HÌNH KIẾN TRÚC (EXPANDED MVC)

Dự án áp dụng mô hình **MVC mở rộng (Expanded Model-View-Controller)** với nguyên tắc **"Slim Controller, Fat Service"** (Controller mỏng chỉ điều phối, Service gom trọn vẹn nghiệp vụ phức tạp):

```
┌─────────────────────────────────────────────────────────────┐
│                       HTTP REQUEST                          │
└──────────────────────────────┬──────────────────────────────┘
                               │
                               ▼
                    [ 1. ROUTING & MIDDLEWARE ]
           (Xác thực Auth, Quyền Role, Throttle Rate Limiting)
                               │
                               ▼
                    [ 2. FORM REQUEST VALIDATION ]
      (Lọc dữ liệu, kiểm tra tính hợp lệ, trả lỗi 422 nếu sai)
                               │
                               ▼
                     [ 3. CONTROLLER ]
     (Ủy quyền qua Policy -> Gọi Service -> Trả về View/Redirect)
          │                                           │
          ├──────────────────┐                        │
          ▼                  ▼                        │
    [ 4. POLICY ]    [ 5. SERVICE LAYER ]             │
  (Kiểm tra quyền    (Xử lý Logic nghiệp vụ:          │
  truy cập model)     PostWorkflow, CommentService,   │
                      UserAccessService, etc.)        │
                             │                        │
                             ▼                        │
                       [ 6. ELOQUENT MODEL ]          │
                     (Tương tác với Database)         │
                             │                        │
                             ▼                        ▼
                       [ 7. BLADE VIEW ] ◄────────────┘
                 (Hiển thị giao diện người dùng)
```

### Các lớp thành phần cốt lõi:
1. **Form Requests (`app/Http/Requests`):** Tách toàn bộ logic validate ra khỏi Controller. Ví dụ: `StorePostRequest`, `UpdateUserAccessRequest`, `SubmitAuthorApplicationRequest`.
2. **Service Layer (`app/Services`):** Đóng gói quy trình nghiệp vụ (Business Workflow) thành các class độc lập có thể tái sử dụng và kiểm thử đơn vị dễ dàng. Ví dụ: `PostWorkflowService`, `AuthorApplicationService`, `UserAccessService`, `CommentModerationService`.
3. **Policies (`app/Policies`):** Phân quyền chi tiết cho từng hành động trên từng bản ghi model cụ thể. Ví dụ: `PostPolicy` (tác giả chỉ được sửa bài của mình, không được sửa bài khi đã nộp duyệt), `UserPolicy` (Admin không thể tự khóa mình).
4. **View Composers (`app/ViewComposers`):** Tự động cung cấp các dữ liệu dùng chung (danh mục hiển thị trên thanh menu, số lượng bài chờ duyệt cho thanh sidebar) cho các view layout mà không cần Controller nào phải truy vấn lặp đi lặp lại.
5. **Activity Log Auditing:** Mọi thao tác biên tập quan trọng đều được ghi nhận tự động vào bảng `activity_logs`.

---

## 📂 2. BẢN ĐỒ CÂU TRÚC THƯ MỤC SOURCE CODE

Dưới đây là sơ đồ chi tiết các thư mục và tệp tin quan trọng trong dự án:

```
PhpProject/
├── app/                                # Mã nguồn PHP cốt lõi của ứng dụng
│   ├── Enums/                          # Định nghĩa các trạng thái bằng PHP Enum chuẩn
│   │   ├── AuthorApplicationStatus.php # Trạng thái đơn CTV: Pending, Approved, Rejected
│   │   ├── CommentReportStatus.php     # Trạng thái báo cáo bình luận: Pending, Resolved, Dismissed
│   │   ├── CommentStatus.php           # Trạng thái bình luận: Visible, Hidden
│   │   ├── PostRequestPriority.php     # Mức ưu tiên yêu cầu bài: Normal, Urgent
│   │   ├── PostRequestStatus.php       # Trạng thái yêu cầu bài: Pending, Approved, Rejected
│   │   ├── PostRequestType.php         # Loại yêu cầu bài: Takedown (gỡ bài), Update (sửa bài)
│   │   ├── PostStatus.php              # Trạng thái bài viết: Draft, Pending, Published, Hidden, Archived
│   │   ├── UserRole.php                # Phân quyền: User, Author, Admin
│   │   └── UserStatus.php              # Trạng thái tài khoản: Active, Blocked
│   ├── Http/
│   │   ├── Controllers/                # Bộ điều khiển điều phối yêu cầu HTTP
│   │   │   ├── Admin/                  # Controller nghiệp vụ quản trị viên
│   │   │   │   ├── ActivityLogController.php
│   │   │   │   ├── AuthorApplicationController.php
│   │   │   │   ├── CategoryController.php
│   │   │   │   ├── CommentController.php
│   │   │   │   ├── CommentReportController.php
│   │   │   │   ├── PostController.php
│   │   │   │   ├── PostFeaturedController.php
│   │   │   │   ├── PostRequestController.php
│   │   │   │   ├── PostReviewController.php
│   │   │   │   ├── TagController.php
│   │   │   │   └── UserController.php
│   │   │   ├── Auth/                   # Đăng nhập, đăng ký, xác thực email, đổi mật khẩu
│   │   │   ├── Author/                 # Controller của tác giả (viết bài, media upload, workflow)
│   │   │   ├── HomeController.php      # Trang chủ tin tức
│   │   │   ├── NewsController.php      # Xem chi tiết bài viết, tìm kiếm, lọc danh mục/thẻ
│   │   │   └── ...                     # Các controller khác: Comment, Favorite, History, etc.
│   │   ├── Middleware/                 # Các bộ lọc trung gian kiểm tra yêu cầu
│   │   │   ├── EnsureUserHasRole.php   # Kiểm tra vai trò: admin, author, user
│   │   │   └── EnsureUserIsActive.php  # Chặn người dùng có trạng thái 'blocked'
│   │   └── Requests/                   # Các lớp Form Request kiểm tra dữ liệu đầu vào
│   ├── Models/                         # Các lớp Eloquent ORM ánh xạ bảng Database
│   │   ├── ActivityLog.php
│   │   ├── AuthorApplication.php
│   │   ├── Category.php
│   │   ├── Comment.php
│   │   ├── CommentReport.php
│   │   ├── Favorite.php
│   │   ├── Post.php
│   │   ├── PostRequest.php
│   │   ├── PostView.php
│   │   ├── Tag.php
│   │   └── User.php
│   ├── Notifications/                  # Thông báo qua Email và Thông báo trong ứng dụng (In-app)
│   ├── Policies/                       # Chính sách phân quyền tài nguyên (Gate & Policy)
│   ├── Providers/                      # Service Providers đăng ký dịch vụ của hệ thống
│   ├── Services/                       # Tầng xử lý nghiệp vụ độc lập (Service Layer)
│   └── ViewComposers/                  # Cung cấp dữ liệu tự động cho Views (Menu, Badges)
│
├── bootstrap/                          # Khởi động ứng dụng (app.php theo chuẩn Laravel 11/12)
├── config/                             # Cấu hình hệ thống (app, auth, database, mail, etc.)
│
├── database/                           # Cơ sở dữ liệu
│   ├── database.sqlite                 # File SQLite chạy độc lập local
│   ├── factories/                      # Sinh dữ liệu giả lập cho Testing
│   ├── migrations/                     # Các file định nghĩa cấu trúc bảng Database
│   ├── seeders/                        # Dữ liệu khởi tạo mẫu (DemoContentSeeder)
│   └── sql/                            # File SQL chuẩn MySQL (apptintuc_mysql.sql)
│
├── public/                             # Thư mục gốc Web Server (chứa index.php, assets compiled)
├── resources/                          # Tài nguyên giao diện người dùng
│   ├── css/                            # File CSS chứa Design Tokens và cấu hình Tailwind v4
│   ├── js/                             # Mã JavaScript bổ trợ (Alpine components, TTS audio)
│   └── views/                          # Toàn bộ Blade Views theo phong cách Editorial OpenJev
│       ├── admin/                      # Giao diện dành riêng cho Ban biên tập
│       ├── author/                     # Giao diện dành riêng cho Tác giả
│       ├── auth/                       # Giao diện Đăng nhập, Đăng ký, Quên mật khẩu
│       ├── errors/                     # Giao diện lỗi chuyên nghiệp: 403, 404, 500
│       ├── layouts/                    # Các khung layout chung: app.blade.php, dashboard.blade.php
│       ├── mail/                       # Template Email gửi người dùng (chuẩn responsive đẹp mắt)
│       ├── news/                       # Trang đọc bài viết chi tiết, danh mục, tìm kiếm
│       ├── partials/                   # Các phần giao diện dùng chung (header, footer, meta, etc.)
│       └── user/                       # Trang cá nhân của độc giả (yêu thích, lịch sử, đổi mật khẩu)
│
├── routes/                             # Định nghĩa toàn bộ đường dẫn URL của website
│   ├── web.php                         # Khai báo routes người dùng, tác giả, admin
│   └── console.php                     # Các câu lệnh Artisan tùy chỉnh
│
├── storage/                            # Nơi lưu file upload (ảnh bài viết, avatar), logs, cache
└── tests/                              # Bộ kiểm thử tự động toàn diện
    ├── Feature/                        # Các kịch bản kiểm thử luồng người dùng (180 tests)
    └── TestCase.php                    # Cấu hình môi trường Test runner
```
