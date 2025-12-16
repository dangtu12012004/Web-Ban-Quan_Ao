<?php
require_once('database/dbhelper.php');
require_once('utils/utility.php');

// Bắt buộc đăng nhập
if (!isset($_COOKIE['tendangnhap'])) {
    echo '<script>alert("Vui lòng đăng nhập!"); window.location.href = "login.php";</script>';
    die();
}
$currentUser = $_COOKIE['tendangnhap'];

// 1. Lấy ID User
$id_user = 0;
// Kiểm tra user là khách hàng (tbl_dangky) hay Admin (tbl_admin)
$sql = "SELECT id_dangky FROM tbl_dangky WHERE tendangnhap = '$currentUser'";
$user = executeSingleResult($sql);
if ($user != null) {
    $id_user = $user['id_dangky'];
} else {
    $sql = "SELECT id_admin FROM tbl_admin WHERE tendangnhap = '$currentUser'";
    $admin = executeSingleResult($sql);
    if ($admin != null) {
        $id_user = $admin['id_admin']; 
    }
}

// Filter Status
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$whereStatus = "";
if ($filter_status != '') {
    $whereStatus = " AND order_details.status LIKE '%$filter_status%'";
}

// --- XỬ LÝ HỦY ĐƠN HÀNG ---
if (isset($_GET['cancel_id']) && is_numeric($_GET['cancel_id'])) {
    $cancelId = intval($_GET['cancel_id']);
    
    // 1. Kiểm tra trạng thái hiện tại của đơn hàng (lấy trạng thái sản phẩm đầu tiên)
    $checkSql = "SELECT status FROM order_details 
                 WHERE order_id = $cancelId AND id_user = $id_user 
                 LIMIT 1";
    $statusRow = executeSingleResult($checkSql);
    
    if ($statusRow) {
        $currentStatus = $statusRow['status'];
        
        // 2. Định nghĩa các trạng thái KHÔNG cho phép hủy
        $forbiddenStatus = ['Đã nhận hàng', 'Đã thanh toán', 'Đã hủy'];
        
        if (in_array($currentStatus, $forbiddenStatus) || strpos($currentStatus, 'giao') !== false) {
            echo '<script>alert("Không thể hủy đơn hàng này vì đơn đã được xử lý hoặc đã giao/thanh toán."); 
            window.location.href = "history.php";</script>';
        } else {
            // 3. Tiến hành hủy đơn (Cập nhật tất cả các chi tiết đơn hàng)
            $updateSql = "UPDATE order_details SET status = 'Đã hủy' 
                          WHERE order_id = $cancelId AND id_user = $id_user AND status NOT IN ('Đã hủy')";
            execute($updateSql);
            echo '<script>alert("Đã hủy đơn hàng thành công!"); window.location.href = "history.php";</script>';
            die();
        }
    } else {
        // Đơn hàng không tồn tại hoặc không thuộc về người dùng này
        echo '<script>alert("Lỗi: Không tìm thấy đơn hàng hợp lệ."); window.location.href = "history.php";</script>';
    }
}
// --- KẾT THÚC XỬ LÝ HỦY ĐƠN HÀNG ---

include("Layout/header.php");
?>

