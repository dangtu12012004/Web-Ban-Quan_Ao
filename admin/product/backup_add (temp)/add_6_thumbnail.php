<?php
header("content-type:text/html; charset=UTF-8");
?>
<?php
require_once('../database/dbhelper.php');
$id = $title = $price = $number = $thumbnail= $thumbnail_1= $thumbnail_2 = $thumbnail_3 = $thumbnail_4 = $thumbnail_5 = $content = $id_category = "";
if (!empty($_POST['title'])) {
    if (isset($_POST['title'])) {
        $title = $_POST['title'];
        $title = str_replace('"', '\\"', $title);
    }
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $id = str_replace('"', '\\"', $id);
    }
    if (isset($_POST['price'])) {
        $price = $_POST['price'];
        $price = str_replace('"', '\\"', $price);
    }
    if (isset($_POST['number'])) {
        $number = $_POST['number'];
        $number = str_replace('"', '\\"', $number);
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        // Dữ liệu gửi lên server không bằng phương thức post
        echo "Phải Post dữ liệu";
        die;
    }

    // Kiểm tra có dữ liệu thumbnail trong $_FILES không
    // Nếu không có thì dừng
    if (!isset($_FILES["thumbnail"])) {
        echo "Dữ liệu không đúng cấu trúc";
        die;
    }
	if (!isset($_FILES["thumbnail_1"])) {
        echo "Dữ liệu không đúng cấu trúc";
        die;
    }
	if (!isset($_FILES["thumbnail_2"])) {
        echo "Dữ liệu không đúng cấu trúc";
        die;
    }
	if (!isset($_FILES["thumbnail_3"])) {
        echo "Dữ liệu không đúng cấu trúc";
        die;
    }
	if (!isset($_FILES["thumbnail_4"])) {
        echo "Dữ liệu không đúng cấu trúc";
        die;
    }
	if (!isset($_FILES["thumbnail_5"])) {
        echo "Dữ liệu không đúng cấu trúc";
        die;
    }
    // Kiểm tra dữ liệu có bị lỗi không
    
    // Đã có dữ liệu upload, thực hiện xử lý file upload

    //Thư mục bạn sẽ lưu file upload
    $target_dir    = "uploads/";
    //Vị trí file lưu tạm trong server (file sẽ lưu trong uploads, với tên giống tên ban đầu)
    $target_file     = $target_dir . basename($_FILES["thumbnail"]["name"]);
	$target_file_1   = $target_dir . basename($_FILES["thumbnail_1"]["name"]);
	$target_file_2   = $target_dir . basename($_FILES["thumbnail_2"]["name"]);
	$target_file_3   = $target_dir . basename($_FILES["thumbnail_3"]["name"]);
	$target_file_4   = $target_dir . basename($_FILES["thumbnail_4"]["name"]);
	$target_file_5   = $target_dir . basename($_FILES["thumbnail_5"]["name"]);
    $allowUpload   = true;

    //Lấy phần mở rộng của file (jpg, png, ...)
    $imageFileType   = pathinfo($target_file, PATHINFO_EXTENSION);
	$imageFileType_1 = pathinfo($target_file_1, PATHINFO_EXTENSION);
	$imageFileType_2 = pathinfo($target_file_2, PATHINFO_EXTENSION);
	$imageFileType_3 = pathinfo($target_file_3, PATHINFO_EXTENSION);
	$imageFileType_4 = pathinfo($target_file_4, PATHINFO_EXTENSION);
	$imageFileType_5 = pathinfo($target_file_5, PATHINFO_EXTENSION);
    // Cỡ lớn nhất được upload (bytes)
    $maxfilesize   = 800000;

    ////Những loại file được phép upload
    $allowtypes    = array('jpg', 'png', 'jpeg', 'gif');


    if (isset($_POST["submit"])) {
        //Kiểm tra xem có phải là ảnh bằng hàm getimagesize
        $check = getimagesize($_FILES["thumbnail"]["tmp_name"]);
		$check_1 = getimagesize($_FILES["thumbnail_1"]["tmp_name"]);
		$check_2 = getimagesize($_FILES["thumbnail_2"]["tmp_name"]);
		$check_3 = getimagesize($_FILES["thumbnail_3"]["tmp_name"]);
		$check_4 = getimagesize($_FILES["thumbnail_4"]["tmp_name"]);
		$check_5 = getimagesize($_FILES["thumbnail_5"]["tmp_name"]);
        
		
		$allowUpload = true;
		$files = array($check, $check_1, $check_2, $check_3, $check_4, $check_5); // mảng chứa các file cần kiểm tra
		for ($i = 0; $i < count($files); $i++) { // vòng lặp qua các file
			$type = exif_imagetype($files[$i]); // lấy loại file ảnh
			if ($type !== false) { // nếu file là ảnh
				echo "Đây là file ảnh - " . image_type_to_mime_type($type) . "."; // in ra loại file ảnh
			} else { // nếu file không phải là ảnh hoặc không tồn tại
				echo "Không phải file ảnh.";
				$allowUpload = false; // gán biến cho phép upload bằng false
				break; // thoát khỏi vòng lặp
			}
		}
			}
    // Kiểm tra kích thước file upload cho vượt quá giới hạn cho phép
    if ($_FILES["thumbnail"]["size"] > $maxfilesize) {
        echo "Không được upload ảnh lớn hơn $maxfilesize (bytes).";
        $allowUpload = false;
    }
	if ($_FILES["thumbnail_1"]["size"] > $maxfilesize) {
        echo "Không được upload ảnh lớn hơn $maxfilesize (bytes).";
        $allowUpload = false;
    }
	if ($_FILES["thumbnail_2"]["size"] > $maxfilesize) {
        echo "Không được upload ảnh lớn hơn $maxfilesize (bytes).";
        $allowUpload = false;
    }
	if ($_FILES["thumbnail_3"]["size"] > $maxfilesize) {
        echo "Không được upload ảnh lớn hơn $maxfilesize (bytes).";
        $allowUpload = false;
    }
	if ($_FILES["thumbnail_4"]["size"] > $maxfilesize) {
        echo "Không được upload ảnh lớn hơn $maxfilesize (bytes).";
        $allowUpload = false;
    }
	if ($_FILES["thumbnail_5"]["size"] > $maxfilesize) {
        echo "Không được upload ảnh lớn hơn $maxfilesize (bytes).";
        $allowUpload = false;
    }
    // Kiểm tra kiểu file
    if (!in_array($imageFileType, $allowtypes)) {
        echo "Chỉ được upload các định dạng JPG, PNG, JPEG, GIF";
        $allowUpload = false;
    }
	if (!in_array($imageFileType_1, $allowtypes)) {
        echo "Chỉ được upload các định dạng JPG, PNG, JPEG, GIF";
        $allowUpload = false;
    }
	if (!in_array($imageFileType_2, $allowtypes)) {
        echo "Chỉ được upload các định dạng JPG, PNG, JPEG, GIF";
        $allowUpload = false;
    }
	if (!in_array($imageFileType_3, $allowtypes)) {
        echo "Chỉ được upload các định dạng JPG, PNG, JPEG, GIF";
        $allowUpload = false;
    }
	if (!in_array($imageFileType_4, $allowtypes)) {
        echo "Chỉ được upload các định dạng JPG, PNG, JPEG, GIF";
        $allowUpload = false;
    }
	if (!in_array($imageFileType_5, $allowtypes)) {
        echo "Chỉ được upload các định dạng JPG, PNG, JPEG, GIF";
        $allowUpload = false;
    }
	//
    if ($allowUpload) {
        // Xử lý di chuyển file tạm ra thư mục cần lưu trữ, dùng hàm move_uploaded_file
        if (move_uploaded_file($_FILES["thumbnail"]["tmp_name"], $target_file)) {
        }
		elseif (move_uploaded_file($_FILES["thumbnail_1"]["tmp_name"], $target_file_1)) {
        }
		elseif (move_uploaded_file($_FILES["thumbnail_2"]["tmp_name"], $target_file_2)) {
        }
		elseif (move_uploaded_file($_FILES["thumbnail_3"]["tmp_name"], $target_file_3)) {
        }
		elseif (move_uploaded_file($_FILES["thumbnail_4"]["tmp_name"], $target_file_4)) {
        }
		elseif (move_uploaded_file($_FILES["thumbnail_5"]["tmp_name"], $target_file_5)) {
        }		else {
						echo "Có lỗi xảy ra khi upload file.";
        }
    } else {
        echo "Không upload được file, có thể do file lớn, kiểu file không đúng ...";
    }

    if (isset($_POST['content'])) {
        $content = $_POST['content'];
        $content = str_replace('"', '\\"', $content);
    }
    if (isset($_POST['id_category'])) {
        $id_category = $_POST['id_category'];
        $id_category = str_replace('"', '\\"', $id_category);
    }
	
    if (!empty($title)) {
        $created_at = $updated_at = date('Y-m-d H:s:i');
		
        // Lưu vào DB
        if ($id == '') {
            // Thêm danh mục
            $sql = 'insert into product(title, price, number, thumbnail, thumbnail_1, thumbnail_2, thumbnail_3, thumbnail_4, thumbnail_5, content, id_category, created_at, updated_at) 
            values ("' . $title . '","' . $price . '","' . $number . '","' . $target_file . '","' . $target_file_1 . '","' . $target_file_2 . '","' . $target_file_3 . '","' . $target_file_4 . '","' . $target_file_5 . '","' . $content . '","' . $id_category . '","' . $created_at . '","' . $updated_at . '")';
        } else {
            // Sửa danh mục
            $sql = 'update product set title="' . $title . '",price="' . $price . '",number="' . $number . '",thumbnail="' . $target_file . '",thumbnail_1="' . $target_file_1 . '",thumbnail_2="' . $target_file_2 . '",thumbnail_3="' . $target_file_3 . '",thumbnail_4="' . $target_file_4 . '",thumbnail_5="' . $target_file_5 . '",content="' . $content . '",id_category="' . $id_category . '", updated_at="' . $updated_at . '" where id=' . $id;
        }
        execute($sql);
        header('Location: index.php');
        die();
    }
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = 'select * from product where id=' . $id;
    $product = executeSingleResult($sql);
    if ($product != null) {
        $title = $product['title'];
        $price = $product['price'];
        $number = $product['number'];
        $thumbnail = $product['thumbnail'];
		$thumbnail_1 = $product['thumbnail_1'];
		$thumbnail_2 = $product['thumbnail_2'];
		$thumbnail_3 = $product['thumbnail_3'];
		$thumbnail_4 = $product['thumbnail_4'];
		$thumbnail_5 = $product['thumbnail_5'];
        $content = $product['content'];
        $id_category = $product['id_category'];
        $created_at = $product['created_at'];
        $updated_at = $product['updated_at'];
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Thêm Sản Phẩm</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <!-- Popper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

    <!-- summernote -->
    <!-- include summernote css/js -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
</head>

<body>
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link" href="../index.php">Thống kê</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="index.php">Quản lý danh mục</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="../product/">Quản lý sản phẩm</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Quản lý giỏ hàng</a>
        </li>
    </ul>
    <div class="container">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2 class="text-center">Thêm/Sửa Sản Phẩm</h2>
            </div>
            <div class="panel-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Tên Sản Phẩm:</label>
                        <input type="text" id="id" name="id" value="<?= $id ?>" hidden="true">
                        <input required="true" type="text" class="form-control" id="title" name="title" value="<?= $title ?>">
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlSelect1">Chọn Danh Mục</label>
                        <select class="form-control" id="id_category" name="id_category">
                            <option>Chọn danh mục</option>
                            <?php
                            $sql = 'select * from category';
                            $categoryList = executeResult($sql);
                            foreach ($categoryList as $item) {
                                if ($item['id'] == $id_category) {
                                    echo '<option selected value="' . $item['id'] . '">' . $item['name'] . '</option>';
                                } else {
                                    echo '<option value="' . $item['id'] . '">' . $item['name'] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name">Giá Sản Phẩm:</label>
                        <input required="true" type="text" class="form-control" id="price" name="price" value="<?= $price ?>">
                    </div>
                    <div class="form-group">
                        <label for="name">Số Lượng Sản Phẩm:</label>
                        <input required="true" type="number" class="form-control" id="number" name="number" value="<?= $number ?>">
                    </div>
                    <h6>Lưu ý: Thêm/sửa đầy đủ 6 thumbnail (Bỏ ảnh cần thêm vào trong thư mục admin/product/uploads)</h6>
					<div class="form-group">
						<label for="name">Ảnh Đại Diện (Thumbnail):</label>
						<input type="file" class="form-control-file" id="thumbnail" name="thumbnail" accept="image/*" onchange="previewImage(this)">
						<img src="<?= $thumbnail ?>" style="max-width: 200px" id="img_thumbnail">
						<br>
						<label for="name">Ảnh 1 (Thumbnail_1):</label>
						<input type="file" class="form-control-file" id="thumbnail_1" name="thumbnail_1" accept="image/*" onchange="previewImage(this)">
						<img src="<?= $thumbnail_1 ?>" style="max-width: 200px" id="img_thumbnail_1">
						<br>
						<label for="name">Ảnh 2 (Thumbnail_2):</label>
						<input type="file" class="form-control-file" id="thumbnail_2" name="thumbnail_2" accept="image/*" onchange="previewImage(this)">
						<img src="<?= $thumbnail_2 ?>" style="max-width: 200px" id="img_thumbnail_2">
						<br>
						<label for="name">Ảnh 3 (Thumbnail_3):</label>
						<input type="file" class="form-control-file" id="thumbnail_3" name="thumbnail_3" accept="image/*" onchange="previewImage(this)">
						<img src="<?= $thumbnail_3 ?>" style="max-width: 200px" id="img_thumbnail_3">
						<br>
						<label for="name">Ảnh 4 (Thumbnail_4):</label>
						<input type="file" class="form-control-file" id="thumbnail_4" name="thumbnail_4" accept="image/*" onchange="previewImage(this)">
						<img src="<?= $thumbnail_4 ?>" style="max-width: 200px" id="img_thumbnail_4">
						<br>
						<label for="name">Ảnh 5 (Thumbnail_5):</label>
						<input type="file" class="form-control-file" id="thumbnail_5" name="thumbnail_5" accept="image/*" onchange="previewImage(this)">
						<img src="<?= $thumbnail_5 ?>" style="max-width: 200px" id="img_thumbnail_5">
						</div>
						
					
                    <div class="form-group">
                        <label for="exampleFormControlTextarea1">Nội dung</label>
                        <textarea class="form-control" id="content" rows="3" name="content"><?= $content ?></textarea>
                    </div>
                    <button class="btn btn-success" onclick="addProduct()">Lưu</button>
                    <?php
                    $previous = "javascript:history.go(-1)";
                    if (isset($_SERVER['HTTP_REFERER'])) {
                        $previous = $_SERVER['HTTP_REFERER'];
                    }
                    ?>
                    <a href="<?= $previous ?>" class="btn btn-warning">Back</a>
                </form>
            </div>
        </div>
    </div>
	
    <script type="text/javascript">
        function previewImage(input) {
    // Kiểm tra nếu có file được chọn
    if (input.files && input.files[0]) {
        // Tạo một đối tượng FileReader để đọc file
        var reader = new FileReader();

        // Định nghĩa hàm onload cho đối tượng FileReader
        reader.onload = function(e) {
            // Lấy id của thẻ <input> hiện tại
            var input_id = input.id;

            // Lấy id của thẻ <img> tương ứng
            var img_id = "img_" + input_id;

            // Hiển thị ảnh lên thẻ <img> tương ứng
            document.getElementById(img_id).src = e.target.result;
        };

        // Đọc file ảnh dưới dạng URL
        reader.readAsDataURL(input.files[0]);
    }
}
    </script>
	<script type="text/javascript">
    $(function() {
        //doi website load noi dung => xu ly phan js
        $('#content').summernote({
            height: 200
        });
    })

    // Hàm để gửi dữ liệu từ form lên server
    function addProduct() {
        // Tạo một đối tượng FormData để lưu trữ dữ liệu từ form
        var formData = new FormData(document.getElementById("product-form"));

        // Gửi dữ liệu bằng phương thức POST bằng ajax
        $.ajax({
            url: "add_product.php", // Đường dẫn của file xử lý dữ liệu
            type: "POST", // Phương thức gửi dữ liệu
            data: formData, // Dữ liệu từ form
            contentType: false, // Không thiết lập kiểu nội dung
            processData: false, // Không xử lý dữ liệu
            success: function(response) { // Hàm xử lý khi gửi thành công
                // Hiển thị thông báo thành công
                alert("Bạn đã thêm sản phẩm thành công");
            },
            error: function(xhr, status, error) { // Hàm xử lý khi gửi thất bại
                // Hiển thị thông báo lỗi
                alert("Có lỗi xảy ra khi thêm sản phẩm");
            }
        });
    }
</script>
</body>

</html>