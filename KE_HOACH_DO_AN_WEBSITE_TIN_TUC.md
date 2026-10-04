# KẾ HOẠCH XÂY DỰNG WEBSITE TIN TỨC

## 1. Thông tin đề tài

### 1.1. Tên đề tài

**Xây dựng hệ thống quản lý và cung cấp tin tức trực tuyến**

### 1.2. Mô tả ngắn

Đề tài xây dựng một website tin tức cho phép khách truy cập đọc và tìm kiếm bài viết; người dùng đăng ký tài khoản, bình luận và lưu bài yêu thích; tác giả soạn bài và gửi duyệt; quản trị viên kiểm duyệt, xuất bản và quản lý toàn bộ nội dung của hệ thống.

Hệ thống hướng đến quy trình vận hành thực tế của một website tin tức ở quy mô nhỏ hoặc vừa. Trọng tâm của đồ án không chỉ là các chức năng thêm, sửa, xóa dữ liệu mà còn bao gồm phân quyền, quy trình duyệt bài, kiểm soát bình luận, tìm kiếm, thống kê, bảo mật, kiểm thử và triển khai.

### 1.3. Mục tiêu

- Xây dựng website tin tức có giao diện rõ ràng và tương thích với máy tính, máy tính bảng và điện thoại.
- Xây dựng quy trình tạo, gửi duyệt, từ chối và xuất bản bài viết.
- Phân quyền rõ ràng giữa người đọc, người viết và quản trị viên.
- Cho phép người dùng bình luận và trả lời bình luận theo mô hình hai cấp.
- Hỗ trợ tìm kiếm, phân loại và gợi ý bài viết liên quan.
- Cung cấp trang quản trị và số liệu thống kê cơ bản.
- Đảm bảo các yêu cầu bảo mật phổ biến của một ứng dụng web.
- Có kiểm thử, tài liệu và phiên bản triển khai thực tế để phục vụ bảo vệ đồ án.

## 2. Phạm vi đề tài

### 2.1. Phạm vi thực hiện

Hệ thống gồm các nhóm chức năng chính:

1. Quản lý tài khoản và xác thực.
2. Phân quyền người dùng.
3. Quản lý chuyên mục và thẻ.
4. Quản lý bài viết.
5. Quy trình gửi và duyệt bài.
6. Hiển thị và tìm kiếm tin tức.
7. Bình luận hai cấp.
8. Báo cáo và quản lý bình luận.
9. Lưu bài viết yêu thích.
10. Ghi nhận lượt xem và thống kê.
11. Ghi nhật ký các thao tác quản trị quan trọng.

### 2.2. Ngoài phạm vi phiên bản đầu

Các chức năng sau chưa cần triển khai trong phiên bản đồ án đầu tiên:

- Ứng dụng di động riêng.
- Chat hoặc thông báo thời gian thực.
- Livestream.
- Hệ thống quảng cáo tự động.
- Thu phí và đăng ký gói thành viên.
- Kiến trúc microservice.
- Elasticsearch hoặc một hệ thống tìm kiếm phân tán.
- Trí tuệ nhân tạo tự động viết bài.
- Hệ thống gợi ý sử dụng machine learning phức tạp.

Những chức năng trên có thể được trình bày trong phần hướng phát triển nhưng không nên làm ảnh hưởng đến tiến độ của các chức năng cốt lõi.

## 3. Đối tượng sử dụng và phân quyền

### 3.1. Guest — Khách truy cập

Guest là người chưa đăng nhập. Guest không cần được lưu thành một vai trò trong cơ sở dữ liệu.

Guest được phép:

- Xem trang chủ.
- Xem các bài viết đã xuất bản.
- Xem bài viết theo chuyên mục hoặc thẻ.
- Tìm kiếm bài viết.
- Đọc bình luận.
- Xem hồ sơ công khai của tác giả.
- Đăng ký và đăng nhập.

Guest không được phép:

- Viết bình luận.
- Trả lời hoặc báo cáo bình luận.
- Lưu bài yêu thích.
- Truy cập khu vực của Author hoặc Admin.

### 3.2. User — Người dùng

User có tất cả quyền của Guest và được phép:

- Quản lý hồ sơ cá nhân.
- Đổi mật khẩu.
- Bình luận vào bài viết.
- Trả lời bình luận.
- Sửa hoặc xóa mềm bình luận của chính mình.
- Báo cáo bình luận vi phạm.
- Lưu hoặc bỏ lưu bài viết yêu thích.
- Xem danh sách bài đã lưu.
- Xem danh sách bình luận của mình.

User không được phép:

- Quản lý chuyên mục hoặc thẻ.
- Đăng hoặc duyệt bài viết.
- Sửa, ẩn hoặc xóa bình luận của người khác.
- Truy cập trang quản trị.

### 3.3. Author — Tác giả

Author có tất cả quyền của User và được phép:

- Tạo bài viết mới.
- Lưu bài dưới dạng bản nháp.
- Xem trước bài chưa công khai.
- Sửa bài viết do mình tạo khi bài còn là bản nháp hoặc bị từ chối.
- Gửi bài cho Admin duyệt.
- Xem trạng thái xử lý của bài viết.
- Xem lý do bài bị từ chối.
- Chỉnh sửa và gửi duyệt lại.
- Xem thống kê cơ bản của các bài do mình viết.

Author không được phép:

- Tự xuất bản bài viết.
- Duyệt bài của bản thân hoặc của Author khác.
- Sửa bài của Author khác.
- Thay đổi vai trò người dùng.
- Truy cập các chức năng quản trị không được cấp quyền.

