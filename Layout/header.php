<?php
require_once(__DIR__ . '/../database/dbhelper.php');
require_once(__DIR__ . '/../utils/utility.php');
?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <link rel="stylesheet" href="plugin/fontawesome/css/all.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="./login.css">
    <link rel="shortcut icon" type="image/png" href="/Web/admin/product/uploads/avt3.png"/>
    <script src="https://kit.fontawesome.com/1147679ae7.js" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <title>Dirty Coin</title>
</head>

<header>
    <a href="/Web/index.php"><img src="/Web/images/avt.png" class="logo" style="width:130px;"></a>
    
    <div id="menu" style="margin-top:10px;">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li>
                <a href="#">Shop</a>
                <ul class="sub-menu">
                    <li><a href="shop_product.php" style="font-weight: bold;">ALL</a></li>
                    <?php
                    $sql = "SELECT * from category";
                    $result = executeResult($sql);
                    foreach($result as $item){
                        echo '<li><a href="shop_product.php?id_category=' . $item['id'] . '">' . $item['name'] . '</a></li>';
                    }
                    ?>
                </ul>
            </li>
            <li>
                <a href="#">Collection</a>
                <ul class="sub-menu">
                    <?php
                    $sql = "SELECT * from collection";
                    $result = executeResult($sql);
                    foreach($result as $item){
                        echo '<li><a href="shop_collection.php?id_sanpham=' . $item['id'] . '">' . $item['name'] . '</a></li>';
                    }
                    ?>
                </ul>
            </li>
            <li><a href="AboutUs/AboutUs/AboutUs.php">About us</a></li>
        </ul>
    </div>

    <div class="other">
        <div class="login"> 
            <?php
            if(isset($_COOKIE['tendangnhap'])) {
                $user = $_COOKIE['tendangnhap'];
                $role = isset($_COOKIE['role']) ? $_COOKIE['role'] : '';

                $displayText = $user;
                if($role == 'admin') $displayText .= ' (Admin)';
                elseif($role == 'staff') $displayText .= ' (Staff)';
                
                echo '<a style="color:black;" href="javascript:void(0);">' . $displayText . '</a>';
                
                // Menu Dropdown
                echo '<div class="logout">';

                // 1. Liên kết Quản lý/Quản trị riêng
                if($role == 'admin') {
                    echo '<a href="/Web/admin/login.php"><i class="fas fa-user-edit"></i> Quản trị</a>';
                } elseif($role == 'staff') {
                    echo '<a href="/Web/admin/product/index.php"><i class="fas fa-user-edit"></i> Quản lý SP</a>';
                }

                // 2. BỔ SUNG: Thông tin cá nhân cho customer
                if ($role == 'customer') {
                    echo '<a href="profile.php"><i class="fas fa-user-cog"></i> Thông tin cá nhân</a>'; 
                }

                // 3. LIÊN KẾT CHUNG: Đổi mật khấu
                if (isset($_COOKIE['tendangnhap'])) {
                    echo '<a href="change_password.php"><i class="fas fa-exchange-alt"></i> Đổi mật khẩu</a>';
                }

                // 4. Liên kết Đăng xuất
                echo '<a href="logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>';
                echo '</div>';
            } else {
                echo '<a href="login.php">Đăng nhập</a>';
            }
            ?>
        </div>
        
        <?php 
            $role = isset($_COOKIE['role']) ? $_COOKIE['role'] : '';
            if ($role != 'admin' && $role != 'staff') { 
            $cart = [];
            if (isset($_COOKIE['cart'])) {
            $json = $_COOKIE['cart'];
            $cart = json_decode($json, true);
            }
            $count = 0;
            foreach ($cart as $item) {
            $count += intval($item['num']); 
            }
        ?>
            <li>
                <a href="cart.php" style="text-decoration:none;">
                    <i class="fas fa-shopping-bag"></i>
                </a> 
                <span><?= $count ?></span>
            </li>
        <?php } ?>
    </div>
</header>

<style>
    /* CSS Chung */
    li{ list-style: none; }
    body{ background-color: white; }
    header{
        display:flex;
        justify-content: space-between;
        align-items: center;
        padding: 0px 5%;
        margin-top:0px; 
        position:fixed; 
        top:0;
        left:0;
        right:0;
        background-color: #ffffff;
        z-index: 1000;
        box-shadow: 2px 2px 2px rgba(241, 241, 241, 0.873);
        float: left;
    }
    header img{ width:150px; cursor:pointer; }
    .other{ display:flex; align-items: center; }
    .other >li{ padding:0 12px; position: relative; }
    
    /* Login Box Container */
    .login {
        padding: 0;
        border: 1px solid rgb(196, 196, 196);
        border-radius: 3px;
        margin: 0 20px;
        position: relative;
        min-width: 120px;
        text-align: center;
        cursor: pointer;
    }
    .login:hover {
        box-shadow: 0px 0px 3px 0px grey;
    }
    
    /* Tên User */
    .login > a { 
        padding: 10px 15px;
        text-decoration: none;
        color: #676767;
        font-weight: 700;
        display: block;
    }
    
    .login:hover .logout {
        display: block;
    }
    
    /* Dropdown Menu */
    .login .logout {
        display: none;
        position: absolute;
        top: 100%; 
        left: 50%;
        transform: translateX(-50%);
        z-index: 999;
        width: 180px; 
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: white;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        text-align: left;
        padding: 5px 0;
    }
    
    /* Link trong dropdown */
    .login .logout a {
        display: block; 
        padding: 8px 15px;
        color: #333;
        font-weight: 500;
        font-size: 14px;
        text-decoration: none;
        white-space: nowrap;
    }
    .login .logout a:hover {
        background-color: #f8f9fa;
        color: #007bff;
    }
    .login .logout a i {
        margin-right: 8px;
        width: 20px;
        text-align: center;
    }

    /* Menu Chính */
    #menu { list-style:none; display: flex; }
    #menu ul{ list-style-type: none; background:#ffffff; text-align: center; margin: 0; padding: 0;}
    #menu ul li{
        color:#0f0f0f;
        display:inline-block;
        width:120px;
        height:50px;
        line-height: 50px;
        position:relative;
    }
    #menu ul li a{
        color:#060606;
        text-decoration: none;
        display:block;
        font-size:17px;
    }
    #menu ul li a:hover{
        background:rgba(123, 123, 123, 0.1);
        color:#333;
    }
    #menu ul li >.sub-menu{
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        background-color: #ffffff;
        z-index: 10;
        list-style: none;
        width: 200px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        border: 1px solid #eee;
    }
    #menu ul li:hover .sub-menu{ display:block; }
    #menu ul li .sub-menu li {
        width: 100%;
        height: auto;
        line-height: 1.5;
    }
    #menu ul li .sub-menu li a {
        padding: 10px;
        font-size: 15px;
        text-align: left;
    }
</style>