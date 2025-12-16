<?php
require_once('../database/dbhelper.php');

// --- 1. XỬ LÝ LOGIC SORT ---
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'new'; // Mặc định là mới nhất
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = 5;
$start = ($page - 1) * $limit;

// Câu lệnh SQL cơ bản
$sqlSelect = "SELECT p.* ";
$sqlFrom = "FROM product p ";
$sqlJoin = "";
$sqlGroup = " ";
$sqlOrder = "ORDER BY p.id DESC "; // Mặc định sắp xếp theo ID giảm dần (Mới nhất)

switch ($sort) {
    case 'price_asc':
        $sqlOrder = "ORDER BY p.price ASC ";
        break;
    case 'price_desc':
        $sqlOrder = "ORDER BY p.price DESC ";
        break;
    case 'sold': // Bán chạy nhất
        // Join bảng order_details để tính tổng số lượng đã bán
        $sqlSelect .= ", SUM(IF(od.status IN ('Đang giao', 'Đã nhận hàng', 'Đã thanh toán', 'Giao hàng thành công', 'Đã chuyển khoản'), od.num, 0)) as total_sold_sort ";
        $sqlJoin = "LEFT JOIN order_details od ON p.id = od.product_id ";
        $sqlGroup = "GROUP BY p.id ";
        $sqlOrder = "ORDER BY total_sold_sort DESC ";
        break;
    case 'inventory': // Tồn kho thấp nhất (Ưu tiên xem hàng sắp hết)
        // Tính tồn kho = Tổng nhập (S+M+L) - Tổng đã bán
        $sqlSelect .= ", ((p.qty_s + p.qty_m + p.qty_l) - SUM(IF(od.status IN ('Đang giao', 'Đã nhận hàng', 'Đã thanh toán', 'Giao hàng thành công', 'Đã chuyển khoản'), od.num, 0))) as inventory_sort ";
        $sqlJoin = "LEFT JOIN order_details od ON p.id = od.product_id ";
        $sqlGroup = "GROUP BY p.id ";
        $sqlOrder = "ORDER BY inventory_sort ASC ";
        break;
}

