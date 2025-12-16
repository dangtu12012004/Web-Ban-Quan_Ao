<?php /*
    session_start();
	if(!isset($_SESSION['submit'])){
		header('Location: login.php');
	}
 */ ?>

<?php require_once('database/config.php');
require_once('database/dbhelper.php');?>
<?php 
include("Layout/header.php"); 
?>


<!--------------------BANNER ONE PIECE--------------------------- -->
<div id="banner1" style="background-repeat:no-repeat;">
        <div class="box-left" >
            <a href="shop_collection.php?id_sanpham=1"><button>Mua ngay </button></a>
        </div>
 </div>
<!--------------------NEW ARRIVALS--------------------------- -->
   <section class="main">
            <section class="recently" style="padding-bottom: 50px;">
                <div class="title">
                    <h1 >NEW ARRIVALS</h1>
                </div>
                <div class="product-recently">
                    <div class="row">
                        <?php
                            $sql = "
                                SELECT 
                                p.*,
                                COUNT(od.id) AS sold,
                                COALESCE(AVG(pr.rating), 5) AS rating
                                FROM product p
                                LEFT JOIN order_details od ON p.id = od.product_id
                                LEFT JOIN product_reviews pr ON p.id = pr.product_id
                                GROUP BY p.id
                                ORDER BY RAND()
                                LIMIT 4
                            ";


                            $productList = executeResult($sql);

                            foreach ($productList as $item) {
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
                                                    <img src="images/icon/icon-star.svg" alt="">
                                                    <span>' . number_format($item["rating"], 1) . '</span>
                                                </div>
                                                <div class="time">
                                                    <img src="images/icon/icon-clock.svg" alt="">
                                                    <span>' . $item["sold"] . ' sold</span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                ';
                            }
                        ?>

                    </div>
                </div>
            </section>   
<!--------------------BANNER SPRING OF THE Y--------------------------- -->
    <div id="banner2"><!--banner2 baneer rồng -->
        <div class="box-left" >
            <h2>
                <span>SPRING OF THE ¥ </span>
            </h2>
            <a href="shop_collection.php?id_sanpham=2"><button>Mua ngay </button><!--nút mua hàng --></a>
        </div>
    </div>



<!--------------------BANNER LILIWYUN--------------------------- -->
    <div id="banner3"><!--banner3 banner liliwyun  -->
        <div class="box-left" >
            <a href="shop_collection.php?id_sanpham=3"><button>Mua ngay </button><!--nút mua hàng --></a>
        </div>
    </div>



<!--------------------WHAT'S HOT--------------------------- -->
    <div id="new">
        <h2>WHAT'S HOT</h2>
        <ul id="list-new">
            <div class="item"><!--sản phẩm 1 -->
                <img src="/Web/images/new1.jpg"width="340" height="340"  alt="">                   
                <div class="name">DIRTYCOINS X LIL' WUYN: CÚ BẮT TAY </div>
                <div class="name">ĐẬM CHẤT VĂN HÓA ĐƯỜNG PHỐ</div>
            </div>
            <div class="box-left" >
                <a href="AboutUs/Dirtycoins/Dirtycoins.php"><button>Xem thêm </button><!--nút mua hàng --></a>
            </div>
            <div class="item"><!--sản phẩm 2 -->
                <img src="/Web/images/new2.jpg"width="340" height="340"  alt="">                   
                <div class="name" >7 TIPS PHỐI ĐỒ VỚI VARSITY JACKET </div>
                <div class="name" >THU HÚT MỌI ÁNH</div>
            </div>  
            <div class="box-left" >
                <a href="AboutUs/7 TIPS PHỐI ĐỒ VỚI VARSITY JACKET/7 TIPS PHỐI ĐỒ VỚI VARSITY JACKET.php"><button>Xem thêm </button></a>
            </div>   
            <div class="item"><!--sản phẩm 1 -->
                <img src="/Web/images/new3.jpg"width="340" height="340"  alt="">                   
                <div class="name">THÔNG TIN CHƯƠNG TRÌNH </div>
                <div class="name">THẺ THÀNH VIÊN DIRTYCOINS</div>
            </div>
            <div class="box-left" >
                <a href="AboutUs/THÔNG TIN CHƯƠNG TRÌNH/THÔNG TIN CHƯƠNG TRÌNH.php"><button>Xem thêm </button><!--nút mua hàng --></a>
            </div>       
        </ul>
    </div>


