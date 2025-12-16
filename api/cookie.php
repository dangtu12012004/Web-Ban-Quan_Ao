<?php
header('Content-Type: application/json; charset=utf-8');

require_once('../database/config.php');
require_once('../database/dbhelper.php');
require_once('../utils/utility.php');

if (empty($_POST)) {
    echo json_encode(['status' => 'error', 'message' => 'No data']);
    exit;
}

$action = getPost('action');
$id     = intval(getPost('id'));
$num    = intval(getPost('num'));
$size   = isset($_POST['size']) ? $_POST['size'] : 'S';
$newSize = isset($_POST['newSize']) ? $_POST['newSize'] : '';

$cart = [];
if (isset($_COOKIE['cart'])) {
    $cart = json_decode($_COOKIE['cart'], true);
    if (!is_array($cart)) $cart = [];
}

$response = ['status' => 'success', 'message' => ''];

switch ($action) {

    case 'add':
        $sql_qty = "SELECT qty_s, qty_m, qty_l, number FROM product WHERE id = $id";
        $product_db = executeSingleResult($sql_qty);

        if (!$product_db) {
            echo json_encode(['status' => 'error', 'message' => 'Sản phẩm không tồn tại']);
            exit;
        }

        if ($size == 'S') $qty_db = (int)$product_db['qty_s'];
        elseif ($size == 'M') $qty_db = (int)$product_db['qty_m'];
        elseif ($size == 'L') $qty_db = (int)$product_db['qty_l'];
        else $qty_db = (int)$product_db['number'];

        $item_index = -1;
        $num_in_cart = 0;

        foreach ($cart as $i => $item) {
            if ($item['id'] == $id && $item['size'] == $size) {
                $item_index = $i;
                $num_in_cart = (int)$item['num'];
                break;
            }
        }

        $total_new_qty = ($item_index != -1) ? $num_in_cart + $num : $num;

        if ($total_new_qty > $qty_db) {
            echo json_encode([
                'status' => 'error',
                'message' => "Số lượng vượt tồn kho ($qty_db)"
            ]);
            exit;
        }

        if ($item_index != -1) {
            $cart[$item_index]['num'] = $total_new_qty;
        } else {
            $cart[] = ['id' => $id, 'num' => $num, 'size' => $size];
        }

        setcookie('cart', json_encode($cart), time() + 30*24*60*60, '/');

        echo json_encode([
            'status' => 'success',
            'message' => 'Đã thêm vào giỏ hàng'
        ]);
        exit;

    case 'delete':
        foreach ($cart as $i => $item) {
            if ($item['id'] == $id && $item['size'] == $size) {
                array_splice($cart, $i, 1);
                break;
            }
        }
        setcookie('cart', json_encode($cart), time() + 30*24*60*60, '/');
        echo json_encode(['status' => 'success']);
        exit;

    case 'update':
        foreach ($cart as $i => $item) {
            if ($item['id'] == $id && $item['size'] == $size) {
                $cart[$i]['num'] = $num;
                break;
            }
        }
        setcookie('cart', json_encode($cart), time() + 30*24*60*60, '/');
        echo json_encode(['status' => 'success']);
        exit;

    case 'change_size':
        foreach ($cart as $i => $item) {
            if ($item['id'] == $id && $item['size'] == $size) {
                $cart[$i]['size'] = $newSize;
                break;
            }
        }
        setcookie('cart', json_encode($cart), time() + 30*24*60*60, '/');
        echo json_encode(['status' => 'success']);
        exit;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Action không hợp lệ']);
        exit;
}
