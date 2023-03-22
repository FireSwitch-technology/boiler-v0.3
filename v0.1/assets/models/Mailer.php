<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{

  public function sendOTPToken($amountTopay = 200, $email = "billyhadiattaofeeq@gmail.com", $fname = "sammy", $month = "December")
  {

    require_once($_SERVER['DOCUMENT_ROOT'] . '/boiler/vendor/autoload.php');

    $calculatePenalty = $amountTopay * 100;
    $new_amount = $amountTopay + $calculatePenalty;

    $mail = new PHPMailer(true);

    $mail->isSMTP();

    // $mail->SMTPDebug = 2;
    $mail->Host = 'solarcredit.io';

    $mail->SMTPAuth = true;

    $mail->Username = $_ENV['APP_MAIL'];

    $mail->Password = $_ENV['MAIL_PWORD'];

    $mail->SMTPSecure = 'ssl';

    $mail->Port = 465;

    $mail->setFrom($_ENV['APP_MAIL'], $_ENV['boiler']);

    $mail->addAddress($email, $fname);

    $mail->isHTML(true);

    $mail->Subject =  $_ENV['APP_NAME'] ."Payment Debit Notification ";

    $body = "
    <html>
        <head>
            <style>
            require_once($_SERVER[DOCUMENT_ROOT] . '/boiler/vendor/css/mailer.css');
            </style>
        </head>
        <body>
        <div class=container>
        <img src=https://via.placeholder.com/150 alt=Company Logo>
        </div>
            <p>Dear $fname,</p>
            <p>We hope this email finds you well. We are writing to inform you that your wallet has been debited with both an initial payment and a penalty payment.</p>
            <p>The initial payment of <strong>" . number_format($amountTopay, 2) . "</strong> has been deducted from your account as per the agreed terms and conditions of the contract. This payment was due on <strong>$month</strong>.</p>
            <p>Additionally, a penalty payment of <strong>" . number_format($calculatePenalty, 2) . "</strong> has been imposed as a result of a late payment. Please note that this penalty has been charged as per the terms of the agreement.</p>
            <p>Total amount debited is <strong>" . number_format($new_amount, 2) . "</strong></p>
            <p>We would like to remind you that timely payment of your bills is important to ensure the smooth functioning of our services. If you have any difficulty making a payment in the future, please reach out to us for assistance.</p>
            <p>Please do not hesitate to reach out to us if you have any questions or concerns regarding this. We are always here to help.</p>
            <p>Thank you for choosing " .  $_ENV['APP_NAME'] . " for your financial needs.</p>
            <p>Best regards,</p>
            <p>Team " .  $_ENV['APP_NAME'] . "</p>
        </body>
    </html>
";


    $mail->Body = $body;

    if (!$mail->send()) {
     echo "sent";
    } else {
      return true;
    }
  }
}
