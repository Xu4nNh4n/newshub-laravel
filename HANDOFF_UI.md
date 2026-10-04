# Bàn giao UI cho Antigravity

Tài liệu này mô tả trạng thái giao diện của project tại ngày 04/10/2026. Phạm vi bàn giao chỉ là UI/presentation. Mọi business logic, dữ liệu, phân quyền và hành vi backend hiện tại phải được giữ nguyên.

## 1. Stack frontend hiện tại

### Công nghệ

- Laravel 13, render phía server bằng Blade.
- Tailwind CSS v4 thông qua `@tailwindcss/vite`.
- Vite 8 dùng để build CSS và theo dõi thay đổi.
- Font chính: Instrument Sans, được cấu hình bởi `laravel-vite-plugin/fonts`.
- Không dùng Bootstrap.
- Không dùng React, Vue, Inertia, Alpine hoặc thư viện component JavaScript.
- Không có entry `resources/js` ở thời điểm bàn giao. JavaScript nhỏ đang được viết inline trong layout.
- Icon trong dashboard là SVG inline; project chưa có icon package riêng.
- Giao diện hiện tại chỉ có dark theme, chủ yếu dùng bảng màu `slate` và màu nhấn `cyan`.

### Điểm vào asset

| Mục | File | Ghi chú |
| --- | --- | --- |
| CSS chính | `resources/css/app.css` | Import Tailwind v4, khai báo `@source` và font token. Chưa có component class/design token riêng ngoài font. |
| Vite | `vite.config.js` | Chỉ build `resources/css/app.css`; không có JS entry. |
| Dependency frontend | `package.json` | Tailwind v4, Vite, Laravel Vite plugin. Không tự ý thêm package khi redesign. |
| Asset build | `public/build/` | File sinh tự động; không sửa trực tiếp. |

### Layout

| Layout | Phạm vi hiện tại |
| --- | --- |
| `resources/views/layouts/app.blade.php` | Layout công khai và cũng đang được hầu hết trang auth, author, profile, admin con sử dụng. Có header công khai, session status, error summary và script chống submit lặp. |
| `resources/views/layouts/dashboard.blade.php` | Layout riêng của trang tổng quan dashboard. Có sidebar trái, topbar sticky, drawer mobile, overlay, skip link và logout. |

### Component/view quan trọng

- `resources/views/components/post-card.blade.php`: card bài viết dùng lại ở trang chủ, danh sách tin, tác giả, yêu thích và bài liên quan. Prop bắt buộc: `post`.
- `resources/views/components/comment-item.blade.php`: hiển thị bình luận, reply/edit/delete/report theo quyền. Props bắt buộc: `comment`, `post`, `reportReasons`.
- `resources/views/dashboard/index.blade.php`: dashboard đang thực sự được dùng bởi `DashboardController`.
- `resources/views/dashboard.blade.php`: bản dashboard cũ, hiện không còn được controller gọi. Xem đây là legacy; không dùng nó làm source of truth.
- `resources/views/sitemap.blade.php`: XML sitemap, không phải UI HTML.
- `resources/views/mail/*`: email template, không thuộc phạm vi redesign website thông thường.

## 2. Cấu trúc giao diện hiện tại

### Public website

- `home.blade.php`: hero tìm kiếm, bài nổi bật, bài mới, chuyên mục, bài xem nhiều và các section theo chuyên mục.
- `news/index.blade.php`: tìm kiếm/lọc/sắp xếp và grid bài viết.
- `news/show.blade.php`: chi tiết bài, lưu bài, tags, bình luận và bài liên quan.
- `news/preview.blade.php`: xem trước bài chưa công khai cho người có quyền.
- `authors/show.blade.php`: hồ sơ tác giả và bài đã xuất bản.
- `favorites/index.blade.php`: danh sách bài đã lưu.

Các trang trên dùng `layouts.app` và `x-post-card`; trang chi tiết còn dùng `x-comment-item`.

