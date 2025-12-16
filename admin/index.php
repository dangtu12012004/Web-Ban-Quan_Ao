<?php 
    session_start();
    require_once('database/dbhelper.php');
    
    if (isset($_COOKIE['role']) && $_COOKIE['role'] == 'staff') {
        header('Location: product/index.php'); 
        die();
    }
    if(!isset($_SESSION['submit'])){
        header('Location: login.php');
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Quản Trị</title>
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
        .card-box { position: relative; color: #fff; padding: 20px 10px 40px; margin: 20px 0px; border-radius: 5px; }
        .card-box:hover { text-decoration: none; color: #f1f1f1; }
        .card-box:hover .icon i { font-size: 100px; transition: 1s; -webkit-transition: 1s; }
        .card-box .inner { padding: 5px 10px 0 10px; }
        .card-box h3 { font-size: 27px; font-weight: bold; margin: 0 0 8px 0; white-space: nowrap; padding: 0; text-align: left; }
        .card-box p { font-size: 15px; }
        .card-box .icon { position: absolute; top: auto; bottom: 5px; right: 5px; z-index: 0; font-size: 72px; color: rgba(0, 0, 0, 0.15); }
        .card-box .card-box-footer { position: absolute; left: 0px; bottom: 0px; text-align: center; padding: 3px 0; color: rgba(255, 255, 255, 0.8); background: rgba(0, 0, 0, 0.1); width: 100%; text-decoration: none; border-bottom-right-radius: 5px; border-bottom-left-radius: 5px; }
        .bg-blue { background-color: #007bff !important; }
        .bg-green { background-color: #28a745 !important; }
        .bg-orange { background-color: #ffc107 !important; color: #1f2d3d !important; }
        .bg-red { background-color: #dc3545 !important; }
        .bg-info { background-color: #17a2b8 !important; }
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
                <li class="active">
                    <a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                </li>
                <li>
                    <a href="category/index.php"><i class="fas fa-folder"></i> Quản lý Danh mục</a>
                </li>
                <li>
                    <a href="product/index.php"><i class="fas fa-box"></i> Quản lý Sản phẩm</a>
                </li>
                <li>
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
            <h2 class="mb-4">Tổng quan hệ thống</h2>
            
            <div class="row">
                <?php
                $sql = "SELECT count(*) as total FROM product";
                $result = executeResult($sql);
                $total_products = 0;
                if (!empty($result)) {
                    $total_products = $result[0]['total'];
                }
                ?>
                <div class="col-lg-3 col-6">
                    <div class="card-box bg-blue">
                        <div class="inner">
                            <h3><?= $total_products ?></h3>
                            <p>Sản phẩm</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-box" aria-hidden="true"></i>
                        </div>
                        <a href="product/index.php" class="card-box-footer">Xem chi tiết <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <?php
                $sql = "SELECT count(*) as total FROM tbl_dangky";
                $result = executeResult($sql);
                $total_users = 0;
                if (!empty($result)) {
                    $total_users = $result[0]['total'];
                }
                ?>
                <div class="col-lg-3 col-6">
                    <div class="card-box bg-green">
                        <div class="inner">
                            <h3><?= $total_users ?></h3>
                            <p>Khách hàng</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users" aria-hidden="true"></i>
                        </div>
                        <a href="customer/index.php" class="card-box-footer">Xem chi tiết <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <?php
                $sql = "SELECT count(*) as total FROM orders";
                $result = executeResult($sql);
                $total_orders = 0;
                if (!empty($result)) {
                    $total_orders = $result[0]['total'];
                }
                ?>
                <div class="col-lg-3 col-6">
                    <div class="card-box bg-orange">
                        <div class="inner">
                            <h3><?= $total_orders ?></h3>
                            <p>Đơn hàng</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-shopping-cart" aria-hidden="true"></i>
                        </div>
                        <a href="dashboard.php" class="card-box-footer">Xem chi tiết <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                
                <?php
                $sql_revenue = "SELECT SUM(price * num) as total_revenue 
                                FROM order_details 
                                WHERE status = 'Đã nhận hàng' OR status = 'Đã thanh toán'";
                
                $result_rev = executeResult($sql_revenue);
                $revenue = 0;
                if (!empty($result_rev) && $result_rev[0]['total_revenue'] != null) {
                    $revenue = $result_rev[0]['total_revenue'];
                }
                ?>
                <div class="col-lg-3 col-6">
                    <div class="card-box bg-red">
                        <div class="inner">
                            <h3><?= number_format($revenue, 0, ',', '.') ?> <sup style="font-size: 20px">đ</sup></h3>
                            <p>Doanh thu thực tế</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-money-bill-wave" aria-hidden="true"></i>
                        </div>
                        <a href="#" class="card-box-footer">Doanh thu của cửa hàng <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-trophy"></i> Top 5 Sản phẩm bán chạy nhất</h5>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th width="50px">Top</th>
                                        <th>Hình ảnh</th>
                                        <th>Tên sản phẩm</th>
                                        <th class="text-center">Số lượng đã bán</th>
                                        <th class="text-right">Doanh thu mang lại</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql_top = "SELECT product.title, product.thumbnail, 
                                                SUM(order_details.num) as da_ban, 
                                                SUM(order_details.price * order_details.num) as tong_tien 
                                                FROM order_details 
                                                JOIN product ON order_details.product_id = product.id 
                                                WHERE order_details.status = 'Đã nhận hàng' OR order_details.status = 'Đã thanh toán'
                                                GROUP BY order_details.product_id 
                                                ORDER BY da_ban DESC 
                                                LIMIT 5";
                                    $top_products = executeResult($sql_top);

                                    $rank = 0;
                                    if(count($top_products) > 0) {
                                        foreach ($top_products as $item) {
                                            $rank++;
                                            ?>
                                            <tr>
                                                <td class="text-center">
                                                    <?php 
                                                    if($rank == 1) echo '<i class="fas fa-crown text-warning"></i>';
                                                    else echo $rank;
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if (strpos($item['thumbnail'], 'admin/') !== false) {
                                                        echo '<img src="/Web/'.$item['thumbnail'].'" style="height: 40px; border-radius: 4px;">';
                                                    } else {
                                                        echo '<img src="/Web/admin/product/'.$item['thumbnail'].'" style="height: 40px; border-radius: 4px;">';
                                                    }
                                                    ?>
                                                </td>
                                                <td style="font-weight: 500;"><?= $item['title'] ?></td>
                                                <td class="text-center"><span class="badge badge-success" style="font-size: 14px;"><?= $item['da_ban'] ?></span></td>
                                                <td class="text-right text-danger font-weight-bold"><?= number_format($item['tong_tien'], 0, ',', '.') ?> đ</td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        echo '<tr><td colspan="5" class="text-center">Chưa có dữ liệu bán hàng</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-5 text-muted">
                <small>Copyright © 2025 - PTUD Web N07.</small>
            </div>
        </div>
    </div>
</body>
</html>