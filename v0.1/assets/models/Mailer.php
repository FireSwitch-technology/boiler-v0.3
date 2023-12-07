<?php

use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{

    public static function sendOTPToken($email, $fname, $otp)
    {
        require_once($_SERVER['DOCUMENT_ROOT'] . '/boiler/vendor/autoload.php');
    
        $mail = new PHPMailer(true);
        $currentDirectory = __DIR__;
        $parentDirectory = dirname(dirname($currentDirectory));
        $filePath = $parentDirectory . '/mail/register.html';
    
        $mail->isSMTP();
        $mail->Host = 'premium295.web-hosting.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'no-reply@greenpower.ng';
        $mail->Password = 'P}TsdpSanRr{';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        #  Sender information
        $mail->setFrom('no-reply@greenpower.ng', 'Netlight Systems');
        #  Recipient
        $mail->addAddress($email, $fname);
    
        #  Email subject
        $mail->Subject = 'Your OTP Token';
    
        #  Read the HTML content from the file
        $htmlContent = file_get_contents($filePath);
    
        #  Set the email body
        $mail->msgHTML($htmlContent);
        
    
    
        #  Send the email
        if ($mail->send()) {
            return true; #  Email sent successfully
        } else {
            return false; #  Email sending failed
        }
    }
    
    


    public static function sendPasswordToUser($email, $fname, $otp)
    {

         return true;
       
    }


}
