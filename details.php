<?php
require_once('database/config.php');
require_once('database/dbhelper.php');
require_once('utils/utility.php');
include("Layout/header.php");

// 1. Lấy thông tin sản phẩm
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = 'select * from product where id=' . $id;
    $product = executeSingleResult($sql);
    if ($product == null) {
        header('Location: index.php');
        die();
    }

    $qty_s = isset($product['qty_s']) ? $product['qty_s'] : intval($product['number'] / 3);
    $qty_m = isset($product['qty_m']) ? $product['qty_m'] : intval($product['number'] / 3);
    $qty_l = isset($product['qty_l']) ? $product['qty_l'] : ($product['number'] - $qty_s - $qty_m);
}
// 2. Xử lý khi khách hàng Submit đánh giá (Giữ nguyên)
if (!empty($_POST['submit_review'])) {
    if (isset($_COOKIE['tendangnhap']) && isset($_COOKIE['role']) && $_COOKIE['role'] == 'customer') {
        $product_id = getPost('product_id');
        $rating = getPost('rating');
        $comment = getPost('comment');
        $fullname = isset($_COOKIE['user']) ? $_COOKIE['user'] : 'Khách hàng';
        $user_id = isset($_COOKIE['id_user']) ? $_COOKIE['id_user'] : 0;
        $created_at = date('Y-m-d H:i:s');

        // Sửa lỗi tên bảng reviews -> product_reviews
        $sql = "INSERT INTO product_reviews (product_id, user_id, fullname, rating, comment, created_at) 
                VALUES ('$product_id', '$user_id', '$fullname', '$rating', '$comment', '$created_at')";
        execute($sql);
        echo '<script>alert("Cảm ơn bạn đã đánh giá sản phẩm!");</script>';
    }
}
?>

