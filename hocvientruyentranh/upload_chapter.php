<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
// Kết nối cấu hình CSDL và include phần header giao diện
require 'inc/config.php';
include 'inc/header.php';

// --- Lấy danh sách truyện từ bảng comics ---
$comics = [];
$res = $mysqli->query("SELECT id, title FROM comics ORDER BY title ASC");
while ($row = $res->fetch_assoc()) {
    $comics[] = $row; // Lưu từng truyện vào mảng $comics
}

// --- Nếu có comic_id truyền qua URL, thì giữ lại để chọn sẵn trong form ---
$selected_comic_id = isset($_GET['comic_id']) ? (int)$_GET['comic_id'] : 0;

// --- Chuẩn bị biến thông báo lỗi và thành công ---
$errors = [];
$success = '';

// --- Xử lý khi người dùng nhấn nút "Upload chương" ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Lấy dữ liệu từ form
    $comic_id = (int)($_POST['comic_id'] ?? 0);
    $chapter_index = trim($_POST['chapter_index'] ?? '');
    $chapter_title = trim($_POST['chapter_title'] ?? '');
    $upload_dir_base = 'assets/uploads/'; // Thư mục gốc chứa ảnh

    // --- Kiểm tra dữ liệu nhập vào ---
    if ($comic_id <= 0) $errors[] = "Bạn phải chọn truyện.";
    if ($chapter_index === '') $errors[] = "Bạn phải nhập số chương.";
    if (empty($_FILES['images']['name'][0])) $errors[] = "Bạn cần chọn ít nhất 1 ảnh chương.";

    // --- Nếu không có lỗi, tiến hành xử lý ---
    if (empty($errors)) {
        // Lấy tên truyện từ CSDL để tạo slug thư mục
        $res = $mysqli->query("SELECT title FROM comics WHERE id = $comic_id");
        $comic = $res->fetch_assoc();

        // Chuyển tên truyện thành slug (chữ thường, thay khoảng trắng bằng dấu "-")
        $slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower($comic['title']));

        // Đường dẫn thư mục chứa ảnh chương: assets/uploads/tên-truyện/ch1/
        $chapter_folder = "{$upload_dir_base}{$slug}/ch{$chapter_index}/";
        $server_path = __DIR__ . '/' . $chapter_folder; // Đường dẫn thực trên máy chủ

        // Tạo thư mục nếu chưa tồn tại
        if (!is_dir($server_path)) mkdir($server_path, 0755, true);

        // --- Upload tất cả các ảnh ---
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp']; // Các định dạng được phép
        foreach ($_FILES['images']['name'] as $key => $name) {
            $tmp = $_FILES['images']['tmp_name'][$key]; // Đường dẫn file tạm
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION)); // Lấy phần mở rộng file

            // Nếu file không hợp lệ (định dạng khác), bỏ qua
            if (!in_array($ext, $allowed)) continue;

            // Tạo đường dẫn đích để lưu ảnh
            $target = $server_path . basename($name);

            // Di chuyển ảnh từ thư mục tạm sang thư mục đích
            move_uploaded_file($tmp, $target);
        }

        // --- Lưu thông tin chương vào bảng 'chapters' trong CSDL ---
        $stmt = $mysqli->prepare("INSERT INTO chapters (comic_id, chapter_index, title, file_path) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $comic_id, $chapter_index, $chapter_title, $chapter_folder);

        // Thực thi truy vấn
        if ($stmt->execute()) {
            $success = "✅ Upload chương thành công!";
        } else {
            $errors[] = "Lỗi khi lưu vào CSDL: " . $stmt->error;
        }
    }
}
?>

<!-- ================================================
     PHẦN HIỂN THỊ GIAO DIỆN TRONG MAIN
================================================ -->
<main class="main">
  <div class="wrap">
<!-- Giao diện hiển thị -->
<h2>Upload chương truyện</h2>

<!-- Nếu có lỗi, hiển thị danh sách lỗi -->
<?php if ($errors): ?>
<div class="errors">
  <?php foreach ($errors as $e) echo "<div>" . htmlspecialchars($e) . "</div>"; ?>
</div>
<?php endif; ?>

<!-- Nếu upload thành công, hiển thị thông báo -->
<?php if ($success): ?>
<div class="success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<!-- Form upload chương -->
    <form action="" method="post" enctype="multipart/form-data">
  <!-- Chọn truyện -->
  <label>Chọn truyện</label>
  <select name="comic_id" required>
    <option value="">-- Chọn truyện --</option>
    <?php foreach ($comics as $comic): ?>
      <option value="<?php echo $comic['id']; ?>"
        <?php 
            // Giữ lại lựa chọn sau khi submit hoặc khi truyền comic_id qua URL
            if (
            (!empty($_POST['comic_id']) && $_POST['comic_id'] == $comic['id'])
        || $selected_comic_id == $comic['id']
        ) echo 'selected';
        ?>>
        <?php echo htmlspecialchars($comic['title']); ?>
      </option>
    <?php endforeach; ?>
  </select>

  <!-- Nhập số chương -->
  <label>Số chương (ví dụ: 1, 2, 3...)</label>
  <input type="text" name="chapter_index" placeholder="Nhập số chương" required>

  <!-- Nhập tiêu đề chương -->
  <label>Tên chương</label>
  <input type="text" name="chapter_title" placeholder="Nhập tiêu đề chương">

  <!-- Chọn ảnh (nhiều ảnh) -->
  <label>Ảnh chương (có thể chọn nhiều ảnh)</label>
  <input type="file" name="images[]" multiple accept="image/*" required>

  <!-- Nút gửi form -->
  <button type="submit">Upload chương</button>
    </form>
  </div>
</main> 

<?php include 'inc/footer.php'; ?>
