# Backend handoff response

**BACKEND STATUS: READY FOR FRONTEND**

Tài liệu này phản hồi các yêu cầu trong `BACKEND_REQUESTS.md`. Phạm vi triển khai tập trung vào backend, data contract và test; không redesign UI, CSS, JavaScript, layout hoặc style. Một số binding Blade tối thiểu được đổi để dùng HTML đã sanitize, tôn trọng tùy chọn thumbnail và loại query Eloquent khỏi view theo đúng contract backend.

## 1. Kết quả theo từng yêu cầu

### 1.1. Badge chờ xử lý trên sidebar — Hoàn thành

- Đã đăng ký `DashboardSidebarComposer` cho `layouts.dashboard`.
- Composer chỉ truy vấn và cung cấp dữ liệu khi người đang đăng nhập có role `admin`.
- Hai biến mới luôn có trên dashboard shell của Admin:

```php
[
    'pending_posts_count' => int,
    'pending_reports_count' => int,
    'pending_applications_count' => int,
    'pending_post_requests_count' => int,
]
```

- Để giữ tương thích với layout hiện tại, composer đồng thời bổ sung hai key cũ:

```php
$adminStats['pending_posts'];
$adminStats['pending_reports'];
$adminStats['pending_applications'];
$adminStats['pending_post_requests'];
```

- Trang dashboard chính tái sử dụng số liệu đã có, không chạy lại hai truy vấn badge. Các trang admin con lấy số liệu thời gian thực từ `DashboardStatsService::pendingModerationCounts()`.

### 1.2. Chỉ số hoạt động hồ sơ — Hoàn thành

`ProfileController@edit` hiện truyền thêm:

```php
$stats = [
    'comments_count' => int,
    'favorites_count' => int,
    'posts_count' => int,
];
```

Các giá trị được lấy bằng relation count thật của user. `posts_count` hiện là tổng bài viết thuộc user, không giới hạn theo trạng thái. Biến `avatarUrl` cũ được giữ nguyên.

### 1.3. Xóa avatar — Hoàn thành

Endpoint hiện có `PATCH /profile` (`profile.update`) nhận thêm:

```php
remove_avatar: boolean|null
```

Khi `remove_avatar` là truthy và không có avatar mới:

- `users.avatar` được đặt thành `null`.
- File avatar cũ được xóa khỏi public disk sau khi lưu database thành công.
- Nếu lưu database thất bại, file mới vừa tối ưu (nếu có) được dọn dẹp và avatar cũ không bị xóa.

Nếu request đồng thời có `avatar` mới và `remove_avatar=1`, avatar mới được ưu tiên và file cũ vẫn được xóa an toàn.

### 1.4. Đổi email và xác minh lại — Hoàn thành

Endpoint `PATCH /profile` nhận thêm field tùy chọn:

```php
email: string
```

Validation:

- Chỉ validate khi request có gửi `email`, nên form cũ không gửi email vẫn hoạt động.
- Bắt buộc là chuỗi email viết thường, tối đa 255 ký tự.
- Unique trong bảng users, ngoại trừ chính user hiện tại.

Khi email thực sự thay đổi:

- Cập nhật email mới.
- Đặt `email_verified_at` thành `null`.
- Gọi `sendEmailVerificationNotification()` hiện có trên model `User` để gửi liên kết xác minh mới.

Các field nhạy cảm như `role` và `status` vẫn không được lấy từ payload cập nhật hồ sơ.

### 1.5. Breadcrumb đa tầng — Hoàn thành data contract

Các view form sau đã nhận biến `$breadcrumbs`:

- `admin.categories.form`: create và edit.
- `admin.tags.form`: create và edit.
- `author.posts.form`: create và edit.

Cấu trúc thống nhất:

```php
array<array{
    label: string,
    url: ?string,
}> $breadcrumbs
```

