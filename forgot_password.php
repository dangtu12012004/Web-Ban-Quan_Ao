<?php
session_start();
require_once('database/config.php');
require_once('database/dbhelper.php');
require_once('utils/utility.php');
require_once('utils/send_mail.php');

$error = '';
$success = '';

if (!empty($_POST)) {
    $email = getPost('email');

    if (empty($email)) {
        $error = 'Vui lòng nhập Email!';
    } else {
        // Kiểm tra Email có tồn tại trong bảng Khách hàng không
        $sql = "SELECT * FROM tbl_dangky WHERE email = '$email'";
        $user = executeSingleResult($sql);

        if ($user != null) {
            // 1. Tạo Token ngẫu nhiên
            $token = bin2hex(random_bytes(32)); // Token dài 64 ký tự
            
            // 2. Thiết lập thời gian hết hạn 
            $expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));

            // 3. Lưu Token và Expiry vào DB
            $sql_update = "UPDATE tbl_dangky SET reset_token = '$token', reset_expiry = '$expiry' WHERE email = '$email'";
            execute($sql_update);

            $resetLink = "http://localhost/Web/reset_password.php?email=" . $email . "&token=" . $token;

            // 5. Gửi Email
            $subject = 'Khôi phục mật khẩu - Dirty Coin';
            $content = "Chào bạn,<br><br>Ai đó đã yêu cầu đặt lại mật khẩu cho tài khoản này.<br>
                        Vui lòng nhấn vào link dưới đây để đặt lại mật khẩu (Link hết hạn sau 15 phút):<br><br>
                        <a href='$resetLink' style='background:#333;color:#fff;padding:10px 20px;text-decoration:none;border-radius:5px;'>ĐẶT LẠI MẬT KHẨU</a>
                        <br><br>Hoặc copy link này: $resetLink";
            
            $send = sendEmail($email, $subject, $content);
            
            if ($send) {
                $success = 'Link khôi phục đã được gửi vào Email. Vui lòng kiểm tra hộp thư.';
            } else {
                $error = 'Lỗi gửi mail. Vui lòng thử lại sau.';
            }
        } else {
            $error = 'Email này không tồn tại trong hệ thống!';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quên Mật Khẩu</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <style>
        body { background: #f8f9fa; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .login-form { width: 400px; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="login-form">
        <h3 class="text-center mb-4">QUÊN MẬT KHẨU</h3>
        <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <?php if (!empty($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Nhập Email đăng ký:</label>
                <input type="email" class="form-control" name="email" required placeholder="example@gmail.com">
            </div>
            <button type="submit" class="btn btn-primary btn-block" style="background:#333;border:none;">Gửi Link Reset</button>
            <div class="text-center mt-3"><a href="login.php">Quay lại Đăng nhập</a></div>
        </form>
    </div>
</body>
</html>