<?php
require_once('../utils/utility.php');
session_start();
if(!empty($_POST)) { $action = getPost('action'); $id = getPost('id'); $num = getPost('num');

    $cart = [];
    if(isset($_SESSION['cart'])) {
        $json = $_SESSION['cart']; 
        $cart = json_decode($json, true);
    }
    
    switch ($action) {
        case 'add':
            $isFind = false;
            for ($i=0; $i < count($cart); $i++) { 
                    if($cart[$i]['id'] == $id) {
                        $cart[$i]['num'] += $num;
                        $isFind = true; 
                        break;
                    }		
            }
    
            if(!$isFind) { 
                $cart[] = [
                    'id'=>$id,
                    'num'=>$num
                ];
            }
            $_SESSION['cart'] = json_encode($cart); 
            break;
        case 'delete':
            for ($i=0; $i < count($cart); $i++) { 
                if($cart[$i]['id'] == $id) {
                    array_splice($cart, $i, 1);
                    break;
                }
            }
            $_SESSION['cart'] = json_encode($cart);
        break;
    }
    
    
}