Phần tử cha có URL dùng named route; phần tử hiện tại có `url => null`. Backend chỉ cung cấp dữ liệu, chưa thay đổi layout để render breadcrumb.

### 1.6. Admin xuất bản bài trực tiếp — Hoàn thành

- `StorePostRequest` và `UpdatePostRequest` nhận thêm:

```php
[
    'action' => 'draft' | 'publish' | null,
    'published_at' => 'date' | null,
]
```

- Admin gửi `action=publish` khi tạo hoặc sửa bài sẽ đưa bài thẳng sang `published`, đặt `published_at` theo request hoặc thời điểm hiện tại, xóa rejection cũ và ghi activity log `post.published`.
- Author gửi `action=publish` không thể bỏ qua kiểm duyệt: bài mới vẫn được lưu ở trạng thái `draft`.
- `action=draft` khi cập nhật sẽ đưa bài về bản nháp và xóa lịch xuất bản/rejection cũ. Request cũ không gửi `action` vẫn giữ hành vi cập nhật hiện tại.
- Có endpoint xuất bản nhanh dành riêng cho Admin và chỉ áp dụng với bài draft/rejected do chính Admin đó sở hữu.
- Logic xuất bản dùng lại `PostWorkflowService`; luồng submit/review cũ của Author không bị thay thế.

### 1.7. Ứng tuyển và duyệt tác giả/CTV — Hoàn thành backend

- Đã thêm model, enum trạng thái, migration, factory, policy, form request, service, controller và route.
- Trạng thái hợp lệ: `pending`, `approved`, `rejected`.
- User đã xác minh có thể nộp đơn với `category_id`, `bio`, `sample_title`, `sample_content`.
- Chỉ role `user` được nộp đơn; Author/Admin bị từ chối bằng authorization.
- Một user chỉ có tối đa một đơn pending. Invariant được bảo vệ bằng transaction, row lock và unique key `pending_user_id` ở database.
- Admin approve sẽ cập nhật đơn, cấp role `author` nếu tài khoản vẫn là User và ghi `author_application.approved`.
- Admin reject bắt buộc `reason`, giải phóng trạng thái pending để User có thể nộp lại và ghi `author_application.rejected`.
- Nhóm route `author.posts.*` hiện yêu cầu `role:author,admin`; User thường nhận `403` kể cả khi đã xác minh email.
- Dashboard của User nhận thêm contract để Antigravity dựng form/trạng thái:

```php
[
    'authorApplication' => AuthorApplication|null, // đơn mới nhất, đã load category
    'authorApplicationCategories' => Collection,   // id, name của category active
]
```

- Admin sidebar composer nhận thêm:

```php
[
    'pending_applications_count' => int,
    'adminStats.pending_applications' => int, // backward-compatible aggregate
]
```

- Trang quản trị đơn ứng tuyển nhận:

```php
[
    'applications' => LengthAwarePaginator, // eager-load user, category, reviewer
    'selectedStatus' => AuthorApplicationStatus,
    'statuses' => AuthorApplicationStatus[],
    'breadcrumbs' => array,
]
```

### 1.8. Danh mục cha-con — Hoàn thành

- Bảng `categories` có thêm foreign key nullable `parent_id` tự tham chiếu, `nullOnDelete`.
- Model `Category` có các quan hệ `parent`, `children`, scope `parents()` và helper `selfAndChildIds()`.
- Hệ thống giới hạn cấu trúc ở 2 tầng: danh mục cha phải là root; không được tự chọn chính nó làm cha; danh mục đang có con không thể bị chuyển thành con của danh mục khác.
- Form create/edit nhận thêm contract:

```php
$parentCategories; // Collection<Category{id, name}>, chỉ gồm danh mục root hợp lệ
```

- Payload create/update nhận thêm:

```php
parent_id: int|null
```

