<?php
session_start();
require_once('../database/dbhelper.php');

// Kiểm tra quyền đăng nhập
if (isset($_COOKIE['role']) && $_COOKIE['role'] == 'staff') {
    // Staff vẫn được vào trang này để thêm sửa sản phẩm
}
if (!isset($_SESSION['submit'])) {
    header('Location: ../login.php');
}

$id = $title = $price = $thumbnail = $content = $id_category = "";
// Khởi tạo số lượng các size
$qty_s = 0;
$qty_m = 0;
$qty_l = 0;

// Các biến ảnh phụ
$thumbnail_1 = $thumbnail_2 = $thumbnail_3 = $thumbnail_4 = $thumbnail_5 = "";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = 'select * from product where id=' . $id;
    $product = executeSingleResult($sql);
    if ($product != null) {
        $title = $product['title'];
        $price = $product['price'];
        // Lấy số lượng size từ DB
        $qty_s = $product['qty_s'];
        $qty_m = $product['qty_m'];
        $qty_l = $product['qty_l'];
        
        $thumbnail = $product['thumbnail'];
        $thumbnail_1 = $product['thumbnail_1'];
        $thumbnail_2 = $product['thumbnail_2'];
        $thumbnail_3 = $product['thumbnail_3'];
        $thumbnail_4 = $product['thumbnail_4'];
        $thumbnail_5 = $product['thumbnail_5'];
        $content = $product['content'];
        $id_category = $product['id_category'];
    }
}

if (!empty($_POST)) {
    if (isset($_POST['title'])) {
        $title = $_POST['title'];
        $title = str_replace('"', '\\"', $title);
    }
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
    }
    if (isset($_POST['price'])) {
        $price = $_POST['price'];
    }
    
    // Lấy dữ liệu số lượng size
    if (isset($_POST['qty_s'])) $qty_s = $_POST['qty_s'];
    if (isset($_POST['qty_m'])) $qty_m = $_POST['qty_m'];
    if (isset($_POST['qty_l'])) $qty_l = $_POST['qty_l'];
    
    // Tính tổng số lượng từ 3 size
    $number = $qty_s + $qty_m + $qty_l;

    if (isset($_POST['content'])) {
        $content = $_POST['content'];
        $content = str_replace('"', '\\"', $content);
    }
    if (isset($_POST['id_category'])) {
        $id_category = $_POST['id_category'];
    }

    function processUpload($fieldName, $currentValue) {
        if (isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] == 0 && $_FILES[$fieldName]['size'] > 0) {
            $target_dir = "upload/"; // Lưu vào thư mục admin/product/upload/
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $fileName = basename($_FILES[$fieldName]["name"]);
            $target_file = $target_dir . $fileName;
            
            // Validate file (đơn giản hóa)
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowtypes = array('jpg', 'png', 'jpeg', 'gif');
            if (in_array($imageFileType, $allowtypes)) {
                if (move_uploaded_file($_FILES[$fieldName]["tmp_name"], $target_file)) {
                    return "admin/product/" . $target_file;
                }
            }
        }
        return $currentValue; // Trả về giá trị cũ nếu không upload mới
    }

    $thumbnail = processUpload("thumbnail", $thumbnail);
    $thumbnail_1 = processUpload("thumbnail_1", $thumbnail_1);
    $thumbnail_2 = processUpload("thumbnail_2", $thumbnail_2);
    $thumbnail_3 = processUpload("thumbnail_3", $thumbnail_3);
    $thumbnail_4 = processUpload("thumbnail_4", $thumbnail_4);
    $thumbnail_5 = processUpload("thumbnail_5", $thumbnail_5);

    $created_at = $updated_at = date('Y-m-d H:s:i');

    // Lưu vào DB
    if ($id == '') {
        // INSERT
        $sql = "INSERT INTO product(title, price, number, qty_s, qty_m, qty_l, thumbnail, thumbnail_1, thumbnail_2, thumbnail_3, thumbnail_4, thumbnail_5, content, id_category, created_at, updated_at) 
                VALUES ('$title', '$price', '$number', '$qty_s', '$qty_m', '$qty_l', '$thumbnail', '$thumbnail_1', '$thumbnail_2', '$thumbnail_3', '$thumbnail_4', '$thumbnail_5', '$content', '$id_category', '$created_at', '$updated_at')";
    } else {
        // UPDATE
        $sql = "UPDATE product SET 
                title='$title', price='$price', number='$number', 
                qty_s='$qty_s', qty_m='$qty_m', qty_l='$qty_l',
                thumbnail='$thumbnail', thumbnail_1='$thumbnail_1', thumbnail_2='$thumbnail_2', 
                thumbnail_3='$thumbnail_3', thumbnail_4='$thumbnail_4', thumbnail_5='$thumbnail_5', 
                content='$content', id_category='$id_category', updated_at='$updated_at' 
                WHERE id=" . $id;
    }

    execute($sql);
    header('Location: index.php');
    die();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Thêm/Sửa Sản Phẩm</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; }
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #sidebar { min-width: 250px; max-width: 250px; background: #343a40; color: #fff; transition: all 0.3s; min-height: 100vh; }
        #sidebar .sidebar-header { padding: 20px; background: #343a40; border-bottom: 1px solid #4b545c; }
        #sidebar ul.components { padding: 20px 0; border-bottom: 1px solid #47748b; }
        #sidebar ul p { color: #fff; padding: 10px; }
        #sidebar ul li a { padding: 15px 20px; font-size: 1.1em; display: block; color: #c2c7d0; text-decoration: none; }
        #sidebar ul li a:hover { color: #fff; background: #494e53; }
        #sidebar ul li.active > a { color: #fff; background: #007bff; }
        #sidebar ul li a i { margin-right: 10px; width: 20px; text-align: center; }
        #content { width: 100%; padding: 20px; min-height: 100vh; transition: all 0.3s; }
    </style>
