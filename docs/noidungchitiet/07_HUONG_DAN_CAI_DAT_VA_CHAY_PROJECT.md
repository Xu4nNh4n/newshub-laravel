# 07. HƯỚNG DẪN CÀI ĐẶT & KHỞI CHẠY DỰ ÁN (SETUP GUIDE)

---

## 💻 1. YÊU CẦU MÔI TRƯỜNG MÁY TÍNH

Trước khi bắt đầu, hãy đảm bảo máy tính của bạn đã cài đặt các công cụ sau:
- **PHP:** Phiên bản `>= 8.2` (khuyến nghị PHP 8.3, 8.4 hoặc 8.5) kèm các extension cơ bản: `pdo`, `sqlite3`, `curl`, `mbstring`, `fileinfo`, `openssl`.
- **Composer:** Trình quản lý thư viện PHP (`composer -v`).
- **Node.js & npm:** Node.js phiên bản `>= 18.x` và npm (`node -v` và `npm -v`).
- **Git** (nếu lấy source code từ kho lưu trữ Git).

---

## 🚀 2. CÁC BƯỚC KHỞI CHẠY NHANH NHẤT (VỚI SQLITE - ZERO-CONFIG)

Đây là cách đơn giản và nhanh nhất để bạn hoặc giảng viên chấm bài mở lên xem ngay mà **không cần cài đặt hay bật bất kỳ phần mềm CSDL nào (XAMPP / Laragon)**:

### Bước 1: Mở terminal tại thư mục gốc dự án
```bash
cd D:\Code\code\PhpProject
```

### Bước 2: Cài đặt các gói phụ thuộc PHP (nếu mới clone)
```bash
composer install
```

### Bước 3: Cài đặt và biên dịch giao diện Frontend
```bash
npm install
npm run build
```

### Bước 4: Chuẩn bị file cấu hình môi trường `.env`
Nếu chưa có file `.env`, copy từ file `.env.example`:
```bash
cp .env.example .env
php artisan key:generate
```

Đảm bảo trong file `.env` dòng cấu hình Database đang là:
```env
DB_CONNECTION=sqlite
```

### Bước 5: Tạo liên kết thư mục chứa ảnh (Storage Link)
```bash
php artisan storage:link
```

### Bước 6: Khởi chạy máy chủ nội bộ
```bash
php artisan serve
```

👉 Mở trình duyệt truy cập: **`http://localhost:8000`**  
Trang web đã sẵn sàng với đầy đủ tin bài mẫu, chuyên mục và tài khoản thử nghiệm!

---

## 🐬 3. CÁCH CHUYỂN SANG DÙNG CSDL MYSQL (NẾU CẦN)

Nếu giảng viên yêu cầu phải chạy trên MySQL (hoặc dùng XAMPP / Laragon):

### Bước 1: Bật dịch vụ MySQL trên máy tính
- Mở bảng điều khiển **XAMPP Control Panel** và nhấn **Start** tại mục MySQL.  
  *(Hoặc mở **Laragon** và nhấn **Start All**).*

### Bước 2: Import file SQL có sẵn vào MySQL
Hệ thống đã chuẩn bị sẵn file SQL chuẩn 100% cú pháp MySQL tại:  
📁 [`database/sql/apptintuc_mysql.sql`](file:///D:/Code/code/PhpProject/database/sql/apptintuc_mysql.sql)

**Cách import bằng phpMyAdmin:**
1. Truy cập `http://localhost/phpmyadmin` trên trình duyệt.
2. Nhấp vào tab **Import** (Nhập).
3. Chọn file `D:\Code\code\PhpProject\database\sql\apptintuc_mysql.sql`.
4. Bấm nút **Import** (Thực hiện). Toàn bộ Database `apptintuc` kèm 14 bảng và dữ liệu mẫu sẽ được tạo tự động.

### Bước 3: Đổi cấu hình trong file `.env`
Mở file `.env`, tìm đến phần cấu hình Database và sửa thành:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=apptintuc
DB_USERNAME=root
DB_PASSWORD=
```
*(Nếu mật khẩu root của bạn khác rỗng, hãy điền vào `DB_PASSWORD`)*.

### Bước 4: Xóa cache và khởi động lại
```bash
php artisan config:clear
php artisan serve
```

---

## 🔑 4. DANH SÁCH TÀI KHOẢN ĐĂNG NHẬP MẪU

Tất cả các tài khoản demo dưới đây đều đã được xác thực email sẵn và sử dụng chung một mật khẩu duy nhất:
👉 **Mật khẩu chung:** **`password`**

| Phân quyền (Role) | Họ và tên | Email đăng nhập | Mật khẩu | Chức năng thử nghiệm chính |
| :--- | :--- | :--- | :--- | :--- |
| **Quản trị viên (Admin)** | Quản trị viên Demo | `admin@newshub.test` | `password` | Duyệt bài, duyệt CTV, quản lý tài khoản, xem audit logs, xuất CSV. |
| **Tác giả (Author)** | Tác giả Demo | `author@newshub.test` | `password` | Dashboard cá nhân, viết bài mới, nộp bài, gửi yêu cầu gỡ bài. |
| **Độc giả (Reader/User)** | Độc giả Demo | `reader@newshub.test` | `password` | Lưu bài yêu thích, lịch sử đọc bài, bình luận, nộp đơn ứng tuyển làm CTV. |

---

## 🛠️ 5. CÁC LỆNH ARTISAN THƯỜNG DÙNG TRONG QUÁ TRÌNH PHÁT TRIỂN

| Câu lệnh | Mục đích sử dụng |
| :--- | :--- |
| `php artisan serve` | Khởi chạy máy chủ web local tại cổng 8000 |
| `npm run dev` | Bật chế độ lắng nghe và biên dịch CSS/JS tức thời khi chỉnh sửa giao diện |
| `npm run build` | Biên dịch tối ưu hóa toàn bộ tài nguyên Frontend để chạy thực tế |
| `php artisan test --compact` | Chạy toàn bộ 180 ca kiểm thử tự động của hệ thống |
| `php artisan route:list` | Xem danh sách toàn bộ các đường dẫn URL trong dự án |
| `vendor/bin/pint --format agent` | Tự động căn chỉnh và format toàn bộ code PHP theo chuẩn PSR-12 |
| `php artisan config:clear` | Xóa bộ nhớ đệm cấu hình khi thay đổi file `.env` |
| `php artisan cache:clear` | Xóa bộ nhớ đệm ứng dụng |

---

## ❓ 6. XỬ LÝ CÁC VẤN ĐỀ THƯỜNG GẶP (TROUBLESHOOTING)

### 1. Lỗi "Unable to locate file in Vite manifest":
- **Nguyên nhân:** Chưa biên dịch asset Frontend.
- **Cách xử lý:** Chạy lệnh `npm run build` (hoặc bật `npm run dev` ở một cửa sổ dòng lệnh riêng).

### 2. Lỗi ảnh đại diện bài viết không hiển thị (ảnh bị vỡ):
- **Nguyên nhân:** Thiếu liên kết tượng trưng từ `public/storage` vào `storage/app/public`.
- **Cách xử lý:** Chạy lệnh: `php artisan storage:link`.

### 3. Lỗi quyền ghi thư mục `storage` hoặc `bootstrap/cache`:
- **Cách xử lý trên Linux / macOS:** Chạy lệnh: `chmod -R 775 storage bootstrap/cache`.
- **Trên Windows:** Thường tự động cấp quyền, nếu bị lỗi hãy mở cửa sổ dòng lệnh bằng quyền Administrator.