- Không cho soft-delete danh mục đang có bài viết hoặc danh mục con để tránh làm mất nhánh điều hướng.
- `NewsController@index` truyền thêm `$selectedCategory` và khi lọc một danh mục cha, paginator gồm bài thuộc chính danh mục cha và các danh mục con trực tiếp.
- `DemoContentSeeder` đã gán 7 danh mục công nghệ vào `Công nghệ`, và `Khởi nghiệp công nghệ` vào `Kinh doanh`. Seeder vẫn idempotent.

### 1.9. Ghim/bỏ ghim tiêu điểm nhanh — Hoàn thành

- Endpoint toggle riêng cho Admin dùng lại `PostModerationService`, không duplicate business logic.
- Toggle chạy trong transaction và khóa row trước khi đọc trạng thái hiện tại, tránh hai request đồng thời ghi theo dữ liệu cũ.
- Chỉ bài `published` mới được ghim/bỏ ghim; bài draft/rejected/hidden bị từ chối bằng validation.
- Activity log ghi `post.featured` hoặc `post.unfeatured`, kèm tiêu đề bài viết trong mô tả.
- Flash message trả về đúng trạng thái sau toggle.

### 1.10. Bộ lọc chuyên mục và tác giả cho danh sách bài Admin — Hoàn thành

`Admin\PostController@index` nhận thêm query params:

```php
[
    'category_id' => int|null, // category tồn tại và chưa bị soft-delete
    'author_id' => int|null,   // user tồn tại
]
```

Các bộ lọc mới có thể kết hợp với `q` và `status`. View `admin.posts.index` nhận thêm:

```php
[
    'categories' => Collection<Category{id, name}>,
    'authors' => Collection<User{id, name}>, // chỉ role author/admin
]
```

Paginator giữ toàn bộ query string khi chuyển trang.

### 1.11. RSS Feed — Hoàn thành

- Feed tổng hợp tối đa 50 bài mới nhất đang thực sự public.
- Feed chuyên mục cha gồm bài của chính danh mục và các danh mục con trực tiếp.
- Draft, bài hẹn giờ, bài bị ẩn và bài thuộc category ẩn không xuất hiện.
- Category ẩn hoặc soft-delete trả `404` ở feed chuyên mục.
- Response dùng `Content-Type: application/rss+xml; charset=UTF-8`, RSS 2.0, Atom self-link và `dc:creator` để biểu diễn tên tác giả đúng chuẩn mà không công khai email.
- Nội dung động được escape XML để không tạo XSS hoặc XML không hợp lệ.

### 1.12. Dynamic Tags — Hoàn thành

- `StorePostRequest` và `UpdatePostRequest` nhận thêm:

```php
[
    'tag_names' => array|null,       // tối đa 20 tag/request
    'tag_names.*' => string|max:50,
]
```

- `PostTagService` chuẩn hóa tên, loại HTML, tạo slug, bỏ trùng theo slug và dùng `createOrFirst()` để an toàn hơn khi hai request đồng thời tạo cùng một tag.
- ID tag có sẵn và tag vừa tạo được hợp nhất trước khi `sync()` quan hệ `post_tag`.
- Cả luồng tạo và cập nhật bài viết đều dùng chung service, nằm trong transaction hiện có.

### 1.13. WYSIWYG, inline media và thumbnail visibility — Hoàn thành

- Bảng `posts` có thêm boolean `show_thumbnail_in_post`, mặc định `true`; model có fillable và boolean cast.
- Form tạo/cập nhật bài nhận `show_thumbnail_in_post: boolean|null`. Request cũ không gửi field vẫn giữ hành vi tương thích: bài mới mặc định hiện ảnh, bài cũ giữ lựa chọn hiện tại khi update.
- Endpoint upload ảnh inline:

```text
POST /author/media/upload
Route name: author.media.upload
Payload: multipart/form-data, field image
Success: HTTP 201
{
  "success": true,
  "url": "/storage/posts/media/{uuid}.webp"
}
```

