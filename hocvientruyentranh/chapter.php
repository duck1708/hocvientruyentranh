<?php
// ==== 1. NẠP CẤU HÌNH & HEADER ====
require 'inc/config.php';
include 'inc/header.php';

// ==== 2. LẤY ID CHƯƠNG TỪ URL ====
$chapter_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$chapter_id) {
    echo "<p>Chương không tồn tại.</p>";
    include 'inc/footer.php';
    exit;
}

// ==== 3. LẤY THÔNG TIN CHƯƠNG & TRUYỆN ====
$sql = "SELECT c.id AS chapter_id, c.chapter_index, c.title AS chapter_title, c.file_path,
        m.id AS comic_id, m.title AS comic_title
        FROM chapters c
        JOIN comics m ON c.comic_id = m.id
        WHERE c.id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $chapter_id);
$stmt->execute();
$res = $stmt->get_result();
$chapter = $res->fetch_assoc();
if (!$chapter) {
    echo "<p>Không tìm thấy chương này.</p>";
    include 'inc/footer.php';
    exit;
}

// ==== 4. LẤY DANH SÁCH TẤT CẢ CÁC CHƯƠNG ====
$stmt_all = $mysqli->prepare("SELECT id, chapter_index, title FROM chapters WHERE comic_id = ? ORDER BY id ASC");
$stmt_all->bind_param("i", $chapter['comic_id']);
$stmt_all->execute();
$res_all = $stmt_all->get_result();
$all_chapters = $res_all->fetch_all(MYSQLI_ASSOC);

// ==== 5. XÁC ĐỊNH CHƯƠNG TRƯỚC & SAU ====
$prev_sql = "SELECT id FROM chapters WHERE comic_id = ? AND id < ? ORDER BY id DESC LIMIT 1";
$next_sql = "SELECT id FROM chapters WHERE comic_id = ? AND id > ? ORDER BY id ASC LIMIT 1";

$stmt_prev = $mysqli->prepare($prev_sql);
$stmt_prev->bind_param("ii", $chapter['comic_id'], $chapter_id);
$stmt_prev->execute();
$prev_res = $stmt_prev->get_result();
$prev_id = $prev_res->fetch_assoc()['id'] ?? null;

$stmt_next = $mysqli->prepare($next_sql);
$stmt_next->bind_param("ii", $chapter['comic_id'], $chapter_id);
$stmt_next->execute();
$next_res = $stmt_next->get_result();
$next_id = $next_res->fetch_assoc()['id'] ?? null;

// ==== 6. ĐƯỜNG DẪN THƯ MỤC ẢNH ====
$web_folder = rtrim($chapter['file_path'], "/\\") . '/';
$server_folder = __DIR__ . '/' . $web_folder;
if (!is_dir($server_folder)) {
    echo "<p>Không tìm thấy thư mục ảnh chương tại: <code>$server_folder</code></p>";
    include 'inc/footer.php';
    exit;
}

// ==== 7. LẤY DANH SÁCH ẢNH ====
$images = glob($server_folder . '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
sort($images, SORT_NATURAL);
?>

<main class="main">
  <div class="wrap">
    <article class="chapter-view">
      <!-- ==== Header hiển thị thông tin truyện + chương ==== -->
      <div class="chapter-header">
        <h2><?php echo htmlspecialchars($chapter['comic_title']); ?></h2>
        <p><strong><?php echo htmlspecialchars($chapter['chapter_index']); ?>:</strong> 
           <?php echo htmlspecialchars($chapter['chapter_title']); ?></p>

        <!-- Thanh điều hướng trên đầu -->
        <div class="chapter-nav">
          <a class="btn" href="view.php?id=<?php echo $chapter['comic_id']; ?>">⬅ Quay lại truyện</a>

          <div class="chapter-nav-right">
            <?php if ($prev_id): ?>
              <a class="btn" href="chapter.php?id=<?php echo $prev_id; ?>">⬅ Chương trước</a>
            <?php endif; ?>

            <select id="chapterSelect" class="chapter-select">
              <?php foreach ($all_chapters as $ch): ?>
                <option value="chapter.php?id=<?php echo $ch['id']; ?>" 
                  <?php if ($ch['id'] == $chapter_id) echo 'selected'; ?>>
                  <?php echo htmlspecialchars($ch['chapter_index']); ?>
                </option>
              <?php endforeach; ?>
            </select>

            <?php if ($next_id): ?>
              <a class="btn" href="chapter.php?id=<?php echo $next_id; ?>">Chương sau ➡</a>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- ==== Nội dung chương: ảnh ==== -->
      <div class="chapter-content">
        <?php
        if (empty($images)) {
            echo "<p>Chưa có ảnh nào trong chương này.</p>";
        } else {
            foreach ($images as $img_path) {
                $img_url = str_replace(__DIR__ . '/', '', $img_path);
                echo '<img src="' . htmlspecialchars($img_url) . '" alt="" class="chapter-img">';
            }
        }
        ?>
      </div>

      <!-- ==== Thanh điều hướng dưới ==== -->
      <div class="chapter-nav">
        <a class="btn" href="view.php?id=<?php echo $chapter['comic_id']; ?>">⬅ Quay lại truyện</a>
        <div class="chapter-nav-right">
          <?php if ($prev_id): ?>
            <a class="btn" href="chapter.php?id=<?php echo $prev_id; ?>">⬅ Chương trước</a>
          <?php endif; ?>

          <select id="chapterSelectBottom" class="chapter-select">
            <?php foreach ($all_chapters as $ch): ?>
              <option value="chapter.php?id=<?php echo $ch['id']; ?>" 
                <?php if ($ch['id'] == $chapter_id) echo 'selected'; ?>>
                <?php echo htmlspecialchars($ch['chapter_index']); ?>
              </option>
            <?php endforeach; ?>
          </select>

          <?php if ($next_id): ?>
            <a class="btn" href="chapter.php?id=<?php echo $next_id; ?>">Chương sau ➡</a>
          <?php endif; ?>
        </div>
      </div>
    </article>
  </div>
</main>

<script>
document.querySelectorAll('.chapter-select').forEach(sel => {
  sel.addEventListener('change', e => {
    window.location.href = e.target.value;
  });
});
</script>

<?php include 'inc/footer.php'; ?>