### Dashboard

- Route `/dashboard`, `/author` và `/admin` cùng đi qua `DashboardController` rồi render `dashboard.index`.
- Dashboard dùng `layouts.dashboard`, không dùng header công khai.
- Admin được thấy khối thống kê hệ thống và, nếu email đã xác minh, vẫn thấy thêm thống kê bài viết cá nhân.
- User/Author đã xác minh thấy thống kê bài viết của chính họ.
- User chưa xác minh thấy CTA xác minh email và không nhận `authorStats`.
- Dashboard hiện có stats cards, bar chart 6 tháng, progress bars 7 ngày, bài xem nhiều, chuyên mục nổi bật, thống kê tác giả và mô tả workflow xuất bản.

### Sidebar dashboard

- Desktop: fixed bên trái, rộng `w-72`.
- Mobile/tablet nhỏ: ẩn ngoài viewport, mở bằng nút hamburger và overlay; Escape đóng drawer.
- Nhóm Tổng quan: Dashboard.
- Nhóm Nội dung, chỉ khi email đã xác minh: bài viết của tôi, viết bài mới, bài đã lưu.
- Nhóm Quản trị, chỉ với role admin: duyệt bài, tất cả bài, danh mục, thẻ, người dùng, bình luận, nhật ký hoạt động.
- Footer: hồ sơ/cài đặt, về trang tin, đăng xuất.
- Badge số bài chờ duyệt dùng `adminStats.pending_posts` nếu biến này có mặt.

### Topbar dashboard

- Sticky phía trên.
- Có nút mở sidebar trên mobile.
- Breadcrumb/tiêu đề hiện đang hard-code là `Workspace / Tổng quan` và `Dashboard`.
- Có nút viết bài nếu email đã xác minh.
- Có avatar chữ cái đầu, tên và role hiện tại.

### Trang author

- `author/posts/index.blade.php`: filter theo trạng thái, bảng bài của người dùng, xem trước, sửa và gửi duyệt theo policy.
- `author/posts/form.blade.php`: tạo/sửa bản nháp, category, tags, SEO fields, thumbnail và content.
- Các trang này hiện vẫn dùng `layouts.app`, không nằm trong dashboard shell.

### Trang quản trị

- `admin/posts/index.blade.php`: filter, bảng bài viết và hành động feature/hide/archive/restore.
- `admin/posts/review.blade.php`: duyệt, hẹn giờ hoặc từ chối bài.
- `admin/categories/index.blade.php` và `form.blade.php`: CRUD chuyên mục.
- `admin/tags/index.blade.php` và `form.blade.php`: CRUD thẻ.
- `admin/users/index.blade.php`: lọc, đổi role user/author và khóa/mở tài khoản.
- `admin/comments/index.blade.php`: lọc, ẩn/hiện/xóa mềm bình luận.
- `admin/comment-reports/index.blade.php`: lọc trạng thái và xử lý báo cáo.
- `admin/activity-logs/index.blade.php`: lọc và xem nhật ký.

Toàn bộ trang admin con ở trên hiện dùng `layouts.app`, vì vậy sidebar/topbar dashboard biến mất sau khi bấm vào một mục quản trị.

### Auth và profile

- `auth/login`, `register`, `forgot-password`, `reset-password`, `verify-email` dùng các card form đơn giản trong `layouts.app`.
- `profile/edit.blade.php`: cập nhật tên/avatar và đổi mật khẩu.
- `profile/comments.blade.php`: lịch sử bình luận của người dùng.

## 3. Vấn đề UI hiện tại và mức ưu tiên

### Ưu tiên cao: tính nhất quán của admin shell