- Ảnh nhận JPG/JPEG/PNG/WEBP/GIF, tối đa 5 MB và tối đa 4096×4096 đầu vào. Backend giải mã, bỏ metadata, giới hạn 1920×1920 và mã hóa lại thành WebP trước khi lưu public disk.
- Endpoint nằm trong middleware `auth`, `active`, `verified`, `role:author,admin` và rate limit 30 request/phút.
- `PostContentSanitizer` cho phép markup báo chí cần thiết (`p`, `h2`, `h3`, `strong`, `em`, danh sách, ảnh, figure, link...) nhưng loại script, event handler, style và URL nguy hiểm.
- Iframe chỉ được giữ khi host thuộc YouTube/YouTube No-Cookie/Youtu.be; iframe domain khác bị loại bỏ.
- Nội dung được sanitize trước khi lưu và sanitize lại trước khi render public, preview và màn hình duyệt để bảo vệ cả dữ liệu cũ.
- Nội dung hợp lệ được render thành HTML; không còn hiển thị mã HTML dưới dạng plain text.

### 1.14. Category tree từ Controller và JSON CRUD — Hoàn thành

- `Admin\CategoryController@index` thực hiện toàn bộ query cây, eager-load children và post count; Blade không còn gọi `Category::query()`.
- View nhận chính xác:

```php
[
    'allCategories' => Collection<Category>,
    'parentCategories' => Collection<Category>,
    'orphanCategories' => Collection<Category>,
]
```

- Category và Tag `store`, `update`, `destroy` vẫn redirect/flash như cũ cho form HTML.
- Khi header `Accept: application/json`, các endpoint trả JSON gồm `success`, `message`, `data` và status thích hợp (`201`, `200`, `422`).
- Xóa category có bài viết hoặc category con trả JSON `422` mà không xóa dữ liệu.

### 1.15. Yêu cầu gỡ bài/đính chính và rút bài về nháp — Hoàn thành

- Tác giả và Admin có thể gửi yêu cầu cho chính bài viết đã xuất bản của mình qua `author.posts.requests.store`.
- Payload được validate theo contract:

```php
[
    'type' => 'removal'|'correction',
    'priority' => 'normal'|'urgent',
    'reason' => string,       // bắt buộc, tối đa 255 ký tự
    'notes' => string|null,   // tối đa 2.000 ký tự
]
```

- Mỗi bài chỉ có tối đa một yêu cầu `pending` tại cùng thời điểm. Backend kiểm tra trong transaction và bảng có unique key kỹ thuật `pending_post_id` để chống request đồng thời.
- Tác giả có thể rút bài `pending_review` của chính mình về `draft`; thao tác được ghi `activity_logs` với action `post.withdrawn`.
- Admin có thể lọc danh sách yêu cầu theo `pending`, `approved`, `rejected` và xử lý từng yêu cầu.
- Khi duyệt yêu cầu `removal`, bài chuyển sang `hidden` và tự bỏ trạng thái featured.
- Khi duyệt yêu cầu `correction`, bài chuyển về `draft`, xóa `published_at` và tự bỏ featured để tác giả chỉnh sửa rồi gửi duyệt lại.
- Khi từ chối, bài viết giữ nguyên trạng thái và `admin_notes` là bắt buộc.
- Toàn bộ thao tác gửi/xử lý dùng transaction, `lockForUpdate()` và activity log để không có trạng thái xử lý một nửa hoặc duyệt trùng.
- View composer cung cấp thêm `$pending_post_requests_count`; `$adminStats['pending_post_requests']` được giữ song song cho cách tích hợp hiện tại.

## 2. File backend đã sửa

