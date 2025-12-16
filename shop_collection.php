<?php 
require_once('database/config.php');
require_once('database/dbhelper.php');
require_once('utils/utility.php');
include("Layout/header.php"); 
?>

<style>
    /* Layout */
    .shop-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 30px 0 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f1f1f1;
    }

    .shop-header h1 {
        font-size: 24px;
        font-weight: 700;
        text-transform: uppercase;
        margin: 0;
    }

    /* PRODUCT CARD */
    .product-item {
        border: 1px solid #eee;
        border-radius: 10px;
        overflow: hidden;
        transition: 0.25s;
        background: #fff;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .product-item:hover {
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        transform: translateY(-4px);
        border-color: transparent;
    }

    .product-item a {
        color: inherit;
        text-decoration: none;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .product-img-wrap {
        width: 100%;
        height: 240px;
        padding: 12px;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .product-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .product-info {
        padding: 15px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .product-title {
        font-size: 15px;
        font-weight: 600;
        height: 45px;
        overflow: hidden;
        margin-bottom: 10px;
        color: #333;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .product-price {
        font-size: 18px;
        font-weight: bold;
        color: #d9534f;
    }

    .product-meta {
        margin-top: 10px;
        padding-top: 10px;
        font-size: 12px;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #777;
    }

    .fa-star { color: #ffc107; font-size: 12px; }
</style>

<main style="background:#fcfcfc; min-height: 100vh; padding-bottom:50px;">
    <div class="container" style="padding-top: 100px;">
        <section class="main">

            <?php
            /*---------------------------------------------
                1. COLLECTION FILTER
            ----------------------------------------------*/
            $where = "WHERE 1=1";
            $pageTitle = "Bộ sưu tập";

            if (!empty($_GET['id_sanpham'])) {
                $id = intval($_GET['id_sanpham']);
                $where .= " AND product.id_sanpham = $id";

                $coll = executeSingleResult("SELECT name FROM collection WHERE id = $id");
                if ($coll) {
                    $pageTitle = $coll['name'];
                }
            }

            /*---------------------------------------------
                2. SORTING
            ----------------------------------------------*/
            $sort = $_GET['sort'] ?? 'new';
            $orderBy = "ORDER BY product.id DESC";
            $selectCol = "product.*";

            switch ($sort) {
                case 'price_asc':
                    $orderBy = "ORDER BY product.price ASC";
                    break;
                case 'price_desc':
                    $orderBy = "ORDER BY product.price DESC";
                    break;
                case 'best_selling':
                    $selectCol = "product.*, 
                    (SELECT IFNULL(SUM(num),0) FROM order_details 
                     WHERE product_id = product.id
                     AND (status='Đã nhận hàng' OR status='Đã thanh toán')
                    ) AS total_sold_sort";
                    $orderBy = "ORDER BY total_sold_sort DESC";
                    break;
            }

            $productList = executeResult("SELECT $selectCol FROM product $where $orderBy");
            ?>

            <div class="shop-header">
                <h1><?= $pageTitle ?> 
                    <small style="font-size:14px; color:#777;">(<?= count($productList) ?> sản phẩm)</small>
                </h1>

                <form method="GET" class="sort-box">
                    <?php 
                    if (!empty($_GET['id_sanpham'])) {
                        echo '<input type="hidden" name="id_sanpham" value="'.$_GET['id_sanpham'].'">';
                    }
                    ?>
                    <select name="sort" onchange="this.form.submit()">
                        <option value="new" <?= ($sort=='new'?'selected':'') ?>>Mới nhất</option>
                        <option value="best_selling" <?= ($sort=='best_selling'?'selected':'') ?>>Bán chạy nhất</option>
                        <option value="price_asc" <?= ($sort=='price_asc'?'selected':'') ?>>Giá tăng dần</option>
                        <option value="price_desc" <?= ($sort=='price_desc'?'selected':'') ?>>Giá giảm dần</option>
                    </select>
                </form>
            </div>

            <div class="row">
            <?php foreach ($productList as $item): 
                $pid = $item['id'];

                /* Lấy rating */
                $rate = executeSingleResult("SELECT AVG(rating) AS avg_star, COUNT(id) AS count_rate 
                                             FROM product_reviews 
                                             WHERE product_id = $pid");

                $star  = $rate['avg_star'] ? round($rate['avg_star'], 1) : 0;
                $count = $rate['count_rate'] ?? 0;

                /* Lấy lượt bán */
                if (isset($item['total_sold_sort'])) {
                    $sold = $item['total_sold_sort'];
                } else {
                    $soldRow = executeSingleResult("
                        SELECT IFNULL(SUM(num),0) AS sold
                        FROM order_details 
                        WHERE product_id = $pid 
                        AND (status='Đã nhận hàng' OR status='Đã thanh toán')
                    ");
                    $sold = $soldRow['sold'];
                }
            ?>
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-6 mb-4">
                    <div class="product-item">
                        <a href="details.php?id=<?= $item['id'] ?>">
                            <div class="product-img-wrap">
                                <img src="admin/product/<?= $item['thumbnail'] ?>" alt="<?= $item['title'] ?>">
                            </div>

                            <div class="product-info">
                                <div class="product-title"><?= $item['title'] ?></div>

                                <div class="product-price">
                                    <?= number_format($item['price'],0,',','.') ?> ₫
                                </div>

                                <div class="product-meta">
                                    <div>
                                        <i class="fas fa-star"></i>
                                        <?= $star ?> <small>(<?= $count ?>)</small>
                                    </div>
                                    <div>Đã bán: <strong><?= $sold ?></strong></div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (count($productList) == 0): ?>
                <div class="col-12 text-center" style="padding:80px 0;">
                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-state-2130362-1800926.png" style="width:200px;opacity:.7;">
                    <h5 class="mt-4 text-muted">Không có sản phẩm nào!</h5>
                    <a href="shop_product.php" class="btn btn-dark mt-2">Xem tất cả sản phẩm</a>
                </div>
            <?php endif; ?>
            </div>

        </section>
    </div>
</main>

<?php include("Layout/footer.php"); ?>
