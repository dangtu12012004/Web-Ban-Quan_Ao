<?php
session_start();
require_once('database/config.php');
require_once('database/dbhelper.php');
require_once('utils/utility.php');

// Nếu đã đăng nhập rồi thì chuyển hướng
if (isset($_COOKIE['tendangnhap'])) {
    header('Location: index.php');
    die();
}

$error = '';

if (!empty($_POST)) {
    $username = getPost('username');
    $password = getPost('password');

    // 1. KIỂM TRA ADMIN
    $sql = "SELECT * FROM tbl_admin WHERE tendangnhap = '$username' AND matkhau = '$password'";
    $admin = executeSingleResult($sql);

    if ($admin != null) {
        // Đăng nhập thành công quyền Admin
        setcookie('tendangnhap', $admin['tendangnhap'], time() + 30 * 24 * 60 * 60, '/');
        setcookie('user', $admin['tenadmin'], time() + 30 * 24 * 60 * 60, '/');
        setcookie('role', 'admin', time() + 30 * 24 * 60 * 60, '/');
        
        $_SESSION['submit'] = $admin['tendangnhap'];
        $_SESSION['role'] = 'admin'; 
        
        header('Location: index.php');
        die();
    }

    // 2. KIỂM TRA NHÂN VIÊN
    $sql = "SELECT * FROM tbl_staff WHERE tendangnhap = '$username'";
    $staff = executeSingleResult($sql);

    if ($staff != null && password_verify($password, $staff['matkhau'])) {
        // Đăng nhập thành công quyền Staff
        setcookie('tendangnhap', $staff['tendangnhap'], time() + 30 * 24 * 60 * 60, '/');
        setcookie('user', $staff['ten_staff'], time() + 30 * 24 * 60 * 60, '/');
        setcookie('role', 'staff', time() + 30 * 24 * 60 * 60, '/');
        
        $_SESSION['submit'] = $staff['tendangnhap'];
        $_SESSION['role'] = 'staff';

        header('Location: index.php'); 
        die();
    }

    // 3. KIỂM TRA KHÁCH HÀNG
    $password_md5 = md5($password);
    $sql = "SELECT * FROM tbl_dangky WHERE tendangnhap = '$username' AND matkhau = '$password_md5'";
    $customer = executeSingleResult($sql);

    if ($customer != null) {
        setcookie('tendangnhap', $customer['tendangnhap'], time() + 30 * 24 * 60 * 60, '/');
        setcookie('user', $customer['tenkhachhang'], time() + 30 * 24 * 60 * 60, '/');
        setcookie('role', 'customer', time() + 30 * 24 * 60 * 60, '/');
        setcookie('id_user', $customer['id_dangky'], time() + 30 * 24 * 60 * 60, '/');
        
        $_SESSION['role'] = 'customer';

        header('Location: index.php');
        die();
    }

    $error = 'Tài khoản hoặc mật khẩu không chính xác!';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Đăng Nhập - Shop Quần Áo</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <style>
        body { background-color: #f8f9fa; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .login-form { width: 400px; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0px 0px 20px 0px rgba(0,0,0,0.1); }
        .login-form h2 { margin-bottom: 30px; text-align: center; font-weight: bold; color: #333; }
    </style>
</head>
<body>
    <div class="login-form">
        <h2>ĐĂNG NHẬP</h2>
        <?php if (!empty($error)) { echo "<div class='alert alert-danger text-center'>$error</div>"; } ?>
        
        <form action="" method="POST">
            <div class="form-group">
                <label for="username">Tên đăng nhập:</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Nhập tên đăng nhập" required>
            </div>
            <div class="form-group">
                <label for="pwd">Mật khẩu:</label>
                <input type="password" class="form-control" id="pwd" name="password" placeholder="Nhập mật khẩu" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block" style="background-color: #333; border-color: #333;">Đăng Nhập</button>
            
            <div class="text-center mt-3">
                <a href="dangky.php" style="color: #333;">Chưa có tài khoản? Đăng ký ngay</a>
                <br>
                <a href="index.php" style="color: #007bff; font-size: 14px;">Quay lại trang chủ</a>
            </div>
        </form>
    </div>
</body>
</html>