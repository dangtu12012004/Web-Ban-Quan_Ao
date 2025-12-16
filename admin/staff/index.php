<?php
$title = 'Quản lý Nhân viên';
$baseUrl = '../';
require_once('../../database/dbhelper.php');
require_once('../../utils/utility.php');

// --- KIỂM TRA QUYỀN: CHỈ ADMIN MỚI ĐƯỢC VÀO TRANG NÀY ---
// Staff sẽ bị đá về trang dashboard chính (hoặc trang đăng nhập) nếu cố tình vào link này
if (!isset($_COOKIE['role']) || strtolower($_COOKIE['role']) != 'admin') {
    header('Location: ../index.php'); 
    die(); 
}

$role = $_COOKIE['role'];

$sql = "SELECT * FROM tbl_staff";
$staffList = executeResult($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title><?=$title?></title>
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
        .panel-heading { margin-bottom: 20px; font-weight: bold; text-align: left; font-size: 24px;}
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
                <?php if($role == 'admin') { ?>
                <li>
                    <a href="../index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                </li>
                <?php } ?>

                <li>
                    <a href="../category/index.php"><i class="fas fa-folder"></i> Quản lý Danh mục</a>
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

                <?php if($role == 'admin') { ?>
                <li class="active">
                    <a href="index.php"><i class="fas fa-user-tie"></i> Quản lý Nhân viên</a>
                </li>
                <?php } ?>

                <li>
                      <a href="/Web/logout.php" style="border-top: 1px solid #4b545c; margin-top: 20px;"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                </li>
            </ul>
        </nav>

        <div id="content">
            <div class="panel panel-primary">
                <div class="panel-heading">QUẢN LÝ NHÂN VIÊN</div>
                <div class="panel-body">
                    <div style="margin-bottom: 15px;">
                        <button class="btn btn-success" onclick="openModal()"> <i class="fas fa-plus"></i> Thêm Nhân Viên</button>
                    </div>
                    
                    <table class="table table-bordered table-hover bg-white shadow-sm">
                        <thead class="thead-light">
                            <tr>
                                <th>STT</th>
                                <th>Họ Tên</th>
                                <th>Tài khoản</th>
                                <th>SĐT</th>
                                <th>Địa chỉ</th>
                                <th width="120px">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $index = 0;
                            foreach ($staffList as $item) {
                                echo '<tr>
                                        <td>'.(++$index).'</td>
                                        <td>'.$item['ten_staff'].'</td>
                                        <td>'.$item['tendangnhap'].'</td>
                                        <td>'.$item['dienthoai'].'</td>
                                        <td>'.$item['diachi'].'</td>
                                        <td>
                                            <button class="btn btn-warning btn-sm" onclick=\'editStaff('.json_encode($item).')\'><i class="fas fa-edit"></i></button>
                                            <button class="btn btn-danger btn-sm" onclick="deleteStaff('.$item['id_staff'].')"><i class="fas fa-trash-alt"></i></button>
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

    <div class="modal fade" id="staffModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Thông tin nhân viên</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="formStaff">
                        <input type="hidden" id="id_staff" name="id_staff">
                        <div class="form-group">
                            <label>Họ tên:</label>
                            <input type="text" class="form-control" id="ten_staff" name="ten_staff" required>
                        </div>
                        <div class="form-group">
                            <label>Tên đăng nhập:</label>
                            <input type="text" class="form-control" id="tendangnhap" name="tendangnhap" required>
                        </div>
                        <div class="form-group">
                            <label>Mật khẩu:</label>
                            <input type="password" class="form-control" id="matkhau" name="matkhau" placeholder="Để trống nếu không đổi">
                        </div>
                        <div class="form-group">
                            <label>SĐT:</label>
                            <input type="text" class="form-control" id="dienthoai" name="dienthoai" required>
                        </div>
                        <div class="form-group">
                            <label>Địa chỉ:</label>
                            <input type="text" class="form-control" id="diachi" name="diachi">
                        </div>
                        <button type="button" class="btn btn-primary btn-block" onclick="saveStaff()">Lưu lại</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        function openModal() {
            $('#formStaff')[0].reset();
            $('#id_staff').val('');
            $('#tendangnhap').prop('readonly', false);
            $('#staffModal').modal('show');
        }

        function editStaff(item) {
            $('#id_staff').val(item.id_staff);
            $('#ten_staff').val(item.ten_staff);
            $('#tendangnhap').val(item.tendangnhap);
            $('#tendangnhap').prop('readonly', true);
            $('#dienthoai').val(item.dienthoai);
            $('#diachi').val(item.diachi);
            $('#matkhau').val(''); 
            $('#staffModal').modal('show');
        }

        function saveStaff() {
            if($('#ten_staff').val() == '' || $('#tendangnhap').val() == '') {
                alert('Vui lòng nhập đủ thông tin!'); return;
            }
            var data = $('#formStaff').serializeArray();
            data.push({name: 'action', value: 'save'});
            $.post('ajax.php', data, function(res) {
                location.reload();
            });
        }

        function deleteStaff(id) {
            if(!confirm('Bạn có chắc chắn muốn xóa nhân viên này?')) return;
            $.post('ajax.php', { 'id_staff': id, 'action': 'delete' }, function(res) {
                location.reload();
            });
        }
    </script>
</body>
</html>