1. Dashboard có sidebar/topbar riêng nhưng tất cả trang admin/author con quay lại layout công khai. Navigation bị đứt quãng và không giống một admin product hoàn chỉnh.
2. Topbar dashboard đang hard-code tiêu đề/breadcrumb nên chưa tái sử dụng đúng cho trang Bài viết, Người dùng, Danh mục...
3. Chỉ menu Dashboard có active state. Các link admin/author chưa highlight route hiện tại.
4. Sidebar chưa có link trực tiếp tới báo cáo bình luận dù dashboard có CTA đến trang này.
5. Drawer mobile chưa có focus trap và chưa trả focus về nút mở sau khi đóng. Cần giữ Escape và `aria-expanded` nếu refactor.

### Layout và spacing

1. Nhiều Blade page được viết thành các dòng HTML rất dài, khó bảo trì và khó giữ spacing nhất quán.
2. Border radius chủ yếu là `rounded-2xl`/`rounded-xl` ở gần như mọi khối; hierarchy giữa page, section, card và input chưa rõ.
3. `layouts.app` dùng `max-w-6xl`, dashboard dùng `max-w-7xl`; cần có quy tắc container rõ theo loại trang.
4. Một số header chứa title, mô tả và CTA chưa chuyển sang layout dọc đủ sớm trên màn hình nhỏ.
5. Admin dashboard của tài khoản admin có thể rất dài vì hiển thị cả thống kê hệ thống lẫn thống kê tác giả. Có thể cải thiện bằng grouping/tabs/collapse ở tầng UI, nhưng không được bỏ dữ liệu.

### Typography và màu sắc

1. Chưa có semantic design tokens cho surface, border, text-muted, success, warning, danger; class màu bị lặp trực tiếp ở từng file.
2. Heading scale, label scale và muted text chưa hoàn toàn đồng nhất giữa public, auth, author và admin.
3. Nhiều trạng thái hiển thị raw enum bằng tiếng Anh như `pending_review`, `blocked`, `published`. Có thể map nhãn ở tầng view/component, nhưng không được đổi enum value gửi về backend.
4. Branding chưa thống nhất: public layout hard-code `NewsHub`, dashboard dùng `config('app.name')`.
5. Một số text `text-slate-500` nhỏ trên nền `slate-950/900` cần kiểm tra lại contrast thực tế.

### Card, table và list

1. Table admin chủ yếu chỉ được bọc `overflow-x-auto`; trên mobile người dùng phải kéo ngang và action dễ bị dồn.
2. Table chưa có component dùng chung cho header, empty state, status badge và action group.
3. Nút/action ở các bảng không đồng nhất về kích thước, border, hover, focus và thứ bậc primary/destructive.
4. Danh sách comment/report/review dùng card riêng lẻ nhưng bố cục metadata/status/action chưa thống nhất.
5. `post-card` hiện khá cơ bản, thiếu trạng thái hover/focus thống nhất và fallback hình ảnh có chủ ý.
6. Chart dashboard dùng inline percentage và vẫn ép 6 cột trên mobile; cần kiểm tra nhãn/độ rộng ở 375 px. Giá trị chữ hiện phải được giữ để chart không phụ thuộc màu duy nhất.

### Form và feedback

1. Error summary nằm ở layout nhưng hầu hết input chưa hiển thị lỗi sát field và chưa có `aria-describedby`.
2. Focus style không đồng nhất: nhiều input chỉ đổi border; nhiều button/link không có `focus-visible` rõ.
3. Một số filter input dựa chủ yếu vào placeholder, thiếu label hiển thị hoặc label dành cho screen reader.
4. Form duyệt/từ chối bài dùng hàng `flex` có khả năng chật/overflow trên mobile.
5. Script chống submit lặp chỉ tồn tại trong `layouts.app`; `layouts.dashboard` không có hành vi tương đương ngoài form logout.
6. Confirm xóa đang dùng `window.confirm`; có thể giữ nguyên hoặc cải thiện presentation, nhưng không được bỏ bước xác nhận.

### Responsive và navigation công khai