<!--------------------BANNER SALE--------------------------- -->
    <div id="banner4"><!--banner4 banner sale off  -->
        <div class="box-left" >
            <a href="dangky.php"><button>SIGN UP FOR FREE →</button><!--nút đăng ký --></a>
        </div>
    </div>
<style>     /* ------------------------Banner one piece------------------------------*/
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}
#banner1 {
    width: 100%;
    
    background-image :url("/Web/images/banner onepiece.png");
    
    height: 880px;/*chỉnh size banner*/
    margin-top:70px;
    display: flex;
    padding:0px 133px;
    position:relative;
}
#banner1 .box-left ,#banner .box-right {
    width: 50%;
}
#banner1 .box-left button {/*nút buttom mua ngay*/
    font-size:20px;
    width: 170px;
    height: 45px;
    margin-top:420px;
    margin-left:-18px;
    background:#1d1a1a;
    border:none;
    outline:none;
    color:#fff;
    font-weight: bold;
    border-radius: 200px;
    transition:0.4s;
}
#banner1 .box-left button:hover {/*màu sắc khi nhấp vô nút buttom mua ngay*/
    background:orange;
}



/* ------------------------NEW ARRIVALS------------------------------*/
  section.main {
  padding: 0 0;
  width: 100%;
  margin: 0px auto;
}
section.main a {
  text-decoration: none;
}
section.main section.recently {
  padding-bottom: 3rem;
  padding-left: 3rem;
  padding-right: 3rem;
}
section.main section.recently .link a {
  text-decoration: none;
  color: black;
  font-size: 20px;
}
section.main section.recently .title h1 {
  font-size: 35px;
  margin: 0px;
  padding: 30px;
  color: black;
  text-align:center;
}
section.main section.recently .product-recently {
  padding-top: 2rem;
}
section.main section.recently .product-recently .row {
  display: grid;
  grid-template-columns: auto auto auto auto;
  grid-column-gap: 30px;
  grid-row-gap: 30px;
}

section.main section.recently .product-recently .row .col img {
  width: 100%;
  border-radius: 10px;
}
section.main section.recently .product-recently .row .col img.thumbnail {
  border: 1px solid rgb(76, 78, 85);
  transition: 0.1s;
}
section.main section.recently .product-recently .row .col img.thumbnail:hover {
  box-shadow: 0px 0px 5px 0px grey;
}
section.main section.recently .product-recently .row .col .title p {
  font-size: 20px;
  font-weight: 600;
  padding: 0px;
  margin: 5px 0;
  color: black;
  font-family: "Encode Sans SC", sans-serif;
}
section.main section.recently .product-recently .row .col .price span {
  padding: 10px 0;
  color: #676767;
  font-size: 20px;
  font-weight: 600;
  color: rgba(207, 16, 16, 0.815);
  font-family: "Bebas Neue", cursive;
}
section.main section.recently .product-recently .row .col .dish span {
  padding: 10px 0;
  color: #676767;
}

section.main section.recently .product-recently .row .col .more {
  display: flex;
  color: #676767;
  padding: 5px 0;
  font-size: 18px;
}
section.main section.recently .product-recently .row .col .more .star {
  display: flex;
  align-items: center;
  justify-content: center;
}

section.main section.recently .product-recently .row .col .more .star img {
  width: 25px;
  height: 25px;
  
}
section.main section.recently .product-recently .row .col .more .time {
  display: flex;
  padding: 0 10px;

}
section.main section.recently .product-recently .row .col .more .time img {
  width: 24px;
  height: 24px;

}
#wp-products {/*căn nguyên lish new arrival và sản phẩm */
    padding-top:130px;/*cách banner trên*/
    padding-bottom: 78px;
    padding-left:0px;
    padding-right:0px;/*căn phải với web*/
}

#wp-products h2 {
    text-align: center;
    margin-bottom: 76px;/*căn trên so với chữ new arrival*/
    font-size:5x;/*size chữ New Arrival*/
    color:black;
    margin-left:40px;
}


#list-products {
    font-size:17px;/*size chữ sản phẩm*/
    display: flex;
    list-style: none;
    justify-content: space-around;
    align-items: center;
    flex-wrap: wrap;
}

#list-products .item {
    width: 100%px;
    height: 0px;
    background:#fafafa;
    border-radius: 0px;
    margin-bottom: 460px;
}


#list-products .item .name {
    text-align: center;
    color:rgb(10, 10, 10);
    font-weight: bold;
    margin-top:0px;
}

#list-products .item .price {
    text-align: center;
    color:#090909;
    font-weight: bold;
    margin-top:0px;
}