### 3.4. Admin — Quản trị viên/Biên tập viên

Admin có quyền:

- Quản lý tài khoản User và Author.
- Khóa hoặc mở khóa tài khoản.
- Cấp hoặc thu hồi vai trò Author.
- Quản lý chuyên mục và thẻ.
- Tạo và quản lý bài viết.
- Duyệt hoặc từ chối bài do Author gửi.
- Nhập lý do từ chối bài.
- Xuất bản, hẹn giờ xuất bản, ẩn hoặc lưu trữ bài viết.
- Đặt hoặc gỡ trạng thái bài nổi bật.
- Ẩn hoặc xóa mềm bất kỳ bình luận nào.
- Xử lý báo cáo bình luận.
- Xem dashboard thống kê.
- Xem nhật ký hoạt động quản trị.

## 4. Yêu cầu chức năng

### 4.1. Xác thực và quản lý tài khoản

Hệ thống cần hỗ trợ:

- Đăng ký bằng tên, email và mật khẩu.
- Không cho phép hai tài khoản sử dụng cùng một email.
- Xác minh địa chỉ email.
- Đăng nhập và đăng xuất.
- Gửi yêu cầu đặt lại mật khẩu.
- Đổi mật khẩu sau khi đăng nhập.
- Cập nhật tên và ảnh đại diện.
- Khóa tài khoản vi phạm.
- Kiểm tra trạng thái tài khoản trong mỗi phiên đăng nhập.
- Phân quyền bằng middleware và policy ở phía máy chủ.

Không được chỉ ẩn nút trên giao diện để phân quyền. Mọi request quan trọng đều phải được kiểm tra quyền ở backend.

### 4.2. Quản lý chuyên mục

Mỗi bài viết thuộc một chuyên mục chính.

Admin được phép:

- Thêm chuyên mục.
- Sửa tên, slug và mô tả chuyên mục.
- Ẩn chuyên mục.
- Xóa mềm chuyên mục phù hợp với quy tắc nghiệp vụ.

Quy tắc đề xuất:

- Tên chuyên mục không được để trống.
- Slug chuyên mục phải duy nhất.
- Không xóa chuyên mục đang có bài viết nếu chưa chuyển các bài sang chuyên mục khác.

Ví dụ chuyên mục:

- Công nghệ.
- Giáo dục.
- Kinh tế.
- Thể thao.
- Đời sống.

### 4.3. Quản lý thẻ

Một bài viết có thể có nhiều thẻ và một thẻ có thể thuộc nhiều bài viết.

Admin được phép:

- Tạo thẻ.
- Sửa thẻ.
- Xóa thẻ.
- Gắn hoặc gỡ thẻ khỏi bài viết.

Ví dụ: bài thuộc chuyên mục `Công nghệ` có thể có các thẻ `AI`, `Open Source` và `Laravel`.

### 4.4. Quản lý bài viết

Thông tin chính của một bài viết gồm:

- Tiêu đề.
- Slug duy nhất.
- Nội dung tóm tắt.
- Nội dung đầy đủ.
- Ảnh đại diện.
- Tác giả.
- Chuyên mục.
- Danh sách thẻ.
- Trạng thái bài viết.
- Trạng thái bài nổi bật.
- Thời điểm xuất bản.
- Lượt xem.
- Thời điểm tạo và cập nhật.

Chức năng cần có:

- Tạo bài viết.
- Lưu bản nháp.
- Chỉnh sửa bài viết.
- Xóa mềm bài viết.
- Xem trước bài chưa xuất bản.
- Gửi bài để duyệt.
- Duyệt hoặc từ chối bài.
- Nhập lý do từ chối.
- Gửi duyệt lại sau khi chỉnh sửa.
- Xuất bản ngay hoặc hẹn giờ xuất bản.
- Ẩn hoặc lưu trữ bài đã xuất bản.
- Đặt bài nổi bật.

### 4.5. Quy trình duyệt bài

Luồng xử lý chính:

```text
Author tạo bài
      |
      v
    draft
      |
      | Gửi duyệt
      v
pending_review
      |
      +-------------------------+
      |                         |
      | Admin duyệt             | Admin từ chối
      v                         v
  published                  rejected
                                |
                                | Author chỉnh sửa
                                v
                         pending_review
```

Các trạng thái bài viết:

| Trạng thái | Ý nghĩa |
|---|---|
| `draft` | Bản nháp, chưa gửi duyệt |
| `pending_review` | Đang chờ Admin kiểm tra |
| `published` | Đã được xuất bản công khai |
| `rejected` | Bị từ chối và cần chỉnh sửa |
| `hidden` | Đã bị ẩn khỏi giao diện công khai |
| `archived` | Được lưu trữ và không còn hiển thị như tin đang hoạt động |

Quy tắc nghiệp vụ:

- Author chỉ được chỉnh sửa bài của mình.
- Author không được tự chuyển bài sang `published`.
- Khi từ chối bài, Admin phải nhập lý do.
- Chỉ bài `published` và đã đến thời điểm xuất bản mới xuất hiện công khai.
- Nếu chỉnh sửa lớn một bài đã xuất bản, hệ thống có thể đưa bài về trạng thái chờ duyệt. Đây là chức năng mở rộng nếu còn thời gian.

### 4.6. Hiển thị tin tức

Trang chủ dự kiến gồm:

- Thanh điều hướng và danh sách chuyên mục.
- Khu vực bài nổi bật.
- Danh sách bài mới nhất.
- Các nhóm bài theo chuyên mục.
- Danh sách bài được xem nhiều.
- Thanh tìm kiếm.

