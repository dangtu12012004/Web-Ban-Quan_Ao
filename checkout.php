<?php

require_once('database/config.php');
require_once('database/dbhelper.php');
require_once('utils/utility.php');
require_once('Layout/header.php');

// 1. Kiểm tra giỏ hàng
$cart = [];
if (isset($_COOKIE['cart'])) {
    $json = $_COOKIE['cart'];
    $cart = json_decode($json, true);
}

if ($cart == null || count($cart) == 0) {
    header('Location: index.php');
    die();
}

// 2. Tính tổng tiền 
$total_money = 0;
foreach ($cart as $item) {
    $product_id = $item['id'];
    $pro = executeSingleResult("SELECT price FROM product WHERE id = $product_id");
    if ($pro != null) {
        // Ép kiểu num và price thành số trước khi nhân để tránh TypeError
        $total_money += intval($item['num']) * floatval($pro['price']); 
    }
}


// 3. Tạo nội dung chuyển khoản ngẫu nhiên
$random_content = "DICO" . rand(1000, 9999); 
$checkout_error = ''; 

// 4. XỬ LÝ KHI BẤM ĐẶT HÀNG
if (!empty($_POST)) {
    $fullname = getPost('fullname');
    $email = getPost('email');
    $phone_number = getPost('phone_number');
    $address = getPost('address');
    $note = getPost('note');
    $order_date = date('Y-m-d H:i:s');
    $payment_method = getPost('payment_method'); // 'COD' hoặc 'QR'

    // --- XỬ LÝ TRẠNG THÁI ---
    if ($payment_method == 'QR') {
        $status_detail = 'Đã thanh toán'; 
        if(isset($_POST['bank_content'])) {
            $note .= " | CK: " . $_POST['bank_content']; 
        }
    } else {
        $status_detail = 'Đang chuẩn bị'; 
    }
    
    $transaction_sqls = [];
    $can_checkout = true;
    
    // --- 4.1 KIỂM TRA & TRỪ TỒN KHO TRƯỚC ---
    foreach ($cart as $item) {
        $product_id = $item['id'];
        $num = intval($item['num']);
        $size = isset($item['size']) ? $item['size'] : 'N/A';

        // Lấy tồn kho hiện tại 
        $sql_stock = "SELECT qty_s, qty_m, qty_l FROM product WHERE id = $product_id";
        $stock_data = executeSingleResult($sql_stock);
        
        if ($stock_data == null) {
            $checkout_error = "Lỗi hệ thống: Sản phẩm ID $product_id không tồn tại.";
            $can_checkout = false;
            break;
        }

        $stock_field = '';
        if ($size == 'S') { $stock_field = 'qty_s'; $qty_available = intval($stock_data['qty_s']); }
        elseif ($size == 'M') { $stock_field = 'qty_m'; $qty_available = intval($stock_data['qty_m']); }
        elseif ($size == 'L') { $stock_field = 'qty_l'; $qty_available = intval($stock_data['qty_l']); }
        else { $qty_available = 9999; } 

        if ($num > $qty_available) {
            $checkout_error = "Lỗi tồn kho: Sản phẩm ID $product_id (Size $size) chỉ còn $qty_available cái. Vui lòng cập nhật giỏ hàng.";
            $can_checkout = false;
            break;
        }

        // Thêm lệnh trừ tồn kho
        if (!empty($stock_field)) {
             $transaction_sqls[] = "UPDATE product SET $stock_field = $stock_field - $num, updated_at = NOW() WHERE id = $product_id";
        }
    }
    
    if ($can_checkout) {
        // --- 4.2. INSERT ORDERS ---
        $sql_insert_order = "INSERT INTO orders(fullname, email, phone_number, address, note, order_date, payment_method) 
                             VALUES ('$fullname', '$email', '$phone_number', '$address', '$note', '$order_date', '$payment_method')";
        execute($sql_insert_order); 

        // Lấy ID đơn hàng vừa tạo (ĐIỂM YẾU: CÓ KHẢ NĂNG GẶP RACE CONDITION)
        $sql_get_order = "SELECT id FROM orders WHERE order_date = '$order_date' AND fullname = '$fullname' ORDER BY id DESC LIMIT 1";
        $order = executeSingleResult($sql_get_order);
        $order_id = $order['id'];

        // --- 4.3. Lệnh INSERT ORDER_DETAILS ---
        $id_user = 0;
        if (isset($_COOKIE['tendangnhap'])) {
            $username = $_COOKIE['tendangnhap'];
            $sql_user = "SELECT id_dangky FROM tbl_dangky WHERE tendangnhap = '$username'";
            $user_data = executeSingleResult($sql_user);
            if ($user_data != null) $id_user = $user_data['id_dangky'];
        }
        
        foreach ($cart as $item) {
            $product_id = $item['id'];
            $num = intval($item['num']);
            $size = isset($item['size']) ? $item['size'] : 'N/A';
            
            $pro = executeSingleResult("SELECT price FROM product WHERE id = $product_id");
            $price = $pro ? floatval($pro['price']) : 0;

            // Thêm lệnh INSERT order_details
            $transaction_sqls[] = "INSERT INTO order_details(order_id, product_id, id_user, num, price, status, size) 
                                 VALUES ($order_id, $product_id, $id_user, $num, $price, '$status_detail', '$size')";
        }

        // --- 4.4 THỰC HIỆN TRỪ TỒN KHO VÀ CHI TIẾT ĐƠN HÀNG ---
        $transaction_success = true; 
        foreach ($transaction_sqls as $sql) {
             execute($sql); 
        }

        if ($transaction_success) {
            // 5. Xóa giỏ hàng & Chuyển hướng
            setcookie('cart', '', time() - 100, '/');
            echo '<script>window.location.href = "complete.php";</script>';
            die();
        } else {
            $checkout_error = "Lỗi hệ thống: Đã xảy ra lỗi trong quá trình xử lý đơn hàng (Transaction Failed). Vui lòng thử lại.";
        }
    }
}
?>

