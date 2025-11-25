<?php
// Kết nối tới cơ sở dữ liệu (MySQL)
require 'inc/config.php';

// Chèn header HTML
include 'inc/header.php';

// --- Lấy ID truyện từ URL ---
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Kiểm tra nếu ID không hợp lệ
if (!$id) {
  echo "<p>Truyện không tồn tại.</p>";
  include 'inc/footer.php';
  exit;
}

// --- Lấy thông tin truyện ---
$stmt = $mysqli->prepare("
  SELECT id, title, author, description, cover, created_at 
  FROM comics 
  WHERE id = ?
");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$comic = $res->fetch_assoc();

if (!$comic) {
  echo "<p>Truyện không tồn tại.</p>";
  include 'inc/footer.php';
  exit;
}
?>

<main class="main">
  <div class="wrap">
    <!-- =================== Giao diện hiển thị chi tiết truyện =================== -->
    <article class="comic-detail">
      <!-- Tiêu đề truyện -->
      <h2><?php echo htmlspecialchars($comic['title']); ?></h2>

      <!-- Ảnh bìa truyện -->
      <img 
        src="<?php echo htmlspecialchars($comic['cover'] ?: 'assets/uploads/default-cover.jpg'); ?>" 
        style="max-width:300px; border-radius:10px;"
        alt="Ảnh bìa <?php echo htmlspecialchars($comic['title']); ?>"
      >

      <!-- Tác giả -->
      <p><strong>Tác giả:</strong> <?php echo htmlspecialchars($comic['author']); ?></p>

      <!-- Mô tả truyện -->
      <p><?php echo nl2br(htmlspecialchars($comic['description'])); ?></p>

      <!-- =================== Danh sách chương =================== -->
      <div class="chapter-header-line">
        <h3>Danh sách chương</h3>
        <a class="btn btn-small" href="upload_chapter.php?comic_id=<?php echo $comic['id']; ?>">➕ Thêm chương mới</a>
        <a class="btn btn-small" href="edit.php?id=<?php echo $comic['id']; ?>">✏️ Sửa thông tin</a>
      </div>

      <ul class="chapter-list">
      <?php
      $stmt2 = $mysqli->prepare("
        SELECT id, chapter_index, title, file_path 
        FROM chapters 
        WHERE comic_id = ? 
        ORDER BY id ASC
      ");
      $stmt2->bind_param('i', $id);
      $stmt2->execute();
      $res2 = $stmt2->get_result();

      if ($res2->num_rows === 0) {
        echo "<li>Chưa có chương nào.</li>";
      }

      while ($ch = $res2->fetch_assoc()):
      ?>
        <li>
          <a href="chapter.php?id=<?php echo $ch['id']; ?>">
            <?php echo htmlspecialchars($ch['chapter_index'] . ' - ' . $ch['title']); ?>
          </a>
          <a class="btn btn-small" href="edit_chapter.php?id=<?php echo $ch['id']; ?>">✏️ Sửa chương truyện</a>
        </li>
      <?php endwhile; ?>
      </ul>
    </article>
  </div>
</main>

<?php include 'inc/footer.php'; ?>
