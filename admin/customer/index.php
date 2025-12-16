<?php
$title = 'Quản lý Khách hàng';
$baseUrl = '../';
require_once('../../database/dbhelper.php');
require_once('../../utils/utility.php');

// Kiểm tra quyền Admin
$isAdmin = false;
if (isset($_COOKIE['role']) && $_COOKIE['role'] == 'admin') {
    $isAdmin = true;
}

// Lấy danh sách khách hàng từ bảng tbl_dangky
$sql = "SELECT * FROM tbl_dangky";
$customerList = executeResult($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Quản lý Khách hàng</title>
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
                <li><a href="../index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="../category/index.php"><i class="fas fa-folder"></i> Quản lý Danh mục</a></li>
                <li><a href="../product/index.php"><i class="fas fa-box"></i> Quản lý Sản phẩm</a></li>
                <li><a href="../dashboard.php"><i class="fas fa-shopping-cart"></i> Quản lý Đơn hàng</a></li>
                <li class="active"><a href="index.php"><i class="fas fa-users"></i> Quản lý Khách hàng</a></li>
                <li><a href="../staff/index.php"><i class="fas fa-user-tie"></i> Quản lý Nhân viên</a></li>
                <li><a href="/Web/logout.php" style="border-top: 1px solid #4b545c; margin-top: 20px;"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
            </ul>
        </nav>

        <div id="content">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h2 class="text-left mb-4">Quản lý Khách hàng</h2>
                </div>
                <div class="panel-body">
                    <?php if ($isAdmin): ?>
                        <button class="btn btn-success mb-3" onclick="openModal()"><i class="fas fa-plus"></i> Thêm Khách hàng</button>
                    <?php endif; ?>

                    <table class="table table-bordered table-hover bg-white shadow-sm">
                        <thead class="thead-light">
                            <tr>
                                <th>STT</th>
                                <th>Họ Tên</th>
                                <th>Email</th>
                                <th>SĐT</th>
                                <th>Địa chỉ</th>
                                <?php if ($isAdmin): ?>
                                    <th width="120px">Hành động</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $index = 0;
                            foreach ($customerList as $item) {
                                echo '<tr>
                                        <td>' . (++$index) . '</td>
                                        <td>' . $item['tenkhachhang'] . '</td>
                                        <td>' . $item['email'] . '</td>
                                        <td>' . $item['dienthoai'] . '</td>
                                        <td>' . $item['diachi'] . '</td>';
                                
                                if ($isAdmin) {
                                    echo '<td>
                                            <button class="btn btn-warning btn-sm" onclick=\'editCustomer(' . json_encode($item) . ')\'><i class="fas fa-edit"></i></button>
                                            <button class="btn btn-danger btn-sm" onclick="deleteCustomer(' . $item['id_dangky'] . ')"><i class="fas fa-trash-alt"></i></button>
                                          </td>';
                                }
                                
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="customerModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Thông tin khách hàng</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="formCustomer">
                        <input type="hidden" id="id_dangky" name="id_dangky">
                        
                        <div class="form-group">
                            <label>Họ tên:</label>
                            <input type="text" class="form-control" id="tenkhachhang" name="tenkhachhang" required>
                        </div>
                        <div class="form-group">
                            <label>Tên đăng nhập:</label>
                            <input type="text" class="form-control" id="tendangnhap" name="tendangnhap">
                        </div>
                         <div class="form-group">
                            <label>Mật khẩu:</label>
                            <input type="password" class="form-control" id="matkhau" name="matkhau" placeholder="Nhập để đổi mk">
                        </div>
                        <div class="form-group">
                            <label>Email:</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        <div class="form-group">
                            <label>SĐT:</label>
                            <input type="text" class="form-control" id="dienthoai" name="dienthoai" required>
                        </div>
                        <div class="form-group">
                            <label>Địa chỉ:</label>
                            <input type="text" class="form-control" id="diachi" name="diachi">
                        </div>
                        <button type="button" class="btn btn-primary btn-block" onclick="saveCustomer()">Lưu lại</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        // Các hàm JS vẫn giữ nguyên, nhưng Staff không có nút bấm để kích hoạt chúng
        function openModal() {
            $('#formCustomer')[0].reset();
            $('#id_dangky').val('');
            $('#customerModal').modal('show');
        }

        function editCustomer(item) {
            $('#id_dangky').val(item.id_dangky);
            $('#tenkhachhang').val(item.tenkhachhang);
            $('#tendangnhap').val(item.tendangnhap);
            $('#email').val(item.email);
            $('#dienthoai').val(item.dienthoai);
            $('#diachi').val(item.diachi);
            $('#customerModal').modal('show');
        }

        function saveCustomer() {
            if ($('#tenkhachhang').val() == '' || $('#dienthoai').val() == '') {
                alert('Vui lòng nhập tên và số điện thoại!');
                return;
            }
            var data = $('#formCustomer').serializeArray();
            data.push({ name: 'action', value: 'add' });

            $.post('ajax.php', data, function(res) {
                // Nếu server trả về lỗi Access Denied
                if(res.includes("Access Denied")) {
                    alert("Bạn không có quyền Thêm/Sửa!");
                } else {
                    location.reload();
                }
            });
        }

        function deleteCustomer(id) {
            if (!confirm('Bạn có chắc chắn muốn xóa khách hàng này?')) return;
            
            $.post('ajax.php', {
                'id_dangky': id,
                'action': 'delete'
            }, function(res) {
                if(res.includes("Access Denied")) {
                    alert("Bạn không có quyền Xóa!");
                }
                location.reload();
            });
        }
    </script>
</body>
</html>