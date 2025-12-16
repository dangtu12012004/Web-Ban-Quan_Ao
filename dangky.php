
<?php
    session_start();
    require_once('config.php');
    require_once('database/dbhelper.php');

    if(isset($_POST['dangky'])) {
        $tenkhachhang = $_POST['hovaten'];
        $tendangnhap  = $_POST['tendangnhap'];
        $email        = $_POST['email'];
        $diachi       = $_POST['diachi'];
        //Mã hóa MD5 để khớp với login.php
        $matkhau      = md5($_POST['matkhau']); 
        $dienthoai    = $_POST['dienthoai'];

        if($tenkhachhang!="" && $tendangnhap!="" && $email!="" && $diachi!="" && $dienthoai!="" && $_POST['matkhau']!=""){
            
            // Thực hiện Insert
            $sql_dangky = mysqli_query($mysqli,"INSERT INTO tbl_dangky(tenkhachhang,tendangnhap,email,diachi,matkhau,dienthoai) VALUE('".$tenkhachhang."','".$tendangnhap."','".$email."','".$diachi."','".$matkhau."','".$dienthoai."')");
            
            if($sql_dangky){
                echo '<script>alert("Đăng ký thành công.");
                window.location.href="login.php"; // Chuyển hướng về trang đăng nhập thay vì trang chủ
                </script>';
            } else {
                echo '<script>alert("Đăng ký thất bại. Có thể tên đăng nhập đã tồn tại.");</script>';
            }
        } else {
            echo '<script>alert("Vui lòng nhập đầy đủ thông tin.");</script>';
        }
    }
?>
<?php 
include("Layout/header.php"); 
?>

<body >
  <div class="container">
    <div class="title">ĐĂNG KÝ TÀI KHOẢN</div>
    <div class="content">
      <form action="#" method="POST">
        <div class="user-details">
          <div class="input-box">
            <span class="details">Họ và tên</span>
            <input type="text" name="hovaten"  placeholder="Enter your name" required>
          </div>
          <div class="input-box">
            <span class="details">Tên đăng nhập</span>
            <input type="text" name="tendangnhap" placeholder="Enter your username" required>
          </div>
          <div class="input-box">
            <span class="details">Email</span>
            <input type="text" name="email" placeholder="Enter your email" required>
          </div>
          <div class="input-box">
            <span class="details">Số điện thoại</span>
            <input type="text" name="dienthoai" placeholder="Enter your number" required>
          </div>
          <div class="input-box">
            <span class="details">Mật khẩu</span>
            <input type="password" name="matkhau" placeholder="Enter your password" required>
          </div>
          <div class="input-box">
            <span class="details">Địa chỉ</span>
            <input type="text" name="diachi" placeholder="Enter your Address" required>
          </div>
        </div>
        
        <div class="button">
          <input type="submit" name="dangky" value="Đăng ký">
        </div>
      </form>
    </div>
  </div>



<style>
*{
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Poppins',sans-serif;
}
body{
  height: 100vh;
  justify-content: center;
  align-items: center;
  background: white;
}
.container{
  
  margin-bottom: 150px;
  max-width: 700px;
  width: 100%;
  background-color: #fff;
  padding: 25px 30px;
  border-radius: 5px;
  box-shadow: 0 5px 10px rgba(0,0,0,0.15);
  margin-top:150px;
}
.container .title{
  font-size: 25px;
  font-weight: 500;
  position: relative;
}
.container .title::before{
  content: "";
  position: absolute;
  left: 0;
  bottom: 0;
  height: 3px;
  width: 30px;
  border-radius: 5px;
  background: linear-gradient(135deg, black,red);
}
.content form .user-details{
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  margin: 20px 0 12px 0;
}
form .user-details .input-box{
  display: block;
  margin: 20px auto;
  border: 0;
  border-radius: 5px;
  padding: 1px 10px;
  width: 320px;
  outline: none;
  color: #000000;
}
form .input-box span.details{
  display: block;
  font-weight: 500;
  margin-bottom: 5px;
}
.user-details .input-box input{
  height: 45px;
  width: 100%;
  outline: none;
  font-size: 16px;
  border-radius: 5px;
  padding-left: 15px;
  border: 1px solid #ccc;
  border-bottom-width: 2px;
  transition: all 0.3s ease;
}
.user-details .input-box input:focus,
.user-details .input-box input:valid{
  border-color: #9b59b6;
}
 form .gender-details .gender-title{
  font-size: 20px;
  font-weight: 500;
 }
 form .category{
   display: flex;
   width: 80%;
   margin: 14px 0 ;
   justify-content: space-between;
 }
 form .category label{
   display: flex;
   align-items: center;
   cursor: pointer;
 }
 form .category label .dot{
  height: 18px;
  width: 18px;
  border-radius: 50%;
  margin-right: 10px;
  background: #d9d9d9;
  border: 5px solid transparent;
  transition: all 0.3s ease;
}
 #dot-1:checked ~ .category label .one,
 #dot-2:checked ~ .category label .two,
 #dot-3:checked ~ .category label .three{
   background: #9b59b6;
   border-color: #d9d9d9;
 }
 form input[type="radio"]{
   display: none;
 }
 form .button{
   height: 45px;
   margin: 35px 0
 }
 form .button input{
   height: 100%;
   width: 100%;
   border-radius: 5px;
   border: none;
   color:white;
   font-size: 18px;
   font-weight: 500;
   letter-spacing: 1px;
   cursor: pointer;
   transition: all 0.3s ease;
   background: black;
 }
 form .button input:hover{background: orange;}

</style></body>
</html>
<?php require_once('Layout/footer.php'); ?>
