# 04. CHI TIẾT CÁC TÍNH NĂNG THEO PHÂN QUYỀN (FEATURES BREAKDOWN)

---

## 🌐 1. PHÂN HỆ CÔNG CHÚNG & ĐỘC GIẢ (PUBLIC INTERFACE)

Phân hệ dành cho mọi người dùng khi truy cập website mà không bắt buộc phải đăng nhập:

### 1. Trang chủ tin tức (Homepage)
- **Tiêu điểm báo chí (Spotlight / Hero Story):** Bài viết nổi bật nhất được ban biên tập ghim tại vị trí trang trọng đầu trang.
- **Dòng sự kiện mới nhất (Latest News):** Danh sách tin bài mới xuất bản theo thứ tự thời gian.
- **Bài viết được quan tâm nhất (Most Viewed / Trending):** Xếp hạng tự động dựa trên tổng số lượt xem thực tế của độc giả.
- **Phân khối theo Chuyên mục:** Hiển thị bài viết theo từng khối chuyên mục cụ thể (Công nghệ, Đời sống, Kinh doanh, v.v.).

### 2. Trang chi tiết bài viết & Bộ công cụ đọc báo hiện đại (Reader Utilities)
- **Nội dung bài viết chuẩn xuất bản:** Hiển thị ảnh bìa sắc nét, tiêu đề chính, Sapo tóm tắt, ngày giờ phát hành, tác giả biên soạn, danh mục và hệ thống thẻ tag liên quan.
- **Tính năng Đọc bài bằng giọng nói tự nhiên (Text-to-Speech - TTS):**  
  Tích hợp sẵn bộ nút phát/tạm dừng ở đầu bài viết. Sử dụng trực tiếp Web Speech Synthesis API của trình duyệt, hỗ trợ giọng đọc tiếng Việt mượt mà mà không tốn chi phí API dịch vụ ngoài và không làm nặng server.
- **Công cụ tùy chỉnh cỡ chữ (A- / A+):**  
  Giúp người lớn tuổi hoặc người mỏi mắt có thể phóng to / thu nhỏ cỡ chữ nội dung bài viết ngay lập tức mà không ảnh hưởng đến bố cục trang.
- **Chế độ In bài viết chuyên dụng (Print CSS):**  
  Nút "In bài viết" kích hoạt lệnh in của trình duyệt với CSS media print được tối ưu hóa đặc biệt: ẩn toàn bộ thanh điều hướng, nút bấm, footer, quảng cáo, chỉ giữ lại bài viết sạch đẹp để in ra giấy hoặc xuất file PDF lưu trữ.
- **Chia sẻ mạng xã hội nhanh (Social Share):**  
  Bộ nút chia sẻ bài viết nhanh chóng lên Facebook, X (Twitter), Zalo và nút Sao chép liên kết (Copy to Clipboard) kèm thông báo toast xác nhận trực quan.
- **Tối ưu hóa SEO với dữ liệu có cấu trúc (JSON-LD Schema):**  
  Nhúng tự động thẻ `<script type="application/ld+json">` chuẩn `schema.org/NewsArticle` chứa đầy đủ Headline, Image, DatePublished, DateModified, Author, Publisher theo đúng khuyến nghị của Google News.

### 3. Tìm kiếm & Bộ lọc tin tức
- Tìm kiếm toàn văn (Fulltext Search) theo từ khóa trong tiêu đề, tóm tắt và nội dung.
- Lọc bài viết theo Chuyên mục cha - Chuyên mục con.
- Lọc bài viết theo Thẻ bài viết (Tag).
- Nguồn cấp dữ liệu chuẩn RSS Feed (`/rss`) và sơ đồ trang web tự động (`/sitemap.xml`).

---

## 👤 2. PHÂN HỆ NGƯỜI DÙNG ĐÃ ĐĂNG NHẬP (REGISTERED USER)

Khi độc giả tạo tài khoản và xác thực email, họ được mở khóa toàn bộ quyền tương tác:

### 1. Bình luận & Thảo luận cộng đồng
- Bình luận trực tiếp dưới bài viết.
- **Trả lời bình luận lồng nhau đa cấp (Nested Threaded Replies):** Hiển thị thụt đầu dòng rõ ràng, tự động gắn tên người được phản hồi (`@Tên`).
- Báo cáo bình luận xấu (Report Comment): Độc giả có thể báo cáo các bình luận phản cảm, quấy rối hoặc spam lên ban biên tập để xử lý.

### 2. Quản lý nội dung cá nhân
- **Bài viết yêu thích (Saved Articles):** Bấm nút icon trái tim để lưu bài đọc lại sau. Trang quản lý bài yêu thích cho phép tìm kiếm và xóa bài khỏi danh sách.
- **Lịch sử đọc bài (Reading History):** Tự động ghi lại các bài viết mà độc giả đã đọc kèm mốc thời gian đọc gần nhất, hỗ trợ xóa từng bài hoặc xóa toàn bộ lịch sử.

### 3. Trung tâm thông báo quả chuông (In-app Notification Dropdown)
- Xuất hiện trên thanh điều hướng đầu trang (Topbar).
- Hiển thị chấm đỏ khi có thông báo mới chưa đọc.
- Báo hiệu tức thời khi: có người trả lời bình luận của bạn, ban biên tập duyệt hoặc từ chối đơn ứng tuyển tác giả của bạn.
- Bấm vào thông báo sẽ tự động dẫn thẳng tới nội dung tương ứng và đánh dấu đã đọc.

### 4. Ứng tuyển làm Tác giả (Author Application)
- Độc giả gửi đơn đăng ký trở thành phóng viên / tác giả của tòa soạn.
- Điền tiểu sử, chuyên môn quan tâm và đính kèm bài viết mẫu để ban biên tập đánh giá năng lực viết.

---

## ✍️ 3. PHÂN HỆ TÁC GIẢ / PHÓNG VIÊN (AUTHOR WORKFLOW)

Dành cho các thành viên ban nội dung sáng tạo bài viết:

### 1. Dashboard Tác giả
- Thống kê trực quan: Tổng số bài viết, Số bài đã xuất bản thành công, Số bài đang chờ duyệt, Tổng lượt xem bài viết của cá nhân.
- Danh sách các bài viết mới đăng gần nhất và lượt tương tác.

### 2. Soạn thảo & Quản lý bài viết
- Trình soạn thảo văn bản phong phú WYSIWYG.
- Tải lên ảnh bìa (Thumbnail) với tùy chọn ẩn/hiện ảnh bìa trong thân bài viết.
- Tải lên hình ảnh chèn vào thân bài viết (Media Upload) với giới hạn kích thước an toàn.
- Cấu hình thẻ SEO Meta riêng biệt: `Meta Title` và `Meta Description`.
- Gắn chuyên mục và chọn nhiều thẻ tag liên quan.

### 3. Quy trình xuất bản chuẩn tòa soạn
```
[Bản nháp (Draft)] ──(Nộp bài)──> [Chờ duyệt (Pending)] ──(Admin duyệt)──> [Đã xuất bản (Published)]
        ▲                                │                                         │
        │                                │                                         │
        └───(Rút bài về sửa)─────────────┴──────(Admin từ chối)                    │
                                                                                   │
[Gửi yêu cầu Gỡ/Sửa bài] ◄─────────────────────────────────────────────────────────┘
```
- **Lưu nháp (Draft):** Tác giả có thể lưu và chỉnh sửa không giới hạn số lần.
- **Nộp bài (Submit for review):** Chuyển trạng thái sang `Pending` để ban biên tập thẩm định. Khi đã nộp, bài viết bị khóa sửa đổi để đảm bảo tính toàn vẹn.
- **Rút bài về (Withdraw submission):** Nếu phát hiện thiếu sót trong lúc bài đang chờ duyệt, tác giả có thể chủ động rút bài về trạng thái Nháp để bổ sung.
- **Yêu cầu gỡ / sửa bài đã xuất bản (Post Requests):** Sau khi bài đã được Admin duyệt và đăng tải, tác giả không thể tự ý sửa hay xóa. Nếu cần chỉnh sửa nội dung hoặc xin gỡ bài vì lý do pháp lý/bản quyền, tác giả gửi một yêu cầu chính thức kèm mức độ ưu tiên (`Normal` hoặc `Urgent`) và giải trình lý do để Admin phê duyệt.