1. Header công khai ẩn Trang chủ, Tin tức, Bài của tôi, Hồ sơ ở mobile nhưng không có menu mobile thay thế.
2. Dashboard mobile drawer hoạt động, nhưng cần kiểm tra 375 px, tablet portrait/landscape, nội dung dài và trạng thái body scroll.
3. Bảng, action group, tag checkbox và comment reply form cần kiểm tra khả năng wrap ở màn hình nhỏ.
4. Cần tránh horizontal scroll ở toàn trang; nếu table cần scroll thì chỉ container table được scroll.

### Phần cần redesign mạnh nhất

1. Đồng bộ dashboard shell cho toàn bộ khu vực admin/author.
2. Chuẩn hóa navigation, active state, page title và breadcrumb.
3. Chuẩn hóa table/list/filter/form trên admin mobile và desktop.
4. Tạo hệ thống component/tokens dùng chung cho button, badge, card, field, page header và empty state.
5. Cải thiện header/navigation mobile của public site.
6. Refactor markup Blade dài thành component/partial dễ bảo trì mà không đổi contract backend.

## 4. File Antigravity được phép sửa

Antigravity được phép sửa presentation trong các phạm vi sau:

- `resources/views/layouts/app.blade.php`
- `resources/views/layouts/dashboard.blade.php`
- `resources/views/dashboard/index.blade.php`
- `resources/views/admin/**/*.blade.php`
- `resources/views/author/**/*.blade.php`
- `resources/views/auth/**/*.blade.php`
- `resources/views/profile/**/*.blade.php`
- `resources/views/home.blade.php`
- `resources/views/news/**/*.blade.php`
- `resources/views/authors/**/*.blade.php`
- `resources/views/favorites/**/*.blade.php`
- `resources/views/errors/**/*.blade.php`
- `resources/views/components/**/*.blade.php`
- `resources/css/app.css`
- Có thể tạo thêm Blade component/partial bên trong `resources/views/components/` hoặc thư mục view hiện có.
- Có thể tạo `resources/js/app.js` hoặc file JS nhỏ cho tương tác thuần UI nếu thật sự cần, nhưng phải xin phép trước khi đổi `vite.config.js` và không được thêm dependency.
- Có thể thêm asset hình ảnh/icon tĩnh trong một thư mục con rõ ràng của `public/`; không ghi đè file upload/runtime trong `public/storage`.
- `resources/views/dashboard.blade.php` là legacy không được dùng. Có thể xóa sau khi xác nhận không còn reference và toàn bộ test pass; không tiếp tục phát triển song song hai dashboard.

Trong các file được phép, Antigravity chỉ được thay đổi:

- HTML semantic và cấu trúc trình bày.
- Tailwind utility classes/CSS presentation.
- SVG/icon mang tính trình bày.
- ARIA, focus state, responsive behavior và interaction UI không làm đổi nghiệp vụ.
- Tách/ghép component Blade khi giữ nguyên props, form contract, route và permission condition.

## 5. File/khu vực không được sửa

Không được sửa nếu chưa có chấp thuận riêng:

- `app/Http/Controllers/**`
- `app/Http/Requests/**`
- `app/Models/**`
- `app/Policies/**`
- `app/Services/**`
- `app/Enums/**`
- `app/Providers/**`
- `routes/**`
- `database/**`
- `config/**`
- `bootstrap/**`
- `storage/**`
- `tests/**` chỉ để làm test pass bằng cách hạ assertion hoặc đổi expectation.
- `resources/views/sitemap.blade.php` vì đây là XML contract cho crawler, không phải UI.
- `resources/views/mail/**` trừ khi có yêu cầu riêng về email UI.
- `composer.json`, `composer.lock`, `package.json`, `package-lock.json`, `vite.config.js` nếu chưa được cho phép.
- `.env` và mọi secret/config môi trường.
- `public/build/**` vì đây là output sinh tự động.
- `public/storage/**` và file người dùng upload.

Các khu vực logic tuyệt đối không được thay đổi trong lúc redesign:

