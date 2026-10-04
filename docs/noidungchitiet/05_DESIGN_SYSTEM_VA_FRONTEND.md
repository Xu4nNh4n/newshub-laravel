# 05. NGÔN NGỮ THIẾT KẾ EDITORIAL & GIAO DIỆN FRONTEND

---

## 🎨 1. ĐỊNH HƯỚNG THẨM MỸ (EDITORIAL / OPENJEV INSPIRATION)

Giao diện của **NewsHub** được xây dựng dựa trên triết lý **Editorial Modernism kết hợp Neo-Brutalist nhẹ nhàng**, lấy cảm hứng từ visual language của các tạp chí báo chí đương đại:

### Các đặc điểm nhận diện chính:
- **Nền giấy báo ấm áp (Warm Newsprint Paper):** Thay vì sử dụng nền trắng toát gây chói mắt hoặc nền tối đơn điệu, hệ thống sử dụng tone nền ngả vàng nhạt (`#F7F6F0`) như một trang báo giấy thực thụ.
- **Mực in sắc sảo (Deep Ink):** Màu chữ chính là màu mực đen sâu (`#171715`), mang lại độ tương phản tuyệt hảo và trải nghiệm đọc báo dễ chịu.
- **Đường kẻ phân cách rõ ràng (Crisp Structural Borders):** Bố cục được phân tách mạch lạc bằng các đường kẻ viền dứt khoát (`border-line`, `border-line-strong`), gợi nhớ đến cách dàn trang báo in truyền thống.
- **Đổ bóng thô không nhòe (Solid Brutalist Shadows):** Sử dụng bóng đổ cứng dạng hình học (`4px 4px 0px #171715` và `2px 2px 0px #171715`) thay vì bóng đổ mờ (blur shadow), tạo cảm giác chắc chắn, xúc giác và hiện đại.
- **Màu nhấn bút dạ quang (Highlighter Lime Accent):** Điểm xuyết tinh tế màu xanh chanh (`#D4FF3F`) cho các nút bấm hành động chính, huy hiệu chuyên mục và các bài viết tiêu điểm.
- **Ít bo góc (Low Border-Radius):** Các khối hộp, nút bấm và ảnh đại diện sử dụng góc vuông hoặc bo cực nhẹ (`rounded-none` hoặc `rounded-sm`), tránh phong cách bong bóng bo tròn của các giao diện mạng xã hội thông thường.

---

## 🎯 2. BẢNG MÃ DESIGN TOKENS (HỆ THỐNG MÀU DÙNG CHUNG)

Được định nghĩa trực tiếp trong file [`resources/css/app.css`](file:///D:/Code/code/PhpProject/resources/css/app.css) và tích hợp liền mạch với Tailwind CSS:

| Tên biến CSS | Mã màu Hex | Tên màu | Mục đích sử dụng |
| :--- | :--- | :--- | :--- |
| `--color-bg` | `#F7F6F0` | **Warm Paper** | Nền chính của toàn bộ trang web và email |
| `--color-surface` | `#FFFEFA` | **Clean Surface** | Nền của thẻ bài viết, ô nhập form, bảng biểu |
| `--color-text` | `#171715` | **Deep Ink** | Màu chữ tiêu đề, thân bài và viền đậm |
| `--color-muted` | `#66645E` | **Muted Slate** | Chữ mô tả phụ, ngày giờ, số liệu thống kê |
| `--color-border` | `#C7C5BB` | **Fine Border** | Viền mỏng phân chia giữa các khối nội dung |
| `--color-border-strong` | `#171715` | **Solid Border** | Viền khung bên ngoài, viền bảng, viền nút bấm |
| `--color-accent` | `#D4FF3F` | **Lime Accent** | Màu nút bấm chính, nhãn tiêu điểm, điểm nhấn |
| `--color-accent-pink` | `#EE5BA6` | **Editorial Pink** | Nhãn khẩn cấp, huy hiệu phụ nổi bật |
| `--color-danger` | `#B42318` | **Crimson Danger** | Nút xóa, từ chối, cảnh báo lỗi nguy hiểm |

---

## 📐 3. TYPOGRAPHY & PHÂN CẤP CHỮ

- **Font chữ chính (Body & Heading):** `Instrument Sans` — Kiểu chữ sans-serif hiện đại, thanh thoát, tối ưu hóa cho màn hình võng mạc (Retina).
- **Phân cấp tiêu đề (Heading Hierarchy):**
  - `H1` (Tiêu đề bài viết / Trang): 28px - 38px, Font weight 900 (Black), tracking hẹp (`tracking-tight`), viết hoa hoặc chữ thường đậm nét.
  - `H2` (Tiêu đề mục con): 20px - 24px, Font weight 800 (Bold).
  - `H3` (Tiêu đề thẻ tin tức): 15px - 18px, Font weight 700.
- **Font Monospace cho siêu dữ liệu (Metadata):**  
  Ngày đăng tin, tên tác giả, chuyên mục, lượt xem, thời gian đọc ước tính và các huy hiệu trạng thái đều sử dụng phông `font-mono` cỡ chữ nhỏ (`text-[11px]` - `text-xs`) viết hoa (`uppercase tracking-wider`) tạo cảm giác chuyên nghiệp của một tòa soạn báo.

---

## 🧩 4. CẤU TRÚC BLADE TEMPLATES & COMPONENTS TÁI SỬ DỤNG

### 1. Khung giao diện chính:
- **`resources/views/layouts/app.blade.php`:**  
  Dành cho toàn bộ phần công cộng: Trang chủ, Đọc bài chi tiết, Chuyên mục, Tìm kiếm, Trang cá nhân của Độc giả. Chứa thanh Topbar tin tức, Menu đa cấp, Quả chuông thông báo, Thanh tìm kiếm nhanh và Footer tòa soạn.
- **`resources/views/layouts/dashboard.blade.php`:**  
  Dành riêng cho khu vực làm việc của Tác giả và Quản trị viên. Có Sidebar điều hướng cố định, huy hiệu đếm số lượng bài chờ duyệt/báo cáo theo thời gian thực và tích hợp sẵn Modal cảnh báo thao tác an toàn.

### 2. Thành phần tương tác đặc thù (Micro-Components):
- **Hộp thoại xác nhận thao tác an toàn (`data-confirm`):**  
  Mọi nút bấm có tính chất nguy hại hoặc nhạy cảm (Xóa bài, Gỡ bài, Khóa tài khoản, Từ chối đơn, Gửi email reset mật khẩu) chỉ cần gắn thuộc tính `data-confirm="..."`, hệ thống Javascript toàn cục sẽ tự động chặn gửi form và bật Modal giao diện phong cách Neo-Brutalist để người dùng xác nhận rõ ràng trước khi thực hiện.
- **Hệ thống Toast thông báo trạng thái:**  
  Tự động bắt thông báo từ `session('status')` hoặc `session('error')` của Laravel để hiển thị thanh trượt góc màn hình có màu sắc tương ứng.
- **Template Email đồng bộ hoàn hảo ([`resources/views/mail/auth-action.blade.php`](file:///D:/Code/code/PhpProject/resources/views/mail/auth-action.blade.php)):**  
  Email gửi liên kết xác thực tài khoản và email gửi liên kết đặt lại mật khẩu đều được thiết kế tỉ mỉ theo đúng ngôn ngữ báo chí OpenJev (nền giấy ấm, viền mực đậm, nút CTA màu Lime nổi bật, tương thích 100% với ứng dụng Gmail trên điện thoại).