// Ghép câu lệnh hoàn chỉnh
$sqlFinal = $sqlSelect . $sqlFrom . $sqlJoin . $sqlGroup . $sqlOrder . " LIMIT $start, $limit";
$productList = executeResult($sqlFinal);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quản lý Sản phẩm</title>
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
        .qty-badge { font-size: 12px; padding: 5px 8px; margin: 2px; display: inline-block; min-width: 80px; text-align: center; }
        .sort-box { float: right; margin-bottom: 15px; }
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
                <li><a href="../index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="../category/index.php"><i class="fas fa-folder"></i> Quản lý Danh mục</a></li>
                <li class="active"><a href="index.php"><i class="fas fa-box"></i> Quản lý Sản phẩm</a></li>
                <li><a href="../dashboard.php"><i class="fas fa-shopping-cart"></i> Quản lý Đơn hàng</a></li>
                <li><a href="../customer/index.php"><i class="fas fa-users"></i> Quản lý Khách hàng</a></li>
                <li><a href="../staff/index.php"><i class="fas fa-user-tie"></i> Quản lý Nhân viên</a></li>
                <li><a href="/Web/logout.php" style="border-top: 1px solid #4b545c; margin-top: 20px;"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
            </ul>
        </nav>

        <div id="content">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h2 class="text-left mb-4">Quản lý Sản Phẩm</h2>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="add.php">
                                <button class="btn btn-success mb-3"><i class="fas fa-plus"></i> Thêm Sản Phẩm</button>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <div class="sort-box form-inline justify-content-end">
                                <label class="mr-2">Sắp xếp theo:</label>
                                <select class="form-control" onchange="window.location.href=this.value">
                                    <option value="?sort=new" <?= $sort == 'new' ? 'selected' : '' ?>>Mới nhất</option>
                                    <option value="?sort=price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Giá tăng dần</option>
                                    <option value="?sort=price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Giá giảm dần</option>
                                    <option value="?sort=sold" <?= $sort == 'sold' ? 'selected' : '' ?>>Bán chạy nhất</option>
                                    <option value="?sort=inventory" <?= $sort == 'inventory' ? 'selected' : '' ?>>Tồn kho thấp nhất</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <table class="table table-bordered table-hover bg-white shadow-sm" style="border-radius: 5px;">
                        <thead class="thead-light">
                            <tr style="font-weight: 500;">
                                <th width="50px">STT</th>
                                <th>Thumbnail</th>
                                <th>Tên Sản Phẩm</th>
                                <th>Giá</th>
                                <th width="100px" class="text-center">Đã bán</th> 
                                <th width="220px">Kho hàng (Thực tế)</th> 
                                <th>Nội dung</th>
                                <th>Danh mục</th>
                                <th width="50px"></th>
                                <th width="50px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                $index = $start + 1; // Số thứ tự bắt đầu từ trang hiện tại
                                foreach ($productList as $item) {
                                    // --- LOGIC HIỂN THỊ CHI TIẾT S/M/L (Vẫn giữ nguyên để hiển thị badge) ---
                                    $pid = $item['id'];
                                    
                                    // Query này chỉ dùng để lấy chi tiết size S, M, L cho việc hiển thị Badge
                                    $sqlSold = "SELECT size, SUM(num) as sold_qty FROM order_details 
                                                WHERE product_id = $pid 
                                                AND status IN ('Đang giao', 'Đã nhận hàng', 'Đã thanh toán', 'Giao hàng thành công', 'Đã chuyển khoản') 
                                                GROUP BY size";
                                    $soldList = executeResult($sqlSold);
                                    
                                    $sold_s = 0; $sold_m = 0; $sold_l = 0;
                                    foreach($soldList as $s) {
                                        if($s['size'] == 'S') $sold_s = $s['sold_qty'];
                                        if($s['size'] == 'M') $sold_m = $s['sold_qty'];
                                        if($s['size'] == 'L') $sold_l = $s['sold_qty'];
                                    }

                                    $total_sold = $sold_s + $sold_m + $sold_l;
                                    $real_s = max(0, $item['qty_s'] - $sold_s);
                                    $real_m = max(0, $item['qty_m'] - $sold_m);
                                    $real_l = max(0, $item['qty_l'] - $sold_l);
                                    $total_real = $real_s + $real_m + $real_l;

                                    echo '<tr>
                                            <td>' . ($index++) . '</td>
                                            <td style="text-align:center">
                                                <img src="' . $item['thumbnail'] . '" alt="" style="width: 50px; height: 50px; object-fit: cover;">
                                            </td>
                                            <td>' . $item['title'] . '</td>
                                            <td class="text-danger font-weight-bold">' . number_format($item['price'], 0, ',', '.') . ' VNĐ</td>
                                            
                                            <td class="text-center font-weight-bold" style="color: #28a745; font-size: 1.1em;">
                                                ' . $total_sold . '
                                            </td>

                                            <td>
                                                <div style="font-weight:bold; margin-bottom:5px;">Tổng: '.$total_real.'</div>
                                                <span class="badge badge-info qty-badge">S: '.$real_s.'</span>
                                                <span class="badge badge-warning qty-badge" style="color:white;">M: '.$real_m.'</span>
                                                <span class="badge badge-success qty-badge">L: '.$real_l.'</span>
                                            </td>
                                            
                                            <td><div style="max-height: 100px; overflow: hidden;">' . $item['content'] . '</div></td>
                                            <td class="text-center">' . $item['id_category'] . '</td>
                                            <td>
                                                <a href="add.php?id=' . $item['id'] . '"><button class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></button></a>
                                            </td>
                                            <td>
                                                <button class="btn btn-danger btn-sm" onclick="deleteProduct(' . $item['id'] . ')"><i class="fas fa-trash"></i></button>
                                            </td>
                                            </tr>';
                                }
                            } catch (Exception $e) {
                                die("Lỗi thực thi sql: " . $e->getMessage());
                            }
                            ?>
                        </tbody>
                    </table>

                    <ul class="pagination justify-content-center mt-4">
                        <?php
                        $sqlCount = "SELECT id FROM product";
                        $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DATABASE);
                        $result = mysqli_query($conn, $sqlCount);
                        $current_page = 0;
                        if (mysqli_num_rows($result)) {
                            $numrow = mysqli_num_rows($result);
                            $current_page = ceil($numrow / $limit);
                        }
                        
                        // Thêm biến sort vào link phân trang
                        $sortParam = "&sort=" . $sort;

                        for ($i = 1; $i <= $current_page; $i++) {
                            if ($i == $page) {
                                echo '<li class="page-item active"><a class="page-link" href="?page=' . $i . $sortParam . '">' . $i . '</a></li>';
                            } else {
                                echo '<li class="page-item"><a class="page-link" href="?page=' . $i . $sortParam . '">' . $i . '</a></li>';
                            }
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        function deleteProduct(id) {
            var option = confirm('Bạn có chắc chắn muốn xoá sản phẩm này không?')
            if (!option) {
                return;
            }

            console.log(id)
            $.post('ajax.php', {
                'id': id,
                'action': 'delete'
            }, function(data) {
                location.reload()
            })
        }
    </script>
</body>
</html>