- Business logic và workflow bài viết.
- Controller/view-model logic.
- Model, relation, cast, scope và query.
- Database schema, migration, seeder hoặc dữ liệu.
- Authentication, email verification và password reset.
- Middleware, authorization, policy, Gate và role checks.
- API/HTTP method, redirect, session message và response behavior.
- Route URL, route name và route-model binding.
- Enum value/action value gửi lên backend.
- Logic bài công khai, ghi lượt xem, favorite, comment, report và moderation.

Nếu UI mới cần thêm dữ liệu chưa có trong contract, dừng lại và yêu cầu backend bổ sung; không query model trực tiếp trong Blade.

## 6. Data contract hiện tại

### Dữ liệu dùng chung từ layout/framework

- `$title`, `$metaDescription`, `$ogImage`: metadata tùy chọn của `layouts.app`.
- `auth()->user()`: user đăng nhập; các field UI đang dùng gồm `name`, `role`, `status`, `email_verified_at` thông qua `hasVerifiedEmail()`.
- `session('status')`: thông báo thành công.
- `$errors`: validation/error bag.
- `old(...)`: dữ liệu form cũ sau validation failure.
- Pagination objects phải tiếp tục render `->links()` và giữ query string do backend đã cấu hình.

### Dashboard: `dashboard/index.blade.php`

| Biến | Kiểu/shape | Điều kiện |
| --- | --- | --- |
| `adminStats` | `array|null` | Chỉ khác `null` khi role là Admin. |
| `authorStats` | `array|null` | Khác `null` khi user đã xác minh email; áp dụng cả Admin. |

`adminStats` có các key bắt buộc:

- `users_total`: số user thường.
- `authors_total`: số author.
- `blocked_total`: số tài khoản bị khóa.
- `posts_total`: tổng số bài.
- `pending_posts`: số bài chờ duyệt.
- `published_total`: số bài có status published.
- `published_visible`: số bài hiện công khai theo scope backend.
- `pending_reports`: số báo cáo bình luận chờ xử lý.
- `top_posts`: collection tối đa 5 Post, có `id`, `author_id`, `category_id`, `title`, `slug`, `view_count`, relation `author(id,name)` và `category(id,name)`.
- `posts_by_month`: list 6 phần tử `{label: string m/Y, count: int}`.
- `views_by_day`: list 7 phần tử `{label: string d/m, count: int}`.
- `top_categories`: collection tối đa 5 Category, có `id`, `name`, `published_posts_count`, `published_views_sum`.

`authorStats` có các key bắt buộc:

- `posts_total`, `drafts`, `pending_posts`, `rejected_posts`, `published_posts`, `total_views`.
- `top_posts`: collection tối đa 5 Post của chính user, có `id`, `category_id`, `title`, `slug`, `status`, `view_count` và relation `category(id,name)`.

Không đổi tên/xóa key và không giả định `adminStats` hay `authorStats` luôn tồn tại.

### Public views

| View | Biến backend truyền vào |
| --- | --- |
| `home` | `featuredPosts`, `latestPosts`, `popularPosts`, `categories`, `categorySections` (mỗi category section đã eager-load tối đa 3 posts). |
| `news.index` | `posts` paginator, `filters` gồm các key tùy chọn `q`, `category`, `tag`, `sort`. |
| `news.show` | `post`, `relatedPosts`, `comments` paginator, `reportReasons`, `isFavorited`, `thumbnailUrl`, `openGraphImageUrl`, `metaDescription`. |
| `news.preview` | `post`, `thumbnailUrl`. |
| `authors.show` | `author`, `avatarUrl`, `posts` paginator. |
| `favorites.index` | `favorites` paginator; mỗi favorite đã load `post.author` và `post.category`. |
| `profile.edit` | `avatarUrl`; user lấy từ auth context. |
| `profile.comments` | `comments` paginator; có thể chứa comment/post đã soft-delete. |

`x-post-card` yêu cầu Post có ít nhất: `thumbnail`, `title`, `slug`, `summary`, `published_at`, relation `category(name,slug)` và relation `author(name)` có thể nullable.