Trang chi tiết bài viết gồm:

- Tiêu đề.
- Tác giả và ngày xuất bản.
- Chuyên mục và thẻ.
- Ảnh đại diện.
- Nội dung bài viết.
- Lượt xem.
- Nút lưu bài yêu thích.
- Danh sách bài liên quan.
- Khu vực bình luận.

### 4.7. Tìm kiếm và lọc

Hệ thống cần hỗ trợ:

- Tìm theo tiêu đề.
- Tìm theo nội dung tóm tắt và nội dung bài.
- Lọc theo chuyên mục.
- Lọc theo thẻ.
- Sắp xếp theo mới nhất hoặc xem nhiều nhất.
- Phân trang kết quả.
- Không hiển thị bài nháp, bài bị từ chối, bài bị ẩn hoặc bài chưa đến giờ xuất bản.

Với quy mô đồ án, có thể sử dụng full-text search của MySQL hoặc tìm kiếm bằng truy vấn đã được tối ưu. Không cần triển khai Elasticsearch.

## 5. Thiết kế hệ thống bình luận

### 5.1. Nguyên tắc chung

- User đã đăng nhập được phép bình luận.
- Bình luận được hiển thị ngay sau khi đăng thành công.
- Bình luận không cần Admin duyệt trước.
- User được sửa hoặc xóa mềm bình luận của mình.
- Admin được ẩn hoặc xóa mềm mọi bình luận.
- User được báo cáo bình luận vi phạm.
- Hệ thống giới hạn tần suất gửi bình luận để chống spam.
- Nội dung phải được kiểm tra và làm sạch để chống XSS.

### 5.2. Mô hình bình luận hai cấp

Giao diện chỉ hiển thị tối đa hai cấp:

```text
Nguyễn Văn A: Nội dung bài rất hay.
├── Trần Văn B: @Nguyễn Văn A Tôi đồng ý.
└── Lê Văn C: @Trần Văn B Nhưng số liệu này chưa chính xác.
```

Trong ví dụ trên, Lê Văn C đang trả lời Trần Văn B nhưng phản hồi vẫn nằm cùng cấp hiển thị với phản hồi của Trần Văn B. Thiết kế này giữ được ngữ cảnh hội thoại mà không tạo ra cây bình luận lồng quá sâu.

### 5.3. Quy tắc lưu quan hệ bình luận

Một bình luận sử dụng hai trường quan hệ:

- `parent_id`: ID của bình luận gốc mà phản hồi trực thuộc.
- `reply_to_id`: ID chính xác của bình luận đang được trả lời.

Quy tắc:

| Trường hợp | `parent_id` | `reply_to_id` |
|---|---:|---:|
| Bình luận gốc | `null` | `null` |
| Trả lời bình luận gốc | ID bình luận gốc | ID bình luận gốc |
| Trả lời một câu trả lời | ID bình luận gốc | ID câu trả lời được phản hồi |

Không cần lưu riêng `reply_to_user_id`, vì có thể xác định người được nhắc đến thông qua `reply_to_id` và quan hệ với bảng `users`.

### 5.4. Xóa bình luận

Hệ thống sử dụng xóa mềm để giữ nguyên cấu trúc hội thoại.

Nếu một bình luận đã có câu trả lời bị xóa, hệ thống hiển thị:

```text
[Bình luận này đã bị xóa]
├── Các câu trả lời vẫn được giữ lại
```

Chỉ nên xóa vĩnh viễn khi Admin có lý do phù hợp và dữ liệu không còn cần thiết cho việc kiểm tra hoặc xử lý báo cáo.

### 5.5. Phân trang và tải phản hồi

- Chỉ phân trang các bình luận gốc.
- Các câu trả lời được tải theo từng bình luận gốc.
- Có thể hiển thị nút `Xem câu trả lời` nếu một bình luận có nhiều phản hồi.
- Cần sắp xếp phản hồi theo thời gian để người đọc dễ theo dõi hội thoại.

### 5.6. Báo cáo bình luận

User có thể báo cáo một bình luận với các lý do:

- Spam.
- Ngôn từ xúc phạm.
- Thông tin sai lệch.
- Nội dung không liên quan.
- Nội dung nguy hiểm hoặc vi phạm pháp luật.
- Lý do khác.

Trạng thái báo cáo:

- `pending`: chưa xử lý.
- `resolved`: đã xử lý.
- `dismissed`: không phát hiện vi phạm.

Admin có thể xem nội dung, người báo cáo, lý do, thời gian báo cáo và đưa ra quyết định giữ nguyên, ẩn hoặc xóa mềm bình luận.

### 5.7. Chống spam

Các biện pháp tối thiểu:

- Rate limit theo tài khoản và địa chỉ IP.
- Không cho gửi nội dung rỗng.
- Giới hạn độ dài bình luận.
- Ngăn gửi liên tiếp cùng một nội dung.
- Escape dữ liệu khi hiển thị.
- Có thể bổ sung CAPTCHA nếu hệ thống phát hiện hành vi bất thường.

## 6. Bài viết yêu thích và bài liên quan

### 6.1. Bài viết yêu thích

User được phép:

- Lưu một bài viết.
- Bỏ lưu bài viết.
- Xem danh sách bài đã lưu.

Mỗi User chỉ được lưu một bài một lần. Cần đặt ràng buộc duy nhất trên cặp `user_id` và `post_id`.

### 6.2. Bài viết liên quan

Phiên bản đầu sử dụng phương pháp tính điểm đơn giản:

- Cùng chuyên mục: cộng điểm ưu tiên cao.
- Có thẻ trùng nhau: cộng điểm theo số thẻ trùng.
- Ưu tiên bài mới hơn nếu có cùng số điểm.
- Không gợi ý chính bài đang xem.
- Chỉ lấy bài đang được xuất bản.

Phương án này dễ giải thích, dễ kiểm thử và không yêu cầu machine learning.

## 7. Dashboard và thống kê

Dashboard dành cho Admin gồm:

- Tổng số tài khoản.
- Tổng số User và Author.
- Số tài khoản đang bị khóa.
- Tổng số bài viết.
- Số bài đang chờ duyệt.
- Số bài đã xuất bản.
- Số báo cáo bình luận chưa xử lý.
- Các bài được xem nhiều nhất.
- Thống kê bài viết theo tháng.
- Thống kê lượt xem theo ngày hoặc tháng.
- Chuyên mục có nhiều bài hoặc lượt xem nhất.

Author có thể xem:

- Số bài đã viết.
- Số bài đang chờ duyệt.
- Số bài đã xuất bản.
- Tổng lượt xem các bài của mình.
- Danh sách bài có lượt xem cao nhất.

Biểu đồ có thể được xây dựng bằng Chart.js.

## 8. Thiết kế cơ sở dữ liệu dự kiến

### 8.1. Danh sách bảng

```text
users
categories
tags
posts
post_tag
comments
comment_reports
favorites
post_views
activity_logs
password_reset_tokens
```

Các bảng mặc định phục vụ session, queue hoặc cache của framework có thể được bổ sung khi triển khai.

### 8.2. Bảng `users`

| Cột | Ý nghĩa |
|---|---|
| `id` | Khóa chính |
| `name` | Tên hiển thị |
| `email` | Email duy nhất |
| `password` | Mật khẩu đã hash |
| `avatar` | Đường dẫn ảnh đại diện |
| `role` | `user`, `author` hoặc `admin` |
| `status` | `active` hoặc `blocked` |
| `email_verified_at` | Thời gian xác minh email |
| `created_at` | Thời gian tạo |
| `updated_at` | Thời gian cập nhật |

### 8.3. Bảng `categories`

| Cột | Ý nghĩa |
|---|---|
| `id` | Khóa chính |
| `name` | Tên chuyên mục |
| `slug` | Đường dẫn duy nhất |
| `description` | Mô tả |
| `status` | Trạng thái hiển thị |
| `created_at` | Thời gian tạo |
| `updated_at` | Thời gian cập nhật |
| `deleted_at` | Hỗ trợ xóa mềm |

### 8.4. Bảng `tags`

| Cột | Ý nghĩa |
|---|---|
| `id` | Khóa chính |
| `name` | Tên thẻ |
| `slug` | Đường dẫn duy nhất |
| `created_at` | Thời gian tạo |
| `updated_at` | Thời gian cập nhật |

### 8.5. Bảng `posts`

| Cột | Ý nghĩa |
|---|---|
| `id` | Khóa chính |
| `author_id` | Người tạo bài |
| `category_id` | Chuyên mục chính |
| `title` | Tiêu đề |
| `slug` | Đường dẫn duy nhất |
| `summary` | Nội dung tóm tắt |
| `content` | Nội dung đầy đủ |
| `thumbnail` | Ảnh đại diện |
| `status` | Trạng thái bài |
| `rejection_reason` | Lý do từ chối |
| `is_featured` | Đánh dấu bài nổi bật |
| `published_at` | Thời điểm xuất bản |
| `created_at` | Thời gian tạo |
| `updated_at` | Thời gian cập nhật |
| `deleted_at` | Hỗ trợ xóa mềm |

### 8.6. Bảng `post_tag`

| Cột | Ý nghĩa |
|---|---|
| `post_id` | ID bài viết |
| `tag_id` | ID thẻ |

Cặp `post_id` và `tag_id` phải là duy nhất.

### 8.7. Bảng `comments`

| Cột | Ý nghĩa |
|---|---|
| `id` | Khóa chính |
| `post_id` | Bài viết được bình luận |
| `user_id` | Người viết bình luận |
| `parent_id` | Bình luận gốc của chuỗi phản hồi |
| `reply_to_id` | Bình luận cụ thể đang được trả lời |
| `content` | Nội dung |
| `status` | `visible` hoặc `hidden` |
| `created_at` | Thời gian tạo |
| `updated_at` | Thời gian cập nhật |
| `deleted_at` | Hỗ trợ xóa mềm |

### 8.8. Bảng `comment_reports`

| Cột | Ý nghĩa |
|---|---|
| `id` | Khóa chính |
| `comment_id` | Bình luận bị báo cáo |
| `reporter_id` | Người báo cáo |
| `reason` | Nhóm lý do |
| `description` | Mô tả bổ sung |
| `status` | Trạng thái xử lý |
| `handled_by` | Admin xử lý |
| `handled_at` | Thời điểm xử lý |
| `created_at` | Thời gian tạo |
| `updated_at` | Thời gian cập nhật |

### 8.9. Bảng `favorites`

| Cột | Ý nghĩa |
|---|---|
| `id` | Khóa chính |
| `user_id` | Người lưu bài |
| `post_id` | Bài được lưu |
| `created_at` | Thời gian lưu |

Cặp `user_id` và `post_id` phải là duy nhất.

### 8.10. Bảng `post_views`

