<?php
require_once('../database/dbhelper.php');
// Kiểm tra quyền (giữ nguyên logic cũ)
if (isset($_COOKIE['role']) && $_COOKIE['role'] == 'staff') {
    header('Location: ../dashboard.php'); 
    die();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Quản Lý Danh Mục</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

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
                <h3>Trang quản lý</h3>
                <div style="font-size: 14px; color: #c2c7d0;">Xin chào, <?php echo $_COOKIE['user'] ?? 'Admin'; ?></div>
            </div>

            <ul class="list-unstyled components">
                <li>
                    <a href="../index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                </li>
                <li class="active">
                    <a href="index.php"><i class="fas fa-folder"></i> Quản lý Danh mục</a>
                </li>
                <li>
                    <a href="../product/index.php"><i class="fas fa-box"></i> Quản lý Sản phẩm</a>
                </li>
                <li>
                    <a href="../dashboard.php"><i class="fas fa-shopping-cart"></i> Quản lý Đơn hàng</a>
                </li>
                <li>
                    <a href="../customer/index.php"><i class="fas fa-users"></i> Quản lý Khách hàng</a>
                </li>
                <li>
                    <a href="../staff/index.php"><i class="fas fa-user-tie"></i> Quản lý Nhân viên</a>
                </li>
                <li>
                     <a href="/Web/logout.php" style="border-top: 1px solid #4b545c; margin-top: 20px;"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                </li>
            </ul>
        </nav>

        <div id="content">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h2 class="text-left mb-4">Quản lý Danh mục</h2>
                </div>
                <div class="panel-body">
                    <a href="add.php">
                        <button class="btn btn-success mb-3"><i class="fas fa-plus"></i> Thêm Danh Mục</button>
                    </a>
                    <table class="table table-bordered table-hover bg-white shadow-sm" style="border-radius: 5px;">
                        <thead class="thead-light">
                            <tr>
                                <th width="70px">STT</th>
                                <th>Tên danh mục</th>
                                <th width="50px"></th>
                                <th width="50px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = 'select * from category';
                            $categoryList = executeResult($sql);
                            $index = 1;
                            foreach ($categoryList as $item) {
                                echo '<tr>
                                    <td>' . ($index++) . '</td>
                                    <td>' . $item['name'] . '</td>
                                    <td>
                                        <a href="add.php?id=' . $item['id'] . '">
                                            <button class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Sửa</button> 
                                        </a> 
                                    </td>
                                    <td>            
                                        <button class="btn btn-danger btn-sm" onclick="deleteCategory('.$item['id'].')"><i class="fas fa-trash"></i> Xoá</button>
                                    </td>
                                </tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        function deleteCategory(id) {
            var option = confirm('Bạn có chắc chắn muốn xoá danh mục này không?')
            if(!option) {
                return;
            }
            console.log(id)
            $.post('ajax.php', {
                'id': id,
                'action': 'delete'
            }, function(data) {
                location.reload()
            })
        }
    </script>
</body>
</html>