- `app/Http/ViewComposers/DashboardSidebarComposer.php` — composer dữ liệu badge admin.
- `app/Providers/AppServiceProvider.php` — đăng ký composer cho dashboard layout.
- `app/Services/DashboardStatsService.php` — tập trung query số bài và báo cáo chờ xử lý để dashboard/composer cùng tái sử dụng.
- `app/Http/Controllers/ProfileController.php` — relation counts, truyền stats và chuyển input email/remove-avatar sang service.
- `app/Http/Requests/Profile/UpdateProfileRequest.php` — validation email và `remove_avatar`.
- `app/Services/UserAvatarService.php` — cập nhật email, reset xác minh, gửi notification, xóa/thay avatar an toàn.
- `app/Http/Controllers/Admin/CategoryController.php` — breadcrumb create/edit danh mục.
- `app/Http/Controllers/Admin/TagController.php` — breadcrumb create/edit thẻ.
- `app/Http/Controllers/Author/PostController.php` — breadcrumb create/edit bài viết.
- `app/Enums/AuthorApplicationStatus.php` — trạng thái đơn ứng tuyển.
- `app/Models/AuthorApplication.php` và `app/Models/User.php` — model, casts và relations ứng tuyển.
- `app/Policies/AuthorApplicationPolicy.php` — quyền xem/duyệt/từ chối/nộp đơn.
- `app/Services/AuthorApplicationService.php` — submit/approve/reject atomic và activity log.
- `app/Http/Requests/Author/StoreAuthorApplicationRequest.php` — authorization và validation đơn.
- `app/Http/Requests/Admin/RejectAuthorApplicationRequest.php` — authorization và validation lý do từ chối.
- `app/Http/Controllers/AuthorApplicationController.php` — nhận đơn từ User.
- `app/Http/Controllers/Admin/AuthorApplicationController.php` — danh sách/filter/approve/reject.
- `app/Http/Controllers/Author/PostPublicationController.php` — xuất bản nhanh bài của Admin.
- `app/Http/Requests/Author/StorePostRequest.php` và `UpdatePostRequest.php` — action/published_at.
- `app/Policies/PostPolicy.php` — giới hạn khu vực viết và quyền publish.
- `app/Services/PostWorkflowService.php` — workflow publish trực tiếp có audit.
- `app/Http/Controllers/DashboardController.php` — application/status/category contract cho User.
- `routes/web.php` — route ứng tuyển, quản trị đơn, publish nhanh và role middleware.
- `database/migrations/2026_10_03_184025_create_author_applications_table.php` — schema mới.
- `database/factories/AuthorApplicationFactory.php` — dữ liệu test cho các trạng thái.
- `database/migrations/2026_10_04_040548_add_parent_id_to_categories_table.php` — foreign key phân cấp category.
- `app/Models/Category.php` và `database/factories/CategoryFactory.php` — quan hệ, scope, helper và factory hierarchy.
- `app/Http/Requests/Admin/StoreCategoryRequest.php` và `UpdateCategoryRequest.php` — validation category cha.
- `app/Http/Controllers/Admin/CategoryController.php` — parent options và bảo vệ khi xóa category cha.
- `app/Http/Controllers/NewsController.php` — lọc bài theo category cha/con và `$selectedCategory`.
- `database/seeders/DemoContentSeeder.php` — cây category demo idempotent.
- `app/Http/Controllers/Admin/PostFeaturedController.php` — endpoint toggle featured.
- `app/Services/PostModerationService.php` — toggle atomic và audit description.
- `app/Http/Controllers/Admin/PostController.php` — filter category/author và option collections.
- `app/Http/Controllers/RssFeedController.php` và `resources/views/feed.blade.php` — RSS response/XML template.
- `app/Services/PostTagService.php` — chuẩn hóa, tạo và hợp nhất dynamic tags.
- `app/Services/PostContentSanitizer.php` — allow-list HTML, URL và YouTube iframe.
- `app/Http/Controllers/Author/PostController.php` — tích hợp tag động, sanitize content và thumbnail visibility.
- `app/Http/Requests/Author/StorePostRequest.php` và `UpdatePostRequest.php` — validation tag names và thumbnail visibility.
- `app/Http/Controllers/Author/MediaController.php` và `app/Http/Requests/Author/UploadPostMediaRequest.php` — endpoint/validation inline media.
- `config/images.php` — giới hạn kích thước inline media.
- `app/Models/Post.php` và `database/factories/PostFactory.php` — field/cast/default thumbnail visibility.
- `database/migrations/2026_10_04_063300_add_show_thumbnail_in_post_to_posts_table.php` — schema thumbnail visibility.
- `app/Http/Controllers/Admin/CategoryController.php` — query cây và JSON category CRUD.
- `app/Http/Controllers/Admin/TagController.php` — JSON tag CRUD.
- `app/Http/Controllers/NewsController.php`, `PostPreviewController.php` và `Admin/PostReviewController.php` — truyền HTML đã sanitize tới view.
- `resources/views/admin/categories/index.blade.php` — bỏ query Eloquent khỏi Blade.
- `resources/views/news/show.blade.php`, `news/preview.blade.php`, `admin/posts/review.blade.php`, `author/posts/form.blade.php` — binding tối thiểu cho safe HTML/thumbnail/checkbox; không thay đổi thiết kế.
- `app/Enums/PostRequestType.php`, `PostRequestPriority.php`, `PostRequestStatus.php` — enum contract của yêu cầu bài viết.
- `app/Models/PostRequest.php`, `app/Models/Post.php`, `app/Models/User.php` — model, casts và relations của workflow yêu cầu.
- `app/Policies/PostRequestPolicy.php` và `app/Policies/PostPolicy.php` — quyền xử lý, rút bài và gửi yêu cầu.
- `app/Http/Requests/Author/StorePostWorkflowRequest.php` — authorization/validation khi tác giả gửi yêu cầu.
- `app/Http/Requests/Admin/UpdatePostWorkflowRequest.php` — authorization/validation khi Admin xử lý.
- `app/Http/Controllers/Author/PostRequestController.php` — nhận yêu cầu gỡ/đính chính.
- `app/Http/Controllers/Admin/PostRequestController.php` — danh sách/filter và xử lý yêu cầu.
- `app/Http/Controllers/Author/PostSubmissionController.php` — thêm thao tác withdraw.
- `app/Services/PostRequestService.php` và `PostWorkflowService.php` — workflow atomic, khóa bản ghi và audit.
- `app/Services/DashboardStatsService.php` và `app/Http/ViewComposers/DashboardSidebarComposer.php` — badge yêu cầu chờ xử lý.
- `database/migrations/2026_10_04_095351_create_post_requests_table.php` — schema yêu cầu bài viết.
- `database/factories/PostRequestFactory.php` và `tests/Feature/PostRequestWorkflowTest.php` — factory/test workflow Mục 15.

