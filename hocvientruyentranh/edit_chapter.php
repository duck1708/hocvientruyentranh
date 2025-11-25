<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require 'inc/config.php';
include 'inc/header.php';

// --- Lấy ID chương từ URL ---
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    echo "<p>Không tìm thấy chương cần sửa.</p>";
    include 'inc/footer.php';
    exit;
}

// --- Lấy thông tin chương ---
$stmt = $mysqli->prepare("
  SELECT c.id, c.chapter_index, c.title, c.file_path, c.comic_id, m.title AS comic_title
  FROM chapters c
  JOIN comics m ON c.comic_id = m.id
  WHERE c.id = ?
");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$chapter = $res->fetch_assoc();

if (!$chapter) {
    echo "<p>Chương không tồn tại.</p>";
    include 'inc/footer.php';
    exit;
}

$errors = [];
$success = '';

// --- Xử lý khi người dùng nhấn nút cập nhật ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $chapter_index = trim($_POST['chapter_index'] ?? '');
    $title = trim($_POST['title'] ?? '');

    if ($chapter_index === '' || !is_numeric($chapter_index)) {
        $errors[] = "Vui lòng nhập số thứ tự chương hợp lệ (chỉ chứa số).";
    }
    if ($title === '') {
        $errors[] = "Vui lòng nhập tiêu đề chương.";
    }

    if (empty($errors)) {
        $file_path = $chapter['file_path']; // giữ nguyên file cũ nếu không tải mới

        // Nếu người dùng chọn file chương mới
        if (!empty($_FILES['chapter_file']['name'])) {
            $upload_dir = 'assets/chapters/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

            $file_name = time() . '_' . basename($_FILES['chapter_file']['name']);
            $target_path = $upload_dir . $file_name;

            // Di chuyển file vào thư mục đích
            if (move_uploaded_file($_FILES['chapter_file']['tmp_name'], $target_path)) {
                $file_path = $target_path;
            } else {
                $errors[] = "Lỗi khi tải file chương lên.";
            }
        }

        // Nếu không có lỗi upload
        if (empty($errors)) {
            $stmt2 = $mysqli->prepare("UPDATE chapters SET chapter_index = ?, title = ?, file_path = ? WHERE id = ?");
            $stmt2->bind_param('issi', $chapter_index, $title, $file_path, $id);
            $stmt2->execute();

            $success = "✅ Cập nhật chương thành công!";
            // Cập nhật lại thông tin hiện tại để hiển thị
            $chapter['chapter_index'] = $chapter_index;
            $chapter['title'] = $title;
            $chapter['file_path'] = $file_path;
        }
    }
}
?>

<!-- =================== Giao diện form sửa chương =================== -->
<main class="main">
  <div class="wrap">
     
<article class="chapter-edit">
  <h2>✏️ Sửa chương</h2>
  <p><strong>Thuộc truyện:</strong> 
    <a href="comic.php?id=<?php echo $chapter['comic_id']; ?>">
      <?php echo htmlspecialchars($chapter['comic_title']); ?>
    </a>
  </p>

  <!-- Thông báo lỗi -->
  <?php if ($errors): ?>
    <div class="alert alert-error">
      <ul>
        <?php foreach ($errors as $e) echo "<li>$e</li>"; ?>
      </ul>
    </div>
  <?php endif; ?>

  <!-- Thông báo thành công -->
  <?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
  <?php endif; ?>

  <form action="" method="POST" enctype="multipart/form-data" style="max-width:600px;">
    <label>Số chương:</label><br>
    <input type="number" name="chapter_index" value="<?php echo htmlspecialchars($chapter['chapter_index']); ?>" required><br><br>

    <label>Tiêu đề chương:</label><br>
    <input type="text" name="title" value="<?php echo htmlspecialchars($chapter['title']); ?>" required><br><br>

    <label>File chương hiện tại:</label><br>
    <?php if ($chapter['file_path']): ?>
        <a href="<?php echo htmlspecialchars($chapter['file_path']); ?>" target="_blank">
          📄 Xem file hiện tại
        </a><br><br>
    <?php else: ?>
        <p><i>Chưa có file chương.</i></p>
    <?php endif; ?>

    <label>Tải file chương mới (nếu muốn thay):</label><br>
    <input type="file" name="chapter_file" accept=".pdf,.zip,.jpg,.png"><br><br>

    <button type="submit" class="btn btn-primary">💾 Cập nhật chương</button>
    <a href="view.php?id=<?php echo $chapter['comic_id']; ?>" class="btn btn-secondary">⬅ Quay lại truyện</a>
  </form>
</article>
    <!-- Nội dung trang ở đây -->
  </div>
</main>
<?php include 'inc/footer.php'; ?>