<style>
/* CSS giữ nguyên */
    .payment-option {
        border: 1px solid #ddd;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 15px;
        cursor: pointer;
        transition: 0.3s;
    }
    .payment-option:hover { background-color: #f9f9f9; border-color: #333; }
    .payment-option label { font-weight: bold; width: 100%; cursor: pointer; display: block;}
    
    .qr-section {
        display: none;
        text-align: center;
        background: #fff;
        padding: 20px;
        border: 2px solid #28a745;
        border-radius: 10px;
        margin-top: 15px;
    }
    .qr-section img { 
        max-width: 100%; 
        width: 250px; /* Kích thước ảnh QR */
        margin-bottom: 10px; 
        border: 1px solid #eee;
    }
    .qr-info {
        background: #f8f9fa;
        padding: 10px;
        border-radius: 5px;
        margin-top: 10px;
        text-align: left;
        display: inline-block;
    }
    .qr-info p { margin: 5px 0; font-size: 14px; }
    .highlight { color: #d9534f; font-weight: bold; font-size: 16px; }
    .btn{
        width: 300px;
    }
</style>

<main>
    <div class="container" style="margin-top: 80px; margin-bottom: 50px;">
        <form method="POST" id="checkoutForm">
            <div class="row">
                <div class="col-md-6">
                    <h3 style="border-bottom: 2px solid #e1e1e1; padding-bottom: 10px;">Thông tin giao hàng</h3>
                    <?php if (isset($checkout_error) && $checkout_error): ?>
                        <div class="alert alert-danger"><?=$checkout_error?></div>
                    <?php endif; ?>
                    <?php
                    $user_info = ['tenkhachhang'=>'', 'email'=>'', 'dienthoai'=>'', 'diachi'=>''];
                    if(isset($_COOKIE['tendangnhap'])){
                        $username = $_COOKIE['tendangnhap'];
                        $sql = "SELECT * FROM tbl_dangky WHERE tendangnhap = '$username'";
                        $user_data = executeSingleResult($sql);
                        if($user_data) $user_info = $user_data;
                    }
                    ?>
                    <div class="form-group">
                        <label>Họ và tên:</label>
                        <input type="text" name="fullname" class="form-control" required value="<?=$user_info['tenkhachhang']?>">
                    </div>
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" class="form-control" required value="<?=$user_info['email']?>">
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại:</label>
                        <input type="text" name="phone_number" class="form-control" required value="<?=$user_info['dienthoai']?>">
                    </div>
                    <div class="form-group">
                        <label>Địa chỉ giao hàng:</label>
                        <input type="text" name="address" class="form-control" required value="<?=$user_info['diachi']?>">
                    </div>
                    <div class="form-group">
                        <label>Ghi chú:</label>
                        <textarea name="note" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <h3 style="border-bottom: 2px solid #e1e1e1; padding-bottom: 10px;">Đơn hàng & Thanh toán</h3>
                    <table class="table table-bordered bg-white">
                        <thead>
                            <tr><th>Sản phẩm</th><th>Size</th><th>SL</th><th>Thành tiền</th></tr>
                        </thead>
                        <tbody>
                            <?php 
                            foreach ($cart as $item): 
                                $product_id = $item['id'];
                                $size = isset($item['size']) ? $item['size'] : 'N/A';
                                $num = $item['num'];
                                // Lấy sản phẩm từ DB
                                $sql = "SELECT title, price FROM product WHERE id = $product_id";
                                $pro = executeSingleResult($sql);
                                if ($pro == null) continue;
                                $title = $pro['title'];
                                $price = $pro['price'];
                            ?>
                                <tr>
                                    <td><?=$title?></td>
                                    <td class="text-center"><strong><?=$size?></strong></td>
                                    <td class="text-center"><?=$num?></td>
                                    <td><?=number_format(intval($num) * floatval($price), 0, ',', '.')?> đ</td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                    </table>
                    <h4 class="mt-4 text-right">
                        Tổng thanh toán: <span class="text-danger font-weight-bold"><?=number_format($total_money,0,',','.')?> VNĐ</span>
                    </h4>

                    <h5 class="mt-4">Hình thức thanh toán:</h5>
                    <div class="payment-option">
                        <label>
                            <input type="radio" name="payment_method" value="COD" checked onchange="togglePayment('COD')"> 
                            <i class="fas fa-truck"></i> Thanh toán khi nhận hàng (COD)
                        </label>
                    </div>

                    <div class="payment-option">
                        <label>
                            <input type="radio" name="payment_method" value="QR" onchange="togglePayment('QR')"> 
                            <i class="fas fa-qrcode"></i> Chuyển khoản ngân hàng
                        </label>
                        <div id="qr-box" class="qr-section">
                            <p style="font-weight:bold; color: #28a745;">Quét mã để thanh toán:</p>
                            
                            <img src="images/qr_code.png" alt="QR Ngân Hàng"> 
                            
                            <div class="qr-info">
                                <p>Số tiền: <span class="highlight"><?=number_format($total_money,0,',','.')?> đ</span></p>
                                <p>Nội dung CK: <span class="highlight"><?=$random_content?></span></p>
                                <p style="font-style: italic; color: #666; font-size: 13px;">(Vui lòng nhập đúng nội dung chuyển khoản)</p>
                            </div>
                            <input type="hidden" name="bank_content" value="<?=$random_content?>">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success btn-block mt-3" style="font-size: 18px; padding: 10px;" id="btn-submit">
                        ĐẶT HÀNG NGAY
                    </button>
                </div>
            </div>
        </form>
    </div>
</main>
<script>
    function togglePayment(method) {
        var qrBox = document.getElementById('qr-box');
        var btnSubmit = document.getElementById('btn-submit');

        if (method === 'QR') {
            $(qrBox).slideDown(); 
            btnSubmit.innerHTML = '<i class="fas fa-check-circle"></i> XÁC NHẬN ĐÃ THANH TOÁN';
            btnSubmit.classList.remove('btn-success');
            btnSubmit.classList.add('btn-primary');
        } else {
            $(qrBox).slideUp();
            btnSubmit.innerHTML = 'ĐẶT HÀNG NGAY';
            btnSubmit.classList.remove('btn-primary');
            btnSubmit.classList.add('btn-success');
        }
    }
</script>

<?php require_once('Layout/footer.php'); ?>