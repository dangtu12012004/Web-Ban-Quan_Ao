<?php
require_once('database/dbhelper.php');

// Lấy ID đơn hàng
$order_id = '';
if (isset($_GET['id'])) {
    $order_id = $_GET['id'];
}

// XỬ LÝ CẬP NHẬT TRẠNG THÁI (ADMIN)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['status'])) {
    $status = $_POST['status'];
    
    // Cập nhật trạng thái cho tất cả sản phẩm trong đơn hàng
    $sql = "UPDATE order_details SET status = '$status' WHERE order_id = $order_id";
    execute($sql);
    
    // NẾU TRẠNG THÁI LÀ "ĐÃ NHẬN HÀNG" HOẶC "ĐÃ THANH TOÁN" 
    // -> CÓ THỂ TRỪ TỒN KHO NẾU MUỐN QUẢN LÝ CHẶT CHẼ
    if ($status == 'Đã nhận hàng' || $status == 'Đã thanh toán') {
        // Lấy danh sách sản phẩm trong đơn này để trừ kho (nếu chưa trừ lúc đặt)
        // (Tùy logic của bạn: Trừ lúc đặt hay trừ lúc giao thành công. 
        // Thông thường nên trừ lúc đặt để giữ hàng. Nếu hủy đơn thì cộng lại.)
    }

    echo '<script>alert("Cập nhật trạng thái thành công!");</script>';
}


// Lấy thông tin người mua (từ bảng orders)
$sql = "SELECT * FROM orders WHERE id = $order_id";
$order = executeSingleResult($sql);

// Lấy danh sách sản phẩm trong đơn (từ order_details join product)
$sql_details = "SELECT order_details.*, product.title, product.thumbnail 
                FROM order_details 
                JOIN product ON order_details.product_id = product.id 
                WHERE order_details.order_id = $order_id";
$order_items = executeResult($sql_details);

