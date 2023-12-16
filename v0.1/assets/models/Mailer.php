<?php

class Mailer {

    public static function sendOTPToken($email, $fname, $otp) {
        $message = "<p style='font-size: 16px; color: #333; line-height: 1.6;'>Hello $fname,</p>
                <p style='font-size: 16px; color: #333; line-height: 1.6;'>Thank you for registering with us! We're thrilled to have you as a part of our community. To ensure the security and validity of your account, we kindly ask you to complete the email verification process.</p>
                <p style='font-size: 16px; color: #333; line-height: 1.6;'>Your OTP is: <strong>$otp</strong></p>
                <p style='font-size: 16px; color: #333; line-height: 1.6;'>To finalize your registration and gain full access to our platform, kindly enter the OTP in the designated field on our website. This verification step helps us ensure that your email address is correct and that you have control over the account.</p>
                <p style='font-size: 16px; color: #333; line-height: 1.6;'>Thank you for your cooperation in completing the email verification process. We look forward to providing you with a fantastic experience on our platform. Should you need any assistance or have any feedback, feel free to reach out to us.</p>
                <p style='font-size: 16px; color: #333; line-height: 1.6;'>Best regards,<br>Team {$_ENV['APP_NAME']}</p>";

        $url = $_ENV['RENI_MAIL'] . "/sendSingleMail";

        $payload = [
            'email' => $email,
            'subject' => $_ENV['APP_NAME'] . ' Account Verification',
            'body' => $message,
        ];

        $connectToReniMail = self::connectToReniMail($payload, $url);

        if ($connectToReniMail['success'] === true) {
            return true;
        } else {
            // Log the detailed error message or handle it as needed
            error_log("Email sending error: " . $connectToReniMail['message']);
            return false;
        }

    }

    public static function ResetPassword($email, $fname, $otp) {

        $message = "<p style='font-size: 16px; color: #333; line-height: 1.6;'>Hello $fname,</p>
                <p style='font-size: 16px; color: #333; line-height: 1.6;'>We received a request to reset your password for your {$_ENV['APP_NAME']} account. If you did not make this request, you can ignore this email.</p>
                <p style='font-size: 16px; color: #333; line-height: 1.6;'>To reset your password, use the following OTP code:</p>
                <p style='font-size: 16px; color: #333; line-height: 1.6;'><strong>$otp</strong></p>
                <p style='font-size: 16px; color: #333; line-height: 1.6;'>Please enter this code on our website to complete the password reset process. This code will expire after a short period for security reasons.</p>
                <p style='font-size: 16px; color: #333; line-height: 1.6;'>If you have any questions or need further assistance, feel free to reach out to us. We're here to help!</p>
                <p style='font-size: 16px; color: #333; line-height: 1.6;'>Best regards,<br> Team {$_ENV['APP_NAME']}</p>";

        $payload = [
            'email' => $email,
            'subject' => $_ENV['APP_NAME'] . '  Password Reset',
            'body' => $message,
        ];
        // billyhadiattaofeeq@gmail.com

        $url = $_ENV['RENI_MAIL'] . "/sendSingleMail";

        $connectToReniMail = self::connectToReniMail($payload, $url);

        if ($connectToReniMail['success'] === true) {
            return true;
        } else {
            // Log the detailed error message or handle it as needed
            error_log("Email sending error: " . $connectToReniMail['message']);
            return false;
        }

    }

    public static function productVerificationRequest($productName, $sellerName, $productCondition) {

        $message = "<p style='font-size: 16px; color: #333; line-height: 1.6;'>Hi Admin,</p>
        <p style='font-size: 16px; color: #333; line-height: 1.6;'>A new product has been uploaded to the platform and is awaiting verification for selling.</p>
        <strong>Product Details:</strong>
        <ul>
            <li><strong>Product Name:</strong> $productName</li>
            <li><strong>Seller:</strong> $sellerName</li>
            <li><strong>Upload Date:</strong>  " . Utility::formatDate(time()) . "</li>
        </ul>
        <strong>Verification Status:</strong> Pending<br>
        <p style='font-size: 16px; color: #333; line-height: 1.6;'>The product details look promising, but we would appreciate your expertise in ensuring that it meets the platform standards before it goes live.</p>
        <strong>Additional Information:</strong>
        <ul>
            <li><strong>Product Condition:</strong> $productCondition</li>
        </ul>
        <p style='font-size: 16px; color: #333; line-height: 1.6;'>Your prompt attention to this matter would be highly appreciated. If you have any questions or need further information, feel free to reach out.</p>
        <p style='font-size: 16px; color: #333; line-height: 1.6;'>Thank you for your dedication to maintaining the quality of products on our platform.</p>
        <p style='font-size: 16px; color: #333; line-height: 1.6;'>Best regards,<br> Team {$_ENV['APP_NAME']}</p>";

        $payload = [
            'email' => $_ENV['APP_MAIL'],
            'subject' => $_ENV['APP_NAME'] . ' Product Verification Required',
            'body' => $message,
        ];

        $url = $_ENV['RENI_MAIL'] . "/sendSingleMail";

        $connectToReniMail = self::connectToReniMail($payload, $url);

        if ($connectToReniMail['success'] === true) {
            return true;
        } else {
            // Log the detailed error message or handle it as needed
            error_log("Email sending error: " . $connectToReniMail['message']);
            return false;
        }
    }