## 3. Endpoint và route

- Các endpoint cũ không bị xóa hoặc đổi tên.
- `PATCH /profile` (`profile.update`) nhận thêm `email` và `remove_avatar` như phần trước.
- `POST /author/posts/{post}/publish` — `author.posts.publish`, chỉ Admin xuất bản bài của chính mình.
- `POST /author-applications` — `author-applications.store`, User đã xác minh nộp đơn.
- `GET /admin/author-applications` — `admin.author-applications.index`, filter qua `?status=pending|approved|rejected`.
- `PATCH /admin/author-applications/{application}/approve` — `admin.author-applications.approve`.
- `PATCH /admin/author-applications/{application}/reject` — `admin.author-applications.reject`, payload `reason` bắt buộc.
- `PATCH /admin/posts/{post}/featured` — `admin.posts.toggle-featured`, chỉ Admin và chỉ bài published.
- `GET /feed` — `feed.index`, feed bài public mới nhất.
- `GET /feed/{category:slug}` — `feed.category`, feed category theo slug.
- `POST /author/media/upload` — `author.media.upload`, upload ảnh inline cho Author/Admin đã xác minh.
- Category/Tag CRUD hiện hỗ trợ content negotiation JSON trên chính các endpoint cũ; không thêm hoặc đổi tên endpoint CRUD.
- `POST /author/posts/{post}/requests` — `author.posts.requests.store`, gửi yêu cầu gỡ/đính chính.
- `POST /author/posts/{post}/withdraw` — `author.posts.withdraw`, rút bài chờ duyệt về nháp.
- `GET /admin/post-requests` — `admin.post-requests.index`, filter qua `?status=pending|approved|rejected`.
- `PATCH /admin/post-requests/{postRequest}` — `admin.post-requests.update`, payload `status=approved|rejected` và `admin_notes` bắt buộc khi từ chối.
- Đã kiểm tra toàn bộ **76 routes** bằng `php artisan route:list --except-vendor`.

