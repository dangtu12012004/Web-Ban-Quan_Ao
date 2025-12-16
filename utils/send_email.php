<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Chỉnh lại đường dẫn tới các file PHPMailer mà bạn đã tải về
require 'plugin/PHPMailer/src/Exception.php';
require 'plugin/PHPMailer/src/PHPMailer.php';
require 'plugin/PHPMailer/src/SMTP.php';

function sendEmail($to, $subject, $content) {
    $mail = new PHPMailer(true);

    try {
        // Cấu hình Server
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'tomcsmoney@gmail.com';
        $mail->Password   = 'mat_khau_ung_dung_cua_ban';
        $mail->SMTPSecure = 'tls'; 
        $mail->Port       = 587;  
        $mail->CharSet    = 'UTF-8';

        // Người gửi và người nhận
        $mail->setFrom('tomcsmoney@gmail.com', 'Dirty Coin Support');
        $mail->addAddress($to);

        // Nội dung
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $content;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>