<style>
    .history-section { min-height: 600px; padding-top: 100px; padding-bottom: 50px; background: #f8f9fa; }
    .nav-tabs { margin-bottom: 20px; border-bottom: 2px solid #ddd; }
    .nav-tabs .nav-link { font-weight: bold; font-size: 16px; color: #555; }
    .nav-tabs .nav-link.active { color: #007bff; border-bottom: 3px solid #007bff; }
    
    /* Order Box Style */
    .order-box {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-bottom: 20px;
        overflow: hidden;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .order-header {
        background: #f1f1f1;
        padding: 10px 15px;
        border-bottom: 1px solid #ddd;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 14px;
    }
    .order-body {
        padding: 0;
    }
    .product-item {
        display: flex;
        padding: 15px;
        border-bottom: 1px solid #eee;
    }
    .product-item:last-child { border-bottom: none; }
    .product-img { width: 80px; height: 80px; object-fit: cover; border: 1px solid #eee; margin-right: 15px; }
    .product-info { flex: 1; }
    .product-price { font-weight: bold; color: #d9534f; }
    .badge-status { padding: 5px 10px; border-radius: 20px; font-size: 12px; color: #fff;}
    
    .bg-process { background-color: #ffc107; color: #000; }
    .bg-shipping { background-color: #17a2b8; }
    .bg-success { background-color: #28a745; }
    .bg-danger { background-color: #dc3545; }
    
    .total-section {
        padding: 10px 15px;
        text-align: right;
        background: #fff;
        border-top: 1px solid #eee;
    }
    .cancel-btn {
        margin-left: 10px;
        padding: 5px 10px;
        font-size: 12px;
    }
</style>

<body>
    <div id="wrapper">
        <main>
            <section class="cart history-section">
                <div class="container" style="max-width: 1000px;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <ul class="nav nav-tabs border-0 m-0">
                            <li class="nav-item"><a class="nav-link" href="cart.php">Giỏ hàng</a></li>
                            <li class="nav-item"><a class="nav-link active" href="history.php">Lịch sử mua hàng</a></li>
                        </ul>
                        
                        <form action="" method="GET" class="form-inline">
                            <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                                <option value="">-- Tất cả trạng thái --</option>
                                <option value="Đang chuẩn bị" <?= ($filter_status=='Đang chuẩn bị')?'selected':'' ?>>Đang chuẩn bị</option>
                                <option value="Đang giao" <?= ($filter_status=='Đang giao')?'selected':'' ?>>Đang giao hàng</option>
                                <option value="Đã nhận hàng" <?= ($filter_status=='Đã nhận hàng')?'selected':'' ?>>Đã nhận hàng</option>
                                <option value="Đã thanh toán" <?= ($filter_status=='Đã thanh toán')?'selected':'' ?>>Đã thanh toán</option>
                                <option value="Đã hủy" <?= ($filter_status=='Đã hủy')?'selected':'' ?>>Đã hủy</option>
                            </select>
                        </form>
                    </div>

                    <?php
                    // 2. Query lấy dữ liệu (Join orders để lấy thông tin chung của đơn)
                    // (Sử dụng $id_user đã được xác định ở đầu file)
                    $sql = "SELECT order_details.*, product.title, product.thumbnail, 
                                   orders.order_date, orders.payment_method, orders.id as order_idx
                            FROM order_details 
                            JOIN product ON product.id = order_details.product_id 
                            JOIN orders ON orders.id = order_details.order_id
                            WHERE order_details.id_user = '$id_user' 
                            $whereStatus
                            ORDER BY order_details.id DESC";
                    $data = executeResult($sql);

                    // 3. Group by Order ID (Gom nhóm sản phẩm theo đơn hàng)
                    $orders = [];
                    foreach ($data as $item) {
                        $oid = $item['order_idx'];
                        if (!isset($orders[$oid])) {
                            $orders[$oid] = [
                                'date' => $item['order_date'],
                                'payment' => $item['payment_method'],
                                'status' => $item['status'], 
                                'products' => [],
                                'total_money' => 0,
                                'can_cancel' => true // Giả sử ban đầu có thể hủy
                            ];
                        }
                        $orders[$oid]['products'][] = $item;
                        $orders[$oid]['total_money'] += $item['num'] * $item['price'];

                        // Nếu bất kỳ sản phẩm nào có trạng thái cấm hủy, set can_cancel = false
                        $forbiddenStatus = ['Đã nhận hàng', 'Đã thanh toán', 'Đã hủy'];
                        if (in_array($item['status'], $forbiddenStatus) || strpos($item['status'], 'giao') !== false) {
                             $orders[$oid]['can_cancel'] = false;
                        }
                    }

                    // 4. Hiển thị
                    if (count($orders) > 0) {
                        foreach ($orders as $orderId => $order) {
                            $statusClass = 'bg-secondary';
                            if (strpos($order['status'], 'Đang chuẩn bị') !== false) $statusClass = 'bg-process';
                            if (strpos($order['status'], 'Đang giao') !== false) $statusClass = 'bg-shipping';
                            if (strpos($order['status'], 'Đã nhận hàng') !== false || strpos($order['status'], 'Đã thanh toán') !== false) $statusClass = 'bg-success';
                            if (strpos($order['status'], 'Đã hủy') !== false) $statusClass = 'bg-danger';
                            
                            $date = date('d/m/Y H:i', strtotime($order['date']));
                            ?>
                            
                            <div class="order-box">
                                <div class="order-header">
                                    <div>
                                        <strong>Đơn hàng #<?= $orderId ?></strong> 
                                        <span class="text-muted ml-2">| <?= $date ?></span>
                                        <span class="text-muted ml-2">| <?= $order['payment'] ?></span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="badge badge-status <?= $statusClass ?>"><?= $order['status'] ?></span>
                                        
                                        <?php if ($order['can_cancel']): ?>
                                        <a href="?cancel_id=<?= $orderId ?>" 
                                           onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng #<?= $orderId ?> không?');"
                                           class="btn btn-danger btn-sm cancel-btn">
                                            Hủy đơn
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="order-body">
                                    <?php foreach ($order['products'] as $prod): ?>
                                    <div class="product-item">
                                        <img src="admin/product/<?= $prod['thumbnail'] ?>" class="product-img">
                                        <div class="product-info">
                                            <h6 class="mb-1"><?= $prod['title'] ?></h6>
                                            <div class="text-muted small">
                                                Size: <?= isset($prod['size']) ? $prod['size'] : 'N/A' ?> | 
                                                x<?= $prod['num'] ?>
                                            </div>
                                        </div>
                                        <div class="product-price">
                                            <?= number_format($prod['price'], 0, ',', '.') ?>đ
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <div class="total-section">
                                    <span class="text-muted">Tổng số tiền:</span>
                                    <span class="text-danger font-weight-bold ml-2" style="font-size: 18px;">
                                        <?= number_format($order['total_money'], 0, ',', '.') ?> VNĐ
                                    </span>
                                </div>
                            </div>

                            <?php
                        }
                    } else {
                        echo '<div class="text-center p-5 bg-white rounded shadow-sm">
                                <img src="https://cdn-icons-png.flaticon.com/512/2038/2038854.png" width="100px" style="opacity: 0.5; margin-bottom: 15px;">
                                <p class="text-muted">Chưa có đơn hàng nào.</p>
                                <a href="shop_product.php" class="btn btn-primary">Mua sắm ngay</a>
                              </div>';
                    }
                    ?>
                </div>
            </section>
        </main>
    </div>
</body>
<?php include("Layout/footer.php") ?>