---

## 🛡️ 4. PHÂN HỆ QUẢN TRỊ VIÊN & TÒA SOẠN (ADMINISTRATION)

Dành cho Trưởng ban biên tập và Quản trị viên hệ thống:

### 1. Dashboard Quản trị toàn diện
- Tổng hợp toàn bộ chỉ số hệ thống: Tổng số bài viết, Số bài chờ duyệt, Tổng người dùng, Lượt xem tích lũy, Số đơn ứng tuyển chờ xử lý, Số báo cáo bình luận đang chờ giải quyết.

### 2. Duyệt bài viết (Post Review)
- Danh sách các bài viết phóng viên gửi lên tòa soạn.
- Xem trước nội dung chi tiết bài viết giống hệt giao diện độc giả.
- Quyết định: **Phê duyệt xuất bản ngay** hoặc **Từ chối bài viết kèm lý do cụ thể** gửi về cho tác giả.

### 3. Quản lý yêu cầu bài viết (Post Requests)
- Tiếp nhận và xử lý yêu cầu xin gỡ bài hoặc xin mở khóa sửa bài từ các tác giả.
- Phê duyệt để tự động đưa bài về trạng thái Nháp cho tác giả chỉnh sửa, hoặc từ chối kèm phản hồi.

### 4. Quản lý đơn ứng tuyển tác giả (Author Applications)
- Đọc bài viết mẫu và lý lịch ứng viên.
- Phê duyệt: Hệ thống tự động nâng cấp quyền của tài khoản đó thành `Author` ngay lập tức.
- Từ chối: Ghi nhận lý do và gửi thông báo về cho ứng viên.

### 5. Quản lý tài khoản & Bảo mật Zero-Knowledge
- Danh sách toàn bộ người dùng trong hệ thống (tìm kiếm theo tên, email, lọc theo vai trò, trạng thái).
- Khóa (Block) hoặc Mở khóa (Active) tài khoản vi phạm.
- Cấp hoặc Thu hồi quyền tác giả (User <-> Author).
- **Tính năng "Gửi link reset mật khẩu" chuẩn Zero-Knowledge:** Khi độc giả quên mật khẩu hoặc báo mất quyền truy cập, Admin bấm nút gửi link đặt lại mật khẩu trực tiếp về hòm thư người dùng qua token bảo mật dùng 1 lần (hạn 60 phút). Admin tuyệt đối không biết mật khẩu của người dùng, đảm bảo tính riêng tư cao nhất.
- Hộp thoại xác nhận an toàn (`data-confirm`) trước mọi hành động quan trọng để chống bấm nhầm.

### 6. Quản trị Chuyên mục & Thẻ bài viết
- Quản lý danh mục cha - con đa cấp: thêm, sửa, xóa mềm, hiển thị cây phân cấp trực quan.
- Quản lý kho Thẻ bài viết (Tags).

### 7. Kiểm duyệt bình luận & Báo cáo vi phạm
- Xem danh sách bình luận bị bạn đọc gắn cờ cảnh báo.
- Xem ngữ cảnh bài viết và nội dung bình luận vi phạm.
- Quyết định: Ẩn bình luận vi phạm, hoặc Bác bỏ báo cáo (Dismiss).

### 8. Xuất dữ liệu báo cáo (Export Excel / CSV) & Nhật ký kiểm toán
- **Xuất CSV Báo cáo bài viết:** Xuất danh sách bài viết theo bộ lọc phục vụ thống kê sản lượng tòa soạn.
- **Xuất CSV Báo cáo bình luận:** Phục vụ kiểm định tương tác độc giả.
- **Xuất CSV Nhật ký kiểm toán (Activity Logs):** Xuất toàn bộ lịch sử thao tác của các biên tập viên phục vụ thanh tra và lưu trữ.
- Toàn bộ hành động quan trọng trên hệ thống đều được tự động ghi nhận vào bảng kiểm toán.
