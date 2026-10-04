# HƯỚNG DẪN CHUYỂN ĐỔI VÀ IMPORT CƠ SỞ DỮ LIỆU MYSQL (NEWSHUB)

Tài liệu này hướng dẫn cách đưa cơ sở dữ liệu của dự án NewsHub lên hệ quản trị **MySQL / MariaDB** (hỗ trợ tốt trên **XAMPP**, **Laragon**, **MySQL Workbench**, hoặc **phpMyAdmin**).

---

## 📁 1. TỆP TIN DỮ LIỆU
- **Tên file:** `apptintuc_mysql.sql`
- **Vị trí:** Thư mục `database/sql/apptintuc_mysql.sql`
- **Bao gồm:**
  - Lệnh tạo database `apptintuc` chuẩn bảng mã `utf8mb4_unicode_ci`.
  - Toàn bộ cấu trúc bảng (DDL), chỉ mục (Indexes), khóa ngoại (Foreign Keys).
  - Bản ghi nhật ký di trú (`migrations`).
  - Dữ liệu mẫu (Seed Data) đầy đủ: Tài khoản các phân quyền, Chuyên mục đa cấp, Thẻ bài viết, Bài viết tin tức thực tế, Bình luận lồng nhau, Báo cáo bình luận, Lịch sử xem bài, Yêu cầu biên tập và Nhật ký hoạt động.

---

## 🚀 2. CÁCH IMPORT DỮ LIỆU VÀO MYSQL

### Cách 1: Sử dụng giao diện phpMyAdmin (Dễ nhất cho XAMPP / Laragon)
1. Mở trình duyệt truy cập: `http://localhost/phpmyadmin`
2. Nhấp vào tab **Import** (hoặc **Nhập**) trên thanh menu trên cùng.
3. Nhấp nút **Choose File** (Chọn tệp) và chọn file:
   `D:\Code\code\PhpProject\database\sql\apptintuc_mysql.sql`
4. Cuộn xuống dưới cùng và nhấn **Import** (hoặc **Thực hiện / Go**).
5. Sau vài giây, hệ thống sẽ báo nhập thành công toàn bộ cơ sở dữ liệu `apptintuc`.

---

### Cách 2: Sử dụng dòng lệnh MySQL CLI (Terminal / PowerShell / CMD)
Mở cửa sổ dòng lệnh và chạy lệnh sau:
```bash
mysql -u root -p < "D:/Code/code/PhpProject/database/sql/apptintuc_mysql.sql"
```
*(Nếu tài khoản `root` không đặt mật khẩu, chỉ cần nhấn Enter khi được hỏi password).*

---

## ⚙️ 3. CẬU HÌNH LARAVEL ĐỂ KẾT NỐI MYSQL

Mở file `.env` ở thư mục gốc của dự án, tìm đến phần Database (khoảng dòng 23-28) và cập nhật:

```env
# Đổi từ DB_CONNECTION=sqlite sang:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=apptintuc
DB_USERNAME=root
DB_PASSWORD=
```
*(Lưu ý: Nếu mật khẩu MySQL của bạn khác rỗng, hãy điền vào `DB_PASSWORD` tương ứng).*

Sau khi sửa file `.env`, chạy lệnh xóa cache cấu hình trong terminal:
```bash
php artisan config:clear
```

Và khởi động lại máy chủ:
```bash
php artisan serve
```

---

## 🔑 4. DANH SÁCH TÀI KHOẢN MẪU CÓ SẴN TRONG FILE SQL

Mọi tài khoản mẫu đều đã được kích hoạt email (`verified`) và có mật khẩu mặc định là:
👉 **`password`**

| Vai trò (Role) | Họ và tên | Email đăng nhập | Mật khẩu |
| :--- | :--- | :--- | :--- |
| **Quản trị viên (Admin)** | Quản trị viên Demo | `admin@newshub.test` | `password` |
| **Tác giả (Author)** | Tác giả Demo | `author@newshub.test` | `password` |
| **Độc giả (Reader/User)** | Độc giả Demo | `reader@newshub.test` | `password` |

---

## 🔄 5. QUAY TRỞ LẠI DÙNG SQLITE (KHI CẦN NỘP BÀI / CHẠY ĐỘC LẬP)
Nếu giảng viên hoặc máy khác không có MySQL và muốn chạy độc lập không cần cài đặt phần mềm CSDL:
Chỉ cần mở lại file `.env` và sửa lại dòng:
```env
DB_CONNECTION=sqlite
```
Hệ thống sẽ tự động dùng lại file `database/database.sqlite` nhúng sẵn trong code!