| Cột | Ý nghĩa |
|---|---|
| `id` | Khóa chính |
| `post_id` | Bài được xem |
| `user_id` | Người xem nếu đã đăng nhập |
| `session_id` | Nhận diện phiên truy cập |
| `ip_hash` | Giá trị băm hỗ trợ hạn chế đếm trùng |
| `viewed_at` | Thời gian xem |

Không nên công khai hoặc lưu địa chỉ IP thô lâu hơn mức cần thiết. Có thể sử dụng dữ liệu đã băm và chính sách lưu trữ phù hợp.

### 8.11. Bảng `activity_logs`

Lưu các hành động quan trọng như:

- Admin duyệt hoặc từ chối bài.
- Admin thay đổi vai trò tài khoản.
- Admin khóa tài khoản.
- Admin ẩn bài hoặc bình luận.
- Admin xử lý báo cáo.

Các trường đề xuất:

```text
id
user_id
action
subject_type
subject_id
description
created_at
```

## 9. Quan hệ dữ liệu chính

```text
User 1 -------- N Post
User 1 -------- N Comment
User 1 -------- N Favorite
User 1 -------- N CommentReport

Category 1 ---- N Post

Post N -------- N Tag
Post 1 -------- N Comment
Post 1 -------- N Favorite
Post 1 -------- N PostView

Comment 1 ----- N Reply
Comment 1 ----- N CommentReport
```

## 10. Danh sách giao diện

### 10.1. Giao diện công khai

1. Trang chủ.
2. Trang danh sách chuyên mục.
3. Trang danh sách bài theo thẻ.
4. Trang chi tiết bài viết.
5. Trang tìm kiếm.
6. Trang hồ sơ công khai của tác giả.
7. Trang đăng ký.
8. Trang đăng nhập.
9. Trang quên và đặt lại mật khẩu.
10. Trang lỗi 403, 404 và 500.

### 10.2. Khu vực User

1. Trang hồ sơ cá nhân.
2. Trang đổi mật khẩu.
3. Danh sách bài viết yêu thích.
4. Danh sách bình luận của tôi.
5. Danh sách báo cáo đã gửi nếu cần.

### 10.3. Khu vực Author

1. Dashboard cá nhân.
2. Danh sách bài của tôi.
3. Trang tạo bài.
4. Trang chỉnh sửa bài.
5. Trang xem trước bài.
6. Danh sách bài nháp.
7. Danh sách bài chờ duyệt.
8. Danh sách bài bị từ chối.
9. Thống kê bài viết cá nhân.

### 10.4. Khu vực Admin

1. Dashboard tổng quan.
2. Quản lý bài viết.
3. Danh sách bài chờ duyệt.
4. Quản lý chuyên mục.
5. Quản lý thẻ.
6. Quản lý tài khoản.
7. Quản lý bình luận.
8. Xử lý báo cáo bình luận.
9. Nhật ký hoạt động.

## 11. Yêu cầu phi chức năng

### 11.1. Bảo mật

- Mật khẩu phải được hash bằng cơ chế an toàn của framework.
- Sử dụng CSRF token cho các form thay đổi dữ liệu.
- Sử dụng ORM hoặc prepared statement để phòng SQL injection.
- Escape dữ liệu do người dùng nhập khi hiển thị.
- Nếu cho phép HTML trong bài viết, phải làm sạch bằng danh sách thẻ được cho phép.
- Kiểm tra MIME type, phần mở rộng và dung lượng của ảnh upload.
- Đổi tên file upload để tránh ghi đè và tên file nguy hiểm.
- Không lưu file thực thi trong thư mục có thể truy cập công khai.
- Giới hạn số lần đăng nhập sai.
- Rate limit đăng nhập, bình luận và báo cáo.
- Kiểm tra quyền ở backend bằng middleware và policy.
- Không đưa mật khẩu database hoặc khóa bí mật lên Git.
- Production phải sử dụng HTTPS.

### 11.2. Hiệu năng

- Phân trang danh sách bài và bình luận.
- Tạo index cho các cột thường xuyên tìm kiếm và liên kết.
- Tránh truy vấn N+1 bằng eager loading phù hợp.
- Tối ưu kích thước ảnh và tạo thumbnail.
- Lazy-load ảnh trong danh sách dài.
- Cache các dữ liệu ít thay đổi như chuyên mục hoặc bài nổi bật nếu cần.
- Không tải toàn bộ phản hồi bình luận khi chưa được yêu cầu.

### 11.3. Khả dụng

- Giao diện responsive.
- Form có thông báo lỗi rõ ràng.
- Thao tác nguy hiểm cần hộp thoại xác nhận.
- Có trạng thái loading khi gửi request kéo dài.
- Màu sắc và độ tương phản dễ đọc.
- Các trang lỗi phải giúp người dùng quay lại trang phù hợp.

### 11.4. Khả năng bảo trì

- Tuân thủ cấu trúc và convention của framework.
- Controller không chứa quá nhiều nghiệp vụ phức tạp.
- Sử dụng Form Request cho validation nếu dùng Laravel.
- Sử dụng Policy để kiểm tra quyền trên tài nguyên.
- Đặt tên biến, class, route và bảng dữ liệu nhất quán.
- Có README hướng dẫn cài đặt.
- Có dữ liệu seed để giảng viên có thể chạy thử.

### 11.5. Sao lưu và phục hồi

- Có hướng dẫn sao lưu database.
- Có phương án sao lưu ảnh bài viết.
- Có hướng dẫn khôi phục dữ liệu.
- Không coi file trên máy lập trình là bản sao lưu duy nhất.

## 12. Công nghệ đề xuất

