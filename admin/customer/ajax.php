<?php
require_once('../database/dbhelper.php');

if (!empty($_POST)) {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        switch ($action) {
            case 'add':
                // --- BẢO MẬT: CHẶN QUYỀN THÊM/SỬA ---
                if (!isset($_COOKIE['role']) || $_COOKIE['role'] != 'admin') {
                    die('Access Denied: Bạn không có quyền Thêm/Sửa.');
                }
                // -------------------------------------

                if (isset($_POST['tenkhachhang'])) {
                    $tenkhachhang = $_POST['tenkhachhang'];
                    $tendangnhap = $_POST['tendangnhap'] ?? '';
                    $email = $_POST['email'];
                    $dienthoai = $_POST['dienthoai'];
                    $diachi = $_POST['diachi'];
                    $matkhau = $_POST['matkhau'];
                    
                    $id = $_POST['id_dangky'];

                    if (empty($id)) {
                        $matkhau_hash = md5($matkhau); 
                        $sql = "INSERT INTO tbl_dangky(tenkhachhang, tendangnhap, matkhau, email, dienthoai, diachi) 
                                VALUES ('$tenkhachhang', '$tendangnhap', '$matkhau_hash', '$email', '$dienthoai', '$diachi')";
                    } else {
                        if(!empty($matkhau)) {
                             $matkhau_hash = md5($matkhau);
                             $sql = "UPDATE tbl_dangky SET tenkhachhang = '$tenkhachhang', tendangnhap = '$tendangnhap', matkhau = '$matkhau_hash',
                                email = '$email', dienthoai = '$dienthoai', diachi = '$diachi' WHERE id_dangky = $id";
                        } else {
                             $sql = "UPDATE tbl_dangky SET tenkhachhang = '$tenkhachhang', tendangnhap = '$tendangnhap',
                                email = '$email', dienthoai = '$dienthoai', diachi = '$diachi' WHERE id_dangky = $id";
                        }
                    }
                    execute($sql);
                }
                break;

            case 'delete':
                // --- BẢO MẬT: CHẶN QUYỀN XÓA ---
                if (!isset($_COOKIE['role']) || $_COOKIE['role'] != 'admin') {
                    die('Access Denied: Bạn không có quyền Xóa.');
                }
                // --------------------------------

                if (isset($_POST['id_dangky'])) {
                    $id = $_POST['id_dangky'];
                    $sql = "DELETE FROM tbl_dangky WHERE id_dangky = $id";
                    execute($sql);
                }
                break;
        }
    }
}
?>