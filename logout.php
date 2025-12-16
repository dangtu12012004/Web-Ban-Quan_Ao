<?php
session_start();
session_destroy();

// Xóa Cookie trên toàn bộ domain ('/')
setcookie('tendangnhap', '', time() - 3600, '/');
setcookie('user', '', time() - 3600, '/');
setcookie('role', '', time() - 3600, '/');
setcookie('id_user', '', time() - 3600, '/');

// SỬA DÒNG NÀY: Chuyển hướng về file login.php nằm NGANG HÀNG
header('Location: login.php'); 
die();
?>