   public static function productApprovalNotification($productName, $sellerName, $sellerEmail, $productCondition) {
    $message = "
        <p style='font-size: 16px; color: #333; line-height: 1.6;'>
            Hi $sellerName,
        </p>
        <p style='font-size: 16px; color: #333; line-height: 1.6;'>
            Congratulations! Your product on {$_ENV['APP_NAME']} has been approved and is now ready for sale.
        </p>
        <strong>Product Details:</strong>
        <ul>
            <li><strong>Product Name:</strong> $productName</li>
            <li><strong>Upload Date:</strong> " . Utility::formatDate(time()) . "</li>
        </ul>
        <strong>Verification Status:</strong> Approved<br>
        <p style='font-size: 16px; color: #333; line-height: 1.6;'>
            Your product meets our platform standards and is now live for users to view and purchase. We appreciate your commitment to maintaining the quality of products on our platform.
        </p>
        <strong>Additional Information:</strong>
        <ul>
            <li><strong>Product Condition:</strong> $productCondition</li>
        </ul>
        <p style='font-size: 16px; color: #333; line-height: 1.6;'>
            Thank you for contributing to the success of {$_ENV['APP_NAME']}. If you have any questions or need further assistance, feel free to reach out.
        </p>
        <p style='font-size: 16px; color: #333; line-height: 1.6;'>
            Best regards,<br> Team {$_ENV['APP_NAME']}
        </p>
    ";

    $payload = [
        'email' => $sellerEmail,  // Assuming $sellerEmail contains the email of the seller
        'subject' => $_ENV['APP_NAME'] . ' Product Approved',
        'body' => $message,
    ];

    $url = $_ENV['RENI_MAIL'] . "/sendSingleMail";

    $connectToReniMail = self::connectToReniMail($payload, $url);

    if ($connectToReniMail['success'] === true) {
        return true;
    } else {
        // Log the detailed error message or handle it as needed
        error_log("Email sending error: " . $connectToReniMail['message']);
        return false;
    }
}


#productDisapprovalNotification:: This method sends notifiation to user  on product-disaaproval 
  public static function productDisapprovalNotification($productName, $sellerName, $sellerEmail, $productCondition, $disapprovalReason) {
    $message = "<p style='font-size: 16px; color: #333; line-height: 1.6;'>Hi $sellerName,</p>
    <p style='font-size: 16px; color: #333; line-height: 1.6;'>We regret to inform you that your product on {$_ENV['APP_NAME']} has not been approved for sale.</p>
    <strong>Product Details:</strong>
    <ul>
        <li><strong>Product Name:</strong> $productName</li>
        <li><strong>Upload Date:</strong> " . Utility::formatDate(time()) . "</li>
    </ul>
    <strong>Verification Status:</strong> Disapproved<br>
    <p style='font-size: 16px; color: #333; line-height: 1.6;'>After careful review, we found that the product did not meet our platform standards. We appreciate your efforts, and we encourage you to review the following reason for disapproval:</p>
    <strong>Disapproval Reason:</strong>
    <p style='font-size: 16px; color: #333; line-height: 1.6;'>$disapprovalReason</p>
    
    <p style='font-size: 16px; color: #333; line-height: 1.6;'>Feel free to make the necessary adjustments and re-upload the product for approval. If you have any questions or need further assistance, please don't hesitate to reach out.</p>
    <p style='font-size: 16px; color: #333; line-height: 1.6;'>Best regards,<br> Team {$_ENV['APP_NAME']}</p>";

    $payload = [
        'email' => $sellerEmail,  // Assuming $sellerEmail contains the email of the seller
        'subject' => $_ENV['APP_NAME'] . ' Product Disapproved',
        'body' => $message,
    ];

    $url = $_ENV['RENI_MAIL'] . "/sendSingleMail";

    $connectToReniMail = self::connectToReniMail($payload, $url);

    if ($connectToReniMail['success'] === true) {
        return true;
    } else {
        // Log the detailed error message or handle it as needed
        error_log("Email sending error: " . $connectToReniMail['message']);
        return false;
    }
}


    #connectToReniMail:: This method links the platfrom to renimail
    public static function connectToReniMail(array $payload, $url) {
        $response = ['success' => false, 'message' => ''];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $headers = [
            'Authorization: Bearer ' . $_ENV['Enicom_Access_Bearer'],
            'Content-Type: application/json', // Added content type header
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        if ($result === false) {
            $response['message'] = "Unable to process request. Please contact support.";
        } else {
            $response = json_decode($result, true); // Assuming the response is in JSON format
        }

        curl_close($ch);
        return $response;
    }

}
