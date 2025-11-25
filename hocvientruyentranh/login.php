<?php
session_start();
require 'inc/config.php';
include 'inc/header.php';

// Nếu đã đăng nhập, chuyển về trang quản trị
if (isset($_SESSION['admin'])) {
    header('Location: upload.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = md5(trim($_POST['password']));

    $stmt = $mysqli->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['admin'] = $username;
        header('Location: upload.php');
        exit;
    } else {
        $error = "Sai tên đăng nhập hoặc mật khẩu!";
    }
}
?>

<div class="login-container" style="max-width:400px;margin:100px auto;padding:20px;border:1px solid #ddd;border-radius:10px;background:#fff;">
  <h2 style="text-align:center;">Đăng nhập Quản trị</h2>
  <?php if ($error): ?>
    <p style="color:red;text-align:center;"><?php echo $error; ?></p>
  <?php endif; ?>
  <form method="POST">
    <label>Tên đăng nhập:</label>
    <input type="text" name="username" required class="form-control" style="width:100%;margin-bottom:10px;padding:8px;">
    <label>Mật khẩu:</label>
    <input type="password" name="password" required class="form-control" style="width:100%;margin-bottom:15px;padding:8px;">
    <button type="submit" style="width:100%;padding:10px;background:#007bff;color:#fff;border:none;border-radius:5px;">Đăng nhập</button>
  </form>
</div>

<?php include 'inc/footer.php'; ?>