| Thành phần | Công nghệ |
|---|---|
| Backend | Laravel |
| Ngôn ngữ | PHP |
| Database | MySQL |
| Giao diện | Blade + Bootstrap hoặc Tailwind CSS |
| Biểu đồ | Chart.js |
| Kiểm thử | PHPUnit hoặc Pest |
| Quản lý phiên bản | Git và GitHub |
| Web server | Nginx hoặc Apache |
| Triển khai | VPS hoặc hosting hỗ trợ PHP |

Kiến trúc xử lý đề xuất:

```text
Request
  -> Route
  -> Middleware
  -> Controller
  -> Service (chỉ khi nghiệp vụ đủ phức tạp)
  -> Model
  -> Database
```

Không cần tạo Service cho mọi thao tác CRUD đơn giản. Chỉ tách Service khi có quy trình nghiệp vụ nhiều bước, ví dụ duyệt bài, xử lý báo cáo hoặc ghi nhận lượt xem.

## 13. Kế hoạch triển khai dự kiến

### Tuần 1 — Phân tích yêu cầu

- Chốt phạm vi chức năng.
- Xác định actor và quyền của từng actor.
- Viết danh sách use case.
- Viết quy tắc nghiệp vụ.
- Vẽ use-case diagram.
- Phác thảo giao diện.

**Kết quả:** đặc tả yêu cầu bước đầu và danh sách chức năng đã được chốt.

### Tuần 2 — Thiết kế hệ thống

- Thiết kế ERD.
- Thiết kế bảng và khóa ngoại.
- Vẽ activity diagram cho duyệt bài.
- Vẽ sequence diagram cho các luồng quan trọng.
- Chốt cấu trúc thư mục và công nghệ.
- Khởi tạo Git repository.

**Kết quả:** thiết kế cơ sở dữ liệu và kiến trúc có thể bắt đầu lập trình.

### Tuần 3 — Tài khoản và phân quyền

- Khởi tạo dự án Laravel.
- Cấu hình MySQL.
- Tạo migration và model tài khoản.
- Xây dựng đăng ký, đăng nhập và đăng xuất.
- Xác minh email và đặt lại mật khẩu.
- Xây dựng role, middleware và policy.
- Xây dựng hồ sơ cá nhân.

**Kết quả:** các vai trò đăng nhập và chỉ truy cập được đúng khu vực.

### Tuần 4 — Chuyên mục, thẻ và bài viết

- CRUD chuyên mục.
- CRUD thẻ.
- Tạo và chỉnh sửa bài viết.
- Upload và kiểm tra ảnh.
- Sinh slug duy nhất.
- Lưu bản nháp và xem trước.

**Kết quả:** Author có thể tạo và quản lý bản nháp của mình.

### Tuần 5 — Quy trình duyệt bài

- Gửi bài duyệt.
- Trang danh sách bài chờ duyệt.
- Duyệt bài.
- Từ chối và nhập lý do.
- Gửi duyệt lại.
- Xuất bản ngay hoặc hẹn giờ.
- Ghi activity log.

**Kết quả:** quy trình Author–Admin hoạt động đầy đủ và không thể vượt quyền.

### Tuần 6 — Giao diện tin tức

- Trang chủ.
- Trang chuyên mục và thẻ.
- Trang chi tiết bài viết.
- Tìm kiếm, lọc và phân trang.
- Bài nổi bật.
- Bài liên quan.
- Ghi nhận lượt xem.

**Kết quả:** khách truy cập có thể đọc và tìm kiếm tin tức hoàn chỉnh.

### Tuần 7 — Bình luận

- Tạo bình luận gốc.
- Trả lời bình luận.
- Bảo đảm giao diện chỉ có hai cấp.
- Sửa và xóa mềm bình luận.
- Phân trang bình luận gốc.
- Tải phản hồi theo từng bình luận.
- Rate limit chống spam.

**Kết quả:** hội thoại hoạt động đúng cấu trúc và đúng quyền.

### Tuần 8 — Báo cáo, yêu thích và thống kê

- Báo cáo bình luận.
- Admin xử lý báo cáo.
- Lưu bài yêu thích.
- Dashboard Admin.
- Dashboard Author.
- Biểu đồ thống kê.

**Kết quả:** hoàn thiện các chức năng tương tác và quản trị nâng cao.

### Tuần 9 — Kiểm thử và bảo mật

- Viết test xác thực và phân quyền.
- Test quy trình duyệt bài.
- Test quyền sửa/xóa bình luận.
- Test bình luận hai cấp.
- Test validation.
- Kiểm tra XSS, CSRF và upload.
- Kiểm tra các truy vấn và hiệu năng cơ bản.
- Kiểm tra responsive.

**Kết quả:** hệ thống ổn định, các luồng quan trọng có bằng chứng kiểm thử.

### Tuần 10 — Triển khai và báo cáo

- Chuẩn bị dữ liệu demo.
- Triển khai production.
- Cấu hình HTTPS và biến môi trường.
- Kiểm tra lại hệ thống sau triển khai.
- Hoàn thiện báo cáo.
- Chuẩn bị slide.
- Viết kịch bản demo và phương án dự phòng.

**Kết quả:** có website chạy thật, mã nguồn, tài liệu và nội dung bảo vệ.

## 14. Kế hoạch kiểm thử

### 14.1. Kiểm thử tài khoản

- Không đăng ký được với email đã tồn tại.
- Không đăng nhập được khi sai mật khẩu.
- Tài khoản bị khóa không thể sử dụng chức năng yêu cầu đăng nhập.
- User không truy cập được trang Author hoặc Admin.
- Author không truy cập được chức năng riêng của Admin.