`x-comment-item` yêu cầu:

- `comment`: có `user`, `replyTo.user`, status, timestamps và soft-delete state.
- `post`: Post dùng cho route tạo reply.
- `reportReasons`: danh sách enum có `value` và `label()`.
- Component phải giữ nguyên các `@can('update'|'delete'|'report', $comment)` và điều kiện email verified.

### Author views

| View | Biến backend truyền vào |
| --- | --- |
| `author.posts.index` | `posts` paginator của chính user, `filters.status`, `statuses` là `PostStatus::cases()`. |
| `author.posts.form` create | `categories`, `tags`; không có `post`. |
| `author.posts.form` edit | `categories`, `tags`, `post` đã load `tags`. |

Không đổi tên form field: `title`, `meta_title`, `slug`, `category_id`, `summary`, `meta_description`, `thumbnail`, `remove_thumbnail`, `content`, `tag_ids[]`.

### Admin views

| View | Biến backend truyền vào |
| --- | --- |
| `admin.posts.index` | `posts` paginator, `filters` (`q`, `status` tùy chọn), `statuses`. |
| `admin.posts.review` | `posts` paginator chỉ gồm bài `pending_review`, đã load `author` và `category`. |
| `admin.categories.index` | `categories` paginator, mỗi item có `posts_count`. |
| `admin.categories.form` | Create: không có `category`; edit: có `category`. |
| `admin.tags.index` | `tags` paginator, mỗi item có `posts_count`. |
| `admin.tags.form` | Create: không có `tag`; edit: có `tag`. |
| `admin.users.index` | `users` paginator không gồm admin, mỗi item có `posts_count`, `comments_count`; `filters` (`q`, `role`, `status`). |
| `admin.comments.index` | `comments` paginator gồm soft-deleted, `filters` (`q`, `state`), `states` map value => label. |
| `admin.comment-reports.index` | `reports` paginator, `selectedStatus`, `statuses`; report đã load reporter và comment/user nếu còn tồn tại. |
| `admin.activity-logs.index` | `logs` paginator, `actions` collection, `actors` collection, `filters` (`action`, `user_id`). |

Các action value phải giữ nguyên:

- Post moderation: `feature`, `unfeature`, `hide`, `archive`, `restore`.
- Comment moderation: `hide`, `restore`, `delete`.
- Comment report: dùng chính value của `CommentReportAction` cho dismiss/hide/delete.
- User access: role `user|author`, status `active|blocked`.
- Review approve: `published_at`; reject: `reason`.

### Auth views

- Login fields: `email`, `password`, `remember`.
- Register fields: `name`, `email`, `password`, `password_confirmation`.
- Forgot password: `email`.
- Reset password nhận `$token`, `$email`; fields gửi đi: `token`, `email`, `password`, `password_confirmation`.
- Verify email không có biến riêng; form POST tới route gửi lại verification.

## 7. Chức năng bắt buộc giữ nguyên

1. Guest xem trang chủ, danh sách tin, chi tiết tin và hồ sơ tác giả công khai.
2. Tìm kiếm/lọc/sắp xếp tin theo query hiện tại và giữ query khi phân trang.
3. SEO title, meta description và Open Graph image của bài chi tiết.
4. Login, logout, register, remember-me, reset password và verify email.
5. Blocked user bị đăng xuất/chặn bởi backend.
6. Dashboard hiển thị đúng dữ liệu theo role và trạng thái email verified.
7. User đã xác minh có thể tạo/sửa bản nháp, xem trước và gửi duyệt theo policy.
8. Chỉ action được policy cho phép mới xuất hiện; giữ nguyên `@can`, `@auth`, `@guest` và verified checks.
9. Admin duyệt/hẹn giờ/từ chối bài và quản trị trạng thái bài viết.
10. Admin CRUD category/tag, quản lý user, comment, report và activity log.
11. Favorite add/remove; chỉ hiển thị bài vẫn công khai.
12. Comment create/reply/edit/delete/report; giữ soft-delete/hidden presentation.
13. Upload/remove thumbnail và upload avatar; giữ `multipart/form-data`, `accept` và field names.
14. CSRF token, method spoofing (`PUT`, `PATCH`, `DELETE`) và HTTP method của mọi form.
15. Confirmation trước destructive action hiện có.
16. Session success message, validation errors, empty states và pagination.
17. Nội dung người dùng phải tiếp tục được escape bằng `{{ ... }}`; không đổi sang `{!! ... !!}`.
18. Route phải tiếp tục được tạo bằng route name hiện tại; không hard-code URL.

