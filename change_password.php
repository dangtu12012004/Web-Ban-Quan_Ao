<?php
require_once('database/config.php');
require_once('database/dbhelper.php');
require_once('utils/utility.php');

// Kiểm tra đăng nhập
if (!isset($_COOKIE['tendangnhap'])) {
    header('Location: login.php');
    die();
}

$user = $_COOKIE['tendangnhap'];
$role = $_COOKIE['role']; // admin, staff, customer
$error = '';
$success = '';

if (!empty($_POST)) {
    $old_pass = getPost('old_pass');
    $new_pass = getPost('new_pass');
    $confirm_pass = getPost('confirm_pass');

    if ($new_pass != $confirm_pass) {
        $error = "Mật khẩu mới và xác nhận mật khẩu không khớp!";
    } 
    // THÊM: Kiểm tra mật khẩu mới có trùng với mật khẩu cũ không (trước khi xác thực với DB)
    elseif ($old_pass == $new_pass) {
        $error = "Mật khẩu mới phải khác mật khẩu cũ!";
    }
    // KẾT THÚC THÊM
    else {
        // 1. XỬ LÝ CHO ADMIN
        if ($role == 'admin') {
            $sql = "SELECT * FROM tbl_admin WHERE tendangnhap = '$user'";
            $data = executeSingleResult($sql);
            
            // Admin
            if ($data['matkhau'] == $old_pass) {
                // Cập nhật pass mới
                $sql_update = "UPDATE tbl_admin SET matkhau = '$new_pass' WHERE tendangnhap = '$user'";
                execute($sql_update);
                $success = "Đổi mật khẩu thành công!";
            } else {
                $error = "Mật khẩu cũ không chính xác!";
            }
        }
        
        // 2. XỬ LÝ CHO STAFF
        elseif ($role == 'staff') {
            $sql = "SELECT * FROM tbl_staff WHERE tendangnhap = '$user'";
            $data = executeSingleResult($sql);
            
            // Staff đang dùng password_verify
            if (password_verify($old_pass, $data['matkhau'])) {
                // Mã hóa Bcrypt cho pass mới
                $new_pass_hash = password_hash($new_pass, PASSWORD_DEFAULT);
                $sql_update = "UPDATE tbl_staff SET matkhau = '$new_pass_hash' WHERE tendangnhap = '$user'";
                execute($sql_update);
                $success = "Đổi mật khẩu thành công!";
            } else {
                $error = "Mật khẩu cũ không chính xác!";
            }
        }
        
        // 3. XỬ LÝ CHO CUSTOMER
        else {
            $sql = "SELECT * FROM tbl_dangky WHERE tendangnhap = '$user'";
            $data = executeSingleResult($sql);
            
            // Customer đang dùng MD5
            if ($data['matkhau'] == md5($old_pass)) {
                // Mã hóa MD5 cho pass mới
                $new_pass_md5 = md5($new_pass);
                $sql_update = "UPDATE tbl_dangky SET matkhau = '$new_pass_md5' WHERE tendangnhap = '$user'";
                execute($sql_update);
                $success = "Đổi mật khẩu thành công!";
            } else {
                $error = "Mật khẩu cũ không chính xác!";
            }
        }
    }
}
?>

<?php include("Layout/header.php"); ?>

<main>
    <div class="container" style="margin-top: 100px; margin-bottom: 50px;">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white text-center">
                        <h4>ĐỔI MẬT KHẨU</h4>
                    </div>
                    <div class="card-body">
                        <?php 
                        if($error != '') {
                            echo '<div class="alert alert-danger">'.$error.'</div>';
                        }
                        if($success != '') {
                            echo '<div class="alert alert-success">'.$success.'</div>';
                        }
                        ?>
                        
                        <form method="POST">
                            <div class="form-group">
                                <label>Tài khoản đang đăng nhập:</label>
                                <input type="text" class="form-control" value="<?=$user?> (<?=ucfirst($role)?>)" disabled>
                            </div>
                            
                            <div class="form-group">
                                <label>Mật khẩu cũ:</label>
                                <input type="password" name="old_pass" class="form-control" required placeholder="Nhập mật khẩu hiện tại">
                            </div>
                            
                            <div class="form-group">
                                <label>Mật khẩu mới:</label>
                                <input type="password" name="new_pass" class="form-control" required placeholder="Nhập mật khẩu mới">
                            </div>
                            
                            <div class="form-group">
                                <label>Xác nhận mật khẩu mới:</label>
                                <input type="password" name="confirm_pass" class="form-control" required placeholder="Nhập lại mật khẩu mới">
                            </div>
                            
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-dark" style="width: 110px;">Lưu thay đổi</button>
                                <a href="index.php" class="btn btn-secondary" style="width: 100px;">Hủy bỏ</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include("Layout/footer.php"); ?>