<?php
require 'inc/config.php';
include 'inc/header.php';

// Lấy 10 truyện mới nhất
$sql = "SELECT id, title, author, description, cover, created_at FROM comics ORDER BY created_at DESC LIMIT 10";
$res = $mysqli->query($sql);
?>
<h2>Truyện mới nhất</h2>
<div class="comic-grid">
<?php while ($row = $res->fetch_assoc()): ?>
  <div class="card">
    <a href="view.php?id=<?php echo $row['id']; ?>">
      <img src="<?php echo htmlspecialchars($row['cover'] ?: 'assets/uploads/default-cover.jpg'); ?>" alt="">
    </a>
    <h3><a href="view.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['title']); ?></a></h3>
    <div class="meta"><?php echo htmlspecialchars($row['author']); ?> — <?php echo date('d/m/Y', strtotime($row['created_at'])); ?></div>
    <p><?php echo htmlspecialchars(mb_substr($row['description'],0,120)); ?>...</p>
  </div>
<?php endwhile; ?>
</div>

<?php include 'inc/footer.php'; ?>