<style>
/* CSS cho phần chọn size và đánh giá */
.size-selection .btn { border: 1px solid #ddd; margin-right: 5px; background: #fff; color: #333; }
.size-selection .btn.active { background-color: #333; color: #fff; border-color: #333; }

.rate { float: left; height: 46px; padding: 0 10px; }
.rate:not(:checked) > input { position:absolute; top:-9999px; }
.rate:not(:checked) > label { float:right; width:1em; overflow:hidden; white-space:nowrap; cursor:pointer; font-size:30px; color:#ccc; }
.rate:not(:checked) > label:before { content: '★ '; }
.rate > input:checked ~ label { color: #ffc700; }
.rate:not(:checked) > label:hover, .rate:not(:checked) > label:hover ~ label { color: #deb217; }
.rate > input:checked + label:hover, .rate > input:checked + label:hover ~ label, .rate > input:checked ~ label:hover, .rate > input:checked ~ label:hover ~ label, .rate > label:hover ~ input:checked ~ label { color: #c59b08; }
</style>

<main>
    <div class="container">
        <div style="padding-top: 100px;width:1300px;" id="ant-layout">
            <section class="search-quan">
                <i class="fas fa-search"></i>
                <form action="shop_product.php" method="GET" >
                    <input name="search" type="text" placeholder="Tìm đồ khác">
                </form>
            </section>
        </div>
        
        <section class="main">
            <section class="oder-product" >
                <div class="title">
                    <section class="main-order">
                        <h1><?= $product['title'] ?></h1>
                        <div class="box">
                            <div class="left" >
                                <li>
                                    <div class="main_image" ><img src="<?='admin/product/'.$product['thumbnail'] ?>" alt=""></div>
                                    <div class="main_image"><img src="<?='admin/product/'.$product['thumbnail_1'] ?>" alt=""></div>
                                    <div class="main_image"><img src="<?='admin/product/'.$product['thumbnail_2'] ?>" alt=""></div>
                                </li>
                                <li>
                                    <div class="main_image"><img src="<?='admin/product/'.$product['thumbnail_3'] ?>" alt=""></div>
                                    <div class="main_image"><img src="<?='admin/product/'.$product['thumbnail_4'] ?>" alt=""></div>
                                    <div class="main_image"><img src="<?='admin/product/'.$product['thumbnail_5'] ?>" alt=""></div>
                                </li>
                            </div>

                            <div class="about">
                                <p style="padding-top:105px;margin-left:10px; width:300px"><?= $product['content'] ?></p>
                                
                                <div class="size-selection" style="padding-top:10px;margin-left:10px;">
                                    <p style="font-weight:bold; margin-bottom: 5px;">Chọn Size:</p>
                                    <div id="myDIV">
                                        <button class="btn active" type="button" data-size="S" data-qty="<?= $qty_s ?>">S</button>
                                        <button class="btn" type="button" data-size="M" data-qty="<?= $qty_m ?>">M</button>
                                        <button class="btn" type="button" data-size="L" data-qty="<?= $qty_l ?>">L</button>
                                    </div>
                                    
                                    <p id="stock-msg" style="margin-top: 10px; color: #555; font-size: 14px;">
                                        Tồn kho: <span id="stock-qty" style="font-weight:bold; color:red;"><?= $qty_s ?></span> sản phẩm
                                    </p>
                                </div>
                                
                                <div class="number" style="padding-top:10px;margin-left:10px;">
                                    <span class="number-buy">Số lượng</span>
                                    <input id="num" type="number" value="1" min="1" max="<?= $qty_s ?>" onchange="updatePrice()"> 
                                </div>

                                <p class="price" style="padding-top:70px;margin-left:10px;">
                                    Giá: <span id="price"><?= number_format($product['price'], 0, ',', '.') ?></span><span> VNĐ</span>
                                    <span class="gia none"><?= $product['price'] ?></span>
                                </p>

                                <?php 
                                $role = isset($_COOKIE['role']) ? $_COOKIE['role'] : '';
                                if ($role == 'admin' || $role == 'staff') {
                                    echo '<div class="alert alert-warning" style="margin-left:10px; margin-top:20px;">
                                            Bạn đang truy cập với quyền <strong>'.ucfirst($role).'</strong>. <br>
                                            Vui lòng đăng nhập tài khoản Khách hàng để mua sắm.
                                          </div>';
                                } else {
                                ?>
                                    <button class="add-cart" style="margin-left:10px;" onclick="addToCart(<?= $id ?>)">
                                        <i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng
                                    </button>
                                    
                                    <button class="buy-now" style="margin-left:10px;" onclick="buyNow(<?= $id ?>)">
                                        Mua ngay
                                    </button>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="review-section" style="clear: both; padding-top: 30px;">
                            <h3 style="border-left: 5px solid #333; padding-left: 10px; margin-bottom: 20px;">Đánh giá sản phẩm</h3>
                            
                            <div class="write-review" style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
                                <h5 style="font-weight: bold;">Gửi nhận xét của bạn</h5>
                                
                                <?php if(isset($_COOKIE['tendangnhap'])): ?>
                                    <?php 
                                        $role = isset($_COOKIE['role']) ? $_COOKIE['role'] : '';
                                        if ($role == 'admin' || $role == 'staff'): 
                                    ?>
                                        <div class="alert alert-info" style="color: #31708f; background-color: #d9edf7; border-color: #bce8f1; padding: 15px; border-radius: 4px;">
                                            <i class="fas fa-info-circle"></i> Bạn đang đăng nhập với quyền <strong><?= ucfirst($role) ?></strong>. <br>
                                            Tính năng đánh giá chỉ dành cho tài khoản <strong>Khách hàng</strong>.
                                        </div>

                                    <?php else: ?>
                                        <form method="POST" action="">
                                            <input type="hidden" name="product_id" value="<?= $id ?>">
                                            <div class="form-group clearfix">
                                                <label style="float:left; margin-top:10px; margin-right:10px; font-weight:bold;">Đánh giá:</label>
                                                <div class="rate">
                                                    <input type="radio" id="star5" name="rating" value="5" checked />
                                                    <label for="star5" title="5 sao">5 stars</label>
                                                    <input type="radio" id="star4" name="rating" value="4" />
                                                    <label for="star4" title="4 sao">4 stars</label>
                                                    <input type="radio" id="star3" name="rating" value="3" />
                                                    <label for="star3" title="3 sao">3 stars</label>
                                                    <input type="radio" id="star2" name="rating" value="2" />
                                                    <label for="star2" title="2 sao">2 stars</label>
                                                    <input type="radio" id="star1" name="rating" value="1" />
                                                    <label for="star1" title="1 star">1 star</label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <textarea class="form-control" name="comment" rows="3" placeholder="Chia sẻ cảm nhận..." required></textarea>
                                            </div>
                                            <button type="submit" name="submit_review" value="1" class="btn btn-primary">Gửi đánh giá</button>
                                        </form>
                                    <?php endif; ?>

                                <?php else: ?>
                                    <p class="text-danger">Vui lòng <a href="login.php" style="text-decoration: underline; font-weight: bold;">Đăng nhập</a> để viết đánh giá.</p>
                                <?php endif; ?>
                            </div>

                            <div class="review-list">
                                <?php
                                $current_id = $id;
                                $sql_reviews = "SELECT * FROM product_reviews WHERE product_id = $current_id ORDER BY created_at DESC";
                                $list_reviews = executeResult($sql_reviews);

                                if (count($list_reviews) > 0) {
                                    echo '<h5 style="margin-bottom: 15px;">Các đánh giá từ khách hàng:</h5>';
                                    foreach ($list_reviews as $review) {
                                        echo '<div class="review-item" style="border-bottom: 1px solid #eee; padding: 15px 0;">
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            <span class="name" style="font-weight: bold; margin-right: 10px;">
                                                                <i class="fas fa-user-circle"></i> ' . $review['fullname'] . '
                                                            </span>
                                                            <span class="stars" style="color: #FFD700;">';
                                                            for ($i = 1; $i <= 5; $i++) {
                                                                echo ($i <= $review['rating']) ? '<i class="fas fa-star"></i>' : '<i class="far fa-star" style="color:#ccc;"></i>';
                                                            }
                                        echo '          </span>
                                                        </div>
                                                        <span class="date" style="font-size: 12px; color: #999;">' . date('d/m/Y', strtotime($review['created_at'])) . '</span>
                                                    </div>
                                                    <p class="comment" style="margin-top: 5px;">' . $review['comment'] . '</p>
                                                </div>';
                                    }
                                } else {
                                    echo '<p class="text-muted text-center" style="padding: 20px; border: 1px dashed #ccc;">Chưa có đánh giá nào cho sản phẩm này.</p>';
                                }
                                ?>
                            </div>
                        </div>

                    </section>
                </div>
            </section>

            <section class="restaurants">
                <div class="title">
                    <h1>Các sản phẩm khác tại DI<span class="green" style="color: red;">CO</span></h1>
                </div>
                <div class="product-restaurants">
                    <div class="row">
                        <?php
                        $sql = 'select * from product LIMIT 4';
                        $productList = executeResult($sql);
                        foreach ($productList as $item) {
                            $pid = $item['id'];
                            $sql_rate = "SELECT AVG(rating) as avg_star FROM product_reviews WHERE product_id = $pid";
                            $rate_data = executeSingleResult($sql_rate);
                            $star_avg = $rate_data['avg_star'] ? round($rate_data['avg_star'], 1) : 0;

                            echo '
                                <div class="col">
                                    <a href="details.php?id=' . $item['id'] . '">
                                        <img class="thumbnail" src="admin/product/' . $item['thumbnail'] . '" alt="">
                                        <div class="title">
                                            <p>' . $item['title'] . '</p>
                                        </div>
                                        <div class="price">
                                            <span>' . number_format($item['price'], 0, ',', '.') . ' VNĐ</span>
                                        </div>
                                        <div class="more">
                                            <div class="star">
                                                <i class="fas fa-star text-warning"></i> <span>' . $star_avg . '</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>';
                        }
                        ?>
                    </div>
                </div>
            </section>
        </section>
    </div>
</main>

<script type="text/javascript">
    // HÀM SỬA LỖI ĐỌC NULL VÀ CẬP NHẬT TỔNG TIỀN/TỒN KHO
    function updatePrice() {
        // Lấy giá trị tiền thô an toàn
        var gia1Element = document.querySelector('.gia');
        // Xóa ký tự phân cách (nếu có) và chuyển sang float
        var gia1 = parseFloat(gia1Element ? gia1Element.innerText.replace(/\s/g, '').replace(/\./g, '').replace(/,/g, '') : 0); 
        
        var numInput = document.querySelector('#num');
        var num = parseInt(numInput.value); 
        
        // Lấy tồn kho an toàn từ nút size đang active
        var sizeBtn = document.querySelector('#myDIV .active');
        var maxQty = parseInt(sizeBtn ? sizeBtn.getAttribute('data-qty') : 0); 
        
        var btnAddCart = document.querySelector('.add-cart');
        var btnBuyNow = document.querySelector('.buy-now');

        if (isNaN(num) || num < 1) {
             num = 1; 
             numInput.value = 1;
        }

        // TỰ ĐỘNG GIỚI HẠN SỐ LƯỢNG INPUT THEO TỒN KHO VÀ VÔ HIỆU HÓA NÚT
        if (num > maxQty) {
            alert('Số lượng yêu cầu (' + num + ') vượt quá tồn kho (' + maxQty + ').');
            numInput.value = maxQty; // Đặt lại số lượng bằng tồn kho
            num = maxQty; // Cập nhật biến num
            
            if(btnAddCart) btnAddCart.disabled = true;
            if(btnBuyNow) btnBuyNow.disabled = true;
        } else if (num <= 0) {
            numInput.value = 1; 
            num = 1;
        } else {
            if(btnAddCart) btnAddCart.disabled = false;
            if(btnBuyNow) btnBuyNow.disabled = false;
        }

        var tong = gia1 * num;
        document.getElementById('price').innerHTML = tong.toLocaleString('vi-VN');
    }

    // Logic chọn size và cập nhật tồn kho
    var header = document.getElementById("myDIV");
    var btns = header.getElementsByClassName("btn");
    
    for (var i = 0; i < btns.length; i++) {
        btns[i].addEventListener("click", function() {
            var current = document.getElementsByClassName("active");
            if (current.length > 0) { 
                current[0].className = current[0].className.replace(" active", "");
            }
            this.className += " active";

            var qty = this.getAttribute("data-qty");
            var stockMsg = document.getElementById("stock-msg");
            var numInput = document.querySelector('#num');
            var btnAddCart = document.querySelector('.add-cart');
            var btnBuyNow = document.querySelector('.buy-now');
            
            document.getElementById("stock-qty").innerText = qty; // Cập nhật số tồn kho hiển thị

            // Cập nhật thuộc tính MAX của input #num
            numInput.setAttribute('max', qty); 

            if(parseInt(qty) <= 0) {
                stockMsg.innerHTML = "Tồn kho: <span style='color:red; font-weight:bold'>Hết hàng</span>";
                if(btnAddCart) {
                    btnAddCart.disabled = true;
                    btnAddCart.style.opacity = "0.5";
                    btnAddCart.style.cursor = "not-allowed";
                }
                if(btnBuyNow) {
                    btnBuyNow.disabled = true;
                    btnBuyNow.style.opacity = "0.5";
                    btnBuyNow.style.cursor = "not-allowed";
                }
            } else {
                stockMsg.innerHTML = "Tồn kho: <span style='font-weight:bold; color:red;'>" + qty + "</span> sản phẩm";
                if(btnAddCart) {
                    btnAddCart.disabled = false;
                    btnAddCart.style.opacity = "1";
                    btnAddCart.style.cursor = "pointer";
                }
                if(btnBuyNow) {
                    btnBuyNow.disabled = false;
                    btnBuyNow.style.opacity = "1";
                    btnBuyNow.style.cursor = "pointer";
                }
            }
            // Quan trọng: Gọi updatePrice() khi thay đổi size để kiểm tra số lượng hiện tại
            updatePrice();
        });
    }
    // Kích hoạt size mặc định (S) khi tải trang lần đầu
    document.addEventListener("DOMContentLoaded", function() {
        var defaultSizeBtn = document.querySelector('#myDIV .btn.active');
        if (defaultSizeBtn) {
            defaultSizeBtn.click();
        }
    });

    // Hàm kiểm tra và thêm vào giỏ hàng
    function addToCart(id) {
        var num = parseInt(document.querySelector('#num').value); 
        var sizeBtn = document.querySelector('#myDIV .active');
        var size = sizeBtn ? sizeBtn.getAttribute('data-size') : 'S';
        var maxQty = parseInt(sizeBtn ? sizeBtn.getAttribute('data-qty') : 0);

        if (isNaN(num) || num <= 0) {
            alert('Số lượng mua không hợp lệ. Vui lòng chọn số lượng lớn hơn 0.');
            return;
        }
        if (num > maxQty) {
            alert('Số lượng yêu cầu (' + num + ') vượt quá số lượng tồn kho (' + maxQty + ') của size ' + size + '!');
            return;
        }

        $.ajax({
        url: 'api/cookie.php',
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'add',
            id: id,
            num: num,
            size: size
        },
        success: function(response) {
            if (response.status === 'error') {
                alert(response.message);
            } else {
                location.reload();
            }
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            alert('Lỗi server');
        }
    });

    }

    // Hàm kiểm tra và mua ngay
    function buyNow(id) {
        var num = parseInt(document.querySelector('#num').value);
        var sizeBtn = document.querySelector('#myDIV .active');
        var size = sizeBtn ? sizeBtn.getAttribute('data-size') : 'S';
        var maxQty = parseInt(sizeBtn ? sizeBtn.getAttribute('data-qty') : 0);

        if (isNaN(num) || num <= 0) {
            alert('Số lượng mua không hợp lệ. Vui lòng chọn số lượng lớn hơn 0.');
            return;
        }
        if (num > maxQty) {
            alert('Số lượng yêu cầu (' + num + ') vượt quá số lượng tồn kho (' + maxQty + ') của size ' + size + '!');
            return;
        }

        $.ajax({
        url: 'api/cookie.php',
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'add',
            id: id,
            num: num,
            size: size
        },
        success: function(response) {
            if (response.status === 'error') {
                alert(response.message);
            } else {
                location.assign("checkout.php");
            }
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            alert('Lỗi server');
        }
    });

    }
</script>

<?php require_once('Layout/footer.php'); ?>