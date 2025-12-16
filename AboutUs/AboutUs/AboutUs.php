<?php
// Quay lùi 2 cấp thư mục để tìm file config và dbhelper
require_once('../../database/config.php');

// Dùng đường dẫn tương đối để tìm dbhelper từ thư mục con
require_once('../../database/dbhelper.php'); 
require_once('../../utils/utility.php');

include("../Layout/header_about.php");
?>

<body>
    <div class="container">
        <div class="gallery-display-area">
            <div class="gallery-wrap">
                <div class="image">
                    <img src="/Web/AboutUs/AboutUs/ảnh1.png" />
                </div>
                <div class="image">
                    <img src="/Web/AboutUs/AboutUs/ảnh2.png" />
                </div>
                <div class="image">
                    <img src="/Web/AboutUs/AboutUs/ảnh3.png" />
                </div>  
                <div class="image">
                    <img src="/Web/AboutUs/AboutUs/ảnh4.png" />
                </div>    
                <div class="image">
                    <img src="/Web/AboutUs/AboutUs/ảnh5.png" />
                </div>            
            </div>
        </div>
    </div>
    <h2 style="   text-align: center;
  padding:80px;
  font-size:25px;
  margin-top:-200px">About us</h2>
    <h5 style="    font-size:16.6px;
    color:rgb(0, 0, 0);
    text-align:center;
    padding-left:395px;
    padding-right:395px;
    letter-spacing:0.5px;
    line-height:40px;
    font-weight:500;
    padding-bottom:70px;">
        Câu chuyện của DirtyCoins được bắt đầu từ 2017 tại Sài Gòn, Việt Nam; xuất phát từ ý tưởng về một thương hiệu văn hóa đường phố từ Khoa Sen và những người bạn Gen Z đầy sáng tạo. Sau những thành công và kinh nghiệm gói ghém từ thương hiệu tiền thân là The Yars Shop, DirtyCoins đã ra đời.</br></br>
        
        Không quá ồn ào hay phô trương, cái tên ‘DirtyCoins’ tượng trưng cho những giá trị nguyên bản nhất của cuộc sống: đó là hiện thực gai góc của những ‘đồng tiền xương máu’, của giá trị lao động đầy mồ hôi, bụi bẩn và nước mắt. DirtyCoins trở thành một thương hiệu của tinh thần thời trang mạnh mẽ, táo bạo tuy nhiên vẫn gần gũi và dễ tiếp cận rộng rãi. Tuy nhiên, không dừng lại ở đó, DirtyCoins muốn vượt qua giới hạn của một thương hiệu thời trang đơn thuần và trở thành một biểu tượng về văn hóa và phong cách sống của những con người trẻ tuổi.</br></br>
        
        Những sản phẩm của DirtyCoins mang đậm dấu ấn kết hợp giữa văn hóa phương Tây và Phương Đông, xác lập một ngôn ngữ sáng tạo rất riêng, đầy mạo hiểm và mới mẻ. Chất liệu cảm hứng được DirtyCoins chắt lọc từ sự đa sắc, đa diện của dòng chảy cuộc sống hàng ngày; pha trộn cùng cảm hứng nghệ thuật đương đại để tạo nên một tiểu vùng văn hóa rất riêng của DirtyCoins.</br></br>
        
        Không ngại thử thách, luôn luôn mạo hiểm và không ngừng đuổi bắt những giới hạn, đó là DNA của DirtyCoins.</h5>
    </body>
</html>

<?php include("../Layout/footer_about.php") ?>
<style>
    *{
    margin: 0;
    padding: 0;
}
i{/*  chỉnh icon ngôi sao */
    font-size:16px;
    text-align: center;
    padding-right: 10px;
}

h2{/*  chỉnh ô chứa chữ H2 */
text-align: center;
font-size:23px; 
padding-left:325px;/*  chỉnh khoảng cách 2 bên lề để song song với ảnh */
padding-right:343px;
padding-bottom:70px;
padding-top:100px;
}

h5{/*  chỉnh ô chứa chữ H5 */
    text-align: left;
    font-size:16px; 
    font-weight: 50;
    padding-left:340px;/*  chỉnh khoảng cách 2 bên lề để song song với ảnh */
    padding-right:343px;
    padding-top:10px;
    padding-bottom:18px;


    }

h6{/*  chỉnh ô chứa chữ H6 */
    text-align: left;
    font-size:17.5px; 
    font-weight: 600;
    text-decoration: underline;
    padding-left:355px;/*  chỉnh khoảng cách 2 bên lề để song song với ảnh */
    padding-right:343px;
    padding-bottom:10px;
    margin-top:-10px;/*  chỉnh khoảng cách so với chữ bên trên */
}
.image{/*  chỉnh ảnh trong mục body */
    align-items: center;
    text-align: center;
}



/*-----------------BÀI VIẾT LIÊN QUAN--------------------------*/


hr{/*  chỉnh thanh kẻ giữa bài viết liên quan với ảnh trên */
    margin-top:70px;/*  chỉnh khoảng cách so với chữ bên trên */padding-left:325px;/*  chỉnh khoảng cách 2 bên lề để song song với ảnh */
    margin-left:313px;
}
h1{/*  chỉnh ô chứa chữ H1 */
    text-align: left;
    font-size:16px; 
    font-weight: 550;
    padding-left:325px;/*  chỉnh khoảng cách 2 bên lề để song song với ảnh */
    padding-right:343px;
    padding-bottom:18px;
    margin-top:15px;/*  chỉnh khoảng cách so với chữ bên trên */
}
#list-new {/*  chỉnh ảnh bài viết liên quan */
    font-size:10px;/*size chữ sản phẩm*/
    display: flex;
    list-style: none;
    justify-content: space-around;
    padding-left:190px;/*  chỉnh khoảng cách 2 bên lề để song song với ảnh */
    padding-right:317px;
    margin-top:40px;

}

#list-new .item .name {/*  chỉnh chữ bài viết liên quan */
    text-align: center;
    color:rgb(10, 10, 10);
    font-weight: bold;
    margin-top:20px;/*chỉnh khoảng cách từ tên so với sản phẩm*/
}


#list-new .box-left{
    text-align: center;
    margin-top:435px;/*chỉnh lên xuống nút xem thêm */
    margin-left:-490px;/*chỉnh trái phải nút xem thêm*/
    
}
#list-new .box-left button:hover {/*màu sắc khi nhấp vô nút buttom mua ngay*/
    background:orange;
}
#list-new .box-left button {/*nút buttom mua ngay*/
    font-size:12px;/*chỉnh size chữ*/
    width: 80px;/*chỉnh size dài bóng đen*/
    height: 30px;/*chỉnh size rộng bóng đen*/
    background:#1d1a1a;
    border:none;
    outline:none;
    color:#fff;
    font-weight: bold;
    border-radius: 200px;
    transition:0.4s;/*chỉnh tốc độ chuyển màu*/
}
