<?php
require_once('../../database/dbhelper.php');
require_once('../../utils/utility.php');

if (!empty($_POST)) {
    $action = getPost('action');

    switch ($action) {
        case 'save':
            $id_staff = getPost('id_staff');
            $ten_staff = getPost('ten_staff');
            $tendangnhap = getPost('tendangnhap');
            $matkhau = getPost('matkhau');
            $dienthoai = getPost('dienthoai');
            $diachi = getPost('diachi');

            if ($id_staff != '') {
                // --- UPDATE ---
                if ($matkhau != '') {
                    // Nếu nhập pass mới -> Hash lại
                    $matkhau_hash = password_hash($matkhau, PASSWORD_DEFAULT);
                    $sql = "UPDATE tbl_staff SET ten_staff='$ten_staff', matkhau='$matkhau_hash', dienthoai='$dienthoai', diachi='$diachi' WHERE id_staff = $id_staff";
                } else {
                    // Giữ nguyên pass cũ
                    $sql = "UPDATE tbl_staff SET ten_staff='$ten_staff', dienthoai='$dienthoai', diachi='$diachi' WHERE id_staff = $id_staff";
                }
            } else {
                // --- INSERT ---
                // Kiểm tra trùng user
                $check = executeSingleResult("SELECT * FROM tbl_staff WHERE tendangnhap='$tendangnhap'");
                if($check != null) {
                    die(); // Có thể trả về chuỗi lỗi để alert
                }
                
                // Mặc định tạo mới bắt buộc có pass
                if($matkhau == '') $matkhau = '123456'; // Pass mặc định nếu quên nhập
                $matkhau_hash = password_hash($matkhau, PASSWORD_DEFAULT);
                
                $sql = "INSERT INTO tbl_staff (ten_staff, tendangnhap, matkhau, dienthoai, diachi) 
                        VALUES ('$ten_staff', '$tendangnhap', '$matkhau_hash', '$dienthoai', '$diachi')";
            }
            execute($sql);
            break;

        case 'delete':
            $id_staff = getPost('id_staff');
            $sql = "DELETE FROM tbl_staff WHERE id_staff = $id_staff";
            execute($sql);
            break;
    }
}
?>