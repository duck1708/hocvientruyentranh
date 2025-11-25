-- Tạo database
CREATE DATABASE IF NOT EXISTS hocvientruyentranh CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hocvientruyentranh;

-- Bảng truyện
CREATE TABLE IF NOT EXISTS comics (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  author VARCHAR(255) DEFAULT '',
  description TEXT,
  cover VARCHAR(255) DEFAULT '', -- đường dẫn file cover trong assets/uploads
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Bảng chương (mỗi chương là 1 record; file_path trỏ tới folder chứa ảnh/chapter)
CREATE TABLE IF NOT EXISTS chapters (
  id INT AUTO_INCREMENT PRIMARY KEY,
  comic_id INT NOT NULL,
  chapter_index VARCHAR(50) DEFAULT '',
  title VARCHAR(255) DEFAULT '',
  file_path VARCHAR(255) DEFAULT '', -- ví dụ: assets/uploads/comic-1/chapter-1/
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (comic_id) REFERENCES comics(id) ON DELETE CASCADE
);

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE,
  password VARCHAR(255)
);

-- Sample data (tối giản)
INSERT INTO comics (title, author, description, cover) VALUES
('Học Viện Anh Hùng', 'Kohei Horikoshi', 'Câu chuyện về những anh hùng trẻ tuổi đang học cách cứu thế giới.', 'assets/uploads/cover-hero.jpg'),
('Thám Tử Lừng Danh Conan', 'Gosho Aoyama', 'Thiên tài thám tử nhỏ tuổi phá giải những vụ án hóc búa.', 'assets/uploads/cover-conan.jpg'),
('One Piece', 'Eiichiro Oda', 'Hành trình của Luffy và băng Mũ Rơm đi tìm kho báu One Piece.', 'assets/uploads/cover-onepiece.jpg'),
('Naruto', 'Masashi Kishimoto', 'Hành trình trở thành Hokage của cậu bé ninja Naruto.', 'assets/uploads/cover-naruto.jpg'),
('Attack on Titan', 'Hajime Isayama', 'Cuộc chiến giữa loài người và Titan khổng lồ.', 'assets/uploads/cover-aot.jpg'),
('Kimetsu no Yaiba', 'Koyoharu Gotouge', 'Câu chuyện về Tanjiro diệt quỷ để cứu em gái.', 'assets/uploads/cover-knyn.jpg'),
('Tokyo Ghoul', 'Sui Ishida', 'Một thanh niên trở thành bán quỷ sau tai nạn định mệnh.', 'assets/uploads/cover-tokyoghoul.jpg'),
('Doraemon', 'Fujiko F. Fujio', 'Chú mèo máy đến từ tương lai giúp đỡ Nobita.', 'assets/uploads/cover-doraemon.jpg'),
('Black Clover', 'Yūki Tabata', 'Hai đứa trẻ mồ côi khao khát trở thành Ma pháp vương.', 'assets/uploads/cover-blackclover.jpg'),
('Chainsaw Man', 'Tatsuki Fujimoto', 'Thanh niên Denji cùng cưa máy chiến đấu với quỷ dữ.', 'assets/uploads/cover-chainsaw.jpg');

INSERT INTO chapters (comic_id, chapter_index, title, file_path) VALUES
(1, 'Chương 1', 'Khởi đầu của anh hùng', 'assets/uploads/hero/ch1/'),
(1, 'Chương 2', 'Bài kiểm tra đầu tiên', 'assets/uploads/hero/ch2/'),
(1, 'Chương 3', 'Cuộc chiến tại học viện', 'assets/uploads/hero/ch3/'),

(2, 'Chương 1', 'Án mạng trong phòng kín', 'assets/uploads/conan/ch1/'),
(2, 'Chương 2', 'Manh mối bí ẩn', 'assets/uploads/conan/ch2/'),
(2, 'Chương 3', 'Sự thật được hé lộ', 'assets/uploads/conan/ch3/'),

(3, 'Chương 1', 'Ra khơi', 'assets/uploads/onepiece/ch1/'),
(3, 'Chương 2', 'Zoro gia nhập', 'assets/uploads/onepiece/ch2/'),
(3, 'Chương 3', 'Trận chiến đầu tiên', 'assets/uploads/onepiece/ch3/'),

(4, 'Chương 1', 'Cậu bé trong làng Lá', 'assets/uploads/naruto/ch1/'),
(4, 'Chương 2', 'Đội 7 ra đời', 'assets/uploads/naruto/ch2/'),
(4, 'Chương 3', 'Cuộc thi Chunin', 'assets/uploads/naruto/ch3/'),

(5, 'Chương 1', 'Bức tường khổng lồ', 'assets/uploads/aot/ch1/'),
(5, 'Chương 2', 'Titan xâm nhập', 'assets/uploads/aot/ch2/'),
(5, 'Chương 3', 'Phản công', 'assets/uploads/aot/ch3/'),

(6, 'Chương 1', 'Gia đình Tanjiro', 'assets/uploads/knyn/ch1/'),
(6, 'Chương 2', 'Gặp gỡ Urokodaki', 'assets/uploads/knyn/ch2/'),
(6, 'Chương 3', 'Bài kiểm tra Sát Quỷ', 'assets/uploads/knyn/ch3/'),

(7, 'Chương 1', 'Tai nạn định mệnh', 'assets/uploads/tokyoghoul/ch1/'),
(7, 'Chương 2', 'Sự thay đổi của Kaneki', 'assets/uploads/tokyoghoul/ch2/'),
(7, 'Chương 3', 'Cuộc chiến với ghoul khác', 'assets/uploads/tokyoghoul/ch3/'),

(8, 'Chương 1', 'Doraemon xuất hiện', 'assets/uploads/doraemon/ch1/'),
(8, 'Chương 2', 'Cánh cửa thần kỳ', 'assets/uploads/doraemon/ch2/'),
(8, 'Chương 3', 'Chiếc bánh chưng thời gian', 'assets/uploads/doraemon/ch3/'),

(9, 'Chương 1', 'Giấc mơ Ma pháp vương', 'assets/uploads/blackclover/ch1/'),
(9, 'Chương 2', 'Cuộc thi tuyển Ma pháp kỵ sĩ', 'assets/uploads/blackclover/ch2/'),
(9, 'Chương 3', 'Asta đối đầu Yuno', 'assets/uploads/blackclover/ch3/'),

(10, 'Chương 1', 'Denji và Pochita', 'assets/uploads/chainsaw/ch1/'),
(10, 'Chương 2', 'Thợ săn quỷ mới', 'assets/uploads/chainsaw/ch2/'),
(10, 'Chương 3', 'Sức mạnh Chainsaw Man', 'assets/uploads/chainsaw/ch3/');

INSERT INTO users (username, password)
VALUES ('admin', MD5('123456'));
