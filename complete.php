    <?php
    // Xóa giỏ hàng NGAY TỪ ĐẦU trước khi có bất kỳ HTML nào
    setcookie('cart', '', time() - 3600, '/');

    require_once(__DIR__ . '/database/dbhelper.php');
    require_once(__DIR__ . '/utils/utility.php');

    require_once('Layout/header.php');
    ?>


    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main {
            flex: 1; 
            display: flex;
            justify-content: center;
            align-items: center; /* Căn giữa nội dung thông báo */
            background-color: #f8f9fa;
            padding: 50px 0;
        }

        /* 2. STYLE HỘP THÔNG BÁO */
        .success-box {
            background: #fff;
            padding: 50px 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            text-align: center;
            max-width: 600px;
            width: 90%;
            border-top: 5px solid #28a745; /* Thanh màu xanh trên cùng */
        }
        .icon-success {
            font-size: 80px;
            color: #28a745;
            margin-bottom: 25px;
            animation: popUp 0.5s ease-out;
        }
        @keyframes popUp {
            0% { transform: scale(0); opacity: 0; }
            80% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }
        .order-title {
            font-weight: 800;
            color: #333;
            margin-bottom: 15px;
            font-size: 28px;
            text-transform: uppercase;
        }
        .order-desc {
            font-size: 16px;
            color: #666;
            margin-bottom: 35px;
            line-height: 1.6;
        }
        .btn-group-custom {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        .btn-custom {
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 50px;
            text-transform: uppercase;
            font-size: 14px;
            transition: 0.3s;
        }
        .btn-home {
            background: #fff;
            border: 2px solid #333;
            color: #333;
        }
        .btn-home:hover {
            background: #333;
            color: #fff;
            text-decoration: none;
        }
        .btn-history {
            background: #28a745;
            border: 2px solid #28a745;
            color: #fff;
        }
        .btn-history:hover {
            background: #218838;
            border-color: #218838;
            color: #fff;
            text-decoration: none;
        }
        .btn {
            width: 270px;
        }
    </style>

    <main>
        <div class="success-box">
            <i class="fas fa-check-circle icon-success"></i>
            
            <h2 class="order-title">Đặt hàng thành công!</h2>
            
            <p class="order-desc">
                Cảm ơn bạn đã tin tưởng và mua sắm tại <strong>Dirty Coin</strong>.<br>
                Đơn hàng của bạn đã được hệ thống ghi nhận và đang trong quá trình xử lý. 
                Chúng tôi sẽ liên hệ sớm nhất để giao hàng.
            </p>
            
            <div class="btn-group-custom">
                <a href="index.php" class="btn btn-custom btn-home">
                    <i class="fas fa-arrow-left"></i> Về trang chủ
                </a>
                <a href="history.php" class="btn btn-custom btn-history">
                    Xem lịch sử đơn hàng <i class="fas fa-history"></i>
                </a>
            </div>
        </div>
        
    </main>
    <?php require_once('Layout/footer.php'); ?>

