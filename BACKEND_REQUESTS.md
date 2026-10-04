# Yêu cầu bổ sung dữ liệu & tính năng Backend (Codex Backlog)

> **TRẠNG THÁI: MỤC 1 &rarr; MỤC 11 ĐÃ ĐƯỢC CODEX HOÀN TẤT & FRONTEND TÍCH HỢP TOÀN DIỆN.**
> 
> **KẾ HOẠCH BÀN GIAO TIẾP THEO CHO CODEX:**
> - **Mục 12**: Cho phép Tác giả tự do tạo Thẻ bài viết mới khi soạn thảo (Dynamic Tags / Tag Creation on the fly).
> - **Mục 13**: Hỗ trợ Trình soạn thảo Trực quan (WYSIWYG), API Upload Ảnh trong bài (`POST /author/media/upload`), Nhúng Video & Tùy chọn Ẩn/Hiện Thumbnail (`show_thumbnail_in_post`).
> - **Mục 14**: Chuẩn hóa Dữ liệu Cây Chuyên mục từ Controller (`CategoryController@index`) & Hỗ trợ JSON Response cho Modal CRUD (Chuyên mục & Thẻ).
> - **Mục 15**: Quy trình Yêu cầu Gỡ bài / Đính chính Bài viết Đã Xuất bản & Hủy Gửi Duyệt về Nháp (Post Take-down & Correction Request Workflow).
>
> Tài liệu này được lập bởi Antigravity (phụ trách UI/UX) để bàn giao cho Codex (phụ trách Backend).
> Antigravity tuân thủ nguyên tắc không can thiệp Controller, Model, Query, Database, Service hay Business Logic. Dưới đây là các điểm giao tiếp dữ liệu giữa Backend và Frontend.

---

## 1. View Composer cung cấp số lượng Badge thông báo trên Sidebar

- **UI cần hỗ trợ**: Sidebar khung quản trị (`resources/views/layouts/dashboard.blade.php`).
- **Hiện trạng & Thiếu hụt**:
  - Hiện tại, biến `$adminStats` chứa `pending_posts` (số bài chờ duyệt) và `pending_reports` (số báo cáo bình luận chờ xử lý) chỉ được truyền duy nhất từ `DashboardController@index`.
  - Khi Admin chuyển sang các trang con (như `admin.posts.index`, `admin.categories.index`, `admin.users.index`, `admin.comments.index`, v.v.), các controller đó không truyền thống kê này. Do đó, badge số lượng thông báo trên Sidebar bị biến mất khi rời khỏi trang Dashboard.
  - Frontend không được phép tự ý gọi `Post::where(...)` hay `CommentReport::where(...)` trực tiếp trong file Blade layout.
- **Đề xuất xử lý Backend**:
  - Tạo một View Composer (ví dụ `app/Http/ViewComposers/DashboardSidebarComposer.php` hoặc đăng ký trong `AppServiceProvider`) áp dụng cho view `layouts.dashboard`.
  - Cung cấp sẵn 2 biến hoặc một mảng chỉ số toàn cục cho sidebar khi người dùng là Admin:
    - `pending_posts_count`: Số bài viết có trạng thái `pending_review`.
    - `pending_reports_count`: Số báo cáo bình luận có trạng thái `pending`.
- **Dạng dữ liệu Frontend mong muốn nhận**:
  ```php
  [
      'pending_posts_count' => int, // e.g. 3
      'pending_reports_count' => int, // e.g. 1
  ]
  ```