</head>

<body>
    <div class="wrapper">
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3>Admin CP</h3>
                <div style="font-size: 14px; color: #c2c7d0;">Xin chào, <?php echo $_COOKIE['user'] ?? 'Admin'; ?></div>
            </div>

            <ul class="list-unstyled components">
                <li><a href="../index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="../category/index.php"><i class="fas fa-folder"></i> Quản lý Danh mục</a></li>
                <li class="active"><a href="index.php"><i class="fas fa-box"></i> Quản lý Sản phẩm</a></li>
                <li><a href="../dashboard.php"><i class="fas fa-shopping-cart"></i> Quản lý Đơn hàng</a></li>
                <li><a href="../customer/index.php"><i class="fas fa-users"></i> Quản lý Khách hàng</a></li>
                <li><a href="../staff/index.php"><i class="fas fa-user-tie"></i> Quản lý Nhân viên</a></li>
                <li><a href="../../authen/logout.php" style="border-top: 1px solid #4b545c; margin-top: 20px;"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
            </ul>
        </nav>

        <div id="content">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0 text-center">Thêm/Sửa Sản Phẩm</h4>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="name">Tên Sản Phẩm:</label>
                            <input type="text" name="id" value="<?= $id ?>" hidden="true">
                            <input required="true" type="text" class="form-control" id="title" name="title" value="<?= $title ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="id_category">Chọn Danh Mục:</label>
                            <select class="form-control" id="id_category" name="id_category">
                                <option>-- Chọn danh mục --</option>
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

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="price">Giá Sản Phẩm:</label>
                                    <input required="true" type="number" class="form-control" id="price" name="price" value="<?= $price ?>">
                                </div>
                            </div>
                        </div>

                        <label style="font-weight: bold;">Quản lý số lượng tồn kho (Theo size):</label>
                        <div class="row mb-3" style="background: #f1f1f1; padding: 15px; border-radius: 5px;">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="qty_s">Số lượng Size S:</label>
                                    <input type="number" class="form-control" id="qty_s" name="qty_s" value="<?= $qty_s ?>" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="qty_m">Số lượng Size M:</label>
                                    <input type="number" class="form-control" id="qty_m" name="qty_m" value="<?= $qty_m ?>" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="qty_l">Số lượng Size L:</label>
                                    <input type="number" class="form-control" id="qty_l" name="qty_l" value="<?= $qty_l ?>" min="0">
                                </div>
                            </div>
                            <div class="col-12">
                                <small class="text-muted">Hệ thống sẽ tự động cộng tổng số lượng.</small>
                            </div>
                        </div>

                        <h6 class="text-danger border-bottom pb-2">Hình ảnh sản phẩm (Thumbnail + 5 ảnh phụ)</h6>
                        
                        <div class="form-group">
                            <label for="thumbnail">Ảnh Đại Diện (Chính):</label>
                            <input type="file" class="form-control-file" id="thumbnail" name="thumbnail" accept="image/*" onchange="previewImage(this, 'img_thumbnail')">
                            <?php 
                                // Hiển thị ảnh nếu có
                                $showImg = (strpos($thumbnail, 'admin/') !== false) ? '/Web/'.$thumbnail : $thumbnail;
                            ?>
                            <img src="<?= $showImg ?>" style="max-width: 150px; margin-top: 10px;" id="img_thumbnail">
                        </div>

                        <div class="row">
                            <?php for($i=1; $i<=5; $i++): 
                                $varName = "thumbnail_" . $i;
                                $imgVal = $$varName;
                                $showImgSub = (strpos($imgVal, 'admin/') !== false) ? '/Web/'.$imgVal : $imgVal;
                            ?>
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label>Ảnh phụ <?= $i ?>:</label>
                                    <input type="file" class="form-control-file mb-2" name="thumbnail_<?= $i ?>" accept="image/*" onchange="previewImage(this, 'img_thumbnail_<?= $i ?>')">
                                    <img src="<?= $showImgSub ?>" style="max-height: 100px;" id="img_thumbnail_<?= $i ?>">
                                </div>
                            </div>
                            <?php endfor; ?>
                        </div>

                        <div class="form-group mt-3">
                            <label for="content">Nội dung mô tả:</label>
                            <textarea class="form-control" id="content" rows="5" name="content"><?= $content ?></textarea>
                        </div>

                        <div class="text-center mt-4 mb-4">
                            <button class="btn btn-success btn-lg" type="submit">
                                <i class="fas fa-save"></i> Lưu Sản Phẩm
                            </button>
                            <a href="index.php" class="btn btn-warning btn-lg">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script type="text/javascript">
        // Hàm preview ảnh tổng quát
        function previewImage(input, imgId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(imgId).src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        $(document).ready(function() {
            // Khởi tạo Summernote
            $('#content').summernote({
                height: 250,
                placeholder: 'Nhập mô tả sản phẩm chi tiết tại đây...'
            });
        });
    </script>
</body>
</html>