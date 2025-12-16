<?php
require_once('database/dbhelper.php');
require_once('utils/utility.php');
include("Layout/header.php");

$cart = [];
if (isset($_COOKIE['cart'])) {
    $json = $_COOKIE['cart'];
    $cart = json_decode($json, true);
}

$cartList = [];
if ($cart != null && count($cart) > 0) {
    $idList = [];
    foreach ($cart as $item) {
        $idList[] = $item['id'];
    }
    $idListStr = implode(',', $idList);
    
    // Query lấy thông tin sản phẩm (bao gồm cả số lượng tồn kho từng size)
    $sql = "SELECT * FROM product WHERE id IN ($idListStr)";
    $cartList = executeResult($sql);
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
    .cart-header {
        display: flex;
        align-items: center;
        margin-top: 100px;
        border-bottom: 2px solid #eee;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }
    .cart-header a {
        text-decoration: none;
        color: #333;
        font-weight: bold;
        margin-right: 20px;
        font-size: 18px;
        padding-bottom: 10px;
    }
    .cart-header a.active {
        color: #007bff;
        border-bottom: 3px solid #007bff;
    }
    .table td, .table th { vertical-align: middle; }
    .btn-delete { color: #dc3545; cursor: pointer; border: none; background: none; font-size: 18px; transition: 0.2s;}
    .btn-delete:hover { transform: scale(1.2); }
    .empty-cart { text-align: center; padding: 50px 0; }
    .empty-cart img { width: 150px; opacity: 0.6; margin-bottom: 20px; }
    
    /* CSS Nút bấm chuẩn */
    .btn-custom {
        font-family: 'Arial', sans-serif;
        font-weight: 600;
        padding: 12px 25px;
        border-radius: 5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap; /* Ngăn xuống dòng chữ bên trong nút */
    }
    
    /* Layout chứa 2 nút hành động */
    .cart-actions {
        display: flex;
        justify-content: space-between;
        align-items: center; 
        margin-top: 30px;
        margin-bottom: 50px;
        flex-wrap: wrap; 
    }
    
    .total-price {
        font-size: 20px;
        margin-bottom: 15px;
        text-align: right;
    }
    
    /* Style cho Dropdown chọn size */
    .size-select {
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 5px;
        font-weight: bold;
        outline: none;
        width: 60px;
        text-align: center;
    }
    .btn{
        width: 270px;
    }
</style>

<main style="display:flex; flex-direction:column; min-height:100vh;">
    <div class="container">
        <div class="cart-header">
            <a href="cart.php" class="active">Giỏ hàng</a>
            <a href="history.php">Lịch sử mua hàng</a>
        </div>

        <?php if (count($cartList) > 0): ?>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover bg-white">
                        <thead class="thead-light">
                            <tr class="text-center">
                                <th width="50px">STT</th>
                                <th width="100px">Ảnh</th>
                                <th>Tên Sản Phẩm</th>
                                <th>Size</th> <th>Giá</th>
                                <th width="100px">Số lượng</th>
                                <th>Thành tiền</th>
                                <th width="50px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 0;
                            $total = 0;
                            
                            foreach ($cart as $index => $item_cookie) {
                                $product_db = null;
                                foreach ($cartList as $p) {
                                    if ($p['id'] == $item_cookie['id']) {
                                        $product_db = $p;
                                        break;
                                    }
                                }
                                
                                if ($product_db != null) {
                                    $num = $item_cookie['num'];
                                    $current_size = isset($item_cookie['size']) ? $item_cookie['size'] : 'S';
                                    $price = $product_db['price'];
                                    $money = floatval($num) * floatval($price);
                                    
                                    // Xác định tồn kho theo size
                                    $max_qty = 0;
                                    if ($current_size == 'S') $max_qty = $product_db['qty_s'];
                                    elseif ($current_size == 'M') $max_qty = $product_db['qty_m'];
                                    elseif ($current_size == 'L') $max_qty = $product_db['qty_l'];
                                    else $max_qty = $product_db['number'];

                                    // BỔ SUNG: KIỂM TRA TỒN KHO VÀ ÉP SỐ LƯỢNG KHI TẢI TRANG
                                    if (intval($num) > $max_qty) {
                                        $stock_warning = "LƯU Ý: Số lượng size $current_size đã được ép về mức tồn kho tối đa ($max_qty) do số lượng đã thay đổi.";
                                        $num = $max_qty; // Ép số lượng trong PHP về tồn kho
                                        $money = floatval($num) * floatval($price);
                                        echo '<tr><td colspan="8"><div class="alert alert-warning text-center small">'.$stock_warning.'</div></td></tr>';
                                    }
                                    
                                    $total += $money;

                                    echo '
                                    <tr class="text-center">
                                        <td>' . (++$count) . '</td>
                                        <td><img src="admin/product/' . $product_db['thumbnail'] . '" style="width: 70px; 
                                        height: 70px; object-fit: cover;"></td>
                                        <td class="text-left font-weight-bold">' . $product_db['title'] . '</td>
                                        
                                        <td>
                                            <select class="size-select" onchange="updateSize('.$product_db['id'].', this.value, 
                                            \''.$current_size.'\')">
                                                <option value="S" '.($current_size=='S'?'selected':'').'>S</option>
                                                <option value="M" '.($current_size=='M'?'selected':'').'>M</option>
                                                <option value="L" '.($current_size=='L'?'selected':'').'>L</option>
                                            </select>
                                        </td>
                                        
                                        <td>' . number_format($price, 0, ',', '.') . '</td>
                                        <td>
                                            <input type="number" value="'.$num.'" min="1" max="'.$max_qty.'" class="form-control text-center font-weight-bold" 
                                            onchange="updateCart('.$product_db['id'].', this.value, \''.$current_size.'\')">
                                            <small class="text-muted small">Tồn: '.$max_qty.'</small>
                                        </td>
                                        <td class="text-danger font-weight-bold">' . number_format($money, 0, ',', '.') . '</td>
                                        <td>
                                            <button class="btn-delete" title="Xóa" onclick="deleteCart(' . $product_db['id'] . ',
                                             \'' . $current_size . '\')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>';
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 text-right">
                    <div class="total-price">
                        Tổng cộng: <span class="text-danger font-weight-bold" 
                        style="font-size: 24px;"><?= number_format($total, 0, ',', '.') ?> VNĐ</span>
                    </div>
                </div>
                
                <div class="col-md-12">
                    <div class="cart-actions">
                        <a href="shop_product.php" class="btn btn-outline-secondary btn-custom">
                            <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
                        </a>
                        
                        <?php 
                        $loginUrl = "login.php";
                        if(isset($_COOKIE['tendangnhap'])) {
                            echo '<a href="checkout.php" class="btn btn-success btn-custom">
                                    Tiến hành thanh toán <i class="fas fa-arrow-right"></i>
                                  </a>';
                        } else {
                            echo '<a href="'.$loginUrl.'" class="btn btn-primary btn-custom">
                                    Đăng nhập để thanh toán
                                  </a>';
                        }
                        ?>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <div class="empty-cart">
                <h3>Giỏ hàng của bạn đang trống!</h3>
                <a href="shop_product.php" class="btn btn-primary mt-3 btn-custom">Quay lại cửa hàng</a>
            </div>
        <?php endif; ?>

    </div>
</main>

<script type="text/javascript">
    // Xóa sản phẩm
    function deleteCart(id, size) {
        if(!confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) return;
        $.post('api/cookie.php', {
            'action': 'delete',
            'id': id,
            'size': size
        }, function(data) {
            location.reload();
        })
    }

    // Cập nhật số lượng
    function updateCart(id, num, size) {
        // Kiểm tra số lượng phía client
        if (num < 1 || num == '') {
            alert('Số lượng phải lớn hơn hoặc bằng 1!');
            location.reload(); // Hoặc cập nhật lại giá trị input
            return;
        }
        // Server side (api/cookie.php) phải kiểm tra max_qty trước khi lưu
        $.post('api/cookie.php', {
            'action': 'update', 
            'id': id,
            'num': num,
            'size': size
        }, function(response_json) {
            // Đọc phản hồi JSON từ Server
            var response = JSON.parse(response_json);
            
            if (response.status === 'error') {
                alert(response.message);
            }
            // Luôn reload để cập nhật giỏ hàng và tổng tiền
            location.reload(); 
        });
    }

    function updateSize(id, newSize, oldSize) {
        $.post('api/cookie.php', {
            'action': 'change_size',
            'id': id,
            'size': oldSize,
            'newSize': newSize
        }, function(data) {
            location.reload();
        });
    }

</script>

<?php require_once('Layout/footer.php'); ?>
