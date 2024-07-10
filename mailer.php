<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '/home/ivimvvzd/att.neeviitgn.com/Exception.php';
require '/home/ivimvvzd/att.neeviitgn.com/PHPMailer.php';
require '/home/ivimvvzd/att.neeviitgn.com/SMTP.php';

function sendMail($to, $subject, $body, $altBody) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;  // Disable verbose debug output
        $mail->isSMTP();                    
        $mail->Host       = 'server1.dnspark.in';  
        $mail->SMTPAuth   = true;                               
        $mail->Username   = 'admin@att.neeviitgn.com';                   
        $mail->Password   = 'NEEV@#335511';                    
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;         
        $mail->Port       = 465;                                

        // Recipients
        $mail->setFrom('admin@att.neeviitgn.com', 'Noreply');
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);                                 
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = $altBody;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>
