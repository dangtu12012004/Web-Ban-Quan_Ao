<?php 
require_once('database/config.php');
require_once('database/dbhelper.php');
require_once('utils/utility.php');
include("Layout/header.php"); 
?>

<style>
/* CSS giữ nguyên */
    .product-list-row {
        display: flex !important;
        flex-wrap: wrap !important;
        margin-right: -15px;
        margin-left: -15px;
    }
    
    .product-col {
        padding-right: 15px;
        padding-left: 15px;
        box-sizing: border-box;
    }
    .shop-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f1f1f1;
        margin-top: 30px;
    }
    .shop-header h1 {
        font-size: 24px;
        font-weight: 700;
        text-transform: uppercase;
        margin: 0;
        color: #333;
    }
    
    /* Search Box */
    .search-quan {
        margin-bottom: 20px; 
        text-align: center;
        position: relative;
        padding-top: 100px;
    }
    .search-quan form {
        width: 100%; 
        max-width: 600px; 
        margin: 0 auto; 
        position: relative;
    }
    .search-quan input {
        width: 100%;
        padding: 12px 20px 12px 45px;
        border-radius: 30px;
        border: 1px solid #ddd;
        outline: none;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        transition: 0.3s;
    }
    .search-quan input:focus {
        border-color: #333;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .search-quan i {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: #999;
    }
    /* Product Card */
    .product-item {
        border: 1px solid #eee;
        border-radius: 10px;
        overflow: hidden;
        transition: all 0.3s ease;
        background: #fff;
        height: 100%; 
        display: flex;
        flex-direction: column;
        position: relative;
        margin-bottom: 30px; 
    }
    .product-item:hover {
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        transform: translateY(-5px);
        border-color: transparent;
    }
    .product-item a {
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .product-item .thumbnail {
        width: 100%;
        height: 250px;
        object-fit: contain; 
        padding: 15px;
        background: #fff;
    }
    .product-item .info {
        padding: 15px;
        text-align: center;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        background-color: #fff;
        border-top: 1px solid #f9f9f9;
    }
    .product-item .title {
        font-weight: 600;
        font-size: 16px;
        line-height: 1.4;
        height: 44px; 
        overflow: hidden;
        margin-bottom: 10px;
        color: #333;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    .product-item .price {
        color: #d9534f;
        font-weight: 700;
        font-size: 18px;
        margin-bottom: 5px;
    }
    .product-item .meta {
        font-size: 12px;
        color: #777;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .fa-star { color: #ffc107; font-size: 12px; }
    /* Sort Select */
    .sort-box select {
        padding: 8px 30px 8px 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        outline: none;
        cursor: pointer;
        background-color: #fff;
    }
    @media (min-width: 992px) {
        .product-list-row .product-col {
            flex: 0 0 25%;
            max-width: 25%;
        }
    }

</style>

<main style="background-color: #fcfcfc; min-height: 100vh; padding-bottom: 50px;">
    <div class="container">
        <div id="ant-layout">
            <section class="search-quan">
                <form action="shop_product.php" method="GET">
                    <i class="fas fa-search"></i>
                    <input name="search" type="text" placeholder="Bạn muốn tìm sản phẩm gì hôm nay?" value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>">
                </form>
            </section>
        </div>

        <section class="main">
            <?php
            // ===========================================================
            // 1. XỬ LÝ LOGIC LỌC & QUERY
            // ===========================================================
            $whereClause = "WHERE 1=1";
            $pageTitle = "Tất cả sản phẩm";
            $error_message = ''; // KHỞI TẠO BIẾN LỖI

            // Lọc theo Danh mục
            if (isset($_GET['id_category']) && !empty($_GET['id_category'])) {
                $id_category = trim(strip_tags($_GET['id_category']));
                $whereClause .= " AND product.id_category = $id_category";
                
                $sqlCat = "SELECT name FROM category WHERE id=$id_category";
                $cat = executeSingleResult($sqlCat);
                if($cat) $pageTitle = $cat['name'];
            }
            // Lọc theo Tìm kiếm
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $search = trim(strip_tags($_GET['search']));
                $whereClause .= " AND product.title LIKE '%$search%'";
                $pageTitle = "Kết quả tìm kiếm: \"$search\"";
            }
            
            // LỌC THEO KHOẢNG GIÁ VÀ XỬ LÝ LỖI GIÁ TRỊ ÂM
            $min_price_input = isset($_GET['min_price']) ? $_GET['min_price'] : '';
            $max_price_input = isset($_GET['max_price']) ? $_GET['max_price'] : '';
            
            $min_price_safe = '';
            $max_price_safe = '';

            // Xử lý Min Price
            if ($min_price_input !== '') {
                if (is_numeric($min_price_input) && intval($min_price_input) >= 0) {
                    $min_price_safe = intval($min_price_input);
                } else {
                    $error_message .= "Giá tối thiểu không hợp lệ hoặc là số âm. ";
                    // Đặt lại giá trị input để hiển thị lỗi mà không áp dụng bộ lọc
                    $min_price_input = ''; 
                }
            }

            // Xử lý Max Price
            if ($max_price_input !== '') {
                if (is_numeric($max_price_input) && intval($max_price_input) > 0) {
                    $max_price_safe = intval($max_price_input);
                } else {
                    $error_message .= "Giá tối đa không hợp lệ hoặc là số âm. ";
                    // Đặt lại giá trị input để hiển thị lỗi mà không áp dụng bộ lọc
                    $max_price_input = '';
                }
            }
            
            // ÁP DỤNG BỘ LỌC VÀO WHERE CLAUSE CHỈ KHI KHÔNG CÓ LỖI
            if (empty($error_message)) {
                if ($min_price_safe !== '') {
                    $whereClause .= " AND product.price >= $min_price_safe";
                }
                if ($max_price_safe !== '') {
                    $whereClause .= " AND product.price <= $max_price_safe";
                }
            }
            // ===========================================================
            // 2. XỬ LÝ SẮP XẾP
            // ===========================================================
            $sort_option = isset($_GET['sort']) ? $_GET['sort'] : 'new';
            $orderBy = "ORDER BY product.id DESC"; 
            $select_col = "product.*";

            switch ($sort_option) {
                case 'price_asc':
                    $orderBy = "ORDER BY product.price ASC";
                    break;
                case 'price_desc':
                    $orderBy = "ORDER BY product.price DESC";
                    break;
                case 'best_selling':
                    $select_col = "product.*, (SELECT IFNULL(SUM(num), 0) FROM order_details WHERE product_id = product.id 
                    AND (status = 'Đã nhận hàng' OR status = 'Đã thanh toán')) as total_sold_sort";
                    $orderBy = "ORDER BY total_sold_sort DESC";
                    break;
            }
            $sql = "SELECT $select_col FROM product $whereClause $orderBy";
            $productList = executeResult($sql);
            ?>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger mt-3">
                    <strong>Lỗi lọc giá:</strong> <?= $error_message ?>
                </div>
            <?php endif; ?>
            
            <section class="recently">
                <div class="shop-header">
                    <h1><?= $pageTitle ?> <small style="font-size: 16px; color: #777; font-weight: normal;">(<?= count($productList) ?> sản phẩm)</small></h1>
                    
                    <div class="sort-box">
                        <form action="" method="GET">
                            <?php 
                            // GIỮ LẠI CÁC THÔNG SỐ LỌC KHÁC KHI SẮP XẾP
                            if(isset($_GET['id_category'])) echo '<input type="hidden" name="id_category" value="'.$_GET['id_category'].'">';
                            if(isset($_GET['search'])) echo '<input type="hidden" name="search" value="'.$_GET['search'].'">';
                            // GIỮ LẠI BỘ LỌC GIÁ CŨ KHI SẮP XẾP (sử dụng giá trị input gốc để giữ lại)
                            if ($min_price_input !== '') echo '<input type="hidden" name="min_price" value="'.$min_price_input.'">';
                            if ($max_price_input !== '') echo '<input type="hidden" name="max_price" value="'.$max_price_input.'">';
                            ?>
                            <select name="sort" onchange="this.form.submit()">
                                <option value="new" <?= ($sort_option == 'new') ? 'selected' : '' ?>>Mới nhất</option>
                                <option value="best_selling" <?= ($sort_option == 'best_selling') ? 'selected' : '' ?>>Bán chạy nhất</option>
                                <option value="price_asc" <?= ($sort_option == 'price_asc') ? 'selected' : '' ?>>Giá: Tăng dần</option>
                                <option value="price_desc" <?= ($sort_option == 'price_desc') ? 'selected' : '' ?>>Giá: Giảm dần</option>
                            </select>
                        </form>
                    </div>
                    
                    <div style="display:flex; gap:10px; align-items:center;">
                        <form action="" method="GET" style="display:flex; gap:5px;">
                            <?php
                            // GIỮ CÁC THAM SỐ LỌC KHÁC KHI LỌC GIÁ
                            if(isset($_GET['id_category'])) echo '<input type="hidden" name="id_category" value="'.$_GET['id_category'].'">';
                            if(isset($_GET['search'])) echo '<input type="hidden" name="search" value="'.$_GET['search'].'">';
                            if(isset($_GET['sort'])) echo '<input type="hidden" name="sort" value="'.$_GET['sort'].'">';
                            ?>

                            <input type="number" name="min_price" placeholder="Giá từ"
                                value="<?php echo $min_price_input; ?>"
                                style="width:110px; padding:5px;">

                            <span>—</span>

                            <input type="number" name="max_price" placeholder="Đến"
                                value="<?php echo $max_price_input; ?>"
                                style="width:110px; padding:5px;">

                            <button type="submit" class="btn btn-dark btn-sm">Lọc</button>
                        </form>
                    </div>

                </div>
                <div class="product-recently">
                    <div class="product-list-row">
                        <?php
                        if (count($productList) > 0) {
                            foreach ($productList as $item) {
                                // Query dữ liệu phụ (Sao, Lượt bán)
                                $pid = $item['id'];
                                $sql_rate = "SELECT AVG(rating) as avg_star, COUNT(id) as count_rate FROM product_reviews WHERE product_id = $pid";
                                $rate_data = executeSingleResult($sql_rate);
                                $star = $rate_data['avg_star'] ? round($rate_data['avg_star'], 1) : 0;
                                $count_rate = $rate_data['count_rate'] ? $rate_data['count_rate'] : 0;

                                if (isset($item['total_sold_sort'])) {
                                    $sold = $item['total_sold_sort'];
                                } else {
                                    $sql_sold = "SELECT IFNULL(SUM(num), 0) as total_sold FROM order_details WHERE product_id = $pid 
                                    AND (status = 'Đã nhận hàng' OR status = 'Đã thanh toán')";
                                    $sold_data = executeSingleResult($sql_sold);
                                    $sold = $sold_data['total_sold'];
                                }
                                echo '
                                <div class="col-lg-3 col-md-3 col-sm-6 col-6 product-col mb-4">
                                    <div class="product-item">
                                        <a href="details.php?id=' . $item['id'] . '">
                                            <div style="position: relative; overflow: hidden; width: 100%;">
                                                <img class="thumbnail" src="admin/product/' . $item['thumbnail'] . '" alt="' . $item['title'] . '">
                                            </div>
                                            <div class="info">
                                                <div class="title" title="'.$item['title'].'">' . $item['title'] . '</div>
                                                <div class="price">' . number_format($item['price'], 0, ',', '.') . ' ₫</div>
                                                
                                                <div class="meta">
                                                    <div class="star-rating" title="'.$count_rate.' đánh giá">
                                                        <i class="fas fa-star"></i> <span>' . $star . '</span>
                                                        <span style="color:#aaa; font-size:11px;">('.$count_rate.')</span>
                                                    </div>
                                                    <div class="sold-count">
                                                        Đã bán: <strong>' . $sold . '</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>';
                            }
                        } else {
                            echo '<div class="col-12 text-center" style="padding: 80px 0;">
                                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-state-2130362-1800926.png" style="width:200px; opacity: 0.7;">
                                    <h5 class="mt-4 text-muted">Không tìm thấy sản phẩm nào!</h5>
                                    <a href="shop_product.php" class="btn btn-dark mt-2">Xem tất cả sản phẩm</a>
                                  </div>';
                        }
                        ?>
                    </div>
                </div>
            </section>
        </section>
    </div>
</main>

<?php include("Layout/footer.php"); ?>
