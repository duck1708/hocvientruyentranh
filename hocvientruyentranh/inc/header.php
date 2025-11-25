<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>HocVienTruyenTranh</title>
  <link rel="stylesheet" href="/hocvientruyentranh/assets/css/style.css">
</head>
<body>
<header class="site-header">
  <div class="wrap header-flex">
    <h1><a href="/hocvientruyentranh/index.php">HocVienTruyenTranh</a></h1>

    <nav>
      <a href="/hocvientruyentranh/index.php">Trang chủ</a> |
      <a href="/hocvientruyentranh/list.php">Tất cả truyện</a> |
      <a href="/hocvientruyentranh/upload.php">Upload truyện</a>
    </nav>

    <!-- 🔍 Ô tìm kiếm -->
    <form action="/hocvientruyentranh/list.php" method="get" class="search">
      <input type="text" name="q" placeholder="Tìm truyện..." 
             value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
      <button>Tìm</button>
    </form>

    <!-- 🔐 Admin info -->
    <?php if (isset($_SESSION['admin'])): ?>
      <div class="admin-info">
        Xin chào, <strong><?php echo htmlspecialchars($_SESSION['admin']); ?></strong> |
        <a href="/hocvientruyentranh/logout.php">Đăng xuất</a>
      </div>
    <?php endif; ?>
  </div>
</header>
<main class="wrap">