## 4. Authentication, authorization và permission

- Profile vẫn nằm trong middleware `auth` và `active` như trước.
- Badge moderation chỉ được composer cung cấp cho Admin.
- Quyền truy cập form danh mục/thẻ vẫn do policy Admin hiện có kiểm soát.
- Quyền truy cập form bài viết vẫn do `PostPolicy` và middleware `verified` hiện có kiểm soát.
- Khu vực `author.posts.*` yêu cầu đồng thời `verified` và role `author|admin`.
- Chỉ Admin được publish trực tiếp; quick publish còn kiểm tra quyền sở hữu bài.
- Chỉ User được nộp đơn; chỉ Admin được xem và xử lý đơn.
- Approve đơn là điểm duy nhất trong workflow mới tự động nâng role User thành Author.
- Endpoint toggle featured và filter danh sách bài nằm trong group `auth`, `active`, `verified`, `role:admin`; controller tiếp tục kiểm tra `PostPolicy@update`.
- RSS là endpoint public nhưng chỉ trả bài thỏa scope `publiclyVisible()`.
- Upload media chỉ dành cho Author/Admin đã xác minh, có validation nội dung file, kích thước, pixel dimension và rate limit.
- HTML bài viết được allow-list trước khi lưu và trước khi render; JavaScript URL, inline event, script và iframe ngoài YouTube bị loại.
- Chỉ Author/Admin đã xác minh mới có thể gửi yêu cầu hoặc rút bài; policy kiểm tra bài thuộc chính người thao tác và đúng trạng thái.
- Chỉ Admin có thể xem danh sách và xử lý yêu cầu. Yêu cầu đã xử lý không thể được xử lý lần hai.

## 5. Database và migration

- Có migration mới: `2026_10_03_184025_create_author_applications_table.php`.
- Có migration mới: `2026_10_04_040548_add_parent_id_to_categories_table.php`.
- Có migration mới: `2026_10_04_063300_add_show_thumbnail_in_post_to_posts_table.php`.
- Có migration mới: `2026_10_04_095351_create_post_requests_table.php`.
- Migration đã được chạy thành công trên database local bằng `php artisan migrate --force`.
- Bảng gồm các field yêu cầu và field kỹ thuật nullable `pending_user_id` có unique index để chống hai đơn pending đồng thời trên mọi database được hỗ trợ.
- `user_id` và `category_id` cascade khi bản ghi cha bị xóa; `reviewed_by` được đặt null khi reviewer bị xóa.
- Có composite index `status, created_at` phục vụ filter và sắp xếp danh sách quản trị.
- Không thêm seeder/fake data runtime. Factory mới chỉ dùng trong test.
- `DemoContentSeeder` hiện có hierarchy thật và đã được chạy lại trên database local; không tạo trùng user/category/tag/post.
- Migration `show_thumbnail_in_post` đã chạy trên database local và ở trạng thái `Ran` (batch 6).
- Migration `post_requests` đã chạy trên database local và ở trạng thái `Ran` (batch 7).
- `post_id`/`author_id` cascade khi bài hoặc tác giả bị xóa; `handled_by` được đặt null khi Admin xử lý bị xóa.
- Các index `status, created_at` và `author_id, created_at` phục vụ danh sách/filter; unique `pending_post_id` bảo đảm một yêu cầu pending cho mỗi bài.