### 14.2. Kiểm thử bài viết

- Author chỉ sửa được bài của mình.
- Author không thể tự xuất bản bài.
- Không gửi duyệt được bài thiếu dữ liệu bắt buộc.
- Admin phải nhập lý do khi từ chối.
- Chỉ bài `published` mới hiển thị công khai.
- Bài hẹn giờ chưa đến thời điểm không được hiển thị.
- Slug của mỗi bài là duy nhất.

### 14.3. Kiểm thử bình luận

- Guest không thể bình luận.
- User chỉ sửa/xóa được bình luận của mình.
- Admin có thể ẩn bình luận vi phạm.
- Khi trả lời một phản hồi, `parent_id` vẫn trỏ về bình luận gốc.
- `reply_to_id` trỏ đúng bình luận đang được trả lời.
- Xóa bình luận gốc không làm mất câu trả lời.
- Nội dung nguy hiểm không được thực thi trên trình duyệt.
- Rate limit ngăn việc gửi bình luận liên tục.

### 14.4. Kiểm thử tìm kiếm và hiển thị

- Tìm kiếm trả về bài phù hợp.
- Bộ lọc chuyên mục và thẻ hoạt động đúng.
- Bài bị ẩn không xuất hiện trong kết quả.
- Phân trang không lặp hoặc bỏ sót dữ liệu.
- Bài liên quan không chứa chính bài đang đọc.

### 14.5. Kiểm thử báo cáo

- User không thể báo cáo cùng một bình luận liên tục.
- Admin xem được báo cáo đang chờ.
- Trạng thái báo cáo được cập nhật sau xử lý.
- Hành động của Admin được ghi vào nhật ký.

## 15. Tài liệu cần có trong báo cáo đồ án

1. Lý do chọn đề tài.
2. Mục tiêu và phạm vi.
3. Khảo sát một số hệ thống tương tự.
4. Yêu cầu chức năng và phi chức năng.
5. Use-case diagram và mô tả use case.
6. Activity diagram cho các quy trình chính.
7. Sequence diagram cho các luồng quan trọng.
8. ERD và mô tả cơ sở dữ liệu.
9. Kiến trúc hệ thống.
10. Thiết kế giao diện.
11. Mô tả triển khai các chức năng chính.
12. Kế hoạch và kết quả kiểm thử.
13. Hướng dẫn cài đặt và sử dụng.
14. Kết quả đạt được.
15. Hạn chế và hướng phát triển.

## 16. Kịch bản demo bảo vệ đồ án

Kịch bản nên ngắn, có trình tự và thể hiện rõ nghiệp vụ:

1. Guest mở trang chủ, xem chuyên mục và tìm kiếm bài.
2. User đăng nhập, lưu bài yêu thích và tạo bình luận.
3. User trả lời một phản hồi để minh họa mô hình bình luận hai cấp.
4. User báo cáo một bình luận vi phạm.
5. Author đăng nhập, tạo bài, lưu nháp và gửi duyệt.
6. Admin đăng nhập, xem bài chờ duyệt và từ chối kèm lý do.
7. Author chỉnh sửa và gửi duyệt lại.
8. Admin duyệt và xuất bản bài.
9. Kiểm tra bài vừa xuất hiện trên trang công khai.
10. Admin xử lý báo cáo bình luận và xem dashboard.

Cần chuẩn bị sẵn tài khoản demo cho từng vai trò và dữ liệu mẫu để tránh mất thời gian nhập dữ liệu trong lúc trình bày.

## 17. Tiêu chí hoàn thành

Đồ án được xem là hoàn thành khi đáp ứng tất cả tiêu chí sau:

- Guest, User, Author và Admin hoạt động đúng quyền.
- Author không thể tự xuất bản bài.
- Quy trình gửi duyệt, từ chối, sửa và duyệt lại hoạt động đầy đủ.
- Chỉ bài hợp lệ được hiển thị công khai.
- Bình luận hai cấp hoạt động đúng thiết kế.
- Xóa mềm không phá vỡ hội thoại.
- Báo cáo bình luận được tiếp nhận và xử lý.
- Tìm kiếm, lọc và phân trang hoạt động chính xác.
- Upload ảnh được kiểm tra an toàn.
- Có dashboard và thống kê cơ bản.
- Các luồng nghiệp vụ quan trọng có kiểm thử.
- Giao diện responsive.
- Website được triển khai chạy thực tế.
- README và tài liệu cho phép cài đặt lại dự án.
- Có dữ liệu mẫu và kịch bản demo hoàn chỉnh.

## 18. Ưu tiên khi thời gian bị giới hạn

Nếu tiến độ không đủ, cần ưu tiên theo thứ tự:

### Mức 1 — Bắt buộc

- Đăng ký, đăng nhập và phân quyền.
- Chuyên mục, thẻ và bài viết.
- Quy trình duyệt bài.
- Trang hiển thị tin tức.
- Tìm kiếm và phân trang.
- Bình luận hai cấp.
- Quản lý bình luận.
- Validation và bảo mật cơ bản.

### Mức 2 — Nên có

- Báo cáo bình luận.
- Bài yêu thích.
- Bài liên quan.
- Dashboard thống kê.
- Activity log.
- Hẹn giờ xuất bản.

### Mức 3 — Có thể mở rộng

- Lịch sử phiên bản bài viết.
- Thông báo cho Author khi bài được xử lý.
- CAPTCHA thích ứng.
- Thống kê chuyên sâu.
- Newsletter.
- API cho ứng dụng di động.

Không được hy sinh tính đúng đắn của phân quyền, bảo mật hoặc quy trình duyệt bài để hoàn thành nhiều chức năng phụ.

