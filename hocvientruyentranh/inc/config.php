<?php
// config.php
$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = ''; // nếu XAMPP mặc định là rỗng
$DB_NAME = 'hocvientruyentranh';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    die("Lỗi kết nối DB: " . $mysqli->connect_error);
}
$mysqli->set_charset("utf8mb4");