## 8. Checklist sau khi sửa UI

### Build và compile

```powershell
npm run build
php artisan view:cache
vendor/bin/pint --dirty --format agent
php artisan test --compact
```

Nếu chỉ chạy development:

```powershell
composer run dev
```

Không chạy `npm install` hoặc thay dependency nếu chưa được chủ project đồng ý, đặc biệt khi thao tác có thể tải dữ liệu lớn.

### Responsive

- Kiểm tra tối thiểu ở 375 px, 768 px, 1024 px và 1440 px.
- Không có horizontal scroll toàn trang.
- Sidebar/drawer mở, đóng bằng overlay, nút close và Escape; body scroll được phục hồi.
- Focus không bị topbar/sidebar che và focus quay lại trigger hợp lý.
- Table/action/form không bị cắt; nếu dùng mobile card view phải giữ đủ dữ liệu và action.
- Kiểm tra text dài, email dài, title dài, empty state và paginator.

### Navigation và permission-dependent UI

- Guest không thấy link/action yêu cầu đăng nhập.
- User chưa xác minh không thấy action viết bài/favorite/comment bị giới hạn; CTA xác minh vẫn hoạt động.
- User và Author không thấy menu/action admin.
- Admin thấy đầy đủ menu admin và badge chờ duyệt nếu có dữ liệu.
- Active state đúng cho từng route con, không chỉ Dashboard.
- Mọi link vẫn dùng route name hiện có và không dẫn tới 403 ngoài ý muốn.

### Form

- Giữ nguyên `name`, `value`, hidden inputs, CSRF, method spoofing và `enctype`.
- `old()` vẫn khôi phục dữ liệu sau validation failure.
- Validation error hiển thị rõ; không được che error summary hiện có nếu chưa có giải pháp tương đương.
- Submit button không gửi hai lần và có trạng thái loading hợp lý.
- Destructive action vẫn có confirmation.
- Keyboard, focus-visible, label, autocomplete và touch target được kiểm tra.

### Table/list/card

- Dữ liệu, status, counts, timestamps và action không bị mất.
- Empty state và pagination vẫn xuất hiện.
- Link xem trước, sửa, duyệt, ẩn, khôi phục, lưu trữ, nổi bật và xóa đúng route/method.
- User-generated text vẫn escape, bao gồm title, comment, report description và activity description.

### Regression tests quan trọng

- `tests/Feature/DashboardStatisticsTest.php` phụ thuộc các nội dung/giá trị dashboard hiện tại.
- `tests/Feature/AuthorizationMiddlewareTest.php` kiểm tra access của guest, user, author, admin, unverified và blocked user.
- Các test public news, favorite, comment, moderation, profile và error page có assertion về nội dung escaped và dữ liệu không được lộ.
- Không sửa test để che regression UI/permission; nếu copy text bắt buộc thay đổi, trao đổi trước vì một số test dùng `assertSee`.

## Nguyên tắc bàn giao cuối cùng

Antigravity có thể thay đổi mạnh presentation, layout và component architecture, nhưng backend hiện tại là nguồn sự thật. Redesign thành công khi giao diện nhất quán hơn mà toàn bộ route, form contract, dữ liệu, permission, security escaping và test hiện tại vẫn giữ nguyên.