- **File Frontend phụ thuộc**: [`resources/views/layouts/dashboard.blade.php`](file:///D:/Code/code/PhpProject/resources/views/layouts/dashboard.blade.php).
- **Kết quả mong muốn**: Ở bất kỳ trang quản trị nào, Admin vẫn nhìn thấy số bài viết và báo cáo đang chờ xử lý ngay trên Sidebar theo thời gian thực.

---

## 2. Dữ liệu chỉ số hoạt động trên trang Hồ sơ cá nhân (`profile.edit`)

- **UI cần hỗ trợ**: Thẻ nhận diện hồ sơ cá nhân (`resources/views/profile/edit.blade.php`).
- **Hiện trạng & Thiếu hụt**:
  - `ProfileController@edit` hiện chỉ truyền duy nhất biến `'avatarUrl'`.
  - Giao diện hồ sơ mới cần hiển thị các chỉ số tóm tắt hoạt động tài khoản: tổng số bình luận đã đăng, tổng số bài viết đã lưu, và tổng số bài viết đã xuất bản (đối với Author/Admin).
  - Hiện tại frontend chỉ hiển thị link điều hướng tĩnh để tránh query database trong template.
- **Đề xuất xử lý Backend**:
  - Trong `app/Http/Controllers/ProfileController.php@edit`, thực hiện tính toán hoặc đếm quan hệ (relation count) từ `$user`:
    ```php
    public function edit(Request $request): View
    {
        $user = $request->user();

        return view('profile.edit', [
            'avatarUrl' => $user->avatar === null
                ? null
                : Storage::disk('public')->url($user->avatar),
            'stats' => [
                'comments_count' => $user->comments()->count(),
                'favorites_count' => $user->favorites()->count(),
                'posts_count' => $user->posts()->count(),
            ],
        ]);
    }
    ```
- **Dạng dữ liệu Frontend mong muốn nhận**: Mảng số nguyên `$stats` gồm các keys:
  ```php
  array{
      comments_count: int,
      favorites_count: int,
      posts_count: int,
  }
  ```
- **File Frontend phụ thuộc**: [`resources/views/profile/edit.blade.php`](file:///D:/Code/code/PhpProject/resources/views/profile/edit.blade.php).
- **Kết quả mong muốn**: Người dùng thấy ngay tổng số bình luận và bài viết đã lưu của mình ngay cạnh ảnh đại diện mà không tốn truy vấn dư thừa.

---

## 3. Tính năng Xóa ảnh đại diện (Gỡ bỏ Avatar, quay về chữ cái mặc định)

- **UI cần hỗ trợ**: Biểu mẫu cập nhật hồ sơ (`resources/views/profile/edit.blade.php`).
- **Hiện trạng & Thiếu hụt**:
  - `UpdateProfileRequest` và `UserAvatarService@update` hiện chỉ hỗ trợ nhận file ảnh mới để ghi đè.
  - Chưa có cơ chế cho phép người dùng xóa ảnh đại diện hiện tại nếu họ muốn quay lại avatar chữ cái đầu mặc định (trong khi ở phần bài viết tác giả `author.posts.form` đã có `remove_thumbnail`).
- **Đề xuất xử lý Backend**:
  - Trong `app/Http/Requests/Profile/UpdateProfileRequest.php`: Cho phép thêm field `remove_avatar => ['nullable', 'boolean']`.
  - Trong `app/Services/UserAvatarService.php@update`: Bổ sung tham số hoặc xử lý nếu `remove_avatar` là `true`:
    ```php
    if ($removeAvatar && $oldAvatarPath !== null) {
        Storage::disk('public')->delete($oldAvatarPath);
        $user->update(['avatar' => null]);
    }
    ```
- **Dạng dữ liệu Frontend gửi lên**: Form input checkbox hoặc boolean: `remove_avatar: '1'`.
- **File Frontend phụ thuộc**: [`resources/views/profile/edit.blade.php`](file:///D:/Code/code/PhpProject/resources/views/profile/edit.blade.php).
- **Kết quả mong muốn**: Người dùng có thể bấm "Gỡ bỏ avatar hiện tại" và quay về trạng thái mặc định an toàn.

---

## 4. Hỗ trợ Cập nhật Địa chỉ Email (Kèm xác thực lại)

- **UI cần hỗ trợ**: Form thông tin cá nhân (`resources/views/profile/edit.blade.php`).
- **Hiện trạng & Thiếu hụt**:
  - Ô địa chỉ email hiện tại đang ở trạng thái `disabled` (chỉ xem) do `UpdateProfileRequest` chỉ validate và update trường `name`.
  - Người dùng không có cách nào thay đổi email khi cần thiết.
- **Đề xuất xử lý Backend**:
  - Mở rộng `UpdateProfileRequest` cho phép trường `email` (unique với users khác ngoại trừ user hiện tại).
  - Nếu email thay đổi:
    1. Cập nhật email mới.
    2. Đặt `email_verified_at = null`.
    3. Gửi thông báo/email xác minh lại theo chuẩn Laravel `MustVerifyEmail`.
- **Dạng dữ liệu Frontend gửi lên**: `email: string` (email hợp lệ).
- **File Frontend phụ thuộc**: [`resources/views/profile/edit.blade.php`](file:///D:/Code/code/PhpProject/resources/views/profile/edit.blade.php).
- **Kết quả mong muốn**: Cho phép người dùng cập nhật email an toàn, đúng quy trình xác thực.

---

## 5. Cấu trúc Breadcrumb đa tầng cho Topbar Admin Shell

- **UI cần hỗ trợ**: Thanh Topbar của khung quản trị (`resources/views/layouts/dashboard.blade.php`).
- **Hiện trạng & Thiếu hụt**:
  - Hiện tại layout chỉ nhận một chuỗi `$title` đơn lẻ từ view con (ví dụ: `Workspace / Quản lý bài viết`, `Workspace / Thêm chuyên mục`).
  - Các trang con cấp sâu (như tạo mới/chỉnh sửa danh mục, duyệt từng bài viết cụ thể) chưa có đường dẫn phân cấp có thể bấm ngược lại trang danh sách cha.
- **Đề xuất xử lý Backend**:
  - Cho phép Controller truyền thêm mảng `$breadcrumbs` tùy chọn:
    ```php
    return view('admin.categories.form', [
        'breadcrumbs' => [
            ['label' => 'Danh mục', 'url' => route('admin.categories.index')],
            ['label' => isset($category) ? 'Sửa chuyên mục' : 'Thêm mới', 'url' => null],
        ],
        ...
    ]);
    ```
- **Dạng dữ liệu Frontend mong muốn nhận**:
  ```php
  array<array{
      label: string,
      url: ?string,
  }> $breadcrumbs
  ```
- **File Frontend phụ thuộc**: [`resources/views/layouts/dashboard.blade.php`](file:///D:/Code/code/PhpProject/resources/views/layouts/dashboard.blade.php).
- **Kết quả mong muốn**: Topbar hiển thị cây điều hướng breadcrumb chuẩn xác, bấm được để quay lại mục cha thay vì phải tìm lại trên Sidebar.

---

## 6. Cho phép Quản trị viên (Admin) Xuất bản bài viết trực tiếp (Bỏ qua hàng đợi duyệt)

- **UI cần hỗ trợ**:
  1. Màn hình tạo và chỉnh sửa bài viết: [`resources/views/author/posts/form.blade.php`](file:///D:/Code/code/PhpProject/resources/views/author/posts/form.blade.php).
  2. Bảng danh sách bài viết của tôi: [`resources/views/author/posts/index.blade.php`](file:///D:/Code/code/PhpProject/resources/views/author/posts/index.blade.php).
- **Hiện trạng & Bất cập**:
  - Hiện tại, bất kể tác giả là `Author` hay `Admin`, quy trình đều bắt buộc:
    1. Tạo bài với trạng thái `Draft` (`author.posts.store`).
    2. Bấm nút "Gửi duyệt" (`author.posts.submit`), chuyển trạng thái sang `PendingReview`.
    3. Phải chuyển sang trang "Duyệt bài" (`admin.post-reviews.index`) để Admin bấm "Duyệt" (`admin.post-reviews.approve`) thì bài viết mới chuyển thành `Published`.
  - Với tài khoản Admin (Quản trị viên / Tổng biên tập), việc phải tự gửi duyệt bài viết của chính mình rồi tự đi duyệt lại là một thao tác thừa, rườm rà và không thực tế trong các hệ thống xuất bản nội dung (CMS/News).
  - Tác giả thông thường (`Author`) vẫn cần bắt buộc qua quy trình duyệt bài để đảm bảo chất lượng nội dung.
- **Đề xuất xử lý Backend**:
  1. Trong `app/Http/Requests/Author/StorePostRequest.php` và `UpdatePostRequest.php`:
     - Bổ sung validation cho `action => ['nullable', 'string', 'in:draft,publish']`.
     - Bổ sung validation cho `published_at => ['nullable', 'date']`.
  2. Trong `app/Http/Controllers/Author/PostController.php`:
     - Khi `store()` hoặc `update()`:
       - Nếu request gửi `action === 'publish'` **VÀ** user hiện tại có role `Admin`:
         - Đặt trạng thái bài viết thẳng thành `PostStatus::Published`.
         - Đặt `published_at = $publishedAt ?? now()`.
         - Ghi `activity_logs` hành động `post.published`.
         - Chuyển hướng về `author.posts.index` kèm thông báo: `"Đã xuất bản bài viết thành công."`.
       - Nếu user là `Author` bình thường gửi `action === 'publish'`: Bỏ qua hoặc chặn (chỉ lưu nháp `Draft`), bắt buộc qua luồng `submit` duyệt bài như hiện tại.
       - Nếu gửi `action === 'draft'` (mặc định): Tiếp tục lưu ở trạng thái `PostStatus::Draft` để Admin có thể soạn dở và tiếp tục sửa sau.
  3. Thao tác nhanh trên bảng danh sách (`author.posts.index`):
     - Cho phép Admin bấm nút "Xuất bản ngay" trực tiếp trên từng bài viết đang ở trạng thái `Draft` của chính mình mà không cần đổi trạng thái sang `PendingReview`.
     - Có thể mở rộng endpoint `POST /author/posts/{post}/publish` (hoặc xử lý trong controller/policy phù hợp).
  4. Phân quyền `PostPolicy`:
     - Thêm hoặc cập nhật gate `publish(User $user, Post $post)`: chỉ cấp quyền khi `$user->role === UserRole::Admin`.
- **Dạng dữ liệu Frontend gửi lên**:
  - Từ form viết bài:
    ```html
    <!-- Khi user là Admin -->
    <button type="submit" name="action" value="publish" class="...">Xuất bản ngay</button>
    <button type="submit" name="action" value="draft" class="...">Lưu bản nháp</button>
    ```
  - Payload POST:
    ```php
    [
        'title' => string,
        'action' => 'publish' | 'draft',
        'published_at' => ?string, // ISO / datetime format
        ...
    ]
    ```
- **Kết quả mong muốn**:
  - Admin có thể trực tiếp xuất bản bài viết ngay khi viết xong mà không bị gián đoạn hay phải tự duyệt bài của mình.
  - Vẫn giữ nguyên luồng duyệt bài chặt chẽ đối với các Tác giả (`Author`) thông thường.

---

## 7. Quy trình Ứng tuyển & Phê duyệt Tác giả / Cộng tác viên (Author Applications)

- **UI cần hỗ trợ**:
  1. Giao diện người dùng (`User`):
     - Giao diện nộp đơn **"Ứng tuyển làm Tác giả / CTV"** và theo dõi trạng thái đơn (chờ duyệt / đã duyệt / bị từ chối kèm lý do).
  2. Giao diện quản trị (`Admin`):
     - Màn hình quản lý & duyệt đơn: [`resources/views/admin/author-applications/index.blade.php`](file:///D:/Code/code/PhpProject/resources/views/admin/author-applications/index.blade.php).
     - Badge số đơn CTV chờ duyệt trên Sidebar Admin: `$pending_applications_count`.
  3. Phân quyền hiển thị Sidebar & Bảo vệ khu vực Viết bài:
     - Nhóm chức năng "Nội dung" (Viết bài mới, Bài viết của tôi) trên [`resources/views/layouts/dashboard.blade.php`](file:///D:/Code/code/PhpProject/resources/views/layouts/dashboard.blade.php) chỉ hiển thị khi `auth()->user()->role` là `Author` hoặc `Admin`.
     - Với tài khoản `User` thông thường: Hiển thị banner/nút mời gọi **"Gia nhập đội ngũ Tác giả NewsHub"**.
- **Hiện trạng & Bất cập**:
  - Hiện tại trong `routes/web.php`, nhóm route `author.posts.*` chỉ yêu cầu middleware `verified`, bất kỳ tài khoản `User` nào sau khi xác minh email cũng có thể tự do vào viết bài và gửi duyệt.
  - Chưa có cơ chế tuyển chọn hoặc xét duyệt đầu vào cho Tác giả/Cộng tác viên (Author) theo đúng chuẩn tòa soạn báo chuyên nghiệp.
- **Đề xuất xử lý Backend**:
  1. **Migration & Model `AuthorApplication`**:
     - Bảng `author_applications`:
       - `id`
       - `user_id` (foreignId -> users, cascade on delete)
       - `category_id` (foreignId -> categories, cascade on delete: chuyên mục/lĩnh vực sở trường muốn viết)
       - `bio` (text: giới thiệu bản thân, kinh nghiệm viết báo / viết blog)
       - `sample_title` (string: tiêu đề bài viết mẫu)
       - `sample_content` (text: nội dung bài viết mẫu hoặc link bài đã từng xuất bản)
       - `status` (string/enum: `pending`, `approved`, `rejected`; default: `pending`)
       - `rejection_reason` (nullable string: phản hồi từ ban biên tập nếu từ chối)
       - `reviewed_by` (nullable foreignId -> users)
       - `reviewed_at` (nullable timestamp)
       - `timestamps`
     - Ràng buộc: Một user chỉ được có tối đa 1 đơn ở trạng thái `pending` cùng một thời điểm.
  2. **Route & Controller cho Độc giả (`User`)**:
     - `POST /author-applications` (`author-applications.store`):
       - Nhận `category_id`, `bio`, `sample_title`, `sample_content`.
       - Chỉ cho phép gửi khi `role === UserRole::User` và chưa có đơn `pending`.
  3. **Route & Controller cho Quản trị viên (`Admin`)**:
     - `GET /admin/author-applications` (`admin.author-applications.index`): Danh sách đơn ứng tuyển kèm bộ lọc trạng thái (`pending`, `approved`, `rejected`), eager load `user` và `category`.
     - `PATCH /admin/author-applications/{application}/approve` (`admin.author-applications.approve`):
       - Cập nhật application: `status = 'approved'`, `reviewed_by = admin.id`, `reviewed_at = now()`.
       - Nâng cấp vai trò user: `$application->user->update(['role' => UserRole::Author])`.
       - Ghi `activity_logs`: `author_application.approved`.
     - `PATCH /admin/author-applications/{application}/reject` (`admin.author-applications.reject`):
       - Nhận `reason` (bắt buộc).
       - Cập nhật application: `status = 'rejected'`, `rejection_reason = $reason`, `reviewed_by = admin.id`, `reviewed_at = now()`.
       - Ghi `activity_logs`: `author_application.rejected`.
  4. **Bảo vệ quyền truy cập khu vực viết bài (Middleware/Route)**:
     - Trong `routes/web.php`, cập nhật nhóm route `author.posts.*` để chỉ cho phép các tài khoản có `role:author,admin` (thay vì chỉ `verified`). Nếu `User` thường cố tình gõ URL sẽ nhận `403 Forbidden` (hoặc chuyển hướng kèm thông báo hướng dẫn đăng ký làm Tác giả).
  5. **Cập nhật Composer Badge Sidebar (`DashboardSidebarComposer`)**:
     - Cung cấp thêm biến `$pending_applications_count` cho view `layouts.dashboard` của Admin.
- **Kết quả mong muốn**:
  - Tạo thành quy trình xuất bản báo chí khép kín, chuẩn mực: **Độc giả &rarr; Nộp đơn CTV &rarr; Admin phê duyệt &rarr; Trở thành Tác giả &rarr; Viết bài &rarr; Admin duyệt bài &rarr; Xuất bản**.

---

## 8. Hỗ trợ Danh mục Đa cấp (Parent - Child Hierarchical Categories)

- **UI cần hỗ trợ**: Thanh điều hướng Navbar chính và Drawer Mobile (`resources/views/layouts/app.blade.php`), Breadcrumbs (`resources/views/news/index.blade.php`, `resources/views/news/show.blade.php`), Form quản lý Chuyên mục trong Admin (`resources/views/admin/categories/create.blade.php`, `edit.blade.php`).
- **Hiện trạng Frontend đã hoàn thành**:
  - Giao diện người dùng (Desktop & Mobile) đã được Antigravity thiết kế hoàn thiện 100% theo chuẩn Báo chí lớn (VnExpress, Dân Trí):
    - **Desktop**: Dropdown hiển thị danh mục con khi rê chuột vào danh mục cha (có indicator xoay 180°, badge bài viết, flyout card thanh lịch). Nút **"Tất cả chuyên mục ▦"** (Mega Menu) mở toàn bộ bản đồ danh mục theo lưới 2-3 cột.
    - **Mobile Drawer**: Tích hợp Accordion mượt mà, cho phép bấm nút `[▾]` để mở rộng/thu gọn các danh mục con mà không làm tràn màn hình.
    - **Hợp đồng kết nối thông minh**: Trong `resources/views/layouts/app.blade.php`, Antigravity đã cài đặt sẵn cơ chế kiểm tra `if (method_exists(\App\Models\Category::class, 'children'))`. Khi Backend cập nhật xong quan hệ Eloquent này, giao diện sẽ **tự động chuyển sang đọc 100% từ Database** mà không cần sửa lại bất kỳ dòng code UI nào!
- **Đề xuất xử lý Backend cho Codex**:
  1. **Migration**:
     - Thêm cột `parent_id` vào bảng `categories`:
       ```php
       $table->foreignId('parent_id')->nullable()->after('id')->constrained('categories')->nullOnDelete();
       ```
  2. **Model `Category`**:
     - Thêm quan hệ Eloquent:
       ```php
       public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
       {
           return $this->belongsTo(Category::class, 'parent_id');
       }

       public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
       {
           return $this->hasMany(Category::class, 'parent_id');
       }

       public function scopeParents($query)
       {
           return $query->whereNull('parent_id');
       }
       ```
     - Cập nhật `$fillable` để bao gồm `'parent_id'`.
  3. **Admin Category Controller & Form**:
     - Khi mở form tạo/sửa chuyên mục (`admin/categories/create`, `edit`), truyền danh sách các danh mục cha (`Category::parents()->where('id', '!=', $category->id ?? 0)->get()`).
     - Validation: `'parent_id' => ['nullable', 'exists:categories,id', 'not_in:'.($category->id ?? '')]`.
  4. **NewsController Query (Bao gồm bài viết của danh mục con khi lọc theo danh mục cha)**:
     - Trong `NewsController@index`, khi lọc theo `category`:
       - Nếu category đó là danh mục cha (có children), truy vấn lấy bài viết có `category_id` thuộc cha HOẶC bất kỳ con nào của nó:
         ```php
         $category = Category::where('slug', $slug)->with('children:id,parent_id')->first();
         $categoryIds = $category ? [$category->id, ...$category->children->pluck('id')] : [];
         $query->whereIn('category_id', $categoryIds);
         ```
  5. **Seeder Demo Content (`DemoContentSeeder`)**:
     - Gán `parent_id` của các chuyên mục con ("Trí tuệ nhân tạo", "Phần mềm", "An ninh mạng", "Phần cứng", "Điện toán đám mây", "Dữ liệu", "Chuyển đổi số") vào ID của chuyên mục cha "Công nghệ".
     - Gán `parent_id` của "Khởi nghiệp công nghệ" vào chuyên mục cha "Kinh doanh".

---

## 9. Nút Ghim / Tiêu điểm nhanh cho Admin (Quick Toggle Featured Post)

- **UI cần hỗ trợ**: Danh sách quản trị bài viết (`resources/views/admin/posts/index.blade.php`).
- **Hiện trạng & Bất cập**:
  - Bảng `posts` có cột `is_featured` boolean để đưa bài viết lên vị trí Top Hero Trang chủ.
  - Tuy nhiên, hiện tại Admin chỉ có thể chỉnh sửa `is_featured` bằng cách mở form chỉnh sửa bài viết của chính mình.
  - Trong danh sách bài viết Admin (`admin.posts.index`), Admin chưa có nút bật/tắt (Toggle) nhanh trạng thái Tiêu điểm cho một bài viết bất kỳ của tác giả khác để đưa lên trang chủ ngay lập tức.
- **Đề xuất xử lý Backend cho Codex**:
  1. **Route**:
     - `PATCH /admin/posts/{post}/featured` (`admin.posts.toggle-featured`), middleware `['auth', 'verified', 'role:admin']`.
  2. **Controller & Service**:
     - Trong `AdminPostController`:
       ```php
       public function toggleFeatured(Post $post): RedirectResponse
       {
           Gate::authorize('update', $post);
           $post->update(['is_featured' => ! $post->is_featured]);

           // Ghi log hoạt động
           ActivityLog::create([
               'user_id' => auth()->id(),
               'action' => $post->is_featured ? 'post.featured' : 'post.unfeatured',
               'description' => ($post->is_featured ? 'Ghim tiêu điểm bài viết: ' : 'Hủy tiêu điểm bài viết: ') . $post->title,
           ]);

           return back()->with('status', $post->is_featured ? 'Đã ghim bài viết làm Tiêu điểm trang chủ.' : 'Đã bỏ ghim tiêu điểm bài viết.');
       }
       ```
- **Kết quả mong muốn**: Admin có thể ghim/hủy ghim bài viết làm Tiêu điểm chỉ bằng 1 cú click ngay trong bảng bài viết mà không phải vào màn hình edit.

---

## 10. Bổ sung Bộ lọc Chuyên mục & Tác giả trong Quản trị bài viết (`admin.posts.index`)

- **UI cần hỗ trợ**: Bảng danh sách bài viết Admin (`resources/views/admin/posts/index.blade.php`).
- **Hiện trạng & Bất cập**:
  - `Admin\PostController@index` hiện chỉ lọc theo từ khóa `q` và trạng thái `status`.
  - Khi hệ thống có nhiều tác giả và bài viết, Ban biên tập cần lọc bài viết theo:
    1. **Chuyên mục** (`category_id`)
    2. **Tác giả** (`author_id`)
- **Đề xuất xử lý Backend cho Codex**:
  - Trong `Admin\PostController@index`:
    - Nhận thêm validation:
      - `'category_id' => ['nullable', 'exists:categories,id']`
      - `'author_id' => ['nullable', 'exists:users,id']`
    - Áp dụng scope query:
      - `->when($filters['category_id'] ?? null, fn ($q, $id) => $q->where('category_id', $id))`
      - `->when($filters['author_id'] ?? null, fn ($q, $id) => $q->where('author_id', $id))`
    - Truyền thêm dữ liệu cho view:
      - `'categories' => Category::orderBy('name')->get(['id', 'name'])`
      - `'authors' => User::whereIn('role', [UserRole::Author, UserRole::Admin])->orderBy('name')->get(['id', 'name'])`
- **Kết quả mong muốn**: Admin có thể lọc chính xác "Bài viết của Tác giả A trong chuyên mục Công nghệ đang ở trạng thái Published/Pending".

---

## 11. Kênh tin RSS Feed chuẩn Báo chí điện tử (`/feed`)

- **UI cần hỗ trợ**: Footer website (`resources/views/layouts/app.blade.php`), các ứng dụng đọc tin tự động (Google News, Apple News, Feedly).
- **Hiện trạng & Bất cập**:
  - Mọi báo điện tử chuyên nghiệp đều có đường dẫn RSS Feed để độc giả đăng ký nhận tin tự động qua các ứng dụng đọc tin RSS. Hiện tại dự án chưa có endpoint trả về chuẩn RSS 2.0 XML.
- **Đề xuất xử lý Backend cho Codex**:
  1. **Route**:
     - `GET /feed` (`feed.index`): Toàn bộ bài viết mới nhất.
     - `GET /feed/{category:slug}` (`feed.category`): Bài viết mới nhất theo chuyên mục.
  2. **Controller (`RssFeedController`)**:
     - Trả về XML response với `Content-Type: application/rss+xml; charset=UTF-8`:
       ```xml
       <?xml version="1.0" encoding="UTF-8"?>
       <rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
         <channel>
           <title>NewsHub - Báo điện tử</title>
           <link>{{ route('home') }}</link>
           <description>Cập nhật tin tức thời sự, công nghệ, kinh doanh 24/7</description>
           <language>vi</language>
           @foreach ($posts as $post)
             <item>
               <title>{{ $post->title }}</title>
               <link>{{ route('news.show', $post->slug) }}</link>
               <description><![CDATA[{{ $post->summary }}]]></description>
               <pubDate>{{ $post->published_at->toRssString() }}</pubDate>
               <guid isPermaLink="true">{{ route('news.show', $post->slug) }}</guid>
               <author>{{ $post->author->name }}</author>
               <category>{{ $post->category->name }}</category>
             </item>
           @endforeach
         </channel>
       </rss>
       ```
- **Kết quả mong muốn**: Website đạt chuẩn công nghệ của một trang báo điện tử hiện đại, sẵn sàng liên kết dữ liệu với các aggregator bên ngoài.

---

## 12. Cho phép Tác giả tự do tạo Thẻ bài viết mới khi soạn thảo (Dynamic Tags on the fly)

- **UI cần hỗ trợ**: Form viết & sửa bài viết của tác giả (`resources/views/author/posts/form.blade.php`), Admin duyệt bài (`resources/views/admin/posts/review.blade.php`).
- **Hiện trạng & Bất cập**:
  - `StorePostRequest` & `UpdatePostRequest` hiện chỉ chấp nhận `tag_ids.* => ['integer', 'distinct', Rule::exists('tags', 'id')]`.
  - Tác giả khi viết bài về các chủ đề nóng theo thời gian thực (ví dụ: `#iphone-16-pro`, `#chatgpt-5`, `#black-friday-2026`, sự kiện nóng...) không thể tạo thẻ mới mà bị ép buộc chỉ được chọn trong các thẻ có sẵn từ DB.
  - Phóng viên/Tác giả không thể chờ Admin tạo tag thủ công trong trang quản trị trước rồi mới được viết bài.
- **Đề xuất xử lý Backend cho Codex**:
  1. **Validation trong `StorePostRequest` và `UpdatePostRequest`**:
     - Cho phép nhận thêm mảng tên thẻ mới:
       ```php
       'tag_names' => ['nullable', 'array'],
       'tag_names.*' => ['string', 'max:50'],
       ```
  2. **Controller `Author\PostController@store` và `Author\PostController@update`**:
     - Tiến hành chuẩn hóa và tạo mới các thẻ chưa tồn tại bằng `Tag::firstOrCreate()`:
       ```php
       $existingTagIds = $request->validated('tag_ids', []);

       $newTagNames = collect($request->input('tag_names', []))
           ->filter()
           ->map(fn ($name) => trim(strip_tags($name)))
           ->unique();

       $createdTagIds = [];
       foreach ($newTagNames as $name) {
           if ($name === '') {
               continue;
           }
           $tag = Tag::firstOrCreate(
               ['slug' => Str::slug($name)],
               ['name' => $name]
           );
           $createdTagIds[] = $tag->id;
       }

       $allTagIds = array_values(array_unique(array_merge($existingTagIds, $createdTagIds)));
       $post->tags()->sync($allTagIds);
       ```
  3. **Feature Test**:
     - Viết test trong `PostWorkflowTest`:
       - `test_author_can_create_post_with_new_dynamic_tags()`: Tác giả gửi kèm `tag_names => ['OpenAI o3', 'Triển lãm CES 2026']`, xác nhận bài viết được lưu thành công, bảng `tags` có 2 bản ghi mới với slug tương ứng, và quan hệ `post_tag` được sync chính xác.
- **Kết quả mong muốn**: Tác giả hoàn toàn chủ động gắn thẻ từ khóa theo dòng sự kiện thời sự, tối ưu hóa SEO nội dung và tăng khả năng kết nối các bài viết cùng chủ đề.

---

## 13. Hỗ trợ Trình soạn thảo Trực quan (WYSIWYG), API Upload Ảnh trong bài (`POST /author/media/upload`), Nhúng Video & Tùy chọn Ẩn/Hiện Thumbnail (`show_thumbnail_in_post`)

- **UI cần hỗ trợ**: Form viết & sửa bài của tác giả/admin (`resources/views/author/posts/form.blade.php`), Trang chi tiết bài viết công khai (`resources/views/news/show.blade.php`), Chế độ xem trước bài viết (Live Preview Modal).
- **Hiện trạng & Bất cập**:
  - `content` của bài viết trước đây chỉ là plain text lưu trong thẻ `<textarea>`, hiển thị bằng `{{ $post->content }}` với `whitespace-pre-line`.
  - Tác giả khi viết các bài phóng sự, phân tích hoặc tin tức thời sự không thể chèn ảnh minh họa kèm chú thích (`caption`) vào giữa các đoạn văn, không thể nhúng video YouTube (16:9 responsive).
  - Ảnh đại diện (`thumbnail`) bị cố định ở đầu bài viết trên `news/show.blade.php`, dẫn tới tình trạng nếu bài viết chèn lại ảnh đó ở giữa bài thì độc giả sẽ thấy ảnh bị lặp 2 lần.
  - Frontend đã hoàn tất tích hợp **Quill.js WYSIWYG Editor**, thanh công cụ tùy chỉnh (H2, H3, Bold, Italic, Blockquote, Image, YouTube Video), checkbox `show_thumbnail_in_post` và **Chế độ xem trước bài viết trực tiếp (Live Preview Modal)**.
- **Đề xuất xử lý Backend cho Codex**:
  1. **Migration thêm cột `show_thumbnail_in_post` vào bảng `posts`**:
     ```php
     Schema::table('posts', function (Blueprint $table) {
         $table->boolean('show_thumbnail_in_post')->default(true)->after('thumbnail');
     });
     ```
  2. **Model `Post` & Form Requests**:
     - Thêm `'show_thumbnail_in_post'` vào `$fillable` hoặc khai báo `$casts = ['show_thumbnail_in_post' => 'boolean']`.
     - Trong `StorePostRequest` & `UpdatePostRequest`:
       ```php
       'show_thumbnail_in_post' => ['nullable', 'boolean'],
       ```
     - Trong `Author\PostController@store` và `update`:
       ```php
       $validated['show_thumbnail_in_post'] = $request->boolean('show_thumbnail_in_post', true);
       ```
  3. **API Upload Ảnh trong bài viết (Media Upload Endpoint)**:
     - Tạo Route trong `routes/web.php` (nhóm middleware `['auth', 'verified']`):
       ```php
       Route::post('/author/media/upload', [App\Http\Controllers\Author\MediaController::class, 'upload'])
           ->name('author.media.upload');
       ```
     - Tạo Controller `app/Http/Controllers/Author/MediaController.php`:
       ```php
       namespace App\Http\Controllers\Author;

       use App\Http\Controllers\Controller;
       use Illuminate\Http\JsonResponse;
       use Illuminate\Http\Request;
       use Illuminate\Support\Facades\Storage;
       use Illuminate\Support\Str;

       class MediaController extends Controller
       {
           public function upload(Request $request): JsonResponse
           {
               $request->validate([
                   'image' => ['required', 'file', 'image', 'mimes:jpeg,png,webp,gif', 'max:5120'], // Max 5MB
               ]);

               $file = $request->file('image');
               $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
               $path = $file->storeAs('posts/media', $filename, 'public');

               return response()->json([
                   'success' => true,
                   'url' => Storage::disk('public')->url($path),
               ], 201);
           }
       }
       ```
  4. **Bảo mật & Hiển thị Nội dung an toàn (XSS Sanitization)**:
     - Quill tạo ra mã HTML chứa các thẻ định dạng tin tức: `<p>`, `<h2>`, `<h3>`, `<strong>`, `<em>`, `<u>`, `<blockquote>`, `<ul>`, `<ol>`, `<li>`, `<a>`, `<figure>`, `<img>`, `<figcaption>`, `<iframe>`, `<div>`.
     - Đảm bảo kiểm duyệt (Sanitize) trước khi lưu hoặc hiển thị:
       - Có thể dùng Accessor `$post->safe_content` hoặc hàm lọc loại bỏ thẻ `<script>`, các thuộc tính nguy hiểm (`onload`, `onerror`, `javascript:`).
       - Cho phép thẻ `<iframe>` nhưng giới hạn domain uy tín (`youtube.com`, `youtu.be`).
     - Cập nhật Feature Test trong `PublicNewsTest.php` (`test_article_content_is_html_escaped`) để phản ánh cơ chế sanitize: script độc hại bị vô hiệu hóa trong khi các thẻ HTML định dạng báo chí hợp lệ được hiển thị mượt mà.
  5. **Feature Tests cần bổ sung**:
     - `test_author_can_upload_inline_media_image()`: Tác giả upload ảnh thành công, nhận lại HTTP 201 và JSON `{"url": "..."}`.
     - `test_guest_cannot_upload_inline_media()`: Khách chưa đăng nhập gọi API bị chuyển hướng `login`.
     - `test_post_supports_hiding_thumbnail_in_article()`: Bài viết có `show_thumbnail_in_post = false` không render ảnh đại diện ở đầu bài trên view `news.show`.
- **Kết quả mong muốn**: NewsHub sở hữu tính năng soạn thảo báo chí điện tử chuẩn mực như VnExpress, Dân Trí, WordPress; phóng viên có thể phân bổ hình ảnh và video xuyên suốt bài viết, đi kèm chú thích chuyên nghiệp và xem trước trực quan trước khi gửi duyệt.

---

## 14. Chuẩn hóa Dữ liệu Cây Chuyên mục từ Controller & Hỗ trợ JSON Response cho Modal CRUD (Chuyên mục & Thẻ)

- **UI cần hỗ trợ**: 
  - Trang Quản lý Chuyên mục (`resources/views/admin/categories/index.blade.php`).
  - Trang Quản lý Thẻ (`resources/views/admin/tags/index.blade.php`).
- **Hiện trạng Frontend đã hoàn thành**:
  - Toàn bộ thao tác **Thêm mới**, **Sửa**, và **Xác nhận xóa** của 2 trang này đã được Antigravity chuyển đổi sang dạng **Form nổi (Modal Popup / Dialog)** thay vì chuyển hướng sang trang `/create` hay `/edit`.
  - Có nút tạo nhanh danh mục con `+ Con` ngay trên từng dòng danh mục cha, tự động truyền `parent_id` vào Modal.
  - Có Modal xác nhận xóa an toàn thay thế `confirm()` mặc định của trình duyệt.
  - Hiện tại các Modal này submit dưới dạng standard HTML form (`POST`, `PUT`, `DELETE`) và backend redirect về index kèm session flash message.
- **Vấn đề Backend cần Codex tối ưu**:
  1. **Truyền cấu trúc Cây chuyên mục trực tiếp từ Controller**:
     - Trong `App\Http\Controllers\Admin\CategoryController@index`:
       - Hiện tại Controller chỉ truyền paginator phẳng `$categories` (`Category::query()->withCount('posts')->latest('id')->paginate(15)`).
       - Do đó, file view `index.blade.php` tạm thời phải tự thực hiện truy vấn Eloquent (`Category::query()->withCount('posts')->with(['children' => ...])`) để render cấu trúc cây cha-con.
       - **Đề xuất**: Codex hãy chuyển logic truy vấn cây chuyên mục vào `CategoryController@index` và truyền sẵn vào View:
         ```php
         public function index(): View
         {
             Gate::authorize('viewAny', Category::class);

             $allCategories = Category::query()
                 ->withCount('posts')
                 ->with(['children' => fn ($q) => $q->withCount('posts')->orderBy('name')])
                 ->orderBy('name')
                 ->get();

             $parentCategories = $allCategories->whereNull('parent_id')->values();
             $parentIds = $parentCategories->pluck('id')->all();
             $orphanCategories = $allCategories->filter(
                 fn ($cat) => $cat->parent_id !== null && !in_array($cat->parent_id, $parentIds, true)
             )->values();

             return view('admin.categories.index', [
                 'parentCategories' => $parentCategories,
                 'orphanCategories' => $orphanCategories,
                 'allCategories' => $allCategories,
             ]);
         }
         ```
  2. **Hỗ trợ JSON Response khi client yêu cầu (`wantsJson()`) cho Category & Tag CRUD**:
     - Trong `Admin\CategoryController` (`store`, `update`, `destroy`) và `Admin\TagController` (`store`, `update`, `destroy`):
       - Bổ sung kiểm tra `$request->wantsJson()`:
         ```php
         // Ví dụ trong CategoryController@store:
         $category = Category::create($request->validated());

         if ($request->wantsJson()) {
             return response()->json([
                 'success' => true,
                 'message' => 'Đã tạo chuyên mục.',
                 'data' => $category,
             ], 201);
         }

         return redirect()->route('admin.categories.index')->with('status', 'Đã tạo chuyên mục.');
         ```
       - Tương tự cho `update`:
         ```php
         $category->update($request->validated());

         if ($request->wantsJson()) {
             return response()->json([
                 'success' => true,
                 'message' => 'Đã cập nhật chuyên mục.',
                 'data' => $category,
             ]);
         }

         return redirect()->route('admin.categories.index')->with('status', 'Đã cập nhật chuyên mục.');
         ```
       - Tương tự cho `destroy`:
         ```php
         if ($category->posts()->exists() || $category->children()->exists()) {
             if ($request->wantsJson()) {
                 return response()->json([
                     'success' => false,
                     'message' => 'Không thể xóa chuyên mục đang có bài viết hoặc danh mục con.',
                 ], 422);
             }

             throw ValidationException::withMessages([
                 'category' => 'Không thể xóa chuyên mục đang có bài viết hoặc danh mục con.',
             ]);
         }

         $category->delete();

         if ($request->wantsJson()) {
             return response()->json([
                 'success' => true,
                 'message' => 'Đã xóa chuyên mục.',
             ]);
         }

         return redirect()->route('admin.categories.index')->with('status', 'Đã xóa chuyên mục.');
         ```
       - Áp dụng cơ chế tương tự cho `TagController@store`, `update`, `destroy`.
  3. **Lợi ích**:
     - Đảm bảo 100% backward compatibility: Mọi test PHPUnit hiện có và form submit reload vẫn chạy bình thường.
     - Cho phép Frontend trong tương lai có thể gửi fetch request ngầm, nhận lỗi validation 422 để hiển thị trực tiếp trong Modal mà không làm mất dữ liệu người dùng vừa nhập.
- **File Backend phụ thuộc**:
  - `app/Http/Controllers/Admin/CategoryController.php`
  - `app/Http/Controllers/Admin/TagController.php`
- **Kết quả mong muốn**: Code Controller chuẩn MVC, không để view Blade tự gọi Model query; hỗ trợ kiến trúc API mở cho Modal CRUD.

---

## 15. Quy trình Yêu cầu Gỡ bài / Đính chính Bài viết Đã Xuất bản & Hủy Gửi Duyệt về Nháp

- **UI cần hỗ trợ**:
  - Trang danh sách bài viết tác giả (`resources/views/author/posts/index.blade.php`).
  - Trang chỉnh sửa bài viết tác giả (`resources/views/author/posts/form.blade.php`).
  - Trang xem trước bài viết (`resources/views/news/preview.blade.php`).
  - Trang quản trị danh sách yêu cầu bài viết của Admin (`resources/views/admin/post_requests/index.blade.php`).
- **Bối cảnh & Nghiệp vụ**:
  - Trong thực tế tòa soạn báo chí và xuất bản chuyên nghiệp, tác giả **không được tự ý xóa vĩnh viễn bài viết đã xuất bản (`published`)** để bảo vệ tính toàn vẹn thông tin, lượng tương tác độc giả và liên kết SEO.
  - Thay vào đó, tác giả gửi **"Yêu cầu gỡ bài"** hoặc **"Yêu cầu đính chính / chỉnh sửa"** đến Ban biên tập (Admin).
  - Đối với bài đang ở trạng thái **Chờ duyệt (`pending_review`)**, tác giả có thể chủ động **"Rút bài về nháp (Withdraw)"** để tự sửa lỗi mà không cần làm phiền Admin.
  - Đối với bài ở trạng thái **Bản nháp (`draft`)** và **Bị từ chối (`rejected`)**, tác giả được phép **Xóa trực tiếp** (đã có route `DELETE /author/posts/{post}`).
- **Đề xuất xử lý Backend**:
  1. **Migration tạo bảng `post_requests`**:
     - `id`: bigint primary key
     - `post_id`: foreignId constrained to `posts` on cascade delete
     - `author_id`: foreignId constrained to `users` on cascade delete
     - `type`: string/enum (`removal`: gỡ bài, `correction`: đính chính/sửa đổi)
     - `priority`: string/enum (`normal`, `urgent`)
     - `reason`: string (lý do tóm tắt)
     - `notes`: text (nội dung trình bày chi tiết của tác giả)
     - `status`: string/enum (`pending`: đang chờ, `approved`: đã duyệt, `rejected`: từ chối)
     - `admin_notes`: text nullable (lý do/phản hồi của Admin)
     - `handled_by`: foreignId nullable constrained to `users`
     - `handled_at`: timestamp nullable
     - `timestamps`
  2. **Model `PostRequest` & Quan hệ Eloquent**:
     - `Post` model có quan hệ:
       ```php
       public function requests(): HasMany
       {
           return $this->hasMany(PostRequest::class);
       }
       ```
     - `User` model có quan hệ `postRequests()`.
  3. **Routes & Controller cho Tác giả**:
     - **Gửi yêu cầu gỡ/đính chính**:
       - `POST /author/posts/{post}/requests` -> `Author\PostRequestController@store`
       - Validation: `type` (in:removal,correction), `priority` (in:normal,urgent), `reason` (string, max:255), `notes` (string, max:2000).
       - Author chỉ có thể gửi khi `$post->author_id === auth()->id()` và bài viết `$post->status === PostStatus::Published`.
     - **Rút bài chờ duyệt về nháp (Withdraw)**:
       - `POST /author/posts/{post}/withdraw` -> `Author\PostSubmissionController@withdraw`
       - Logic: Nếu `$post->status === PostStatus::PendingReview` và `$post->author_id === auth()->id()`, chuyển `$post->status = PostStatus::Draft` và `$post->save()`.
  4. **Routes & Controller cho Quản trị viên (Admin)**:
     - `GET /admin/post-requests` -> `Admin\PostRequestController@index` (hiển thị danh sách yêu cầu với bộ lọc trạng thái `pending`, `approved`, `rejected`).
     - `PATCH /admin/post-requests/{postRequest}` -> `Admin\PostRequestController@update`
       - Admin chọn:
         + `approved`:
           - Nếu `type === 'removal'`: Chuyển bài viết liên quan sang `PostStatus::Hidden` (hoặc `Archived`), lưu `status = 'approved'`, `handled_by = auth()->id()`, `handled_at = now()`.
           - Nếu `type === 'correction'`: Có thể chuyển bài về `PostStatus::Draft` để tác giả vào sửa, hoặc cho phép Admin chỉnh sửa trực tiếp.
         + `rejected`: Giữ nguyên bài viết, lưu `admin_notes` giải thích lý do từ chối.
  5. **Cung cấp chỉ số badge cho Sidebar Admin**:
     - Thêm `pending_post_requests_count` vào View Composer của Sidebar để Admin luôn thấy số lượng yêu cầu cần xử lý.
- **File Backend phụ thuộc**:
  - `database/migrations/xxxx_xx_xx_create_post_requests_table.php`
  - `app/Models/PostRequest.php`, `app/Enums/PostRequestType.php`, `app/Enums/PostRequestStatus.php`
  - `app/Http/Controllers/Author/PostRequestController.php`
  - `app/Http/Controllers/Author/PostSubmissionController.php`
  - `app/Http/Controllers/Admin/PostRequestController.php`
- **Kết quả mong muốn**: Tác giả tương tác mượt mà với toà soạn, bài viết đã xuất bản được bảo vệ an toàn, Admin kiểm soát chặt chẽ mọi thay đổi và xóa bài trên toàn trang tin.

---

*Tài liệu được cập nhật ngày 04/10/2026. Mọi thắc mắc về hợp đồng dữ liệu giao diện vui lòng đối chiếu với [HANDOFF_UI.md](file:///D:/Code/code/PhpProject/HANDOFF_UI.md).*



