<?php
require_once('database/config.php');
require_once('database/dbhelper.php');
require_once('utils/utility.php');

// Kiểm tra đăng nhập và vai trò
if (!isset($_COOKIE['tendangnhap']) || $_COOKIE['role'] != 'customer') {
    header('Location: index.php');
    die();
}

$username = $_COOKIE['tendangnhap'];
$customerInfo = null;
$success = '';
$error = '';

// --- XỬ LÝ CẬP NHẬT THÔNG TIN ---
if (!empty($_POST)) {
    // Lấy dữ liệu từ form
    $tenkhachhang_new = getPost('tenkhachhang');
    $email_new = getPost('email');
    $diachi_new = getPost('diachi');
    $dienthoai_new = getPost('dienthoai');

    // Kiểm tra dữ liệu hợp lệ (Cần bổ sung validation chi tiết hơn nếu cần)
    if (empty($tenkhachhang_new) || empty($diachi_new) || empty($dienthoai_new)) {
        $error = "Vui lòng không để trống các trường Họ và Tên, Địa chỉ, và Số điện thoại.";
    } else {
        // Cập nhật vào database
        // Lưu ý: Đảm bảo hàm escape/clean data được sử dụng trong hàm execute nếu cần
        $sql_update = "UPDATE tbl_dangky SET 
                        tenkhachhang = '$tenkhachhang_new', 
                        email = '$email_new', 
                        diachi = '$diachi_new', 
                        dienthoai = '$dienthoai_new' 
                        WHERE tendangnhap = '$username'";
        
        execute($sql_update);
        
        // Cập nhật cookie tên hiển thị sau khi đổi tên
        setcookie('user', $tenkhachhang_new, time() + 30 * 24 * 60 * 60, '/');
        
        $success = "Cập nhật thông tin cá nhân thành công!";
    }
}
// --- KẾT THÚC XỬ LÝ CẬP NHẬT ---


// Lấy thông tin khách hàng mới nhất từ Database sau khi cập nhật (hoặc lần đầu)
$sql = "SELECT tenkhachhang, tendangnhap, email, diachi, dienthoai FROM tbl_dangky WHERE tendangnhap = '$username'";
$customerInfo = executeSingleResult($sql);

if ($customerInfo == null) {
    header('Location: index.php');
    die();
}

$customerName = $customerInfo['tenkhachhang'];
?>

<?php include("Layout/header.php"); ?>

<main>
    <div class="container" style="margin-top: 100px; margin-bottom: 50px;">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white text-center">
                        <h4>CHỈNH SỬA THÔNG TIN CÁ NHÂN</h4>
                    </div>
                    <div class="card-body">
                        <h5 class="text-center mb-4 text-primary">Xin chào, <?= htmlspecialchars($customerName) ?>!</h5>
                        
                        <?php 
                        if ($error != '') {
                            echo '<div class="alert alert-danger">'.$error.'</div>';
                        }
                        if ($success != '') {
                            echo '<div class="alert alert-success">'.$success.'</div>';
                        }
                        ?>

                        <form method="POST">
                            <ul class="list-group list-group-flush mb-4">
                                <li class="list-group-item">
                                    <div class="form-group row mb-0">
                                        <label for="tenkhachhang" class="col-sm-4 col-form-label"><strong><i class="fas fa-user-tag mr-2"></i> Họ và Tên:</strong></label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="tenkhachhang" name="tenkhachhang" 
                                                   value="<?= htmlspecialchars($customerInfo['tenkhachhang']) ?>" required>
                                        </div>
                                    </div>
                                </li>
                                
                                <li class="list-group-item">
                                    <div class="form-group row mb-0">
                                        <label class="col-sm-4 col-form-label"><strong><i class="fas fa-user mr-2"></i> Tên đăng nhập:</strong></label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" value="<?= htmlspecialchars($customerInfo['tendangnhap']) ?>" disabled>
                                        </div>
                                    </div>
                                </li>
                                
                                <li class="list-group-item">
                                    <div class="form-group row mb-0">
                                        <label for="email" class="col-sm-4 col-form-label"><strong><i class="fas fa-envelope mr-2"></i> Email:</strong></label>
                                        <div class="col-sm-8">
                                            <input type="email" class="form-control" id="email" name="email" 
                                                   value="<?= htmlspecialchars($customerInfo['email']) ?>" required>
                                        </div>
                                    </div>
                                </li>
                                
                                <li class="list-group-item">
                                    <div class="form-group row mb-0">
                                        <label for="dienthoai" class="col-sm-4 col-form-label"><strong><i class="fas fa-phone mr-2"></i> Số điện thoại:</strong></label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="dienthoai" name="dienthoai" 
                                                   value="<?= htmlspecialchars($customerInfo['dienthoai']) ?>" required>
                                        </div>
                                    </div>
                                </li>
                                
                                <li class="list-group-item">
                                    <div class="form-group row mb-0">
                                        <label for="diachi" class="col-sm-4 col-form-label"><strong><i class="fas fa-map-marker-alt mr-2"></i> Địa chỉ:</strong></label>
                                        <div class="col-sm-8">
                                            <textarea class="form-control" id="diachi" name="diachi" rows="2" required><?= htmlspecialchars($customerInfo['diachi']) ?></textarea>
                                        </div>
                                    </div>
                                </li>
                            </ul>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-dark" style="width: 150px;">
                                    <i class="fas fa-save"></i> Lưu thay đổi
                                </button>
                                <a href="index.php" class="btn btn-secondary" style="width: 150px;">
                                    <i class="fas fa-undo"></i> Quay lại
                                </a>
                            </div>
                        </form>
                        
                        <div class="text-center mt-3">
                             <a href="change_password.php" class="btn btn-link text-warning" style="width: 130px;">
                                <i class="fas fa-key" ></i> Đổi mật khẩu
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include("Layout/footer.php"); ?>