// Lấy trạng thái hiện tại (Lấy của món đầu tiên để hiển thị)
$current_status = '';
if (count($order_items) > 0) {
    $current_status = $order_items[0]['status'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chi tiết đơn hàng #<?= $order_id ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; }
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #sidebar { min-width: 250px; max-width: 250px; background: #343a40; color: #fff; transition: all 0.3s; min-height: 100vh; }
        #sidebar .sidebar-header { padding: 20px; background: #343a40; border-bottom: 1px solid #4b545c; }
        #sidebar ul.components { padding: 20px 0; border-bottom: 1px solid #47748b; }
        #sidebar ul p { color: #fff; padding: 10px; }
        #sidebar ul li a { padding: 15px 20px; font-size: 1.1em; display: block; color: #c2c7d0; text-decoration: none; }
        #sidebar ul li a:hover { color: #fff; background: #494e53; }
        #sidebar ul li.active > a { color: #fff; background: #007bff; }
        #sidebar ul li a i { margin-right: 10px; width: 20px; text-align: center; }
        #content { width: 100%; padding: 20px; min-height: 100vh; transition: all 0.3s; }
        
        .info-box { background: white; padding: 20px; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .info-label { font-weight: bold; color: #555; width: 130px; display: inline-block; }
    </style>
</head>

<body>
    <div class="wrapper">
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3>Admin CP</h3>
                <div style="font-size: 14px; color: #c2c7d0;">Xin chào, <?php echo $_COOKIE['user'] ?? 'Admin'; ?></div>
            </div>

            <ul class="list-unstyled components">
                <li>
                    <a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                </li>
                <li>
                    <a href="category/index.php"><i class="fas fa-folder"></i> Quản lý Danh mục</a>
                </li>
                <li>
                    <a href="product/index.php"><i class="fas fa-box"></i> Quản lý Sản phẩm</a>
                </li>
                <li class="active">
                    <a href="dashboard.php"><i class="fas fa-shopping-cart"></i> Quản lý Đơn hàng</a>
                </li>
                <li>
                    <a href="customer/index.php"><i class="fas fa-users"></i> Quản lý Khách hàng</a>
                </li>
                <li>
                    <a href="staff/index.php"><i class="fas fa-user-tie"></i> Quản lý Nhân viên</a>
                </li>
                <li>
                     <a href="../authen/logout.php" style="border-top: 1px solid #4b545c; margin-top: 20px;"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                </li>
            </ul>
        </nav>

        <div id="content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Chi tiết đơn hàng #<?= $order_id ?></h2>
                <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="info-box">
                        <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-user"></i> Thông tin khách hàng</h5>
                        <p><span class="info-label">Họ tên:</span> <?= $order['fullname'] ?></p>
                        <p><span class="info-label">Số điện thoại:</span> <?= $order['phone_number'] ?></p>
                        <p><span class="info-label">Địa chỉ:</span> <?= $order['address'] ?></p>
                        <p><span class="info-label">Email:</span> <?= $order['email'] ?></p>
                        <p><span class="info-label">Ngày đặt:</span> <?= $order['order_date'] ?></p>
                        <p><span class="info-label">Ghi chú:</span> <span class="text-muted"><?= $order['note'] ?></span></p>
                    </div>

                    <div class="info-box">
                        <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-edit"></i> Cập nhật trạng thái</h5>
                        <form method="POST" action="">
                            <div class="form-group">
                                <label>Trạng thái đơn hàng:</label>
                                <select class="form-control" name="status">
                                    <option value="Đang chuẩn bị" <?= ($current_status == 'Đang chuẩn bị') ? 'selected' : '' ?>>Đang chuẩn bị</option>
                                    <option value="Đang giao" <?= ($current_status == 'Đang giao') ? 'selected' : '' ?>>Đang giao</option>
                                    <option value="Đã nhận hàng" <?= ($current_status == 'Đã nhận hàng') ? 'selected' : '' ?>>Đã nhận hàng</option>
                                    <option value="Đã thanh toán" <?= ($current_status == 'Đã thanh toán') ? 'selected' : '' ?>>Đã thanh toán (Chuyển khoản)</option>
                                    <option value="Đã hủy" <?= ($current_status == 'Đã hủy') ? 'selected' : '' ?>>Đã hủy</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Cập nhật</button>
                        </form>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="info-box">
                        <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-box-open"></i> Danh sách sản phẩm</h5>
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>STT</th>
                                    <th>Hình ảnh</th>
                                    <th>Tên sản phẩm</th>
                                    <th class="text-center">Size</th>
                                    <th class="text-center">SL</th>
                                    <th class="text-right">Đơn giá</th>
                                    <th class="text-right">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $index = 1;
                                $total_bill = 0;
                                foreach ($order_items as $item) {
                                    $total_row = $item['price'] * $item['num'];
                                    $total_bill += $total_row;
                                    
                                    // Kiểm tra xem cột size có tồn tại không (phòng trường hợp DB cũ chưa update)
                                    $size = isset($item['size']) ? $item['size'] : 'N/A';

                                    echo '<tr>
                                        <td>'.($index++).'</td>
                                        <td class="text-center"><img src="product/'.$item['thumbnail'].'" style="width: 50px; height: 50px; object-fit: cover;"></td>
                                        <td>'.$item['title'].'</td>
                                        <td class="text-center"><span class="badge badge-info">'.$size.'</span></td>
                                        <td class="text-center">'.$item['num'].'</td>
                                        <td class="text-right">'.number_format($item['price'], 0, ',', '.').'</td>
                                        <td class="text-right font-weight-bold">'.number_format($total_row, 0, ',', '.').'</td>
                                    </tr>';
                                }
                                ?>
                                <tr class="bg-light">
                                    <td colspan="6" class="text-right font-weight-bold" style="font-size: 18px;">Tổng cộng:</td>
                                    <td class="text-right text-danger font-weight-bold" style="font-size: 18px;"><?= number_format($total_bill, 0, ',', '.') ?> VNĐ</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>