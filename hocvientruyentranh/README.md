# HocVienTruyenTranh

Mô tả: website đọc truyện tranh mã nguồn mở, chạy trên XAMPP (PHP + MySQL).

## Setup nhanh
1. Copy thư mục `hocvientruyentranh` vào `C:\xampp\htdocs\`.
2. Start Apache & MySQL bằng XAMPP Control Panel.
3. Mở `http://localhost/phpmyadmin` -> tạo database `hocvientruyentranh` và import file `db.sql`.
4. Cập nhật `inc/config.php` nếu cần username/password khác.
5. Truy cập `http://localhost/hocvientruyentranh/index.php`.

## Ghi chú
- Thư mục `assets/uploads` dùng để chứa cover và folder chương.
- Để upload chương (các hình trang), bạn có thể tạo cấu trúc folder thủ công và đặt ảnh vào `assets/uploads/(tên-manga)/(tên-Chapter)/` và cập nhật `chapters.file_path`.
- Tên đăng nhập admin và mật khẩu 123456 

