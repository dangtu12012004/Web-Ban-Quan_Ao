<?php
require_once('database/dbhelper.php');

if (isset($_COOKIE['role']) && $_COOKIE['role'] == 'staff') {
    // Code xử lý permission
}

// --- XỬ LÝ LOGIC LỌC VÀ SẮP XẾP ---

// 1. Khởi tạo câu SQL cơ bản (lấy dữ liệu và tính toán cột ảo)
$sql = "SELECT orders.*, 
        (SELECT SUM(price * num) FROM order_details WHERE order_details.order_id = orders.id) as total_money,
        (SELECT status FROM order_details WHERE order_details.order_id = orders.id LIMIT 1) as order_status
        FROM orders 
        WHERE 1=1 "; // Dùng 1=1 để dễ nối chuỗi AND phía sau

// 2. Lọc theo Tên người đặt (keyword)
$keyword = '';
if (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
    $keyword = $_GET['keyword'];
    $sql .= " AND fullname LIKE '%$keyword%' ";
}

// 3. Lọc theo Thời gian đặt (date)
$date = '';
if (isset($_GET['date']) && !empty($_GET['date'])) {
    $date = $_GET['date'];
    // So sánh ngày trong DB (dạng datetime) với ngày nhập vào
    $sql .= " AND DATE(order_date) = '$date' "; 
}

// 4. Lọc theo Trạng thái (status)
// Vì 'order_status' là cột được tạo từ Subquery (AS alias), ta phải dùng HAVING thay vì WHERE
$status = '';
$havingClause = "";
if (isset($_GET['status']) && !empty($_GET['status'])) {
    $status = $_GET['status'];
    $havingClause = " HAVING order_status LIKE '%$status%' ";
}

// 5. Sắp xếp theo Giá (sort_price)
$sort_price = '';
$orderBy = " ORDER BY order_date DESC"; // Mặc định: Mới nhất xếp trước
if (isset($_GET['sort_price']) && !empty($_GET['sort_price'])) {
    $sort_price = $_GET['sort_price'];
    if ($sort_price == 'ASC') {
        $orderBy = " ORDER BY total_money ASC "; // Giá thấp đến cao
    } elseif ($sort_price == 'DESC') {
        $orderBy = " ORDER BY total_money DESC "; // Giá cao đến thấp
    }
}

// Ghép chuỗi SQL hoàn chỉnh
$finalSql = $sql . $havingClause . $orderBy;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quản lý Đơn hàng</title>
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
        #sidebar ul li a { padding: 15px 20px; font-size: 1.1em; display: block; color: #c2c7d0; text-decoration: none; }
        #sidebar ul li a:hover { color: #fff; background: #494e53; }
        #sidebar ul li.active > a { color: #fff; background: #007bff; }
        #content { width: 100%; padding: 20px; min-height: 100vh; }
        .table td, .table th { vertical-align: middle; }
        /* Style cho form lọc */
        .filter-bar { background: #fff; padding: 15px; border-radius: 5px; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075); margin-bottom: 20px; }
    </style>
</head>

<body>
    <div class="wrapper">
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3>Trang quản lý</h3>
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
                    <a href="/Web/logout.php" style="border-top: 1px solid #4b545c; margin-top: 20px;"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                </li>
            </ul>
        </nav>

        <div id="content">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h2 class="text-left mb-4">Quản lý Đơn hàng</h2>
                </div>
                
                <div class="panel-body">
                    <div class="filter-bar">
                        <form method="GET">
                            <div class="form-row align-items-end">
                                <div class="col-md-3 mb-2">
                                    <label class="font-weight-bold">Người đặt:</label>
                                    <input type="text" name="keyword" class="form-control" placeholder="Nhập tên khách hàng..." value="<?=$keyword?>">
                                </div>
                                
                                <div class="col-md-3 mb-2">
                                    <label class="font-weight-bold">Ngày đặt:</label>
                                    <input type="date" name="date" class="form-control" value="<?=$date?>">
                                </div>

                                
                                
                                
                                <div class="col-md-2 mb-2">
                                <label class="font-weight-bold">Trạng thái:</label>
                                <select name="status" class="form-control">
                                    <option value="">-- Tất cả --</option>
                                    
                                    <option value="Đang chuẩn bị" <?= ($status == 'Đang chuẩn bị') ? 'selected' : '' ?>>Đang chuẩn bị</option>
                                    <option value="Đang giao" <?= ($status == 'Đang giao') ? 'selected' : '' ?>>Đang giao</option>
                                    <option value="Đã thanh toán" <?= ($status == 'Đã thanh toán') ? 'selected' : '' ?>>Đã thanh toán</option>
                                    <option value="Đã nhận hàng" <?= ($status == 'Đã nhận hàng') ? 'selected' : '' ?>>Đã nhận hàng</option>
                                    
                                    <option value="Đã hủy" <?= ($status == 'Đã hủy') ? 'selected' : '' ?>>Đã hủy</option>
                                </select>
                            </div>

                                <div class="col-md-2 mb-2">
                                    <label class="font-weight-bold">Sắp xếp giá:</label>
                                    <select name="sort_price" class="form-control">
                                        <option value="">Mặc định</option>
                                        <option value="ASC" <?= ($sort_price == 'ASC') ? 'selected' : '' ?>>Thấp đến Cao</option>
                                        <option value="DESC" <?= ($sort_price == 'DESC') ? 'selected' : '' ?>>Cao đến Thấp</option>
                                    </select>
                                </div>

                                <div class="col-md-2 mb-2">
                                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-filter"></i> Lọc đơn</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <table class="table table-bordered table-hover bg-white shadow-sm" style="border-radius: 5px;">
                        <thead class="thead-light">
                            <tr>
                                <th width="50px">STT</th>
                                <th>Họ tên</th>
                                <th>Số điện thoại</th>
                                <th>Địa chỉ</th>
                                <th>Ngày đặt</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th width="120px">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Thực thi câu lệnh SQL đã xây dựng ở trên
                            $orders = executeResult($finalSql);
                            
                            $index = 1;
                            if (count($orders) > 0) {
                                foreach ($orders as $item) {
                                    $statusColor = 'secondary';
                                    $stt = $item['order_status'];
                                    if(strpos($stt, 'Đang') !== false) $statusColor = 'warning';
                                    if(strpos($stt, 'Đã') !== false) $statusColor = 'success';
                                    if(strpos($stt, 'Hủy') !== false) $statusColor = 'danger';

                                    echo '<tr>
                                            <td>' . ($index++) . '</td>
                                            <td style="font-weight:bold;">' . $item['fullname'] . '</td>
                                            <td>' . $item['phone_number'] . '</td>
                                            <td>' . $item['address'] . '</td>
                                            <td>' . date('d/m/Y H:i', strtotime($item['order_date'])) . '</td>
                                            <td class="text-danger font-weight-bold">' . number_format($item['total_money'], 0, ',', '.') . ' VNĐ</td>
                                            <td><span class="badge badge-'.$statusColor.'">' . $stt . '</span></td>
                                            <td>
                                                <a href="order_details.php?id=' . $item['id'] . '">
                                                    <button class="btn btn-info btn-sm"><i class="fas fa-eye"></i> Chi tiết</button>
                                                </a>
                                            </td>
                                        </tr>';
                                }
                            } else {
                                echo '<tr><td colspan="8" class="text-center text-muted py-4">Không tìm thấy đơn hàng nào phù hợp tiêu chí lọc.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>