.list-page {
    width: 50%;
    margin:0px auto;
}

.list-page {
    display: flex;
    list-style: none;
    justify-content: center;
    align-items: center;
}


/* ------------------------Banner SPRING OF THE Y------------------------------*/
#banner2 {/* banner rồng*/
    width: 100%;
    background-image :url("/Web/images/banner rồng2.jpg");
    background-size:cover;
    height: 710px;/*chỉnh size banner*/
    margin-top:40px;
    display: flex;
    padding:0px 133px;
    position:relative;
}
#banner2 .box-left ,#banner .box-right {
    width: 50%;
}

#banner2  .box-left h2 {/* chỉnh chữ spring of the Y*/
    
    font-size:50px;
    margin-top:55px;
    margin-left:409px;
    width: 100%;
    padding:0px 30px;   
    font-family:Tahoma ;
    color:#AE611D
}

#banner2 .box-left button {
    font-size:20px;
    width: 170px;
    height: 45px;
    margin-top:460px;
    margin-left:565px;
    background:#1d1a1a;
    border:none;
    outline:none;
    color:#fff;
    font-weight: bold;
    border-radius: 200px;
    transition:0.4s;
}
#banner2 .box-left button:hover {
    background:orange;
}


/* ------------------------Banner LILIWUYN------------------------------*/
#banner3 {/* banner lilywuyn*/
    width: 100%;
    background-image :url("/Web/images/banner liliwuyn2.jpg");
    background-size:cover;
    height: 700px;/*chỉnh size banner*/
    margin-top:-40px;
    display: flex;
    padding:0px 133px;
    position:relative;
}
#banner3 .box-left ,#banner .box-right {
    width: 50%;
}

#banner3 .box-left button {
    font-size:20px;
    width: 170px;
    height: 45px;
    margin-top:435px;
    margin-left:565px;
    background:#1d1a1a;
    border:none;
    outline:none;
    color:#fff;
    font-weight: bold;
    border-radius: 200px;
    transition:0.4s;
}
#banner3 .box-left button:hover {
    background:orange;
}

/* ------------------------WHAT'S HOT------------------------------*/
#new {/*căn nguyên lish new arrival và sản phẩm */
    padding-top:50px;
    padding-bottom: 78px;
    padding-left:0px;
    padding-right:160px;
     
}

#new h2 {
    padding-left:175px;
    text-align: center;
    margin-bottom: 50px;
    font-size:5x;
    color:black;
    
}


#list-new {
    font-size:13px;
    display: flex;
    list-style: none;
    justify-content: space-around;
    align-items: center;
    flex-wrap: wrap;
}

#list-new .item {
    width: 100%px;
    height: 0px;
    background:#fafafa;
    border-radius: 0px;
    margin-bottom: 460px;
}


#list-new .item .name {
    text-align: center;
    color:rgb(10, 10, 10);
    font-weight: bold;
    margin-top:20px;
}


.list-page {
    width: 50%;
    margin:0px auto;
}

.list-page {
    display: flex;
    list-style: none;
    justify-content: center;
    align-items: center;
}
#list-new .box-left{
    text-align: center;
    margin-top:470px;
    margin-left:-458px;
    
}
#list-new .box-left button:hover {
    background:orange;
}
#list-new .box-left button {
    font-size:13px;
    width: 90px;
    height: 35px;
    background:#1d1a1a;
    border:none;
    outline:none;
    color:#fff;
    font-weight: bold;
    border-radius: 200px;
    transition:0.4s;
}
/* ------------------------Banner 4------------------------------*/
#banner4 {/* banner sale off*/
    width: 100%;
    background-image :url("/Web/images/banner saleoff2.jpg");
    background-size:cover;
    height: 113px;
    margin-top:-20px;
    margin-left:0px;
    display: flex;
    padding:0px 133px;
    position:relative;
}
#banner4 .box-left ,#banner .box-right {
    width: 50%;
}

#banner4 .box-left button {
    font-size:15px;
    width: 190px;
    height: 55px;
    margin-top:27px;
    margin-left:670px;
    background:#1d1a1a;
    border:none;
    outline:none;
    color:#fff;
    font-weight: bold;
    border-radius: 200px;
    transition:0.4s;
}
#banner4 .box-left button:hover {
    background:orange;
}
  @media screen and  (max-width: 870px)  and (min-width:300px){
    body {
   width: 1600px;
    }

}
</style>


<?php require_once('Layout/footer.php'); ?>
