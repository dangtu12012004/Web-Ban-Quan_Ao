<?php
// Đường dẫn file require (Đã sửa đúng ở trên)
require_once(__DIR__ . '/../../database/dbhelper.php');
require_once(__DIR__ . '/../../utils/utility.php');
?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    
    <link rel="stylesheet" href="../../plugin/fontawesome/css/all.css">
    <link rel="stylesheet" href="../../style.css">
    <link rel="stylesheet" href="../../login.css">
    <link rel="shortcut icon" type="image/png" href="../../admin/product/uploads/avt3.png"/>
    <script src="https://kit.fontawesome.com/1147679ae7.js" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <title>Dirty Coin</title>
</head>

<header>
    <a href="../../index.php"><img src="../../images/avt.png" class="logo" style="width:130px;"></a>
    
    <div id="menu" style="margin-top:10px;">
        <ul>
            <li><a href="../../index.php">Home</a></li>
            <li>
                <a href="#">Shop</a>
                <ul class="sub-menu">
                    <li><a href="../../shop_product.php" style="font-weight: bold;">ALL</a></li>
                    
                    <?php
                    $sql = "SELECT * from category";
                    $result = executeResult($sql);
                    foreach($result as $item){
                        echo '<li><a href="../../shop_product.php?id_category=' . $item['id'] . '">' . $item['name'] . '</a></li>';
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
                        echo '<li><a href="../../shop_collection.php?id_sanpham=' . $item['id'] . '">' . $item['name'] . '</a></li>';
                    }
                    ?>
                </ul>
            </li>
            <li><a href="../AboutUs/AboutUs.php">About us</a></li>
        </ul>
    </div>

    
    <div class="other">
        <div class="login"> 
            <?php
            // Sửa thông tin đăng nhập Admin và user
            if(isset($_COOKIE['tendangnhap'])) {
                $user = $_COOKIE['tendangnhap'];
                $role = isset($_COOKIE['role']) ? $_COOKIE['role'] : '';

                // Nếu là Admin
                if($role == 'admin') {
                    echo '<a style="color:black;" href="">' . $user . ' (Admin)</a>
                    <div class="logout">
                    <a href="../../admin/login.php"><i class="fas fa-user-edit"></i>Quản trị</a> <br>                            
                    <a href="../../logout.php"><i class="fas fa-sign-out-alt"></i>Đăng xuất</a>
                    </div>';
                }
                // Nếu là Staff
                elseif($role == 'staff') {
                    echo '<a style="color:black;" href="">' . $user . ' (Staff)</a>
                    <div class="logout">
                    <a href="../../admin/product/index.php"><i class="fas fa-user-edit"></i>Quản lý SP</a> <br>                            
                    <a href="../../logout.php"><i class="fas fa-sign-out-alt"></i>Đăng xuất</a>
                    </div>';
                }
                // Nếu là Customer
                else {
                    echo '<a style="color:black;" href="">' . $user . '</a>
                    <div class="logout">
                    <a href="../../change_password.php"><i class="fas fa-exchange-alt"></i>Đổi mật khẩu</a> <br>                           
                    <a href="../../logout.php"><i class="fas fa-sign-out-alt"></i>Đăng xuất</a>
                    </div>';
                }
            } 
            else {
                 echo '<a href="../../login.php">Đăng nhập</a>';
            }
            ?>
        </div>
        
        <?php 
        $role = isset($_COOKIE['role']) ? $_COOKIE['role'] : '';
        if ($role != 'admin' && $role != 'staff') { 
        ?>
            <li>
                <a href="../../cart.php" style="text-decoration:none;">
                    <i class="fas fa-shopping-bag"></i>
                </a> 
                <?php
                    $cart = [];
                    if (isset($_COOKIE['cart'])) {
                        $json = $_COOKIE['cart'];
                        $cart = json_decode($json, true);
                    }
                    $count = 0;
                    foreach ($cart as $item) {
                        $count += $item['num']; // đếm tổng số item
                    }
                ?>
                <span><?= $count ?></span>
            </li>
        <?php } ?>
    </div>
    
</header>

<style>
    /* ... (Giữ nguyên phần CSS style của bạn ở dưới đây) ... */
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
        z-index: 1;
        box-shadow: 2px 2px 2px rgba(241, 241, 241, 0.873);
        float: left;
    }
    header img{ width:150px; cursor:pointer; }
    .other{ display:flex; }
    .other >li{ padding:0 12px; }
    .other >li:first-child{ position:relative; }
    .login {
        padding: 0px;
        border: 1px solid rgb(196, 196, 196);
        border-radius: 3px;
        margin: 0 50px;
        position: relative;
    }
    .login:hover {
        box-shadow: 0px 0px 3px 0px grey;
        cursor: pointer;
    }
    .login a {
        padding: 15px;
        text-decoration: none;
        color: #676767;
        font-weight: 700;
    }
    .login:hover .logout{ display: block; }
    .login .logout{
        display: none;
        position: absolute;
        top: 2.3rem;
        left: 0px;
        z-index: 10;
        width: 150%;
        border: 1px solid grey;
        border-radius: 5px;
        padding: 10px 0;
        background-color: white;
    }
    .login .logout a{
        color: black;
        font-weight: 500;
        border-radius: 5px;
        margin: 10px 0;
    }
    .login .logout a:hover{ opacity: 0.8; }
    #menu { list-style:none; display: flex; }
    #menu ul{ list-style-type: none; background:#ffffff; text-align: center; }
    #menu ul li{
        color:#0f0f0f;
        display:inline-table;
        width:120px;
        height:30px;
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
        background:rgba(123, 123, 123, 0.262);
        color:#333;
    }
    #menu ul li >.sub-menu{
        display: none;
        position: absolute;
        background-color: #ffffff;
        z-index: 1;
        list-style: none;
    }
    #menu ul li:hover .sub-menu{ display:block; }
</style>