## 6. Việc Antigravity cần thực hiện tiếp

- Sidebar có thể chuyển sang đọc trực tiếp `$pending_posts_count` và `$pending_reports_count`. Layout hiện tại vẫn hoạt động qua `$adminStats` để bảo đảm backward compatibility.
- Render `$breadcrumbs` trong topbar nếu biến tồn tại; giữ fallback `$title` cho các view chưa truyền breadcrumb.
- Bật input email trong form profile và đặt `name="email"` nếu muốn cho người dùng chỉnh sửa.
- Thêm checkbox/nút gửi `remove_avatar=1` nếu muốn hiển thị chức năng gỡ avatar.
- Có thể hiển thị `$stats['posts_count']`; hai key comments/favorites hiện có vẫn dùng bình thường.
- Trên dashboard User, dùng `$authorApplication` để hiển thị trạng thái mới nhất và `$authorApplicationCategories` cho select chuyên mục; form POST tới `author-applications.store`.
- Chỉ hiển thị menu viết bài khi role là `author` hoặc `admin`; backend đã chặn User thường bằng middleware và policy.
- Form bài viết Admin có thể gửi `action=publish`, `action=draft` và `published_at`; bảng bài của Admin có thể POST tới `author.posts.publish`.
- Thêm link/sidebar badge quản lý đơn bằng route `admin.author-applications.index` và `$pending_applications_count`.
- Sau khi chỉnh UI, chạy `npm run build` hoặc `npm run dev` để cập nhật asset frontend.
- Form category có thể render select từ `$parentCategories` và gửi `parent_id`; backend đã sẵn sàng dù UI hiện tại chưa có select này.
- Trang `admin.posts.index` có thể thêm hai select dùng `$categories`, `$authors`, giữ query params `category_id`, `author_id`.
- Nút ghim nhanh gửi `PATCH` tới `admin.posts.toggle-featured`; endpoint `admin.posts.update` cũ vẫn được giữ để backward compatibility.
- Footer có thể liên kết tới `feed.index`; từng category có thể liên kết tới `feed.category`.
- `news.index` có thể dùng `$selectedCategory` thay vì query Category trực tiếp trong Blade khi Antigravity chỉnh UI lần tiếp theo.
- Không còn bước backend bắt buộc cho mục 12–14. Frontend hiện tại đã có field/tag manager/editor và URL upload phù hợp với contract mới.
- Nếu chuyển modal Category/Tag sang fetch/AJAX trong tương lai, gửi `Accept: application/json` để nhận JSON và lỗi validation `422`; standard form hiện tại không cần thay đổi.
- Form tác giả hiện có đã tự nhận hai route `author.posts.requests.store` và `author.posts.withdraw`; không cần đổi data contract.
- Tạo `resources/views/admin/post_requests/index.blade.php` dùng `$postRequests`, `$selectedStatus`, `$statuses`, `$breadcrumbs`. Mỗi `PostRequest` đã eager-load `post`, `author`, `handler`; không query thêm trong Blade.
- Thêm mục sidebar trỏ tới `admin.post-requests.index` và hiển thị `$pending_post_requests_count` nếu muốn hiện badge mới. Đây là phần UI còn lại cho Antigravity.

## 7. Kiểm tra đã chạy

- Laravel Pint: passed sau khi tự sửa format.
- Test hẹp sau Mục 15: **26 passed, 122 assertions**.
- Toàn bộ test suite: **159 passed, 669 assertions**.
- Route list: **76 routes**.
- Migration local: passed.

Không còn yêu cầu backend nào trong `BACKEND_REQUESTS.md` bị bỏ ngỏ.

**BACKEND STATUS: READY FOR FRONTEND**
