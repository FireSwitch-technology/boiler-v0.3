<?php

use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{

    public function sendOTPToken($email, $fname, $otp)
    {

        require_once($_SERVER['DOCUMENT_ROOT'] . '/boiler/vendor/autoload.php');

        $mail = new PHPMailer(true);

        $mail->isSMTP();

        // $mail->SMTPDebug = 2;
        $mail->Host = $_ENV['HOST_NAME'];

        $mail->SMTPAuth = true;

        $mail->Username = $_ENV['SMTP_USERNAME'];

        $mail->Password = $_ENV['SMTP_PWORD'];

        $mail->SMTPSecure = 'ssl';

        $mail->Port = 465;

        $mail->setFrom($_ENV['APP_MAIL'], $_ENV['APP_NAME']);

        $mail->addAddress($email, $fname);

        $mail->isHTML(true);

        $mail->Subject =  " " . $_ENV['APP_NAME'] . "  Account Verification";

        $body =  "Dear $fname ,<br><br>";

        $body .= "Thank you for registering with us! We're thrilled to have you as a part of our community. To ensure the security and validity of your account, we kindly ask you to complete the email verification process. <br><br>";

        $body .=  "Your OTP is: $otp<br><br>";

        $body .=  "To finalize your registration and gain full access to our platform, kindly enter the OTP in the designated field on our website. This verification step helps us ensure that your email address is correct and that you have control over the account.<br><br>";

        $body .= "<h4> $otp</h4><br><br>";

        $body .= "Thank you for your cooperation in completing the email verification process. We look forward to providing you with a fantastic experience on our platform. Should you need any assistance or have any feedback, feel free to reach out to us<br><br>";

        $body .= 'Best regards, <br><br>';

        $body .= "Team  {$_ENV['APP_NAME']}";

        $mail->Body = $body;

        if (!$mail->send()) {
            echo 'sent';
        } else {
            return true;
        }
    }




    public function sendPasswordToUser($email, $fname, $token)
    {

        require_once($_SERVER['DOCUMENT_ROOT'] . '/boiler/vendor/autoload.php');

        $mail = new PHPMailer(true);

        $mail->isSMTP();

        // $mail->SMTPDebug = 2;
        $mail->Host = $_ENV['HOST_NAME'];

        $mail->SMTPAuth = true;

        $mail->Username = $_ENV['SMTP_USERNAME'];

        $mail->Password = $_ENV['SMTP_PWORD'];

        $mail->SMTPSecure = 'ssl';

        $mail->Port = 465;

        $mail->setFrom($_ENV['APP_MAIL'], $_ENV['APP_NAME']);

        $mail->addAddress($email, $fname);

        $mail->isHTML(true);

        $mail->Subject =  " " . $_ENV['APP_NAME'] . "  Reset Your Password";

        $body = "
           <html>
               <head>
                   <style>
                       @media only screen and (max-width: 768px) {
                           h1 {
                               font-size: 15px;
                               margin-bottom: 20px;
                           }
                           p {
                               font-size: 14px;
                               margin-bottom: 15px;
                           }
                       }
                   </style>
               </head>
               <body style='font-family: Arial,sans-serif; font-size: 14px;padding:30px; line-height: 1.6; color: #333; box-shadow: 0px 0px 10px #ccc;'>
                   <div style='padding: 20px;'>
          
                       <p style='font-size: 14px; margin-bottom: 20px;'>Dear $fname,</p>
                       <p style='font-size: 14px; margin-bottom: 20px;'>We received a request to reset the password for your account. If you did not request this, please ignore this email.</p>
                       
                       <p style='font-size: 14px; margin-bottom: 20px;'>Password: [Default Password $token]</p>
                       <p style='font-size: 14px; margin-bottom: 20px;'>To reset your password, please follow these steps:</p>
                       <p style='font-size: 14px; margin-bottom: 20px;'>1) Go to the login page on our website</p>
                       <p style='font-size: 14px; margin-bottom: 20px;'>2) Click on the Forgot password link</p>
                       <p style='font-size: 14px; margin-bottom: 20px;'>3) Enter your email address and click Submit</p>
                       <p style='font-size: 14px; margin-bottom: 20px;'>4) Check your email for further instructions on how to reset your password</p>
                       <p style='font-size: 14px; margin-bottom: 20px;'>5) After following this procedure,a new password will be sent to your mail</p>
                       <p style='font-size: 14px; margin-bottom: 20px;'>Please note that this is a default password token and we strongly recommend that you change it after login for security purposes. You can change your password by logging in to your account and updating your account settings</p>
                       <p style='font-size: 14px; margin-bottom: 20px;'>If you have any questions or concerns, please do not hesitate to contact us</p>
                       <p style='font-size: 14px; margin-bottom: 20px;'>Thank you again for joining us!</p>
                       <p style='font-size: 14px; margin-bottom: 20px;'>Best regards,</p>
                       <p style='font-size: 14px; margin-bottom: 20px;'>Team  "  . $_ENV['APP_NAME'] . "</p>
                   </div>
               </body>
           </html>
        ";


        $mail->Body = $body;

        if (!$mail->send()) {
            echo 'sent';
        } else {
            return true;
        }
    }
}
