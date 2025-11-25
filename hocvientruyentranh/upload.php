<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

// ================================================
// KẾT NỐI & GIAO DIỆN CHUNG
// ================================================
require 'inc/config.php';
include 'inc/header.php';

// ================================================
// KHỞI TẠO BIẾN KIỂM TRA
// ================================================
$errors = [];
$success = '';

// ================================================
// KIỂM TRA NẾU NGƯỜI DÙNG GỬI FORM (POST)
// ================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $desc = trim($_POST['description'] ?? '');

    if ($title === '') $errors[] = "Bạn phải nhập tiêu đề.";

    // ================================================
    // XỬ LÝ ẢNH BÌA
    // ================================================
    $coverPath = '';
    if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
        $uploaddir = 'assets/uploads/';
        if (!is_dir($uploaddir)) mkdir($uploaddir, 0755, true);
        $ext = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
        $filename = 'cover-' . time() . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
        $dest = $uploaddir . $filename;
        if (move_uploaded_file($_FILES['cover']['tmp_name'], $dest)) {
            $coverPath = $dest;
        } else {
            $errors[] = "Không lưu được file cover.";
        }
    }

    // ================================================
    // LƯU VÀO DATABASE NẾU KHÔNG LỖI
    // ================================================
    if (empty($errors)) {
        $stmt = $mysqli->prepare("
            INSERT INTO comics (title, author, description, cover)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param('ssss', $title, $author, $desc, $coverPath);
        if ($stmt->execute()) {
            $success = "Upload thành công!";
        } else {
            $errors[] = "Lỗi khi lưu vào CSDL.";
        }
    }
}
?>

<!-- ================================================
     PHẦN HIỂN THỊ GIAO DIỆN TRONG MAIN
================================================ -->
<main class="main">
  <div class="wrap">

    <h2>Upload truyện (công khai)</h2>

    <!-- Hiển thị danh sách lỗi -->
    <?php if ($errors): ?>
      <div class="errors">
        <?php foreach ($errors as $e) echo "<div>" . htmlspecialchars($e) . "</div>"; ?>
      </div>
    <?php endif; ?>

    <!-- Hiển thị thông báo thành công -->
    <?php if ($success): ?>
      <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <!-- FORM UPLOAD -->
    <form action="upload.php" method="post" enctype="multipart/form-data">
      <label>Tiêu đề<br>
        <input type="text" name="title" required>
      </label><br>

      <label>Tác giả<br>
        <input type="text" name="author">
      </label><br>

      <label>Mô tả<br>
        <textarea name="description" rows="5"></textarea>
      </label><br>

      <label>Cover (jpg/png)<br>
        <input type="file" name="cover" accept="image/*">
      </label><br>

      <button type="submit">Upload</button>
    </form>

  </div>
</main>

<?php include 'inc/footer.php'; ?>