## 19. Kết luận

Đề tài được định hướng thành một hệ thống tin tức có quy trình nghiệp vụ rõ ràng thay vì một website CRUD đơn giản. Ba điểm trọng tâm của hệ thống là:

1. **Quy trình Author gửi bài và Admin kiểm duyệt trước khi xuất bản.**
2. **Hệ thống bình luận hai cấp, có trả lời, xóa mềm, báo cáo và chống spam.**
3. **Phân quyền, bảo mật, kiểm thử và triển khai đủ nghiêm túc cho đồ án năm 4.**

Phạm vi này đủ để thể hiện năng lực phân tích, thiết kế cơ sở dữ liệu, xây dựng ứng dụng web, xử lý nghiệp vụ, bảo mật và kiểm thử, đồng thời vẫn có khả năng hoàn thành trong thời gian thực hiện đồ án.

## 20. Các quyết định bổ sung cần chốt trước khi code

Phần này là quyết định chính thức cho những nội dung trước đây còn diễn giải chung. Nếu có thay đổi, cần cập nhật đồng thời migration, model, policy, test và tài liệu.

### 20.1. Hẹn giờ xuất bản

Phiên bản đồ án sử dụng **query-time filter**, không phụ thuộc vào một job để đổi trạng thái:

- Bài được Admin duyệt có `status = published` và `published_at` là thời điểm tương lai.
- Truy vấn công khai luôn áp dụng điều kiện `status = published` và `published_at <= now()`.
- Scheduler chỉ là phương án bổ sung để gửi thông báo hoặc thực hiện tác vụ bảo trì, không phải nguồn sự thật cho việc hiển thị bài.
- Dashboard phải tách rõ `published_total` (đã duyệt/xuất bản) và `published_visible` (đang đủ điều kiện hiển thị ở thời điểm hiện tại).

### 20.2. Chống trùng lượt xem

Một người xem chỉ được tính **một lượt xem cho một bài trong mỗi 24 giờ**:

- User đã đăng nhập: khóa dedup là `post_id + user_id`.
- Guest: khóa dedup là `post_id + session_id`.
- Nếu session không có, dùng `post_id + ip_hash` làm phương án dự phòng.
- Ứng dụng kiểm tra cache trước khi ghi `post_views` và tăng `posts.view_count`.
- Hết 24 giờ, lượt xem tiếp theo mới được ghi nhận. Không tạo unique index theo thời gian trên database vì quy tắc này cần TTL.

### 20.3. Chống trùng báo cáo bình luận

Một User chỉ được báo cáo một bình luận **một lần trong toàn bộ vòng đời của bình luận**. Database bắt buộc có unique constraint trên cặp `comment_id` và `reporter_id`. Nếu Admin cần xem xét lại, xử lý trên báo cáo cũ thay vì tạo báo cáo trùng.

### 20.4. Email chưa xác minh

User được phép đăng nhập và đọc nội dung khi email chưa xác minh, nhưng bị giới hạn các hành động tạo dữ liệu hoặc có rủi ro spam:

- Không được bình luận hoặc trả lời bình luận.
- Không được báo cáo bình luận.
- Không được lưu bài yêu thích.
- Author chưa xác minh không được gửi bài để duyệt.
- Giao diện hiển thị nhắc nhở xác minh và cho phép gửi lại email xác minh có rate limit.

### 20.5. Xóa tag đang được sử dụng

Xóa tag sử dụng `ON DELETE CASCADE` trên bảng trung gian `post_tag`. Việc này chỉ xóa liên kết giữa tag và các bài viết, không xóa bài viết. Category vẫn giữ quy tắc chặn xóa khi đang có bài viết.

### 20.6. Rate limit đặt lại mật khẩu

Endpoint yêu cầu đặt lại mật khẩu phải giới hạn theo email và IP, đề xuất tối đa **5 yêu cầu trong 60 phút**. Khi vượt giới hạn, hệ thống trả thông báo chung, không tiết lộ email có tồn tại hay không. Token đặt lại mật khẩu vẫn phải có thời hạn và chỉ sử dụng một lần.

### 20.7. SEO cơ bản

Mỗi bài viết nên bổ sung:

- `meta_title`, mặc định lấy từ tiêu đề nếu để trống.
- `meta_description`, mặc định lấy từ summary và cắt theo độ dài phù hợp.
- Open Graph title, description và image trong trang chi tiết.
- `sitemap.xml` chỉ chứa URL bài viết đang công khai, chuyên mục và thẻ đang hoạt động.

SEO là hạng mục mức 2; nếu thiếu thời gian, ưu tiên meta title/description và sitemap bài viết trước.

### 20.8. Buffer tiến độ

Lịch 10 tuần được điều chỉnh để dành **tuần 11 làm buffer** cho phản hồi của GVHD, sửa lỗi tích hợp, kiểm thử hồi quy và hoàn thiện demo. Không dồn các chức năng mức 3 vào tuần buffer nếu các tiêu chí mức 1 chưa hoàn thành.

### 20.9. Lưu ảnh và logging lỗi

- Phiên bản đồ án dùng local storage của Laravel (`storage/app/public`) và `php artisan storage:link`.
- File upload phải được đổi tên, kiểm tra MIME type, phần mở rộng và dung lượng.
- Không cần S3 trong phiên bản đầu; có thể nêu là hướng phát triển.
- Lỗi ứng dụng được ghi bằng Laravel log theo môi trường. Sentry chỉ là lựa chọn mở rộng, không bắt buộc.
