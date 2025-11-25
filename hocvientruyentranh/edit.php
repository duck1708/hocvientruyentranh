<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require 'inc/config.php';
include 'inc/header.php';

// --- Lấy ID truyện cần sửa ---
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    echo "<p>Không tìm thấy truyện cần sửa.</p>";
    include 'inc/footer.php';
    exit;
}

// --- Lấy thông tin truyện hiện tại ---
$stmt = $mysqli->prepare("SELECT id, title, author, description, cover FROM comics WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$comic = $res->fetch_assoc();
if (!$comic) {
    echo "<p>Truyện không tồn tại.</p>";
    include 'inc/footer.php';
    exit;
}

// --- Xử lý khi người dùng nhấn nút Cập nhật ---
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $desc = trim($_POST['description'] ?? '');

    // Kiểm tra dữ liệu hợp lệ
    if ($title === '') $errors[] = "Vui lòng nhập tiêu đề truyện.";
    if ($author === '') $errors[] = "Vui lòng nhập tên tác giả.";

    // Nếu không có lỗi thì cập nhật
    if (empty($errors)) {
        $cover_path = $comic['cover']; // giữ nguyên ảnh cũ nếu không tải ảnh mới

        // Nếu người dùng chọn ảnh mới
        if (!empty($_FILES['cover']['name'])) {
            $upload_dir = 'assets/uploads/';
            $file_name = time() . '_' . basename($_FILES['cover']['name']);
            $target_path = $upload_dir . $file_name;

            // Di chuyển ảnh vào thư mục đích
            if (move_uploaded_file($_FILES['cover']['tmp_name'], $target_path)) {
                $cover_path = $target_path;
            } else {
                $errors[] = "Lỗi khi tải ảnh lên.";
            }
        }

        // Nếu không có lỗi upload → tiến hành cập nhật DB
        if (empty($errors)) {
            $stmt2 = $mysqli->prepare("UPDATE comics SET title = ?, author = ?, description = ?, cover = ? WHERE id = ?");
            $stmt2->bind_param('ssssi', $title, $author, $desc, $cover_path, $id);
            $stmt2->execute();

            $success = "✅ Cập nhật truyện thành công!";
            // Cập nhật lại thông tin hiển thị
            $comic['title'] = $title;
            $comic['author'] = $author;
            $comic['description'] = $desc;
            $comic['cover'] = $cover_path;
        }
    }
}
?>

<!-- =================== Giao diện form sửa truyện =================== -->
<main class="main">
  <div class="wrap">
<article class="comic-edit">
  <h2>✏️ Sửa thông tin truyện</h2>

  <!-- Hiển thị thông báo lỗi nếu có -->
  <?php if ($errors): ?>
    <div class="alert alert-error">
      <ul>
        <?php foreach ($errors as $e) echo "<li>$e</li>"; ?>
      </ul>
    </div>
  <?php endif; ?>

  <!-- Hiển thị thông báo thành công -->
  <?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
  <?php endif; ?>

  <form action="" method="POST" enctype="multipart/form-data" style="max-width:600px;">
    <label>Tiêu đề:</label><br>
    <input type="text" name="title" value="<?php echo htmlspecialchars($comic['title']); ?>" required><br><br>

    <label>Tác giả:</label><br>
    <input type="text" name="author" value="<?php echo htmlspecialchars($comic['author']); ?>" required><br><br>

    <label>Mô tả:</label><br>
    <textarea name="description" rows="6"><?php echo htmlspecialchars($comic['description']); ?></textarea><br><br>

    <label>Ảnh bìa hiện tại:</label><br>
    <img src="<?php echo htmlspecialchars($comic['cover'] ?: 'assets/uploads/default-cover.jpg'); ?>" 
         alt="Bìa truyện" 
         style="max-width:200px; border-radius:8px;"><br><br>

    <label>Chọn ảnh mới (nếu muốn đổi):</label><br>
    <input type="file" name="cover" accept="image/*"><br><br>

    <button type="submit" class="btn btn-primary">💾 Cập nhật truyện</button>
    <a href="view.php?id=<?php echo $comic['id']; ?>" class="btn btn-secondary">⬅ Quay lại</a>
  </form>
  </div>
</main>
</article>

<?php include 'inc/footer.php'; ?>
