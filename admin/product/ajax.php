<?php
require_once('../database/dbhelper.php');

// 1. BẢO MẬT: Chỉ cho phép Admin thực hiện
// if (!isset($_COOKIE['role']) || $_COOKIE['role'] != 'admin') {
//     die('Access Denied: Bạn không có quyền thực hiện thao tác này.');
// }

if (!empty($_POST)) {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        switch ($action) {
            case 'delete':
                if (isset($_POST['id'])) {
                    $id = (int)$_POST['id']; 

                    // --- XỬ LÝ LOGIC XÓA ---
                    
                    // Xóa cứng (Hard Delete) - Dùng cho sản phẩm test/rác
                    // Lưu ý: Nếu sản phẩm đã có trong đơn hàng (bảng order_details)
                    
                    // Nếu bạn muốn XÓA BẤT CHẤP (chấp nhận mất lịch sử đơn hàng của sp này):
                    // Bỏ comment dòng dưới:
                    execute("DELETE FROM order_details WHERE product_id = $id");

                    $sql = "DELETE FROM product WHERE id = $id";
                    execute($sql);
                    
                    // Cách 2: Xóa mềm (Soft Delete) - Khuyên dùng cho dự án thật
                    // Thay vì xóa, ta cập nhật trạng thái để ẩn nó đi
                    // $sql = "UPDATE product SET deleted = 1 WHERE id = $id";
                    // execute($sql);
                }
                break;
        }
    }
}
?>