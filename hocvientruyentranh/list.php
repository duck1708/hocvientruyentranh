<?php
require 'inc/config.php';
include 'inc/header.php';

$q = isset($_GET['q']) ? $mysqli->real_escape_string(trim($_GET['q'])) : '';
$page = isset($_GET['page']) ? max(1,(int)$_GET['page']) : 1;
$perpage = 12;
$offset = ($page-1)*$perpage;

$where = "1";
if ($q !== '') {
  $where = "title LIKE '%{$q}%' OR author LIKE '%{$q}%'";
}

// tổng số
$totalRes = $mysqli->query("SELECT COUNT(*) AS cnt FROM comics WHERE {$where}");
$total = $totalRes->fetch_assoc()['cnt'];
$pages = ceil($total / $perpage);

$sql = "SELECT id, title, author, cover, created_at FROM comics WHERE {$where} ORDER BY created_at DESC LIMIT {$perpage} OFFSET {$offset}";
$res = $mysqli->query($sql);
?>
<h2>Tất cả truyện <?php if($q) echo " - Kết quả tìm: ".htmlspecialchars($q); ?></h2>

<main class="main">
  <div class="wrap">
<div class="comic-grid">
<?php while ($row = $res->fetch_assoc()): ?>
  <div class="card">
    <a href="view.php?id=<?php echo $row['id']; ?>">
      <img src="<?php echo htmlspecialchars($row['cover'] ?: 'assets/uploads/default-cover.jpg'); ?>" alt="">
    </a>
    <h3><a href="view.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['title']); ?></a></h3>
    <div class="meta"><?php echo htmlspecialchars($row['author']); ?></div>
  </div>
<?php endwhile; ?>
</div>

<div class="pager">
<?php if ($pages > 1): ?>
  <?php for ($p=1;$p<=$pages;$p++): ?>
    <?php if ($p == $page): ?>
      <strong><?php echo $p; ?></strong>
    <?php else: ?>
      <a href="?q=<?php echo urlencode($q); ?>&page=<?php echo $p; ?>"><?php echo $p; ?></a>
    <?php endif; ?>
  <?php endfor; ?>
<?php endif; ?>
</div>
  </div>
</main>
<?php include 'inc/footer.php'; ?>
